<?php

namespace Tests\Feature;

use App\Enums\ProductType;
use App\Exceptions\NoProviderAvailableException;
use App\Models\ProviderAccount;
use App\Signing\Drivers\FirmaSegura\FirmaSeguraDriver;
use App\Signing\Routing\ProviderRouter;
use App\Signing\SignatureProviderManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProviderRoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_router_picks_the_active_account_with_highest_priority_in_the_configured_environment(): void
    {
        config(['gateway.environment' => 'sandbox']);
        $this->account(['name' => 'respaldo', 'priority' => 200]);
        $preferred = $this->account(['name' => 'principal', 'priority' => 10]);
        $this->account(['name' => 'inactiva', 'priority' => 1, 'is_active' => false]);
        $this->account(['name' => 'producción', 'priority' => 1, 'environment' => 'production']);

        $this->assertTrue(app(ProviderRouter::class)->accountFor(ProductType::headless())->is($preferred));
    }

    public function test_router_fails_when_no_driver_supports_the_product(): void
    {
        $this->account();

        $this->expectException(NoProviderAvailableException::class);
        app(ProviderRouter::class)->accountFor(ProductType::regular());
    }

    public function test_driver_calls_the_provider_with_the_account_token(): void
    {
        Http::preventStrayRequests();
        Http::fake(['api.dev-firmaseguraec.com/*' => Http::response(['ok' => true])]);
        $account = $this->account(['credentials' => ['token' => 'jwt-de-prueba']]);

        $driver = app(SignatureProviderManager::class)->forAccount($account);
        $this->assertInstanceOf(FirmaSeguraDriver::class, $driver);

        $driver->http()->get('/ping');

        Http::assertSent(fn (Request $request) => $request->url() === 'https://api.dev-firmaseguraec.com/ping'
            && $request->hasHeader('Authorization', 'Bearer jwt-de-prueba'));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function account(array $attributes = []): ProviderAccount
    {
        return ProviderAccount::create([
            'provider' => 'firmasegura',
            'environment' => 'sandbox',
            'name' => 'FS',
            'base_url' => 'https://api.dev-firmaseguraec.com',
            'credentials' => ['token' => 'jwt'],
            ...$attributes,
        ]);
    }
}
