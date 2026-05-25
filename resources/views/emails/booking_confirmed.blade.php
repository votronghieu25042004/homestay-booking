@component('mail::message')
# Xác nhận đặt phòng thành công!

Chào **{{ $booking->customer_name }}**,

Cảm ơn bạn đã tin tưởng và lựa chọn **Duy Homestay**. Chúng tôi rất vui mừng xác nhận rằng đơn đặt phòng của bạn đã thành công.

### Thông tin đơn hàng:
- **Mã đặt phòng:** #{{ $booking->booking_code }}
- **Phòng:** {{ $booking->room_name }}
- **Ngày nhận phòng:** {{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }}
- **Ngày trả phòng:** {{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}

### Chi tiết thanh toán:
- **Tổng cộng:** {{ number_format($booking->total_amount, 0, ',', '.') }}đ
- **Đã thanh toán cọc (30%):** {{ number_format($booking->deposit_amount, 0, ',', '.') }}đ
- **Số tiền còn lại:** {{ number_format($booking->total_amount - $booking->deposit_amount, 0, ',', '.') }}đ

Bạn có thể xem chi tiết hóa đơn và quản lý lịch sử đặt phòng tại trang cá nhân của mình.

@component('mail::button', ['url' => config('app.url') . '/payment-success?id=' . $booking->id])
Xem chi tiết hóa đơn
@endcomponent

Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi qua hotline: **090 123 4567**.

Trân trọng,<br>
Đội ngũ {{ config('app.name') }}
@endcomponent
