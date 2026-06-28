<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingServiceController extends Controller
{
    public function store(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $v = $request->validate(['service_name'=>'required|string','unit_price'=>'required|numeric|min:0','quantity'=>'required|integer|min:1','is_paid'=>'nullable|boolean','note'=>'nullable|string']);
        $svc = $booking->services()->create(array_merge($v, ['total_price' => (int)($v['unit_price'] * $v['quantity'])]));
        
        if (!empty($v['is_paid'])) {
            // Không cộng vào total_amount hay paid_amount vì đây là tiền thu riêng ngoài booking chính
            $booking->logActivity('service_added',"Thêm dịch vụ thu tiền mặt ngay (không tính vào bill): {$v['service_name']} × {$v['quantity']}");
        } else {
            $booking->increment('subtotal',(int)$svc->total_price);
            $booking->increment('total_amount',(int)$svc->total_price);
            $booking->logActivity('service_added',"Thêm dịch vụ: {$v['service_name']} × {$v['quantity']}");
        }
        
        return response()->json($svc, 201);
    }

    public function destroy($bookingId, $serviceId)
    {
        $booking = Booking::findOrFail($bookingId);
        $svc     = $booking->services()->findOrFail($serviceId);
        
        if (!$svc->is_paid) {
            $booking->decrement('subtotal',(int)$svc->total_price);
            $booking->decrement('total_amount',(int)$svc->total_price);
        }
        $name = $svc->service_name; $svc->delete();
        $booking->logActivity('service_removed',"Xóa dịch vụ: {$name}");
        return response()->json(['message'=>'Đã xóa dịch vụ']);
    }
}
