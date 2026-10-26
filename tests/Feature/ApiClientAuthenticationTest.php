<?php

namespace Tests\Feature;

use App\Actions\IssueApiClientCredentials;
use App\Models\AuditLog;
use App\Models\PersonalAccessToken;
use App\Models\User;
use App\Security\RequestSignature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Tests\Concerns\InteractsWithSignedApi;
use Tests\TestCase;

class ApiClientAuthenticationTest extends TestCase
{
    use InteractsWithSignedApi, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->issueApiCredentials();
    }

    public function test_signed_request_with_valid_token_is_accepted(): void
    {
        $this->signedGet('/api/v1/ping')
            ->assertOk()
            ->assertJsonPath('tenant', 'acme')
            ->assertJsonPath('client', $this->credentials['client']->getKey());
    }

    public function test_token_without_signature_is_rejected(): void
    {
        $this->withToken($this->credentials['token'])
            ->getJson('/api/v1/ping')
            ->assertUnauthorized();
    }

    public function test_signature_with_wrong_secret_is_rejected(): void
    {
        $this->signedGet('/api/v1/ping', secret: 'otro-secreto')->assertUnauthorized();

        $this->assertDatabaseHas('audit_logs', ['action' => 'api_client.signature_invalid']);
    }

    public function test_replayed_nonce_is_rejected(): void
    {
        $nonce = Str::random(32);

        $this->signedGet('/api/v1/ping', nonce: $nonce)->assertOk();
        $this->signedGet('/api/v1/ping', nonce: $nonce)->assertUnauthorized();
    }

    public function test_expired_timestamp_is_rejected(): void
    {
        $this->signedGet('/api/v1/ping', timestamp: now()->subMinutes(10)->getTimestamp())->assertUnauthorized();
    }

    public function test_tampered_query_is_rejected(): void
    {
        $timestamp = (string) now()->getTimestamp();
        $nonce = Str::random(32);
        $signature = RequestSignature::sign(
            $this->credentials['signing_secret'],
            RequestSignature::canonical('GET', '/api/v1/catalogs/provinces', $timestamp, $nonce, ''),
        );

        $this->withToken($this->credentials['token'])
            ->withHeaders(['X-Timestamp' => $timestamp, 'X-Nonce' => $nonce, 'X-Signature' => $signature])
            ->withHeader('Accept', 'application/json')
            ->get('/api/v1/catalogs/periods')
            ->assertUnauthorized();
    }

    public function test_revoked_client_is_rejected(): void
    {
        $this->credentials['client']->update(['revoked_at' => now()]);

        $this->signedGet('/api/v1/ping')->assertForbidden();
    }

    public function test_a_catalog_only_credential_reads_the_catalog_but_cannot_issue(): void
    {
        $this->credentials = app(IssueApiClientCredentials::class)($this->tenant, 'Plataforma: catálogo', abilities: ['catalogs.view']);

        $this->signedGet('/api/v1/catalogs/periods')->assertOk();
        $this->signedGet('/api/v1/signature-sessions')->assertForbidden();
        $this->signedJson('POST', '/api/v1/signature-sessions', ['product' => 'HEADLESS'])->assertForbidden();
    }

    public function test_a_credential_cannot_get_abilities_of_staff(): void
    {
        $this->expectException(InvalidArgumentException::class);

        app(IssueApiClientCredentials::class)($this->tenant, 'Intento', abilities: ['providers.manage']);
    }

    public function test_the_revoke_command_disables_the_client_at_once(): void
    {
        $client = $this->credentials['client'];

        $this->artisan('gateway:client:revoke', ['client' => $client->getKey()])->assertSuccessful();

        $this->assertNotNull($client->fresh()->revoked_at);
        $this->assertSame(0, $client->tokens()->count());
        $this->assertTrue(AuditLog::query()->where('action', 'api_client.revoked')->exists());
        $this->signedGet('/api/v1/ping')->assertUnauthorized();
    }

    public function test_suspended_tenant_is_rejected(): void
    {
        $this->tenant->update(['suspended_at' => now()]);

        $this->signedGet('/api/v1/ping')->assertForbidden();
    }

    public function test_ip_outside_allow_list_is_rejected(): void
    {
        $this->credentials['client']->update(['allowed_ips' => ['10.8.0.0/24']]);

        $this->signedGet('/api/v1/ping')->assertForbidden();
        $this->assertTrue(AuditLog::where('action', 'api_client.ip_denied')->exists());
    }

    public function test_user_tokens_cannot_authenticate_as_api_client(): void
    {
        // Los operadores no tienen HasApiTokens; se fuerza un token de otro modelo para
        // comprobar que el guard api solo acepta tokens cuyo dueño es un ApiClient.
        $user = User::factory()->create();
        $plain = Str::random(40);
        $token = PersonalAccessToken::forceCreate([
            'tokenable_type' => $user->getMorphClass(),
            'tokenable_id' => $user->getKey(),
            'name' => 'x',
            'token' => hash('sha256', $plain),
            'abilities' => ['*'],
        ]);

        $this->withToken("{$token->getKey()}|{$plain}")->getJson('/api/v1/ping')->assertUnauthorized();
    }
}
