<?php

namespace App\Livewire;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class MyOrders extends Component
{
    use WithPagination;

    public string $filter = 'all';

    public function updating(string $name): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $orders = Order::with(['offer.merchant', 'offer.category', 'payment'])
            ->where('user_id', auth()->id())
            ->when($this->filter === 'active', fn ($q) => $q->whereIn('status', ['reserved', 'payment_pending']))
            ->when($this->filter === 'paid', fn ($q) => $q->whereIn('status', ['payment_confirmed', 'ready_for_redemption', 'redeemed']))
            ->when($this->filter === 'done', fn ($q) => $q->whereIn('status', ['fulfilled', 'refunded', 'cancelled', 'expired']))
            ->latest()
            ->paginate(10);

        return view('livewire.my-orders', compact('orders'))->title('My orders — GBOffers');
    }
}
