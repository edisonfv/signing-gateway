<?php

namespace Tests\Feature;

use App\Actions\IssueApiClientCredentials;
use App\Models\ApiClient;
use App\Models\ProviderAccount;
use App\Models\SignatureRequest;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\FakesFirmaSegura;
use Tests\Concerns\InteractsWithSignedApi;
use Tests\TestCase;

class TenantProductsTest extends TestCase
{
    use FakesFirmaSegura, InteractsWithSignedApi, RefreshDatabase;

    /** @var array{client: ApiClient, token: string, signing_secret: string, notification_secret: string, expires_at: string|null} */
    protected array $platform;

    /** @var array{client: ApiClient, token: string, signing_secret: string, notification_secret: string, expires_at: string|null} */
    protected array $affiliate;

    protected ProviderAccount $account;

    protected function setUp(): void
    {
        parent::setUp();

        $this->account = $this->createFirmaSeguraAccount();
        $this->issueApiCredentials();
        $this->affiliate = $this->credentials;
        $platformTenant = Tenant::create(['external_id' => 'plataforma', 'name' => 'Plataforma']);
        $this->platform = app(IssueApiClientCredentials::class)($platformTenant, 'Plataforma: alta', abilities: ['clients.manage']);
    }

    public function test_admin_catalog_lists_every_offered_product_with_its_provider_accounts(): void
    {
        $this->credentials = $this->platform;

        $this->signedGet('/api/v1/admin/products')
            ->assertOk()
            ->assertJsonPath('data.0.product', 'HEADLESS')
            ->assertJsonPath('data.0.flow', 'LIVENESS_API')
            ->assertJsonPath('data.0.allows_holder_password', true)
            ->assertJsonPath('data.1.product', 'REGULAR')
            ->assertJsonPath('data.1.flow', 'DOCUMENT_REVIEW')
            ->assertJsonPath('data.1.delivery_mode', 'HOLDER')
            ->assertJsonPath('data.1.provider_accounts.0.id', $this->account->getKey());
    }

    public function test_affiliate_credential_cannot_read_or_configure_the_admin_catalog_and_gets_403(): void
    {
        $this->signedGet('/api/v1/admin/products')->assertForbidden();
        $this->signedJson('PUT', '/api/v1/admin/tenants/acme/products', ['products' => []])->assertForbidden();
    }

    public function test_affiliate_sees_and_can_only_request_the_contracted_periods(): void
    {
        $this->fakeFirmaSegura();
        $this->credentials = $this->platform;

        $this->signedJson('PUT', '/api/v1/admin/tenants/acme/products', ['products' => [
            ['product' => 'HEADLESS', 'periods' => ['ONE_YEAR', 'TWO_YEARS']],
            ['product' => 'REGULAR'],
        ]])
            ->assertOk()
            ->assertJsonPath('uses_defaults', false)
            ->assertJsonPath('settings.0.periods', ['ONE_YEAR', 'TWO_YEARS'])
            ->assertJsonCount(2, 'available');

        $this->credentials = $this->affiliate;

        $this->signedGet('/api/v1/products')
            ->assertOk()
            ->assertJsonPath('data.0.periods.*.value', ['ONE_YEAR', 'TWO_YEARS'])
            ->assertJsonPath('data.1.product', 'REGULAR');

        $this->signedJson('POST', '/api/v1/signature-sessions', $this->sessionPayload(['period' => 'ONE_MONTH']), ['Idempotency-Key' => 'op-1'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['period' => 'La vigencia no está disponible para el producto elegido.']);

        $this->signedJson('POST', '/api/v1/signature-sessions', $this->sessionPayload(), ['Idempotency-Key' => 'op-2'])->assertAccepted();
    }

    public function test_products_left_out_of_the_configuration_are_deactivated(): void
    {
        $this->credentials = $this->platform;
        $this->signedJson('PUT', '/api/v1/admin/tenants/acme/products', ['products' => [['product' => 'HEADLESS'], ['product' => 'REGULAR']]])->assertOk();

        $this->signedJson('PUT', '/api/v1/admin/tenants/acme/products', ['products' => [['product' => 'REGULAR']]])
            ->assertOk()
            ->assertJsonPath('available.*.product', ['REGULAR']);

        $this->credentials = $this->affiliate;
        $this->signedGet('/api/v1/products')->assertJsonPath('data.*.product', ['REGULAR']);
    }

    public function test_rejects_periods_the_provider_does_not_offer_and_unavailable_accounts_with_422(): void
    {
        $inactive = ProviderAccount::create([...$this->account->only(['provider', 'environment', 'base_url']), 'name' => 'apagada', 'is_active' => false]);
        $this->credentials = $this->platform;

        $this->signedJson('PUT', '/api/v1/admin/tenants/acme/products', ['products' => [
            ['product' => 'HEADLESS', 'periods' => ['ONE_DAY']],
            ['product' => 'REGULAR', 'preferred_provider_account_id' => $inactive->getKey()],
        ]])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'products.0.periods' => 'Vigencias no ofrecidas por el proveedor: ONE_DAY.',
                'products.1.preferred_provider_account_id' => 'La cuenta no está activa en este ambiente o no ofrece el producto.',
            ]);

        $this->assertSame(0, $this->tenant->products()->count());
    }

    public function test_affiliate_requests_go_to_its_preferred_account_instead_of_the_priority_one(): void
    {
        $this->fakeFirmaSegura();
        $dedicated = ProviderAccount::create([...$this->account->only(['provider', 'environment', 'base_url', 'credentials']), 'name' => 'dedicada', 'priority' => 500]);
        $this->credentials = $this->platform;
        $this->signedJson('PUT', '/api/v1/admin/tenants/acme/products', ['products' => [
            ['product' => 'HEADLESS', 'preferred_provider_account_id' => $dedicated->getKey()],
        ]])->assertOk();

        $this->credentials = $this->affiliate;
        $id = $this->signedJson('POST', '/api/v1/signature-sessions', $this->sessionPayload(), ['Idempotency-Key' => 'op-1'])->assertAccepted()->json('data.id');

        $this->assertTrue(SignatureRequest::find($id)->providerAccount->is($dedicated));
    }

    public function test_simple_product_list_from_the_affiliate_upsert_keeps_configured_periods(): void
    {
        $this->credentials = $this->platform;
        $this->signedJson('PUT', '/api/v1/admin/tenants/acme/products', ['products' => [['product' => 'HEADLESS', 'periods' => ['ONE_YEAR']]]])->assertOk();

        $this->signedJson('PUT', '/api/v1/admin/tenants/acme', ['name' => 'ACME', 'products' => ['HEADLESS', 'REGULAR']])
            ->assertOk()
            ->assertJsonPath('products', ['HEADLESS', 'REGULAR']);

        $this->signedGet('/api/v1/admin/tenants/acme/products')
            ->assertOk()
            ->assertJsonPath('settings.0.periods', ['ONE_YEAR'])
            ->assertJsonPath('settings.1.product', 'REGULAR');
    }

    public function test_terms_come_from_the_account_that_serves_the_requested_product(): void
    {
        $this->fakeFirmaSegura();
        $this->tenant->enableOnly(['HEADLESS', 'REGULAR']);

        $this->signedGet('/api/v1/terms/current?product=REGULAR')->assertOk()->assertJsonPath('data.id', self::TERMS_ID);
        $this->signedGet('/api/v1/terms/current?product=NOPE')->assertUnprocessable();
    }
}
