<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;

class AdminBookingController extends Controller
{
    // 1. LẤY DANH SÁCH HÓA ĐƠN

    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = Booking::with(['customer', 'room', 'payments', 'services']);

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('booking_code', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$search}%")
                  ->orWhere('room_name', 'LIKE', "%{$search}%")
                  ->orWhere('customer_email', 'LIKE', "%{$search}%")
                  ->orWhereHas('customer', function($subQ) use ($search) {
                      $subQ->where('name', 'LIKE', "%{$search}%")
                           ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $bookings = $query->latest()
            ->get()
            ->map(function ($booking) {
                $booking->time_vn = $booking->created_at->format('H:i - d/m/Y');
                
                // Đồng bộ logic lấy tên khách hàng và phòng với Quản lý Booking
                $booking->customer_name = $booking->customer->name ?? $booking->customer_name ?? 'Khách vãng lai';
                $booking->customer_email = $booking->customer->email ?? $booking->customer_email ?? '';
                $booking->customer_phone = $booking->customer->phone ?? $booking->customer_phone ?? '';
                $booking->room_name = $booking->room->title ?? $booking->room_name ?? 'Không rõ';
                
                // Đồng bộ logic tiền tệ: total_amount là tổng, paid_amount là số tiền đã thu (cọc/thanh toán)
                $booking->total_price = $booking->total_amount;
                $booking->deposit_amount = $booking->paid_amount;
                
                // Luôn tính lại payment_status theo trạng thái thực tế (bỏ qua giá trị cũ trong DB)
                if ($booking->status === 'checked_out' || $booking->paid_amount >= $booking->total_amount) {
                    $booking->payment_status = 'completed';
                } elseif ($booking->paid_amount > 0) {
                    $booking->payment_status = 'deposited';
                } else {
                    $booking->payment_status = 'pending';
                }
                
                return $booking;
            });

        return response()->json($bookings);
    }


    // 3. XÓA HÓA ĐƠN (Nếu khách bùng cọc)
    public function destroy($id)
    {
        Booking::destroy($id);
        return response()->json(['message' => 'Đã xóa hóa đơn!']);
    }
}