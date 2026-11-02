<?php

namespace Tests\Feature;

use App\Actions\IssueApiClientCredentials;
use App\Models\ApiClient;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\FakesFirmaSegura;
use Tests\Concerns\InteractsWithSignedApi;
use Tests\TestCase;

class PlatformCredentialTest extends TestCase
{
    use FakesFirmaSegura, InteractsWithSignedApi, RefreshDatabase;

    /** @var array{client: ApiClient, token: string, signing_secret: string, notification_secret: string, expires_at: string|null} */
    private array $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createFirmaSeguraAccount();
        $this->issueApiCredentials();
        $platform = Tenant::create(['external_id' => 'plataforma', 'name' => 'Plataforma']);
        $this->admin = app(IssueApiClientCredentials::class)($platform, 'Plataforma: alta', ['10.8.0.2'], abilities: ['clients.manage']);
        $this->credentials = $this->admin;
    }

    public function test_platform_issues_its_service_credential_which_only_reads_the_catalog_and_relays_events(): void
    {
        $response = $this->withServerVariables(['REMOTE_ADDR' => '10.8.0.2'])
            ->signedJson('POST', '/api/v1/admin/platform-credential')
            ->assertCreated()
            ->assertJsonMissingPath('notification_secret');

        $service = ApiClient::findOrFail($response->json('client'));
        $this->assertSame(['10.8.0.2'], $service->allowed_ips);
        $this->assertSame(['platform.service'], $service->tokens()->first()->abilities);

        $this->credentials = ['client' => $service, 'token' => $response->json('token'), 'signing_secret' => $response->json('signing_secret')];
        $this->withServerVariables(['REMOTE_ADDR' => '10.8.0.2'])->signedGet('/api/v1/platform/products')
            ->assertOk()
            ->assertJsonPath('data.0.product', 'HEADLESS');
        $this->withServerVariables(['REMOTE_ADDR' => '10.8.0.2'])->signedGet('/api/v1/admin/issuances')->assertOk();
        $this->withServerVariables(['REMOTE_ADDR' => '10.8.0.2'])->signedGet('/api/v1/admin/products')->assertForbidden();
        $this->withServerVariables(['REMOTE_ADDR' => '10.8.0.2'])->signedJson('PUT', '/api/v1/admin/tenants/nuevo', ['name' => 'Nuevo'])->assertForbidden();
    }

    public function test_retrying_with_the_same_idempotency_key_revokes_the_credential_of_the_failed_attempt(): void
    {
        $payload = [
            'name' => 'acme',
            'delivery_public_key' => base64_encode(sodium_crypto_box_publickey(sodium_crypto_box_keypair())),
            'notification_url' => 'https://app.efirma.test/hook',
        ];

        $first = $this->withServerVariables(['REMOTE_ADDR' => '10.8.0.2'])
            ->signedJson('POST', '/api/v1/admin/tenants/acme/clients', $payload, ['Idempotency-Key' => 'alta-acme-1'])
            ->assertCreated()
            ->json('client');
        $this->withServerVariables(['REMOTE_ADDR' => '10.8.0.2'])
            ->signedJson('POST', '/api/v1/admin/tenants/acme/clients', $payload, ['Idempotency-Key' => 'alta-acme-1'])
            ->assertCreated()
            ->assertJsonPath('replaced_clients', [$first]);

        $this->assertNotNull(ApiClient::find($first)->revoked_at);
        $this->withServerVariables(['REMOTE_ADDR' => '10.8.0.2'])->signedGet('/api/v1/admin/tenants/acme/clients')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonMissingPath('data.0.token')
            ->assertJsonMissingPath('data.0.signing_secret');
    }
}
