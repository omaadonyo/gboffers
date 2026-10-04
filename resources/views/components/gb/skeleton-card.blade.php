@props(['variant' => 'default'])
@if($variant === 'group')
<div class="flex h-full animate-pulse flex-col rounded-lg bg-white p-3.5 shadow-sm dark:bg-stone-900" aria-hidden="true">
  <div class="flex items-center justify-between gap-2">
    <div class="h-5 w-20 rounded-full bg-stone-200 dark:bg-stone-700"></div>
    <div class="h-3 w-14 rounded-full bg-stone-200 dark:bg-stone-700"></div>
  </div>
  <div class="mt-2 h-4 w-full rounded-full bg-stone-200 dark:bg-stone-700"></div>
  <div class="mt-1 h-4 w-2/3 rounded-full bg-stone-200 dark:bg-stone-700"></div>
  <div class="mt-0.5 h-3 w-1/2 rounded-full bg-stone-200 dark:bg-stone-700"></div>
  <div class="mt-3 flex -space-x-2"><div class="size-7 rounded-full bg-stone-300 dark:bg-stone-600"></div><div class="size-7 rounded-full bg-stone-300 dark:bg-stone-600"></div><div class="size-7 rounded-full bg-stone-300 dark:bg-stone-600"></div><div class="size-7 rounded-full bg-stone-300 dark:bg-stone-600"></div></div>
  <div class="mt-2 space-y-1.5"><div class="h-3 w-1/2 rounded-full bg-stone-200 dark:bg-stone-700"></div><div class="h-1.5 w-full rounded-full bg-stone-200 dark:bg-stone-700"></div></div>
  <div class="mt-3 h-9 w-full rounded-full bg-stone-300 dark:bg-stone-600"></div>
</div>
@elseif($variant === 'food')
<div class="block animate-pulse overflow-hidden rounded-lg bg-white shadow-sm dark:bg-stone-900" aria-hidden="true">
  <div class="relative aspect-[4/3] bg-stone-100 dark:bg-stone-800">
    <div class="absolute inset-x-0 bottom-0 flex items-center justify-between px-3 py-1.5">
      <div class="flex -space-x-2"><div class="size-7 rounded-full bg-stone-300 dark:bg-stone-600"></div><div class="size-7 rounded-full bg-stone-300 dark:bg-stone-600"></div><div class="size-7 rounded-full bg-stone-300 dark:bg-stone-600"></div></div>
      <div class="h-3 w-16 rounded-full bg-stone-300 dark:bg-stone-600"></div>
    </div>
  </div>
  <div class="space-y-2 p-3">
    <div class="h-2.5 w-1/2 rounded-full bg-stone-200 dark:bg-stone-700"></div>
    <div class="h-3.5 w-full rounded-full bg-stone-200 dark:bg-stone-700"></div>
    <div class="mt-1 h-5 w-2/3 rounded-md bg-stone-200 dark:bg-stone-700"></div>
    <div class="mt-2 h-7 w-32 rounded-md bg-stone-300 dark:bg-stone-600"></div>
  </div>
</div>
@elseif($variant === 'electronics')
<div class="block animate-pulse overflow-hidden rounded-lg bg-white shadow-sm dark:bg-stone-900" aria-hidden="true">
  <div class="aspect-[16/10] bg-stone-100 dark:bg-stone-800"></div>
  <div class="space-y-2 p-3">
    <div class="h-2.5 w-2/3 rounded-full bg-stone-200 dark:bg-stone-700"></div>
    <div class="h-3.5 w-full rounded-full bg-stone-200 dark:bg-stone-700"></div>
    <div class="h-3 w-3/4 rounded-full bg-stone-200 dark:bg-stone-700"></div>
    <div class="mt-1 h-5 w-1/2 rounded-md bg-stone-200 dark:bg-stone-700"></div>
    <div class="mt-2 h-1.5 w-full rounded-full bg-stone-200 dark:bg-stone-700"></div>
    <div class="mt-2 h-3 w-24 rounded-full bg-stone-200 dark:bg-stone-700"></div>
  </div>
</div>
@elseif($variant === 'spotlight')
<div class="grid animate-pulse overflow-hidden rounded-lg bg-white shadow-sm md:grid-cols-2 dark:bg-stone-900" aria-hidden="true">
  <div class="min-h-52 bg-stone-200 md:min-h-64 dark:bg-stone-700"></div>
  <div class="space-y-3 p-5 md:p-6">
    <div class="h-3 w-1/3 rounded-full bg-stone-200 dark:bg-stone-700"></div>
    <div class="h-5 w-full rounded-full bg-stone-200 dark:bg-stone-700"></div>
    <div class="h-5 w-2/3 rounded-full bg-stone-200 dark:bg-stone-700"></div>
    <div class="h-6 w-1/2 rounded-md bg-stone-200 dark:bg-stone-700"></div>
    <div class="h-1.5 w-full rounded-full bg-stone-200 dark:bg-stone-700"></div>
    <div class="flex gap-2 pt-1"><div class="h-10 flex-1 rounded-lg bg-stone-300 dark:bg-stone-600"></div><div class="h-10 w-20 rounded-lg bg-stone-200 dark:bg-stone-700"></div></div>
  </div>
</div>
@else
<div class="block animate-pulse overflow-hidden rounded-lg bg-white shadow-sm dark:bg-stone-900" aria-hidden="true">
  <div class="aspect-[4/3] bg-stone-100 dark:bg-stone-800"></div>
  <div class="space-y-2 p-3">
    <div class="h-2.5 w-1/2 rounded-full bg-stone-200 dark:bg-stone-700"></div>
    <div class="h-3.5 w-full rounded-full bg-stone-200 dark:bg-stone-700"></div>
    <div class="h-3.5 w-2/3 rounded-full bg-stone-200 dark:bg-stone-700"></div>
    <div class="mt-2 flex items-baseline gap-2"><div class="h-6 w-1/3 rounded-md bg-stone-200 dark:bg-stone-700"></div><div class="h-4 w-1/4 rounded-md bg-stone-200 dark:bg-stone-700"></div></div>
    <div class="mt-2 space-y-1.5"><div class="h-3 w-2/3 rounded-full bg-stone-200 dark:bg-stone-700"></div><div class="h-1.5 w-full rounded-full bg-stone-200 dark:bg-stone-700"></div></div>
  </div>
</div>
@endif
