<?php

use App\Jobs\ExpireFeaturedListingsJob;
use App\Jobs\ExpireReservationsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new ExpireReservationsJob)->everyFiveMinutes()->name('expire-reservations');
Schedule::job(new ExpireFeaturedListingsJob)->daily()->name('expire-featured');
