@props(['normal' => 0, 'gang' => 0])
@php $save = max(0, $normal - $gang); $pct = $normal > 0 ? round($save / $normal * 100) : 0; @endphp
<div class="flex flex-wrap items-baseline gap-2">
  <span class="text-xl font-bold tracking-tight text-stone-900 dark:text-white">{{ App\Support\Money::formatUgx($gang) }}</span>
  @if($save > 0)
    <span class="text-sm text-stone-400 line-through dark:text-stone-500">{{ App\Support\Money::formatUgx($normal) }}</span>
    <span class="rounded-md bg-emerald-50 px-1.5 py-0.5 text-xs font-semibold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">Save {{ App\Support\Money::formatUgx($save) }} ({{ $pct }}%)</span>
  @else
    <span class="text-xs text-stone-400 dark:text-stone-500">group price unlocks as buyers join</span>
  @endif
</div>
