<?php

namespace App\Livewire\Merchant;

use App\Models\Gang;
use App\Models\Merchant;
use App\Models\Offer;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Analytics extends Component
{
    use ResolvesMerchant;

    public ?Merchant $merchant = null;

    public function mount(): void
    {
        $this->merchant = $this->resolveMerchant();
        if (! $this->merchant) { $this->redirect(route('merchant.setup'), navigate: true); }
    }

    public function render(): View
    {
        $m = $this->merchant;
        $paid = ['payment_confirmed', 'ready_for_redemption', 'redeemed', 'fulfilled'];

        $days = collect(range(13, 0))->map(function ($ago) use ($m, $paid) {
            $day = Carbon::today()->subDays($ago);

            return [
                'label' => $day->format('d M'),
                'value' => (int) Order::where('merchant_id', $m->id)->whereIn('status', $paid)->whereDate('created_at', $day)->sum('total'),
            ];
        });

        $offerIds = Offer::where('merchant_id', $m->id)->pluck('id');
        $gangs = Gang::whereIn('offer_id', $offerIds)->get();
        $fill = $gangs->isNotEmpty()
            ? round($gangs->avg(fn ($g) => $g->target > 0 ? min(100, $g->confirmed_count / $g->target * 100) : 0), 1)
            : 0;

        return view('livewire.merchant.analytics', [
            'days' => $days,
            'maxDay' => max(1, $days->max('value')),
            'revenue' => (int) Order::where('merchant_id', $m->id)->whereIn('status', $paid)->sum('total'),
            'orders' => Order::where('merchant_id', $m->id)->count(),
            'byStatus' => Order::where('merchant_id', $m->id)->selectRaw('status, COUNT(*) c, SUM(total) t')->groupBy('status')->orderByDesc('c')->get(),
            'topOffers' => Offer::where('merchant_id', $m->id)->withSum(['orders as revenue' => fn ($q) => $q->whereIn('status', $paid)], 'total')
                ->withCount('orders')->orderByDesc('revenue')->take(5)->get(),
            'gangsFormed' => $gangs->count(),
            'gangsCompleted' => $gangs->whereIn('status', ['unlocked', 'completed'])->count(),
            'avgFill' => $fill,
        ])->title('Analytics — Merchant');
    }
}
