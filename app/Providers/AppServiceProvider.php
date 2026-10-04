<?php

namespace App\Providers;

use App\Models\Commission;
use App\Models\Dispute;
use App\Models\Gang;
use App\Models\GbPass;
use App\Models\Merchant;
use App\Models\Offer;
use App\Models\Order;
use App\Models\WantedRequest;
use App\Policies\DisputePolicy;
use App\Policies\GangPolicy;
use App\Policies\GbPassPolicy;
use App\Policies\MerchantPolicy;
use App\Policies\OfferPolicy;
use App\Policies\OrderPolicy;
use App\Policies\WantedRequestPolicy;
use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureMailTemplates();
        date_default_timezone_set(config('gboffers.timezone', 'Africa/Kampala'));

        Gate::policy(Offer::class, OfferPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(GbPass::class, GbPassPolicy::class);
        Gate::policy(Merchant::class, MerchantPolicy::class);
        Gate::policy(Gang::class, GangPolicy::class);
        Gate::policy(Dispute::class, DisputePolicy::class);
        Gate::policy(WantedRequest::class, WantedRequestPolicy::class);
        Gate::define('admin', fn ($u) => ($u->role ?? null) === 'admin');
        Gate::define('merchant', fn ($u) => in_array($u->role ?? null, ['merchant_owner', 'merchant_staff', 'admin'], true));

        RateLimiter::for('join-gang', fn (Request $r) => Limit::perMinute(10)->by($r->user()?->id ?: $r->ip()));
        RateLimiter::for('scanner', fn (Request $r) => Limit::perMinute(60)->by($r->user()?->id ?: $r->ip()));
    }

    /**
     * Branded, minimal mail templates used across the platform.
     */
    protected function configureMailTemplates(): void
    {
        \Illuminate\Auth\Notifications\VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Verify your GBOffers email')
                ->greeting('Hello '.($notifiable->displayName() ?? $notifiable->name).',')
                ->line('Thanks for joining GBOffers — group up, pay less. Please confirm this email address to secure your account.')
                ->action('Verify email address', $url)
                ->line('The link expires in 60 minutes. If you did not create this account, ignore this email.');
        });

        \Illuminate\Auth\Notifications\ResetPassword::toMailUsing(function ($notifiable, $token) {
            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Reset your GBOffers password')
                ->greeting('Hello '.($notifiable->displayName() ?? $notifiable->name).',')
                ->line('We received a password reset request for your account.')
                ->action('Reset password', url(route('password.reset', ['token' => $token, 'email' => $notifiable->getEmailForPasswordReset()], false)))
                ->line('The link expires in 60 minutes. If you did not ask for this, ignore this email.');
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
