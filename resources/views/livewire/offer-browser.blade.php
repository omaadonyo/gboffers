<div>
  <h1 class="text-xl font-bold tracking-tight">Explore</h1>
  <div class="mt-3 flex flex-col gap-2 md:flex-row md:items-center">
    <input type="search" wire:model.live.debounce.300ms="q" placeholder="Search products, merchants…" aria-label="Search" class="h-10 flex-1 rounded-full border-0 bg-stone-100 px-4 text-sm dark:bg-stone-800 dark:text-stone-100">
    <div class="flex items-center gap-2">
      <x-gb.select wire:model.live="sort" class="h-10 rounded-full border-0 bg-stone-100 dark:bg-stone-800" aria-label="Sort" :options="['popular' => 'Popular', 'ending' => 'Ending soon', 'new' => 'Newly added']" />
      <div class="flex rounded-full bg-stone-100 p-1 dark:bg-stone-800" role="group" aria-label="Display view">
        @foreach([['grid','layout-grid','Grid'],['list','list-bullet','List'],['showcase','sparkles','Showcase']] as [$v,$i,$l])
          <button type="button" wire:click="setView('{{ $v }}')" title="{{ $l }} view" aria-label="{{ $l }} view" aria-pressed="{{ $view === $v ? 'true' : 'false' }}" class="grid size-8 place-items-center rounded-full transition {{ $view === $v ? 'bg-white text-stone-900 shadow-sm dark:bg-stone-950 dark:text-white' : 'text-stone-400 hover:text-stone-700 dark:text-stone-500 dark:hover:text-stone-200' }}"><flux:icon :name="$i" class="size-4" /></button>
        @endforeach
      </div>
    </div>
  </div>
  <div class="mt-3 flex gap-1.5 overflow-x-auto pb-1">
    <button wire:click="$set('category', '')" class="flex shrink-0 items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-semibold {{ $category === '' ? 'bg-brand-600 text-white dark:bg-brand-500' : 'bg-stone-100 text-stone-600 hover:bg-stone-200 dark:bg-stone-800 dark:text-stone-300 dark:hover:bg-stone-700' }}"><flux:icon.layout-grid class="size-3.5" /> All</button>
    @foreach($cats as $c)<button wire:click="$set('category', '{{ $c->slug }}')" class="flex shrink-0 items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-semibold {{ $category === $c->slug ? 'bg-brand-600 text-white dark:bg-brand-500' : 'bg-stone-100 text-stone-600 hover:bg-stone-200 dark:bg-stone-800 dark:text-stone-300 dark:hover:bg-stone-700' }}"><flux:icon :name="App\Support\CategoryStyle::iconFor($c)" class="size-3.5 {{ $category === $c->slug ? '' : 'text-brand-600 dark:text-white' }}" /> {{ $c->name }}</button>@endforeach
  </div>
  <div wire:loading.remove class="mt-4">
    @if($view === 'list')
      <div class="grid gap-2 md:grid-cols-2">@foreach($offers as $o)<x-gb.offer-row :offer="$o" />@endforeach</div>
    @elseif($view === 'showcase')
      <div class="grid gap-3">@foreach($offers as $i => $o)<x-gb.offer-spotlight :offer="$o" :flip="$i % 2 === 1" />@endforeach</div>
    @else
      <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6">@foreach($offers as $o)<x-gb.offer-card-default :offer="$o" />@endforeach</div>
    @endif
  </div>
  <div wire:loading class="mt-4">
    @if($view === 'list')
      <div class="grid gap-2 md:grid-cols-2">@for($i = 0; $i < 12; $i++)<x-gb.skeleton-row />@endfor</div>
    @elseif($view === 'showcase')
      <div class="grid gap-3">@for($i = 0; $i < 4; $i++)<x-gb.skeleton-card variant="spotlight" />@endfor</div>
    @else
      <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6">@for($i = 0; $i < 12; $i++)<x-gb.skeleton-card />@endfor</div>
    @endif
  </div>
  <div class="mt-4">{{ $offers->links() }}</div>
</div>
