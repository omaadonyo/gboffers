<div>
  <div>
    <h1 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">Platform analytics</h1>
    <p class="text-sm text-zinc-500 dark:text-zinc-400">Revenue, funnel and leaderboard across the marketplace.</p>
  </div>

  <div class="mt-4 grid grid-cols-2 gap-2.5 lg:grid-cols-4">
    @foreach([['Gross volume', App\Support\Money::formatUgx($gmv), 'banknotes'], ['Transactions', number_format($days->sum('orders')), 'receipt-percent'], ['Offers live', number_format($offerCount), 'tag'], ['Signups · 14d', number_format($signups), 'user-group']] as [$l, $v, $i])
      <div class="rounded-lg border border-zinc-200 bg-white p-3.5 dark:border-zinc-700 dark:bg-zinc-900">
        <span class="grid size-8 place-items-center rounded-lg bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300"><flux:icon :name="$i" class="size-4" /></span>
        <p class="mt-2.5 truncate text-lg font-bold tracking-tight text-zinc-900 dark:text-white">{{ $v }}</p>
        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $l }}</p>
      </div>
    @endforeach
  </div>

  <div class="mt-3 rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
    <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Volume · last 14 days</h2>
    <div class="mt-3 flex h-32 items-end gap-1" role="img" aria-label="Daily volume chart">
      @foreach($days as $d)
        <div class="flex flex-1 flex-col items-center gap-1">
          <div class="w-full rounded-t-md bg-brand-600 dark:bg-brand-400" style="height: {{ max(4, round($d['value'] / $maxDay * 100)) }}px" title="{{ $d['label'] }} · {{ App\Support\Money::formatUgx($d['value']) }} · {{ $d['orders'] }} orders"></div>
          <span class="hidden text-[10px] text-zinc-400 md:block">{{ $d['label'] }}</span>
        </div>
      @endforeach
    </div>
  </div>

  <div class="mt-3 grid gap-2.5 lg:grid-cols-2">
    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
      <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Orders by status</h2>
      <div class="mt-2 space-y-2">
        @foreach($byStatus as $s)
          <div>
            <div class="flex justify-between text-xs"><span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $s->status }}</span><span class="text-zinc-500">{{ $s->c }} · {{ App\Support\Money::formatUgx((int) $s->t) }}</span></div>
            <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700"><div class="h-full rounded-full bg-brand-600 dark:bg-brand-400" style="width: {{ $byStatus->max('c') > 0 ? round($s->c / $byStatus->max('c') * 100) : 0 }}%"></div></div>
          </div>
        @endforeach
      </div>
    </div>
    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
      <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Group funnel</h2>
      <div class="mt-2 space-y-2">
        @foreach($funnel as $f)
          <div>
            <div class="flex justify-between text-xs"><span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $f->status }}</span><span class="text-zinc-500">{{ $f->c }}</span></div>
            <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700"><div class="h-full rounded-full bg-zinc-900 dark:bg-white" style="width: {{ $funnel->max('c') > 0 ? round($f->c / $funnel->max('c') * 100) : 0 }}%"></div></div>
          </div>
        @endforeach
      </div>
      <h2 class="mt-4 text-sm font-bold text-zinc-900 dark:text-zinc-100">Commissions</h2>
      <div class="mt-2 flex flex-wrap gap-1.5">
        @foreach($commissions as $c)<span class="rounded-md bg-zinc-100 px-2 py-1 text-xs font-bold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $c->status }}: {{ App\Support\Money::formatUgx((int) $c->t) }}</span>@endforeach
      </div>
    </div>
  </div>

  <div class="mt-3 grid gap-2.5 lg:grid-cols-2">
    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
      <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Top merchants by revenue</h2>
      <div class="mt-2 divide-y divide-zinc-100 dark:divide-zinc-800">
        @forelse($topMerchants as $m)
          <a href="{{ route('merchants.show', $m->slug) }}" class="flex items-center justify-between gap-2 py-2 text-sm">
            <span class="truncate font-medium text-zinc-900 dark:text-zinc-100">{{ $m->trading_name ?? $m->business_name }}</span>
            <span class="shrink-0 text-xs text-zinc-500">{{ $m->orders_count }} orders · <strong class="text-zinc-900 dark:text-zinc-100">{{ App\Support\Money::formatUgx((int) $m->revenue) }}</strong></span>
          </a>
        @empty<p class="py-3 text-xs text-zinc-500">No revenue yet.</p>@endforelse
      </div>
    </div>
    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
      <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Categories by offers</h2>
      <div class="mt-2 divide-y divide-zinc-100 dark:divide-zinc-800">
        @foreach($topCats as $c)
          <a href="{{ route('explore', ['category' => $c->slug]) }}" class="flex items-center gap-2.5 py-2 text-sm">
            <flux:icon :name="App\Support\CategoryStyle::iconFor($c)" class="size-4 shrink-0 text-brand-600 dark:text-white" />
            <span class="flex-1 font-medium text-zinc-900 dark:text-zinc-100">{{ $c->name }}</span>
            <span class="text-xs text-zinc-500">{{ $c->active_offers }} live / {{ $c->offers_count }}</span>
          </a>
        @endforeach
      </div>
    </div>
  </div>
</div>
