<?php

namespace App\Livewire\Merchant;

use App\Enums\CommissionStatus;
use App\Models\Commission;
use App\Models\Merchant;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Commissions extends Component
{
    use ResolvesMerchant, WithPagination;

    public ?Merchant $merchant = null;

    public string $status = '';

    public function mount(): void
    {
        $this->merchant = $this->resolveMerchant();
        if (! $this->merchant) { $this->redirect(route('merchant.setup'), navigate: true); }
    }

    public function updating(string $name): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $base = Commission::where('merchant_id', $this->merchant->id)
            ->when($this->status !== '', fn ($q) => $q->where('status', $this->status));

        $totals = [];
        foreach (CommissionStatus::cases() as $s) {
            $totals[$s->value] = (int) (clone $base)->where('status', $s->value)->sum('amount');
        }

        return view('livewire.merchant.commissions', [
            'rows' => $base->with('order')->latest()->paginate(15),
            'totals' => $totals,
            'statuses' => CommissionStatus::cases(),
        ])->title('Commissions — Merchant');
    }
}
