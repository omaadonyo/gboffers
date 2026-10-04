<div>
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">Offers</h1>
      <p class="text-sm text-zinc-500 dark:text-zinc-400">Every product listed on the platform, across all merchants.</p>
    </div>
    <span class="rounded-lg bg-zinc-100 px-2.5 py-1 text-xs font-bold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $offers->total() }} total</span>
  </div>

  @if(session('ok'))<p class="mt-3 rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-2 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">{{ session('ok') }}</p>@endif
  @if(session('err'))<p class="mt-3 rounded-lg border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-800 dark:border-red-800 dark:bg-red-950/60 dark:text-red-300">{{ session('err') }}</p>@endif

  <div class="mt-4 flex flex-col gap-2 md:flex-row">
    <input type="search" wire:model.live.debounce.300ms="q" placeholder="Search title or merchant…" aria-label="Search offers" class="h-10 flex-1 rounded-lg border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
    <x-gb.select wire:model.live="status" aria-label="Status" :options="['' => 'All statuses'] + collect($statuses)->mapWithKeys(fn($s) => [$s->value => ucfirst($s->value)])->toArray()" />
  </div>

  <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
    <table class="w-full min-w-[760px] text-left text-sm">
      <thead>
        <tr class="border-b border-zinc-200 text-xs uppercase tracking-wide text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
          <th class="px-3 py-2.5 font-semibold">Offer</th>
          <th class="px-3 py-2.5 font-semibold">Merchant</th>
          <th class="px-3 py-2.5 font-semibold">Price</th>
          <th class="px-3 py-2.5 font-semibold">Groups / Orders</th>
          <th class="px-3 py-2.5 font-semibold">Status</th>
          <th class="px-3 py-2.5 text-right font-semibold">Actions</th>
        </tr>
      </thead>
      <tbody wire:loading.remove class="divide-y divide-zinc-100 dark:divide-zinc-800">
        @forelse($offers as $o)
          <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60">
            <td class="px-3 py-2.5">
              <a href="{{ route('offers.show', $o->slug) }}" class="font-medium text-zinc-900 hover:underline dark:text-zinc-100">{{ $o->title }}</a>
              <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $o->category->name ?? '—' }} · {{ $o->confirmed_count }}/{{ $o->gang_target }} confirmed @if($o->featured)· <span class="font-bold text-amber-600 dark:text-amber-400">Featured</span>@endif</p>
            </td>
            <td class="px-3 py-2.5 text-zinc-600 dark:text-zinc-300">{{ $o->merchant->trading_name ?? $o->merchant->business_name }}</td>
            <td class="px-3 py-2.5 font-semibold text-zinc-900 dark:text-zinc-100">{{ App\Support\Money::formatUgx($o->normal_price) }}</td>
            <td class="px-3 py-2.5 text-zinc-600 dark:text-zinc-300">{{ $o->gangs_count }} / {{ $o->orders_count }}</td>
            <td class="px-3 py-2.5"><span class="rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-bold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $o->status }}</span></td>
            <td class="px-3 py-2.5">
              <div class="flex justify-end gap-1.5">
                @if($o->status === 'active')
                  <button wire:click="pause({{ $o->id }})" class="rounded-md border border-zinc-300 px-2 py-1 text-xs font-medium hover:bg-zinc-100 dark:border-zinc-600 dark:hover:bg-zinc-800">Pause</button>
                @else
                  <button wire:click="activate({{ $o->id }})" class="rounded-md border border-zinc-300 px-2 py-1 text-xs font-medium hover:bg-zinc-100 dark:border-zinc-600 dark:hover:bg-zinc-800">Activate</button>
                @endif
                <button wire:click="toggleFeatured({{ $o->id }})" class="rounded-md border border-zinc-300 px-2 py-1 text-xs font-medium hover:bg-zinc-100 dark:border-zinc-600 dark:hover:bg-zinc-800">{{ $o->featured ? 'Unfeature' : 'Feature' }}</button>
                <button wire:click="destroy({{ $o->id }})" wire:confirm="Delete this offer? Only possible when it has no groups or orders." class="rounded-md border border-red-300 px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-950/40">Delete</button>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="px-3 py-8 text-center text-sm text-zinc-500">No offers match.</td></tr>
        @endforelse
      </tbody>
      <tbody wire:loading><tr><td colspan="6"><x-gb.skeleton-table :cols="6" :rows="15" /></td></tr></tbody>
    </table>
  </div>
  <div class="mt-3">{{ $offers->links() }}</div>
</div>
