<?php

namespace App\Livewire;

use App\Models\Gang;
use App\Models\Merchant;
use App\Models\Offer;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $q = '';

    public bool $open = false;

    public function updatedQ(): void
    {
        $this->open = mb_strlen(trim($this->q)) >= 2;
    }

    public function reopen(): void
    {
        $this->open = true;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function clear(): void
    {
        $this->reset('q');
        $this->open = false;
    }

    public function render(): View
    {
        $q = trim($this->q);
        $offers = collect();
        $gangs = collect();
        $merchants = collect();
        $popular = collect();

        if ($this->open && mb_strlen($q) >= 2) {
            $offers = Offer::with(['merchant', 'gangs'])
                ->where('status', 'active')
                ->where(fn ($w) => $w->where('title', 'like', '%'.$q.'%')->orWhere('description', 'like', '%'.$q.'%'))
                ->orderByDesc('confirmed_count')
                ->take(5)
                ->get();
            $gangs = Gang::with('offer.merchant')
                ->where('status', 'forming')
                ->where(fn ($w) => $w->where('code', 'like', '%'.$q.'%')
                    ->orWhereHas('offer', fn ($o) => $o->where('title', 'like', '%'.$q.'%')))
                ->orderByDesc('confirmed_count')
                ->take(4)
                ->get();
            $merchants = Merchant::where('is_active', true)
                ->where(fn ($w) => $w->where('business_name', 'like', '%'.$q.'%')->orWhere('trading_name', 'like', '%'.$q.'%'))
                ->orderByDesc('rating_avg')
                ->take(3)
                ->get();
        }

        if ($this->open && mb_strlen($q) < 2) {
            $popular = Offer::with('merchant')
                ->where('status', 'active')
                ->orderByDesc('confirmed_count')
                ->take(5)
                ->get();
        }

        $hasResults = $offers->isNotEmpty() || $gangs->isNotEmpty() || $merchants->isNotEmpty();
        $showPopular = ! $hasResults && $popular->isNotEmpty();
        $showEmpty = ! $hasResults && ! $showPopular && mb_strlen($q) >= 2;

        return view('livewire.global-search', compact('offers', 'gangs', 'merchants', 'popular', 'hasResults', 'showPopular', 'showEmpty'));
    }
}
