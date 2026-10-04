@props(['offer'])
@php
$svc = app(App\Services\PricingService::class);
$gang = $offer->gangs->firstWhere('status','forming') ?? $offer->gangs->first();
$confirmed = $gang?->confirmed_count ?? 0;
$target = $gang?->target ?? $offer->gang_target;
$price = $svc->priceFor($offer, $confirmed);
$members = $gang ? $gang->members()->with('user.profile')->latest()->take(6)->get()->map(fn($m) => ['name' => $m->user?->displayName() ?? 'Member', 'contact' => $m->user?->profile?->phone ?? null]) : collect();
@endphp
<a href="/offers/{{ $offer->slug }}" class="group block overflow-hidden rounded-lg bg-white shadow-sm transition hover:shadow-md dark:bg-stone-900">
  <div class="aspect-[4/3] overflow-hidden bg-stone-100 dark:bg-stone-800">
    @if($offer->image_path)<img src="{{ $offer->image_path }}" alt="{{ $offer->title }}" loading="lazy" class="size-full object-cover transition-transform duration-300 group-hover:scale-[1.02]">
    @else<div class="flex size-full items-center justify-center"><flux:icon.tv class="size-10 text-brand-600/50 dark:text-white/50" /></div>@endif
  </div>
  <div class="p-3">
    <p class="flex items-center gap-1 truncate text-[11px] uppercase tracking-wide text-stone-500 dark:text-stone-400"><flux:icon.tv class="size-3 shrink-0 text-brand-600 dark:text-white" /> Electronics · collective buying power</p>
    <h3 class="mt-0.5 line-clamp-2 text-sm font-semibold text-stone-900 dark:text-stone-100">{{ $offer->title }}</h3>
    <div class="mt-1 flex items-center justify-between gap-2">
      <x-gb.avatar-stack :users="$members" :limit="4" size="size-6" />
      @if($offer->normal_price > $price)<span class="shrink-0 text-xs font-semibold text-emerald-700 dark:text-emerald-400">Save {{ App\Support\Money::formatUgx($offer->normal_price - $price) }}</span>@endif
    </div>
    <div class="mt-1"><x-gb.price-breakdown :normal="$offer->normal_price" :gang="$price" /></div>
    <div class="mt-2"><x-gb.gang-progress :confirmed="$confirmed" :target="$target" /></div>
    <div class="mt-2 flex items-center gap-1"><x-gb.merchant-badge :merchant="$offer->merchant" :link="false" /></div>
  </div>
</a>
