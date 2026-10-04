<div>
  <div>
    <h1 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">Sales analytics</h1>
    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $this->merchant->trading_name ?? $this->merchant->business_name }} · paid revenue, fills and winners.</p>
  </div>

  <div class="mt-4 grid grid-cols-2 gap-2.5 lg:grid-cols-4">
    @foreach([['Revenue (paid)', App\Support\Money::formatUgx($revenue), 'banknotes'], ['Orders', number_format($orders), 'shopping-cart'], ['Groups formed', $gangsFormed.' · '.$gangsCompleted.' done', 'users'], ['Avg group fill', $avgFill.'%', 'chart-bar']] as [$l, $v, $i])
      <div class="rounded-lg border border-zinc-200 bg-white p-3.5 dark:border-zinc-700 dark:bg-zinc-900">
        <span class="grid size-8 place-items-center rounded-lg bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300"><flux:icon :name="$i" class="size-4" /></span>
        <p class="mt-2.5 truncate text-lg font-bold tracking-tight text-zinc-900 dark:text-white">{{ $v }}</p>
        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $l }}</p>
      </div>
    @endforeach
  </div>

  <div class="mt-3 rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
    <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Revenue · last 14 days</h2>
    <div class="mt-3 flex h-32 items-end gap-1" role="img" aria-label="Daily revenue chart">
      @foreach($days as $d)
        <div class="flex flex-1 flex-col items-center gap-1">
          <div class="w-full rounded-t-md bg-brand-600 dark:bg-brand-400" style="height: {{ max(4, round($d['value'] / $maxDay * 100)) }}px" title="{{ $d['label'] }} · {{ App\Support\Money::formatUgx($d['value']) }}"></div>
          <span class="hidden text-[10px] text-zinc-400 md:block">{{ $d['label'] }}</span>
        </div>
      @endforeach
    </div>
  </div>

  <div class="mt-3 grid gap-2.5 lg:grid-cols-2">
    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
      <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Orders by status</h2>
      <div class="mt-2 space-y-2">
        @forelse($byStatus as $s)
          <div>
            <div class="flex justify-between text-xs"><span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $s->status }}</span><span class="text-zinc-500">{{ $s->c }} · {{ App\Support\Money::formatUgx((int) $s->t) }}</span></div>
            <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700"><div class="h-full rounded-full bg-brand-600 dark:bg-brand-400" style="width: {{ $byStatus->max('c') > 0 ? round($s->c / $byStatus->max('c') * 100) : 0 }}%"></div></div>
          </div>
        @empty<p class="py-3 text-xs text-zinc-500">No orders yet.</p>@endforelse
      </div>
    </div>
    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
      <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Top offers by revenue</h2>
      <div class="mt-2 divide-y divide-zinc-100 dark:divide-zinc-800">
        @forelse($topOffers as $o)
          <a href="{{ route('offers.show', $o->slug) }}" class="flex items-center justify-between gap-2 py-2 text-sm">
            <span class="truncate font-medium text-zinc-900 dark:text-zinc-100">{{ $o->title }}</span>
            <span class="shrink-0 text-xs text-zinc-500">{{ $o->orders_count }} orders · <strong class="text-zinc-900 dark:text-zinc-100">{{ App\Support\Money::formatUgx((int) $o->revenue) }}</strong></span>
          </a>
        @empty<p class="py-3 text-xs text-zinc-500">No revenue yet.</p>@endforelse
      </div>
    </div>
  </div>
</div>
