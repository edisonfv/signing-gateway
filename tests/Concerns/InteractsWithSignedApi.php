<?php

namespace Tests\Concerns;

use App\Actions\IssueApiClientCredentials;
use App\Models\ApiClient;
use App\Models\Tenant;
use App\Security\RequestSignature;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

/**
 * Emite una credencial real y firma las peticiones como lo haría el SaaS.
 */
trait InteractsWithSignedApi
{
    protected Tenant $tenant;

    /** @var array{client: ApiClient, token: string, signing_secret: string, notification_secret: string} */
    protected array $credentials;

    protected function issueApiCredentials(string $externalId = 'acme'): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->tenant = Tenant::create(['external_id' => $externalId, 'name' => Str::upper($externalId)]);
        $this->credentials = app(IssueApiClientCredentials::class)($this->tenant, "{$externalId} integración");
    }

    protected function signedGet(string $uri, ?string $secret = null, ?string $nonce = null, ?int $timestamp = null): TestResponse
    {
        $timestamp = (string) ($timestamp ?? now()->getTimestamp());
        $nonce ??= Str::random(32);

        // El servidor recibe la query codificada; se firma exactamente esa.
        $sent = Request::create($uri);
        $query = (string) $sent->server->get('QUERY_STRING', '');
        $signature = RequestSignature::sign(
            $secret ?? $this->credentials['signing_secret'],
            RequestSignature::canonical('GET', '/'.ltrim($sent->path(), '/').($query ? "?{$query}" : ''), $timestamp, $nonce, ''),
        );

        return $this->withToken($this->credentials['token'])
            ->withHeaders([
                'Accept' => 'application/json',
                RequestSignature::HEADER_TIMESTAMP => $timestamp,
                RequestSignature::HEADER_NONCE => $nonce,
                RequestSignature::HEADER_SIGNATURE => $signature,
            ])
            ->get($uri);
    }
}
