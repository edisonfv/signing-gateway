<?php

namespace App\Providers;

use App\Jobs\CreateProviderSession;
use App\Jobs\DeliverClientNotification;
use App\Jobs\IssueCertificate;
use App\Jobs\RequestLivenessSession;
use App\Jobs\SubmitFaceLiveness;
use App\Jobs\ValidateCivilRegistry;
use App\Models\ApiClient;
use App\Models\PersonalAccessToken;
use App\Security\BlindIndex;
use App\Security\KeyRing;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(KeyRing::class);
        $this->app->singleton(BlindIndex::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        Model::shouldBeStrict(! $this->app->isProduction());

        Queue::route(DeliverClientNotification::class, queue: 'notifications');
        Queue::route([
            CreateProviderSession::class,
            ValidateCivilRegistry::class,
            RequestLivenessSession::class,
            SubmitFaceLiveness::class,
            IssueCertificate::class,
        ], queue: 'interactive');

        RateLimiter::for('api-client', function (Request $request) {
            $client = $request->user();

            return $client instanceof ApiClient
                ? Limit::perMinute(120)->by($client->getKey())
                : Limit::perMinute(10)->by($request->ip());
        });

        // Webhooks del proveedor: solo avisan; un tope por IP frena el ruido.
        RateLimiter::for('webhooks', fn (Request $request) => Limit::perMinute(300)->by($request->ip()));
    }
}
