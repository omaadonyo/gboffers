<?php

namespace App\Livewire\Admin;

use App\Enums\OfferStatus;
use App\Models\Offer;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Offers extends Component
{
    use WithPagination;

    public string $q = '';

    public string $status = '';

    public function updating(string $name): void
    {
        $this->resetPage();
    }

    public function activate(int $id): void
    {
        $offer = Offer::findOrFail($id);
        $offer->update(['status' => OfferStatus::ACTIVE->value]);
    }

    public function pause(int $id): void
    {
        $offer = Offer::findOrFail($id);
        $offer->update(['status' => OfferStatus::PAUSED->value]);
    }

    public function toggleFeatured(int $id): void
    {
        $offer = Offer::findOrFail($id);
        $offer->update(['featured' => ! $offer->featured]);
    }

    public function destroy(int $id): void
    {
        $offer = Offer::withCount(['gangs', 'orders'])->findOrFail($id);
        if ($offer->gangs_count > 0 || $offer->orders_count > 0) {
            session()->flash('err', 'Cannot delete an offer that already has groups or orders. Pause it instead.');

            return;
        }
        $offer->delete();
        session()->flash('ok', 'Offer deleted.');
    }

    public function render(): View
    {
        $offers = Offer::with(['merchant', 'category'])
            ->withCount(['gangs', 'orders'])
            ->when($this->q !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('title', 'like', '%'.$this->q.'%')
                ->orWhereHas('merchant', fn ($m) => $m
                    ->where('business_name', 'like', '%'.$this->q.'%')
                    ->orWhere('trading_name', 'like', '%'.$this->q.'%'))))
            ->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.offers', [
            'offers' => $offers,
            'statuses' => OfferStatus::cases(),
        ])->title('Offers — Admin');
    }
}
