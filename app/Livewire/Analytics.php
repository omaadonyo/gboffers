<?php

namespace App\Livewire;

use App\Models\GangMember;
use App\Models\GbPass;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Analytics extends Component
{
    public function render(): View
    {
        $u = auth()->user();
        $paid = ['payment_confirmed', 'ready_for_redemption', 'redeemed', 'fulfilled'];
        $orders = Order::where('user_id', $u->id)->whereIn('status', $paid);

        $months = collect(range(5, 0))->map(function ($ago) use ($u, $paid) {
            $m = Carbon::now()->startOfMonth()->subMonths($ago);

            return [
                'label' => $m->format('M'),
                'value' => (int) Order::where('user_id', $u->id)->whereIn('status', $paid)
                    ->whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->sum('total'),
            ];
        });

        $joined = GangMember::where('user_id', $u->id)->count();
        $completed = GangMember::where('user_id', $u->id)->whereHas('gang', fn ($q) => $q->whereIn('status', ['unlocked', 'completed']))->count();
        $topCat = Order::where('user_id', $u->id)->with('offer.category')
            ->get()->groupBy(fn ($o) => $o->offer->category->name ?? 'Other')
            ->map->count()->sortDesc()->keys()->first();

        return view('livewire.analytics', [
            'spent' => (int) (clone $orders)->sum('total'),
            'saved' => (int) (clone $orders)->sum('discount'),
            'orderCount' => (clone $orders)->count(),
            'joined' => $joined,
            'completed' => $completed,
            'passes' => GbPass::where('user_id', $u->id)->where('status', 'redeemed')->count(),
            'months' => $months,
            'maxMonth' => max(1, $months->max('value')),
            'topCat' => $topCat ?? '—',
        ])->title('My stats — GBOffers');
    }
}
