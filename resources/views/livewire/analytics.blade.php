<div class="mx-auto max-w-3xl">
  <div>
    <h1 class="text-xl font-bold tracking-tight">My stats</h1>
    <p class="text-sm text-stone-500 dark:text-stone-400">Your group-buying record — spending, savings and completed groups.</p>
  </div>

  <div class="mt-4 grid grid-cols-2 gap-2.5 md:grid-cols-4">
    @foreach([['Total spent', App\Support\Money::formatUgx($spent), 'banknotes'], ['Total saved', App\Support\Money::formatUgx($saved), 'receipt-percent'], ['Groups joined', $joined.' · '.$completed.' done', 'users'], ['Passes redeemed', $passes, 'ticket']] as [$l, $v, $i])
      <div class="rounded-lg bg-white p-3.5 shadow-sm dark:bg-stone-900">
        <span class="grid size-8 place-items-center rounded-lg bg-stone-100 text-stone-600 dark:bg-stone-800 dark:text-stone-300"><flux:icon :name="$i" class="size-4" /></span>
        <p class="mt-2.5 truncate text-lg font-bold tracking-tight text-stone-900 dark:text-white">{{ $v }}</p>
        <p class="text-xs text-stone-500 dark:text-stone-400">{{ $l }}</p>
      </div>
    @endforeach
  </div>

  <div class="mt-3 rounded-lg bg-white p-4 shadow-sm dark:bg-stone-900">
    <div class="flex items-baseline justify-between">
      <h2 class="text-sm font-bold text-stone-900 dark:text-stone-100">Spending · last 6 months</h2>
      <span class="text-xs text-stone-500 dark:text-stone-400">{{ $orderCount }} paid orders · loves {{ $topCat }}</span>
    </div>
    <div class="mt-3 flex h-32 items-end gap-2" role="img" aria-label="Monthly spending chart">
      @foreach($months as $m)
        <div class="flex flex-1 flex-col items-center gap-1">
          <div class="w-full rounded-t-md bg-brand-600 dark:bg-brand-400" style="height: {{ max(4, round($m['value'] / $maxMonth * 100)) }}px" title="{{ $m['label'] }} · {{ App\Support\Money::formatUgx($m['value']) }}"></div>
          <span class="text-[11px] text-stone-400">{{ $m['label'] }}</span>
        </div>
      @endforeach
    </div>
  </div>

  <div class="mt-3 grid gap-2 text-sm">
    <a href="/groups" class="flex items-center gap-3 rounded-lg bg-white p-3.5 shadow-sm dark:bg-stone-900">
      <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-stone-100 text-stone-600 dark:bg-stone-800 dark:text-stone-300"><flux:icon.users class="size-4" /></span>
      <span class="flex-1"><span class="block font-semibold">My groups</span><span class="block text-xs text-stone-500 dark:text-stone-400">Track active and completed groups</span></span>
      <flux:icon.chevron-right class="size-4 text-stone-400" />
    </a>
    <a href="/wallet" class="flex items-center gap-3 rounded-lg bg-white p-3.5 shadow-sm dark:bg-stone-900">
      <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-stone-100 text-stone-600 dark:bg-stone-800 dark:text-stone-300"><flux:icon.ticket class="size-4" /></span>
      <span class="flex-1"><span class="block font-semibold">GBPass wallet</span><span class="block text-xs text-stone-500 dark:text-stone-400">Passes ready to redeem</span></span>
      <flux:icon.chevron-right class="size-4 text-stone-400" />
    </a>
  </div>
</div>
