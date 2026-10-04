<div>
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">Commissions</h1>
      <p class="text-sm text-zinc-500 dark:text-zinc-400">What you owe GBOffers per settled order.</p>
    </div>
    <select wire:model.live="status" aria-label="Status" class="h-10 rounded-lg border border-zinc-300 bg-white px-2 text-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
      <option value="">All statuses</option>
      @foreach($statuses as $s)<option value="{{ $s->value }}">{{ $s->value }}</option>@endforeach
    </select>
  </div>

  <div class="mt-4 grid grid-cols-2 gap-2.5 md:grid-cols-5">
    @foreach($totals as $label => $amount)
      <div class="rounded-lg border border-zinc-200 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900">
        <p class="text-base font-bold tracking-tight text-zinc-900 dark:text-white">{{ App\Support\Money::formatUgx($amount) }}</p>
        <p class="text-xs capitalize text-zinc-500 dark:text-zinc-400">{{ $label }}</p>
      </div>
    @endforeach
  </div>

  <div wire:loading.remove class="mt-3 space-y-2">
    @forelse($rows as $c)
      <div class="flex items-center justify-between gap-3 rounded-lg border border-zinc-200 bg-white p-3.5 text-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="min-w-0">
          <p class="font-semibold text-zinc-900 dark:text-zinc-100">Order #{{ $c->order_id }} · {{ $c->rule ?? 'standard rate' }}</p>
          <p class="text-xs text-zinc-500">{{ $c->created_at->format('d M Y') }} @if($c->settlement_reference)· ref {{ $c->settlement_reference }}@endif</p>
        </div>
        <div class="flex shrink-0 items-center gap-2">
          <span class="rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-bold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $c->status }}</span>
          <span class="font-bold text-zinc-900 dark:text-white">{{ App\Support\Money::formatUgx($c->amount) }}</span>
        </div>
      </div>
    @empty
      <p class="rounded-lg border border-zinc-200 bg-white px-3 py-8 text-center text-sm text-zinc-500 dark:border-zinc-700 dark:bg-zinc-900">No commission lines yet.</p>
    @endforelse
  </div>
  <div wire:loading class="mt-3 space-y-2">@for($i = 0; $i < 15; $i++)<x-gb.skeleton-row />@endfor</div>
  <div class="mt-3">{{ $rows->links() }}</div>
</div>
