<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Notifications\BookingStatusUpdated;
use App\Notifications\ReviewRequest;
use App\Models\Review;
use App\Http\Resources\BookingResource;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;


class BookingController extends Controller
{
    // GET /api/admin/bookings
    public function index(Request $request)
    {
        $query = Booking::with(['customer','room','payments'])->latest();

        if ($request->tab) {
            match ($request->tab) {
                'checkin_today'  => $query->whereDate('check_in_date', today())->whereIn('status',['confirmed','checked_in']),
                'checkout_today' => $query->whereDate('check_out_date', today())->where('status','checked_in'),
                'inhouse'        => $query->where('status','checked_in'),
                'upcoming'       => $query->whereIn('status',['pending','confirmed'])->whereBetween('check_in_date',[today(),today()->addDays(7)]),
                'cancelled'      => $query->where('status','cancelled'),
                'pending'        => $query->where('status','pending'),
                default          => null,
            };
        }

        if ($request->date_type && $request->date_from && $request->date_to) {
            $col = match($request->date_type) {
                'check_in'  => 'check_in_date',
                'check_out' => 'check_out_date',
                default     => 'created_at',
            };
            $query->whereBetween($col, [$request->date_from, $request->date_to]);
        }

        if ($request->status) {
            $statuses = is_array($request->status) ? $request->status : explode(',',$request->status);
            $query->whereIn('status', $statuses);
        }
        if ($request->source)  $query->where('source', $request->source);
        if ($request->room_id) $query->where('room_id', $request->room_id);

        if ($request->customer_search) {
            $q = $request->customer_search;
            $query->whereHas('customer', fn($s) =>
                $s->where('name','like',"%$q%")->orWhere('phone','like',"%$q%")->orWhere('email','like',"%$q%")
            );
        }
        if ($request->booking_code) {
            $query->where('booking_code','like','%'.$request->booking_code.'%');
        }

        $bookings = $query->paginate($request->per_page ?? 15);
        
        return BookingResource::collection($bookings);
    }

    // GET /api/admin/bookings/stats
    public function stats()
    {
        $totalRooms   = Room::count() ?: 1;
        $occupied     = Booking::where('status','checked_in')->count();
        return response()->json([
            'checkin_today'  => Booking::whereDate('check_in_date', today())->whereIn('status',['confirmed','pending'])->count(),
            'checkout_today' => Booking::whereDate('check_out_date', today())->where('status','checked_in')->count(),
            'inhouse'        => $occupied,
            'pending'        => Booking::where('status','pending')->count(),
            'today_revenue'  => (int) Booking::whereDate('created_at', today())->whereNotIn('status',['cancelled','no_show'])->sum('paid_amount'),
            'occupancy_rate' => round(($occupied / $totalRooms) * 100),
            'unpaid_count'   => Booking::whereRaw('paid_amount < total_amount')->whereNotIn('status',['cancelled','no_show'])->count(),
            'dirty_rooms'    => Room::where('room_status','dirty')->count(),
            'total_reviews'  => Review::count(),
            'avg_rating'     => round(Review::avg('rating') ?: 0, 1),
        ]);
    }

