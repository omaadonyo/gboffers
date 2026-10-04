<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PlatformNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $body = '',
        public string $url = '/dashboard',
        public string $icon = 'bell',
        public bool $mail = true,
    ) {}

    public function via(object $notifiable): array
    {
        return $this->mail ? ['database', 'mail'] : ['database'];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        $name = method_exists($notifiable, 'displayName') ? ($notifiable->displayName() ?? $notifiable->name) : $notifiable->name;

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject($this->title.' — GBOffers')
            ->greeting('Hello '.$name.',')
            ->line($this->body)
            ->action('View in GBOffers', url($this->url))
            ->line('You can manage notifications from your account at any time.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'url' => $this->url,
            'icon' => $this->icon,
        ];
    }
}
