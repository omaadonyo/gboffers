<?php
namespace App\Livewire\Merchant;
use App\Models\Merchant;
use Livewire\Component;
use Livewire\WithPagination;
class Orders extends Component {
    use ResolvesMerchant, WithPagination;
    public ?Merchant $merchant = null;
    public function mount(): void {
        $uid = auth()->id();
        $this->merchant = $this->resolveMerchant();
        if (! $this->merchant) { $this->redirect(route('merchant.setup'), navigate: true); }
    }
    public function fulfill(int $orderId): void {
        $o = \App\Models\Order::where('merchant_id',$this->merchant->id)->findOrFail($orderId);
        if ($o->status === 'redeemed') { $o->update(['status'=>'fulfilled','fulfillment_status'=>'fulfilled']); event(new \App\Events\OrderFulfilled($o)); }
    }
    public function render() {
        $rows = \App\Models\Order::with(['customer','offer','gbPass'])->where('merchant_id',$this->merchant->id)->latest()->paginate(15);
        return view('livewire.merchant.orders', compact('rows'));
    }
}
