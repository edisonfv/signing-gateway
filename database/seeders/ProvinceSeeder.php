<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

/**
 * Carga provincias y ciudades desde el catálogo del proveedor. Es idempotente:
 * para actualizar el catálogo se edita el JSON y se vuelve a correr el seeder.
 */
class ProvinceSeeder extends Seeder
{
    public const CATALOG = 'resources/catalogs/firmasegura/cities.json';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catalog = json_decode(File::get(base_path(self::CATALOG)), true, flags: JSON_THROW_ON_ERROR);

        foreach ($catalog['provinces'] as $provinceName => $cityNames) {
            $province = Province::firstOrCreate(['name' => $provinceName]);

            foreach ($cityNames as $cityName) {
                $province->cities()->firstOrCreate(['name' => $cityName]);
            }
        }
    }
}
