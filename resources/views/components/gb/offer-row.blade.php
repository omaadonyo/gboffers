@props(['offer'])
@php
$svc = app(App\Services\PricingService::class);
$gang = $offer->gangs->firstWhere('status','forming') ?? $offer->gangs->first();
$confirmed = $gang?->confirmed_count ?? $offer->confirmed_count;
$target = $gang?->target ?? $offer->gang_target;
$price = $svc->priceFor($offer, $confirmed);
$pct = $target > 0 ? min(100, round($confirmed / $target * 100)) : 0;
$catIcon = App\Support\CategoryStyle::iconFor($offer->category);
@endphp
<a href="/offers/{{ $offer->slug }}" class="group flex items-center gap-3 rounded-lg bg-white p-3 shadow-sm transition hover:shadow-md dark:bg-stone-900">
  <span class="grid size-14 shrink-0 place-items-center overflow-hidden rounded-md bg-stone-100 dark:bg-stone-800">
    @if($offer->image_path)<img src="{{ $offer->image_path }}" alt="" loading="lazy" class="size-full object-cover">
    @else<flux:icon :name="$catIcon" class="size-6 text-brand-600 dark:text-white" />@endif
  </span>
  <span class="min-w-0 flex-1">
    <span class="flex items-center gap-1 text-[11px] uppercase tracking-wide text-stone-500 dark:text-stone-400"><flux:icon :name="$catIcon" class="size-3 shrink-0 text-brand-600 dark:text-white" /><span class="truncate">{{ $offer->category->name ?? 'Offer' }} · {{ $offer->merchant->trading_name ?? $offer->merchant->business_name ?? '' }}</span></span>
    <span class="mt-0.5 block truncate text-sm font-semibold text-stone-900 dark:text-stone-100">{{ $offer->title }}</span>
    <span class="mt-1.5 flex items-center gap-2">
      <span class="h-1.5 w-24 overflow-hidden rounded-full bg-stone-200 sm:w-32 dark:bg-stone-700"><span class="block h-full rounded-full bg-brand-600 dark:bg-brand-400" style="width: {{ $pct }}%"></span></span>
      <span class="text-xs text-stone-500 dark:text-stone-400">{{ $confirmed }}/{{ $target }} · @if($offer->ends_at)ends {{ $offer->ends_at->format('d M') }}@else open @endif</span>
    </span>
  </span>
  <span class="shrink-0 text-right">
    <span class="block text-base font-bold tracking-tight text-stone-900 dark:text-white">{{ App\Support\Money::formatUgx($price) }}</span>
    <span class="block text-xs text-stone-400 line-through dark:text-stone-500">{{ App\Support\Money::formatUgx($offer->normal_price) }}</span>
  </span>
  <flux:icon.chevron-right class="size-4 shrink-0 text-stone-300 transition group-hover:translate-x-0.5 group-hover:text-brand-600 dark:text-stone-600 dark:group-hover:text-brand-400" />
</a>
