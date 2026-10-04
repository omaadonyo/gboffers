<div
    x-data
    x-on:keydown.escape.window="$wire.close()"
    x-on:click.outside="$wire.close()"
    class="relative w-full"
    role="search"
>
    <div class="pointer-events-none absolute left-0 top-1/2 -translate-y-1/2 pl-3 text-stone-400 dark:text-stone-500">
        <flux:icon.search class="size-4" />
    </div>
    <input
        type="search"
        wire:model.live.debounce.300ms="q"
        wire:focus="reopen"
        placeholder="Search offers, groups, merchants…"
        aria-label="Search GBOffers"
        autocomplete="off"
        class="h-10 w-full rounded-full border-0 bg-stone-100 pl-10 pr-10 text-sm text-stone-900 outline-none placeholder:text-stone-400 focus:bg-white focus:ring-2 focus:ring-brand-600/30 dark:bg-stone-800 dark:text-stone-100 dark:placeholder:text-stone-500 dark:focus:bg-stone-900 dark:focus:ring-brand-400/40"
    />
    <div class="absolute right-0 top-1/2 flex -translate-y-1/2 items-center pr-2">
        <span wire:loading.delay wire:target="q" class="grid size-6 place-items-center text-brand-600 dark:text-brand-400" aria-label="Searching">
            <flux:icon.loading class="size-4 animate-spin" />
        </span>
        @if(trim($q) !== '')
            <button type="button" wire:click="clear" wire:loading.remove wire:target="q" aria-label="Clear search" class="grid size-6 place-items-center rounded-md text-stone-400 transition hover:bg-stone-200 hover:text-stone-700 dark:text-stone-500 dark:hover:bg-stone-700 dark:hover:text-stone-200">
                <flux:icon.x-mark class="size-4" />
            </button>
        @endif
    </div>

    @if($open && ($hasResults || $showPopular || $showEmpty))
    <div class="absolute inset-x-0 top-full z-50 mt-2 overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-xl dark:border-stone-700 dark:bg-stone-900">
        <div wire:loading.delay wire:target="q" class="space-y-2.5 px-3 py-3" aria-hidden="true">
            @for($i = 0; $i < 3; $i++)<div class="flex animate-pulse items-center gap-3"><div class="size-8 shrink-0 rounded-md bg-stone-200 dark:bg-stone-700"></div><div class="flex-1 space-y-1.5"><div class="h-3 w-3/4 rounded-full bg-stone-200 dark:bg-stone-700"></div><div class="h-2.5 w-1/2 rounded-full bg-stone-200 dark:bg-stone-700"></div></div></div>@endfor
        </div>
        <div wire:loading.remove wire:target="q">
        @if($showEmpty)
            <div class="px-4 py-5 text-center">
                <p class="text-sm font-medium text-stone-700 dark:text-stone-200">No matches for “{{ trim($q) }}”</p>
                <a href="{{ route('explore', ['q' => trim($q)]) }}" class="mt-1 inline-block text-sm font-medium text-stone-900 underline dark:text-white">Browse everything instead</a>
            </div>
        @else
            @if($offers->isNotEmpty())
                <p class="px-3 pb-1 pt-2 text-[11px] font-bold uppercase tracking-wider text-stone-400 dark:text-stone-500">Offers</p>
                @foreach($offers as $offer)
                    <a href="{{ route('offers.show', $offer->slug) }}" class="flex items-center gap-3 px-3 py-2 hover:bg-stone-50 dark:hover:bg-stone-800">
                        <span class="grid size-8 shrink-0 place-items-center rounded-md bg-brand-600 text-[10px] font-bold text-white dark:bg-brand-500">{{ strtoupper(substr($offer->merchant->trading_name ?? $offer->merchant->business_name ?? 'GB', 0, 2)) }}</span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-medium text-stone-900 dark:text-stone-100">{{ $offer->title }}</span>
                            <span class="block truncate text-xs text-stone-500 dark:text-stone-400">{{ $offer->merchant->trading_name ?? $offer->merchant->business_name }} · {{ $offer->gangs->where('status','forming')->count() }} group(s) forming</span>
                        </span>
                        <span class="shrink-0 text-xs font-bold text-stone-900 dark:text-white">{{ App\Support\Money::formatUgx($offer->normal_price) }}</span>
                    </a>
                @endforeach
            @endif
            @if($gangs->isNotEmpty())
                <p class="border-t border-stone-100 px-3 pb-1 pt-2 text-[11px] font-bold uppercase tracking-wider text-stone-400 dark:border-stone-800 dark:text-stone-500">Groups forming</p>
                @foreach($gangs as $gang)
                    <a href="{{ route('offers.show', $gang->offer->slug) }}" class="flex items-center gap-3 px-3 py-2 hover:bg-stone-50 dark:hover:bg-stone-800">
                        <flux:icon.users class="size-4 shrink-0 text-stone-400" />
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-medium text-stone-900 dark:text-stone-100">{{ $gang->offer->title }}</span>
                            <span class="mt-1 block h-1 overflow-hidden rounded-full bg-stone-200 dark:bg-stone-700"><span class="block h-full rounded-full bg-brand-600 dark:bg-brand-400" style="width: {{ $gang->progressPct() }}%"></span></span>
                        </span>
                        <span class="shrink-0 text-xs text-stone-500 dark:text-stone-400">{{ $gang->confirmed_count }}/{{ $gang->target }}</span>
                    </a>
                @endforeach
            @endif
            @if($merchants->isNotEmpty())
                <p class="border-t border-stone-100 px-3 pb-1 pt-2 text-[11px] font-bold uppercase tracking-wider text-stone-400 dark:border-stone-800 dark:text-stone-500">Merchants</p>
                @foreach($merchants as $merchant)
                    <a href="{{ route('merchants.show', $merchant->slug) }}" class="flex items-center gap-3 px-3 py-2 hover:bg-stone-50 dark:hover:bg-stone-800">
                        <flux:icon.store class="size-4 shrink-0 text-stone-400" />
                        <span class="min-w-0 flex-1 truncate text-sm font-medium text-stone-900 dark:text-stone-100">{{ $merchant->trading_name ?? $merchant->business_name }}</span>
                        @if($merchant->isVerified())
                            <flux:icon.badge-check class="size-4 shrink-0 text-emerald-600 dark:text-emerald-400" />
                        @endif
                    </a>
                @endforeach
            @endif
            @if($showPopular)
                <p class="flex items-center gap-1.5 px-3 pb-1 pt-2.5 text-[11px] font-bold uppercase tracking-wider text-stone-400 dark:text-stone-500"><flux:icon.fire class="size-3.5 text-brand-600 dark:text-brand-400" /> Popular right now</p>
                @foreach($popular as $offer)
                    <a href="{{ route('offers.show', $offer->slug) }}" class="flex items-center gap-3 px-3 py-2 hover:bg-stone-50 dark:hover:bg-stone-800">
                        <span class="grid size-8 shrink-0 place-items-center rounded-md bg-brand-600 text-[10px] font-bold text-white dark:bg-brand-500">{{ strtoupper(substr($offer->merchant->trading_name ?? $offer->merchant->business_name ?? 'GB', 0, 2)) }}</span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-medium text-stone-900 dark:text-stone-100">{{ $offer->title }}</span>
                            <span class="block truncate text-xs text-stone-500 dark:text-stone-400">{{ $offer->merchant->trading_name ?? $offer->merchant->business_name }} · {{ $offer->confirmed_count }} joined</span>
                        </span>
                        <span class="shrink-0 text-xs font-bold text-stone-900 dark:text-white">{{ App\Support\Money::formatUgx($offer->normal_price) }}</span>
                    </a>
                @endforeach
            @endif
            @if($hasResults)
            <a href="{{ route('explore', ['q' => trim($q)]) }}" class="block border-t border-stone-100 bg-stone-50 px-3 py-2 text-center text-xs font-bold text-stone-700 hover:bg-stone-100 dark:border-stone-800 dark:bg-stone-800/60 dark:text-stone-200 dark:hover:bg-stone-800">
                See all results for “{{ trim($q) }}”
            </a>
            @endif
        @endif
        </div>
    </div>
    @endif
</div>
