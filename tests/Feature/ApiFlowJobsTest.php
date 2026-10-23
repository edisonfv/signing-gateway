<?php

namespace Tests\Feature;

use App\Enums\ApplicationType;
use App\Enums\CertificatePeriod;
use App\Enums\ProductType;
use App\Enums\RequestStatus;
use App\Exceptions\ProviderRequestException;
use App\Jobs\CreateProviderSession;
use App\Jobs\IssueCertificate;
use App\Jobs\SubmitFaceLiveness;
use App\Models\ApiClient;
use App\Models\SignatureRequest;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\Concerns\FakesFirmaSegura;
use Tests\TestCase;

class ApiFlowJobsTest extends TestCase
{
    use FakesFirmaSegura, RefreshDatabase;

    public function test_transient_provider_errors_are_retried_without_changing_the_request(): void
    {
        $this->fakeFirmaSegura([self::FS_BASE.'/signature/api/request' => Http::response(['message' => 'Servicio no disponible'], 503)]);
        $request = $this->makeRequest(RequestStatus::submitting());

        try {
            (new CreateProviderSession($request))->handle();
            $this->fail('Se esperaba que el job relanzara el error para reintentar.');
        } catch (ProviderRequestException $exception) {
            $this->assertTrue($exception->isRetryable());
        }

        $this->assertSame('SUBMITTING', $request->fresh()->status->value);
    }

    public function test_business_errors_close_the_request(): void
    {
        $this->fakeFirmaSegura([self::FS_BASE.'/signature/api/request' => Http::response(['message' => 'La ciudad proporcionada no es válida'], 412)]);
        $request = $this->makeRequest(RequestStatus::submitting());

        (new CreateProviderSession($request))->handle();

        $request->refresh();
        $this->assertSame('REJECTED', $request->status->value);
        $this->assertSame('La ciudad proporcionada no es válida', $request->last_error['message']);
        $this->assertNotNull($request->closed_at);
    }

    public function test_liveness_mismatch_reported_as_retryable_session_allows_a_new_attempt(): void
    {
        // Respuesta real observada en el sandbox: 409 con sessionStatus RETRYABLE y sin remainingAttempts.
        $this->fakeFirmaSegura([
            self::FS_BASE.'/signature/api/session/*/face-liveness' => Http::response([
                'status' => 409, 'error' => 'Conflict',
                'message' => 'El rostro del titular no coincide con su cédula; le quedan intentos disponibles.',
                'sessionStatus' => 'RETRYABLE',
            ], 409),
        ]);
        $request = $this->makeRequest(RequestStatus::validating());

        (new SubmitFaceLiveness($request, 'aws-liveness-1'))->handle();

        $request->refresh();
        $this->assertSame('FAILED_RETRYABLE', $request->status->value);
        $this->assertSame('RETRYABLE', $request->provider_status);
        $this->assertNull($request->closed_at);
    }

    public function test_liveness_with_exhausted_attempts_rejects_the_request(): void
    {
        $this->fakeFirmaSegura([
            self::FS_BASE.'/signature/api/session/*/face-liveness' => Http::response([
                'message' => 'Intentos de prueba de vida agotados', 'sessionStatus' => 'REJECTED',
            ], 409),
        ]);
        $request = $this->makeRequest(RequestStatus::validating());

        (new SubmitFaceLiveness($request, 'aws-liveness-1'))->handle();

        $this->assertSame('REJECTED', $request->fresh()->status->value);
    }

    public function test_failed_issuance_is_flagged_for_manual_review(): void
    {
        $request = $this->makeRequest(RequestStatus::validating());

        (new IssueCertificate($request))->failed(new RuntimeException('timeout'));

        $this->assertSame('FAILED', $request->fresh()->status->value);
        $this->assertDatabaseHas('audit_logs', ['action' => 'certificate.issuance_failed', 'subject_id' => $request->id]);
        $this->assertDatabaseHas('client_notifications', ['event' => 'session.failed']);
    }

    private function makeRequest(RequestStatus $status): SignatureRequest
    {
        $tenant = Tenant::create(['external_id' => 'acme', 'name' => 'ACME']);
        $client = ApiClient::create(['tenant_id' => $tenant->id, 'name' => 'c', 'signing_secret' => 's', 'notification_secret' => 'n']);
        $account = $this->createFirmaSeguraAccount();

        $request = SignatureRequest::create([
            'tenant_id' => $tenant->id,
            'api_client_id' => $client->id,
            'provider_account_id' => $account->id,
            'idempotency_key' => 'op-1',
            'product' => ProductType::headless(),
            'application_type' => ApplicationType::naturalPerson(),
            'period' => CertificatePeriod::oneYear(),
            'status' => $status,
            'provider_session_id' => 'fs-session-1',
            'identification_hash' => str_repeat('a', 64),
            'applicant' => [
                'identification_number' => '1710034065', 'finger_code' => 'E4333V2422', 'email' => 'titular@example.com',
                'cellphone' => '+593998765432', 'address' => 'Quito', 'city' => 'QUITO', 'province' => 'PICHINCHA',
            ],
        ]);
        $request->termsAcceptance()->create([
            'provider_terms_id' => self::TERMS_ID, 'version' => '2.0.9', 'content_sha256' => str_repeat('b', 64),
            'accepted_at' => now(), 'applicant_ip' => '190.152.10.20',
        ]);

        return $request;
    }
}
