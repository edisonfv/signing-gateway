<?php

namespace Tests\Feature;

use App\Enums\ApplicationType;
use App\Enums\CertificatePeriod;
use App\Enums\ProductType;
use App\Enums\RequestStatus;
use App\Events\LimitExceeded;
use App\Exceptions\LimitExceededException;
use App\Models\ApiClient;
use App\Models\ProviderAccount;
use App\Models\SignatureRequest;
use App\Models\Tenant;
use App\Signing\Limits\UsageGuard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\TestCase;

class UsageGuardTest extends TestCase
{
    use RefreshDatabase;

    public function test_requests_below_the_tenant_cap_are_created(): void
    {
        $tenant = Tenant::create(['external_id' => 'a', 'name' => 'A', 'hourly_cap' => 2]);

        $request = app(UsageGuard::class)->within($tenant, fn () => $this->createRequest($tenant));

        $this->assertTrue($request->exists);
    }

    public function test_hourly_tenant_cap_blocks_creation_and_alerts(): void
    {
        Event::fake([LimitExceeded::class]);
        $tenant = Tenant::create(['external_id' => 'a', 'name' => 'A', 'hourly_cap' => 2]);
        $this->createRequest($tenant);
        $this->createRequest($tenant);

        try {
            app(UsageGuard::class)->within($tenant, fn () => $this->fail('No debió crear la solicitud.'));
            $this->fail('Se esperaba LimitExceededException.');
        } catch (LimitExceededException $exception) {
            $this->assertSame('tenant_hourly', $exception->scope);
        }

        Event::assertDispatched(LimitExceeded::class, fn (LimitExceeded $event) => $event->tenant->is($tenant) && $event->current === 2);
        $this->assertDatabaseHas('audit_logs', ['action' => 'limits.exceeded', 'subject_id' => $tenant->id]);
    }

    public function test_requests_older_than_the_window_do_not_count(): void
    {
        $tenant = Tenant::create(['external_id' => 'a', 'name' => 'A', 'hourly_cap' => 1]);
        $this->travel(-2)->hours(fn () => $this->createRequest($tenant));

        $request = app(UsageGuard::class)->within($tenant, fn () => $this->createRequest($tenant));

        $this->assertTrue($request->exists);
    }

    public function test_daily_cap_applies_across_hours(): void
    {
        $tenant = Tenant::create(['external_id' => 'a', 'name' => 'A', 'hourly_cap' => 10, 'daily_cap' => 1]);
        $this->travel(-3)->hours(fn () => $this->createRequest($tenant));

        $this->expectException(LimitExceededException::class);
        app(UsageGuard::class)->within($tenant, fn () => $this->createRequest($tenant));
    }

    public function test_global_cap_counts_all_tenants(): void
    {
        config(['gateway.limits.global_hourly' => 1]);
        $this->createRequest(Tenant::create(['external_id' => 'a', 'name' => 'A']));
        $other = Tenant::create(['external_id' => 'b', 'name' => 'B']);

        $this->expectException(LimitExceededException::class);
        app(UsageGuard::class)->within($other, fn () => $this->createRequest($other));
    }

    public function test_limit_exception_renders_as_too_many_requests(): void
    {
        $response = (new LimitExceededException('tenant_daily', 5))->render();

        $this->assertSame(429, $response->getStatusCode());
        $this->assertSame('tenant_daily', $response->getData(true)['scope']);
    }

    private function createRequest(Tenant $tenant): SignatureRequest
    {
        $client = ApiClient::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'c'],
            ['signing_secret' => 's', 'notification_secret' => 'n'],
        );
        $account = ProviderAccount::firstOrCreate(
            ['provider' => 'firmasegura', 'environment' => 'sandbox'],
            ['name' => 'FS', 'base_url' => 'https://api.dev-firmaseguraec.com'],
        );

        return SignatureRequest::create([
            'tenant_id' => $tenant->id,
            'api_client_id' => $client->id,
            'provider_account_id' => $account->id,
            'idempotency_key' => (string) Str::uuid(),
            'product' => ProductType::headless(),
            'application_type' => ApplicationType::naturalPerson(),
            'period' => CertificatePeriod::oneYear(),
            'status' => RequestStatus::draft(),
            'identification_hash' => str_repeat('a', 64),
            'applicant' => [],
        ]);
    }
}
