<div class="mx-auto max-w-lg">
  <h1 class="text-xl font-bold">Complete your commitment</h1>
  @php
    $pm = App\Enums\PaymentMethod::tryFrom($order->payment_method ?? 'merchant_direct');
    $isDirect = in_array($order->payment_method, ['merchant_direct', 'momo_direct'], true);
    $terms = $isDirect
      ? 'I understand my payment goes directly to the merchant. Screenshots are not proof — the merchant verifies receipt in their own account.'
      : 'I understand I pay through '.($pm?->label() ?? 'the provider').'. GBOffers never sees my card or PIN. The merchant still confirms receipt before my GBPass is issued.';
  @endphp
  <div class="mt-3 rounded-xl bg-white p-4 shadow-sm sm:p-5 dark:bg-stone-900">
    <div class="flex items-center justify-between gap-2">
      <p class="text-xs font-bold uppercase tracking-wide text-stone-500 dark:text-stone-400">{{ $pm?->label() ?? 'Payment' }}</p>
      <span class="rounded-full bg-stone-100 px-2 py-0.5 font-mono text-[11px] font-bold text-stone-600 dark:bg-stone-800 dark:text-stone-300">{{ $instructions['reference'] }}</span>
    </div>
    <p class="mt-1 text-2xl font-bold tracking-tight">{{ App\Support\Money::formatUgx($order->total) }}</p>
    <p class="text-sm text-stone-600 dark:text-stone-300">To <strong>{{ $instructions['merchant'] }}</strong></p>

    @if($order->payment_status === 'pending')
      <div class="mt-3">
        <p class="text-xs font-bold uppercase tracking-wide text-stone-500 dark:text-stone-400">Switch method</p>
        <div class="mt-1.5 grid grid-cols-2 gap-1.5">
          @foreach(config('gboffers.payments.methods', ['merchant_direct']) as $m)
            @php $opt = App\Enums\PaymentMethod::tryFrom($m); @endphp
            @continue(!$opt)
            <button type="button" wire:click="switchMethod('{{ $m }}')" wire:loading.attr="disabled" class="rounded-lg border px-2.5 py-2 text-left text-xs font-semibold transition {{ $order->payment_method === $m ? 'border-brand-600 bg-brand-50 text-brand-700 dark:border-brand-400 dark:bg-brand-950 dark:text-brand-300' : 'border-stone-300 dark:border-stone-700' }}">{{ $opt->label() }}</button>
          @endforeach
        </div>
      </div>
    @endif

    @if(!empty($instructions['steps']))
      <ol class="mt-3 space-y-1.5">
        @foreach($instructions['steps'] as $i => $step)
          <li class="flex items-start gap-2.5 text-sm"><span class="grid size-5 shrink-0 place-items-center rounded-full bg-brand-600 text-[11px] font-bold text-white dark:bg-brand-500">{{ $i + 1 }}</span><span class="text-stone-700 dark:text-stone-200">{{ $step }}</span></li>
        @endforeach
      </ol>
    @endif

    <dl class="mt-3 space-y-1.5 border-t border-stone-100 pt-3 text-sm dark:border-stone-800">
      @foreach((array)($instructions['pay_to'] ?? []) as $k => $v)<div class="flex justify-between gap-3"><dt class="text-stone-500 dark:text-stone-400">{{ $k }}</dt><dd class="font-mono font-semibold">{{ is_array($v) ? json_encode($v) : $v }}</dd></div>@endforeach
    </dl>

    @if(!empty($instructions['checkout_url']))
      <a href="{{ $instructions['checkout_url'] }}" target="_blank" rel="noopener" class="mt-3 inline-flex h-11 w-full items-center justify-center gap-1.5 rounded-full bg-brand-600 text-sm font-bold text-white hover:bg-brand-700 dark:bg-brand-500 dark:hover:bg-brand-400">Pay now <flux:icon.arrow-up-right class="size-4" /></a>
    @endif

    <p class="mt-3 rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-900 dark:bg-amber-950/60 dark:text-amber-200">{{ $instructions['note'] }}</p>
    <button id="copyBtn" class="mt-2 h-9 w-full rounded-full border border-stone-300 text-xs font-medium dark:border-stone-700" onclick="navigator.clipboard.writeText('{{ $instructions['reference'] }} — {{ App\Support\Money::formatUgx($order->total) }} — {{ $instructions['merchant'] }}');this.textContent='Copied!';">Copy payment details</button>
  </div>
  <label class="mt-3 flex items-start gap-2 text-xs text-stone-600 dark:text-stone-400"><input type="checkbox" wire:model="ack" class="mt-0.5"> {{ $terms }}</label>
  @if($order->payment->status === 'reported' || $order->payment->status === 'confirmed')
    <div class="mt-3 rounded-xl bg-emerald-50 p-4 text-sm shadow-sm dark:bg-emerald-950/60" role="status"><p class="font-semibold text-emerald-900 dark:text-emerald-200">Waiting for merchant confirmation</p><p class="text-xs text-emerald-800 dark:text-emerald-300">We don't treat screenshots as payment confirmation. The merchant will verify receipt. Payment status: <strong>{{ $order->payment->status }}</strong></p><a href="/wallet" class="mt-2 inline-block rounded-full bg-brand-600 px-4 py-2 text-xs font-semibold text-white dark:bg-brand-500">View my GBPasses</a></div>
  @else
    <button wire:click="reportPaid" wire:loading.attr="disabled" wire:target="reportPaid" class="mt-3 inline-flex h-11 w-full items-center justify-center gap-2 rounded-full bg-brand-600 text-sm font-bold text-white hover:bg-brand-700 disabled:opacity-60 dark:bg-brand-500 dark:hover:bg-brand-400">
      <span wire:loading.remove wire:target="reportPaid">I've paid</span>
      <span wire:loading wire:target="reportPaid" class="inline-flex items-center gap-2"><flux:icon.loading class="size-4 animate-spin" /> Confirming…</span>
    </button>
  @endif
</div>
