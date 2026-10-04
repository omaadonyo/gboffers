<div>
  <section class="overflow-hidden rounded-lg bg-white px-5 py-8 shadow-sm md:py-10 dark:bg-stone-900">
    <p class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-widest text-brand-700 dark:text-brand-300"><span class="size-2 rounded-full bg-brand-600 dark:bg-brand-400"></span> Group buying marketplace · Uganda</p>
    <h1 class="mt-2 max-w-xl text-2xl font-bold leading-tight tracking-tight md:text-3xl">Buy together. Pay less.</h1>
    <p class="mt-2 max-w-xl text-sm text-stone-600 dark:text-stone-400">Find people buying what you want and unlock better prices. Join a group, invite friends on WhatsApp, and pay the merchant directly.</p>
    <form action="/explore" method="get" class="mt-4 flex max-w-xl gap-2" role="search">
      <input type="search" name="q" placeholder="What are you looking for?" aria-label="Search" class="h-11 flex-1 rounded-lg border border-stone-300 px-3 text-sm outline-none focus:border-brand-600 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-100 dark:focus:border-brand-400">
      <button class="h-11 rounded-lg bg-brand-600 px-5 text-sm font-semibold text-white hover:bg-brand-700 dark:bg-brand-500 dark:hover:bg-brand-400">Search</button>
    </form>
    <div class="mt-5 flex flex-wrap gap-2">
      @foreach($cats as $c)<a href="/explore?category={{ $c->slug }}" class="inline-flex items-center gap-1.5 rounded-full bg-stone-100 px-3.5 py-1.5 text-xs font-medium text-stone-700 transition hover:bg-stone-200 dark:bg-stone-800 dark:text-stone-200 dark:hover:bg-stone-700"><flux:icon :name="App\Support\CategoryStyle::iconFor($c)" class="size-3.5 text-brand-600 dark:text-white" />{{ $c->name }}</a>@endforeach
    </div>
  </section>

  <section class="mt-8">
    <div class="mb-3 flex items-baseline justify-between"><h2 class="text-base font-bold">Shop by category</h2><a href="/explore" class="inline-flex items-center gap-1 rounded-full bg-stone-900 py-1.5 pl-3 pr-2.5 text-xs font-semibold text-white transition hover:bg-stone-700 dark:bg-white dark:text-stone-900 dark:hover:bg-stone-200">View all <flux:icon.arrow-right class="size-3.5" /></a></div>
    <div class="flex flex-wrap gap-2">
      @foreach($cats as $c)
        <a href="/explore?category={{ $c->slug }}" class="inline-flex items-center gap-2 rounded-full bg-stone-100 py-2 pl-2.5 pr-4 transition hover:bg-stone-200 dark:bg-stone-800 dark:hover:bg-stone-700">
          <flux:icon :name="App\Support\CategoryStyle::iconFor($c)" class="size-4 text-brand-600 dark:text-white" />
          <span class="text-xs font-semibold text-stone-700 dark:text-stone-200">{{ $c->name }}</span>
        </a>
      @endforeach
    </div>
  </section>

  <section class="mt-8">
    <div class="mb-3 flex items-baseline justify-between"><h2 class="text-base font-bold">Groups forming now</h2><a href="/explore" class="inline-flex items-center gap-1 rounded-full bg-stone-900 py-1.5 pl-3 pr-2.5 text-xs font-semibold text-white transition hover:bg-stone-700 dark:bg-white dark:text-stone-900 dark:hover:bg-stone-200">View all <flux:icon.arrow-right class="size-3.5" /></a></div>
    @if($forming->isEmpty())<x-gb.empty-state title="No groups yet" body="Find something you want and start one with your friends." action="Explore offers" action-url="/explore" />@endif
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6">@foreach($forming as $o)@if(($o->category->slug ?? '') === 'food')<x-gb.offer-card-food :offer="$o" />@elseif(str_contains($o->category->slug ?? '', 'electro'))<x-gb.offer-card-electronics :offer="$o" />@else<x-gb.offer-card-default :offer="$o" />@endif@endforeach</div>
  </section>

  <section class="mt-8">
    <div class="mb-3 flex items-baseline justify-between"><h2 class="text-base font-bold">Ending soon</h2><a href="/explore?sort=ending" class="inline-flex items-center gap-1 rounded-full bg-stone-900 py-1.5 pl-3 pr-2.5 text-xs font-semibold text-white transition hover:bg-stone-700 dark:bg-white dark:text-stone-900 dark:hover:bg-stone-200">View all <flux:icon.arrow-right class="size-3.5" /></a></div>
    <div class="grid gap-2 md:grid-cols-2">@foreach($ending as $o)<x-gb.offer-row :offer="$o" />@endforeach</div>
  </section>

  @if($spotlight->isNotEmpty())
  <section class="mt-8">
    <div class="mb-3 flex items-baseline justify-between"><h2 class="text-base font-bold">Spotlight deals</h2><a href="/explore" class="inline-flex items-center gap-1 rounded-full bg-stone-900 py-1.5 pl-3 pr-2.5 text-xs font-semibold text-white transition hover:bg-stone-700 dark:bg-white dark:text-stone-900 dark:hover:bg-stone-200">View all <flux:icon.arrow-right class="size-3.5" /></a></div>
    <div class="grid gap-3">@foreach($spotlight as $i => $o)<x-gb.offer-spotlight :offer="$o" :flip="$i % 2 === 1" />@endforeach</div>
  </section>
  @endif

  @if($dealOfDay)
  <section class="mt-8 overflow-hidden rounded-lg bg-brand-600 text-white shadow-sm dark:bg-brand-500">
    <div class="grid items-center gap-4 p-5 md:grid-cols-2 md:p-8">
      <div>
        <p class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-white/80"><flux:icon.fire class="size-4" /> Deal of the day</p>
        <h2 class="mt-1.5 text-xl font-bold leading-tight tracking-tight md:text-2xl">{{ $dealOfDay->title }}</h2>
        <p class="mt-1 text-sm text-white/80">{{ $dealOfDay->merchant->trading_name ?? $dealOfDay->merchant->business_name }} · {{ $dealOfDay->confirmed_count }}/{{ $dealOfDay->gang_target }} joined</p>
        <div class="mt-2 flex items-center gap-2 text-white">
          <span class="text-xs font-semibold uppercase tracking-widest text-white/70">Ends in</span>
          <x-gb.countdown :ends-at="$dealOfDay->ends_at?->toISOString()" :compact="true" />
        </div>
        <div class="mt-3 flex items-baseline gap-2">
          <span class="text-2xl font-bold tracking-tight">{{ App\Support\Money::formatUgx(app(App\Services\PricingService::class)->priceFor($dealOfDay, $dealOfDay->confirmed_count)) }}</span>
          <span class="text-sm text-white/70 line-through">{{ App\Support\Money::formatUgx($dealOfDay->normal_price) }}</span>
        </div>
        <a href="/offers/{{ $dealOfDay->slug }}" class="mt-4 inline-flex h-10 items-center gap-1.5 rounded-lg bg-white px-5 text-sm font-bold text-brand-700 hover:bg-brand-50">Grab this deal <flux:icon.arrow-right class="size-4" /></a>
      </div>
      <div class="hidden items-center justify-center md:flex" aria-hidden="true">
        <flux:icon :name="App\Support\CategoryStyle::iconFor($dealOfDay->category)" class="size-32 text-white/30" />
      </div>
    </div>
  </section>
  @endif

  <section class="mt-8" x-data="{ atStart: true, atEnd: false, page() { return $refs.track.clientWidth * 0.8; }, check() { const el = $refs.track; this.atStart = el.scrollLeft <= 4; this.atEnd = el.scrollLeft + el.clientWidth >= el.scrollWidth - 4; } }" x-init="check()">
    <div class="mb-3 flex items-center justify-between gap-2">
      <h2 class="text-base font-bold">Trending now</h2>
      <div class="flex items-center gap-1.5">
        <a href="/explore?sort=popular" class="mr-1 inline-flex items-center gap-1 rounded-full bg-stone-900 py-1.5 pl-3 pr-2.5 text-xs font-semibold text-white transition hover:bg-stone-700 dark:bg-white dark:text-stone-900 dark:hover:bg-stone-200">View all <flux:icon.arrow-right class="size-3.5" /></a>
        <button type="button" x-on:click="$refs.track.scrollBy({ left: -page(), behavior: 'smooth' })" :disabled="atStart" aria-label="Previous products" class="grid size-8 place-items-center rounded-full bg-stone-100 text-stone-700 transition hover:bg-stone-200 disabled:opacity-40 dark:bg-stone-800 dark:text-stone-200 dark:hover:bg-stone-700"><flux:icon.chevron-left class="size-4" /></button>
        <button type="button" x-on:click="$refs.track.scrollBy({ left: page(), behavior: 'smooth' })" :disabled="atEnd" aria-label="Next products" class="grid size-8 place-items-center rounded-full bg-stone-100 text-stone-700 transition hover:bg-stone-200 disabled:opacity-40 dark:bg-stone-800 dark:text-stone-200 dark:hover:bg-stone-700"><flux:icon.chevron-right class="size-4" /></button>
      </div>
    </div>
    <div x-ref="track" x-on:scroll.debounce.100ms="check()" class="flex snap-x snap-mandatory gap-3 overflow-x-auto pb-2 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
      @foreach($trending as $o)<div class="w-44 shrink-0 snap-start md:w-52">@if(($o->category->slug ?? '') === 'food')<x-gb.offer-card-food :offer="$o" />@elseif(str_contains($o->category->slug ?? '', 'electro'))<x-gb.offer-card-electronics :offer="$o" />@else<x-gb.offer-card-default :offer="$o" />@endif</div>@endforeach
    </div>
  </section>
</div>
