<header class="sticky top-0 z-40 bg-white/90 shadow-sm backdrop-blur dark:bg-stone-950/90">
    <div class="mx-auto flex h-14 w-full max-w-full items-center gap-2 px-4 sm:gap-3 md:px-6">
        {{-- Brand --}}
        <a href="/" class="flex shrink-0 items-center gap-2" aria-label="GBOffers home">
            <span class="grid size-7 place-items-center rounded-md bg-brand-600 text-xs font-bold text-white dark:bg-brand-500">GB</span>
            <span class="hidden text-[15px] font-bold tracking-tight text-stone-900 min-[400px]:block dark:text-white">GBOffers</span>
        </a>

        {{-- Primary nav --}}
        <nav class="hidden shrink-0 items-center gap-0.5 text-sm md:flex" aria-label="Primary">
            @foreach([['Explore', '/explore', 'explore*'], ['Groups', '/groups', 'groups*'], ['Wanted', '/wanted', 'wanted*']] as [$label, $href, $pattern])
                <a href="{{ $href }}" @if(request()->is($pattern)) aria-current="page" @endif class="rounded-lg px-2.5 py-1.5 font-medium {{ request()->is($pattern) ? 'bg-stone-100 text-stone-900 dark:bg-stone-800 dark:text-white' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-stone-300 dark:hover:bg-stone-800 dark:hover:text-white' }}">{{ $label }}</a>
            @endforeach
        </nav>

        {{-- Search --}}
        {{-- Search · centered --}}
        <div class="hidden min-w-0 flex-1 justify-center px-2 sm:flex">
            <div class="w-full max-w-md">
                @livewire('global-search')
            </div>
        </div>

        {{-- Actions --}}
        <div class="ml-auto flex shrink-0 items-center gap-0.5 sm:ml-0">
            <x-gb.appearance-toggle />
            @auth
                @livewire('notification-bell')
                <span class="mx-1 hidden h-5 w-px bg-stone-200 sm:block dark:bg-stone-700"></span>
                <a href="/wallet" class="hidden items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-sm font-medium text-stone-600 hover:bg-stone-100 hover:text-stone-900 sm:flex dark:text-stone-300 dark:hover:bg-stone-800 dark:hover:text-white"><flux:icon.ticket class="size-4" /> GBPasses</a>
                <x-gb.user-menu />
            @else
                <span class="mx-1 hidden h-5 w-px bg-stone-200 sm:block dark:bg-stone-700"></span>
                <a href="{{ route('login') }}" class="rounded-full px-3.5 py-1.5 text-sm font-semibold text-stone-700 transition hover:bg-stone-100 hover:text-stone-900 dark:text-stone-200 dark:hover:bg-stone-800 dark:hover:text-white">Log in</a>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 rounded-full bg-brand-600 py-1.5 pl-4 pr-3 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 dark:bg-brand-500 dark:hover:bg-brand-400">Join <flux:icon.arrow-right class="size-3.5" /></a>
            @endauth
        </div>
    </div>
    <div class="border-t border-stone-100 px-4 py-2 sm:hidden dark:border-stone-800">
        @livewire('global-search')
    </div>
</header>
