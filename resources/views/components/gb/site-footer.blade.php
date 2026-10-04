@php
$cats = App\Models\Category::where('is_active', true)->orderBy('sort')->take(5)->get();
@endphp
<footer class="border-t border-stone-200 bg-white dark:border-stone-800 dark:bg-stone-950">
    <div class="mx-auto grid w-full max-w-full gap-8 px-4 py-10 sm:grid-cols-2 md:px-6 lg:grid-cols-5">
        <div class="lg:col-span-2">
            <a href="/" class="flex items-center gap-2">
                <span class="grid size-7 place-items-center rounded-md bg-brand-600 text-xs font-bold text-white dark:bg-brand-500">GB</span>
                <span class="text-[15px] font-bold tracking-tight text-stone-900 dark:text-white">GBOffers</span>
            </a>
            <p class="mt-3 max-w-xs text-sm text-stone-500 dark:text-stone-400">Group up. Pay less. Group buying for Uganda — join groups, unlock wholesale prices, and redeem with GBPass.</p>
            <div class="mt-4 flex items-center gap-1.5">
                <a href="#" aria-label="X" class="grid size-8 place-items-center rounded-lg text-stone-500 hover:bg-stone-100 hover:text-stone-900 dark:text-stone-400 dark:hover:bg-stone-800 dark:hover:text-white"><flux:icon.twitter class="size-4" /></a>
                <a href="#" aria-label="Facebook" class="grid size-8 place-items-center rounded-lg text-stone-500 hover:bg-stone-100 hover:text-stone-900 dark:text-stone-400 dark:hover:bg-stone-800 dark:hover:text-white"><flux:icon.facebook class="size-4" /></a>
                <a href="#" aria-label="Instagram" class="grid size-8 place-items-center rounded-lg text-stone-500 hover:bg-stone-100 hover:text-stone-900 dark:text-stone-400 dark:hover:bg-stone-800 dark:hover:text-white"><flux:icon.instagram class="size-4" /></a>
                <a href="#" aria-label="WhatsApp" class="grid size-8 place-items-center rounded-lg text-stone-500 hover:bg-stone-100 hover:text-stone-900 dark:text-stone-400 dark:hover:bg-stone-800 dark:hover:text-white"><flux:icon.message-circle class="size-4" /></a>
            </div>
            <div class="mt-4 flex flex-wrap gap-1.5">
                <span class="rounded-md bg-stone-100 px-2 py-1 text-[11px] font-bold text-stone-600 dark:bg-stone-800 dark:text-stone-300">MTN MoMo</span>
                <span class="rounded-md bg-stone-100 px-2 py-1 text-[11px] font-bold text-stone-600 dark:bg-stone-800 dark:text-stone-300">Airtel Money</span>
                <span class="rounded-md bg-stone-100 px-2 py-1 text-[11px] font-bold text-stone-600 dark:bg-stone-800 dark:text-stone-300">GBPass QR</span>
            </div>
        </div>
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-stone-400 dark:text-stone-500">Shop</p>
            <ul class="mt-3 space-y-2 text-sm">
                <li><a href="/explore" class="text-stone-600 hover:text-stone-900 hover:underline dark:text-stone-300 dark:hover:text-white">Explore offers</a></li>
                @foreach($cats as $cat)
                    <li><a href="{{ route('explore', ['category' => $cat->slug]) }}" class="text-stone-600 hover:text-stone-900 hover:underline dark:text-stone-300 dark:hover:text-white">{{ $cat->name }}</a></li>
                @endforeach
                <li><a href="/wanted" class="text-stone-600 hover:text-stone-900 hover:underline dark:text-stone-300 dark:hover:text-white">Wanted board</a></li>
            </ul>
        </div>
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-stone-400 dark:text-stone-500">Sell</p>
            <ul class="mt-3 space-y-2 text-sm">
                <li><a href="{{ route('register') }}" class="text-stone-600 hover:text-stone-900 hover:underline dark:text-stone-300 dark:hover:text-white">Become a merchant</a></li>
                <li><a href="{{ route('merchant.dashboard') }}" class="text-stone-600 hover:text-stone-900 hover:underline dark:text-stone-300 dark:hover:text-white">Merchant dashboard</a></li>
                <li><a href="{{ route('merchant.scan') }}" class="text-stone-600 hover:text-stone-900 hover:underline dark:text-stone-300 dark:hover:text-white">GBPass scanner</a></li>
                <li><a href="/groups" class="text-stone-600 hover:text-stone-900 hover:underline dark:text-stone-300 dark:hover:text-white">My groups</a></li>
            </ul>
        </div>
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-stone-400 dark:text-stone-500">Support</p>
            <ul class="mt-3 space-y-2 text-sm">
                <li><a href="/wallet" class="text-stone-600 hover:text-stone-900 hover:underline dark:text-stone-300 dark:hover:text-white">GBPass wallet</a></li>
                <li><a href="#" class="text-stone-600 hover:text-stone-900 hover:underline dark:text-stone-300 dark:hover:text-white">Help center</a></li>
                <li><a href="#" class="text-stone-600 hover:text-stone-900 hover:underline dark:text-stone-300 dark:hover:text-white">Terms &amp; refunds</a></li>
                <li><a href="#" class="text-stone-600 hover:text-stone-900 hover:underline dark:text-stone-300 dark:hover:text-white">Privacy</a></li>
                <li><a href="#" class="text-stone-600 hover:text-stone-900 hover:underline dark:text-stone-300 dark:hover:text-white">Contact us</a></li>
            </ul>
        </div>
    </div>
    <div class="border-t border-stone-200 dark:border-stone-800">
        <div class="mx-auto flex w-full max-w-full flex-col items-center justify-between gap-2 px-4 py-4 text-xs text-stone-500 sm:flex-row md:px-6 dark:text-stone-400">
            <p>© {{ date('Y') }} GBOffers · Kampala, Uganda</p>
            <p class="flex items-center gap-1.5"><flux:icon.map-pin class="size-3.5" /> Prices in UGX · English</p>
        </div>
    </div>
</footer>
