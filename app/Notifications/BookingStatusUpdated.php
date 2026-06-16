<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingStatusUpdated extends Notification
{
    use Queueable;

    public $booking;
    public $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($booking, $message)
    {
        $this->booking = $booking;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // Chỉ dùng database để tránh lỗi mail chưa cấu hình SMTP
        // Bật lại 'mail' khi đã thiết lập SMTP trong .env
        $channels = ['database'];
        
        // Tự động kích hoạt mail nếu MAIL_HOST đã được cấu hình
        if (config('mail.mailers.smtp.host') && config('mail.mailers.smtp.host') !== 'mailpit') {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
                    ->subject('Cập nhật trạng thái đặt phòng - #' . $this->booking->booking_code)
                    ->greeting('Chào ' . $notifiable->name . ',')
                    ->line($this->message)
                    ->action('Xem chi tiết hóa đơn', url('/payment-success?id=' . $this->booking->id))
                    ->line('Cảm ơn bạn đã lựa chọn dịch vụ của chúng tôi!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'message' => $this->message,
            'status' => $this->booking->status,
            'title' => 'Cập nhật trạng thái đơn hàng',
            'action_url' => '/profile?tab=history',
        ];
    }
}
