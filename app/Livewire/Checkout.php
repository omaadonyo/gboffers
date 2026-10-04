<?php
namespace App\Livewire;
use App\Models\Order;
use App\Services\PaymentService;
use Livewire\Attributes\Layout;
use Livewire\Component;
#[Layout('layouts.storefront')]
class Checkout extends Component {
    public Order $order;
    public bool $ack = false;
    public function mount(Order $order): void {
        abort_unless((int)$order->user_id === (int)auth()->id(), 403);
        $this->order = $order->load(['merchant','offer']);
    }
    public function reportPaid(): void {
        abort_unless($this->ack, 422, 'Please acknowledge the direct-payment terms first.');
        app(PaymentService::class)->reportPaid($this->order);
        $this->order->refresh();
    }
    public function switchMethod(string $method): void {
        abort_if($this->order->payment_status !== 'pending', 422, 'Payment method can no longer be changed.');
        $allowed = config('gboffers.payments.methods', ['merchant_direct']);
        abort_unless(in_array($method, $allowed, true), 422, 'Unsupported payment method.');
        \Illuminate\Support\Facades\DB::transaction(function () use ($method) {
            $this->order->update(['payment_method' => $method, 'payment_provider' => $method]);
            $this->order->payment()->update(['method' => $method, 'provider' => $method]);
        });
        $this->order->refresh();
    }
    public function render() {
        $instructions = app(PaymentService::class)->providerFor($this->order->payment_provider)->instructions($this->order);
        return view('livewire.checkout', compact('instructions'))->title('Checkout — GBOffers');
    }
}
