<x-layouts::app title="Profile — GBOffers">
@php $u = auth()->user(); @endphp
<div class="mx-auto max-w-2xl">
  <div class="flex items-center gap-4 rounded-lg bg-white p-5 shadow-sm dark:bg-stone-900">
    <span class="grid size-14 shrink-0 place-items-center rounded-full bg-stone-900 text-lg font-bold text-white dark:bg-white dark:text-stone-900">{{ $u->initials() }}</span>
    <div class="min-w-0">
      <h1 class="truncate text-xl font-bold tracking-tight">{{ $u->displayName() }}</h1>
      <p class="mt-0.5 text-xs text-stone-500 dark:text-stone-400">Member since {{ $u->created_at->format('M Y') }} · Buyer level {{ $u->buyer_trust ?? '—' }} · {{ $u->profile->phone ?? 'No phone set' }}</p>
      <p class="mt-1 inline-flex items-center gap-1 text-xs font-medium {{ $u->email_verified_at ? 'text-emerald-700 dark:text-emerald-400' : 'text-amber-700 dark:text-amber-400' }}">
        <flux:icon :name="$u->email_verified_at ? 'badge-check' : 'circle-alert'" class="size-3.5" /> {{ $u->email_verified_at ? 'Email verified' : 'Email not verified' }}
      </p>
    </div>
  </div>
  <div class="mt-3 grid gap-2 text-sm">
    @foreach([['My groups','Track the groups you joined','users','/groups'],['GBPass wallet','QR passes ready to redeem','ticket','/wallet'],['Orders','Payment and fulfillment status','package','/orders'],['Wanted requests','Things you asked suppliers for','megaphone','/wanted'],['Manage profile','Name, email, password, security','settings',route('profile.edit')]] as [$t,$s,$i,$h])
      <a href="{{ $h }}" class="flex items-center gap-3 rounded-lg bg-white p-3.5 shadow-sm transition hover:shadow-md dark:bg-stone-900">
        <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-stone-100 text-stone-600 dark:bg-stone-800 dark:text-stone-300"><flux:icon :name="$i" class="size-4" /></span>
        <span class="flex-1"><span class="block font-semibold">{{ $t }}</span><span class="block text-xs text-stone-500 dark:text-stone-400">{{ $s }}</span></span>
        <flux:icon.chevron-right class="size-4 text-stone-400" />
      </a>
    @endforeach
    @if($u->isMerchant() || $u->isAdmin())
      <a href="{{ route('merchant.dashboard') }}" class="flex items-center gap-3 rounded-lg bg-white p-3.5 shadow-sm transition hover:shadow-md dark:bg-stone-900">
        <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-stone-100 text-stone-600 dark:bg-stone-800 dark:text-stone-300"><flux:icon.store class="size-4" /></span>
        <span class="flex-1"><span class="block font-semibold">Merchant dashboard</span><span class="block text-xs text-stone-500 dark:text-stone-400">Offers, orders, scanner, payouts</span></span>
        <flux:icon.chevron-right class="size-4 text-stone-400" />
      </a>
    @endif
    @if($u->isAdmin())
      <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-lg bg-white p-3.5 shadow-sm transition hover:shadow-md dark:bg-stone-900">
        <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-stone-100 text-stone-600 dark:bg-stone-800 dark:text-stone-300"><flux:icon.shield-check class="size-4" /></span>
        <span class="flex-1"><span class="block font-semibold">Admin console</span><span class="block text-xs text-stone-500 dark:text-stone-400">Platform oversight</span></span>
        <flux:icon.chevron-right class="size-4 text-stone-400" />
      </a>
    @endif
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="flex w-full items-center gap-3 rounded-lg border border-red-200 bg-white p-3.5 text-left transition hover:bg-red-50 dark:border-red-900 dark:bg-stone-900 dark:hover:bg-red-950/40">
        <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-red-50 text-red-600 dark:bg-red-950 dark:text-red-400"><flux:icon.log-out class="size-4" /></span>
        <span class="flex-1"><span class="block font-semibold text-red-700 dark:text-red-300">Log out</span><span class="block text-xs text-stone-500 dark:text-stone-400">Sign out of this device</span></span>
      </button>
    </form>
  </div>
</div>
</x-layouts::app>
