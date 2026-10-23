<?php

namespace Tests\Feature;

use App\Enums\ApplicationType;
use App\Enums\CertificatePeriod;
use App\Enums\ProductType;
use App\Enums\RequestStatus;
use App\Exceptions\InvalidStatusTransition;
use App\Models\ApiClient;
use App\Models\AuditLog;
use App\Models\CertificateVaultEntry;
use App\Models\ProviderAccount;
use App\Models\SignatureRequest;
use App\Models\Tenant;
use App\Security\BlindIndex;
use App\Security\KeyRing;
use App\Security\SealedBox;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use LogicException;
use Tests\TestCase;

class DataProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_models_use_uuid_v7_keys(): void
    {
        $tenant = Tenant::create(['external_id' => 't1', 'name' => 'T1']);

        $this->assertTrue(Str::isUuid($tenant->getKey()));
        $this->assertSame('7', $tenant->getKey()[14]);
    }

    public function test_vault_values_are_not_stored_in_plain_text_and_use_separate_keys(): void
    {
        $request = $this->makeSignatureRequest();

        $entry = CertificateVaultEntry::create([
            'signature_request_id' => $request->getKey(),
            'certificate' => 'P12-BASE64-CONTENT',
            'password' => 'Secreta123',
            'certificate_sha256' => hash('sha256', 'P12-BASE64-CONTENT'),
            'received_at' => now(),
        ]);

        $row = DB::table('certificate_vault_entries')->find($entry->getKey());

        $this->assertStringNotContainsString('P12-BASE64-CONTENT', $row->certificate);
        $this->assertStringNotContainsString('Secreta123', $row->password);

        $keys = app(KeyRing::class);
        $this->assertSame('Secreta123', $keys->encrypter(KeyRing::VAULT_PASSWORD)->decryptString($row->password));
        $this->expectException(DecryptException::class);
        $keys->encrypter(KeyRing::VAULT)->decryptString($row->password);
    }

    public function test_vault_is_not_readable_with_app_key(): void
    {
        $request = $this->makeSignatureRequest();
        $entry = CertificateVaultEntry::create([
            'signature_request_id' => $request->getKey(),
            'certificate' => 'P12',
            'password' => 'x',
            'certificate_sha256' => str_repeat('a', 64),
            'received_at' => now(),
        ]);

        $this->expectException(DecryptException::class);
        decrypt(DB::table('certificate_vault_entries')->value('certificate'), false);
    }

    public function test_delivered_certificates_are_purged_without_deleting_the_row(): void
    {
        $request = $this->makeSignatureRequest();
        $entry = CertificateVaultEntry::create([
            'signature_request_id' => $request->getKey(),
            'certificate' => 'P12',
            'password' => 'x',
            'certificate_sha256' => str_repeat('a', 64),
            'received_at' => now(),
            'delivered_at' => now(),
        ]);

        $this->artisan('model:prune', ['--model' => [CertificateVaultEntry::class]])->assertSuccessful();

        $entry->refresh();
        $this->assertTrue($entry->isPurged());
        $this->assertNull($entry->certificate);
        $this->assertNull($entry->password);
    }

    public function test_applicant_data_is_encrypted_and_searchable_by_blind_index(): void
    {
        $request = $this->makeSignatureRequest();

        $raw = DB::table('signature_requests')->value('applicant');
        $this->assertStringNotContainsString('1700000000', $raw);

        $this->assertTrue(SignatureRequest::where('identification_hash', app(BlindIndex::class)->for('cedula', ' 1700000000 '))->exists());
        $this->assertSame('1700000000', $request->fresh()->applicant['identification_number']);
    }

    public function test_status_machine_rejects_invalid_transitions_and_records_valid_ones(): void
    {
        $request = $this->makeSignatureRequest();

        $request->transitionTo(RequestStatus::submitting(), 'api');
        $this->assertDatabaseHas('signature_request_transitions', ['from' => 'DRAFT', 'to' => 'SUBMITTING']);

        $this->expectException(InvalidStatusTransition::class);
        $request->transitionTo(RequestStatus::delivered(), 'api');
    }

    public function test_audit_log_is_append_only(): void
    {
        $log = AuditLog::record('test.action', ip: 'cli');

        $this->expectException(LogicException::class);
        $log->update(['action' => 'otra']);
    }

    public function test_sealed_box_can_only_be_opened_with_client_private_key(): void
    {
        $keyPair = sodium_crypto_box_keypair();
        $publicKey = base64_encode(sodium_crypto_box_publickey($keyPair));

        $sealed = SealedBox::seal('p12+password', $publicKey);

        $this->assertSame('p12+password', sodium_crypto_box_seal_open(base64_decode($sealed), $keyPair));
        $this->assertFalse(sodium_crypto_box_seal_open(base64_decode($sealed), sodium_crypto_box_keypair()));
    }

    public function test_provider_credentials_are_encrypted(): void
    {
        ProviderAccount::create([
            'provider' => 'firmasegura',
            'environment' => 'sandbox',
            'name' => 'FS sandbox',
            'base_url' => 'https://api.dev-firmaseguraec.com',
            'credentials' => ['token' => 'jwt-secreto'],
        ]);

        $this->assertStringNotContainsString('jwt-secreto', DB::table('provider_accounts')->value('credentials'));
        $this->assertSame('jwt-secreto', ProviderAccount::first()->credentials['token']);
    }

    private function makeSignatureRequest(): SignatureRequest
    {
        $tenant = Tenant::create(['external_id' => Str::random(8), 'name' => 'T']);
        $client = ApiClient::create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'c',
            'signing_secret' => 's',
            'notification_secret' => 'n',
        ]);
        $account = ProviderAccount::create([
            'provider' => 'firmasegura',
            'environment' => 'sandbox',
            'name' => 'FS',
            'base_url' => 'https://api.dev-firmaseguraec.com',
        ]);

        return SignatureRequest::create([
            'tenant_id' => $tenant->getKey(),
            'api_client_id' => $client->getKey(),
            'provider_account_id' => $account->getKey(),
            'idempotency_key' => Str::uuid()->toString(),
            'product' => ProductType::headless(),
            'application_type' => ApplicationType::naturalPerson(),
            'period' => CertificatePeriod::oneYear(),
            'status' => RequestStatus::draft(),
            'identification_hash' => app(BlindIndex::class)->for('cedula', '1700000000'),
            'applicant' => ['identification_number' => '1700000000', 'email' => 'titular@example.com'],
        ]);
    }
}
