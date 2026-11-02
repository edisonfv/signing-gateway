<?php

namespace Tests\Feature;

use App\Actions\IssueApiClientCredentials;
use App\Jobs\DeliverClientNotification;
use App\Models\ApiClient;
use App\Models\ClientNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\InteractsWithSignedApi;
use Tests\TestCase;

class NetworkHardeningTest extends TestCase
{
    use InteractsWithSignedApi, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->issueApiCredentials();
    }

    public function test_api_rejects_with_403_any_source_outside_the_platform_network(): void
    {
        config(['gateway.network.allowed_sources' => ['10.8.0.2']]);

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.9'])->signedGet('/api/v1/ping')
            ->assertForbidden()
            ->assertJsonPath('message', 'Origen no permitido.');
        $this->withServerVariables(['REMOTE_ADDR' => '10.8.0.2'])->signedGet('/api/v1/ping')->assertOk();
    }

    public function test_in_production_an_unconfigured_network_closes_the_api(): void
    {
        config(['gateway.network.allowed_sources' => []]);
        $this->app->detectEnvironment(fn () => 'production');

        $this->signedGet('/api/v1/ping')->assertForbidden();
    }

    public function test_notification_url_must_be_https_on_an_allowed_host(): void
    {
        config(['gateway.network.notification_hosts' => ['app.efirma.test']]);
        $this->credentials = app(IssueApiClientCredentials::class)(null, 'Plataforma: alta', abilities: ['clients.manage']);
        $base = ['name' => 'X', 'delivery_public_key' => base64_encode(sodium_crypto_box_publickey(sodium_crypto_box_keypair()))];

        $this->signedJson('POST', '/api/v1/admin/tenants/acme/clients', [...$base, 'notification_url' => 'http://app.efirma.test/hook'])
            ->assertJsonValidationErrors('notification_url');
        $this->signedJson('POST', '/api/v1/admin/tenants/acme/clients', [...$base, 'notification_url' => 'https://169.254.169.254/latest'])
            ->assertJsonValidationErrors(['notification_url' => 'El host de la URL de notificaciones no está permitido en el gateway (GATEWAY_NOTIFICATION_HOSTS).']);
        $this->signedJson('POST', '/api/v1/admin/tenants/acme/clients', [...$base, 'notification_url' => 'https://app.efirma.test/hook', 'allowed_ips' => ['10.8.0.0/99']])
            ->assertJsonValidationErrors('allowed_ips.0');
        $this->signedJson('POST', '/api/v1/admin/tenants/acme/clients', [...$base, 'notification_url' => 'https://app.efirma.test/hook', 'allowed_ips' => ['10.8.0.0/24']])
            ->assertCreated();
    }

    public function test_notifications_are_not_sent_to_a_host_removed_from_the_list(): void
    {
        Http::fake();
        $notification = $this->notificationFor('https://viejo.efirma.test/hook');
        config(['gateway.network.notification_hosts' => ['app.efirma.test']]);

        (new DeliverClientNotification($notification))->handle();

        Http::assertNothingSent();
        $this->assertNotNull($notification->fresh()->failed_at);
    }

    public function test_a_redirect_is_not_followed_nor_counted_as_delivered(): void
    {
        Http::fake(['app.efirma.test/*' => Http::response(status: 302, headers: ['Location' => 'http://10.0.0.1/interno'])]);
        $notification = $this->notificationFor('https://app.efirma.test/hook');

        try {
            (new DeliverClientNotification($notification))->handle();
            $this->fail('Un 302 no debe contar como entrega.');
        } catch (\RuntimeException) {
        }

        Http::assertSentCount(1);
        $this->assertNull($notification->fresh()->delivered_at);
    }

    private function notificationFor(string $url): ClientNotification
    {
        /** @var ApiClient $client */
        $client = $this->credentials['client'];
        $client->forceFill(['notification_url' => $url])->save();

        return ClientNotification::create(['api_client_id' => $client->id, 'event' => 'session.submitted', 'payload' => []]);
    }
}
