<?php

namespace Tests\Feature;

use App\Actions\IssueApiClientCredentials;
use App\Enums\ApplicationType;
use App\Enums\CertificatePeriod;
use App\Enums\ProductType;
use App\Enums\RequestStatus;
use App\Enums\SignatureFlow;
use App\Jobs\ReconcileSignatureSession;
use App\Models\ApiClient;
use App\Models\ProviderAccount;
use App\Models\SignatureRequest;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\Concerns\FakesFirmaSegura;
use Tests\Concerns\InteractsWithSignedApi;
use Tests\TestCase;

class ProviderWebhookTest extends TestCase
{
    use FakesFirmaSegura, InteractsWithSignedApi, RefreshDatabase;

    private ProviderAccount $account;

    /** @var array{client: ApiClient, token: string, signing_secret: string, notification_secret: string, expires_at: string|null} */
    private array $affiliate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->account = $this->createFirmaSeguraAccount();
        $this->issueApiCredentials();
        $this->affiliate = $this->credentials;
        Queue::fake();

        // La plataforma reenvía con su credencial de servicio (ability platform.service).
        $platform = Tenant::create(['external_id' => 'plataforma', 'name' => 'Plataforma']);
        $this->credentials = app(IssueApiClientCredentials::class)($platform, 'Plataforma: servicio', abilities: ['platform.service']);
    }

    public function test_the_callback_url_is_only_built_with_a_public_url(): void
    {
        config(['gateway.webhooks.url' => null]);
        $this->assertNull($this->account->webhookUrl());

        config(['gateway.webhooks.url' => 'https://app.ejemplo.test/provider-webhooks/']);
        $this->assertSame(
            "https://app.ejemplo.test/provider-webhooks/firmasegura/{$this->account->id}/{$this->account->webhookToken()}",
            $this->account->webhookUrl(),
        );
    }

    public function test_an_event_brings_the_reconciliation_of_its_session_forward(): void
    {
        $request = $this->makeRequest(RequestStatus::validating());

        $this->signedJson('POST', $this->url(), $this->event($request->provider_session_id))->assertStatus(202);

        Queue::assertPushed(ReconcileSignatureSession::class, fn (ReconcileSignatureSession $job) => $job->signatureRequest->is($request) && $job->source === 'webhook');
        $this->assertSame('VALIDATING', $request->refresh()->status->value);
    }

    public function test_a_repeated_event_is_attended_once(): void
    {
        $request = $this->makeRequest(RequestStatus::validating());
        $event = $this->event($request->provider_session_id);

        $this->signedJson('POST', $this->url(), $event)->assertStatus(202);
        $this->signedJson('POST', $this->url(), $event)->assertStatus(202);

        Queue::assertPushed(ReconcileSignatureSession::class, 1);
    }

    public function test_a_wrong_token_or_unknown_session_does_nothing(): void
    {
        $request = $this->makeRequest(RequestStatus::validating());

        $this->signedJson('POST', "/api/v1/platform/provider-events/firmasegura/{$this->account->id}/otrotoken", $this->event($request->provider_session_id))->assertNotFound();
        $this->signedJson('POST', $this->url(), $this->event((string) Str::uuid()))->assertStatus(202);
        $this->signedJson('POST', $this->url(), ['type' => 'otro', 'subject' => 'x'])->assertStatus(422);

        $closed = $this->makeRequest(RequestStatus::rejected());
        $this->signedJson('POST', $this->url(), $this->event($closed->provider_session_id))->assertStatus(202);

        Queue::assertNothingPushed();
    }

    private function url(): string
    {
        return "/api/v1/platform/provider-events/firmasegura/{$this->account->id}/{$this->account->webhookToken()}";
    }

    /**
     * @return array<string, mixed>
     */
    private function event(string $sessionId): array
    {
        return [
            'specversion' => '1.0',
            'id' => (string) Str::uuid(),
            'source' => '/firmasegura/signature-service',
            'type' => 'ec.firmasegura.signature.session.rejected',
            'subject' => $sessionId,
            'time' => now()->toIso8601String(),
            'datacontenttype' => 'application/json',
            // El contenido no se cree: el estado sale del tracking.
            'data' => ['code' => 'FACE_LIVENESS_EXHAUSTED', 'message' => 'Rechazada'],
        ];
    }

    private function makeRequest(RequestStatus $status): SignatureRequest
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
            'provider_session_id' => (string) Str::uuid(),
            'identification_hash' => str_repeat('a', 64),
            'applicant' => [],
        ]);
    }
}
