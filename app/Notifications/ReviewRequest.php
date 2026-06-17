<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReviewRequest extends Notification
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
        $roomTitle = $this->booking->room->title ?? 'phòng';
        return [
            'booking_id'  => $this->booking->id,
            'room_id'     => $this->booking->room_id,
            'title'       => 'Để lại đánh giá ⭐',
            'message'     => "Kỳ nghỉ của bạn tại {$roomTitle} đã kết thúc. Hãy chia sẻ cảm nhận của bạn nhé!",
            'type'        => 'review_request',
            'action_url'  => "/profile?tab=history"
        ];
    }
}
