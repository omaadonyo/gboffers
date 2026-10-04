<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
class GangUpdateNotification extends Notification {
    use Queueable;
    public function __construct(public string $message, public ?int $gangId = null) {}
    public function via(object $n): array { return ['database','mail']; }
    public function toArray(object $n): array { return ['message'=>$this->message,'gang_id'=>$this->gangId]; }
    public function toMail(object $n): \Illuminate\Notifications\Messages\MailMessage {
        return (new \Illuminate\Notifications\Messages\MailMessage)->subject('GBOffers group update')->line($this->message)->action('View group', url('/groups'));
    }
}
