<?php

namespace Tests\Feature;

use App\Actions\IssueApiClientCredentials;
use App\Models\ApiClient;
use App\Models\AuditLog;
use App\Models\Tenant;
use App\Security\SealedBox;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithSignedApi;
use Tests\TestCase;

class AdminProvisioningTest extends TestCase
{
    use InteractsWithSignedApi, RefreshDatabase;

    /** @var array{client: ApiClient, token: string, signing_secret: string, notification_secret: string, expires_at: string|null} */
    protected array $platform;

    protected function setUp(): void
    {
        parent::setUp();

        // A regular client (acme) and the platform's provisioning credential.
        $this->issueApiCredentials();
        $platformTenant = Tenant::create(['external_id' => 'plataforma', 'name' => 'Plataforma']);
        $this->platform = app(IssueApiClientCredentials::class)($platformTenant, 'Plataforma: alta', abilities: ['clients.manage']);
    }

    protected function asPlatform(): void
    {
        $this->credentials = $this->platform;
    }

    protected function publicKey(): string
    {
        return base64_encode(sodium_crypto_box_publickey(sodium_crypto_box_keypair()));
    }

    public function test_the_platform_registers_an_affiliate_once_and_updates_it()
    {
        $this->asPlatform();
        $id = '9d0a6b2e-1111-4a1a-8a1a-000000000001';

        $this->signedJson('PUT', "/api/v1/admin/tenants/{$id}", ['name' => 'Comercial Demo'])
            ->assertCreated()
            ->assertJsonPath('external_id', $id)
            ->assertJsonPath('products', config('gateway.default_products'));

        $this->signedJson('PUT', "/api/v1/admin/tenants/{$id}", ['name' => 'Comercial Demo S.A.', 'products' => ['HEADLESS', 'REGULAR']])
            ->assertOk()
            ->assertJsonPath('name', 'Comercial Demo S.A.')
            ->assertJsonPath('products', ['HEADLESS', 'REGULAR']);

        $this->assertSame(1, Tenant::query()->where('external_id', $id)->count());
        $this->signedJson('PUT', "/api/v1/admin/tenants/{$id}", ['name' => 'X', 'products' => ['NOPE']])->assertUnprocessable();
    }

    public function test_the_platform_issues_a_client_credential_that_works_and_cannot_provision()
    {
        $this->asPlatform();
        $id = '9d0a6b2e-1111-4a1a-8a1a-000000000002';
        $this->signedJson('PUT', "/api/v1/admin/tenants/{$id}", ['name' => 'Comercial Demo']);

        $issued = $this->signedJson('POST', "/api/v1/admin/tenants/{$id}/clients", [
            'name' => 'Comercial Demo · 2026-10-26',
            'delivery_public_key' => $key = $this->publicKey(),
            'notification_url' => 'https://demo.efirma.test/webhooks/signing-gateway',
        ])->assertCreated()->json();

        $client = ApiClient::query()->findOrFail($issued['client']);
        $this->assertSame([$key, 'https://demo.efirma.test/webhooks/signing-gateway'], [$client->delivery_public_key, $client->notification_url]);
        $this->assertNotNull($issued['token_expires_at']);
        $this->assertTrue(AuditLog::query()->where('action', 'api_client.issued')->where('actor_id', $this->platform['client']->getKey())->exists());

        // The new credential is a normal client: it signs requests, but cannot provision.
        $this->credentials = ['client' => $client, 'token' => $issued['token'], 'signing_secret' => $issued['signing_secret'], 'notification_secret' => $issued['notification_secret'], 'expires_at' => null];
        $this->signedGet('/api/v1/ping')->assertOk()->assertJsonPath('tenant', $id);
        $this->signedJson('PUT', '/api/v1/admin/tenants/otro', ['name' => 'Otro'])->assertForbidden();
    }

    public function test_an_invalid_delivery_key_or_unknown_affiliate_is_refused()
    {
        $this->asPlatform();

        $this->signedJson('POST', '/api/v1/admin/tenants/no-existe/clients', ['name' => 'X', 'delivery_public_key' => $this->publicKey(), 'notification_url' => 'https://x.test/hook'])->assertNotFound();

        $this->signedJson('POST', '/api/v1/admin/tenants/acme/clients', ['name' => 'X', 'delivery_public_key' => 'no-es-una-llave', 'notification_url' => 'https://x.test/hook'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('delivery_public_key');
    }

    public function test_renewal_revokes_the_old_credential_at_once()
    {
        $old = $this->credentials['client'];
        $this->asPlatform();

        $this->signedJson('DELETE', "/api/v1/admin/clients/{$old->getKey()}")->assertOk()->assertJsonPath('client', $old->getKey());
        $this->assertNotNull($old->fresh()->revoked_at);
        $this->assertSame(0, $old->tokens()->count());

        // It cannot revoke itself.
        $this->signedJson('DELETE', "/api/v1/admin/clients/{$this->platform['client']->getKey()}")->assertStatus(409);
    }

    public function test_a_regular_client_cannot_use_the_provisioning_api()
    {
        $this->signedJson('PUT', '/api/v1/admin/tenants/nuevo', ['name' => 'Nuevo'])->assertForbidden();
        $this->signedJson('DELETE', "/api/v1/admin/clients/{$this->platform['client']->getKey()}")->assertForbidden();
        $this->assertNull($this->platform['client']->fresh()->revoked_at);
    }

    public function test_provisioning_is_never_granted_by_default()
    {
        $default = app(IssueApiClientCredentials::class)(Tenant::query()->where('external_id', 'acme')->sole(), 'Por defecto');

        $this->assertNotContains('clients.manage', $default['client']->tokens()->first()->abilities);
        $this->assertTrue(SealedBox::isValidPublicKey($this->publicKey()));
    }
}
