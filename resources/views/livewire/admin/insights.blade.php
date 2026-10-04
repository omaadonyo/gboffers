<div>
  <div>
    <h1 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">Views &amp; search trends</h1>
    <p class="text-sm text-zinc-500 dark:text-zinc-400">What buyers look at and what they type.</p>
  </div>

  <div class="mt-4 grid grid-cols-2 gap-2.5 lg:grid-cols-4">
    @foreach([['Offer views · 14d', number_format($days->sum('value')), 'eye'], ['All-time views', number_format($totalViews), 'eye'], ['Searches tracked', number_format($totalSearches), 'magnifying-glass'], ['Keywords', number_format($topTerms->count()), 'hashtag']] as [$l, $v, $i])
      <div class="rounded-lg border border-zinc-200 bg-white p-3.5 dark:border-zinc-700 dark:bg-zinc-900">
        <span class="grid size-8 place-items-center rounded-lg bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300"><flux:icon :name="$i" class="size-4" /></span>
        <p class="mt-2.5 truncate text-lg font-bold tracking-tight text-zinc-900 dark:text-white">{{ $v }}</p>
        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $l }}</p>
      </div>
    @endforeach
  </div>

  <div class="mt-3 rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
    <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Offer views · last 14 days</h2>
    <div class="mt-3 flex h-32 items-end gap-1" role="img" aria-label="Daily views chart">
      @foreach($days as $d)
        <div class="flex flex-1 flex-col items-center gap-1">
          <div class="w-full rounded-t-md bg-brand-600 dark:bg-brand-400" style="height: {{ max(4, round($d['value'] / $maxDay * 100)) }}px" title="{{ $d['label'] }} · {{ $d['value'] }} views"></div>
          <span class="hidden text-[10px] text-zinc-400 md:block">{{ $d['label'] }}</span>
        </div>
      @endforeach
    </div>
  </div>

  <div class="mt-3 grid gap-2.5 lg:grid-cols-2">
    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
      <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Most viewed offers</h2>
      <div class="mt-2 divide-y divide-zinc-100 dark:divide-zinc-800">
        @forelse($topOffers as $o)
          <a href="{{ route('offers.show', $o->slug) }}" class="flex items-center justify-between gap-2 py-2 text-sm">
            <span class="min-w-0 truncate font-medium text-zinc-900 dark:text-zinc-100">{{ $o->title }} <span class="font-normal text-zinc-400">· {{ $o->merchant->trading_name ?? $o->merchant->business_name }}</span></span>
            <span class="shrink-0 rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-bold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ number_format($o->view_logs_count) }} views</span>
          </a>
        @empty<p class="py-3 text-xs text-zinc-500">No views tracked yet.</p>@endforelse
      </div>
    </div>
    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
      <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Top search keywords</h2>
      <div class="mt-2 divide-y divide-zinc-100 dark:divide-zinc-800">
        @forelse($topTerms as $t)
          <a href="{{ route('explore', ['q' => $t->term]) }}" class="flex items-center justify-between gap-2 py-2 text-sm">
            <span class="font-medium text-zinc-900 dark:text-zinc-100">“{{ $t->term }}”</span>
            <span class="shrink-0 text-xs text-zinc-500">{{ number_format($t->hits) }} hits · {{ $t->last_searched_at?->diffForHumans() ?? '—' }}</span>
          </a>
        @empty<p class="py-3 text-xs text-zinc-500">No searches tracked yet.</p>@endforelse
      </div>
    </div>
  <div class="mt-3 grid gap-2.5 lg:grid-cols-2">
    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
      <div class="flex items-baseline justify-between">
        <h2 class="flex items-center gap-1.5 text-sm font-bold text-zinc-900 dark:text-zinc-100"><flux:icon.share-2 class="size-4" /> Shares</h2>
        <span class="rounded-lg bg-zinc-100 px-2.5 py-1 text-xs font-bold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ number_format($totalShares) }} total</span>
      </div>
      <div class="mt-2 flex flex-wrap gap-1.5">
        @forelse($sharesByChannel as $s)<span class="rounded-md bg-zinc-100 px-2 py-1 text-xs font-bold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $s->channel }}: {{ number_format($s->c) }}</span>@empty<span class="text-xs text-zinc-500">No shares tracked yet.</span>@endforelse
      </div>
      <div class="mt-2 divide-y divide-zinc-100 dark:divide-zinc-800">
        @foreach($topShared as $o)
          <a href="{{ route('offers.show', $o->slug) }}" class="flex items-center justify-between gap-2 py-2 text-sm">
            <span class="min-w-0 truncate font-medium text-zinc-900 dark:text-zinc-100">{{ $o->title }}</span>
            <span class="shrink-0 text-xs font-bold text-zinc-600 dark:text-zinc-300">{{ $o->shares_count }} shares</span>
          </a>
        @endforeach
      </div>
    </div>
  </div>
</div>
