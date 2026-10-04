<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <x-gb.impersonation-banner />
        <x-gb.verify-banner />
        <flux:sidebar sticky collapsible style="transition: width .3s cubic-bezier(.4,0,.2,1), padding .3s cubic-bezier(.4,0,.2,1);" class="overflow-x-clip border-e border-zinc-200 bg-zinc-50 transition-[width,padding] duration-300 ease-in-out dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group expandable icon="squares-2x2" :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="search" href="/" :current="request()->is('/')">
                        {{ __('Storefront') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
                <flux:sidebar.group expandable icon="circle-user-round" :heading="__('Account')" class="grid">
                    <flux:sidebar.item icon="users" href="/groups" :current="request()->is('groups*')" wire:navigate>
                        {{ __('My groups') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="shopping-cart" href="/orders" :current="request()->is('orders*')" wire:navigate>
                        {{ __('Orders') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="ticket" href="/wallet" :current="request()->is('wallet*')" wire:navigate>
                        {{ __('GBPass wallet') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="chart-bar" href="/analytics" :current="request()->is('analytics*')" wire:navigate>
                        {{ __('My stats') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="cog" href="/profile" :current="request()->is('profile*')" wire:navigate>
                        {{ __('Profile') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
                @if(auth()->user()?->isMerchant() || auth()->user()?->isAdmin())
                    <flux:sidebar.group expandable icon="store" :heading="__('Merchant')" class="grid">
                        <flux:sidebar.item icon="layout-grid" :href="route('merchant.dashboard')" :current="request()->routeIs('merchant.dashboard')" wire:navigate>
                            {{ __('Overview') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="tag" :href="route('merchant.offers')" :current="request()->routeIs('merchant.offers')" wire:navigate>
                            {{ __('Offers') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="package" :href="route('merchant.orders')" :current="request()->routeIs('merchant.orders')" wire:navigate>
                            {{ __('Orders') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="banknotes" :href="route('merchant.payments')" :current="request()->routeIs('merchant.payments')" wire:navigate>
                            {{ __('Payments') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="qr-code" :href="route('merchant.scan')" :current="request()->routeIs('merchant.scan')" wire:navigate>
                            {{ __('Scanner') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="receipt-percent" :href="route('merchant.commissions')" :current="request()->routeIs('merchant.commissions')" wire:navigate>
                            {{ __('Commissions') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="presentation-chart-line" :href="route('merchant.analytics')" :current="request()->routeIs('merchant.analytics')" wire:navigate>
                            {{ __('Analytics') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endif
                @if(auth()->user()?->isAdmin())
                    <flux:sidebar.group expandable icon="shield-check" :heading="__('Admin console')" class="grid">
                        <flux:sidebar.item icon="chart-bar" :href="route('admin.dashboard')" :current="request()->routeIs('admin.dashboard')" wire:navigate>
                            {{ __('Overview') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="tag" :href="route('admin.offers')" :current="request()->routeIs('admin.offers')" wire:navigate>
                            {{ __('Offers') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="users" :href="route('admin.groups')" :current="request()->routeIs('admin.groups')" wire:navigate>
                            {{ __('Groups') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="shopping-cart" :href="route('admin.orders')" :current="request()->routeIs('admin.orders')" wire:navigate>
                            {{ __('Orders') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="user-group" :href="route('admin.users')" :current="request()->routeIs('admin.users')" wire:navigate>
                            {{ __('Users') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="store" :href="route('admin.merchants')" :current="request()->routeIs('admin.merchants')" wire:navigate>
                            {{ __('Merchants') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="shield-exclamation" :href="route('admin.disputes')" :current="request()->routeIs('admin.disputes')" wire:navigate>
                            {{ __('Disputes') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="presentation-chart-bar" :href="route('admin.analytics')" :current="request()->routeIs('admin.analytics')" wire:navigate>
                            {{ __('Analytics') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="eye" :href="route('admin.insights')" :current="request()->routeIs('admin.insights')" wire:navigate>
                            {{ __('Insights') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="sparkles" :href="route('admin.featured')" :current="request()->routeIs('admin.featured')" wire:navigate>
                            {{ __('Featured') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="arrow-down-tray" :href="route('admin.import')" :current="request()->routeIs('admin.import')" wire:navigate>
                            {{ __('Import') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endif
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                    {{ __('Repository') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                    {{ __('Documentation') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
