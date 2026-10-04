<?php
namespace App\Listeners;
use App\Events\PaymentConfirmed;
use App\Services\CommissionService;
class RecordCommission {
    public function handle(PaymentConfirmed $event): void {
        app(CommissionService::class)->record($event->order);
        $m = $event->order->merchant;
        $m->increment('completed_count');
    }
}
