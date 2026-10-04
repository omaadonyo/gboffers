<?php

namespace App\Livewire\Admin;

use App\Enums\MerchantStatus;
use App\Models\Merchant;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Merchants extends Component
{
    use WithPagination;

    public string $q = '';

    public string $status = '';

    public function updating(string $name): void
    {
        $this->resetPage();
    }

    public function approve(int $id): void
    {
        $merchant = Merchant::findOrFail($id);
        $merchant->update([
            'verification_status' => MerchantStatus::APPROVED->value,
            'verified_at' => now(),
            'is_active' => true,
        ]);
        event(new \App\Events\MerchantVerified($merchant->fresh()));
    }

    public function reject(int $id): void
    {
        Merchant::findOrFail($id)->update([
            'verification_status' => MerchantStatus::REJECTED->value,
            'is_active' => false,
        ]);
    }

    public function suspend(int $id): void
    {
        Merchant::findOrFail($id)->update([
            'verification_status' => MerchantStatus::SUSPENDED->value,
            'is_active' => false,
        ]);
    }

    public function render(): View
    {
        $merchants = Merchant::with('owner')
            ->withCount(['offers', 'orders'])
            ->when($this->q !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('business_name', 'like', '%'.$this->q.'%')
                ->orWhere('trading_name', 'like', '%'.$this->q.'%')))
            ->when($this->status !== '', fn ($q) => $q->where('verification_status', $this->status))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.merchants', [
            'merchants' => $merchants,
            'statuses' => MerchantStatus::cases(),
        ])->title('Merchants — Admin');
    }
}
