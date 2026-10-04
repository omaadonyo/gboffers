<?php

namespace App\Livewire\Merchant;

use App\Models\Merchant;

trait ResolvesMerchant
{
    public function resolveMerchant(): ?Merchant
    {
        if (auth()->user()?->isAdmin()) {
            $slug = request()->query('merchant', session('admin_merchant'));
            $merchant = $slug
                ? Merchant::where('slug', $slug)->first()
                : null;
            $merchant ??= Merchant::orderBy('id')->firstOrFail();
            session(['admin_merchant' => $merchant->slug]);

            return $merchant;
        }
        $uid = auth()->id();

        return Merchant::where('user_id', $uid)->first()
            ?? Merchant::whereHas('staff', fn ($q) => $q->where('user_id', $uid))->first();
    }

    public function merchantChoices(): array
    {
        if (! auth()->user()?->isAdmin()) {
            return [];
        }

        return Merchant::orderBy('business_name')->get()->map(
            fn ($m) => ['slug' => $m->slug, 'name' => $m->trading_name ?? $m->business_name]
        )->all();
    }
}
