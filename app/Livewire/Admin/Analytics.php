<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Commission;
use App\Models\Gang;
use App\Models\Merchant;
use App\Models\Offer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Analytics extends Component
{
    public function render(): View
    {
        $paid = ['payment_confirmed', 'ready_for_redemption', 'redeemed', 'fulfilled'];

        $days = collect(range(13, 0))->map(function ($ago) use ($paid) {
            $day = Carbon::today()->subDays($ago);

            return [
                'label' => $day->format('d M'),
                'value' => (int) Order::whereIn('status', $paid)->whereDate('created_at', $day)->sum('total'),
                'orders' => Order::whereDate('created_at', $day)->count(),
            ];
        });

        return view('livewire.admin.analytics', [
            'days' => $days,
            'maxDay' => max(1, $days->max('value')),
            'gmv' => (int) Order::whereIn('status', $paid)->sum('total'),
            'byStatus' => Order::selectRaw('status, COUNT(*) c, SUM(total) t')->groupBy('status')->orderByDesc('c')->get(),
            'funnel' => Gang::selectRaw('status, COUNT(*) c')->groupBy('status')->get(),
            'topMerchants' => Merchant::withSum(['orders as revenue' => fn ($q) => $q->whereIn('status', $paid)], 'total')
                ->withCount('orders')->orderByDesc('revenue')->take(5)->get(),
            'topCats' => Category::withCount(['offers', 'offers as active_offers' => fn ($q) => $q->where('status', 'active')])
                ->orderByDesc('offers_count')->take(8)->get(),
            'commissions' => Commission::selectRaw('status, COUNT(*) c, SUM(amount) t')->groupBy('status')->get(),
            'signups' => User::where('created_at', '>=', Carbon::today()->subDays(13))->count(),
            'offerCount' => Offer::count(),
        ])->title('Analytics — Admin');
    }
}
