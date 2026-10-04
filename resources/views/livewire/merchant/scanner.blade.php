<div class="mx-auto max-w-md">
  <h1 class="text-lg font-bold">Scan GBPass</h1>
  <div class="mt-3 border border-stone-200 dark:border-stone-800 bg-white dark:bg-stone-900 p-4">
    <label class="text-xs font-medium" for="token">GBPass token (e.g. GB-7K4M-29XP)</label>
    <div class="mt-1 flex gap-2"><input id="token" wire:model="token" placeholder="GB-XXXX-XXXX" class="h-11 flex-1 border border-stone-300 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-100 px-3 font-mono text-sm uppercase"><button wire:click="lookup" class="h-11 bg-brand-600 dark:bg-brand-500 px-4 text-sm font-semibold text-white">Check</button></div>
    <p class="mt-1 text-[11px] text-stone-500 dark:text-stone-400">Camera scanning uses the same token lookup — enter the code shown under the customer's QR.</p>
  </div>
  @if($error)<p class="mt-3 border border-red-300 bg-red-50 p-3 text-sm font-medium text-red-800" role="alert">{{ $error }}</p>@endif
  @if($result)
  <div class="mt-3 border border-stone-200 dark:border-stone-800 bg-white dark:bg-stone-900 p-4 @if(($result['status'] ?? '')==='ready') border-emerald-500 @endif">
    <p class="text-xs font-bold uppercase tracking-wide @if(($result['just_redeemed'] ?? false) || ($result['status'] ?? '')==='redeemed') text-emerald-700 dark:text-emerald-400 @else text-stone-500 dark:text-stone-400 @endif">@if($result['just_redeemed'] ?? false)✓ Redeemed @elseif(($result['status'] ?? '')==='ready')✓ Valid @else {{ $result['status'] }} @endif</p>
    <p class="mt-1 text-sm font-semibold">{{ $result['title'] }}</p>
    <p class="text-sm">{{ App\Support\Money::formatUgx($result['amount']) }} · Payment: <strong>{{ $result['payment'] }}</strong> · Customer: {{ $result['customer'] }}</p>
    @if(($result['status'] ?? '')==='ready' && !($result['just_redeemed'] ?? false))
    <label class="mt-3 block text-xs font-medium" for="pin">Ask customer for their redemption PIN</label>
    <div class="mt-1 flex gap-2"><input id="pin" wire:model="pin" inputmode="numeric" maxlength="4" placeholder="••••" class="h-11 flex-1 border border-stone-300 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-100 px-3 font-mono text-center text-lg tracking-widest"><button wire:click="redeem" class="h-11 bg-brand-600 dark:bg-brand-500 px-4 text-sm font-semibold text-white">Redeem &amp; release</button></div>
    @endif
  </div>
  @endif
</div>
