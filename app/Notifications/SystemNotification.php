<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification
{
    use Queueable;

    public $title;
    public $message;
    public $url;
    public $icon;
    public $color;

    public function __construct($title, $message, $url = '#', $icon = 'ph-bell', $color = 'primary')
    {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
        $this->icon = $icon;
        $this->color = $color;
    }

    public function via($notifiable)
    {
        return ['database']; // Simpan ke tabel notifications
    }

    public function toArray($notifiable)
    {
        return [
            'title'   => $this->title,
            'message' => $this->message,
            'url'     => $this->url,
            'icon'    => $this->icon,
            'color'   => $this->color,
            'time'    => now()
        ];
    }
}
