<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewBookingAdmin extends Notification
{
    use Queueable;

    public $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'title' => '🔔 Đơn đặt phòng mới',
            'message' => "Khách hàng {$this->booking->customer_name} vừa đặt phòng {$this->booking->room_name} - Mã: {$this->booking->booking_code}. Vui lòng xác nhận!",
            'type' => 'new_booking',
            'action_url' => "/admin/booking-management"
        ];
    }
}