    // GET /api/admin/bookings/calendar
    public function calendar(Request $request)
    {
        $year  = (int)($request->year  ?? now()->year);
        $month = (int)($request->month ?? now()->month);
        $from  = \Carbon\Carbon::create($year,$month,1)->startOfMonth();
        $to    = $from->copy()->endOfMonth();
        $total = Room::count() ?: 1;

        $bookings = Booking::whereNotIn('status',['cancelled','no_show'])
            ->where('check_in_date','<=',$to)->where('check_out_date','>=',$from)
            ->select('check_in_date','check_out_date')->get();

        $days = [];
        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $ds = $d->toDateString();
            $count = $bookings->filter(fn($b) => $b->check_in_date->toDateString() <= $ds && $b->check_out_date->toDateString() > $ds)->count();
            $days[$ds] = ['bookings'=>$count, 'occupancy_rate'=>min(100,round(($count/$total)*100))];
        }
        return response()->json($days);
    }

    public function show($id)
    {
        $b = Booking::with(['customer', 'room.images', 'services', 'payments.recordedBy', 'activities', 'createdBy'])->findOrFail($id);
        
        return new BookingResource($b);
    }

    // POST /api/admin/bookings
    public function store(Request $request)
    {
        $v = $request->validate([
            'customer_id'    => 'required|exists:users,id',
            'room_id'        => 'required|exists:rooms,id',
            'check_in_date'  => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'adults'         => 'required|integer|min:1',
            'children'       => 'nullable|integer|min:0',
            'source'         => 'nullable|in:website,booking_com,agoda,walkin,phone,other',
            'discount_amount'=> 'nullable|numeric|min:0',
            'discount_type'  => 'nullable|in:percent,fixed',
            'discount_reason'=> 'nullable|string',
            'guest_note'     => 'nullable|string',
            'internal_note'  => 'nullable|string',
            'services'       => 'nullable|array',
            'services.*.service_name' => 'required_with:services|string',
            'services.*.unit_price'   => 'required_with:services|numeric',
            'services.*.quantity'     => 'required_with:services|integer|min:1',
            'deposit_amount' => 'nullable|numeric|min:0',
            'deposit_method' => 'nullable|in:cash,transfer,card',
        ]);

        DB::beginTransaction();
        try {
            $room   = Room::findOrFail($v['room_id']);
            $nights = \Carbon\Carbon::parse($v['check_in_date'])->diffInDays(\Carbon\Carbon::parse($v['check_out_date']));
            $subtotal = (int)($room->price * $nights);
            foreach ($v['services'] ?? [] as $s) { $subtotal += (int)($s['unit_price'] * $s['quantity']); }

            $disc = (int)($v['discount_amount'] ?? 0);
            if (($v['discount_type'] ?? 'fixed') === 'percent') { $disc = (int)round($subtotal * ($disc / 100)); }

            $booking = Booking::create([
                ...$v,
                'subtotal'      => $subtotal,
                'discount_amount'=> $disc,
                'total_amount'  => $subtotal - $disc,
                'paid_amount'   => 0,
                'status'        => 'pending',
                'source'        => $v['source'] ?? 'walkin',
                'created_by'    => auth('sanctum')->id(),
            ]);

            foreach ($v['services'] ?? [] as $s) {
                $booking->services()->create([
                    'service_name' => $s['service_name'],
                    'unit_price'   => (int)$s['unit_price'],
                    'quantity'     => (int)$s['quantity'],
                    'total_price'  => (int)($s['unit_price'] * $s['quantity']),
                ]);
            }

            if (!empty($v['deposit_amount']) && $v['deposit_amount'] > 0) {
                $booking->payments()->create(['amount'=>(int)$v['deposit_amount'],'payment_method'=>$v['deposit_method']??'cash','payment_type'=>'deposit','recorded_by'=>auth('sanctum')->id()]);
                $booking->increment('paid_amount', (int)$v['deposit_amount']);
            }

            $booking->logActivity('created', "Booking #{$booking->booking_code} được tạo");
            DB::commit();
            return response()->json($booking->load(['customer','room','services','payments']), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message'=>$e->getMessage()], 500);
        }
    }

    // PUT /api/admin/bookings/{id}
    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $v = $request->validate([
            'check_in_date'  => 'sometimes|date',
            'check_out_date' => 'sometimes|date|after:check_in_date',
            'adults'         => 'sometimes|integer|min:1',
            'children'       => 'nullable|integer|min:0',
            'room_id'        => 'sometimes|exists:rooms,id',
            'guest_note'     => 'nullable|string',
            'internal_note'  => 'nullable|string',
            'discount_amount'=> 'nullable|numeric|min:0',
            'discount_type'  => 'nullable|in:percent,fixed',
            'discount_reason'=> 'nullable|string',
        ]);
        $old = $booking->only(array_keys($v));
        $booking->update($v);
        if (isset($v['check_in_date'])||isset($v['check_out_date'])||isset($v['room_id'])) $booking->recalculateTotals();
        $booking->logActivity('updated', 'Thông tin booking cập nhật', $old, $v);
        return response()->json($booking->fresh(['customer','room','services','payments']));
    }

    // PATCH /api/admin/bookings/{id}/status
    public function changeStatus(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $request->validate(['status'=>['required',Rule::in(['pending','confirmed','checked_in','checked_out','cancelled','no_show'])]]);
        $old = $booking->status;
        $booking->update(['status'=>$request->status]);
        if ($request->status==='confirmed') $booking->update(['confirmed_by'=>auth('sanctum')->id()]);
        $labels = ['pending'=>'Chờ xác nhận','confirmed'=>'Đã xác nhận','checked_in'=>'Đã check-in','checked_out'=>'Đã check-out','cancelled'=>'Đã hủy','no_show'=>'No-show'];
        $booking->logActivity('status_changed',"Trạng thái: {$labels[$old]} → {$labels[$request->status]}",['status'=>$old],['status'=>$request->status]);
        
        // Notify Customer
        if ($booking->customer) {
            $booking->customer->notify(new BookingStatusUpdated($booking, "Đơn đặt phòng #{$booking->booking_code} của bạn đã chuyển sang trạng thái: {$labels[$request->status]}"));
        }

        return response()->json(['message'=>'OK','booking'=>$booking]);
    }

    // POST /api/admin/bookings/{id}/checkin
    public function checkin(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        
        $request->validate([
            'id_card_image' => 'required|image|max:10240', // Max 10MB
        ]);

        // Chấp nhận check-in từ trạng thái deposited hoặc confirmed/pending (tùy existing logic)
        if (!in_array($booking->status,['deposited','confirmed','pending'])) return response()->json(['message'=>'Không thể check-in'],422);
        DB::beginTransaction();
        try {
            if ($request->room_id && $request->room_id != $booking->room_id) {
                $old = $booking->room_id;
                $booking->update(['room_id'=>$request->room_id]);
                $booking->logActivity('room_changed','Đổi phòng khi check-in',['room_id'=>$old],['room_id'=>$request->room_id]);
            }

            // Mã hóa và lưu CCCD
            if ($request->hasFile('id_card_image')) {
                $file = $request->file('id_card_image');
                $encryptedContent = Crypt::encrypt(file_get_contents($file->getRealPath()));
                $filename = 'id_card_' . $booking->id . '_' . time() . '.dat';
                Storage::put('id_cards/' . $filename, $encryptedContent);
                $booking->id_card_image = $filename;
            }

            $booking->status = 'checked_in';
            $booking->checked_in_by = auth('sanctum')->id();
            $booking->save();

            // Theo yêu cầu: cập nhật trạng thái Room thành in_use
            $booking->room()->update(['status'=>'in_use','room_status'=>'occupied','room_status_updated_by'=>auth('sanctum')->id()]);
            $booking->logActivity('checkin','Khách đã check-in thành công (đã lưu CCCD mã hóa)');
            
            // Notify Customer
            if ($booking->customer) {
                $booking->customer->notify(new BookingStatusUpdated($booking, "Bạn đã nhận phòng (Check-in) thành công cho đơn đặt phòng #{$booking->booking_code}."));
            }

            DB::commit();
            return response()->json(['message'=>'Check-in thành công','booking'=>$booking->fresh(['customer','room'])]);
        } catch (\Exception $e) { DB::rollBack(); return response()->json(['message'=>$e->getMessage()],500); }
    }

    // GET /api/admin/bookings/{id}/id-card
    public function viewIdCard($id)
    {
        $booking = Booking::findOrFail($id);
        if (!$booking->id_card_image) {
            return response()->json(['message' => 'Không có hình ảnh CCCD'], 404);
        }

        $path = 'id_cards/' . $booking->id_card_image;
        if (!Storage::exists($path)) {
            return response()->json(['message' => 'Tệp không tồn tại'], 404);
        }

        try {
            $encryptedContent = Storage::get($path);
            $decryptedContent = Crypt::decrypt($encryptedContent);
            
            return response($decryptedContent)->header('Content-Type', 'image/jpeg');
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi khi giải mã hình ảnh: ' . $e->getMessage()], 500);
        }
    }


    // POST /api/admin/bookings/{id}/checkout
    public function checkout(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        if ($booking->status !== 'checked_in') return response()->json(['message'=>'Booking phải ở Checked-in'],422);

        // Kiểm tra ngày: nếu chưa đến ngày check-out thì phải xác nhận trả sớm
        $checkoutDate = \Carbon\Carbon::parse($booking->check_out_date)->startOfDay();
        $today        = \Carbon\Carbon::today();
        $isEarly      = $today->lt($checkoutDate);

        if ($isEarly && !$request->boolean('early_checkout')) {
            return response()->json([
                'message'      => 'Khách chưa đến ngày trả phòng. Vui lòng xác nhận trả phòng sớm.',
                'is_early'     => true,
                'checkout_date'=> $checkoutDate->toDateString(),
                'today'        => $today->toDateString(),
            ], 422);
        }

        $request->validate([
            'additional_fee' => 'nullable|numeric|min:0',
            'additional_note'=> 'nullable|string',
            'payment_method' => 'nullable|in:cash,transfer,card'
        ]);

        DB::beginTransaction();
        try {
            $addFee = (float)($request->additional_fee ?? 0);
            
            if ($addFee > 0) {
                // Tạo service thay vì chỉ cộng tiền
                $note = $request->additional_note ?: 'Phụ thu khi trả phòng';
                $booking->services()->create([
                    'service_name' => $note,
                    'unit_price' => $addFee,
                    'quantity' => 1,
                    'total_price' => $addFee,
                    'is_paid' => false,
                ]);
                $booking->increment('total_amount', (int)$addFee);
                $booking->increment('subtotal', (int)$addFee);
                $booking->logActivity('service_added', "Thêm dịch vụ lúc check-out: {$note}");
            }
            
            $final_paid = $booking->total_amount - $booking->paid_amount;
            
            if ($final_paid > 0) {
                $booking->payments()->create([
                    'amount' => $final_paid,
                    'payment_method' => $request->payment_method ?? 'cash',
                    'payment_type' => 'balance',
                    'recorded_by' => auth('sanctum')->id(),
                    'note' => 'Thanh toán khi check-out'
                ]);
                $booking->increment('paid_amount', $final_paid);
            }

            // Update booking status
            $booking->update([
                'status' => 'checked_out',
                'checked_out_by' => auth('sanctum')->id(),
                'paid_at' => now(),
            ]);

            // Cập nhật trạng thái Room thành maintenance
            $booking->room()->update([
                'status' => 'maintenance',
                'room_status' => 'dirty',
                'room_status_updated_by' => auth('sanctum')->id()
            ]);

            // Tự động chuyển phòng sang Available sau 1h
            dispatch(new \App\Jobs\CleanRoomJob($booking->room_id))->delay(now()->addHour());

            $booking->logActivity('checkout','Khách đã check-out. Phòng chuyển sang bảo trì/dọn dẹp.');
            
            // Notify Customer
            if ($booking->customer) {
                $booking->customer->notify(new BookingStatusUpdated($booking, "Bạn đã trả phòng (Check-out) thành công cho đơn đặt phòng #{$booking->booking_code}."));
                // Gửi thêm thông báo yêu cầu đánh giá
                $booking->customer->notify(new ReviewRequest($booking));
            }

            DB::commit();
            return response()->json(['message'=>'Check-out thành công','booking'=>$booking->fresh(['customer','room','payments'])]);
        } catch (\Exception $e) { DB::rollBack(); return response()->json(['message'=>$e->getMessage()],500); }
    }

    // POST /api/admin/bookings/{id}/cancel
    public function cancel(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        // Chỉ cho hủy khi đang ở trạng thái deposited hoặc pending (chưa check-in)
        if (!in_array($booking->status, ['deposited', 'pending', 'confirmed'])) {
            return response()->json(['message' => 'Chỉ có thể hủy booking khi chưa check-in (đã cọc hoặc chờ xử lý)'], 422);
        }
        $request->validate(['cancel_reason'=>'required|string','refund_amount'=>'nullable|numeric|min:0','refund_method'=>'nullable|in:cash,transfer,card']);
        DB::beginTransaction();
        try {
            $booking->update(['status'=>'cancelled','cancel_reason'=>$request->cancel_reason,'cancelled_at'=>now(),'refund_amount'=>(int)($request->refund_amount??0)]);
            if ((int)($request->refund_amount??0) > 0) {
                $booking->payments()->create(['amount'=>(int)$request->refund_amount,'payment_method'=>$request->refund_method??'transfer','payment_type'=>'refund','recorded_by'=>auth('sanctum')->id(),'note'=>'Hoàn tiền: '.$request->cancel_reason]);
            }
            
            // Cập nhật trạng thái phòng thành available
            $booking->room()->update([
                'status' => 'available',
                'room_status' => 'available'
            ]);

            $booking->logActivity('cancelled',"Booking hủy: {$request->cancel_reason}");
            
            // Notify Customer
            if ($booking->customer) {
                $booking->customer->notify(new BookingStatusUpdated($booking, "Đơn đặt phòng #{$booking->booking_code} của bạn đã bị hủy. Lý do: {$request->cancel_reason}"));
            }

            // Notify All Admins
            $adminUsers = \App\Models\User::where('role', 'admin')->get();
            \Illuminate\Support\Facades\Notification::send($adminUsers, new \App\Notifications\BookingCancelledAdmin($booking));

            DB::commit();
            return response()->json(['message'=>'Hủy thành công','booking'=>$booking->fresh()]);
        } catch (\Exception $e) { DB::rollBack(); return response()->json(['message'=>$e->getMessage()],500); }
    }

    // POST /api/admin/bookings/{id}/confirm-payment
    // Admin xác nhận đã nhận tiền chuyển khoản ngân hàng
    public function confirmPayment(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'pending') {
            return response()->json(['message' => 'Chỉ có thể xác nhận thanh toán cho đơn đang ở trạng thái Chờ xử lý (pending).'], 422);
        }

        DB::beginTransaction();
        try {
            // Cập nhật trạng thái đơn thành confirmed
            $booking->update([
                'status' => 'confirmed',
                'paid_amount' => $booking->deposit_amount,
            ]);

            // Ghi nhận giao dịch thanh toán cọc
            $booking->payments()->create([
                'amount' => $booking->deposit_amount,
                'payment_method' => 'transfer',
                'payment_type' => 'deposit',
                'recorded_by' => auth('sanctum')->id(),
                'note' => $request->note ?? 'Admin xác nhận đã nhận tiền chuyển khoản ngân hàng',
            ]);

            // Ghi log hoạt động
            $booking->logActivity('status_changed', 'Admin xác nhận nhận tiền cọc qua chuyển khoản → Đã xác nhận');

            // Thông báo cho khách hàng
            if ($booking->customer) {
                $booking->customer->notify(new \App\Notifications\BookingStatusUpdated(
                    $booking,
                    "Tuyệt vời! Đơn đặt phòng #{$booking->booking_code} của bạn đã được xác nhận. Hẹn gặp bạn vào ngày {$booking->check_in_date}!"
                ));
            }

            DB::commit();
            return response()->json([
                'message' => 'Xác nhận nhận tiền thành công! Đơn đặt phòng đã chuyển sang trạng thái Đã xác nhận.',
                'booking' => $booking->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // POST /api/admin/bookings/{id}/transfer-room
    public function transferRoom(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'checked_in') {
            return response()->json(['message' => 'Chỉ có thể đổi phòng khi khách đang ở (checked_in)'], 422);
        }

        $request->validate(['new_room_id' => 'required|exists:rooms,id']);

        $newRoomId = $request->new_room_id;
        if ($newRoomId == $booking->room_id) {
            return response()->json(['message' => 'Phòng mới phải khác phòng hiện tại'], 422);
        }

        $newRoom = Room::findOrFail($newRoomId);
        if ($newRoom->status !== 'available') {
            return response()->json(['message' => 'Phòng đã chọn không còn trống'], 422);
        }

        DB::beginTransaction();
        try {
            $oldRoomId = $booking->room_id;
            $oldRoom   = Room::findOrFail($oldRoomId);

            // Phòng cũ -> dọn dẹp/bảo trì
            $oldRoom->update(['status' => 'maintenance', 'room_status' => 'dirty']);

            // Phòng mới -> đang sử dụng
            $newRoom->update(['status' => 'in_use', 'room_status' => 'occupied']);

            // Gán booking sang phòng mới
            $booking->update(['room_id' => $newRoomId]);

            $booking->logActivity(
                'room_transferred',
                "Đổi phòng: #{$oldRoomId} → #{$newRoomId}. Lý do: " . ($request->reason ?? 'Không rõ'),
                ['room_id' => $oldRoomId],
                ['room_id' => $newRoomId]
            );

            DB::commit();
            return response()->json([
                'message' => "Đổi phòng thành công! Phòng cũ đang được dọn dẹp.",
                'booking' => $booking->fresh(['customer', 'room'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
