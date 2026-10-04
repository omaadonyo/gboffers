@props(['offer'])
@php
$svc = app(App\Services\PricingService::class);
$gang = $offer->gangs->firstWhere('status','forming') ?? $offer->gangs->first();
$confirmed = $gang?->confirmed_count ?? $offer->confirmed_count;
$target = $gang?->target ?? $offer->gang_target;
$price = $svc->priceFor($offer, $confirmed);
$next = $svc->nextTier($offer, $confirmed);
$catIcon = App\Support\CategoryStyle::iconFor($offer->category);
$members = $gang ? $gang->members()->with('user.profile')->latest()->take(6)->get()->map(fn($m) => ['name' => $m->user?->displayName() ?? 'Member', 'contact' => $m->user?->profile?->phone ?? null]) : collect();
@endphp
<a href="/offers/{{ $offer->slug }}" class="group block overflow-hidden rounded-lg bg-white shadow-sm transition hover:shadow-md dark:bg-stone-900">
  <div class="relative aspect-[4/3] overflow-hidden bg-stone-100 dark:bg-stone-800">
    @if($offer->image_path)<img src="{{ $offer->image_path }}" alt="{{ $offer->title }}" loading="lazy" class="size-full object-cover transition-transform duration-300 group-hover:scale-[1.02]">
    @else<div class="flex size-full flex-col items-center justify-center gap-1.5"><flux:icon :name="$catIcon" class="size-9 text-brand-600/50 dark:text-white/50" /><span class="text-[10px] font-bold uppercase tracking-widest text-stone-400 dark:text-stone-500">{{ $offer->category->name ?? 'GBOffers' }}</span></div>@endif
    @if($offer->featured)<span class="absolute left-2 top-2 inline-flex items-center gap-1 rounded-full bg-brand-600 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-white shadow"><flux:icon.sparkles class="size-3" /> Featured</span>@endif
  </div>
  <div class="p-3">
    <p class="flex items-center gap-1 truncate text-[11px] uppercase tracking-wide text-stone-500 dark:text-stone-400"><flux:icon :name="$catIcon" class="size-3 shrink-0 text-brand-600 dark:text-white" />{{ $offer->category->name ?? 'Offer' }} · {{ $offer->merchant->trading_name ?? $offer->merchant->business_name ?? '' }}</p>
    <h3 class="mt-0.5 line-clamp-2 text-sm font-semibold leading-snug text-stone-900 dark:text-stone-100">{{ $offer->title }}</h3>
    <div class="mt-2"><x-gb.price-breakdown :normal="$offer->normal_price" :gang="$price" /></div>
    <div class="mt-2 flex items-center justify-between gap-2">
      <x-gb.avatar-stack :users="$members" :limit="4" size="size-6" />
      <span class="shrink-0 text-xs font-semibold text-stone-500 dark:text-stone-400">{{ $confirmed }}/{{ $target }} joined</span>
    </div>
    <div class="mt-2"><x-gb.gang-progress :confirmed="$confirmed" :target="$target" :next-price="$next['price'] ?? null" /></div>
  </div>
</a>
