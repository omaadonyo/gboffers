<?php

namespace App\Livewire\Merchant;

use App\Models\Category;
use App\Models\Merchant;
use App\Models\Offer;
use App\Models\User;
use App\Notifications\PlatformNotification;
use App\Support\FeaturedPricing;
use Illuminate\Support\Str;
use Livewire\Component;

class Offers extends Component
{
    use ResolvesMerchant;

    public ?Merchant $merchant = null;

    public string $title = '';

    public int $normal_price = 0;

    public int $gang_target = 5;

    public ?int $category_id = null;

    public string $tiers = '5:1650000,10:1550000';

    public string $audiences = '';

    public ?int $featuring = null;

    public int $featureDays = 7;

    public function mount(): void
    {
        $this->merchant = $this->resolveMerchant();
        if (! $this->merchant) { $this->redirect(route('merchant.setup'), navigate: true); }
    }

    protected function rules(): array
    {
        return ['title' => 'required|min:4', 'normal_price' => 'required|integer|min:100', 'gang_target' => 'required|integer|min:2', 'category_id' => 'nullable|exists:categories,id', 'audiences' => 'nullable|string'];
    }

    public function save(): void
    {
        $this->validate();
        $offer = Offer::create([
            'merchant_id' => $this->merchant->id, 'category_id' => $this->category_id, 'title' => $this->title,
            'slug' => Str::slug($this->title).'-'.strtolower(Str::random(5)), 'normal_price' => $this->normal_price,
            'gang_target' => $this->gang_target, 'min_buyers' => $this->gang_target, 'status' => 'active', 'ends_at' => now()->addDays(14),
        ]);
        foreach (explode(',', $this->tiers) as $i => $pair) {
            if (! str_contains($pair, ':')) {
                continue;
            }
            [$min, $price] = explode(':', trim($pair));
            $offer->priceTiers()->create(['min_qty' => (int) $min, 'price' => (int) $price, 'sort' => $i]);
        }
        foreach (preg_split('/\r?\n/', trim($this->audiences)) as $i => $line) {
            $parts = array_map('trim', explode(':', $line));
            if (count($parts) !== 3 || $parts[0] === '' || (int) $parts[1] < 2 || (int) $parts[2] < 100) {
                continue;
            }
            $offer->audiences()->create(['label' => mb_substr($parts[0], 0, 64), 'min_buyers' => (int) $parts[1], 'price' => (int) $parts[2], 'sort' => $i]);
        }
        $this->reset(['title', 'normal_price', 'audiences']);
        session()->flash('ok', 'Offer created.');
    }

    public function feature(int $id): void
    {
        Offer::where('merchant_id', $this->merchant->id)->findOrFail($id);
        $this->featuring = $id;
        $this->featureDays = 7;
    }

    public function featurePrice(): int
    {
        return FeaturedPricing::priceForDays($this->featureDays);
    }

    public function submitFeature(): void
    {
        $offer = Offer::where('merchant_id', $this->merchant->id)->findOrFail($this->featuring);
        $this->validate(['featureDays' => 'required|integer|min:1|max:30']);
        if ($offer->featuredListings()->whereIn('status', ['pending', 'active'])->exists()) {
            session()->flash('err', 'This offer already has a pending or active feature.');
            $this->featuring = null;

            return;
        }
        $offer->featuredListings()->create([
            'days' => $this->featureDays,
            'amount' => FeaturedPricing::priceForDays($this->featureDays),
            'status' => 'pending',
        ]);
        User::where('role', 'admin')->get()->each(fn ($a) => $a->notify(
            new PlatformNotification('Feature request', ($offer->title).' — '.$this->featureDays.' day(s).', '/admin/featured', 'sparkles')
        ));
        $this->featuring = null;
        session()->flash('ok', 'Feature request sent. Admin will activate it after payment.');
    }

    public function toggleStatus(int $id): void
    {
        $offer = Offer::where('merchant_id', $this->merchant->id)->findOrFail($id);
        $offer->update(['status' => $offer->status === 'active' ? 'paused' : 'active']);
    }

    public function destroy(int $id): void
    {
        $offer = Offer::where('merchant_id', $this->merchant->id)->withCount(['gangs', 'orders'])->findOrFail($id);
        if ($offer->gangs_count > 0 || $offer->orders_count > 0) {
            session()->flash('err', 'Cannot delete an offer with groups or orders. Pause it instead.');

            return;
        }
        $offer->delete();
        session()->flash('ok', 'Offer deleted.');
    }

    public function render()
    {
        $offers = Offer::with(['audiences', 'featuredListings'])->where('merchant_id', $this->merchant->id)->latest()->paginate(10);
        $cats = Category::orderBy('name')->get();

        return view('livewire.merchant.offers', compact('offers', 'cats'));
    }
}
