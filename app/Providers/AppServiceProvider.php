<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
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
        $this->configureRateLimiters();
    }

    /**
     * Configure named rate limiters.
     *
     * Chaque limiter possède sa propre clé de cache (préfixée par son nom), donc
     * les compteurs ne se partagent plus entre routes. Sans ça, la forme inline
     * `throttle:max,min` garde une signature `domaine|ip` identique pour tous les
     * visiteurs invités, ce qui fait déborder un compteur commun (→ 429).
     */
    protected function configureRateLimiters(): void
    {
        RateLimiter::for('tracking', fn (Request $request) => Limit::perMinute(200)->by($request->ip()));
        RateLimiter::for('newsletter', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));
        RateLimiter::for('reminder', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));
        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));
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
