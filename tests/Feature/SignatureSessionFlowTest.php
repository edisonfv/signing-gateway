<?php

namespace Tests\Feature;

use App\Jobs\CreateProviderSession;
use App\Jobs\ValidateCivilRegistry;
use App\Models\ClientNotification;
use App\Models\SignatureRequest;
use App\Models\Tenant;
use Database\Seeders\ProvinceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\FakesFirmaSegura;
use Tests\Concerns\InteractsWithSignedApi;
use Tests\TestCase;

class SignatureSessionFlowTest extends TestCase
{
    use FakesFirmaSegura, InteractsWithSignedApi, RefreshDatabase;

    private string $privateKeyPair;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ProvinceSeeder::class);
        $this->createFirmaSeguraAccount();
        $this->issueApiCredentials();

        $this->privateKeyPair = sodium_crypto_box_keypair();
        $this->credentials['client']->update(['delivery_public_key' => base64_encode(sodium_crypto_box_publickey($this->privateKeyPair))]);
    }

    public function test_current_terms_are_served_from_the_provider(): void
    {
        $this->fakeFirmaSegura();

        $this->signedGet('/api/v1/terms/current')
            ->assertOk()
            ->assertJsonPath('data.id', self::TERMS_ID)
            ->assertJsonPath('data.content_sha256', hash('sha256', 'Términos de prueba'));
    }

    public function test_full_api_flow_issues_and_delivers_a_sealed_certificate_once(): void
    {
        $this->fakeFirmaSegura();

        $id = $this->signedJson('POST', '/api/v1/signature-sessions', $this->sessionPayload(), ['Idempotency-Key' => 'op-1'])
            ->assertAccepted()
            ->assertJsonPath('data.status', 'AWAITING_APPLICANT')
            ->assertJsonPath('data.step', 'CIVIL_REGISTRY')
            ->json('data.id');

        Http::assertSent(fn (Request $request) => $request->url() === self::FS_BASE.'/signature/api/request'
            && $request['referenceTransaction'] === $id
            && $request['city'] === 'QUITO'
            && $request['province'] === 'PICHINCHA'
            && $request['termsAndConditionsId'] === self::TERMS_ID
            && ! isset($request['password'])
            && $request->hasHeader('Authorization', 'Bearer jwt-sandbox'));

        $this->signedJson('POST', "/api/v1/signature-sessions/{$id}/liveness-session")->assertAccepted();

        $this->signedGet("/api/v1/signature-sessions/{$id}/liveness-credentials")
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertJsonPath('data.liveness_session_id', 'aws-liveness-1')
            ->assertJsonPath('data.credentials.secret_access_key', 'secret-temporal');
        $this->signedGet("/api/v1/signature-sessions/{$id}/liveness-credentials")->assertNotFound();

        $this->signedJson('POST', "/api/v1/signature-sessions/{$id}/liveness-result", ['liveness_session_id' => 'aws-liveness-1'])
            ->assertAccepted();

        $this->signedGet("/api/v1/signature-sessions/{$id}")
            ->assertOk()
            ->assertJsonPath('data.status', 'CERTIFICATE_STORED')
            ->assertJsonPath('data.certificate_available', true);

        $sealed = $this->signedGet("/api/v1/signature-sessions/{$id}/certificate")->assertOk()->json('data.sealed');
        $opened = json_decode(sodium_crypto_box_seal_open(base64_decode($sealed), $this->privateKeyPair), true);

        $this->assertSame(self::P12_PASSWORD, $opened['password']);
        $this->assertTrue(openssl_pkcs12_read(base64_decode($opened['certificate_base64']), $certificates, $opened['password']));

        $this->signedGet("/api/v1/signature-sessions/{$id}/certificate")->assertStatus(410);
        $this->assertNull(DB::table('certificate_vault_entries')->value('certificate'));
        $this->assertSame('DELIVERED', SignatureRequest::find($id)->status->value);
        $this->assertEqualsCanonicalizing(
            ['session.submitted', 'session.awaiting_liveness', 'session.liveness_ready', 'session.liveness_passed', 'certificate.ready'],
            ClientNotification::pluck('event')->all(),
        );
    }

    public function test_creation_dispatches_the_provider_chain_and_stores_encrypted_evidence(): void
    {
        $this->fakeFirmaSegura();
        Bus::fake();

        $id = $this->signedJson('POST', '/api/v1/signature-sessions', $this->sessionPayload(), ['Idempotency-Key' => 'op-1'])
            ->assertAccepted()
            ->assertJsonPath('data.status', 'SUBMITTING')
            ->json('data.id');

        Bus::assertChained([CreateProviderSession::class, ValidateCivilRegistry::class]);
        $this->assertStringNotContainsString('1710034065', DB::table('signature_requests')->value('applicant'));
        $this->assertDatabaseHas('terms_acceptances', ['signature_request_id' => $id, 'provider_terms_id' => self::TERMS_ID, 'version' => '2.0.9']);
        $this->signedGet("/api/v1/signature-sessions/{$id}")->assertJsonMissingPath('data.applicant');
    }

    public function test_same_idempotency_key_returns_the_same_request(): void
    {
        $this->fakeFirmaSegura();
        Bus::fake();

        $first = $this->signedJson('POST', '/api/v1/signature-sessions', $this->sessionPayload(), ['Idempotency-Key' => 'op-1'])->assertAccepted();
        $second = $this->signedJson('POST', '/api/v1/signature-sessions', $this->sessionPayload(), ['Idempotency-Key' => 'op-1'])->assertOk();

        $this->assertSame($first->json('data.id'), $second->json('data.id'));
        $this->assertSame(1, SignatureRequest::count());
    }

    public function test_missing_idempotency_key_is_rejected(): void
    {
        $this->fakeFirmaSegura();

        $this->signedJson('POST', '/api/v1/signature-sessions', $this->sessionPayload())->assertBadRequest();
    }

    public function test_outdated_terms_are_rejected(): void
    {
        $this->fakeFirmaSegura();

        $this->signedJson('POST', '/api/v1/signature-sessions', $this->sessionPayload(['terms' => ['version' => '1.0.0']]), ['Idempotency-Key' => 'op-1'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('terms.id');
    }

    public function test_applicant_data_is_validated(): void
    {
        $this->fakeFirmaSegura();
        config(['gateway.strict_identification' => true]);

        $this->signedJson('POST', '/api/v1/signature-sessions', $this->sessionPayload([
            'application_type' => 'LEGAL_REPRESENTATIVE',
            'applicant' => ['identification_number' => '1712345678', 'cellphone' => '0998765432'],
        ]), ['Idempotency-Key' => 'op-1'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['application_type', 'applicant.identification_number', 'applicant.cellphone']);

        $this->signedJson('POST', '/api/v1/signature-sessions', $this->sessionPayload(['application_type' => 'NATURAL_PERSON_RUC']), ['Idempotency-Key' => 'op-2'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('applicant.person_ruc');

        $this->signedJson('POST', '/api/v1/signature-sessions', $this->sessionPayload([
            'application_type' => 'NATURAL_PERSON_RUC',
            'applicant' => ['person_ruc' => '1710034065001'],
        ]), ['Idempotency-Key' => 'op-3'])->assertAccepted();
    }

    public function test_sandbox_accepts_provider_test_identifications_without_check_digit(): void
    {
        $this->fakeFirmaSegura();
        Bus::fake();
        config(['gateway.strict_identification' => false]);

        $this->signedJson('POST', '/api/v1/signature-sessions', $this->sessionPayload([
            'applicant' => ['identification_number' => '1801020101'],
        ]), ['Idempotency-Key' => 'op-1'])->assertAccepted();

        $this->signedJson('POST', '/api/v1/signature-sessions', $this->sessionPayload([
            'applicant' => ['identification_number' => '18010201'],
        ]), ['Idempotency-Key' => 'op-2'])->assertJsonValidationErrors('applicant.identification_number');
    }

    public function test_underage_applicant_is_rejected_by_the_civil_registry(): void
    {
        $this->fakeFirmaSegura([
            self::FS_BASE.'/signature/api/session/*/civil-registry' => Http::response([
                'status' => 409, 'error' => 'Conflict', 'message' => 'El titular es menor de edad', 'sessionStatus' => 'REJECTED',
            ], 409),
        ]);

        $this->signedJson('POST', '/api/v1/signature-sessions', $this->sessionPayload(), ['Idempotency-Key' => 'op-1'])
            ->assertAccepted()
            ->assertJsonPath('data.status', 'REJECTED')
            ->assertJsonPath('data.error.message', 'El titular es menor de edad')
            ->assertJsonPath('data.error.provider_status', 'REJECTED');

        $this->assertDatabaseHas('client_notifications', ['event' => 'session.rejected']);
    }

    public function test_failed_liveness_with_attempts_left_allows_a_new_attempt_without_issuing(): void
    {
        $this->fakeFirmaSegura([
            self::FS_BASE.'/signature/api/session/*/face-liveness' => Http::sequence()
                ->push(['message' => 'La verificación biométrica falló', 'sessionStatus' => 'FAILED', 'remainingAttempts' => 2], 409)
                ->push(['biometricMatch' => true, 'sessionStatus' => 'IN_PROGRESS', 'livenessConfidence' => 99.0, 'faceMatchSimilarity' => 98.0]),
        ]);

        $id = $this->signedJson('POST', '/api/v1/signature-sessions', $this->sessionPayload(), ['Idempotency-Key' => 'op-1'])->json('data.id');
        $this->signedJson('POST', "/api/v1/signature-sessions/{$id}/liveness-session")->assertAccepted();

        $this->signedJson('POST', "/api/v1/signature-sessions/{$id}/liveness-result", ['liveness_session_id' => 'aws-liveness-1'])->assertAccepted();

        $this->signedGet("/api/v1/signature-sessions/{$id}")
            ->assertJsonPath('data.status', 'FAILED_RETRYABLE')
            ->assertJsonPath('data.remaining_liveness_attempts', 2);
        Http::assertNotSent(fn (Request $request) => str_ends_with($request->url(), '/issuance'));

        $this->signedJson('POST', "/api/v1/signature-sessions/{$id}/liveness-session")->assertAccepted();
        $this->signedJson('POST', "/api/v1/signature-sessions/{$id}/liveness-result", ['liveness_session_id' => 'aws-liveness-1'])->assertAccepted();

        $this->signedGet("/api/v1/signature-sessions/{$id}")->assertJsonPath('data.status', 'CERTIFICATE_STORED');
    }

    public function test_liveness_result_is_refused_when_the_request_is_not_waiting_for_it(): void
    {
        $this->fakeFirmaSegura();
        Bus::fake();

        $id = $this->signedJson('POST', '/api/v1/signature-sessions', $this->sessionPayload(), ['Idempotency-Key' => 'op-1'])->json('data.id');

        $this->signedJson('POST', "/api/v1/signature-sessions/{$id}/liveness-result", ['liveness_session_id' => 'x'])->assertConflict();
        $this->signedGet("/api/v1/signature-sessions/{$id}/certificate")->assertConflict();
    }

    public function test_request_can_be_cancelled_before_issuance(): void
    {
        $this->fakeFirmaSegura();

        $id = $this->signedJson('POST', '/api/v1/signature-sessions', $this->sessionPayload(), ['Idempotency-Key' => 'op-1'])->json('data.id');

        $this->signedJson('POST', "/api/v1/signature-sessions/{$id}/cancel")
            ->assertOk()
            ->assertJsonPath('data.status', 'CANCELLED');

        Http::assertSent(fn (Request $request) => $request->method() === 'PUT' && str_contains($request->url(), "referenceTransaction={$id}"));
        $this->signedJson('POST', "/api/v1/signature-sessions/{$id}/liveness-session")->assertConflict();
    }

    public function test_requests_of_another_tenant_are_not_visible(): void
    {
        $this->fakeFirmaSegura();
        Bus::fake();

        $id = $this->signedJson('POST', '/api/v1/signature-sessions', $this->sessionPayload(), ['Idempotency-Key' => 'op-1'])->json('data.id');

        $this->issueApiCredentials('otra-empresa');

        $this->signedGet("/api/v1/signature-sessions/{$id}")->assertNotFound();
        $this->signedGet("/api/v1/signature-sessions/{$id}/certificate")->assertNotFound();
        $this->signedJson('POST', "/api/v1/signature-sessions/{$id}/cancel")->assertNotFound();
        $this->signedGet('/api/v1/signature-sessions')->assertOk()->assertJsonCount(0, 'data');
        $this->assertSame(2, Tenant::count());
    }
}
