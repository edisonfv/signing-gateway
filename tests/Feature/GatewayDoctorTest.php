<?php

namespace Tests\Feature;

use App\Actions\IssueApiClientCredentials;
use App\Security\KeyRing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\FakesFirmaSegura;
use Tests\TestCase;

class GatewayDoctorTest extends TestCase
{
    use FakesFirmaSegura, RefreshDatabase;

    public function test_reports_a_missing_provider_account_and_fails(): void
    {
        $this->artisan('gateway:doctor', ['--offline' => true])
            ->expectsOutputToContain('sin cuenta activa (php artisan gateway:provider:credentials sandbox)')
            ->assertFailed();
    }

    public function test_passes_with_keys_catalogs_and_a_provider_that_answers(): void
    {
        $this->fakeFirmaSegura();
        $this->createFirmaSeguraAccount();
        app(IssueApiClientCredentials::class)(null, 'Plataforma: alta', abilities: ['clients.manage']);

        $this->artisan('gateway:doctor')
            ->expectsOutputToContain('responde desde este servidor')
            ->expectsOutputToContain('emparejada')
            ->expectsOutputToContain('Todo en orden.')
            ->assertSuccessful();
    }

    public function test_detects_data_encrypted_with_other_keys(): void
    {
        $this->createFirmaSeguraAccount();
        config(['gateway.keys.vault' => 'base64:'.base64_encode(random_bytes(32))]);
        $this->app->forgetInstance(KeyRing::class);

        $this->artisan('gateway:doctor', ['--offline' => true])
            ->expectsOutputToContain('NO se descifran: las llaves no son las que cifraron estos datos')
            ->assertFailed();
    }
}
