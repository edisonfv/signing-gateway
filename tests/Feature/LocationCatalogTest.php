<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Province;
use Database\Seeders\ProvinceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithSignedApi;
use Tests\TestCase;

class LocationCatalogTest extends TestCase
{
    use InteractsWithSignedApi, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->issueApiCredentials();
    }

    public function test_seeder_loads_the_provider_catalog_and_is_idempotent(): void
    {
        $this->seed(ProvinceSeeder::class);
        $this->seed(ProvinceSeeder::class);

        $this->assertSame(24, Province::count());
        $this->assertSame(234, City::count());
        $this->assertSame(2, City::where('name', 'BOLÍVAR')->count());
    }

    public function test_provinces_are_listed_with_city_counts_and_can_be_filtered(): void
    {
        $this->seed(ProvinceSeeder::class);

        $this->signedGet('/api/v1/provinces')
            ->assertOk()
            ->assertJsonCount(24, 'data')
            ->assertJsonPath('data.0.name', 'AZUAY');

        $this->signedGet('/api/v1/provinces?filter[name]=santo')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'SANTO DOMINGO DE LOS TSÁCHILAS')
            ->assertJsonPath('data.0.cities_count', 2);
    }

    public function test_cities_are_returned_only_for_the_requested_province(): void
    {
        $this->seed(ProvinceSeeder::class);
        $carchi = Province::where('name', 'CARCHI')->firstOrFail();

        $response = $this->signedGet("/api/v1/provinces/{$carchi->id}/cities")
            ->assertOk()
            ->assertJsonCount(6, 'data');

        $this->assertEqualsCanonicalizing(
            ['BOLÍVAR', 'ESPEJO', 'MIRA', 'MONTÚFAR', 'SAN PEDRO DE HUACA', 'TULCÁN'],
            $response->json('data.*.name'),
        );
        $this->assertSame([$carchi->id], array_values(array_unique($response->json('data.*.province_id'))));
    }

    public function test_unknown_or_malformed_province_returns_not_found(): void
    {
        $this->signedGet('/api/v1/provinces/'.Province::factory()->make()->newUniqueId().'/cities')->assertNotFound();
        $this->signedGet('/api/v1/provinces/no-es-uuid/cities')->assertNotFound();
    }

    public function test_unknown_filter_is_rejected(): void
    {
        $this->signedGet('/api/v1/provinces?filter[id]=x')->assertBadRequest();
    }

    public function test_enum_catalogs_are_exposed(): void
    {
        $this->signedGet('/api/v1/catalogs/periods')
            ->assertOk()
            ->assertJsonFragment(['value' => 'ONE_YEAR', 'label' => '1 AÑO']);

        $this->signedGet('/api/v1/catalogs/unknown')->assertNotFound();
    }

    public function test_token_without_catalog_ability_is_forbidden(): void
    {
        $this->credentials['client']->tokens()->update(['abilities' => json_encode(['signature-sessions.view'])]);

        $this->signedGet('/api/v1/provinces')->assertForbidden();
    }
}
