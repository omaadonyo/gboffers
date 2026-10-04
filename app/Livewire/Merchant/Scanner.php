<?php
namespace App\Livewire\Merchant;
use App\Models\Merchant;
use App\Services\RedemptionService;
use Livewire\Component;
class Scanner extends Component {
    use ResolvesMerchant;
    public ?Merchant $merchant = null;
    public string $token = '';
    public string $pin = '';
    public ?array $result = null;
    public ?string $error = null;
    public function mount(): void {
        $this->merchant = $this->resolveMerchant();
        if (! $this->merchant) { $this->redirect(route('merchant.setup'), navigate: true); }
    }
    public function lookup(): void {
        $this->error = null; $this->result = null;
        $this->token = trim(strtoupper($this->token));
        $r = app(RedemptionService::class)->lookup($this->token, $this->merchant);
        if (!$r['ok']) { $this->error = $r['error']; return; }
        $p = $r['pass'];
        $this->result = ['token'=>$p->token,'title'=>$p->offer->title,'amount'=>$p->amount,'customer'=>$p->customer->displayName(),'payment'=>$p->order->payment_status,'status'=>$p->status];
    }
    public function redeem(): void {
        $this->error = null;
        try {
            $pass = \App\Models\GbPass::where('token', trim(strtoupper($this->token)))->firstOrFail();
            $done = app(RedemptionService::class)->redeem($pass, $this->merchant, auth()->user(), trim($this->pin), request()->ip());
            $this->result = ['token'=>$done->token,'title'=>$done->offer->title,'amount'=>$done->amount,'customer'=>$done->customer->displayName(),'payment'=>'confirmed','status'=>'redeemed','just_redeemed'=>true];
            $this->pin = '';
        } catch (\Exception $e) { $this->error = $e->getMessage() ?: 'Redemption failed.'; }
    }
    public function render() { return view('livewire.merchant.scanner'); }
}
