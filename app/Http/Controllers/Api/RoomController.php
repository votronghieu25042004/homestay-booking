<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\RoomImage;
use App\Http\Resources\RoomResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin/rooms",
     *     tags={"Rooms"},
     *     summary="Lấy danh sách phòng cho Admin",
     *     description="Trả về danh sách các phòng (có hỗ trợ phân trang và tìm kiếm).",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Từ khóa tìm kiếm theo tên phòng",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Thành công"
     *     )
     * )
     */
    public function adminIndex(Request $request)
    {
        // CHỈ LẤY CÁC PHÒNG CHA (Nguyên căn, Tòa nhà, Phòng Home, hoặc Phòng riêng lẻ không thuộc tòa nhà nào)
        $query = Room::withCount('childRooms')
            ->with(['images', 'childRooms.images'])
            ->where(function($q) {
                $q->whereIn('rent_type', ['whole_house', 'room_based', 'home'])
                  ->orWhere(function($sq) {
                      $sq->where('rent_type', 'private_room')
                         ->whereNull('parent_id');
                  });
            });

        // Search & Filter
        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->type) {
            if ($request->type === 'private_room_standalone') {
                $query->where('rent_type', 'private_room')->whereNull('parent_id');
            } else {
                $query->where('rent_type', $request->type);
            }
        }

        $rooms = $query->orderBy('id', 'desc')->paginate($request->input('per_page', 10));

        return RoomResource::collection($rooms);
    }

    public function index()
    {
        // Lấy tất cả phòng, sắp xếp mới nhất lên đầu, kèm theo hình ảnh
        $rooms = Room::with(['images', 'parentHomestay'])->orderBy('id', 'desc')->get();

        return RoomResource::collection($rooms);
    }

    public function show($id)
    {
        $room = Room::with(['images', 'childRooms.images', 'parentHomestay'])->find($id);

        if (!$room) {
            return response()->json(['message' => 'Không tìm thấy phòng'], 404);
        }

        // Lấy tiện nghi thủ công (vì bảng trung gian không có model riêng dễ dùng)
        $amenities = DB::table('amenities')
            ->join('room_amenities', 'amenities.id', '=', 'room_amenities.amenity_id')
            ->where('room_amenities.room_id', $id)
            ->select('amenities.*')
            ->get();

        $room->amenity_list = $amenities;

        return new RoomResource($room);
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $room = Room::find($id);
            if (!$room) {
                return response()->json(['message' => 'Không tìm thấy phòng'], 404);
            }

            // Nếu là phòng riêng, tự động lấy location từ cha
            $location = $request->location;
            if ($request->rent_type === 'private_room' && $request->parent_id) {
                $parent = Room::find($request->parent_id);
                if ($parent) {
                    $location = $parent->location;
                }
            }

            // 1. Cập nhật thông tin cơ bản
            $room->update([
                'title' => $request->title,
                'location' => $location,
                'rent_type' => $request->rent_type,
                'parent_id' => $request->parent_id,
                'type' => $request->type,
                'price' => $request->price,
                'max_guests' => $request->max_guests,
                'max_children' => $request->max_children,
                'description' => $request->description,
                'status' => $request->status,
                'is_visible' => $request->is_visible === 'true' || $request->is_visible == 1 ? 1 : 0,
            ]);

            // 2. Cập nhật tiện nghi (Xóa sạch tiện nghi cũ của phòng này và lưu lại các tick mới)
            DB::table('room_amenities')->where('room_id', $id)->delete();
            $amenities = $request->input('amenities', []);
            // Đảm bảo amenities là mảng (form data có thể gửi lên chuỗi 'null' hoặc chuỗi json)
            if (is_string($amenities)) {
                $amenities = json_decode($amenities, true) ?? [];
            }
            if (!empty($amenities) && is_array($amenities)) {
                foreach ($amenities as $amenity_id) {
                    if ($amenity_id && $amenity_id !== 'null') {
                        DB::table('room_amenities')->insert([
                            'room_id' => $id,
                            'amenity_id' => $amenity_id
                        ]);
                    }
                }
            }

            // 3. Xử lý ảnh cũ (xóa những ảnh không còn trong retained_images)
            $retainedUrls = $request->input('retained_images', []);
            $oldImages = RoomImage::where('room_id', $room->id)->get();
            foreach ($oldImages as $oldImg) {
                if (!in_array($oldImg->image_url, $retainedUrls)) {
                    // Xóa file vật lý trong storage (Lấy path tương đối sau /storage/)
                    $filePath = str_replace('/storage/', '', $oldImg->image_url);
                    Storage::disk('public')->delete($filePath);
                    // Xóa record trong DB
                    $oldImg->delete();
                }
            }

            // 4. Upload thêm ảnh mới nếu admin có chọn thêm
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $key => $file) {
                    $path = $file->store('rooms', 'public');
                    RoomImage::create([
                        'room_id' => $room->id,
                        'image_url' => '/storage/' . $path,
                        'is_primary' => false
                    ]);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Cập nhật phòng thành công!']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Lỗi khi cập nhật: ' . $e->getMessage()], 500);
        }
    }

    public function getAmenities()
    {
        // Kéo toàn bộ 7 tiện nghi từ DB lên
        $amenities = DB::table('amenities')->get();
        return response()->json($amenities);
    }

    // HÀM THỐNG KÊ SỐ LƯỢNG PHÒNG CHO ADMIN
    public function stats()
    {
        // ĐẾM CHỈ CÁC PHÒNG CỐ ĐỊNH (Phòng đơn, Phòng Home, Nguyên căn)
        // LOẠI BỎ 'room_based' (Tòa nhà/Homestay tổng) vì nó là vật chứa, không phải đơn vị thuê trực tiếp
        
        $baseQuery = \App\Models\Room::where('rent_type', '!=', 'room_based');

        $total = (clone $baseQuery)->count();
        $available = (clone $baseQuery)->where('status', 'available')->count();
        $deposited = (clone $baseQuery)->where('status', 'booked')->count();
        $occupied = (clone $baseQuery)->where('status', 'in_use')->count();
        $maintenance = (clone $baseQuery)->where('status', 'maintenance')->count();

        // Trả về cho Vue hiển thị
        return response()->json([
            'total' => $total,
            'available' => $available,
            'deposited' => $deposited,
            'occupied' => $occupied,
            'maintenance' => $maintenance
        ]);
    }

    // HÀM XÓA PHÒNG (ĐÃ ĐƯỢC CẬP NHẬT ĐỂ XÓA CẢ PHÒNG CON)
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $room = Room::find($id);
            
            if (!$room) {
                return response()->json(['message' => 'Không tìm thấy phòng này!'], 404);
            }

            // 1. TÌM VÀ XÓA TẤT CẢ PHÒNG CON (Nếu phòng này là phòng cha)
            $childRooms = Room::where('parent_id', $room->id)->get();
            foreach ($childRooms as $child) {
                // Xóa ảnh vật lý của phòng con trong storage
                $childImages = RoomImage::where('room_id', $child->id)->get();
                foreach ($childImages as $img) {
                    $filePath = str_replace('/storage/', '', $img->image_url);
                    Storage::disk('public')->delete($filePath);
                }
                // Xóa dữ liệu phòng con
                $child->delete();
            }

            // 2. XÓA ẢNH CỦA CHÍNH PHÒNG CHA TRONG STORAGE
            $roomImages = RoomImage::where('room_id', $room->id)->get();
            foreach ($roomImages as $img) {
                $filePath = str_replace('/storage/', '', $img->image_url);
                Storage::disk('public')->delete($filePath);
            }

            // 3. Xóa phòng cha khỏi Database
            $room->delete();

            DB::commit();
            return response()->json(['message' => 'Xóa phòng thành công!']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Lỗi khi xóa: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Nếu là phòng riêng, tự động lấy location từ cha
            $location = $request->location;
            if ($request->rent_type === 'private_room' && $request->parent_id) {
                $parent = Room::find($request->parent_id);
                if ($parent) {
                    $location = $parent->location;
                }
            }

            // 1. Lưu thông tin phòng vào bảng rooms
            $room = Room::create([
                'title' => $request->title,
                'location' => $location,
                'rent_type' => $request->rent_type,
                'parent_id' => $request->parent_id,
                'type' => $request->type,
                'price' => $request->price,
                'max_guests' => $request->max_guests,
                'max_children' => $request->max_children,
                'description' => $request->description,
                'status' => $request->status,
                'is_visible' => $request->is_visible === 'true' || $request->is_visible == 1 ? 1 : 0,
            ]);

            // 2. Lưu tiện nghi vào bảng trung gian room_amenities
            $amenities = $request->input('amenities', []);
            if (is_string($amenities)) {
                $amenities = json_decode($amenities, true) ?? [];
            }
            if (!empty($amenities) && is_array($amenities)) {
                foreach ($amenities as $amenity_id) {
                    if ($amenity_id && $amenity_id !== 'null') {
                        DB::table('room_amenities')->insert([
                            'room_id' => $room->id,
                            'amenity_id' => $amenity_id
                        ]);
                    }
                }
            }

            // 3. Upload ảnh và lưu vào bảng room_images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $key => $file) {
                    // Lưu file vào thư mục storage/app/public/rooms
                    $path = $file->store('rooms', 'public');
                    
                    RoomImage::create([
                        'room_id' => $room->id,
                        'image_url' => '/storage/' . $path,
                        'is_primary' => $key === 0 ? true : false // Ảnh đầu tiên làm ảnh bìa
                    ]);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Lưu phòng thành công!', 'room' => $room], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Lỗi khi lưu: ' . $e->getMessage()], 500);
        }
    }

    public function storeAmenity(Request $request)
    {
        $request->validate(['name' => 'required|string']);
        
        $id = DB::table('amenities')->insertGetId([
            'name' => $request->name,
            'icon' => $request->icon ?? 'star'
        ]);
        
        $amenity = DB::table('amenities')->where('id', $id)->first();
        return response()->json($amenity, 201);
    }

    public function deleteAmenity($id)
    {
        DB::table('amenities')->where('id', $id)->delete();
        DB::table('room_amenities')->where('amenity_id', $id)->delete();
        return response()->json(['message' => 'Đã xóa tiện nghi']);
    }

    public function getHomestays()
    {
        // Lấy các homestay đang cho thuê phòng lẻ
        $homestays = \App\Models\Room::where('rent_type', 'room_based')->get();
        return response()->json($homestays);
    }

    public function convertToRoomBased($id)
    {
        $room = \App\Models\Room::find($id);
        if (!$room) {
            return response()->json(['message' => 'Không tìm thấy homestay'], 404);
        }

        $room->update(['rent_type' => 'room_based']);
        return response()->json(['message' => 'Đã chuyển đổi sang mô hình cho thuê phòng lẻ thành công!', 'room' => $room]);
    }

    public function getAvailableLocations()
    {
        // Lấy danh sách các phòng/homestay đang trống để gợi ý cụ thể
        $suggestions = Room::where('status', 'available')
                         ->where('is_visible', true)
                         ->where('rent_type', '!=', 'room_based')
                         ->whereNotNull('location')
                         ->where('location', '!=', '')
                         ->select('id', 'title', 'location')
                         ->latest()
                         ->limit(15)
                         ->get();
                         
        return response()->json($suggestions);
    }
}