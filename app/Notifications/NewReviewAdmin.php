<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewReviewAdmin extends Notification
{
    use Queueable;

    public $review;

    public function __construct($review)
    {
        $this->review = $review;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'review_id' => $this->review->id,
            'title' => '⭐ Đánh giá mới',
            'message' => "Bạn có một đánh giá {$this->review->rating} sao mới từ khách hàng {$this->review->user->name} cho phòng {$this->review->room->title}.",
            'type' => 'new_review',
            'action_url' => "/admin/reviews"
        ];
    }
}
