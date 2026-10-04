<?php

namespace App\Livewire;

use App\Models\Gang;
use App\Models\GangMember;
use App\Models\Merchant;
use App\Models\Offer;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.storefront')]
class MerchantStorefront extends Component
{
    use WithPagination;

    public Merchant $merchant;

    public function mount(Merchant $merchant): void
    {
        abort_unless($merchant->is_active, 404);
        $this->merchant = $merchant;
    }

    public function render(): View
    {
        $m = $this->merchant;
        $offers = Offer::with(['merchant', 'category', 'gangs'])
            ->where('merchant_id', $m->id)
            ->where('status', 'active')
            ->latest()
            ->paginate(12);

        $offerIds = Offer::where('merchant_id', $m->id)->pluck('id');
        $completedGangs = Gang::whereIn('offer_id', $offerIds)
            ->whereIn('status', ['unlocked', 'completed'])->count();
        $buyers = GangMember::whereIn('offer_id', $offerIds)
            ->whereIn('status', ['payment_confirmed', 'ready', 'redeemed', 'fulfilled'])->count();

        return view('livewire.merchant-storefront', [
            'offers' => $offers,
            'completedGangs' => $completedGangs,
            'buyers' => $buyers,
            'share' => 'https://wa.me/?text='.urlencode('Check out '.($m->trading_name ?? $m->business_name).' on GBOffers: '.route('merchants.show', $m->slug)),
        ])->title(('@'.$m->slug).' — GBOffers');
    }
}
