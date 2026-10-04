<div>
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">Orders</h1>
      <p class="text-sm text-zinc-500 dark:text-zinc-400">Every transaction on the platform, with payment and fulfillment state.</p>
    </div>
    <span class="rounded-lg bg-zinc-100 px-2.5 py-1 text-xs font-bold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $orders->total() }} orders · {{ App\Support\Money::formatUgx($total) }}</span>
  </div>

  <div class="mt-4 flex flex-col gap-2 md:flex-row">
    <input type="search" wire:model.live.debounce.300ms="q" placeholder="Search id, reference, customer…" aria-label="Search orders" class="h-10 flex-1 rounded-lg border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
    <x-gb.select wire:model.live="status" aria-label="Status" :options="['' => 'All statuses'] + collect($statuses)->mapWithKeys(fn($s) => [$s->value => $s->value])->toArray()" />
    <x-gb.select wire:model.live="payment" aria-label="Payment" :options="['' => 'All payments'] + collect($payments)->mapWithKeys(fn($p) => [$p->value => $p->value])->toArray()" />
  </div>

  <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
    <table class="w-full min-w-[820px] text-left text-sm">
      <thead>
        <tr class="border-b border-zinc-200 text-xs uppercase tracking-wide text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
          <th class="px-3 py-2.5 font-semibold">#</th>
          <th class="px-3 py-2.5 font-semibold">Customer</th>
          <th class="px-3 py-2.5 font-semibold">Offer</th>
          <th class="px-3 py-2.5 font-semibold">Merchant</th>
          <th class="px-3 py-2.5 font-semibold">Total</th>
          <th class="px-3 py-2.5 font-semibold">Payment</th>
          <th class="px-3 py-2.5 font-semibold">Status</th>
          <th class="px-3 py-2.5 font-semibold">Date</th>
        </tr>
      </thead>
      <tbody wire:loading.remove class="divide-y divide-zinc-100 dark:divide-zinc-800">
        @forelse($orders as $o)
          <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60">
            <td class="px-3 py-2.5 font-mono text-xs text-zinc-500 dark:text-zinc-400">#{{ $o->id }}</td>
            <td class="px-3 py-2.5 font-medium text-zinc-900 dark:text-zinc-100">{{ $o->customer->displayName() }}</td>
            <td class="px-3 py-2.5"><a href="{{ route('offers.show', $o->offer->slug) }}" class="text-zinc-700 hover:underline dark:text-zinc-300">{{ Str::limit($o->offer->title, 32) }}</a></td>
            <td class="px-3 py-2.5 text-zinc-600 dark:text-zinc-300">{{ $o->merchant->trading_name ?? $o->merchant->business_name }}</td>
            <td class="px-3 py-2.5 font-semibold text-zinc-900 dark:text-zinc-100">{{ App\Support\Money::formatUgx($o->total) }} <span class="font-normal text-zinc-400">×{{ $o->quantity }}</span></td>
            <td class="px-3 py-2.5"><span class="rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-bold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $o->payment_status }}</span></td>
            <td class="px-3 py-2.5"><span class="rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-bold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $o->status }}</span></td>
            <td class="px-3 py-2.5 text-xs text-zinc-500 dark:text-zinc-400">{{ $o->created_at->format('d M H:i') }}</td>
          </tr>
        @empty
          <tr><td colspan="8" class="px-3 py-8 text-center text-sm text-zinc-500">No orders match.</td></tr>
        @endforelse
      </tbody>
      <tbody wire:loading><tr><td colspan="8"><x-gb.skeleton-table :cols="8" :rows="15" /></td></tr></tbody>
    </table>
  </div>
  <div class="mt-3">{{ $orders->links() }}</div>
</div>
