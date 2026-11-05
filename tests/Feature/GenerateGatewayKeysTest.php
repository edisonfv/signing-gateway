<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class GenerateGatewayKeysTest extends TestCase
{
    private string $envDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->envDirectory = storage_path('framework/testing/env-'.uniqid());
        File::ensureDirectoryExists($this->envDirectory);
        $this->app->useEnvironmentPath($this->envDirectory);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->envDirectory);

        parent::tearDown();
    }

    public function test_in_production_force_does_not_replace_existing_keys(): void
    {
        $env = "GATEWAY_VAULT_KEY=base64:actual\nGATEWAY_VAULT_PASSWORD_KEY=base64:actual\nGATEWAY_BLIND_INDEX_KEY=base64:actual\n";
        File::put($this->envDirectory.'/.env', $env);
        $this->app->detectEnvironment(fn () => 'production');

        $this->artisan('gateway:keys:generate', ['--force' => true])
            ->expectsOutputToContain('En producción no se reemplazan llaves')
            ->assertFailed();

        $this->assertSame($env, File::get($this->envDirectory.'/.env'));
    }

    public function test_in_production_only_missing_keys_are_generated(): void
    {
        File::put($this->envDirectory.'/.env', "GATEWAY_VAULT_KEY=base64:actual\nGATEWAY_VAULT_PASSWORD_KEY=\n");
        $this->app->detectEnvironment(fn () => 'production');

        $this->artisan('gateway:keys:generate')
            ->expectsConfirmation('Está en producción. ¿Escribir llaves en .env?', 'yes')
            ->assertSuccessful();

        $env = File::get($this->envDirectory.'/.env');
        $this->assertStringContainsString("GATEWAY_VAULT_KEY=base64:actual\n", $env);
        $this->assertMatchesRegularExpression('/^GATEWAY_VAULT_PASSWORD_KEY=base64:.+$/m', $env);
        $this->assertMatchesRegularExpression('/^GATEWAY_BLIND_INDEX_KEY=base64:.+$/m', $env);
    }
}
