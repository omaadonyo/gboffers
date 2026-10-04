@props(['title' => 'Nothing here yet', 'body' => '', 'action' => null, 'actionUrl' => '#', 'icon' => 'package-search'])
<div class="rounded-lg bg-white px-6 py-10 text-center shadow-sm dark:bg-stone-900">
  <flux:icon :name="$icon" class="mx-auto size-7 text-stone-300 dark:text-stone-600" />
  <h3 class="mt-2 text-sm font-semibold text-stone-900 dark:text-stone-100">{{ $title }}</h3>
  @if($body)<p class="mx-auto mt-1 max-w-sm text-sm text-stone-500 dark:text-stone-400">{{ $body }}</p>@endif
  @if($action)<a href="{{ $actionUrl }}" class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700 dark:bg-brand-500 dark:hover:bg-brand-400">{{ $action }}</a>@endif
</div>
