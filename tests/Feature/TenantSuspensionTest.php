<?php

namespace Tests\Feature;

use App\Actions\IssueApiClientCredentials;
use App\Enums\ApplicationType;
use App\Enums\CertificatePeriod;
use App\Enums\ProductType;
use App\Enums\RequestStatus;
use App\Enums\SignatureFlow;
use App\Jobs\CreateProviderSession;
use App\Models\ApiClient;
use App\Models\ProviderAccount;
use App\Models\SignatureRequest;
use App\Signing\EmissionGuard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\Concerns\FakesFirmaSegura;
use Tests\Concerns\InteractsWithSignedApi;
use Tests\TestCase;

class TenantSuspensionTest extends TestCase
{
    use FakesFirmaSegura, InteractsWithSignedApi, RefreshDatabase;

    private ProviderAccount $account;

    /** @var array{client: ApiClient, token: string, signing_secret: string, notification_secret: string, expires_at: string|null} */
    private array $affiliate;

    /** @var array{client: ApiClient, token: string, signing_secret: string, notification_secret: string, expires_at: string|null} */
    private array $platform;

    protected function setUp(): void
    {
        parent::setUp();

        config(['gateway.emission_halt_file' => storage_path('framework/testing/emission-halted-'.Str::random(8))]);
        $this->account = $this->createFirmaSeguraAccount();
        $this->issueApiCredentials();
        $this->affiliate = $this->credentials;
        $this->platform = app(IssueApiClientCredentials::class)(null, 'Plataforma: alta', abilities: ['clients.manage']);
    }

    protected function tearDown(): void
    {
        app(EmissionGuard::class)->resume();

        parent::tearDown();
    }

    public function test_a_queued_job_does_not_call_the_provider_once_the_tenant_is_suspended(): void
    {
        $this->fakeFirmaSegura();
        $request = $this->makeRequest(RequestStatus::submitting());
        $this->tenant->update(['suspended_at' => now()]);

        CreateProviderSession::dispatch($request);

        Http::assertNotSent(fn (Request $sent) => str_contains($sent->url(), '/signature/api/request'));
        $this->assertSame('CANCELLED', $request->fresh()->status->value);
        $this->assertDatabaseHas('client_notifications', ['signature_request_id' => $request->id, 'event' => 'session.cancelled']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'signature_session.stopped', 'subject_id' => $request->id]);
    }

    public function test_suspending_cancels_open_requests_at_the_provider_but_issued_certificates_can_still_be_retrieved(): void
    {
        $this->fakeFirmaSegura();
        $issued = $this->makeRequest(RequestStatus::certificateStored());
        $open = $this->makeRequest(RequestStatus::awaitingApplicant(), providerSessionId: 'fs-session-open');

        $this->credentials = $this->platform;
        $this->signedJson('POST', '/api/v1/admin/tenants/acme/suspend', ['reason' => 'Suscripción cancelada'])
            ->assertOk()
            ->assertJsonPath('suspended', true)
            ->assertJsonPath('blocked', false);

        $this->assertSame('CANCELLED', $open->fresh()->status->value);
        Http::assertSent(fn (Request $sent) => str_contains($sent->url(), '/signature/request/cancel') && str_contains($sent->url(), $open->id));
        $this->assertSame('CERTIFICATE_STORED', $issued->fresh()->status->value);

        $this->credentials = $this->affiliate;
        $this->signedGet("/api/v1/signature-sessions/{$issued->id}")->assertOk();
        $this->signedJson('POST', '/api/v1/signature-sessions', $this->sessionPayload(), ['Idempotency-Key' => 'op-1'])->assertForbidden();
    }

    public function test_blocking_revokes_every_credential_and_denies_even_reading_with_403(): void
    {
        $second = app(IssueApiClientCredentials::class)($this->tenant, 'acme segunda');
        $this->credentials = $this->platform;

        $this->signedJson('POST', '/api/v1/admin/tenants/acme/block')
            ->assertOk()
            ->assertJsonPath('blocked', true)
            ->assertJsonPath('active_credentials', 0);

        $this->assertNotNull($second['client']->fresh()->revoked_at);
        $this->credentials = $this->affiliate;
        $this->signedGet('/api/v1/ping')->assertUnauthorized();

        $this->credentials = $this->platform;
        $this->signedJson('POST', '/api/v1/admin/tenants/acme/resume')->assertOk()->assertJsonPath('suspended', false);
    }

    public function test_halting_emission_rejects_new_requests_with_503_and_stops_queued_jobs(): void
    {
        $this->fakeFirmaSegura();
        $queued = $this->makeRequest(RequestStatus::submitting());

        $this->artisan('gateway:halt', ['--reason' => 'Incidente'])->assertSuccessful();

        $this->signedJson('POST', '/api/v1/signature-sessions', $this->sessionPayload(), ['Idempotency-Key' => 'op-1'])
            ->assertStatus(503);
        CreateProviderSession::dispatch($queued);
        Http::assertNotSent(fn (Request $sent) => str_contains($sent->url(), '/signature/api/request'));
        $this->assertSame('CANCELLED', $queued->fresh()->status->value);

        $this->artisan('gateway:halt', ['--off' => true])->assertSuccessful();
        $this->signedJson('POST', '/api/v1/signature-sessions', $this->sessionPayload(), ['Idempotency-Key' => 'op-2'])->assertAccepted();
    }

    public function test_the_platform_has_no_tenant_routes_and_an_affiliate_cannot_stop_anyone(): void
    {
        $this->credentials = $this->platform;
        $this->signedGet('/api/v1/ping')->assertOk()->assertJsonPath('platform', true)->assertJsonPath('tenant', null);
        $this->signedGet('/api/v1/signature-sessions')
            ->assertForbidden()
            ->assertJsonPath('message', 'Una credencial de la plataforma solo usa /v1/admin y /v1/platform.');

        $this->credentials = $this->affiliate;
        $this->signedJson('POST', '/api/v1/admin/tenants/acme/suspend')->assertForbidden();
    }

    private function makeRequest(RequestStatus $status, ?string $providerSessionId = null): SignatureRequest
    {
        return SignatureRequest::create([
            'tenant_id' => $this->tenant->id,
            'api_client_id' => $this->affiliate['client']->id,
            'provider_account_id' => $this->account->id,
            'idempotency_key' => (string) Str::uuid(),
            'product' => ProductType::headless(),
            'flow' => SignatureFlow::livenessApi(),
            'application_type' => ApplicationType::naturalPerson(),
            'period' => CertificatePeriod::oneYear(),
            'status' => $status,
            'provider_session_id' => $providerSessionId,
            'identification_hash' => str_repeat('a', 64),
            'applicant' => [],
        ]);
    }
}
