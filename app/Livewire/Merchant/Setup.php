<?php

namespace App\Livewire\Merchant;

use App\Models\Category;
use App\Models\Merchant;
use App\Models\User;
use App\Notifications\PlatformNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class Setup extends Component
{
    public string $business_name = '';

    public string $trading_name = '';

    public string $phone = '';

    public string $location = '';

    public ?int $category_id = null;

    public string $network = 'MTN';

    public string $account_number = '';

    public string $account_name = '';

    public function mount(): void
    {
        $uid = auth()->id();
        $has = Merchant::where('user_id', $uid)->exists()
            || Merchant::whereHas('staff', fn ($q) => $q->where('user_id', $uid))->exists();
        if ($has) {
            $this->redirect(route('merchant.dashboard'), navigate: true);
        }
    }

    public function save(): void
    {
        $this->validate([
            'business_name' => 'required|min:3|max:120',
            'trading_name' => 'required|min:2|max:120',
            'phone' => 'required|min:7|max:32',
            'location' => 'required|min:3|max:160',
            'category_id' => 'nullable|exists:categories,id',
            'network' => 'required|in:MTN,Airtel,Bank',
            'account_number' => 'required|min:4|max:64',
            'account_name' => 'required|min:2|max:120',
        ]);
        $slug = Str::slug($this->trading_name);
        if (Merchant::where('slug', $slug)->exists()) {
            $slug .= '-'.strtolower(Str::random(5));
        }
        $merchant = Merchant::create([
            'user_id' => auth()->id(),
            'category_id' => $this->category_id,
            'business_name' => $this->business_name,
            'trading_name' => $this->trading_name,
            'slug' => $slug,
            'phone' => $this->phone,
            'location' => $this->location,
            'payment_details' => ['network' => $this->network, 'account_number' => $this->account_number, 'account_name' => $this->account_name],
            'verification_status' => 'pending',
            'is_active' => false,
        ]);
        User::where('role', 'admin')->get()->each(fn ($a) => $a->notify(
            new PlatformNotification('New merchant', $merchant->business_name.' is awaiting approval.', '/admin/merchants', 'store')
        ));
        session()->flash('ok', 'Shop submitted! We will review it shortly — you can explore the dashboard meanwhile.');
        $this->redirect(route('merchant.dashboard'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.merchant.setup', [
            'cats' => Category::orderBy('name')->get(),
        ])->title('Open your shop — GBOffers');
    }
}
