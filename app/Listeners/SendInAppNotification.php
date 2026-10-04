<?php

namespace App\Listeners;

use App\Events\GangCompleted;
use App\Events\GBPassIssued;
use App\Events\GBPassRedeemed;
use App\Events\MerchantVerified;
use App\Events\OrderDisputed;
use App\Events\OrderFulfilled;
use App\Events\PaymentConfirmed;
use App\Events\PaymentReported;
use App\Events\ReservationExpired;
use App\Models\User;
use App\Notifications\GangUpdateNotification;
use App\Notifications\PaymentUpdateNotification;
use App\Notifications\PlatformNotification;

class SendInAppNotification
{
    public function handle(object $event): void
    {
        if ($event instanceof GangCompleted) {
            foreach ($event->gang->members()->with('user')->get() as $m) {
                $m->user?->notify(new GangUpdateNotification('Your group unlocked the group price!', $event->gang->id));
            }
        }
        if ($event instanceof PaymentReported) {
            $event->order->merchant->owner?->notify(new PaymentUpdateNotification('Customer reported payment: '.$event->order->payment_reference, $event->order->id));
        }
        if ($event instanceof PaymentConfirmed) {
            $event->order->customer?->notify(new PaymentUpdateNotification('Payment confirmed. Your GBPass is ready.', $event->order->id));
        }
        if ($event instanceof GBPassIssued) {
            $event->pass->customer?->notify(new PlatformNotification(
                'GBPass ready',
                'Your pass '.$event->pass->token.' is ready to redeem.',
                '/wallet',
                'ticket',
            ));
        }
        if ($event instanceof GBPassRedeemed) {
            $event->pass->merchant->owner?->notify(new PlatformNotification(
                'GBPass redeemed',
                'Pass '.$event->pass->token.' was redeemed at your shop.',
                '/merchant/orders',
                'ticket',
                mail: false,
            ));
            $event->pass->customer?->notify(new PlatformNotification(
                'Pass redeemed',
                'Your pass '.$event->pass->token.' was redeemed. Enjoy!',
                '/wallet',
                'ticket',
                mail: false,
            ));
        }
        if ($event instanceof OrderFulfilled) {
            $event->order->customer?->notify(new PlatformNotification(
                'Order fulfilled',
                'Order #'.$event->order->id.' is fulfilled. Enjoy your purchase!',
                '/orders',
                'shopping-cart',
            ));
        }
        if ($event instanceof ReservationExpired) {
            $event->reservation->user?->notify(new PlatformNotification(
                'Reservation expired',
                'Your held spot was released. Join again before the group fills up.',
                '/groups',
                'clock',
                mail: false,
            ));
        }
        if ($event instanceof MerchantVerified) {
            $event->merchant->owner?->notify(new PlatformNotification(
                'Merchant approved',
                ($event->merchant->trading_name ?? $event->merchant->business_name).' is verified. Start listing offers!',
                '/merchant',
                'store',
            ));
        }
        if ($event instanceof OrderDisputed) {
            $d = $event->dispute;
            $d->merchant?->owner?->notify(new PlatformNotification(
                'Dispute opened',
                'A buyer opened a dispute on order #'.$d->order_id.': '.$d->reason,
                '/merchant/orders',
                'shield-exclamation',
            ));
            foreach (User::where('role', 'admin')->get() as $admin) {
                $admin->notify(new PlatformNotification(
                    'Dispute opened',
                    'Order #'.$d->order_id.': '.$d->reason,
                    '/admin/disputes',
                    'shield-exclamation',
                ));
            }
        }
    }
}
