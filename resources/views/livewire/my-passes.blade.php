<div class="mx-auto max-w-lg">
  <h1 class="text-xl font-bold">My GBPasses</h1>
  <div class="mt-4 space-y-3">@forelse($passes as $p)
  <article class="rounded-lg bg-white p-4 text-center shadow-sm dark:bg-stone-900">
    <p class="text-xs uppercase tracking-widest text-stone-500 dark:text-stone-400">GBPass</p>
    <div class="mx-auto mt-2 grid size-40 place-items-center border border-stone-900 font-mono text-[10px] leading-tight">QR<br>{{ $p->token }}</div>
    <p class="mt-2 font-mono text-sm font-bold">{{ $p->token }}</p>
    <p class="text-sm font-semibold">{{ $p->offer->title }}</p>
    <p class="text-xs text-stone-500 dark:text-stone-400">{{ $p->merchant->trading_name ?? $p->merchant->business_name }} · {{ App\Support\Money::formatUgx($p->amount) }}</p>
    <p class="mt-1 text-xs">Payment: <strong>{{ $p->order->payment_status ?? $p->status }}</strong> · Redemption: <strong>{{ $p->status }}</strong></p>
    @if($p->status === 'ready')
    <p class="mt-2 inline-block border border-dashed border-stone-400 px-3 py-1 font-mono text-sm tracking-widest">PIN: {{ app(App\Services\GBPassService::class)->revealPin($p, auth()->user()) ?? '••••' }}</p>
    <p class="mt-1 text-[11px] text-stone-500 dark:text-stone-400">Tell this PIN to the merchant only when they scan your pass.</p>
    @endif
    <p class="mt-2 text-xs font-semibold uppercase tracking-wide text-stone-700 dark:text-stone-300">Show to merchant — do not share your PIN publicly</p>
  </article>
  @empty<x-gb.empty-state title="No GBPasses yet" body="Join a group and complete payment to receive your secure pass." action="Explore offers" action-url="/explore" />@endforelse</div>
  <div class="mt-3">{{ $passes->links() }}</div>
</div>
