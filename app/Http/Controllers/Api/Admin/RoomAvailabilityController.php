<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomAvailabilityController extends Controller
{
    // GET /api/admin/rooms/available?check_in=&check_out=
    public function available(Request $request)
    {
        $request->validate(['check_in'=>'required|date','check_out'=>'required|date|after:check_in']);
        $rooms = Room::with('images')
            ->available($request->check_in, $request->check_out)
            ->get()->map(fn($r) => [
                'id'          => $r->id,
                'room_number' => $r->room_number,
                'title'       => $r->title,
                'type'        => $r->type,
                'floor'       => $r->floor,
                'price'       => $r->price,
                'max_guests'  => $r->max_guests,
                'room_status' => $r->room_status,
                'image'       => $r->images->first()?->url ?? null,
            ]);
        return response()->json($rooms);
    }

    // GET /api/admin/rooms/all-status
    public function allRooms()
    {
        return response()->json(Room::select('id','room_number','title','type','floor','price','room_status','out_of_order_reason')->get());
    }

    // PATCH /api/admin/rooms/{id}/status
    public function updateStatus(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        $request->validate(['room_status'=>'required|in:available,occupied,dirty,out_of_order','out_of_order_reason'=>'nullable|string','estimated_fix_date'=>'nullable|date']);
        $old = $room->room_status;
        $room->update([...$request->only(['room_status','out_of_order_reason','estimated_fix_date']),'room_status_updated_by'=>auth('sanctum')->id()]);
        return response()->json(['message'=>"Phòng {$room->room_number}: {$old} → {$request->room_status}",'room'=>$room]);
    }

    // PATCH /api/admin/rooms/{id}/toggle-maintenance
    public function toggleMaintenance($id)
    {
        $room = Room::findOrFail($id);
        
        // Chỉ cho phép toggle nếu phòng đang available hoặc maintenance
        if (!in_array($room->status, ['available', 'maintenance'])) {
            return response()->json(['message' => 'Phòng đang được sử dụng hoặc đã đặt cọc, không thể chuyển đổi trạng thái'], 422);
        }

        $newStatus = $room->status === 'available' ? 'maintenance' : 'available';
        
        $room->update(['status' => $newStatus]);
        
        return response()->json([
            'message' => "Đã chuyển phòng thành " . ($newStatus === 'available' ? 'Sẵn sàng' : 'Bảo trì'),
            'room' => $room
        ]);
    }
}
