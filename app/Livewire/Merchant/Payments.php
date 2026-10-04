<?php
namespace App\Livewire\Merchant;
use App\Models\Merchant;
use App\Services\PaymentService;
use Livewire\Component;
use Livewire\WithPagination;
class Payments extends Component {
    use ResolvesMerchant, WithPagination;
    public ?Merchant $merchant = null;
    public bool $confirmAck = false;
    public function mount(): void {
        $uid = auth()->id();
        $this->merchant = $this->resolveMerchant();
        if (! $this->merchant) { $this->redirect(route('merchant.setup'), navigate: true); }
    }
    public function confirm(int $orderId): void {
        $order = \App\Models\Order::where('merchant_id',$this->merchant->id)->findOrFail($orderId);
        app(PaymentService::class)->confirmPayment($order, auth()->user(), $this->merchant, $this->confirmAck);
        $this->confirmAck = false;
    }
    public function render() {
        $rows = \App\Models\Order::with(['customer','offer','payment'])->where('merchant_id',$this->merchant->id)->whereIn('payment_status',['pending','reported'])->latest()->paginate(15);
        return view('livewire.merchant.payments', compact('rows'));
    }
}
