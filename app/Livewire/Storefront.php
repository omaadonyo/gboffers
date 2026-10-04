<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Offer;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.storefront')]
class Storefront extends Component
{
    public function render(): View
    {
        $with = ['merchant', 'category', 'gangs'];
        $forming = Offer::with($with)->where('status', 'active')->orderByDesc('featured')->orderByDesc('confirmed_count')->take(8)->get();
        $ending = Offer::with($with)->where('status', 'active')->whereNotNull('ends_at')->orderBy('ends_at')->take(6)->get();
        $spotlight = Offer::with($with)->where('status', 'active')
            ->whereHas('category', fn ($q) => $q->whereIn('slug', ['food', 'electronics']))
            ->orderByDesc('confirmed_count')->take(4)->get();
        $trending = Offer::with($with)->where('status', 'active')->orderByDesc('featured')->orderByDesc('confirmed_count')->take(10)->get();
        $dealOfDay = Offer::with($with)->where('status', 'active')->where('confirmed_count', '>', 0)
            ->orderByDesc('confirmed_count')->first();
        $cats = Category::where('is_active', true)->orderBy('sort')->get();

        return view('livewire.storefront', compact('forming', 'ending', 'spotlight', 'trending', 'dealOfDay', 'cats'))->title('GBOffers — Group up. Pay less.');
    }
}
