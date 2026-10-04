<div>
  <section class="overflow-hidden rounded-lg bg-white shadow-sm dark:bg-stone-900">
    <div class="h-36 bg-gradient-to-r from-brand-800 via-brand-600 to-brand-800 md:h-44 dark:from-brand-900 dark:via-brand-700 dark:to-brand-900"></div>
    <div class="px-5 pb-5">
      <div class="-mt-10 flex flex-wrap items-end justify-between gap-3">
        <div class="flex items-end gap-3">
          <span class="grid size-20 place-items-center rounded-xl border-4 border-white bg-stone-900 text-2xl font-bold text-white shadow-md dark:border-stone-900 dark:bg-white dark:text-stone-900">{{ strtoupper(substr($merchant->trading_name ?? $merchant->business_name, 0, 2)) }}</span>
          <div class="pb-0.5">
            <h1 class="flex items-center gap-1.5 text-xl font-bold tracking-tight">{{ $merchant->trading_name ?? $merchant->business_name }} <x-gb.merchant-badge :merchant="$merchant" :link="false" /></h1>
            <p class="text-sm text-stone-500 dark:text-stone-400">{{ '@'.$merchant->slug }}@if($merchant->location) · {{ $merchant->location }}@endif</p>
          </div>
        </div>
        <a href="{{ $share }}" wire:click="trackShare" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 rounded-lg border border-stone-300 px-3 py-1.5 text-sm font-medium hover:bg-stone-100 dark:border-stone-700 dark:hover:bg-stone-800"><flux:icon.share-2 class="size-4" /> Share shop</a>
      </div>
      <div class="mt-4 grid grid-cols-3 gap-2 text-center sm:max-w-md">
        <div class="rounded-lg bg-stone-50 px-2 py-2.5 dark:bg-stone-800/70"><p class="text-base font-bold">{{ number_format((float) $merchant->rating_avg, 1) }}</p><p class="text-[11px] text-stone-500 dark:text-stone-400">Rating</p></div>
        <div class="rounded-lg bg-stone-50 px-2 py-2.5 dark:bg-stone-800/70"><p class="text-base font-bold">{{ $completedGangs }}</p><p class="text-[11px] text-stone-500 dark:text-stone-400">Groups completed</p></div>
        <div class="rounded-lg bg-stone-50 px-2 py-2.5 dark:bg-stone-800/70"><p class="text-base font-bold">{{ $buyers }}</p><p class="text-[11px] text-stone-500 dark:text-stone-400">Happy buyers</p></div>
      </div>
      @if($merchant->description)<p class="mt-3 max-w-2xl text-sm text-stone-600 dark:text-stone-300">{{ $merchant->description }}</p>@endif
    </div>
  </section>

  <section class="mt-6">
    <div class="mb-3 flex items-baseline justify-between">
      <h2 class="text-base font-bold">Live offers</h2>
      <span class="text-xs text-stone-500 dark:text-stone-400">{{ $offers->total() }} active</span>
    </div>
    @if($offers->isEmpty())
      <x-gb.empty-state title="No live offers" body="This merchant has nothing on sale right now. Check back soon." icon="store" />
    @else
      <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6">@foreach($offers as $o)<x-gb.offer-card-default :offer="$o" />@endforeach</div>
      <div class="mt-4">{{ $offers->links() }}</div>
    @endif
  </section>

  <section class="mt-6 grid gap-3 md:grid-cols-3">
    <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-stone-900">
      <p class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-stone-400"><flux:icon.truck class="size-3.5" /> Fulfillment</p>
      <p class="mt-1.5 text-sm text-stone-600 dark:text-stone-300">{{ $merchant->fulfillment_rate ? $merchant->fulfillment_rate.'% on-time · ' : '' }}{{ $merchant->pickup_location ?? $merchant->location ?? 'See offer terms' }}</p>
    </div>
    <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-stone-900">
      <p class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-stone-400"><flux:icon.undo-2 class="size-3.5" /> Returns</p>
      <p class="mt-1.5 text-sm text-stone-600 dark:text-stone-300">{{ $merchant->return_policy ?? 'Contact the merchant within 24h of redemption for issues.' }}</p>
    </div>
    <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-stone-900">
      <p class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-stone-400"><flux:icon.phone class="size-3.5" /> Contact</p>
      <p class="mt-1.5 text-sm text-stone-600 dark:text-stone-300">{{ $merchant->phone ?? $merchant->email ?? 'Via GBOffers support' }}</p>
    </div>
  </div>
</div>
