<?php

namespace Tests\Feature;

use App\Actions\IssueApiClientCredentials;
use App\Enums\ApplicationType;
use App\Enums\CertificatePeriod;
use App\Enums\ProductType;
use App\Enums\RequestStatus;
use App\Enums\SignatureFlow;
use App\Models\ApiClient;
use App\Models\ClientNotification;
use App\Models\IssuanceLedgerEntry;
use App\Models\ProviderAccount;
use App\Models\SignatureRequest;
use App\Models\Tenant;
use App\Signing\Ledger\IssuanceLedger;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use LogicException;
use Tests\Concerns\FakesFirmaSegura;
use Tests\Concerns\InteractsWithSignedApi;
use Tests\TestCase;

class IssuanceLedgerTest extends TestCase
{
    use FakesFirmaSegura, InteractsWithSignedApi, RefreshDatabase;

    protected ProviderAccount $account;

    protected function setUp(): void
    {
        parent::setUp();

        $this->account = $this->createFirmaSeguraAccount();
        $this->issueApiCredentials();
    }

    public function test_issuing_records_one_chained_entry_and_notifies_once(): void
    {
        $first = $this->makeRequest(RequestStatus::validating());
        $first->transitionTo(RequestStatus::issued(), 'job');
        $first->transitionTo(RequestStatus::certificateStored(), 'job');
        $first->transitionTo(RequestStatus::delivered(), 'api');
        $second = $this->makeRequest(RequestStatus::validating(), ProductType::regular());
        $second->transitionTo(RequestStatus::issued(), 'reconcile');

        $entries = IssuanceLedgerEntry::query()->orderBy('sequence')->get();
        $this->assertSame([1, 2], $entries->pluck('sequence')->all());
        $this->assertNull($entries[0]->previous_hash);
        $this->assertSame($entries[0]->hash, $entries[1]->previous_hash);
        $this->assertSame('REGULAR', $entries[1]->product->value);
        $this->assertSame('fs-session-'.$first->id, $entries[0]->provider_reference);
        $this->assertSame(1, ClientNotification::where('event', 'certificate.issued')->where('signature_request_id', $first->id)->count());
        $this->assertNull(app(IssuanceLedger::class)->verify());
    }

    public function test_ledger_refuses_updates_and_deletes_even_through_sql(): void
    {
        $request = $this->makeRequest(RequestStatus::validating());
        $request->transitionTo(RequestStatus::issued(), 'job');
        $entry = IssuanceLedgerEntry::sole();

        try {
            $entry->update(['period' => CertificatePeriod::fiveYears()]);
            $this->fail('El modelo permitió editar una emisión.');
        } catch (LogicException) {
        }

        $this->expectException(QueryException::class);
        DB::table('issuance_ledger')->where('id', $entry->id)->delete();
    }

    public function test_verify_command_detects_an_altered_entry_and_fails(): void
    {
        foreach (range(1, 3) as $i) {
            $this->makeRequest(RequestStatus::validating())->transitionTo(RequestStatus::issued(), 'job');
        }
        $this->artisan('gateway:ledger:verify')->expectsOutputToContain('íntegro: 3 entradas')->assertSuccessful();

        // Simula a alguien con acceso directo a la base que desactiva el trigger.
        DB::statement('ALTER TABLE issuance_ledger DISABLE TRIGGER issuance_ledger_append_only');
        DB::table('issuance_ledger')->where('sequence', 2)->update(['period' => 'FIVE_YEARS']);

        $this->artisan('gateway:ledger:verify')
            ->expectsOutputToContain('correlativo 2: el contenido no coincide con su hash')
            ->assertFailed();
        $this->assertDatabaseHas('audit_logs', ['action' => 'issuance_ledger.tampered']);
    }

    public function test_verify_detects_a_deleted_entry_by_the_gap_in_the_sequence(): void
    {
        foreach (range(1, 3) as $i) {
            $this->makeRequest(RequestStatus::validating())->transitionTo(RequestStatus::issued(), 'job');
        }
        DB::statement('ALTER TABLE issuance_ledger DISABLE TRIGGER issuance_ledger_append_only');
        DB::table('issuance_ledger')->where('sequence', 2)->delete();

        $this->assertSame(['sequence' => 3, 'reason' => 'falta el correlativo 2'], app(IssuanceLedger::class)->verify());
    }

