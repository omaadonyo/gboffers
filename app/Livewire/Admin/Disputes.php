<?php

namespace App\Livewire\Admin;

use App\Enums\DisputeStatus;
use App\Models\Dispute;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Disputes extends Component
{
    use WithPagination;

    public string $status = '';

    public ?int $resolving = null;

    public string $resolution = '';

    public string $outcome = 'resolved_merchant';

    public function updating(string $name): void
    {
        $this->resetPage();
    }

    public function start(int $id): void
    {
        Dispute::findOrFail($id)->update(['status' => DisputeStatus::UNDER_REVIEW->value]);
    }

    public function openResolve(int $id): void
    {
        $this->resolving = $id;
        $this->resolution = '';
        $this->outcome = DisputeStatus::RESOLVED_MERCHANT->value;
    }

    public function resolve(): void
    {
        $this->validate([
            'resolution' => 'required|min:10',
            'outcome' => 'required|in:resolved_customer,resolved_merchant,closed',
        ]);
        $dispute = Dispute::findOrFail($this->resolving);
        $dispute->update([
            'status' => $this->outcome,
            'resolution' => $this->resolution,
            'resolved_at' => now(),
            'resolved_by' => auth()->id(),
        ]);
        $dispute->reporter?->notify(new \App\Notifications\PlatformNotification(
            'Dispute resolved',
            'Your dispute on order #'.$dispute->order_id.' was resolved: '.str_replace('_', ' ', $this->outcome).'.',
            '/orders',
            'shield-exclamation',
        ));
        $dispute->merchant?->owner?->notify(new \App\Notifications\PlatformNotification(
            'Dispute resolved',
            'Dispute on order #'.$dispute->order_id.' was resolved: '.str_replace('_', ' ', $this->outcome).'.',
            '/merchant/orders',
            'shield-exclamation',
        ));
        $this->resolving = null;
        session()->flash('ok', 'Dispute resolved.');
    }

    public function render(): View
    {
        $disputes = Dispute::with(['order.offer', 'order.customer'])
            ->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.disputes', [
            'disputes' => $disputes,
            'statuses' => DisputeStatus::cases(),
        ])->title('Disputes — Admin');
    }
}
