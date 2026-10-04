<div>
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">Featured listings</h1>
      <p class="text-sm text-zinc-500 dark:text-zinc-400">Paid pinning · UGX 1,000/day · 10 days for 6,500.</p>
    </div>
    <div class="flex gap-1.5">
      <span class="rounded-lg bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-800 dark:bg-amber-950 dark:text-amber-300">{{ $pendingCount }} pending</span>
      <span class="rounded-lg bg-zinc-100 px-2.5 py-1 text-xs font-bold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ App\Support\Money::formatUgx($revenue) }} earned</span>
    </div>
  </div>

  <div class="mt-4">
    <x-gb.select wire:model.live="status" aria-label="Status" :options="['' => 'All statuses', 'pending' => 'Pending', 'active' => 'Active', 'rejected' => 'Rejected', 'cancelled' => 'Cancelled', 'expired' => 'Expired']" />
  </div>

  <div class="mt-3 space-y-2">
    @forelse($rows as $f)
      <div class="flex flex-wrap items-center gap-3 rounded-lg border border-zinc-200 bg-white p-3.5 dark:border-zinc-700 dark:bg-zinc-900">
        <div class="min-w-0 flex-1">
          <a href="{{ route('offers.show', $f->offer->slug) }}" class="block truncate text-sm font-semibold text-zinc-900 hover:underline dark:text-zinc-100">{{ $f->offer->title }}</a>
          <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">{{ $f->offer->merchant->trading_name ?? $f->offer->merchant->business_name }} · {{ $f->days }} day(s) · {{ App\Support\Money::formatUgx($f->amount) }} @if($f->starts_at)· {{ $f->starts_at->format('d M') }} → {{ $f->ends_at?->format('d M') }}@endif</p>
        </div>
        <span class="rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-bold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $f->status }}</span>
        <div class="flex shrink-0 gap-1.5">
          @if($f->status === 'pending')
            <button wire:click="approve({{ $f->id }})" class="rounded-md bg-brand-600 px-2.5 py-1.5 text-xs font-bold text-white hover:bg-brand-700 dark:bg-brand-500 dark:hover:bg-brand-400">Approve &amp; activate</button>
            <button wire:click="reject({{ $f->id }})" class="rounded-md border border-zinc-300 px-2.5 py-1.5 text-xs font-medium hover:bg-zinc-100 dark:border-zinc-600 dark:hover:bg-zinc-800">Reject</button>
          @endif
          @if($f->status === 'active')
            <button wire:click="cancel({{ $f->id }})" wire:confirm="End featuring now?" class="rounded-md border border-red-300 px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-950/40">End now</button>
          @endif
        </div>
      </div>
    @empty
      <p class="rounded-lg border border-zinc-200 bg-white px-3 py-8 text-center text-sm text-zinc-500 dark:border-zinc-700 dark:bg-zinc-900">No featured requests.</p>
    @endforelse
  </div>
  <div class="mt-3">{{ $rows->links() }}</div>
</div>
