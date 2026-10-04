@props(['endsAt' => null, 'compact' => false])
@if($endsAt)
<div
  x-data="{ left: '', urgent: false, tick() { const ms = new Date('{{ $endsAt }}').getTime() - Date.now(); if (ms <= 0) { this.left = 'Ended'; this.urgent = true; return; } const s = Math.floor(ms / 1000); const d = Math.floor(s / 86400); const h = Math.floor(s % 86400 / 3600); const m = Math.floor(s % 3600 / 60); const ss = s % 60; const p = (n) => String(n).padStart(2, '0'); this.urgent = ms < 864e5; this.left = d > 0 ? d + 'd ' + p(h) + ':' + p(m) + ':' + p(ss) : p(h) + ':' + p(m) + ':' + p(ss); } }"
  x-init="tick(); setInterval(() => tick(), 1000)"
  class="inline-flex items-center gap-1.5"
  role="timer"
  aria-label="Offer ends countdown"
>
  <flux:icon.clock class="size-4 shrink-0 text-stone-400 dark:text-stone-500" />
  @if($compact)
    <span class="rounded-full px-2.5 py-1 font-mono text-xs font-bold tabular-nums" :class="urgent ? 'bg-red-600 text-white' : 'bg-stone-900 text-white dark:bg-white dark:text-stone-900'" x-text="left"></span>
  @else
    <span class="rounded-lg px-2.5 py-1 font-mono text-sm font-bold tabular-nums" :class="urgent ? 'bg-red-600 text-white' : 'bg-stone-900 text-white dark:bg-white dark:text-stone-900'" x-text="left"></span>
  @endif
</div>
@endif
