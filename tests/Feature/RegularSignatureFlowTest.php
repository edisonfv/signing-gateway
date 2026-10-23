<?php

namespace Tests\Feature;

use App\Models\SignatureRequest;
use App\Signing\ApiFlow\SessionReconciler;
use Database\Seeders\ProvinceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\FakesFirmaSegura;
use Tests\Concerns\InteractsWithSignedApi;
use Tests\TestCase;

class RegularSignatureFlowTest extends TestCase
{
    use FakesFirmaSegura, InteractsWithSignedApi, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('documents');
        $this->seed(ProvinceSeeder::class);
        $this->createFirmaSeguraAccount();
        $this->issueApiCredentials();
        $this->tenant->update(['enabled_products' => ['HEADLESS', 'REGULAR']]);
    }

    public function test_product_catalog_reflects_the_tenant_commercial_conditions(): void
    {
        $this->signedGet('/api/v1/products')
            ->assertOk()
            ->assertJsonPath('data.0.product', 'HEADLESS')
            ->assertJsonPath('data.0.requires_liveness', true)
            ->assertJsonPath('data.1.product', 'REGULAR')
            ->assertJsonPath('data.1.delivery_mode', 'HOLDER')
            ->assertJsonPath('data.1.guides.elderly_video.applies_from_age', 65);

        $this->tenant->update(['enabled_products' => ['HEADLESS']]);

        $this->signedGet('/api/v1/products')->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_product_not_contracted_is_rejected(): void
    {
        $this->fakeFirmaSegura();
        $this->tenant->update(['enabled_products' => ['HEADLESS']]);

        $this->signedJson('POST', '/api/v1/signature-sessions', $this->regularPayload(), ['Idempotency-Key' => 'op-1'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('product');
    }

    public function test_regular_request_is_registered_with_documents_which_are_then_purged(): void
    {
        $this->fakeFirmaSegura();

        $id = $this->signedJson('POST', '/api/v1/signature-sessions', $this->regularPayload([
            'application_type' => 'NATURAL_PERSON_RUC',
            'applicant' => ['person_ruc' => '1710034065001'],
            'documents' => ['company_ruc_pdf' => base64_encode($this->pdf())],
        ]), ['Idempotency-Key' => 'op-1'])
            ->assertAccepted()
            ->assertJsonPath('data.product', 'REGULAR')
            ->assertJsonPath('data.delivery_mode', 'HOLDER')
            ->assertJsonPath('data.status', 'SUBMITTED')
            ->json('data.id');

        Http::assertSent(fn (Request $request) => $request->url() === self::FS_BASE.'/collector/request'
            && $request['referenceTransaction'] === $id
            && $request['applicationType'] === 'NATURAL_PERSON'
            && $request['companyRuc'] === '1710034065001'
            && $request['countryCode'] === 'ECU'
            && $request['documentType'] === 'CI'
            && $request['identificationFront'] === base64_encode($this->png())
            && $request['pdfCompanyRuc'] === base64_encode($this->pdf())
            && ! isset($request['authorizationVideo']));

        $request = SignatureRequest::find($id);
        $this->assertSame('fs-regular-1', $request->provider_request_id);
        $this->assertSame(4, $request->documents()->whereNotNull('purged_at')->count());
        $this->assertSame([], Storage::disk('documents')->allFiles());
    }

    public function test_documents_are_stored_encrypted_until_sent(): void
    {
        $this->fakeFirmaSegura();
        Bus::fake();

        $id = $this->signedJson('POST', '/api/v1/signature-sessions', $this->regularPayload(), ['Idempotency-Key' => 'op-1'])
            ->assertAccepted()
            ->json('data.id');

        $files = Storage::disk('documents')->allFiles();
        $this->assertCount(3, $files);
        $this->assertStringNotContainsString(base64_encode($this->png()), Storage::disk('documents')->get($files[0]));
        $this->assertSame(hash('sha256', $this->png()), SignatureRequest::find($id)->documents()->first()->sha256);
    }

    public function test_legal_representative_requires_company_data_and_documents(): void
    {
        $this->fakeFirmaSegura();
        Bus::fake();

        $this->signedJson('POST', '/api/v1/signature-sessions', $this->regularPayload(['application_type' => 'LEGAL_REPRESENTATIVE']), ['Idempotency-Key' => 'op-1'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('company');

        $this->signedJson('POST', '/api/v1/signature-sessions', $this->regularPayload([
            'application_type' => 'LEGAL_REPRESENTATIVE',
            'company' => ['ruc' => '1790012345001', 'name' => 'ACME S.A.', 'position' => 'Gerente General', 'appointment_expires_at' => now()->addYear()->format('Y-m-d')],
        ]), ['Idempotency-Key' => 'op-2'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['documents.company_ruc_pdf', 'documents.representative_appointment_pdf', 'documents.company_constitution_pdf']);
    }

    public function test_age_rules_reject_minors_and_require_video_from_65(): void
    {
        $this->fakeFirmaSegura();
        Bus::fake();

        $this->signedJson('POST', '/api/v1/signature-sessions', $this->regularPayload(['applicant' => ['birth_date' => now()->subYears(17)->format('Y-m-d')]]), ['Idempotency-Key' => 'op-1'])
            ->assertJsonValidationErrors('applicant.birth_date');

        $this->signedJson('POST', '/api/v1/signature-sessions', $this->regularPayload(['applicant' => ['birth_date' => '1950-01-01']]), ['Idempotency-Key' => 'op-2'])
            ->assertJsonValidationErrors('documents.authorization_video');

        $this->signedJson('POST', '/api/v1/signature-sessions', $this->regularPayload([
            'applicant' => ['birth_date' => '1950-01-01'],
            'documents' => ['authorization_video' => base64_encode($this->mp4())],
        ]), ['Idempotency-Key' => 'op-3'])->assertAccepted();
    }

    public function test_document_content_is_checked_not_its_declared_type(): void
    {
        $this->fakeFirmaSegura();

        $this->signedJson('POST', '/api/v1/signature-sessions', $this->regularPayload([
            'documents' => ['identification_front' => base64_encode($this->pdf()), 'identification_selfie' => 'no-es-base64%%'],
        ]), ['Idempotency-Key' => 'op-1'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['documents.identification_front', 'documents.identification_selfie']);
    }

    public function test_provider_errors_can_be_corrected_and_are_revalidated(): void
    {
        $this->fakeFirmaSegura([
            self::FS_BASE.'/middleware/signature-request/status*' => Http::response([
                'request' => ['id' => 'fs-regular-1', 'status' => 'ERROR'],
                'validation' => ['record' => ['status' => 'ERROR'], 'errors' => [
                    ['code' => 'A01', 'reason' => 'El DNI del registro civil no coincide con el del archivo', 'active' => true, 'managed' => false],
                    ['code' => 'A01', 'reason' => 'La fecha de nacimiento no coincide', 'active' => true, 'managed' => false],
                    ['code' => 'D02', 'reason' => 'Error ya resuelto', 'active' => false, 'managed' => false],
                ]],
            ]),
        ]);
        $id = $this->signedJson('POST', '/api/v1/signature-sessions', $this->regularPayload(), ['Idempotency-Key' => 'op-1'])->json('data.id');

        app(SessionReconciler::class)->reconcile(SignatureRequest::find($id));

        $this->signedGet("/api/v1/signature-sessions/{$id}")
            ->assertJsonPath('data.status', 'FAILED_RETRYABLE')
            ->assertJsonPath('data.error.codes', ['A01']);

        $this->signedJson('POST', "/api/v1/signature-sessions/{$id}/corrections", [
            'documents' => ['identification_front' => base64_encode($this->jpeg())],
        ])->assertOk()->assertJsonPath('data.status', 'VALIDATING');

        Http::assertSent(fn (Request $request) => $request->method() === 'PATCH'
            && $request->url() === self::FS_BASE."/manager/validation-request/request-data/{$id}"
            && $request['identificationFront'] === base64_encode($this->jpeg()));

        $this->signedJson('POST', "/api/v1/signature-sessions/{$id}/corrections", ['finger_code' => 'V3443V3242'])->assertConflict();
    }

    public function test_reconciliation_follows_the_provider_until_the_holder_downloads(): void
    {
        $this->fakeFirmaSegura([
            self::FS_BASE.'/middleware/signature-request/status*' => Http::sequence()
                ->push(['request' => ['id' => 'fs-regular-1', 'status' => 'APPROVED'], 'validation' => ['record' => ['status' => 'COMPLETED']]])
                ->push(['request' => ['id' => 'fs-regular-1', 'status' => 'GENERATED'], 'validation' => ['record' => ['status' => 'COMPLETED']]]),
        ]);
        $id = $this->signedJson('POST', '/api/v1/signature-sessions', $this->regularPayload(), ['Idempotency-Key' => 'op-1'])->json('data.id');

        app(SessionReconciler::class)->reconcile(SignatureRequest::find($id));
        $this->assertSame('ISSUED', SignatureRequest::find($id)->status->value);

        app(SessionReconciler::class)->reconcile(SignatureRequest::find($id));
        $this->signedGet("/api/v1/signature-sessions/{$id}")
            ->assertJsonPath('data.status', 'DELIVERED')
            ->assertJsonPath('data.certificate_available', false);

        $this->signedGet("/api/v1/signature-sessions/{$id}/certificate")->assertConflict();
    }

    public function test_refused_request_is_rejected(): void
    {
        $this->fakeFirmaSegura([
            self::FS_BASE.'/middleware/signature-request/status*' => Http::response([
                'request' => ['id' => 'fs-regular-1', 'status' => 'REFUSED'],
                'validation' => ['record' => ['status' => 'REFUSED', 'automaticCancellationReason' => 'OUTDATED_IDENTIFICATION'], 'errors' => []],
            ]),
        ]);
        $id = $this->signedJson('POST', '/api/v1/signature-sessions', $this->regularPayload(), ['Idempotency-Key' => 'op-1'])->json('data.id');

        app(SessionReconciler::class)->reconcile(SignatureRequest::find($id));

        $this->signedGet("/api/v1/signature-sessions/{$id}")
            ->assertJsonPath('data.status', 'REJECTED')
            ->assertJsonPath('data.error.message', 'OUTDATED_IDENTIFICATION');
    }

    public function test_duplicate_registration_recovers_the_provider_request_id(): void
    {
        $this->fakeFirmaSegura([
            self::FS_BASE.'/collector/request' => Http::response(['status' => 409, 'error' => 'Conflict', 'message' => 'Ya existe el id de transacción'], 409),
            self::FS_BASE.'/middleware/signature-request/status*' => Http::response([
                'request' => ['id' => 'fs-recuperado', 'status' => 'REGISTERED'], 'validation' => ['record' => ['status' => 'PENDING']],
            ]),
        ]);

        $id = $this->signedJson('POST', '/api/v1/signature-sessions', $this->regularPayload(), ['Idempotency-Key' => 'op-1'])
            ->assertJsonPath('data.status', 'SUBMITTED')
            ->json('data.id');

        $this->assertSame('fs-recuperado', SignatureRequest::find($id)->provider_request_id);
        Http::assertSent(fn (Request $request) => str_contains($request->url(), "referenceTransaction={$id}"));
    }

    public function test_liveness_endpoints_and_cancellation_do_not_apply_to_regular_requests(): void
    {
        $this->fakeFirmaSegura();
        $id = $this->signedJson('POST', '/api/v1/signature-sessions', $this->regularPayload(), ['Idempotency-Key' => 'op-1'])->json('data.id');

        $this->signedJson('POST', "/api/v1/signature-sessions/{$id}/liveness-session")->assertConflict();
        $this->signedJson('POST', "/api/v1/signature-sessions/{$id}/cancel")->assertConflict();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function regularPayload(array $overrides = []): array
    {
        return array_replace_recursive($this->sessionPayload([
            'product' => 'REGULAR',
            'applicant' => ['birth_date' => '1990-05-10'],
            'documents' => [
                'identification_front' => base64_encode($this->png()),
                'identification_back' => base64_encode($this->png()),
                'identification_selfie' => base64_encode($this->jpeg()),
            ],
        ]), $overrides);
    }

    private function png(): string
    {
        ob_start();
        imagepng(imagecreatetruecolor(4, 4));

        return ob_get_clean();
    }

    private function jpeg(): string
    {
        ob_start();
        imagejpeg(imagecreatetruecolor(4, 4));

        return ob_get_clean();
    }

    private function pdf(): string
    {
        return "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF\n";
    }

    private function mp4(): string
    {
        return "\x00\x00\x00\x18ftypmp42\x00\x00\x00\x00mp42isom".str_repeat("\x00", 32);
    }
}
