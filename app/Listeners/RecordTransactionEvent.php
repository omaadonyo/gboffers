<?php
namespace App\Listeners;
use App\Events\GangCompleted;
use App\Events\GangJoined;
use App\Events\GBPassIssued;
use App\Events\GBPassRedeemed;
use App\Events\OrderDisputed;
use App\Events\OrderFulfilled;
use App\Events\PaymentConfirmed;
use App\Events\PaymentReported;
use App\Events\ReservationExpired;
use App\Models\TransactionEvent;
class RecordTransactionEvent {
    public function handle(object $event): void {
        $map = [
            GangJoined::class => 'gang.joined', GangCompleted::class => 'gang.completed',
            PaymentReported::class => 'payment.reported', PaymentConfirmed::class => 'payment.confirmed',
            GBPassIssued::class => 'gbpass.issued', GBPassRedeemed::class => 'gbpass.redeemed',
            OrderFulfilled::class => 'order.fulfilled', OrderDisputed::class => 'order.disputed',
            ReservationExpired::class => 'reservation.expired',
        ];
        $type = $map[$event::class] ?? 'unknown';
        $orderId = $event->order->id ?? $event->pass->order_id ?? $event->dispute->order_id ?? null;
        $gangId = $event->gang->id ?? $event->order->gang_id ?? $event->pass->gang_id ?? null;
        TransactionEvent::create(['order_id'=>$orderId,'gang_id'=>$gangId,'actor_id'=>$event->user->id ?? $event->staff->id ?? null,'type'=>$type,'payload'=>json_encode(['event'=>$event::class])]);
    }
}
