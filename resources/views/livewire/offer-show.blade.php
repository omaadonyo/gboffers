<div>
  <nav class="flex items-center gap-1.5 text-xs text-stone-500 dark:text-stone-400" aria-label="Breadcrumb">
    <a href="/explore" class="hover:underline">Explore</a><span>/</span>
    @if($offer->category)<a href="{{ route('explore', ['category' => $offer->category->slug]) }}" class="hover:underline">{{ $offer->category->name }}</a><span>/</span>@endif
    <span class="truncate font-medium text-stone-700 dark:text-stone-200">{{ $offer->title }}</span>
  </nav>

  <div class="mt-3 grid gap-5 lg:grid-cols-5">
    <div class="lg:col-span-3">
      <div class="relative overflow-hidden rounded-xl bg-stone-100 shadow-sm dark:bg-stone-800">
        <div class="aspect-[16/10]">
          @if($offer->image_path)<img src="{{ $offer->image_path }}" alt="{{ $offer->title }}" class="size-full object-cover">
          @else<div class="flex size-full flex-col items-center justify-center gap-2"><flux:icon :name="App\Support\CategoryStyle::iconFor($offer->category)" class="size-16 text-brand-600/50 dark:text-white/50" /></div>@endif
        </div>
        @if($offer->featured)<span class="absolute left-3 top-3 inline-flex items-center gap-1 rounded-full bg-brand-600 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-white shadow"><flux:icon.sparkles class="size-3.5" /> Featured</span>@endif
        @if($offer->ends_at)<span class="absolute bottom-3 right-3 rounded-full bg-black/60 px-2.5 py-1 text-[11px] font-semibold text-white backdrop-blur">Ends {{ $offer->ends_at->format('d M') }}</span>@endif
      </div>

      <div class="mt-4 rounded-xl bg-white p-4 shadow-sm sm:p-5 dark:bg-stone-900">
        <h2 class="text-sm font-bold text-stone-900 dark:text-stone-100">About this deal</h2>
        <p class="mt-1.5 text-sm leading-relaxed text-stone-600 dark:text-stone-300">{{ $offer->description ?? 'Group deal. Group terms apply.' }}</p>
        <dl class="mt-4 grid gap-3 text-xs sm:grid-cols-3">
          <div class="rounded-lg bg-stone-50 p-3 dark:bg-stone-800/70"><dt class="font-bold uppercase tracking-wide text-stone-400 dark:text-stone-500">Fulfillment</dt><dd class="mt-1 text-stone-700 dark:text-stone-200">{{ ucfirst($offer->fulfillment_method ?? 'pickup') }}@if($offer->pickup_location) · {{ $offer->pickup_location }}@endif</dd></div>
          <div class="rounded-lg bg-stone-50 p-3 dark:bg-stone-800/70"><dt class="font-bold uppercase tracking-wide text-stone-400 dark:text-stone-500">Cancellation</dt><dd class="mt-1 text-stone-700 dark:text-stone-200">{{ $offer->cancellation_policy ?? 'See merchant terms.' }}</dd></div>
          <div class="rounded-lg bg-stone-50 p-3 dark:bg-stone-800/70"><dt class="font-bold uppercase tracking-wide text-stone-400 dark:text-stone-500">Terms</dt><dd class="mt-1 text-stone-700 dark:text-stone-200">{{ $offer->terms ?? '—' }}</dd></div>
        </dl>
      </div>

      @if($offer->priceTiers->isNotEmpty())
        <div class="mt-4 rounded-xl bg-white p-4 shadow-sm sm:p-5 dark:bg-stone-900">
          <h2 class="text-sm font-bold text-stone-900 dark:text-stone-100">Price tiers — more buyers, lower price</h2>
          <div class="mt-2 divide-y divide-stone-100 dark:divide-stone-800">
            @foreach($offer->priceTiers as $t)
              <div class="flex items-center justify-between py-2 text-sm @if($gang && $gang->confirmed_count >= $t->min_qty) font-bold @endif">
                <span class="text-stone-600 dark:text-stone-300">{{ $t->min_qty }}+ buyers @if($gang && $gang->confirmed_count >= $t->min_qty)<span class="ml-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-bold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">unlocked</span>@endif</span>
                <span class="font-bold text-stone-900 dark:text-white">{{ App\Support\Money::formatUgx($t->price) }}</span>
              </div>
            @endforeach
          </div>
        </div>
      @endif

      <section class="mt-4 rounded-xl bg-white p-4 shadow-sm sm:p-5 dark:bg-stone-900">
        <h2 class="flex items-center gap-2 text-sm font-bold text-stone-900 dark:text-stone-100">
          Confirmed buyers
          <span class="rounded-full bg-stone-100 px-2 py-0.5 text-[11px] font-bold text-stone-600 dark:bg-stone-800 dark:text-stone-300">{{ $confirmedBuyers->count() }}</span>
        </h2>
        @if($confirmedBuyers->isNotEmpty())
          <div class="mt-3"><x-gb.avatar-stack :users="$confirmedBuyers" :limit="12" /></div>
          <details class="mt-3 text-sm">
            <summary class="cursor-pointer text-xs font-bold text-brand-700 hover:underline dark:text-brand-300">View all {{ $confirmedBuyers->count() }} buyers</summary>
            <ul class="mt-2 grid gap-1 sm:grid-cols-2">
              @foreach($confirmedBuyers as $b)<li class="rounded-lg bg-stone-50 px-3 py-1.5 text-xs text-stone-700 dark:bg-stone-800 dark:text-stone-200">{{ $b['name'] }}</li>@endforeach
            </ul>
          </details>
        @else
          <p class="mt-2 text-xs text-stone-500 dark:text-stone-400">No confirmed buyers yet — join first and lead the group.</p>
        @endif
        <p class="mt-2 text-xs text-stone-500 dark:text-stone-400">{{ $gang->interested_count ?? 0 }} interested · only confirmed buyers count</p>
      </section>
    </div>

    <div class="lg:col-span-2">
      <div class="rounded-xl bg-white p-4 shadow-sm sm:p-5 md:sticky md:top-20 dark:bg-stone-900">
        <p class="flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wide text-stone-500 dark:text-stone-400"><flux:icon :name="App\Support\CategoryStyle::iconFor($offer->category)" class="size-3.5 text-brand-600 dark:text-white" /> {{ $offer->category->name ?? 'Offer' }}</p>
        <h1 class="mt-1 text-xl font-bold leading-snug tracking-tight">{{ $offer->title }}</h1>
        <p class="mt-1.5 flex items-center gap-2 text-sm text-stone-600 dark:text-stone-400">
          <span>{{ $offer->merchant->trading_name ?? $offer->merchant->business_name }}</span>
          <x-gb.merchant-badge :merchant="$offer->merchant" />
        </p>
        <div class="mt-3 border-t border-stone-100 pt-3 dark:border-stone-800"><x-gb.price-breakdown :normal="$offer->normal_price" :gang="$price" /></div>

        <div class="mt-3 rounded-lg bg-stone-50 p-3 dark:bg-stone-800/70">
          <div class="flex items-center justify-between gap-2">
            <p class="text-xs font-bold uppercase tracking-wide text-stone-500 dark:text-stone-400">Group price · {{ $gang->confirmed_count ?? 0 }}/{{ $gang->target ?? $offer->gang_target }} confirmed</p>
            <x-gb.countdown :ends-at="$offer->ends_at?->toISOString()" />
          </div>
          <div class="mt-2"><x-gb.gang-progress :confirmed="$gang->confirmed_count ?? 0" :target="$gang->target ?? $offer->gang_target" :next-price="$next['price'] ?? null" :ends-at="$offer->ends_at" /></div>
        </div>

        @if(count($groups) > 1)
          <div class="mt-3">
            <p class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wide text-stone-500 dark:text-stone-400"><flux:icon.users class="size-3.5 text-brand-600 dark:text-white" /> Discounts per group</p>
            <div class="mt-1.5 divide-y divide-stone-100 dark:divide-stone-800">
              @foreach($groups as $gg)
                <div class="flex items-center justify-between gap-2 py-1.5 text-sm">
                  <span class="text-stone-600 dark:text-stone-300">{{ $gg['label'] }} <span class="text-xs text-stone-400">{{ $gg['min_buyers'] }}+ buyers</span></span>
                  <span class="font-bold text-stone-900 dark:text-white">{{ App\Support\Money::formatUgx($gg['price']) }}</span>
                </div>
              @endforeach
            </div>
          </div>
        @endif

        @if($notice)<p class="mt-3 rounded-lg bg-amber-50 px-3 py-2 text-xs font-medium text-amber-900 dark:bg-amber-950/60 dark:text-amber-200" role="status">{{ $notice }}</p>@endif

        <div class="sticky bottom-16 mt-4 grid gap-2 md:static">
          @if($membership && in_array($membership->status, ['reserved','payment_pending','payment_confirmed','ready']))
            @if($membership->status === 'payment_pending' && $membership->order_id)
              <a href="/checkout/{{ $membership->order_id }}" class="inline-flex h-11 items-center justify-center gap-1.5 rounded-full bg-brand-600 text-sm font-bold text-white hover:bg-brand-700 dark:bg-brand-500 dark:hover:bg-brand-400">Continue to payment <flux:icon.arrow-right class="size-4" /></a>
            @else
              <button wire:click="$set('showJoin', true)" class="h-11 rounded-full bg-brand-600 text-sm font-bold text-white hover:bg-brand-700 dark:bg-brand-500 dark:hover:bg-brand-400">Confirm spot &amp; pay</button>
            @endif
            <p class="text-center text-[11px] text-emerald-700 dark:text-emerald-400">You're in this group · {{ str_replace('_', ' ', $membership->status) }}</p>
          @else
            <button wire:click="interested" wire:loading.attr="disabled" wire:target="interested" class="inline-flex h-11 items-center justify-center gap-1.5 rounded-full bg-brand-600 text-sm font-bold text-white hover:bg-brand-700 disabled:opacity-60 dark:bg-brand-500 dark:hover:bg-brand-400">
              <span wire:loading.remove wire:target="interested" class="inline-flex items-center gap-1.5"><flux:icon.megaphone class="size-4" /> I'm interested{{ $membership ? ' ✓' : '' }}</span>
              <span wire:loading wire:target="interested" class="inline-flex items-center gap-1.5"><flux:icon.loading class="size-4 animate-spin" /> Saving…</span>
            </button>
            <button wire:click="$set('showJoin', true)" class="h-11 rounded-full border border-stone-300 text-sm font-semibold hover:bg-stone-100 dark:border-stone-700 dark:hover:bg-stone-800">Confirm &amp; pay directly</button>
          @endif
          @if(isset($share))<a href="{{ $share }}" wire:click="trackShare" target="_blank" rel="noopener" class="inline-flex h-10 items-center justify-center gap-1.5 rounded-full text-xs font-semibold text-stone-500 hover:text-stone-900 dark:text-stone-400 dark:hover:text-white"><flux:icon.share-2 class="size-3.5" /> Invite friends on WhatsApp</a>@endif
        </div>

        <div class="mt-3 flex items-center gap-4 border-t border-stone-100 pt-3 text-[11px] text-stone-500 dark:border-stone-800 dark:text-stone-400">
          <span class="inline-flex items-center gap-1"><flux:icon.shield-check class="size-3.5" /> Buyer protection</span>
          <span class="inline-flex items-center gap-1"><flux:icon.ticket class="size-3.5" /> GBPass QR</span>
          <span class="inline-flex items-center gap-1"><flux:icon.banknotes class="size-3.5" /> Pay merchant directly</span>
        </div>
      </div>
    </div>
  </div>

  @if($showJoin)
  <div class="fixed inset-0 z-50 grid place-items-end bg-black/40 p-0 sm:place-items-center sm:p-4" role="dialog" aria-modal="true" aria-label="Join group">
    <div class="w-full max-w-md rounded-t-2xl bg-white p-5 shadow-xl sm:rounded-2xl dark:bg-stone-900">
      <h2 class="text-base font-bold">Join {{ $offer->title }} group</h2>
      <p class="mt-1 text-sm">Group price: <strong>{{ App\Support\Money::formatUgx($price) }}</strong></p>
      <p class="text-xs text-stone-500 dark:text-stone-400">{{ $gang->confirmed_count }}/{{ $gang->target }} confirmed · reservation held 15 minutes · unpaid spots expire automatically</p>
      <div class="mt-3">
        <p class="text-xs font-bold uppercase tracking-wide text-stone-500 dark:text-stone-400">Pay with</p>
        <div class="mt-1.5 grid grid-cols-2 gap-1.5">
          @foreach(config('gboffers.payments.methods', ['merchant_direct']) as $pm)
            @php $pme = App\Enums\PaymentMethod::tryFrom($pm); @endphp
            @continue(!$pme)
            <button type="button" wire:click="$set('method', '{{ $pm }}')" class="rounded-lg border px-2.5 py-2 text-left text-xs font-semibold transition {{ $method === $pm ? 'border-brand-600 bg-brand-50 text-brand-700 dark:border-brand-400 dark:bg-brand-950 dark:text-brand-300' : 'border-stone-300 dark:border-stone-700' }}">
              {{ $pme->label() }}
              <span class="block text-[11px] font-normal text-stone-500 dark:text-stone-400">{{ $pme->hint() }}</span>
            </button>
          @endforeach
        </div>
      </div>
      <label class="mt-3 flex items-start gap-2 text-xs"><input type="checkbox" wire:model="ack" class="mt-0.5"> I understand payment goes directly to the merchant and GBOffers does not hold funds. Cancellation: {{ $offer->cancellation_policy ?? 'see terms' }}.</label>
      <div class="mt-3 flex gap-2">
        <button wire:click="$set('showJoin', false)" class="h-10 flex-1 rounded-full border border-stone-300 text-sm dark:border-stone-700">Cancel</button>
        <button wire:click="confirmJoin" @disabled(!$ack) wire:loading.attr="disabled" wire:target="confirmJoin" class="inline-flex h-10 flex-1 items-center justify-center gap-1.5 rounded-full bg-brand-600 text-sm font-bold text-white disabled:opacity-40 dark:bg-brand-500">
          <span wire:loading.remove wire:target="confirmJoin">Continue</span>
          <span wire:loading wire:target="confirmJoin" class="inline-flex items-center gap-1.5"><flux:icon.loading class="size-4 animate-spin" /> Reserving…</span>
        </button>
      </div>
    </div>
  </div>
  @endif
</div>
