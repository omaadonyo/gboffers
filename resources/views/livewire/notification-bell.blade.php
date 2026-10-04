<div x-data="{ open: false }" x-on:keydown.escape.window="open = false" class="relative">
    <button
        type="button"
        x-on:click="open = !open"
        aria-label="Notifications{{ $count > 0 ? ', '.$count.' unread' : '' }}"
        class="relative grid size-9 place-items-center rounded-lg text-stone-500 transition hover:bg-stone-100 hover:text-stone-900 dark:text-stone-400 dark:hover:bg-stone-800 dark:hover:text-stone-100"
    >
        <flux:icon.bell class="size-[18px]" />
        @if($count > 0)
            <span class="absolute right-1 top-1 grid min-w-4 place-items-center rounded-full bg-brand-600 px-1 text-[10px] font-bold leading-4 text-white dark:bg-brand-500">{{ $count > 9 ? '9+' : $count }}</span>
        @endif
    </button>

    <div
        x-show="open"
        x-cloak
        x-on:click.outside="open = false"
        role="menu"
        aria-label="Notifications"
        class="absolute right-0 top-full z-50 mt-2 max-h-96 w-80 overflow-y-auto rounded-lg border border-stone-200 bg-white py-1 shadow-xl dark:border-stone-700 dark:bg-stone-900"
    >
        <div class="flex items-center justify-between px-3 py-2">
            <p class="text-sm font-bold text-stone-900 dark:text-white">Notifications</p>
            @if($count > 0)
                <button type="button" wire:click="markAllRead" class="text-xs font-medium text-brand-700 underline dark:text-brand-300">Mark all read</button>
            @endif
        </div>
        <div class="border-t border-stone-100 dark:border-stone-800"></div>
        @forelse($items as $n)
            @php
                $d = $n->data;
                $title = $d['title'] ?? 'Update';
                $body = $d['body'] ?? ($d['message'] ?? '');
                $url = $d['url'] ?? (isset($d['gang_id']) ? '/groups' : (isset($d['order_id']) ? '/orders' : '/dashboard'));
                $icon = $d['icon'] ?? 'bell';
            @endphp
            <a href="{{ $url }}" class="flex items-start gap-2.5 px-3 py-2.5 hover:bg-stone-50 dark:hover:bg-stone-800 {{ $n->read_at ? '' : 'bg-brand-50 dark:bg-brand-500/15' }}">
                <span class="grid size-8 shrink-0 place-items-center rounded-lg {{ $n->read_at ? 'bg-stone-100 text-stone-500 dark:bg-stone-800 dark:text-stone-400' : 'bg-brand-600 text-white dark:bg-brand-500' }}"><flux:icon :name="$icon" class="size-4" /></span>
                <span class="min-w-0 flex-1">
                    <span class="block text-sm font-semibold text-stone-900 dark:text-stone-100">{{ $title }}</span>
                    @if($body)<span class="block truncate text-xs text-stone-500 dark:text-stone-400">{{ $body }}</span>@endif
                    <span class="block text-[11px] text-stone-400 dark:text-stone-500">{{ $n->created_at->diffForHumans() }}</span>
                </span>
                @if(!$n->read_at)<span class="mt-1.5 size-2 shrink-0 rounded-full bg-brand-600 dark:bg-brand-400"></span>@endif
            </a>
        @empty
            <p class="px-3 py-6 text-center text-sm text-stone-500 dark:text-stone-400">You're all caught up.</p>
        @endforelse
    </div>
</div>
