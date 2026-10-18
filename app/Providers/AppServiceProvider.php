<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Company;
use App\Models\Order;
use App\Observers\CompanyObserver;
use App\Observers\OrderObserver;
use App\Services\SecurityLogger;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        if (env('APP_ENV') === 'production' || env('APP_FORCE_HTTPS')) {
            URL::forceScheme('https');
        }

        // Custom
        Paginator::useBootstrapFive();

        Model::preventLazyLoading(! $this->app->isProduction());
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());

        Company::observe(CompanyObserver::class);
        Order::observe(OrderObserver::class);

        $this->configureRateLimiting();
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)
                ->by(Str::transliterate(Str::lower((string) $request->input('email')).'|'.$request->ip()))
                ->response(function (Request $request, array $headers) {
                    app(SecurityLogger::class)->log('api_login_throttled', [
                        'attempted_email' => (string) $request->input('email'),
                    ]);

                    return response()->json(['error' => 'Too many login attempts. Please try again later.'], 429, $headers);
                });
        });
    }
}
