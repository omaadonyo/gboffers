<?php

namespace App\Livewire;

use App\Models\Merchant;
use App\Models\WantedRequest;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.storefront')]
class WantedIndex extends Component
{
    use WithPagination;

    public string $title = '';

    public int $budget = 0;

    public string $guest_name = '';

    public string $guest_contact = '';

    public bool $showPost = false;

    public string $q = '';

    public ?int $responding = null;

    public string $r_message = '';

    public int $r_price = 0;

    public string $r_name = '';

    public string $r_contact = '';

    public function openPost(): void
    {
        $this->showPost = true;
    }

    public function closePost(): void
    {
        $this->showPost = false;
        $this->reset(['title', 'budget', 'guest_name', 'guest_contact']);
        $this->resetValidation();
    }

    public function updating($property): void
    {
        if (in_array($property, ['q'], true)) {
            $this->resetPage();
        }
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|min:3',
            'budget' => 'required|integer|min:1000',
            'guest_name' => 'nullable|max:64',
            'guest_contact' => 'nullable|max:64',
        ]);
        if (! auth()->check()) {
            $key = 'wanted-guest:'.request()->ip();
            if (RateLimiter::tooManyAttempts($key, 3)) {
                $this->addError('title', 'Too many requests. Try again in an hour or log in to post freely.');

                return;
            }
            RateLimiter::hit($key, 3600);
            if (trim($this->guest_name) === '') {
                $this->addError('guest_name', 'Tell suppliers your name.');

                return;
            }
        }
        WantedRequest::create([
            'user_id' => auth()->id(),
            'guest_name' => auth()->check() ? null : trim($this->guest_name),
            'guest_contact' => auth()->check() ? null : (trim($this->guest_contact) ?: null),
            'title' => $this->title, 'budget' => $this->budget, 'status' => 'open', 'expires_at' => now()->addDays(14),
        ]);
        $this->reset(['title', 'budget', 'guest_name', 'guest_contact']);
        $this->showPost = false;
        session()->flash('ok', auth()->check() ? 'Request posted.' : 'Request posted as guest. Log in to manage it.');
    }

    public function respond(int $id): void
    {
        $this->responding = $this->responding === $id ? null : $id;
        $this->reset(['r_message', 'r_price', 'r_name', 'r_contact']);
    }

    public function sendResponse(): void
    {
        $request = WantedRequest::findOrFail($this->responding);
        $this->validate([
            'r_message' => 'required|min:3|max:500',
            'r_price' => 'required|integer|min:100',
            'r_name' => 'nullable|max:64',
            'r_contact' => 'nullable|max:64',
        ]);
        if (! auth()->check()) {
            $key = 'wanted-resp:'.request()->ip();
            if (RateLimiter::tooManyAttempts($key, 3)) {
                $this->addError('r_message', 'Too many responses. Try again in an hour or log in.');

                return;
            }
            RateLimiter::hit($key, 3600);
            if (trim($this->r_name) === '') {
                $this->addError('r_name', 'Tell the buyer your name.');

                return;
            }
        }
        $merchantId = auth()->check()
            ? Merchant::where('user_id', auth()->id())->value('id')
            : null;
        $request->responses()->create([
            'merchant_id' => $merchantId,
            'user_id' => auth()->id(),
            'guest_name' => auth()->check() ? null : trim($this->r_name),
            'guest_contact' => auth()->check() ? null : (trim($this->r_contact) ?: null),
            'price' => $this->r_price,
            'message' => $this->r_message,
        ]);
        $request->increment('responses_count');
        $this->responding = null;
        $this->reset(['r_message', 'r_price', 'r_name', 'r_contact']);
        session()->flash('ok', 'Response sent. The buyer can reach you.');
    }

    public function render()
    {
        $rows = WantedRequest::with(['user', 'responses.user', 'responses.merchant'])
            ->where('status', 'open')
            ->when($this->q !== '', fn ($query) => $query->where('title', 'like', '%'.$this->q.'%'))
            ->latest()->paginate(12);

        return view('livewire.wanted-index', compact('rows'))->title('Wanted — GBOffers');
    }
}
