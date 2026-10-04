<?php
namespace App\Livewire;
use App\Models\Offer;
use App\Services\GangService;
use App\Services\PaymentService;
use App\Services\PricingService;
use App\Services\ShareService;
use Livewire\Attributes\Layout;
use Livewire\Component;
#[Layout('layouts.storefront')]
class OfferShow extends Component {
    public Offer $offer;
    public bool $showJoin = false;
    public string $method = 'merchant_direct';
    public bool $ack = false;
    public ?string $notice = null;
    public function mount(Offer $offer): void { $this->offer = $offer->load(['merchant','category','priceTiers','audiences','gangs.members.user']); $offer->increment('views');
        $seen = 'viewed:'.$offer->id.':'.(auth()->id() ?? request()->ip());
        if (\Illuminate\Support\Facades\Cache::add($seen, true, 3600)) {
            \App\Models\OfferView::create(['offer_id' => $offer->id, 'user_id' => auth()->id(), 'ip' => request()->ip()]);
        }
    }
    public function join(): void {
        if (!auth()->check()) { $this->redirect(route('login')); return; }
        try {
            app(GangService::class)->joinGang(auth()->user(), $this->offer);
            $this->showJoin = true; $this->notice = null;
        } catch (\Exception $e) { $this->notice = $e->getMessage(); }
    }
    public function interested(): void {
        if (!auth()->check()) { $this->redirect(route('login')); return; }
        app(GangService::class)->markInterested(auth()->user(), $this->offer);
        $this->notice = 'Saved as interested. This does not count toward the group target — only confirmed buyers count.';
    }
    public function confirmJoin(): void {
        $gang = app(GangService::class)->openGang($this->offer);
        $price = app(PricingService::class)->priceFor($this->offer, $gang->confirmed_count);
        $allowed = config('gboffers.payments.methods', ['merchant_direct']);
        $method = in_array($this->method, $allowed, true) ? $this->method : 'merchant_direct';
        $order = app(PaymentService::class)->createOrder(auth()->user(), $this->offer->merchant, $this->offer, $gang, $price, 1, $method);
        \App\Models\GangMember::where('gang_id',$gang->id)->where('user_id',auth()->id())->update(['order_id'=>$order->id,'status'=>'payment_pending']);
        $this->redirect('/checkout/'.$order->id);
    }
    public function render() {
        $gang = $this->offer->gangs->firstWhere('status','forming') ?? $this->offer->gangs->first();
        $price = app(PricingService::class)->priceFor($this->offer, $gang->confirmed_count ?? 0);
        $next = app(PricingService::class)->nextTier($this->offer, $gang->confirmed_count ?? 0);
        $groups = app(PricingService::class)->groupOptions($this->offer);
        $membership = auth()->check() && $gang
            ? \App\Models\GangMember::where('gang_id',$gang->id)->where('user_id',auth()->id())->first()
            : null;
        $confirmedBuyers = $gang
            ? $gang->confirmedMembers()->with('user.profile')->get()->map(fn($m) => ['name' => $m->user?->displayName() ?? 'Member', 'contact' => $m->user?->profile?->phone ?? null])
            : collect();
        $share = $gang ? app(ShareService::class)->whatsappUrl($gang) : '#';
        return view('livewire.offer-show', compact('gang','price','next','groups','membership','confirmedBuyers','share'))->title($this->offer->title.' — GBOffers');
    }
}
