<?php

namespace App\Livewire\Admin;

use App\Models\Offer;
use App\Models\OfferView;
use App\Models\SearchTerm;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Insights extends Component
{
    public function render(): View
    {
        $days = collect(range(13, 0))->map(function ($ago) {
            $day = Carbon::today()->subDays($ago);

            return [
                'label' => $day->format('d M'),
                'value' => OfferView::whereDate('created_at', $day)->count(),
            ];
        });

        return view('livewire.admin.insights', [
            'days' => $days,
            'maxDay' => max(1, $days->max('value')),
            'totalViews' => OfferView::count(),
            'topOffers' => Offer::with('merchant')->withCount('viewLogs')->orderByDesc('view_logs_count')->take(10)->get(),
            'topTerms' => SearchTerm::orderByDesc('hits')->take(15)->get(),
            'totalSearches' => (int) SearchTerm::sum('hits'),
            'totalShares' => \App\Models\Share::count(),
            'sharesByChannel' => \App\Models\Share::selectRaw('channel, COUNT(*) c')->groupBy('channel')->get(),
            'topShared' => Offer::with('merchant')->withCount('shares')->orderByDesc('shares_count')->take(5)->get(),
        ])->title('Insights — Admin');
    }
}
