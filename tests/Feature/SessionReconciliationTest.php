<?php

namespace Tests\Feature;

use App\Enums\ApplicationType;
use App\Enums\CertificatePeriod;
use App\Enums\ProductType;
use App\Enums\RequestStatus;
use App\Enums\SessionStep;
use App\Exceptions\TrackingBudgetExhaustedException;
use App\Jobs\ReconcileSignatureSession;
use App\Models\AuditLog;
use App\Models\ProviderAccount;
use App\Models\SignatureRequest;
use App\Signing\ApiFlow\SessionReconciler;
use App\Signing\ApiFlow\TrackingBudget;
use Database\Seeders\ProvinceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\Concerns\FakesFirmaSegura;
use Tests\Concerns\InteractsWithSignedApi;
use Tests\TestCase;

class SessionReconciliationTest extends TestCase
{
    use FakesFirmaSegura, InteractsWithSignedApi, RefreshDatabase;

    private ProviderAccount $account;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ProvinceSeeder::class);
        $this->account = $this->createFirmaSeguraAccount();
        $this->issueApiCredentials();
    }

    public function test_provider_rejection_closes_the_request_and_notifies_the_client(): void
    {
        $this->fakeTracking('REJECTED', 'BIOMETRIC', 'FACE_LIVENESS_EXHAUSTED', 'Intentos de prueba de vida agotados');
        $request = $this->makeRequest(RequestStatus::failedRetryable());

        app(SessionReconciler::class)->reconcile($request);

        $request->refresh();
        $this->assertSame('REJECTED', $request->status->value);
        $this->assertSame('Intentos de prueba de vida agotados', $request->last_error['message']);
        $this->assertNotNull($request->last_reconciled_at);
        $this->assertDatabaseHas('signature_request_transitions', ['signature_request_id' => $request->id, 'to' => 'REJECTED', 'source' => 'reconcile']);
        $this->assertDatabaseHas('client_notifications', ['event' => 'session.reconciled']);
    }

    public function test_missed_retryable_pause_is_recorded(): void
    {
        $this->fakeTracking('RETRYABLE', 'BIOMETRIC', 'FACE_LIVENESS_FAILED', 'El rostro del titular no coincide con su cédula; le quedan intentos disponibles.');
        $request = $this->makeRequest(RequestStatus::validating());

        app(SessionReconciler::class)->reconcile($request);

        $this->assertSame('FAILED_RETRYABLE', $request->fresh()->status->value);
    }

    public function test_in_progress_session_only_updates_provider_status(): void
    {
        $this->fakeFirmaSegura();
        $request = $this->makeRequest(RequestStatus::awaitingApplicant());

        app(SessionReconciler::class)->reconcile($request);

        $request->refresh();
        $this->assertSame('AWAITING_APPLICANT', $request->status->value);
        $this->assertSame('IN_PROGRESS', $request->provider_status);
        $this->assertSame('BIOMETRIC', $request->provider_stage);
        $this->assertDatabaseCount('client_notifications', 0);
    }

    public function test_completed_session_without_stored_certificate_is_reported_once(): void
    {
        $this->fakeTracking('COMPLETED', 'CERTIFICATE');
        $request = $this->makeRequest(RequestStatus::failed(), SessionStep::issuance());

        app(SessionReconciler::class)->reconcile($request);
        app(SessionReconciler::class)->reconcile($request->fresh());

        $this->assertSame('FAILED', $request->fresh()->status->value);
        $this->assertSame(1, AuditLog::where('action', 'certificate.issued_not_stored')->where('subject_id', $request->id)->count());
        $this->assertDatabaseHas('client_notifications', ['event' => 'session.needs_review']);
    }

    public function test_reconciliation_stops_when_the_hourly_budget_is_exhausted(): void
    {
        config(['gateway.limits.provider_status_hourly' => 1]);
        $this->fakeFirmaSegura();
        $request = $this->makeRequest(RequestStatus::awaitingApplicant());

        app(SessionReconciler::class)->reconcile($request);

        $this->expectException(TrackingBudgetExhaustedException::class);
        app(SessionReconciler::class)->reconcile($request);
    }

    public function test_command_queues_only_due_requests_within_the_budget(): void
    {
        Queue::fake();
        config(['gateway.limits.provider_status_hourly' => 2]);

        $neverReconciled = $this->makeRequest(RequestStatus::awaitingApplicant());
        $dueRecent = $this->makeRequest(RequestStatus::validating(), lastReconciledMinutesAgo: 15);
        $this->makeRequest(RequestStatus::awaitingApplicant(), lastReconciledMinutesAgo: 5);
        $this->makeRequest(RequestStatus::certificateStored());
        $this->makeRequest(RequestStatus::awaitingApplicant(), changedSecondsAgo: 30);
        $extraDue = $this->makeRequest(RequestStatus::failedRetryable(), lastReconciledMinutesAgo: 30);

        $this->artisan('gateway:reconcile-sessions')->assertSuccessful();

        Queue::assertPushed(ReconcileSignatureSession::class, 2);
        Queue::assertPushed(ReconcileSignatureSession::class, fn ($job) => $job->signatureRequest->is($neverReconciled));
        Queue::assertPushed(ReconcileSignatureSession::class, fn ($job) => $job->signatureRequest->is($extraDue));
        Queue::assertNotPushed(ReconcileSignatureSession::class, fn ($job) => $job->signatureRequest->is($dueRecent));
    }

    public function test_old_requests_are_reconciled_less_often(): void
    {
        Queue::fake();
        $old = $this->makeRequest(RequestStatus::awaitingApplicant(), lastReconciledMinutesAgo: 30, createdHoursAgo: 48);
        $oldDue = $this->makeRequest(RequestStatus::awaitingApplicant(), lastReconciledMinutesAgo: 90, createdHoursAgo: 48);

        $this->artisan('gateway:reconcile-sessions')->assertSuccessful();

        Queue::assertPushed(ReconcileSignatureSession::class, fn ($job) => $job->signatureRequest->is($oldDue));
        Queue::assertNotPushed(ReconcileSignatureSession::class, fn ($job) => $job->signatureRequest->is($old));
    }

    public function test_refresh_endpoint_reconciles_on_demand_and_respects_the_budget(): void
    {
        $this->fakeTracking('EXPIRED', 'VALIDATION');
        $request = $this->makeRequest(RequestStatus::awaitingApplicant());

        $this->signedJson('POST', "/api/v1/signature-sessions/{$request->id}/refresh")
            ->assertOk()
            ->assertJsonPath('data.status', 'EXPIRED')
            ->assertJsonPath('data.provider_status', 'EXPIRED');

        config(['gateway.limits.provider_status_hourly' => 1]);
        $this->signedJson('POST', "/api/v1/signature-sessions/{$request->id}/refresh")->assertTooManyRequests();
    }

    public function test_budget_is_tracked_per_provider_account(): void
    {
        $budget = app(TrackingBudget::class);

        $budget->consume($this->account);

        $this->assertSame($budget->limit() - 1, $budget->remaining($this->account));
    }

    private function fakeTracking(string $status, string $stage, ?string $errorCode = null, ?string $detail = null): void
    {
        $this->fakeFirmaSegura([
            self::FS_BASE.'/signature/traditional/session/*/tracking' => Http::response([
                'sessionId' => 'fs-session-1',
                'status' => ['code' => $status, 'label' => $status],
                'stage' => ['code' => $stage, 'label' => $stage],
                'error' => $errorCode ? ['taskLabel' => 'Prueba de Vida', 'code' => $errorCode, 'detail' => $detail] : null,
            ]),
        ]);
    }

    private function makeRequest(
        RequestStatus $status,
        ?SessionStep $step = null,
        ?int $lastReconciledMinutesAgo = null,
        int $changedSecondsAgo = 600,
        int $createdHoursAgo = 1,
    ): SignatureRequest {
        $request = SignatureRequest::create([
            'tenant_id' => $this->tenant->id,
            'api_client_id' => $this->credentials['client']->id,
            'provider_account_id' => $this->account->id,
            'idempotency_key' => (string) Str::uuid(),
            'product' => ProductType::headless(),
            'application_type' => ApplicationType::naturalPerson(),
            'period' => CertificatePeriod::oneYear(),
            'status' => $status,
            'current_step' => $step,
            'provider_session_id' => (string) Str::uuid(),
            'identification_hash' => str_repeat('a', 64),
            'applicant' => [],
        ]);

        $request->forceFill([
            'created_at' => now()->subHours($createdHoursAgo),
            'updated_at' => now()->subSeconds($changedSecondsAgo),
            'last_reconciled_at' => $lastReconciledMinutesAgo === null ? null : now()->subMinutes($lastReconciledMinutesAgo),
        ])->saveQuietly();

        return $request;
    }
}
