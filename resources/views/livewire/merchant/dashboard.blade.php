<div>
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <span class="grid size-11 place-items-center rounded-lg bg-brand-600 text-sm font-bold text-white dark:bg-brand-500">{{ strtoupper(substr($this->merchant->trading_name ?? $this->merchant->business_name, 0, 2)) }}</span>
      <div>
        <h1 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ $this->merchant->trading_name ?? $this->merchant->business_name }}</h1>
        <p class="text-xs text-zinc-500 dark:text-zinc-400"><x-gb.merchant-badge :merchant="$this->merchant" :link="false" /> · Commission owed: {{ App\Support\Money::formatUgx($owed) }}</p>
      </div>
    </div>
    <div class="flex gap-1.5">
      @if(auth()->user()?->isAdmin())
        <select wire:change="switchMerchant($event.target.value)" aria-label="View merchant" class="h-9 rounded-lg border border-amber-300 bg-amber-50 px-2 text-sm font-medium text-amber-900 dark:border-amber-800 dark:bg-amber-950/40 dark:text-amber-200">
          @foreach($this->merchantChoices() as $c)<option value="{{ $c['slug'] }}" @selected($c['slug'] === $this->merchant->slug)>{{ $c['name'] }}</option>@endforeach
        </select>
      @endif
      <a href="/merchant/scan" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-3.5 py-2 text-sm font-semibold text-white hover:bg-brand-700 dark:bg-brand-500 dark:hover:bg-brand-400"><flux:icon.qr-code class="size-4" /> Scan GBPass</a>
      <a href="{{ route('merchants.show', $this->merchant->slug) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-300 px-3.5 py-2 text-sm font-medium hover:bg-zinc-100 dark:border-zinc-600 dark:hover:bg-zinc-800"><flux:icon.store class="size-4" /> View my page</a>
    </div>
  </div>

  <div class="mt-4 grid grid-cols-2 gap-2.5 md:grid-cols-3 xl:grid-cols-6">
    @foreach($stats as $s)
      <a href="{{ $s['href'] }}" class="rounded-lg border border-zinc-200 bg-white p-3.5 transition hover:border-zinc-300 hover:shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600">
        <span class="grid size-8 place-items-center rounded-lg bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300"><flux:icon :name="$s['icon']" class="size-4" /></span>
        <p class="mt-2.5 truncate text-lg font-bold tracking-tight text-zinc-900 dark:text-white">{{ $s['value'] }}</p>
        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $s['label'] }}</p>
      </a>
    @endforeach
  </div>

  <div class="mt-3 grid gap-2.5 lg:grid-cols-5">
    <div class="rounded-lg border border-zinc-200 bg-white p-4 lg:col-span-3 dark:border-zinc-700 dark:bg-zinc-900">
      <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Revenue · last 7 days</h2>
      <div class="mt-3 flex h-32 items-end gap-1.5" role="img" aria-label="Daily revenue chart">
        @foreach($days as $d)
          <div class="flex flex-1 flex-col items-center gap-1">
            <div class="w-full rounded-t-md bg-brand-600 dark:bg-brand-400" style="height: {{ max(4, round($d['value'] / $maxDay * 100)) }}px" title="{{ App\Support\Money::formatUgx($d['value']) }}"></div>
            <span class="text-[10px] text-zinc-400">{{ $d['label'] }}</span>
          </div>
        @endforeach
      </div>
    </div>
    <div class="rounded-lg border border-zinc-200 bg-white p-4 lg:col-span-2 dark:border-zinc-700 dark:bg-zinc-900">
      <div class="flex items-baseline justify-between">
        <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Offers ending soon</h2>
        <a href="/merchant/offers" class="text-xs font-medium text-zinc-500 underline dark:text-zinc-400">Manage</a>
      </div>
      <div class="mt-2 divide-y divide-zinc-100 dark:divide-zinc-800">
        @forelse($ending as $o)
          <a href="{{ route('offers.show', $o->slug) }}" class="flex items-center justify-between gap-2 py-2 text-sm">
            <span class="truncate font-medium text-zinc-900 dark:text-zinc-100">{{ $o->title }}</span>
            <span class="shrink-0 text-xs text-zinc-500">{{ $o->ends_at->format('d M') }} · {{ $o->confirmed_count }}/{{ $o->gang_target }}</span>
          </a>
        @empty<p class="py-3 text-xs text-zinc-500">No active offers ending.</p>@endforelse
      </div>
    </div>
  </div>

  <div class="mt-3 rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
    <div class="flex items-baseline justify-between">
      <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Recent orders</h2>
      <a href="/merchant/orders" class="text-xs font-medium text-zinc-500 underline dark:text-zinc-400">View all</a>
    </div>
    <div class="mt-2 divide-y divide-zinc-100 dark:divide-zinc-800">
      @forelse($recent as $o)
        <a href="/merchant/orders?highlight={{ $o->id }}" class="flex items-center justify-between gap-3 py-2 text-sm">
          <span class="min-w-0 truncate text-zinc-700 dark:text-zinc-300">{{ $o->customer->displayName() }} · {{ $o->offer->title }}</span>
          <span class="flex shrink-0 items-center gap-2"><span class="rounded-md bg-zinc-100 px-1.5 py-0.5 text-[11px] font-bold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">{{ $o->status }}</span><span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ App\Support\Money::formatUgx($o->total) }}</span></span>
        </a>
      @empty<p class="py-3 text-xs text-zinc-500">No orders yet — share your offers to get groups forming.</p>@endforelse
    </div>
  </div>
</div>
