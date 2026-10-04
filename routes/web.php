<?php
use App\Livewire\Checkout;
use App\Livewire\MyGangs;
use App\Livewire\MyPasses;
use App\Livewire\OfferBrowser;
use App\Livewire\OfferShow;
use App\Livewire\Storefront;
use App\Livewire\WantedIndex;
use Illuminate\Support\Facades\Route;
Route::get('/', Storefront::class)->name('home');
Route::middleware(['auth'])->get('/dashboard', function () {
    $u = auth()->user();
    if (($u->role ?? null) === 'admin') return redirect()->route('admin.dashboard');
    if (in_array($u->role ?? null, ['merchant_owner', 'merchant_staff'], true)) return redirect()->route('merchant.dashboard');
    return response()->view('dashboard');
})->name('dashboard');
Route::get('/explore', OfferBrowser::class)->name('explore');
Route::get('/offers/{offer:slug}', OfferShow::class)->name('offers.show');
Route::get('/wanted', WantedIndex::class)->name('wanted')->middleware('throttle:60,1');
Route::get('/verify/{token}', function (string $token) {
    $p = \App\Models\GbPass::where('token', strtoupper($token))->with(['offer','merchant'])->first();
    abort_unless($p, 404);
    return response()->view('pages.verify', compact('p'));
})->name('passes.verify');
Route::get('/groups', MyGangs::class)->name('groups')->middleware('throttle:120,1');
Route::middleware(['auth','throttle:join-gang'])->group(function () {
    Route::get('/analytics', \App\Livewire\Analytics::class)->name('analytics');
    Route::get('/wallet', MyPasses::class)->name('wallet');
    Route::get('/orders', \App\Livewire\MyOrders::class)->name('orders');
    Route::get('/checkout/{order}', Checkout::class)->name('checkout');
    Route::view('/profile', 'pages.profile')->name('profile');
});
Route::middleware(['auth','role:merchant_owner,merchant_staff,admin'])->prefix('merchant')->name('merchant.')->group(function () {
    Route::get('/', \App\Livewire\Merchant\Dashboard::class)->name('dashboard');
    Route::get('/setup', \App\Livewire\Merchant\Setup::class)->name('setup');
    Route::get('/scan', \App\Livewire\Merchant\Scanner::class)->name('scan')->middleware('throttle:scanner');
    Route::get('/payments', \App\Livewire\Merchant\Payments::class)->name('payments');
    Route::get('/orders', \App\Livewire\Merchant\Orders::class)->name('orders');
    Route::get('/offers', \App\Livewire\Merchant\Offers::class)->name('offers')->middleware('role:merchant_owner,admin');
    Route::get('/commissions', \App\Livewire\Merchant\Commissions::class)->name('commissions');
    Route::get('/analytics', \App\Livewire\Merchant\Analytics::class)->name('analytics');
});
Route::middleware(['auth','role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', \App\Livewire\Admin\Dashboard::class)->name('dashboard');
    Route::get('/offers', \App\Livewire\Admin\Offers::class)->name('offers');
    Route::get('/groups', \App\Livewire\Admin\Gangs::class)->name('groups');
    Route::get('/orders', \App\Livewire\Admin\Orders::class)->name('orders');
    Route::get('/users', \App\Livewire\Admin\Users::class)->name('users');
    Route::get('/merchants', \App\Livewire\Admin\Merchants::class)->name('merchants');
    Route::get('/disputes', \App\Livewire\Admin\Disputes::class)->name('disputes');
    Route::get('/analytics', \App\Livewire\Admin\Analytics::class)->name('analytics');
    Route::get('/insights', \App\Livewire\Admin\Insights::class)->name('insights');
    Route::get('/featured', \App\Livewire\Admin\Featured::class)->name('featured');
    Route::get('/import', \App\Livewire\OfferImporter::class)->name('import');
    Route::post('/impersonate/{user}', function (\App\Models\User $user) {
        abort_if($user->isAdmin() || $user->id === auth()->id() || $user->is_suspended, 403);
        session(['impersonator' => auth()->id()]);
        auth()->login($user);
        return redirect()->route('dashboard');
    })->name('impersonate');
});
Route::middleware(['auth'])->post('/impersonate/stop', function () {
    $id = session('impersonator');
    abort_unless($id && \App\Models\User::where('id', $id)->where('role', 'admin')->exists(), 403);
    session()->forget(['impersonator', 'admin_merchant']);
    auth()->loginUsingId($id);

    return redirect()->route('admin.users');
})->name('impersonate.stop');
require __DIR__.'/settings.php';
// Legacy gang URLs → groups (bookmarks, shares).
Route::redirect('/gangs', '/groups', 301);
Route::redirect('/admin/gangs', '/admin/groups', 301);
// Public merchant storefronts (gboffers.com/@citystyle). Keep last: the handle must not swallow other routes.
Route::get('/@{merchant:slug}', \App\Livewire\MerchantStorefront::class)->name('merchants.show');
