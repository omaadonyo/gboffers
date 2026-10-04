<div>
  <h1 class="text-lg font-bold">Payments awaiting confirmation</h1>
  <p class="text-xs text-stone-500 dark:text-stone-400">Check your mobile-money/bank account first. Screenshots are not proof — only confirm what you actually received.</p>
  <div wire:loading.remove class="mt-3 space-y-2">@forelse($rows as $o)
  <div class="border border-stone-200 dark:border-stone-800 bg-white dark:bg-stone-900 p-3 text-sm">
    <p class="font-semibold">{{ $o->customer->displayName() }} · {{ $o->offer->title }}</p>
    <p>{{ App\Support\Money::formatUgx($o->total) }} · Ref: <span class="font-mono">{{ $o->payment_reference }}</span> · reported: {{ $o->payment->reported_at?->diffForHumans() ?? 'not yet' }}</p>
    <label class="mt-2 flex items-start gap-2 text-xs"><input type="checkbox" wire:model="confirmAck"> I confirm I have received {{ App\Support\Money::formatUgx($o->total) }} for {{ $o->payment_reference }} in my own account.</label>
    <button wire:click="confirm({{ $o->id }})" class="mt-2 h-9 bg-brand-600 dark:bg-brand-500 px-4 text-xs font-semibold text-white">Confirm payment received</button>
  </div>
  @empty<x-gb.empty-state title="All caught up" body="No payments awaiting confirmation." />@endforelse</div>
  <div wire:loading class="mt-3 space-y-2">@for($i = 0; $i < 15; $i++)<x-gb.skeleton-row />@endfor</div>
  <div class="mt-3">{{ $rows->links() }}</div>
</div>
