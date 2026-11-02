<?php

namespace Tests\Feature;

use App\Actions\IssueApiClientCredentials;
use App\Actions\PairPlatform;
use App\Models\ApiClient;
use App\Models\PairingCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\Concerns\InteractsWithSignedApi;
use Tests\TestCase;

class PlatformPairingTest extends TestCase
{
    use InteractsWithSignedApi, RefreshDatabase;

    public function test_a_code_is_redeemed_once_for_a_platform_credential_locked_to_the_caller_ip(): void
    {
        $code = app(PairPlatform::class)->generate();

        $response = $this->withServerVariables(['REMOTE_ADDR' => '10.8.0.2'])
            ->postJson('/api/v1/pair', ['code' => strtolower($code)])
            ->assertCreated()
            ->assertHeader('Cache-Control', 'no-store, private');

        $client = ApiClient::findOrFail($response->json('client'));
        $this->assertTrue($client->isPlatform());
        $this->assertSame(['10.8.0.2'], $client->allowed_ips);
        $this->assertSame(['clients.manage'], $client->tokens()->first()->abilities);
        $this->assertDatabaseMissing('pairing_codes', ['code_hash' => PairingCode::normalize($code)]);

        $this->credentials = ['client' => $client, 'token' => $response->json('token'), 'signing_secret' => $response->json('signing_secret')];
        $this->withServerVariables(['REMOTE_ADDR' => '10.8.0.2'])->signedGet('/api/v1/ping')->assertOk()->assertJsonPath('platform', true);

        $this->withServerVariables(['REMOTE_ADDR' => '10.8.0.2'])->postJson('/api/v1/pair', ['code' => $code])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Código inválido, usado o vencido. Genere uno nuevo con php artisan gateway:pair.');
    }

    public function test_an_expired_code_is_rejected_with_422(): void
    {
        $code = app(PairPlatform::class)->generate(minutes: 1);
        $this->travel(2)->minutes();

        $this->postJson('/api/v1/pair', ['code' => $code])->assertUnprocessable();
        $this->assertSame(0, ApiClient::query()->count());
    }

    public function test_five_wrong_attempts_invalidate_the_active_code(): void
    {
        $code = app(PairPlatform::class)->generate();

        foreach (range(1, PairPlatform::MAX_FAILED_ATTEMPTS) as $attempt) {
            RateLimiter::clear('pairing');
            app(PairPlatform::class)->redeem('ZZZZ-ZZZZ', '203.0.113.9');
        }

        $this->postJson('/api/v1/pair', ['code' => $code])->assertUnprocessable();
        $this->assertDatabaseHas('audit_logs', ['action' => 'platform.pairing_locked']);
    }

    public function test_pairing_again_revokes_the_previous_platform_credentials(): void
    {
        $old = app(IssueApiClientCredentials::class)(null, 'Plataforma: alta', abilities: ['clients.manage']);
        $oldService = app(IssueApiClientCredentials::class)(null, 'Plataforma: servicio', abilities: ['platform.service']);

        $this->postJson('/api/v1/pair', ['code' => app(PairPlatform::class)->generate()])->assertCreated();

        $this->assertNotNull($old['client']->fresh()->revoked_at);
        $this->assertNotNull($oldService['client']->fresh()->revoked_at);
    }

    public function test_pairing_only_answers_the_platform_network(): void
    {
        config(['gateway.network.allowed_sources' => ['10.8.0.2']]);
        $code = app(PairPlatform::class)->generate();

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.9'])->postJson('/api/v1/pair', ['code' => $code])->assertForbidden();
        $this->withServerVariables(['REMOTE_ADDR' => '10.8.0.2'])->postJson('/api/v1/pair', ['code' => $code])->assertCreated();
    }

    public function test_an_affiliate_credential_can_never_get_platform_abilities(): void
    {
        $this->issueApiCredentials();

        $this->expectException(\InvalidArgumentException::class);
        app(IssueApiClientCredentials::class)($this->tenant, 'intento', abilities: ['clients.manage']);
    }
}
