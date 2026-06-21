<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DailyReminderAdmin extends Notification
{
    use Queueable;

    public $stats;

    public function __construct($stats)
    {
        $this->stats = $stats;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => '📅 Nhắc nhở công việc hôm nay',
            'message' => "Hôm nay có {$this->stats['checkins']} phòng cần Check-in và {$this->stats['checkouts']} phòng cần Check-out. Hãy chuẩn bị dọn dẹp!",
            'type' => 'daily_reminder',
            'action_url' => "/admin/booking-management"
        ];
    }
}
