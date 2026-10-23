<?php

namespace Tests\Feature;

use App\Actions\IssueApiClientCredentials;
use App\Models\ApiClient;
use App\Models\AuditLog;
use App\Models\PersonalAccessToken;
use App\Models\Tenant;
use App\Models\User;
use App\Security\RequestSignature;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class ApiClientAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    /** @var array{client: ApiClient, token: string, signing_secret: string, notification_secret: string} */
    private array $credentials;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->tenant = Tenant::create(['external_id' => 'acme', 'name' => 'ACME']);
        $this->credentials = app(IssueApiClientCredentials::class)($this->tenant, 'ACME producción');
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

    public function test_catalogs_are_exposed_from_enums(): void
    {
        $this->signedGet('/api/v1/catalogs/provinces')->assertOk()->assertJsonCount(24, 'data');
        $this->signedGet('/api/v1/catalogs/cities')->assertOk()
            ->assertJsonCount(234, 'data')
            ->assertJsonFragment(['value' => 'CARCHI:BOLÍVAR', 'label' => 'BOLÍVAR', 'province' => 'CARCHI']);
        $this->signedGet('/api/v1/catalogs/unknown')->assertNotFound();
    }

    private function signedGet(string $uri, ?string $secret = null, ?string $nonce = null, ?int $timestamp = null): TestResponse
    {
        $timestamp = (string) ($timestamp ?? now()->getTimestamp());
        $nonce ??= Str::random(32);
        $signature = RequestSignature::sign(
            $secret ?? $this->credentials['signing_secret'],
            RequestSignature::canonical('GET', $uri, $timestamp, $nonce, ''),
        );

        return $this->withToken($this->credentials['token'])
            ->withHeaders(['X-Timestamp' => $timestamp, 'X-Nonce' => $nonce, 'X-Signature' => $signature])
            ->withHeader('Accept', 'application/json')
            ->get($uri);
    }
}
