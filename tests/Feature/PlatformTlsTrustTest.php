<?php

namespace Tests\Feature;

use App\Actions\IssueApiClientCredentials;
use App\Actions\PairPlatform;
use App\Security\TlsTrust;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\Concerns\InteractsWithSignedApi;
use Tests\TestCase;

class PlatformTlsTrustTest extends TestCase
{
    use InteractsWithSignedApi, RefreshDatabase;

    private string $tlsPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tlsPath = storage_path('framework/testing/tls-'.Str::random(8));
        File::ensureDirectoryExists($this->tlsPath);
        File::copy(base_path('tests/Fixtures/tls/current.crt'), "{$this->tlsPath}/current.crt");

        config([
            'gateway.tls.path' => $this->tlsPath,
            'gateway.tls.ack_file' => "{$this->tlsPath}/ack",
        ]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->tlsPath);

        parent::tearDown();
    }

    public function test_pairing_hands_over_the_certificates_the_platform_must_trust(): void
    {
        $this->addNextCertificate();

        $response = $this->postJson('/api/v1/pair', ['code' => app(PairPlatform::class)->generate()])->assertCreated();

        $this->assertSame([
            File::get(base_path('tests/Fixtures/tls/current.crt')),
            File::get(base_path('tests/Fixtures/tls/next.crt')),
        ], $response->json('tls_certificates'));
        $this->assertTrue(app(TlsTrust::class)->isNextAcknowledged());
    }

    public function test_the_platform_downloads_the_trust_list_and_unlocks_the_switch_to_the_next_certificate(): void
    {
        $this->addNextCertificate();
        $this->credentials = app(IssueApiClientCredentials::class)(null, 'Plataforma: servicio', abilities: ['platform.service']);
        $this->assertFalse(app(TlsTrust::class)->isNextAcknowledged());

        $this->signedGet('/api/v1/platform/tls')
            ->assertOk()
            ->assertJsonCount(2, 'certificates')
            ->assertJsonPath('certificates.0.fingerprint', openssl_x509_fingerprint(File::get(base_path('tests/Fixtures/tls/current.crt')), 'sha256'))
            ->assertJsonPath('certificates.1.pem', File::get(base_path('tests/Fixtures/tls/next.crt')));

        $this->assertTrue(app(TlsTrust::class)->isNextAcknowledged());
    }

    public function test_health_warns_before_expiry_until_the_platform_has_the_next_certificate(): void
    {
        $this->travelTo(app(TlsTrust::class)->current()['expires_at']->subDays(15));
        $this->credentials = app(IssueApiClientCredentials::class)(null, 'Plataforma: servicio', abilities: ['platform.service']);

        $this->signedGet('/api/v1/platform/health')->assertJsonFragment(['key' => 'tls', 'status' => 'warn', 'detail' => 'Vence en 15 días y aún no hay siguiente']);

        $this->addNextCertificate();
        $this->signedGet('/api/v1/platform/health')->assertJsonFragment(['key' => 'tls', 'status' => 'warn', 'detail' => 'Vence en 15 días; la plataforma aún no descargó el siguiente']);

        $this->signedGet('/api/v1/platform/tls')->assertOk();
        $this->signedGet('/api/v1/platform/health')->assertJsonFragment(['key' => 'tls', 'status' => 'ok']);
    }

    public function test_health_fails_when_the_certificate_is_about_to_expire(): void
    {
        $this->travelTo(app(TlsTrust::class)->current()['expires_at']->subDays(3));
        $this->credentials = app(IssueApiClientCredentials::class)(null, 'Plataforma: servicio', abilities: ['platform.service']);

        $this->signedGet('/api/v1/platform/health')
            ->assertJsonPath('status', 'down')
            ->assertJsonFragment(['key' => 'tls', 'status' => 'fail', 'detail' => 'Vence en 3 días ('.app(TlsTrust::class)->current()['expires_at']->toDateString().')']);
    }

    private function addNextCertificate(): void
    {
        File::copy(base_path('tests/Fixtures/tls/next.crt'), "{$this->tlsPath}/next.crt");
    }
}
