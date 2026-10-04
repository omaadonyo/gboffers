<x-layouts::app :title="__('Dashboard')">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">Hello, {{ auth()->user()->displayName() }}</h1>
        <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Your group-buying home — track everything from here.</p>
        <div class="mt-4 grid gap-2.5 sm:grid-cols-2">
            @foreach([
                ['My groups', 'Discover and track your groups', 'users', '/groups'],
                ['Orders', 'Payments and fulfillment', 'package', '/orders'],
                ['GBPass wallet', 'Passes ready to redeem', 'ticket', '/wallet'],
                ['My stats', 'Spending, savings and activity', 'chart-bar', '/analytics'],
                ['Wanted board', 'Post what you are looking for', 'megaphone', '/wanted'],
                ['Profile', 'Account and security', 'circle-user-round', '/profile'],
            ] as [$t, $s, $i, $h])
                <a href="{{ $h }}" wire:navigate class="flex items-center gap-3 rounded-lg border border-zinc-200 bg-white p-4 transition hover:border-zinc-300 hover:shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600">
                    <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300"><flux:icon :name="$i" class="size-5" /></span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-bold text-zinc-900 dark:text-white">{{ $t }}</span>
                        <span class="block truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $s }}</span>
                    </span>
                    <flux:icon.chevron-right class="size-4 shrink-0 text-zinc-400" />
                </a>
            @endforeach
        </div>
        @if(auth()->user()?->isMerchant() || auth()->user()?->isAdmin())
            <div class="mt-3 flex flex-wrap gap-2">
                @if(auth()->user()?->isMerchant() || auth()->user()?->isAdmin())
                    <a href="{{ route('merchant.dashboard') }}" wire:navigate class="inline-flex h-10 items-center gap-1.5 rounded-full bg-zinc-900 px-4 text-sm font-semibold text-white hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200"><flux:icon.store class="size-4" /> Merchant dashboard</a>
                @endif
                @if(auth()->user()?->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" wire:navigate class="inline-flex h-10 items-center gap-1.5 rounded-full border border-zinc-300 px-4 text-sm font-semibold hover:bg-zinc-100 dark:border-zinc-600 dark:hover:bg-zinc-800"><flux:icon.shield-check class="size-4" /> Admin console</a>
                @endif
            </div>
        @endif
    </div>
</x-layouts::app>
