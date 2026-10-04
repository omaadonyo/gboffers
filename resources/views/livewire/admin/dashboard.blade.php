<div>
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">Platform overview</h1>
      <p class="text-sm text-zinc-500 dark:text-zinc-400">Everything happening on GBOffers, at a glance.</p>
    </div>
    <span class="rounded-lg bg-zinc-100 px-2.5 py-1 text-xs font-bold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">Commission pending: {{ App\Support\Money::formatUgx($commissionPending) }}</span>
  </div>

  <div class="mt-4 grid grid-cols-2 gap-2.5 lg:grid-cols-4">
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
      <div class="flex items-baseline justify-between">
        <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Volume · last 7 days</h2>
        <span class="text-xs text-zinc-500 dark:text-zinc-400">UGX</span>
      </div>
      <div class="mt-3 flex h-32 items-end gap-1.5" role="img" aria-label="Daily volume chart">
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
        <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Merchants awaiting approval</h2>
        <a href="{{ route('admin.merchants') }}" class="text-xs font-medium text-zinc-500 underline dark:text-zinc-400">Review all</a>
      </div>
      <div class="mt-2 divide-y divide-zinc-100 dark:divide-zinc-800">
        @forelse($pendingMerchants as $m)
          <a href="{{ route('admin.merchants') }}" class="flex items-center justify-between gap-2 py-2 text-sm">
            <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $m->trading_name ?? $m->business_name }}</span>
            <span class="rounded-md bg-amber-100 px-2 py-0.5 text-xs font-bold text-amber-700 dark:bg-amber-950 dark:text-amber-300">pending</span>
          </a>
        @empty<p class="py-3 text-xs text-zinc-500">Queue is clear. 🎉</p>@endforelse
      </div>
    </div>
  </div>

  <div class="mt-3 grid gap-2.5 lg:grid-cols-2">
    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
      <div class="flex items-baseline justify-between">
        <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Open disputes</h2>
        <a href="{{ route('admin.disputes') }}" class="text-xs font-medium text-zinc-500 underline dark:text-zinc-400">Resolve</a>
      </div>
      <div class="mt-2 divide-y divide-zinc-100 dark:divide-zinc-800">
        @forelse($openDisputes as $d)
          <a href="{{ route('admin.disputes') }}" class="block py-2 text-sm">
            <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $d->reason }}</span>
            <span class="block text-xs text-zinc-500">order #{{ $d->order_id }} · {{ str_replace('_', ' ', $d->status) }}</span>
          </a>
        @empty<p class="py-3 text-xs text-zinc-500">No open disputes.</p>@endforelse
      </div>
    </div>
    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
      <div class="flex items-baseline justify-between">
        <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Latest orders</h2>
        <a href="{{ route('admin.orders') }}" class="text-xs font-medium text-zinc-500 underline dark:text-zinc-400">View all</a>
      </div>
      <div class="mt-2 divide-y divide-zinc-100 dark:divide-zinc-800">
        @forelse($recentOrders as $o)
          <div class="flex items-center justify-between gap-2 py-2 text-sm">
            <span class="min-w-0 truncate text-zinc-700 dark:text-zinc-300">#{{ $o->id }} · {{ $o->customer->displayName() }} · {{ $o->merchant->trading_name ?? $o->merchant->business_name }}</span>
            <span class="shrink-0 font-semibold text-zinc-900 dark:text-zinc-100">{{ App\Support\Money::formatUgx($o->total) }}</span>
          </div>
        @empty<p class="py-3 text-xs text-zinc-500">No orders yet.</p>@endforelse
      </div>
    </div>
  </div>
</div>
