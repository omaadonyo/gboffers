<div>
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">Offers</h1>
      <p class="text-sm text-zinc-500 dark:text-zinc-400">Your products — create new ones, pause slow movers, track fills.</p>
    </div>
  </div>

  @if(session('ok'))<p class="mt-3 rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-2 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">{{ session('ok') }}</p>@endif
  @if(session('err'))<p class="mt-3 rounded-lg border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-800 dark:border-red-800 dark:bg-red-950/60 dark:text-red-300">{{ session('err') }}</p>@endif

  <div class="mt-4">
    @livewire('offer-importer', ['merchantId' => $this->merchant->id])
  </div>

  <form wire:submit="save" class="mt-4 grid gap-2 rounded-lg border border-zinc-200 bg-white p-4 text-sm md:grid-cols-2 dark:border-zinc-700 dark:bg-zinc-900">
    <h2 class="font-bold text-zinc-900 md:col-span-2 dark:text-zinc-100">New offer</h2>
    <input wire:model="title" placeholder="Offer title" aria-label="Offer title" class="h-10 rounded-lg border border-zinc-300 px-3 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
    <input wire:model="normal_price" type="number" placeholder="Normal price (UGX)" aria-label="Normal price" class="h-10 rounded-lg border border-zinc-300 px-3 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
    <input wire:model="gang_target" type="number" placeholder="Group target (buyers)" aria-label="Group target" class="h-10 rounded-lg border border-zinc-300 px-3 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
    <select wire:model="category_id" aria-label="Category" class="h-10 rounded-lg border border-zinc-300 px-2 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"><option value="">Category…</option>@foreach($cats as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select>
    <input wire:model="tiers" placeholder="Price tiers — e.g. 5:1650000,10:1550000" aria-label="Price tiers" class="h-10 rounded-lg border border-zinc-300 px-3 md:col-span-2 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
    <div class="md:col-span-2">
      <textarea wire:model="audiences" rows="2" placeholder="Group discounts — one per line, e.g.&#10;Students:5:150000&#10;Staff SACCO:10:140000" aria-label="Group discounts" class="w-full rounded-lg border border-zinc-300 px-3 py-2 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"></textarea>
      <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Different prices for different groups of buyers. Format per line: <span class="font-mono">Group name:min buyers:price</span>. Leave empty for open groups only.</p>
    </div>
    @error('title')<p class="text-xs text-red-600 md:col-span-2">{{ $message }}</p>@enderror
    <button class="h-10 rounded-lg bg-brand-600 font-semibold text-white hover:bg-brand-700 md:col-span-2 dark:bg-brand-500 dark:hover:bg-brand-400">Create offer</button>
  </form>

  <div class="mt-3 space-y-2">
    @foreach($offers as $o)
      <div class="flex flex-wrap items-center gap-3 rounded-lg border border-zinc-200 bg-white p-3.5 dark:border-zinc-700 dark:bg-zinc-900">
        <div class="min-w-0 flex-1">
          <a href="{{ route('offers.show', $o->slug) }}" class="block truncate text-sm font-semibold text-zinc-900 hover:underline dark:text-zinc-100">{{ $o->title }}</a>
          <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">{{ $o->confirmed_count }}/{{ $o->gang_target }} confirmed · {{ App\Support\Money::formatUgx($o->normal_price) }} · <span class="font-bold">{{ $o->status }}</span></p>
          @if($o->audiences->isNotEmpty())
            <p class="mt-1 flex flex-wrap gap-1">
              @foreach($o->audiences as $a)<span class="rounded-full bg-brand-50 px-2 py-0.5 text-[11px] font-semibold text-brand-700 dark:bg-brand-950 dark:text-brand-300">{{ $a->label }} · {{ $a->min_buyers }}+ → {{ App\Support\Money::formatUgx($a->price) }}</span>@endforeach
            </p>
          @endif
          <div class="mt-1.5 h-1.5 w-40 overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700"><div class="h-full rounded-full bg-brand-600 dark:bg-brand-400" style="width: {{ $o->gang_target > 0 ? min(100, round($o->confirmed_count / $o->gang_target * 100)) : 0 }}%"></div></div>
          @php $fl = $o->featuredListings->first(fn ($f) => in_array($f->status, ['pending','active'])); @endphp
          @if($fl)<p class="mt-1 inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-bold text-amber-800 dark:bg-amber-950 dark:text-amber-300"><flux:icon.sparkles class="size-3" /> Featured {{ $fl->status }} · {{ $fl->days }}d</p>@endif
        </div>
        <div class="flex shrink-0 gap-1.5">
          <button wire:click="feature({{ $o->id }})" class="rounded-md border border-zinc-300 px-2.5 py-1.5 text-xs font-medium hover:bg-zinc-100 dark:border-zinc-600 dark:hover:bg-zinc-800">Feature</button>
          <button wire:click="toggleStatus({{ $o->id }})" class="rounded-md border border-zinc-300 px-2.5 py-1.5 text-xs font-medium hover:bg-zinc-100 dark:border-zinc-600 dark:hover:bg-zinc-800">{{ $o->status === 'active' ? 'Pause' : 'Activate' }}</button>
          <button wire:click="destroy({{ $o->id }})" wire:confirm="Delete this offer?" class="rounded-md border border-red-300 px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-950/40">Delete</button>
        </div>
      </div>
    @endforeach
  </div>
  <div class="mt-3">{{ $offers->links() }}</div>

  @if($featuring)
    <div class="fixed inset-0 z-50 grid place-items-center p-4" role="dialog" aria-modal="true" aria-label="Feature offer">
      <div class="absolute inset-0 bg-black/50" wire:click="$set('featuring', null)"></div>
      <div class="relative w-full max-w-sm rounded-2xl bg-white p-5 shadow-2xl dark:bg-zinc-900" x-data @keydown.escape.window="$wire.set('featuring', null)">
        <h2 class="flex items-center gap-1.5 text-base font-bold text-zinc-900 dark:text-white"><flux:icon.sparkles class="size-4 text-brand-600 dark:text-brand-400" /> Feature this offer</h2>
        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Pinned placement on Explore + homepage. UGX 1,000/day · 10 days for 6,500.</p>
        <div class="mt-3 grid grid-cols-2 gap-1.5">
          @foreach(\App\Support\FeaturedPricing::options() as $d)
            <button type="button" wire:click="$set('featureDays', {{ $d }})" class="rounded-lg border px-3 py-2 text-sm font-semibold {{ $featureDays === $d ? 'border-brand-600 bg-brand-50 text-brand-700 dark:border-brand-400 dark:bg-brand-950 dark:text-brand-300' : 'border-zinc-300 dark:border-zinc-600' }}">{{ $d }} day{{ $d > 1 ? 's' : '' }} · {{ App\Support\Money::formatUgx(\App\Support\FeaturedPricing::priceForDays($d)) }}</button>
          @endforeach
        </div>
        <div class="mt-3 flex items-center justify-between rounded-lg bg-stone-100 px-3 py-2 text-sm dark:bg-zinc-800"><span class="text-zinc-500">Total due</span><strong>{{ App\Support\Money::formatUgx($this->featurePrice()) }}</strong></div>
        <div class="mt-3 flex gap-2">
          <button type="button" wire:click="$set('featuring', null)" class="h-10 flex-1 rounded-full border border-zinc-300 text-sm dark:border-zinc-600">Cancel</button>
          <button type="button" wire:click="submitFeature" class="h-10 flex-1 rounded-full bg-brand-600 text-sm font-bold text-white hover:bg-brand-700 dark:bg-brand-500 dark:hover:bg-brand-400">Request</button>
        </div>
      </div>
    </div>
  @endif
</div>
