<x-layouts.storefront title="Verify GBPass">
  <div class="mx-auto max-w-md border bg-white dark:bg-stone-900 p-6 text-center">
    <p class="text-xs uppercase tracking-widest text-stone-500 dark:text-stone-400">GBPass {{ $p->token }}</p>
    <h1 class="mt-1 font-bold">{{ $p->offer->title }}</h1>
    <p class="text-sm text-stone-500 dark:text-stone-400">{{ $p->merchant->business_name }} · Status: {{ $p->status }}</p>
  </div>
</x-layouts.storefront>
