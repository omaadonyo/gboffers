<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Merchant;
use App\Models\Offer;
use App\Services\ImportService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class OfferImporter extends Component
{
    public ?int $fixedMerchantId = null;

    public string $url = '';

    public ?array $preview = null;

    public string $title = '';

    public string $description = '';

    public string $image = '';

    public int $price = 0;

    public int $gang_target = 5;

    public ?int $merchant_id = null;

    public ?int $category_id = null;

    public function mount(?int $merchantId = null): void
    {
        $this->fixedMerchantId = $merchantId;
        $this->merchant_id = $merchantId;
    }

    public function fetch(): void
    {
        $this->validate(['url' => 'required|url|max:500']);
        try {
            $data = app(ImportService::class)->fetch($this->url);
        } catch (\Throwable $e) {
            $this->addError('url', $e->getMessage());

            return;
        }
        $this->preview = $data;
        $this->title = $data['title'];
        $this->description = $data['description'];
        $this->image = $data['image'];
        $this->price = $data['price'];
        if ($this->price <= 0) {
            session()->flash('warn', 'No price detected on that page — enter it manually below.');
        }
    }

    public function pickImage(string $image): void
    {
        $this->image = $image;
    }

    public function create(): void
    {
        $this->validate([
            'title' => 'required|min:4|max:160',
            'price' => 'required|integer|min:100',
            'gang_target' => 'required|integer|min:2|max:500',
            'merchant_id' => 'required|exists:merchants,id',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|url|max:1000',
        ]);
        $merchant = Merchant::findOrFail($this->merchant_id);
        if ($this->fixedMerchantId && (int) $merchant->id !== (int) $this->fixedMerchantId) {
            abort(403);
        }
        $offer = Offer::create([
            'merchant_id' => $merchant->id,
            'category_id' => $this->category_id,
            'title' => $this->title,
            'slug' => Str::slug($this->title).'-'.strtolower(Str::random(5)),
            'description' => $this->description ?: null,
            'normal_price' => $this->price,
            'image_path' => $this->image ?: null,
            'gang_target' => $this->gang_target,
            'min_buyers' => $this->gang_target,
            'status' => 'active',
            'ends_at' => now()->addDays(14),
        ]);
        $offer->priceTiers()->create(['min_qty' => 1, 'price' => $this->price, 'sort' => 0]);
        $this->reset(['url', 'preview', 'title', 'description', 'image', 'price', 'gang_target']);
        $this->merchant_id = $this->fixedMerchantId;
        session()->flash('ok', 'Offer imported. Add gang tiers and group discounts from the list below.');
    }

    public function render(): View
    {
        return view('livewire.offer-importer', [
            'merchants' => $this->fixedMerchantId
                ? Merchant::whereKey($this->fixedMerchantId)->get()
                : Merchant::orderBy('business_name')->get(['id', 'business_name', 'trading_name']),
            'cats' => Category::orderBy('name')->get(),
        ]);
    }
}
