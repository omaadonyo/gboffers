<?php

namespace App\Livewire\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Orders extends Component
{
    use WithPagination;

    public string $q = '';

    public string $status = '';

    public string $payment = '';

    public function updating(string $name): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $base = Order::with(['customer', 'merchant', 'offer'])
            ->when($this->q !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('id', $this->q)
                ->orWhere('payment_reference', 'like', '%'.$this->q.'%')
                ->orWhereHas('customer', fn ($c) => $c
                    ->where('name', 'like', '%'.$this->q.'%')
                    ->orWhere('email', 'like', '%'.$this->q.'%'))))
            ->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
            ->when($this->payment !== '', fn ($q) => $q->where('payment_status', $this->payment));

        $total = (clone $base)->sum('total');

        return view('livewire.admin.orders', [
            'orders' => $base->latest()->paginate(15),
            'total' => (int) $total,
            'statuses' => OrderStatus::cases(),
            'payments' => PaymentStatus::cases(),
        ])->title('Orders — Admin');
    }
}
