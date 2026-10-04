<?php

namespace App\Livewire;

use App\Models\Gang;
use App\Models\GangMember;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.storefront')]
class MyGangs extends Component
{
    use WithPagination;

    public string $tab = 'discover';

    public string $q = '';

    public function updating(string $name): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $tab = in_array($this->tab, ['discover', 'mine', 'done'], true) ? $this->tab : 'discover';
        if (! auth()->check() && $tab !== 'discover') {
            $tab = 'discover';
        }

        if ($tab === 'mine' || $tab === 'done') {
            $q = GangMember::with(['gang.offer', 'gang.members.user.profile', 'offer', 'offer.merchant', 'offer.category', 'order'])->where('user_id', auth()->id());
            $statuses = $tab === 'done'
                ? ['fulfilled', 'redeemed']
                : ['reserved', 'payment_pending', 'payment_confirmed', 'ready'];
            $members = (clone $q)->whereIn('status', $statuses)->latest()->paginate(10);

            return view('livewire.my-gangs', compact('members') + ['tab' => $tab, 'offers' => collect()])->title('My groups — GBOffers');
        }

        $gangs = Gang::with(['offer.merchant', 'offer.category'])
            ->where('status', 'forming')
            ->when($this->q !== '', fn ($query) => $query->whereHas('offer', fn ($o) => $o->where('title', 'like', '%'.$this->q.'%')))
            ->orderByDesc('confirmed_count')
            ->paginate(12);

        return view('livewire.my-gangs', ['members' => collect(), 'tab' => 'discover', 'gangs' => $gangs])->title('Groups — GBOffers');
    }
}
