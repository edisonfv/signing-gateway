<?php

namespace Tests\Feature;

use App\Jobs\DeliverClientNotification;
use App\Models\ApiClient;
use App\Models\ClientNotification;
use App\Models\Tenant;
use App\Security\RequestSignature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use RuntimeException;
use Tests\TestCase;

class ClientNotificationDeliveryTest extends TestCase
{
    use RefreshDatabase;

    private ApiClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();

        $tenant = Tenant::create(['external_id' => 'acme', 'name' => 'ACME']);
        $this->client = ApiClient::create([
            'tenant_id' => $tenant->id,
            'name' => 'acme',
            'signing_secret' => 'firma',
            'notification_secret' => 'secreto-notificaciones',
            'notification_url' => 'https://saas.test/hooks/firmas',
        ]);
    }

    public function test_notification_is_queued_on_its_own_queue_and_payload_is_encrypted(): void
    {
        Queue::fake();

        $notification = ClientNotification::queue($this->client, 'session.advanced', ['step' => 'ISSUANCE']);

        Queue::assertPushedOn('notifications', DeliverClientNotification::class, fn ($job) => $job->notification->is($notification));
        $this->assertStringNotContainsString('ISSUANCE', DB::table('client_notifications')->value('payload'));
    }

    public function test_delivery_is_signed_with_the_notification_secret(): void
    {
        Http::fake(['saas.test/*' => Http::response(status: 204)]);
        $notification = ClientNotification::queue($this->client, 'session.advanced', ['step' => 'ISSUANCE']);

        Http::assertSent(function (Request $request) use ($notification) {
            $expected = RequestSignature::signNotification(
                'secreto-notificaciones',
                $request->header('X-Gateway-Timestamp')[0],
                $request->body(),
            );

            return $request->url() === 'https://saas.test/hooks/firmas'
                && $request['id'] === $notification->id
                && $request['data'] === ['step' => 'ISSUANCE']
                && hash_equals($expected, $request->header('X-Gateway-Signature')[0]);
        });
        $this->assertNotNull($notification->fresh()->delivered_at);
    }

    public function test_failed_delivery_is_not_marked_as_delivered(): void
    {
        Http::fake(['saas.test/*' => Http::response(status: 500)]);
        $notification = ClientNotification::create([
            'api_client_id' => $this->client->id,
            'event' => 'session.advanced',
            'payload' => [],
        ]);

        try {
            (new DeliverClientNotification($notification))->handle();
            $this->fail('Se esperaba una excepción por el 500.');
        } catch (RequestException) {
        }

        $notification->refresh();
        $this->assertNull($notification->delivered_at);
        $this->assertSame(1, $notification->attempts);
    }

    public function test_exhausted_delivery_is_recorded_as_failed(): void
    {
        $notification = ClientNotification::create([
            'api_client_id' => $this->client->id,
            'event' => 'session.advanced',
            'payload' => [],
        ]);

        (new DeliverClientNotification($notification))->failed(new RuntimeException('SaaS caído'));

        $this->assertNotNull($notification->fresh()->failed_at);
        $this->assertDatabaseHas('audit_logs', ['action' => 'client_notification.failed', 'subject_id' => $notification->id]);
    }
}
