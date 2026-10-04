<?php

namespace App\Jobs;

use App\Models\FeaturedListing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExpireFeaturedListingsJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        FeaturedListing::with('offer')->where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now())
            ->chunkById(100, function ($rows) {
                foreach ($rows as $listing) {
                    $listing->update(['status' => 'expired']);
                    $listing->offer?->update(['featured' => false]);
                }
            });
    }
}
