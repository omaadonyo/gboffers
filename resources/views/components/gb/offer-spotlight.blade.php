@props(['offer', 'flip' => false])
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
<article class="grid overflow-hidden rounded-lg bg-white shadow-sm transition hover:shadow-md md:grid-cols-2 dark:bg-stone-900">
  <div class="relative min-h-52 overflow-hidden bg-stone-100 md:min-h-64 dark:bg-stone-800 @if($flip) md:order-2 @endif">
    @if($offer->image_path)<img src="{{ $offer->image_path }}" alt="{{ $offer->title }}" loading="lazy" class="absolute inset-0 size-full object-cover">
    @else<div class="absolute inset-0 flex flex-col items-center justify-center gap-2"><flux:icon :name="$catIcon" class="size-14 text-brand-600/50 dark:text-white/50" /><span class="text-[11px] font-bold uppercase tracking-widest text-stone-400 dark:text-stone-500">{{ $offer->category->name ?? 'GBOffers' }}</span></div>@endif
    <span class="absolute left-3 top-3 inline-flex items-center gap-1 rounded-full bg-white/90 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-stone-700 backdrop-blur dark:bg-stone-950/80 dark:text-white"><flux:icon :name="$catIcon" class="size-3.5 text-brand-600 dark:text-white" /> {{ $offer->category->name ?? 'Spotlight' }}</span>
  </div>
  <div class="flex flex-col p-5 md:p-6 @if($flip) md:order-1 @endif">
    <p class="text-xs text-stone-500 dark:text-stone-400">{{ $offer->merchant->trading_name ?? $offer->merchant->business_name }} · <x-gb.merchant-badge :merchant="$offer->merchant" :link="false" /></p>
    <h3 class="mt-1 text-lg font-bold leading-snug tracking-tight text-stone-900 md:text-xl dark:text-stone-100">{{ $offer->title }}</h3>
    <div class="mt-2"><x-gb.price-breakdown :normal="$offer->normal_price" :gang="$price" /></div>
    <div class="mt-3 flex items-center justify-between gap-2">
      <x-gb.avatar-stack :users="$members" :limit="5" size="size-7" />
      <span class="shrink-0 text-xs font-semibold text-stone-500 dark:text-stone-400">{{ $confirmed }}/{{ $target }} joined</span>
    </div>
    <div class="mt-2"><x-gb.gang-progress :confirmed="$confirmed" :target="$target" :next-price="$next['price'] ?? null" :ends-at="$offer->ends_at" /></div>
    <div class="mt-4 flex gap-2 pt-1">
      <a href="/offers/{{ $offer->slug }}" class="inline-flex h-10 flex-1 items-center justify-center gap-1.5 rounded-lg bg-brand-600 text-sm font-semibold text-white hover:bg-brand-700 dark:bg-brand-500 dark:hover:bg-brand-400"><flux:icon.users class="size-4" /> Join the group</a>
      <a href="/offers/{{ $offer->slug }}" class="inline-flex h-10 items-center rounded-lg border border-stone-300 px-4 text-sm font-medium hover:bg-stone-100 dark:border-stone-700 dark:hover:bg-stone-800">Details</a>
    </div>
  </div>
</article>
