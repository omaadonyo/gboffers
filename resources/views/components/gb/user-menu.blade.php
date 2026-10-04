<div x-data="{ open: false }" x-on:keydown.escape.window="open = false" class="relative">
    <button
        type="button"
        x-on:click="open = !open"
        aria-label="Account menu"
        aria-haspopup="menu"
        class="flex items-center gap-1.5 rounded-lg p-1 transition hover:bg-stone-100 dark:hover:bg-stone-800"
    >
        <span class="grid size-8 place-items-center rounded-full bg-stone-900 text-xs font-bold text-white dark:bg-white dark:text-stone-900">{{ auth()->user()->initials() }}</span>
        <flux:icon.chevron-down class="size-3.5 text-stone-400" />
    </button>

    <div
        x-show="open"
        x-cloak
        x-on:click.outside="open = false"
        role="menu"
        class="absolute right-0 top-full z-50 mt-2 w-60 overflow-hidden rounded-lg border border-stone-200 bg-white py-1 shadow-xl dark:border-stone-700 dark:bg-stone-900"
    >
        <div class="flex items-center gap-2.5 px-3 py-2.5">
            <span class="grid size-9 shrink-0 place-items-center rounded-full bg-stone-900 text-xs font-bold text-white dark:bg-white dark:text-stone-900">{{ auth()->user()->initials() }}</span>
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-stone-900 dark:text-white">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-stone-500 dark:text-stone-400">{{ auth()->user()->email }}</p>
            </div>
        </div>
        <div class="my-1 border-t border-stone-100 dark:border-stone-800"></div>
        @php $u = auth()->user(); @endphp
        <a href="/groups" role="menuitem" class="flex items-center gap-2.5 px-3 py-2 text-sm text-stone-700 hover:bg-stone-50 dark:text-stone-200 dark:hover:bg-stone-800"><flux:icon.users class="size-4 text-stone-400" /> My groups</a>
        <a href="/wallet" role="menuitem" class="flex items-center gap-2.5 px-3 py-2 text-sm text-stone-700 hover:bg-stone-50 dark:text-stone-200 dark:hover:bg-stone-800"><flux:icon.ticket class="size-4 text-stone-400" /> GBPass wallet</a>
        <a href="/orders" role="menuitem" class="flex items-center gap-2.5 px-3 py-2 text-sm text-stone-700 hover:bg-stone-50 dark:text-stone-200 dark:hover:bg-stone-800"><flux:icon.package class="size-4 text-stone-400" /> Orders</a>
        <a href="{{ route('analytics') }}" role="menuitem" class="flex items-center gap-2.5 px-3 py-2 text-sm text-stone-700 hover:bg-stone-50 dark:text-stone-200 dark:hover:bg-stone-800"><flux:icon.chart-bar class="size-4 text-stone-400" /> My stats</a>
        @if($u->isMerchant() || $u->isAdmin())
            <a href="{{ route('merchant.dashboard') }}" role="menuitem" class="flex items-center gap-2.5 px-3 py-2 text-sm text-stone-700 hover:bg-stone-50 dark:text-stone-200 dark:hover:bg-stone-800"><flux:icon.store class="size-4 text-stone-400" /> Merchant dashboard</a>
        @endif
        @if($u->isAdmin())
            <a href="{{ route('admin.dashboard') }}" role="menuitem" class="flex items-center gap-2.5 px-3 py-2 text-sm text-stone-700 hover:bg-stone-50 dark:text-stone-200 dark:hover:bg-stone-800"><flux:icon.shield-check class="size-4 text-stone-400" /> Admin console</a>
        @endif
        <div class="my-1 border-t border-stone-100 dark:border-stone-800"></div>
        <a href="{{ route('profile.edit') }}" role="menuitem" class="flex items-center gap-2.5 px-3 py-2 text-sm text-stone-700 hover:bg-stone-50 dark:text-stone-200 dark:hover:bg-stone-800"><flux:icon.settings class="size-4 text-stone-400" /> Manage profile</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" role="menuitem" class="flex w-full items-center gap-2.5 px-3 py-2 text-left text-sm font-medium text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40"><flux:icon.log-out class="size-4" /> Log out</button>
        </form>
    </div>
</div>
