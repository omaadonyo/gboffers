<x-layouts::app :title="__('Dashboard')">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">Hello, {{ auth()->user()->displayName() }} 👋</h1>
        <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Everything you need — groups, orders, passes and stats.</p>
    </div>
    <div class="mt-4 grid gap-2.5 sm:grid-cols-2">
        @foreach([
            ['My groups', 'Track active and completed groups', 'users', '/groups'],
            ['Orders', 'Payments and fulfillment per order', 'shopping-cart', '/orders'],
            ['GBPass wallet', 'QR passes ready to redeem', 'ticket', '/wallet'],
            ['My stats', 'Spending, savings and activity', 'chart-bar', '/analytics'],
            ['Wanted board', 'Post what you are looking for', 'megaphone', '/wanted'],
            ['Profile', 'Manage your account', 'circle-user-round', '/profile'],
        ] as [$t, $s, $i, $h])
            <a href="{{ $h }}" wire:navigate class="group flex items-center gap-3 rounded-xl border border-zinc-200 bg-white p-4 transition hover:border-zinc-300 hover:shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600">
                <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300"><flux:icon :name="$i" class="size-5" /></span>
                <span class="min-w-0 flex-1">
                    <span class="block text-sm font-bold text-zinc-900 dark:text-white">{{ $t }}</span>
                    <span class="block truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $s }}</span>
                </span>
                <flux:icon.chevron-right class="size-4 shrink-0 text-zinc-400 transition group-hover:translate-x-0.5" />
            </a>
        @endforeach
    </div>
</x-layouts::app>
