<div>
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">Disputes</h1>
      <p class="text-sm text-zinc-500 dark:text-zinc-400">Buyer–merchant conflicts, oldest unresolved first in your queue.</p>
    </div>
    <select wire:model.live="status" aria-label="Status" class="h-10 rounded-lg border border-zinc-300 bg-white px-2 text-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
      <option value="">All statuses</option>
      @foreach($statuses as $s)<option value="{{ $s->value }}">{{ str_replace('_', ' ', $s->value) }}</option>@endforeach
    </select>
  </div>

  @if(session('ok'))<p class="mt-3 rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-2 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">{{ session('ok') }}</p>@endif

  <div wire:loading.remove class="mt-3 space-y-2">
    @forelse($disputes as $d)
      <article class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex flex-wrap items-center justify-between gap-2">
          <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $d->reason }} <span class="font-normal text-zinc-500">· order #{{ $d->order_id }} · {{ $d->order?->customer?->displayName() ?? '—' }}</span></p>
          <span class="rounded-md px-2 py-0.5 text-xs font-bold {{ in_array($d->status, ['open','under_review']) ? 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }}">{{ str_replace('_', ' ', $d->status) }}</span>
        </div>
        <p class="mt-1.5 text-sm text-zinc-600 dark:text-zinc-300">{{ $d->description }}</p>
        @if($d->resolution)<p class="mt-1.5 rounded-md bg-zinc-50 px-2.5 py-1.5 text-xs text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300"><strong>Resolution:</strong> {{ $d->resolution }}</p>@endif
        <div class="mt-2.5 flex flex-wrap gap-1.5">
          @if($d->status === 'open')
            <button wire:click="start({{ $d->id }})" class="rounded-md border border-zinc-300 px-2.5 py-1 text-xs font-medium hover:bg-zinc-100 dark:border-zinc-600 dark:hover:bg-zinc-800">Start review</button>
          @endif
          @if(in_array($d->status, ['open','under_review']))
            <button wire:click="openResolve({{ $d->id }})" class="rounded-md bg-brand-600 px-2.5 py-1 text-xs font-semibold text-white hover:bg-brand-700 dark:bg-brand-500 dark:hover:bg-brand-400">Resolve</button>
          @endif
        </div>
        @if($resolving === $d->id)
          <div class="mt-3 rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
            <textarea wire:model="resolution" rows="3" placeholder="Resolution summary (min 10 characters)…" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"></textarea>
            @error('resolution')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            <div class="mt-2 flex flex-wrap items-center gap-2">
              <select wire:model="outcome" class="h-9 rounded-lg border border-zinc-300 bg-white px-2 text-sm dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                <option value="resolved_merchant">Resolve for merchant</option>
                <option value="resolved_customer">Resolve for customer</option>
                <option value="closed">Close without ruling</option>
              </select>
              <button wire:click="resolve" class="h-9 rounded-lg bg-brand-600 px-4 text-sm font-semibold text-white hover:bg-brand-700 dark:bg-brand-500 dark:hover:bg-brand-400">Save resolution</button>
              <button wire:click="$set('resolving', null)" class="h-9 rounded-lg border border-zinc-300 px-3 text-sm dark:border-zinc-600">Cancel</button>
            </div>
          </div>
        @endif
      </article>
    @empty
      <p class="rounded-lg border border-zinc-200 bg-white px-3 py-8 text-center text-sm text-zinc-500 dark:border-zinc-700 dark:bg-zinc-900">No disputes match.</p>
    @endforelse
  </div>
  <div wire:loading class="mt-3 space-y-2" aria-hidden="true">
    @for($i = 0; $i < 6; $i++)
      <div class="animate-pulse rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex items-center justify-between gap-2"><div class="h-4 w-1/2 rounded-full bg-zinc-200 dark:bg-zinc-700"></div><div class="h-5 w-16 rounded-md bg-zinc-200 dark:bg-zinc-700"></div></div>
        <div class="mt-2 h-3 w-full rounded-full bg-zinc-200 dark:bg-zinc-700"></div>
        <div class="mt-1.5 h-3 w-2/3 rounded-full bg-zinc-200 dark:bg-zinc-700"></div>
        <div class="mt-2.5 flex gap-1.5"><div class="h-7 w-20 rounded-md bg-zinc-200 dark:bg-zinc-700"></div><div class="h-7 w-16 rounded-md bg-zinc-200 dark:bg-zinc-700"></div></div>
      </div>
    @endfor
  </div>
  <div class="mt-3">{{ $disputes->links() }}</div>
</div>
