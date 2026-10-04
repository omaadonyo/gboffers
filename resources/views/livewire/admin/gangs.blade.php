<div>
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">Groups</h1>
      <p class="text-sm text-zinc-500 dark:text-zinc-400">Every buyer group on the platform and how close each is to unlocking.</p>
    </div>
    <span class="rounded-lg bg-zinc-100 px-2.5 py-1 text-xs font-bold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $gangs->total() }} total</span>
  </div>

  <div class="mt-4 flex flex-col gap-2 md:flex-row">
    <input type="search" wire:model.live.debounce.300ms="q" placeholder="Search group code or offer…" aria-label="Search groups" class="h-10 flex-1 rounded-lg border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
    <x-gb.select wire:model.live="status" aria-label="Status" :options="['' => 'All statuses'] + collect($statuses)->mapWithKeys(fn($s) => [$s->value => ucfirst($s->value)])->toArray()" />
  </div>

  <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
    <table class="w-full min-w-[720px] text-left text-sm">
      <thead>
        <tr class="border-b border-zinc-200 text-xs uppercase tracking-wide text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
          <th class="px-3 py-2.5 font-semibold">Group</th>
          <th class="px-3 py-2.5 font-semibold">Progress</th>
          <th class="px-3 py-2.5 font-semibold">Members</th>
          <th class="px-3 py-2.5 font-semibold">Status</th>
          <th class="px-3 py-2.5 font-semibold">Expires</th>
        </tr>
      </thead>
      <tbody wire:loading.remove class="divide-y divide-zinc-100 dark:divide-zinc-800">
        @forelse($gangs as $g)
          <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60">
            <td class="px-3 py-2.5">
              <a href="{{ route('offers.show', $g->offer->slug) }}" class="font-medium text-zinc-900 hover:underline dark:text-zinc-100">{{ $g->offer->title }}</a>
              <p class="font-mono text-xs text-zinc-500 dark:text-zinc-400">{{ $g->code }} · {{ $g->offer->merchant->trading_name ?? $g->offer->merchant->business_name }}</p>
            </td>
            <td class="px-3 py-2.5">
              <div class="flex items-center gap-2">
                <div class="h-1.5 w-24 overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700"><div class="h-full rounded-full bg-brand-600 dark:bg-brand-400" style="width: {{ $g->progressPct() }}%"></div></div>
                <span class="text-xs text-zinc-600 dark:text-zinc-300">{{ $g->confirmed_count }}/{{ $g->target }}</span>
              </div>
            </td>
            <td class="px-3 py-2.5 text-zinc-600 dark:text-zinc-300">{{ $g->members_count }}</td>
            <td class="px-3 py-2.5"><span class="rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-bold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $g->status }}</span></td>
            <td class="px-3 py-2.5 text-xs text-zinc-500 dark:text-zinc-400">{{ $g->expires_at?->format('d M H:i') ?? '—' }}</td>
          </tr>
        @empty
          <tr><td colspan="5" class="px-3 py-8 text-center text-sm text-zinc-500">No groups match.</td></tr>
        @endforelse
      </tbody>
      <tbody wire:loading><tr><td colspan="5"><x-gb.skeleton-table :cols="5" :rows="15" /></td></tr></tbody>
    </table>
  </div>
  <div class="mt-3">{{ $gangs->links() }}</div>
</div>
