@props(['gang'])
@php
$offer = $gang->offer;
$members = $gang->members()->with('user.profile')->latest()->take(6)->get()->map(fn($m) => ['name' => $m->user?->displayName() ?? 'Member', 'contact' => $m->user?->profile?->phone ?? null]);
$catIcon = App\Support\CategoryStyle::iconFor($offer->category ?? null);
@endphp
<a href="/offers/{{ $offer->slug }}" class="group flex h-full flex-col rounded-lg bg-white p-3.5 shadow-sm transition hover:shadow-md dark:bg-stone-900">
  <div class="flex items-center justify-between gap-2">
    <span class="rounded-full bg-brand-50 px-2 py-0.5 font-mono text-[11px] font-bold text-brand-700 dark:bg-brand-950 dark:text-brand-300">{{ $gang->code }}</span>
    <span class="text-[11px] text-stone-400 dark:text-stone-500">@if($gang->expires_at)ends {{ $gang->expires_at->format('d M') }}@else open @endif</span>
  </div>
  <h3 class="mt-2 line-clamp-2 text-sm font-bold leading-snug text-stone-900 dark:text-stone-100">{{ $offer->title }}</h3>
  <p class="mt-0.5 flex items-center gap-1 truncate text-xs text-stone-500 dark:text-stone-400"><flux:icon :name="$catIcon" class="size-3.5 shrink-0 text-brand-600 dark:text-white" /> {{ $offer->merchant->trading_name ?? $offer->merchant->business_name ?? '' }}</p>
  <div class="mt-3"><x-gb.avatar-stack :users="$members" :limit="5" /></div>
  <div class="mt-2"><x-gb.gang-progress :confirmed="$gang->confirmed_count" :target="$gang->target" /></div>
  <span class="mt-3 inline-flex h-9 items-center justify-center gap-1.5 rounded-full bg-stone-900 text-xs font-bold text-white transition group-hover:bg-brand-600 dark:bg-white dark:text-stone-900 dark:group-hover:bg-brand-500 dark:group-hover:text-white"><flux:icon.users class="size-3.5" /> Join this group</span>
</a>
