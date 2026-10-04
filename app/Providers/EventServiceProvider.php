<?php

namespace App\Providers;

use App\Events\GangCompleted;
use App\Events\GangJoined;
use App\Events\GBPassIssued;
use App\Events\GBPassRedeemed;
use App\Events\MerchantVerified;
use App\Events\OrderDisputed;
use App\Events\OrderFulfilled;
use App\Events\PaymentConfirmed;
use App\Events\PaymentReported;
use App\Events\ReservationExpired;
use App\Listeners\RecordCommission;
use App\Listeners\RecordTransactionEvent;
use App\Listeners\SendInAppNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [SendEmailVerificationNotification::class],
        GangJoined::class => [RecordTransactionEvent::class],
        GangCompleted::class => [RecordTransactionEvent::class, SendInAppNotification::class],
        PaymentReported::class => [RecordTransactionEvent::class, SendInAppNotification::class],
        PaymentConfirmed::class => [RecordTransactionEvent::class, SendInAppNotification::class, RecordCommission::class],
        GBPassIssued::class => [RecordTransactionEvent::class, SendInAppNotification::class],
        GBPassRedeemed::class => [RecordTransactionEvent::class, SendInAppNotification::class],
        OrderFulfilled::class => [RecordTransactionEvent::class, SendInAppNotification::class],
        OrderDisputed::class => [RecordTransactionEvent::class, SendInAppNotification::class],
        ReservationExpired::class => [RecordTransactionEvent::class, SendInAppNotification::class],
        MerchantVerified::class => [RecordTransactionEvent::class, SendInAppNotification::class],
    ];
}
