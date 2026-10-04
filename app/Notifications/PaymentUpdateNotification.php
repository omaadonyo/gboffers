<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
class PaymentUpdateNotification extends Notification {
    use Queueable;
    public function __construct(public string $message, public ?int $orderId = null) {}
    public function via(object $n): array { return ['database','mail']; }
    public function toArray(object $n): array { return ['message'=>$this->message,'order_id'=>$this->orderId]; }
    public function toMail(object $n): \Illuminate\Notifications\Messages\MailMessage {
        return (new \Illuminate\Notifications\Messages\MailMessage)->subject('GBOffers payment update')->line($this->message)->action('View order', url('/orders'));
    }
}
