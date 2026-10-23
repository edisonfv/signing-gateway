<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class LogMaskingTest extends TestCase
{
    private string $path;

    protected function setUp(): void
    {
        parent::setUp();

        $this->path = storage_path('logs/masking-test.log');
        File::delete($this->path);
        config(['logging.channels.masking-test' => [...config('logging.channels.single'), 'path' => $this->path]]);
    }

    protected function tearDown(): void
    {
        File::delete($this->path);

        parent::tearDown();
    }

    public function test_sensitive_context_and_identifiers_are_masked(): void
    {
        Log::channel('masking-test')->info('Solicitud de 1712345678 con RUC 1712345678001', [
            'password' => 'Secreta123',
            'certificateBase64' => 'MIIP12',
            'nested' => ['identificationNumber' => '1712345678', 'note' => 'cédula 0912345678'],
            'request_status' => 'ISSUED',
        ]);

        $log = File::get($this->path);

        foreach (['Secreta123', 'MIIP12', '1712345678', '0912345678', '1712345678001'] as $secret) {
            $this->assertStringNotContainsString($secret, $log);
        }
        $this->assertStringContainsString('[REDACTED]', $log);
        $this->assertStringContainsString('17******78', $log);
        $this->assertStringContainsString('ISSUED', $log);
    }
}
