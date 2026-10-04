<?php

namespace App\Livewire\Admin;

use App\Models\FeaturedListing;
use App\Notifications\PlatformNotification;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Featured extends Component
{
    use WithPagination;

    public string $status = '';

    public function updating(string $name): void
    {
        $this->resetPage();
    }

    public function approve(int $id): void
    {
        $listing = FeaturedListing::with('offer')->findOrFail($id);
        $listing->update([
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addDays($listing->days),
            'approved_by' => auth()->id(),
        ]);
        $listing->offer?->update(['featured' => true]);
        $listing->offer?->merchant?->owner?->notify(new PlatformNotification(
            'Offer featured',
            $listing->offer->title.' is now featured for '.$listing->days.' day(s).',
            route('offers.show', $listing->offer, absolute: false),
            'sparkles',
        ));
    }

    public function reject(int $id): void
    {
        FeaturedListing::findOrFail($id)->update(['status' => 'rejected']);
    }

    public function cancel(int $id): void
    {
        $listing = FeaturedListing::with('offer')->findOrFail($id);
        $listing->update(['status' => 'cancelled']);
        $listing->offer?->update(['featured' => false]);
    }

    public function render(): View
    {
        return view('livewire.admin.featured', [
            'rows' => FeaturedListing::with(['offer.merchant'])->when($this->status !== '', fn ($q) => $q->where('status', $this->status))->latest()->paginate(15),
            'pendingCount' => FeaturedListing::where('status', 'pending')->count(),
            'revenue' => (int) FeaturedListing::whereIn('status', ['active', 'expired'])->sum('amount'),
        ])->title('Featured — Admin');
    }
}
