<?php

namespace App\Livewire\Admin;

use App\Enums\GangStatus;
use App\Models\Gang;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Gangs extends Component
{
    use WithPagination;

    public string $q = '';

    public string $status = '';

    public function updating(string $name): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $gangs = Gang::with(['offer.merchant'])
            ->withCount('members')
            ->when($this->q !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('code', 'like', '%'.$this->q.'%')
                ->orWhereHas('offer', fn ($o) => $o->where('title', 'like', '%'.$this->q.'%'))))
            ->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.gangs', [
            'gangs' => $gangs,
            'statuses' => GangStatus::cases(),
        ])->title('Groups — Admin');
    }
}
