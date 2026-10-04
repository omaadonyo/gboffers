<div class="mx-auto max-w-3xl">
  <div class="flex flex-wrap items-end justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold tracking-tight">Wanted board</h1>
      <p class="text-sm text-stone-500 dark:text-stone-400">Post what you want — no account needed. Suppliers compete to serve you.</p>
    </div>
    <button type="button" wire:click="openPost" class="inline-flex h-10 items-center gap-1.5 rounded-full bg-brand-600 px-5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 dark:bg-brand-500 dark:hover:bg-brand-400"><flux:icon.plus class="size-4" /> Post a request</button>
  </div>

  @if(session('ok'))<p class="mt-3 rounded-lg bg-emerald-50 px-3 py-2 text-xs font-medium text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">{{ session('ok') }}</p>@endif

  <div class="mt-4">
    <input type="search" wire:model.live.debounce.300ms="q" placeholder="Filter requests…" aria-label="Filter requests" class="h-10 w-full max-w-md rounded-full border-0 bg-white px-4 text-sm shadow-sm dark:bg-stone-900 dark:text-stone-100">
  </div>

  <div class="mt-3 space-y-2.5">
    @forelse($rows as $r)
      <article class="rounded-lg bg-white p-4 shadow-sm dark:bg-stone-900">
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <h3 class="truncate text-sm font-bold text-stone-900 dark:text-stone-100">{{ $r->title }}</h3>
            <p class="mt-0.5 text-xs text-stone-500 dark:text-stone-400">Budget {{ App\Support\Money::formatUgx($r->budget) }} · {{ $r->location ?? 'Kampala' }} · by {{ $r->authorName() }} · {{ $r->created_at->diffForHumans() }}</p>
          </div>
          <span class="shrink-0 rounded-full bg-stone-100 px-2.5 py-1 text-[11px] font-bold text-stone-600 dark:bg-stone-800 dark:text-stone-300">{{ $r->responses_count }} {{ Str::plural('response', $r->responses_count) }}</span>
        </div>

        @if($r->responses->isNotEmpty())
          <div class="mt-3 space-y-1.5 border-t border-stone-100 pt-3 dark:border-stone-800">
            @foreach($r->responses->sortByDesc('created_at')->take(4) as $resp)
              <div class="flex items-center justify-between gap-2 rounded-lg bg-stone-50 px-3 py-2 text-xs dark:bg-stone-800/70">
                <span class="flex min-w-0 items-center gap-1">
                  <span class="truncate"><strong class="font-semibold text-stone-800 dark:text-stone-100">{{ $resp->displayName() }}</strong> <span class="text-stone-500 dark:text-stone-400">· {{ Str::limit($resp->message, 90) }}</span></span>
                  @if($resp->isVerifiedResponder())<flux:icon.badge-check class="size-3.5 shrink-0 text-emerald-600 dark:text-emerald-400" />@endif
                </span>
                <span class="shrink-0 font-bold text-stone-900 dark:text-white">{{ App\Support\Money::formatUgx($resp->price) }}</span>
              </div>
            @endforeach
          </div>
        @endif

        <div class="mt-2.5">
          <button type="button" wire:click="respond({{ $r->id }})" class="inline-flex items-center gap-1 text-xs font-bold text-brand-700 hover:underline dark:text-brand-300">
            <flux:icon.chat-bubble-left class="size-3.5" /> {{ $responding === $r->id ? 'Cancel' : 'Respond with your price' }}
          </button>
        </div>

        @if($responding === $r->id)
          <form wire:submit="sendResponse" class="mt-2.5 grid gap-2 rounded-lg bg-stone-50 p-3 md:grid-cols-[1fr_150px_auto] dark:bg-stone-800/70">
            <div class="md:col-span-3">
              <input wire:model="r_message" placeholder="e.g. Genuine unit, sealed box, pickup in Kampala" aria-label="Your offer" class="h-10 w-full rounded-full border-0 bg-white px-4 text-sm dark:bg-stone-900 dark:text-stone-100">
              @error('r_message')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <input wire:model="r_price" type="number" min="100" placeholder="Your price UGX" aria-label="Your price" class="h-10 rounded-full border-0 bg-white px-4 text-sm dark:bg-stone-900 dark:text-stone-100">
            <div class="hidden md:block"></div>
            <button class="h-10 rounded-full bg-stone-900 px-4 text-sm font-semibold text-white hover:bg-stone-700 dark:bg-white dark:text-stone-900 dark:hover:bg-stone-200">Send</button>
            @guest
              <input wire:model="r_name" placeholder="Your name" aria-label="Your name" class="h-10 rounded-full border-0 bg-white px-4 text-sm dark:bg-stone-900 dark:text-stone-100">
              <input wire:model="r_contact" placeholder="Phone or email" aria-label="Contact" class="h-10 rounded-full border-0 bg-white px-4 text-sm dark:bg-stone-900 dark:text-stone-100">
              <p class="text-xs text-stone-400 md:col-span-1 dark:text-stone-500">Only shared if the buyer picks you.</p>
            @endguest
            @error('r_price')<p class="text-xs text-red-600 md:col-span-3">{{ $message }}</p>@enderror
            @error('r_name')<p class="text-xs text-red-600 md:col-span-3">{{ $message }}</p>@enderror
          </form>
        @endif
      </article>
    @empty
      <x-gb.empty-state title="No wanted requests" body="Be the first to post what you're looking for." icon="megaphone" />
    @endforelse
  </div>
  <div class="mt-3">{{ $rows->links() }}</div>

  @if($showPost)
    <div class="fixed inset-0 z-50 grid place-items-center p-4" role="dialog" aria-modal="true" aria-label="Post a request">
      <div class="absolute inset-0 bg-black/50" wire:click="closePost"></div>
      <div class="relative w-full max-w-md rounded-2xl bg-white p-5 shadow-2xl sm:p-6 dark:bg-stone-900" x-data @keydown.escape.window="$wire.closePost()">
        <div class="flex items-start justify-between gap-3">
          <div>
            <h2 class="text-base font-bold text-stone-900 dark:text-white">Post a request</h2>
            <p class="mt-0.5 text-xs text-stone-500 dark:text-stone-400">Suppliers will compete with their best price.</p>
          </div>
          <button type="button" wire:click="closePost" aria-label="Close" class="grid size-8 shrink-0 place-items-center rounded-full text-stone-400 hover:bg-stone-100 hover:text-stone-700 dark:hover:bg-stone-800 dark:hover:text-stone-200"><flux:icon.x-mark class="size-4" /></button>
        </div>
        <form wire:submit="save" class="mt-4 space-y-2.5">
          <div>
            <label class="mb-1 block text-xs font-semibold text-stone-600 dark:text-stone-300" for="want-title">What do you want?</label>
            <input id="want-title" wire:model="title" placeholder="e.g. Samsung Galaxy S9" class="h-11 w-full rounded-xl border-0 bg-stone-100 px-4 text-sm dark:bg-stone-800 dark:text-stone-100">
            @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="mb-1 block text-xs font-semibold text-stone-600 dark:text-stone-300" for="want-budget">Budget (UGX · min 1,000)</label>
            <input id="want-budget" wire:model="budget" type="number" min="1000" placeholder="e.g. 850000" class="h-11 w-full rounded-xl border-0 bg-stone-100 px-4 text-sm dark:bg-stone-800 dark:text-stone-100">
            @error('budget')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
          </div>
          @guest
            <div>
              <label class="mb-1 block text-xs font-semibold text-stone-600 dark:text-stone-300" for="want-name">Your name</label>
              <input id="want-name" wire:model="guest_name" placeholder="e.g. Brian K." class="h-11 w-full rounded-xl border-0 bg-stone-100 px-4 text-sm dark:bg-stone-800 dark:text-stone-100">
              @error('guest_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
              <label class="mb-1 block text-xs font-semibold text-stone-600 dark:text-stone-300" for="want-contact">Phone or email <span class="font-normal text-stone-400">(suppliers contact you)</span></label>
              <input id="want-contact" wire:model="guest_contact" placeholder="e.g. 0772…" class="h-11 w-full rounded-xl border-0 bg-stone-100 px-4 text-sm dark:bg-stone-800 dark:text-stone-100">
            </div>
          @endguest
          <button class="h-11 w-full rounded-xl bg-brand-600 text-sm font-bold text-white hover:bg-brand-700 dark:bg-brand-500 dark:hover:bg-brand-400">Post request</button>
          @guest<p class="text-center text-xs text-stone-400 dark:text-stone-500"><a href="{{ route('register') }}" class="font-medium underline">Join free</a> to manage your requests.</p>@endguest
        </form>
      </div>
    </div>
  @endif
</div>
