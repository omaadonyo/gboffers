<div class="rounded-xl bg-white p-4 shadow-sm sm:p-5 dark:bg-stone-900">
  <h2 class="flex items-center gap-1.5 text-sm font-bold text-stone-900 dark:text-stone-100"><flux:icon.arrow-down-tray class="size-4 text-brand-600 dark:text-white" /> Import from a link</h2>
  <p class="mt-0.5 text-xs text-stone-500 dark:text-stone-400">Paste a product page URL — we pull the title, text, price and photos. You confirm before anything is listed.</p>

  @if(session('ok'))<p class="mt-2 rounded-lg bg-emerald-50 px-3 py-2 text-xs font-medium text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">{{ session('ok') }}</p>@endif
  @if(session('warn'))<p class="mt-2 rounded-lg bg-amber-50 px-3 py-2 text-xs font-medium text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">{{ session('warn') }}</p>@endif

  <div class="mt-3 flex gap-2">
    <input wire:model="url" type="url" placeholder="https://example.com/product/…" aria-label="Product URL" class="h-10 flex-1 rounded-full border-0 bg-stone-100 px-4 text-sm dark:bg-stone-800 dark:text-stone-100">
    <button type="button" wire:click="fetch" wire:loading.attr="disabled" wire:target="fetch" class="inline-flex h-10 shrink-0 items-center gap-1.5 rounded-full bg-stone-900 px-4 text-sm font-semibold text-white hover:bg-stone-700 disabled:opacity-60 dark:bg-white dark:text-stone-900 dark:hover:bg-stone-200">
      <span wire:loading.remove wire:target="fetch">Fetch</span>
      <span wire:loading wire:target="fetch" class="inline-flex items-center gap-1.5"><flux:icon.loading class="size-4 animate-spin" /> Reading…</span>
    </button>
  </div>
  @error('url')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror

  @if($preview)
    <div class="mt-4 grid gap-3 border-t border-stone-100 pt-4 md:grid-cols-2 dark:border-stone-800">
      <div>
        <p class="text-xs font-bold uppercase tracking-wide text-stone-400">Photos — pick one</p>
        <div class="mt-2 grid grid-cols-3 gap-1.5">
          @forelse(array_slice($preview['images'] ?? [], 0, 6) as $img)
            <button type="button" wire:click="pickImage('{{ $img }}')" class="relative aspect-square overflow-hidden rounded-lg {{ $image === $img ? 'ring-2 ring-brand-600 dark:ring-brand-400' : 'opacity-80 hover:opacity-100' }}">
              <img src="{{ $img }}" alt="" loading="lazy" class="size-full object-cover" onerror="this.parentElement.style.display='none'">
              @if($image === $img)<span class="absolute right-1 top-1 grid size-5 place-items-center rounded-full bg-brand-600 text-white"><flux:icon.check class="size-3" /></span>@endif
            </button>
          @empty
            <p class="col-span-3 text-xs text-stone-500">No photos found on that page.</p>
          @endforelse
        </div>
      </div>
      <div class="space-y-2">
        @if(!$fixedMerchantId)
          <div>
            <label class="mb-1 block text-xs font-semibold text-stone-600 dark:text-stone-300">Merchant</label>
            <x-gb.select wire:model="merchant_id" class="h-10 w-full rounded-xl border-0 bg-stone-100 dark:bg-stone-800" placeholder="Choose merchant…" :options="['' => 'Choose merchant…'] + $merchants->mapWithKeys(fn($m) => [$m->id => ($m->trading_name ?? $m->business_name)])->toArray()" />
          </div>
        @endif
        <div>
          <label class="mb-1 block text-xs font-semibold text-stone-600 dark:text-stone-300">Title</label>
          <input wire:model="title" class="h-10 w-full rounded-xl border-0 bg-stone-100 px-3 text-sm dark:bg-stone-800 dark:text-stone-100">
          @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="mb-1 block text-xs font-semibold text-stone-600 dark:text-stone-300">Price (UGX)</label>
            <input wire:model="price" type="number" class="h-10 w-full rounded-xl border-0 bg-stone-100 px-3 text-sm dark:bg-stone-800 dark:text-stone-100">
            @error('price')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="mb-1 block text-xs font-semibold text-stone-600 dark:text-stone-300">Group target</label>
            <input wire:model="gang_target" type="number" class="h-10 w-full rounded-xl border-0 bg-stone-100 px-3 text-sm dark:bg-stone-800 dark:text-stone-100">
          </div>
        </div>
        <div>
          <label class="mb-1 block text-xs font-semibold text-stone-600 dark:text-stone-300">Description</label>
          <textarea wire:model="description" rows="3" class="w-full rounded-xl border-0 bg-stone-100 px-3 py-2 text-sm dark:bg-stone-800 dark:text-stone-100"></textarea>
        </div>
        <div>
          <label class="mb-1 block text-xs font-semibold text-stone-600 dark:text-stone-300">Category</label>
          <x-gb.select wire:model="category_id" class="h-10 w-full rounded-xl border-0 bg-stone-100 dark:bg-stone-800" placeholder="Pick later…" :options="['' => 'Pick later…'] + $cats->pluck('name', 'id')->toArray()" />
        </div>
        <button type="button" wire:click="create" wire:loading.attr="disabled" wire:target="create" class="inline-flex h-11 w-full items-center justify-center gap-1.5 rounded-full bg-brand-600 text-sm font-bold text-white hover:bg-brand-700 disabled:opacity-60 dark:bg-brand-500 dark:hover:bg-brand-400">
          <span wire:loading.remove wire:target="create">Create offer</span>
          <span wire:loading wire:target="create" class="inline-flex items-center gap-1.5"><flux:icon.loading class="size-4 animate-spin" /> Creating…</span>
        </button>
      </div>
    </div>
  @endif
</div>
