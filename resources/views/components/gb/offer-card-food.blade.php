@props(['offer'])
@php
$gang = $offer->gangs->firstWhere('status','forming') ?? $offer->gangs->first();
$members = $gang ? $gang->members()->with('user.profile')->latest()->take(6)->get()->map(fn($m) => ['name' => $m->user?->displayName() ?? 'Member', 'contact' => $m->user?->profile?->phone ?? null]) : collect();
$price = app(App\Services\PricingService::class)->priceFor($offer, $gang->confirmed_count ?? 0);
@endphp
<a href="/offers/{{ $offer->slug }}" class="group block overflow-hidden rounded-lg bg-white shadow-sm transition hover:shadow-md dark:bg-stone-900">
  <div class="relative aspect-[4/3] overflow-hidden bg-stone-100 dark:bg-stone-800">
    @if($offer->image_path)<img src="{{ $offer->image_path }}" alt="{{ $offer->title }}" loading="lazy" class="size-full object-cover transition-transform duration-300 group-hover:scale-[1.02]">
    @else<div class="flex size-full items-center justify-center"><flux:icon.utensils class="size-10 text-brand-600/50 dark:text-white/50" /></div>@endif
    <div class="absolute inset-x-0 bottom-0 flex items-center justify-between bg-white/90 px-3 py-1.5 backdrop-blur dark:bg-stone-950/80">
      <x-gb.avatar-stack :users="$members" :limit="5" />
      <span class="text-xs font-bold text-stone-700 dark:text-stone-300">{{ $gang->confirmed_count ?? 0 }}/{{ $gang->target ?? $offer->gang_target }}</span>
    </div>
  </div>
  <div class="p-3">
    <p class="flex items-center gap-1 truncate text-[11px] uppercase tracking-wide text-stone-500 dark:text-stone-400"><flux:icon.utensils class="size-3 shrink-0 text-brand-600 dark:text-white" /> Food group · {{ $offer->merchant->trading_name ?? '' }}</p>
    <h3 class="mt-0.5 line-clamp-2 text-sm font-semibold text-stone-900 dark:text-stone-100">{{ $offer->title }}</h3>
    <div class="mt-1"><x-gb.price-breakdown :normal="$offer->normal_price" :gang="$price" /></div>
    <span class="mt-2 inline-flex items-center gap-1.5 rounded-md bg-brand-600 px-3 py-1.5 text-xs font-semibold uppercase tracking-wide text-white dark:bg-brand-500"><flux:icon.users class="size-3.5" /> Join the food group</span>
  </div>
</a>
