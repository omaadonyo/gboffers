<?php

namespace App\Livewire\Admin;

use App\Models\Commission;
use App\Models\Dispute;
use App\Models\Gang;
use App\Models\Merchant;
use App\Models\Offer;
use App\Models\Order;
use App\Models\User;
use App\Support\Money;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public function render(): View
    {
        $paidStatuses = ['payment_confirmed', 'ready_for_redemption', 'redeemed', 'fulfilled'];
        $gmv = (int) Order::whereIn('status', $paidStatuses)->sum('total');

        $days = collect(range(6, 0))->map(function ($ago) use ($paidStatuses) {
            $day = Carbon::today()->subDays($ago);

            return [
                'label' => $day->format('D'),
                'value' => (int) Order::whereIn('status', $paidStatuses)
                    ->whereDate('created_at', $day)->sum('total'),
            ];
        });
        $maxDay = max(1, $days->max('value'));

        $totalGangs = Gang::count();
        $stats = [
            ['label' => 'Gross volume', 'value' => Money::formatUgx($gmv), 'icon' => 'banknotes', 'href' => route('admin.orders')],
            ['label' => 'Transactions', 'value' => number_format(Order::count()), 'icon' => 'receipt-percent', 'href' => route('admin.orders')],
            ['label' => 'Groups forming', 'value' => number_format(Gang::where('status', 'forming')->count()), 'icon' => 'users', 'href' => route('admin.groups')],
            ['label' => 'Completion rate', 'value' => $totalGangs ? round(Gang::whereIn('status', ['unlocked', 'completed'])->count() / $totalGangs * 100, 1).'%' : '—', 'icon' => 'chart-bar', 'href' => route('admin.groups')],
            ['label' => 'Active offers', 'value' => number_format(Offer::where('status', 'active')->count()), 'icon' => 'tag', 'href' => route('admin.offers')],
            ['label' => 'Merchants', 'value' => number_format(Merchant::count()), 'icon' => 'store', 'href' => route('admin.merchants')],
            ['label' => 'Users', 'value' => number_format(User::count()), 'icon' => 'user-group', 'href' => route('admin.users')],
            ['label' => 'Open disputes', 'value' => number_format(Dispute::whereIn('status', ['open', 'under_review'])->count()), 'icon' => 'shield-exclamation', 'href' => route('admin.disputes')],
        ];

        return view('livewire.admin.dashboard', [
            'stats' => $stats,
            'days' => $days,
            'maxDay' => $maxDay,
            'pendingMerchants' => Merchant::where('verification_status', 'pending')->latest()->take(5)->get(),
            'openDisputes' => Dispute::whereIn('status', ['open', 'under_review'])->latest()->take(5)->get(),
            'recentOrders' => Order::with(['customer', 'merchant'])->latest()->take(8)->get(),
            'commissionPending' => (int) Commission::where('status', 'pending')->sum('amount'),
        ])->title('Admin — GBOffers');
    }
}
