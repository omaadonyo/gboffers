<div>
  <div class="flex flex-wrap items-end justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold tracking-tight">Groups</h1>
      <p class="text-sm text-stone-500 dark:text-stone-400">Discover forming groups — or track your own.</p>
    </div>
    <div class="flex gap-1.5 rounded-full bg-stone-100 p-1 text-sm dark:bg-stone-800" role="tablist" aria-label="Groups">
      @foreach([['discover','Discover'],['mine','My groups'],['done','Completed']] as [$k,$l])
        @if($k === 'discover' || auth()->check())
          <button wire:click="$set('tab','{{ $k }}')" role="tab" aria-selected="{{ $tab === $k ? 'true' : 'false' }}" class="rounded-full px-3.5 py-1.5 text-xs font-semibold transition {{ $tab === $k ? 'bg-white text-stone-900 shadow-sm dark:bg-stone-950 dark:text-white' : 'text-stone-500 hover:text-stone-800 dark:text-stone-400 dark:hover:text-stone-100' }}">{{ $l }}</button>
        @endif
      @endforeach
    </div>
  </div>

  @if($tab === 'discover')
    <div class="mt-4">
      <input type="search" wire:model.live.debounce.300ms="q" placeholder="Search groups by product…" aria-label="Search groups" class="h-10 w-full max-w-md rounded-full border-0 bg-stone-100 px-4 text-sm dark:bg-stone-800 dark:text-stone-100">
    </div>
    <div wire:loading.remove class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
      @foreach($gangs as $g)<x-gb.group-card :gang="$g" />@endforeach
    </div>
    <div wire:loading class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">@for($i = 0; $i < 12; $i++)<x-gb.skeleton-card variant="group" />@endfor</div>
    @if($gangs->isEmpty())
      <div class="mt-4"><x-gb.empty-state title="No groups forming" body="Be the first — find something you want and start one with your friends." action="Explore offers" action-url="/explore" /></div>
    @endif
    <div class="mt-4">{{ $gangs->links() }}</div>
  @else
    <div wire:loading.remove class="mt-4 grid gap-2.5 md:grid-cols-2">
      @forelse($members as $m)
        @php
          $g = $m->gang;
          $o = $m->offer ?? $g?->offer;
          $fellows = $g ? $g->members->where('user_id', '!==', $m->user_id)->take(5)->map(fn($fm) => ['name' => $fm->user?->displayName() ?? 'Member', 'contact' => $fm->user?->profile?->phone ?? null]) : collect();
          $catIcon = App\Support\CategoryStyle::iconFor($o?->category);
          $href = ($m->status === 'payment_pending' && $m->order_id) ? '/checkout/'.$m->order_id : ($m->status === 'ready' ? '/wallet' : '/offers/'.($o?->slug ?? ''));
          $cta = ($m->status === 'payment_pending' && $m->order_id) ? 'Pay now' : ($m->status === 'ready' ? 'View pass' : 'Track');
          $chip = match($m->status) {
            'reserved' => 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300',
            'payment_pending' => 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300',
            'payment_confirmed' => 'bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300',
            'ready' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300',
            default => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300',
          };
        @endphp
        <a href="{{ $href }}" class="group flex gap-3 rounded-lg bg-white p-3.5 shadow-sm transition hover:shadow-md dark:bg-stone-900">
          <span class="grid size-14 shrink-0 place-items-center overflow-hidden rounded-lg bg-stone-100 dark:bg-stone-800">
            @if($o?->image_path)<img src="{{ $o->image_path }}" alt="" loading="lazy" class="size-full object-cover">
            @else<flux:icon :name="$catIcon" class="size-6 text-brand-600 dark:text-white" />@endif
          </span>
          <span class="min-w-0 flex-1">
            <span class="flex items-center justify-between gap-2">
              <span class="truncate text-sm font-bold text-stone-900 dark:text-stone-100">{{ $o?->title ?? 'Group order' }}</span>
              <span class="shrink-0 rounded-full px-2 py-0.5 text-[11px] font-bold {{ $chip }}">{{ str_replace('_', ' ', $m->status) }}</span>
            </span>
            <span class="mt-0.5 block truncate text-xs text-stone-500 dark:text-stone-400">{{ $o?->merchant->trading_name ?? $o?->merchant->business_name ?? '' }} · {{ $g?->code ?? '' }}</span>
            @if($g)
              <span class="mt-1.5 block h-1.5 overflow-hidden rounded-full bg-stone-200 dark:bg-stone-700"><span class="block h-full rounded-full bg-brand-600 dark:bg-brand-400" style="width: {{ $g->progressPct() }}%"></span></span>
            @endif
            <span class="mt-2 flex items-center justify-between gap-2">
              <x-gb.avatar-stack :users="$fellows" :limit="4" size="size-6" />
              <span class="inline-flex shrink-0 items-center gap-1 text-xs font-bold {{ $cta === 'Track' ? 'text-stone-500 dark:text-stone-400' : 'text-brand-700 dark:text-brand-300' }}">{{ $cta }} <flux:icon.chevron-right class="size-3.5 transition group-hover:translate-x-0.5" /></span>
            </span>
          </span>
        </a>
      @empty
        <div class="md:col-span-2"><x-gb.empty-state title="Nothing here yet" body="Join a group and it will show up here." action="Discover groups" action-url="/groups" /></div>
      @endforelse
    </div>
    <div wire:loading class="mt-4 grid gap-2.5 md:grid-cols-2">@for($i = 0; $i < 10; $i++)<x-gb.skeleton-row />@endfor</div>
    <div class="mt-3">{{ $members->links() }}</div>
  @endif
</div>
