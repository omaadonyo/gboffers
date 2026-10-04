@props(['merchant' => null, 'link' => true])
@if($merchant)
@php $label = $merchant->trading_name ?? $merchant->business_name; @endphp
@if($link)<a href="{{ route('merchants.show', $merchant->slug) }}" class="inline-flex items-center gap-1 text-xs hover:underline">@else<span class="inline-flex items-center gap-1 text-xs">@endif
  @if($merchant->isVerified())<flux:icon.badge-check class="size-3.5 text-emerald-700 dark:text-emerald-400" /><span class="font-medium text-emerald-800 dark:text-emerald-300">Verified merchant</span>
  @else<flux:icon.circle-alert class="size-3.5 text-stone-400 dark:text-stone-500" /><span class="text-stone-500 dark:text-stone-400">Unverified merchant</span>@endif
@if($link)</a>@else</span>@endif
@endif
