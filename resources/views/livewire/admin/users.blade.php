<div>
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">Users</h1>
      <p class="text-sm text-zinc-500 dark:text-zinc-400">Customers, merchants and staff — roles, suspension and verification.</p>
    </div>
    <span class="rounded-lg bg-zinc-100 px-2.5 py-1 text-xs font-bold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $users->total() }} total</span>
  </div>

  @if(session('ok'))<p class="mt-3 rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-2 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">{{ session('ok') }}</p>@endif
  @if(session('err'))<p class="mt-3 rounded-lg border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-800 dark:border-red-800 dark:bg-red-950/60 dark:text-red-300">{{ session('err') }}</p>@endif

  <div class="mt-4 flex flex-col gap-2 md:flex-row">
    <input type="search" wire:model.live.debounce.300ms="q" placeholder="Search name or email…" aria-label="Search users" class="h-10 flex-1 rounded-lg border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
    <x-gb.select wire:model.live="role" aria-label="Role" :options="['' => 'All roles'] + collect($roles)->mapWithKeys(fn($r) => [$r->value => $r->value])->toArray()" />
  </div>

  <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
    <table class="w-full min-w-[820px] text-left text-sm">
      <thead>
        <tr class="border-b border-zinc-200 text-xs uppercase tracking-wide text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
          <th class="px-3 py-2.5 font-semibold">User</th>
          <th class="px-3 py-2.5 font-semibold">Activity</th>
          <th class="px-3 py-2.5 font-semibold">Role</th>
          <th class="px-3 py-2.5 font-semibold">State</th>
          <th class="px-3 py-2.5 text-right font-semibold">Actions</th>
        </tr>
      </thead>
      <tbody wire:loading.remove class="divide-y divide-zinc-100 dark:divide-zinc-800">
        @forelse($users as $u)
          <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60">
            <td class="px-3 py-2.5">
              <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $u->name }} @if($u->id === auth()->id())<span class="text-xs text-zinc-400">(you)</span>@endif</p>
              <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $u->email }} · joined {{ $u->created_at->format('d M Y') }}</p>
            </td>
            <td class="px-3 py-2.5 text-zinc-600 dark:text-zinc-300">{{ $u->orders_count }} orders · {{ $u->gang_memberships_count }} groups</td>
            <td class="px-3 py-2.5">
              <x-gb.select wire:change="setRole({{ $u->id }}, $event.target.value)" class="h-8 text-xs" :disabled="$u->id === auth()->id()" :options="collect($roles)->mapWithKeys(fn($r) => [$r->value => $r->value])->toArray()" :value="$u->role" />
            </td>
            <td class="px-3 py-2.5">
              <div class="flex flex-wrap gap-1">
                @if($u->is_suspended)<span class="rounded-md bg-red-100 px-2 py-0.5 text-xs font-bold text-red-700 dark:bg-red-950 dark:text-red-300">suspended</span>@endif
                @if($u->email_verified_at)<span class="rounded-md bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">verified</span>@else<span class="rounded-md bg-amber-100 px-2 py-0.5 text-xs font-bold text-amber-700 dark:bg-amber-950 dark:text-amber-300">unverified</span>@endif
              </div>
            </td>
            <td class="px-3 py-2.5">
              <div class="flex justify-end gap-1.5">
                @if($u->id !== auth()->id() && !$u->isAdmin() && !$u->is_suspended)
                  <form method="POST" action="{{ route('admin.impersonate', $u) }}" class="inline">
                    @csrf
                    <button type="submit" class="rounded-md border border-zinc-300 px-2 py-1 text-xs font-medium hover:bg-zinc-100 dark:border-zinc-600 dark:hover:bg-zinc-800">Login as</button>
                  </form>
                @endif
                <button wire:click="toggleVerified({{ $u->id }})" class="rounded-md border border-zinc-300 px-2 py-1 text-xs font-medium hover:bg-zinc-100 dark:border-zinc-600 dark:hover:bg-zinc-800">{{ $u->email_verified_at ? 'Unverify' : 'Verify' }}</button>
                <button wire:click="toggleSuspend({{ $u->id }})" @if($u->id === auth()->id()) disabled @endif class="rounded-md border border-zinc-300 px-2 py-1 text-xs font-medium hover:bg-zinc-100 disabled:opacity-40 dark:border-zinc-600 dark:hover:bg-zinc-800">{{ $u->is_suspended ? 'Unsuspend' : 'Suspend' }}</button>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="px-3 py-8 text-center text-sm text-zinc-500">No users match.</td></tr>
        @endforelse
      </tbody>
      <tbody wire:loading><tr><td colspan="5"><x-gb.skeleton-table :cols="5" :rows="15" /></td></tr></tbody>
    </table>
  </div>
  <div class="mt-3">{{ $users->links() }}</div>
</div>
