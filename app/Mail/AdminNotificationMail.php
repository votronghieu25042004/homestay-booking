<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $title;
    public string $body;
    public ?string $actionUrl;
    public ?string $actionLabel;
    public string $emoji;

    public function __construct(string $title, string $body, string $emoji = '🔔', ?string $actionUrl = null, ?string $actionLabel = null)
    {
        $this->title = $title;
        $this->body = $body;
        $this->emoji = $emoji;
        $this->actionUrl = $actionUrl;
        $this->actionLabel = $actionLabel;
    }

    public function build()
    {
        return $this->subject("{$this->emoji} {$this->title} - Duy Homestay")
                    ->html($this->buildHtml());
    }

    private function buildHtml(): string
    {
        $actionBtn = '';
        if ($this->actionUrl && $this->actionLabel) {
            $actionBtn = "<div style='text-align:center;margin-top:28px;'>
                <a href='{$this->actionUrl}' style='background:#4A7055;color:white;padding:12px 32px;border-radius:10px;text-decoration:none;font-weight:bold;font-size:15px;display:inline-block;'>
                    {$this->actionLabel}
                </a>
            </div>";
        }

        return "<!DOCTYPE html>
<html lang='vi'>
<head><meta charset='UTF-8'><meta name='viewport' content='width=device-width,initial-scale=1'></head>
<body style='margin:0;padding:0;background:#f4f7f6;font-family:Arial,sans-serif;'>
  <div style='max-width:580px;margin:40px auto;background:white;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);'>
    <div style='background:linear-gradient(135deg,#4A7055,#2d4a37);padding:32px 36px;text-align:center;'>
      <div style='font-size:48px;margin-bottom:8px;'>{$this->emoji}</div>
      <h1 style='color:white;margin:0;font-size:22px;font-weight:bold;'>{$this->title}</h1>
      <p style='color:rgba(255,255,255,0.7);margin:6px 0 0;font-size:13px;'>Duy Homestay · Thông báo quản trị</p>
    </div>
    <div style='padding:32px 36px;'>
      <div style='background:#f8faf9;border-left:4px solid #4A7055;border-radius:4px;padding:16px 20px;font-size:15px;color:#374151;line-height:1.7;'>
        {$this->body}
      </div>
      {$actionBtn}
      <p style='margin-top:28px;font-size:12px;color:#9ca3af;text-align:center;border-top:1px solid #f0f0f0;padding-top:20px;'>
        Email này được gửi tự động từ hệ thống Duy Homestay.<br/>Vui lòng không trả lời email này.
      </p>
    </div>
  </div>
</body>
</html>";
    }
}
