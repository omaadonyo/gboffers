<div>
  <h1 class="text-lg font-bold">Orders</h1>
  <div wire:loading.remove class="mt-3 space-y-2">@forelse($rows as $o)
  <div class="border border-stone-200 dark:border-stone-800 bg-white dark:bg-stone-900 p-3 text-sm">
    <p class="font-semibold">{{ $o->customer->displayName() }} · {{ $o->offer->title }}</p>
    <p class="text-xs text-stone-500 dark:text-stone-400">{{ App\Support\Money::formatUgx($o->total) }} · payment: {{ $o->payment_status }} · GBPass: <span class="font-mono">{{ $o->gbPass->token ?? '—' }}</span> · fulfillment: {{ $o->fulfillment_status }} · order: {{ $o->status }}</p>
    @if($o->status==='redeemed')<button wire:click="fulfill({{ $o->id }})" class="mt-2 h-9 border border-stone-900 px-3 text-xs font-semibold">Mark fulfilled</button>@endif
  </div>
  @empty<x-gb.empty-state title="No orders" body="Orders from your groups will appear here." />@endforelse</div>
  <div wire:loading class="mt-3 space-y-2">@for($i = 0; $i < 15; $i++)<x-gb.skeleton-row />@endfor</div>
  <div class="mt-3">{{ $rows->links() }}</div>
</div>
