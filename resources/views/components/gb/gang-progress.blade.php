@props(['confirmed' => 0, 'target' => 10, 'price' => null, 'nextPrice' => null, 'endsAt' => null])
@php $pct = $target > 0 ? min(100, round($confirmed / $target * 100, 1)) : 0; $need = max(0, $target - $confirmed); @endphp
<div class="w-full" x-data="{ pct: {{ $pct }} }">
  <div class="flex items-baseline justify-between gap-2">
    <p class="text-sm font-semibold text-stone-900 dark:text-stone-100"><span>{{ $confirmed }}/{{ $target }}</span> <span class="font-normal text-stone-500 dark:text-stone-400">confirmed</span></p>
    @if($need > 0)<p class="text-xs text-stone-600 dark:text-stone-400">{{ $need }} more {{ Str::plural('buyer', $need) }} unlock{{ $need === 1 ? 's' : '' }} @if($nextPrice)<span class="font-semibold text-stone-900 dark:text-stone-100">{{ App\Support\Money::formatUgx($nextPrice) }}</span>@endif</p>
    @else<p class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 dark:text-emerald-400"><flux:icon.badge-check class="size-3.5" /> Group price unlocked</p>@endif
  </div>
  <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-stone-200 dark:bg-stone-700" role="progressbar" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
    <div class="h-full rounded-full bg-brand-600 transition-all duration-500 dark:bg-brand-400" style="width: {{ $pct }}%"></div>
  </div>
  @if($endsAt)<p class="mt-1.5 text-xs text-stone-500 dark:text-stone-400" x-data="{ left: '' }" x-init="const e = new Date('{{ $endsAt }}').getTime(); const tick = () => { const d = e - Date.now(); if (d <= 0) { left = 'Ending now'; return; } const h = Math.floor(d/36e5), m = Math.floor(d%36e5/6e4); left = h > 0 ? h + 'h ' + m + 'm left' : m + 'm left'; }; tick(); setInterval(tick, 30000);" x-text="left"></p>@endif
</div>
