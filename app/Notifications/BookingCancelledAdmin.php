<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingCancelledAdmin extends Notification
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
            'title' => '❌ Đơn đặt phòng đã bị hủy',
            'message' => "Đơn đặt phòng {$this->booking->booking_code} đã bị hủy. Room {$this->booking->room_name} hiện đã trống lại.",
            'type' => 'booking_cancelled',
            'status' => 'cancelled',
            'action_url' => "/admin/booking-management"
        ];
    }
}
