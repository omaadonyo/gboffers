<div class="mx-auto max-w-3xl">
  <div class="flex flex-wrap items-end justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold tracking-tight">My orders</h1>
      <p class="text-sm text-stone-500 dark:text-stone-400">Payments, passes and fulfillment — per order.</p>
    </div>
    <div class="flex gap-1.5 rounded-full bg-stone-100 p-1 text-sm dark:bg-stone-800" role="tablist" aria-label="Orders">
      @foreach([['all','All'],['active','To pay'],['paid','Paid'],['done','Done']] as [$k,$l])
        <button wire:click="$set('filter','{{ $k }}')" role="tab" aria-selected="{{ $filter === $k ? 'true' : 'false' }}" class="rounded-full px-3.5 py-1.5 text-xs font-semibold transition {{ $filter === $k ? 'bg-white text-stone-900 shadow-sm dark:bg-stone-950 dark:text-white' : 'text-stone-500 hover:text-stone-800 dark:text-stone-400 dark:hover:text-stone-100' }}">{{ $l }}</button>
      @endforeach
    </div>
  </div>

  <div wire:loading.remove class="mt-4 space-y-2.5">
    @forelse($orders as $o)
      @php
        $payChip = match($o->payment_status) {
          'confirmed' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300',
          'reported' => 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300',
          'failed','refunded' => 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300',
          default => 'bg-stone-100 text-stone-600 dark:bg-stone-800 dark:text-stone-300',
        };
        $stChip = match(true) {
          in_array($o->status, ['fulfilled']) => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300',
          in_array($o->status, ['cancelled','expired','refunded']) => 'bg-stone-100 text-stone-500 dark:bg-stone-800 dark:text-stone-400',
          default => 'bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300',
        };
      @endphp
      <article class="rounded-xl bg-white p-4 shadow-sm dark:bg-stone-900">
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <a href="{{ route('offers.show', $o->offer->slug) }}" class="block truncate text-sm font-bold text-stone-900 hover:underline dark:text-stone-100">{{ $o->offer->title }}</a>
            <p class="mt-0.5 text-xs text-stone-500 dark:text-stone-400">Order #{{ $o->id }} · {{ $o->merchant->trading_name ?? $o->merchant->business_name }} · {{ $o->created_at->format('d M Y') }} · {{ App\Enums\PaymentMethod::tryFrom($o->payment_method)?->label() ?? $o->payment_method }}</p>
          </div>
          <p class="shrink-0 text-base font-bold text-stone-900 dark:text-white">{{ App\Support\Money::formatUgx($o->total) }}</p>
        </div>
        <div class="mt-2.5 flex flex-wrap items-center gap-1.5">
          <span class="rounded-full px-2 py-0.5 text-[11px] font-bold {{ $payChip }}">{{ str_replace('_', ' ', $o->payment_status) }}</span>
          <span class="rounded-full px-2 py-0.5 text-[11px] font-bold {{ $stChip }}">{{ str_replace('_', ' ', $o->status) }}</span>
          @if($o->gbPass)<span class="rounded-full bg-stone-100 px-2 py-0.5 font-mono text-[11px] font-bold text-stone-600 dark:bg-stone-800 dark:text-stone-300">{{ $o->gbPass->token }}</span>@endif
          <span class="ml-auto flex gap-1.5">
            @if($o->status === 'payment_pending')
              <a href="/checkout/{{ $o->id }}" class="rounded-full bg-brand-600 px-3 py-1.5 text-[11px] font-bold text-white hover:bg-brand-700 dark:bg-brand-500 dark:hover:bg-brand-400">Pay now</a>
            @endif
            @if(in_array($o->status, ['ready_for_redemption','redeemed']))
              <a href="/wallet" class="rounded-full border border-stone-300 px-3 py-1.5 text-[11px] font-bold hover:bg-stone-100 dark:border-stone-700 dark:hover:bg-stone-800">View pass</a>
            @endif
          </span>
        </div>
      </article>
    @empty
      <x-gb.empty-state title="No orders yet" body="Join a group and confirm your spot — orders land here." action="Explore offers" action-url="/explore" />
    @endforelse
  </div>
  <div wire:loading class="mt-4 space-y-2.5">@for($i = 0; $i < 10; $i++)<x-gb.skeleton-row />@endfor</div>
  <div class="mt-3">{{ $orders->links() }}</div>
</div>
