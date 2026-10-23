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

    /**
     * Petición firmada con cuerpo JSON (se firma exactamente el cuerpo enviado).
     *
     * @param  array<string, mixed>|null  $data
     * @param  array<string, string>  $headers
     */
    protected function signedJson(string $method, string $uri, ?array $data = null, array $headers = []): TestResponse
    {
        $this->app['auth']->forgetGuards();

        $body = $data === null ? '' : json_encode($data, JSON_THROW_ON_ERROR);
        $timestamp = (string) now()->getTimestamp();
        $nonce = Str::random(32);
        $sent = Request::create($uri, $method);
        $query = rawurldecode((string) $sent->server->get('QUERY_STRING', ''));

        $signature = RequestSignature::sign(
            $this->credentials['signing_secret'],
            RequestSignature::canonical($method, '/'.ltrim($sent->path(), '/').($query ? "?{$query}" : ''), $timestamp, $nonce, $body),
        );

        return $this->call($method, $uri, server: $this->transformHeadersToServerVars([
            'Authorization' => 'Bearer '.$this->credentials['token'],
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            RequestSignature::HEADER_TIMESTAMP => $timestamp,
            RequestSignature::HEADER_NONCE => $nonce,
            RequestSignature::HEADER_SIGNATURE => $signature,
            ...$headers,
        ]), content: $body);
    }

    protected function signedGet(string $uri, ?string $secret = null, ?string $nonce = null, ?int $timestamp = null): TestResponse
    {
        $this->app['auth']->forgetGuards();

        $timestamp = (string) ($timestamp ?? now()->getTimestamp());
        $nonce ??= Str::random(32);

        // La firma usa la query decodificada, como la ve el cliente.
        $sent = Request::create($uri);
        $query = rawurldecode((string) $sent->server->get('QUERY_STRING', ''));
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