    public function test_backfill_records_past_issuances_in_order_without_notifying(): void
    {
        $later = $this->makeRequest(RequestStatus::delivered(), issuedAt: now()->subDays(2));
        $earlier = $this->makeRequest(RequestStatus::issued(), ProductType::regular(), issuedAt: now()->subDays(5));
        $this->makeRequest(RequestStatus::rejected());

        $this->artisan('gateway:ledger:backfill')->expectsOutputToContain('Emisiones registradas: 2')->assertSuccessful();
        $this->artisan('gateway:ledger:backfill')->expectsOutputToContain('Emisiones registradas: 0')->assertSuccessful();

        $this->assertSame([$earlier->id, $later->id], IssuanceLedgerEntry::query()->orderBy('sequence')->pluck('signature_request_id')->all());
        $this->assertSame(0, ClientNotification::where('event', 'certificate.issued')->count());
        $this->assertNull(app(IssuanceLedger::class)->verify());
    }

    public function test_platform_lists_issuances_incrementally_and_by_tenant(): void
    {
        $other = Tenant::create(['external_id' => 'otro', 'name' => 'Otro']);
        foreach ([$this->tenant, $other, $this->tenant] as $tenant) {
            $this->makeRequest(RequestStatus::validating(), tenant: $tenant)->transitionTo(RequestStatus::issued(), 'job');
        }
        $this->asPlatform();

        $this->signedGet('/api/v1/admin/issuances?filter[after_sequence]=1')
            ->assertOk()
            ->assertJsonPath('data.*.sequence', [2, 3])
            ->assertJsonPath('data.0.tenant_external_id', 'otro')
            ->assertJsonMissingPath('data.0.identification_hash');

        $this->signedGet('/api/v1/admin/issuances?filter[tenant]=acme')
            ->assertOk()
            ->assertJsonPath('data.*.sequence', [1, 3]);
    }

    public function test_summary_counts_issuances_per_tenant_product_and_period(): void
    {
        $this->makeRequest(RequestStatus::validating())->transitionTo(RequestStatus::issued(), 'job');
        $this->makeRequest(RequestStatus::validating())->transitionTo(RequestStatus::issued(), 'job');
        $this->makeRequest(RequestStatus::validating(), ProductType::regular())->transitionTo(RequestStatus::issued(), 'job');
        $old = $this->makeRequest(RequestStatus::validating(), issuedAt: now()->subMonths(2));
        $old->transitionTo(RequestStatus::issued(), 'job');
        $this->asPlatform();
        $from = now()->startOfMonth()->toDateString();

        $this->signedGet("/api/v1/admin/issuances/summary?filter[issued_from]={$from}")
            ->assertOk()
            ->assertJsonPath('total', 3)
            ->assertJsonPath('data', [
                ['tenant_external_id' => 'acme', 'provider' => 'firmasegura', 'provider_account_id' => $this->account->id, 'product' => 'HEADLESS', 'period' => 'ONE_YEAR', 'count' => 2],
                ['tenant_external_id' => 'acme', 'provider' => 'firmasegura', 'provider_account_id' => $this->account->id, 'product' => 'REGULAR', 'period' => 'ONE_YEAR', 'count' => 1],
            ]);
    }

    public function test_affiliate_credential_cannot_read_the_ledger_and_gets_403(): void
    {
        $this->signedGet('/api/v1/admin/issuances')->assertForbidden();
        $this->signedGet('/api/v1/admin/issuances/summary')->assertForbidden();
    }

    private function asPlatform(): void
    {
        $this->credentials = app(IssueApiClientCredentials::class)(null, 'Plataforma: alta', abilities: ['clients.manage']);
    }

    private function makeRequest(RequestStatus $status, ?ProductType $product = null, ?Tenant $tenant = null, mixed $issuedAt = null): SignatureRequest
    {
        $product ??= ProductType::headless();
        $tenant ??= $this->tenant;
        $client = $tenant->is($this->tenant)
            ? $this->credentials['client']
            : ApiClient::create(['tenant_id' => $tenant->id, 'name' => 'c', 'signing_secret' => 's', 'notification_secret' => 'n']);
        $id = (string) Str::uuid7();

        return SignatureRequest::forceCreate([
            'id' => $id,
            'tenant_id' => $tenant->id,
            'api_client_id' => $client->id,
            'provider_account_id' => $this->account->id,
            'idempotency_key' => (string) Str::uuid(),
            'product' => $product,
            'flow' => $product->equals(ProductType::regular()) ? SignatureFlow::documentReview() : SignatureFlow::livenessApi(),
            'application_type' => ApplicationType::naturalPerson(),
            'period' => CertificatePeriod::oneYear(),
            'status' => $status,
            'provider_session_id' => "fs-session-{$id}",
            'identification_hash' => str_repeat('a', 64),
            'applicant' => [],
            'issued_at' => $issuedAt,
        ]);
    }
}
