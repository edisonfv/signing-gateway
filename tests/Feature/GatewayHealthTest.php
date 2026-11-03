<?php

namespace Tests\Feature;

use App\Actions\IssueApiClientCredentials;
use App\Exceptions\ProviderRequestException;
use App\Models\ApiClient;
use App\Models\ProviderAccount;
use App\Signing\ApiFlow\TermsCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\Concerns\FakesFirmaSegura;
use Tests\Concerns\InteractsWithSignedApi;
use Tests\TestCase;

class GatewayHealthTest extends TestCase
{
    use FakesFirmaSegura, InteractsWithSignedApi, RefreshDatabase;

    private ProviderAccount $account;

    /** @var array{client: ApiClient, token: string, signing_secret: string, notification_secret: string, expires_at: string|null} */
    private array $service;

    protected function setUp(): void
    {
        parent::setUp();

        config(['gateway.emission_halt_file' => storage_path('framework/testing/emission-halted-'.Str::random(8))]);
        $this->account = $this->createFirmaSeguraAccount();
        app(IssueApiClientCredentials::class)(null, 'Plataforma: alta', abilities: ['clients.manage']);
        $this->service = app(IssueApiClientCredentials::class)(null, 'Plataforma: servicio', abilities: ['platform.service']);
        $this->credentials = $this->service;
    }

    public function test_a_running_gateway_reports_ok(): void
    {
        $this->fakeFirmaSegura();
        app(TermsCatalog::class)->current($this->account);
        $this->artisan('gateway:heartbeat')->assertSuccessful();
        $this->artisan('gateway:reconcile-sessions')->assertSuccessful();

        $this->signedGet('/api/v1/platform/health')
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('checks.0.key', 'scheduler')
            ->assertJsonFragment(['key' => 'queue:interactive', 'status' => 'ok'])
            ->assertJsonFragment(['key' => "provider:{$this->account->id}", 'status' => 'ok']);
    }

    public function test_a_stopped_scheduler_and_workers_report_down_with_what_to_do(): void
    {
        $this->artisan('gateway:heartbeat')->assertSuccessful();
        $this->travel(5)->minutes();

        $this->signedGet('/api/v1/platform/health')
            ->assertOk()
            ->assertJsonPath('status', 'down')
            ->assertJsonFragment(['key' => 'scheduler', 'status' => 'fail', 'hint' => 'Revise el servicio gateway-scheduler (php artisan schedule:work).'])
            ->assertJsonFragment(['key' => 'queue:notifications', 'status' => 'fail']);
    }

    public function test_provider_errors_and_failed_emissions_report_down(): void
    {
        Http::fake(['*' => Http::response(status: 503)]);

        try {
            app(TermsCatalog::class)->current($this->account);
        } catch (ProviderRequestException) {
        }
        DB::table('failed_jobs')->insert([
            'uuid' => (string) Str::uuid(), 'connection' => 'database', 'queue' => 'interactive',
            'payload' => '{}', 'exception' => 'RuntimeException', 'failed_at' => now(),
        ]);

        $this->signedGet('/api/v1/platform/health')
            ->assertJsonPath('status', 'down')
            ->assertJsonFragment(['key' => "provider:{$this->account->id}", 'status' => 'fail'])
            ->assertJsonFragment(['key' => 'failed-jobs', 'status' => 'fail']);
    }

    public function test_an_affiliate_credential_cannot_read_the_health_and_gets_403(): void
    {
        $this->issueApiCredentials();

        $this->signedGet('/api/v1/platform/health')->assertForbidden();
    }
}
