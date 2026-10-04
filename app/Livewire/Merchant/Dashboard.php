<?php

namespace App\Livewire\Merchant;

use App\Models\Commission;
use App\Models\Dispute;
use App\Models\GbPass;
use App\Models\Merchant;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Payment;
use App\Support\Money;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    use ResolvesMerchant;
    public ?Merchant $merchant = null;

    public function mount(): void
    {
        $this->merchant = $this->resolveMerchant();
        if (! $this->merchant) { $this->redirect(route('merchant.setup'), navigate: true); }
    }

    public function switchMerchant(string $slug): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $this->redirect(route('merchant.dashboard', ['merchant' => $slug]), navigate: true);
    }

    public function render(): View
    {
        $m = $this->merchant;
        $paid = ['payment_confirmed', 'ready_for_redemption', 'redeemed', 'fulfilled'];
        $revenue = (int) Order::where('merchant_id', $m->id)->whereIn('status', $paid)->sum('total');

        $days = collect(range(6, 0))->map(function ($ago) use ($m, $paid) {
            $day = Carbon::today()->subDays($ago);

            return [
                'label' => $day->format('D'),
                'value' => (int) Order::where('merchant_id', $m->id)
                    ->whereIn('status', $paid)->whereDate('created_at', $day)->sum('total'),
            ];
        });

        $stats = [
            ['label' => 'Revenue (paid)', 'value' => Money::formatUgx($revenue), 'icon' => 'banknotes', 'href' => '/merchant/orders'],
            ['label' => 'Payments to confirm', 'value' => Payment::where('merchant_id', $m->id)->where('status', 'reported')->count(), 'icon' => 'bell-alert', 'href' => '/merchant/payments'],
            ['label' => 'Passes ready to scan', 'value' => GbPass::where('merchant_id', $m->id)->where('status', 'ready')->count(), 'icon' => 'qr-code', 'href' => '/merchant/scan'],
            ['label' => 'Orders to fulfill', 'value' => Order::where('merchant_id', $m->id)->where('status', 'redeemed')->count(), 'icon' => 'package', 'href' => '/merchant/orders'],
            ['label' => 'Active offers', 'value' => Offer::where('merchant_id', $m->id)->where('status', 'active')->count(), 'icon' => 'tag', 'href' => '/merchant/offers'],
            ['label' => 'Open disputes', 'value' => Dispute::where('respondent_merchant_id', $m->id)->whereIn('status', ['open', 'under_review'])->count(), 'icon' => 'shield-exclamation', 'href' => '/merchant/orders'],
        ];

        return view('livewire.merchant.dashboard', [
            'stats' => $stats,
            'days' => $days,
            'maxDay' => max(1, $days->max('value')),
            'owed' => (int) Commission::where('merchant_id', $m->id)->where('status', 'pending')->sum('amount'),
            'ending' => Offer::where('merchant_id', $m->id)->where('status', 'active')->whereNotNull('ends_at')->orderBy('ends_at')->take(5)->get(),
            'recent' => Order::with('customer', 'offer')->where('merchant_id', $m->id)->latest()->take(8)->get(),
        ])->title(($m->trading_name ?? $m->business_name).' — Merchant');
    }
}
