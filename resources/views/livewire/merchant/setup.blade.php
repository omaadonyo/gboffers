<div class="mx-auto max-w-xl">
  <div class="text-center">
    <span class="mx-auto grid size-12 place-items-center rounded-2xl bg-brand-600 text-white dark:bg-brand-500"><flux:icon.store class="size-6" /></span>
    <h1 class="mt-3 text-xl font-bold tracking-tight text-zinc-900 dark:text-white">Open your shop</h1>
    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Tell us about your business. Verification usually takes under a day — you can look around meanwhile.</p>
  </div>

  <form wire:submit="save" class="mt-5 grid gap-3 rounded-xl bg-white p-5 shadow-sm sm:grid-cols-2 dark:bg-zinc-900">
    <div class="sm:col-span-2">
      <label class="mb-1 block text-xs font-semibold text-zinc-600 dark:text-zinc-300" for="s-biz">Business name</label>
      <input id="s-biz" wire:model="business_name" placeholder="e.g. Kampala Kitchen Ltd" class="h-11 w-full rounded-xl border-0 bg-zinc-100 px-4 text-sm dark:bg-zinc-800 dark:text-zinc-100">
      @error('business_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
      <label class="mb-1 block text-xs font-semibold text-zinc-600 dark:text-zinc-300" for="s-trade">Shop display name</label>
      <input id="s-trade" wire:model="trading_name" placeholder="e.g. Kampala Kitchen" class="h-11 w-full rounded-xl border-0 bg-zinc-100 px-4 text-sm dark:bg-zinc-800 dark:text-zinc-100">
      @error('trading_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
      <label class="mb-1 block text-xs font-semibold text-zinc-600 dark:text-zinc-300" for="s-cat">Category</label>
      <x-gb.select wire:model="category_id" id="s-cat" class="h-11 w-full rounded-xl border-0 bg-zinc-100 dark:bg-zinc-800" placeholder="Pick one…" :options="['' => 'Pick one…'] + $cats->pluck('name', 'id')->toArray()" />
    </div>
    <div>
      <label class="mb-1 block text-xs font-semibold text-zinc-600 dark:text-zinc-300" for="s-phone">Business phone</label>
      <input id="s-phone" wire:model="phone" placeholder="+256…" class="h-11 w-full rounded-xl border-0 bg-zinc-100 px-4 text-sm dark:bg-zinc-800 dark:text-zinc-100">
      @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
      <label class="mb-1 block text-xs font-semibold text-zinc-600 dark:text-zinc-300" for="s-loc">Location</label>
      <input id="s-loc" wire:model="location" placeholder="e.g. Kisementi, Kampala" class="h-11 w-full rounded-xl border-0 bg-zinc-100 px-4 text-sm dark:bg-zinc-800 dark:text-zinc-100">
      @error('location')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="sm:col-span-2">
      <p class="mb-1 text-xs font-semibold text-zinc-600 dark:text-zinc-300">Where should buyers pay you?</p>
      <div class="grid grid-cols-3 gap-2">
        <x-gb.select wire:model="network" aria-label="Network" class="h-11 rounded-xl border-0 bg-zinc-100 dark:bg-zinc-800" :options="['MTN' => 'MTN', 'Airtel' => 'Airtel', 'Bank' => 'Bank']" />
        <input wire:model="account_number" placeholder="Account / MoMo number" aria-label="Account number" class="h-11 rounded-xl border-0 bg-zinc-100 px-4 text-sm dark:bg-zinc-800 dark:text-zinc-100">
        <input wire:model="account_name" placeholder="Account name" aria-label="Account name" class="h-11 rounded-xl border-0 bg-zinc-100 px-4 text-sm dark:bg-zinc-800 dark:text-zinc-100">
      </div>
      @error('account_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
      @error('account_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <button class="h-11 rounded-full bg-brand-600 text-sm font-bold text-white hover:bg-brand-700 sm:col-span-2 dark:bg-brand-500 dark:hover:bg-brand-400">Submit for review</button>
  </form>
</div>
