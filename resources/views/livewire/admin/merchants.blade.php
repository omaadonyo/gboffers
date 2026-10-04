<div>
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">Merchants</h1>
      <p class="text-sm text-zinc-500 dark:text-zinc-400">Approve new sellers, suspend bad actors, reactivate good ones.</p>
    </div>
    <span class="rounded-lg bg-zinc-100 px-2.5 py-1 text-xs font-bold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $merchants->total() }} total</span>
  </div>

  <div class="mt-4 flex flex-col gap-2 md:flex-row">
    <input type="search" wire:model.live.debounce.300ms="q" placeholder="Search business name…" aria-label="Search merchants" class="h-10 flex-1 rounded-lg border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
    <select wire:model.live="status" aria-label="Status" class="h-10 rounded-lg border border-zinc-300 bg-white px-2 text-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
      <option value="">All statuses</option>
      @foreach($statuses as $s)<option value="{{ $s->value }}">{{ $s->value }}</option>@endforeach
    </select>
  </div>

  <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
    <table class="w-full min-w-[780px] text-left text-sm">
      <thead>
        <tr class="border-b border-zinc-200 text-xs uppercase tracking-wide text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
          <th class="px-3 py-2.5 font-semibold">Merchant</th>
          <th class="px-3 py-2.5 font-semibold">Owner</th>
          <th class="px-3 py-2.5 font-semibold">Offers / Orders</th>
          <th class="px-3 py-2.5 font-semibold">Rating</th>
          <th class="px-3 py-2.5 font-semibold">Status</th>
          <th class="px-3 py-2.5 text-right font-semibold">Actions</th>
        </tr>
      </thead>
      <tbody wire:loading.remove class="divide-y divide-zinc-100 dark:divide-zinc-800">
        @forelse($merchants as $m)
          <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60">
            <td class="px-3 py-2.5">
              <a href="{{ route('merchants.show', $m->slug) }}" class="font-medium text-zinc-900 hover:underline dark:text-zinc-100">{{ $m->trading_name ?? $m->business_name }}</a>
              <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ '@'.$m->slug }} · {{ $m->location ?? '—' }}</p>
            </td>
            <td class="px-3 py-2.5 text-zinc-600 dark:text-zinc-300">{{ $m->owner->name ?? '—' }}</td>
            <td class="px-3 py-2.5 text-zinc-600 dark:text-zinc-300">{{ $m->offers_count }} / {{ $m->orders_count }}</td>
            <td class="px-3 py-2.5 text-zinc-600 dark:text-zinc-300">{{ number_format((float) $m->rating_avg, 1) }} ★</td>
            <td class="px-3 py-2.5">
              <span class="rounded-md px-2 py-0.5 text-xs font-bold {{ $m->verification_status === 'approved' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : ($m->verification_status === 'pending' ? 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' : 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300') }}">{{ $m->verification_status }}</span>
            </td>
            <td class="px-3 py-2.5">
              <div class="flex justify-end gap-1.5">
                @if($m->verification_status !== 'approved')
                  <button wire:click="approve({{ $m->id }})" class="rounded-md border border-emerald-300 px-2 py-1 text-xs font-medium text-emerald-700 hover:bg-emerald-50 dark:border-emerald-800 dark:text-emerald-300 dark:hover:bg-emerald-950/40">Approve</button>
                @endif
                @if($m->verification_status === 'pending')
                  <button wire:click="reject({{ $m->id }})" class="rounded-md border border-zinc-300 px-2 py-1 text-xs font-medium hover:bg-zinc-100 dark:border-zinc-600 dark:hover:bg-zinc-800">Reject</button>
                @endif
                @if($m->verification_status === 'approved')
                  <button wire:click="suspend({{ $m->id }})" class="rounded-md border border-red-300 px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-950/40">Suspend</button>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="px-3 py-8 text-center text-sm text-zinc-500">No merchants match.</td></tr>
        @endforelse
      </tbody>
      <tbody wire:loading><tr><td colspan="6"><x-gb.skeleton-table :cols="6" :rows="15" /></td></tr></tbody>
    </table>
  </div>
  <div class="mt-3">{{ $merchants->links() }}</div>
</div>
