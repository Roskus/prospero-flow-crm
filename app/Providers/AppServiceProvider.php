<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Order;
use App\Models\Transaction;
use App\Observers\BranchObserver;
use App\Observers\CompanyObserver;
use App\Observers\OrderObserver;
use App\Observers\TransactionObserver;
use App\Services\SecurityLogger;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
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

        Branch::observe(BranchObserver::class);
        Company::observe(CompanyObserver::class);
        Order::observe(OrderObserver::class);
        Transaction::observe(TransactionObserver::class);

        $this->configureRateLimiting();
        $this->configureSchemaMacros();
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

    /**
     * `$table->address()` creates the address columns read by `App\ValueObjects\Address`.
     */
    protected function configureSchemaMacros(): void
    {
        Blueprint::macro('address', function (): void {
            /** @var Blueprint $this */
            $this->string('country_id', 2)->nullable();
            $this->string('province', 80)->nullable();
            $this->string('city', 50)->nullable();
            $this->string('locality', 80)->nullable();
            $this->string('street', 80)->nullable();
            $this->string('address_extra', 80)->nullable();
            $this->string('zipcode', 10)->nullable();
            $this->decimal('latitude', 10, 8)->nullable();
            $this->decimal('longitude', 11, 8)->nullable();
        });
    }
}
