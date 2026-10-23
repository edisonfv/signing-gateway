<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Genera app/Enums/Province.php y app/Enums/City.php desde el catálogo del proveedor.
 *
 * Los enums no se editan a mano: se actualiza el JSON y se vuelve a correr este comando.
 */
#[Signature('gateway:catalogs:locations {--source=resources/catalogs/firmasegura/cities.json}')]
#[Description('Genera los enums Province y City desde el catálogo de ciudades del proveedor')]
class GenerateLocationEnums extends Command
{
    public function handle(): int
    {
        $catalog = json_decode(File::get(base_path($this->option('source'))), true, flags: JSON_THROW_ON_ERROR);

        $provinces = [];
        $cities = [];

        foreach ($catalog['provinces'] as $province => $provinceCities) {
            $provinceMethod = $this->methodName($province);
            $provinces[$provinceMethod] = $province;

            foreach ($provinceCities as $city) {
                $cities[$provinceMethod.Str::studly($this->methodName($city))] = [
                    'value' => "{$province}:{$city}",
                    'label' => "{$city} ({$province})",
                ];
            }
        }

        File::put(app_path('Enums/Province.php'), $this->provinceEnum($provinces, $catalog['source']));
        File::put(app_path('Enums/City.php'), $this->cityEnum($cities, $catalog['source']));

        $this->components->info(sprintf('Generadas %d provincias y %d ciudades.', count($provinces), count($cities)));

        return self::SUCCESS;
    }

    private function methodName(string $name): string
    {
        return Str::camel(trim(preg_replace('/[^A-Za-z0-9]+/', ' ', Str::ascii(Str::lower($name)))));
    }

    /**
     * @param  array<string, string>  $provinces
     */
    private function provinceEnum(array $provinces, string $source): string
    {
        $methods = collect($provinces)->keys()->map(fn ($method) => " * @method static self {$method}()")->implode("\n");
        $values = collect($provinces)->map(fn ($value, $method) => "            '{$method}' => '{$value}',")->implode("\n");

        return <<<PHP
<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * Provincias del Ecuador con el nombre exacto que espera el proveedor.
 *
 * Archivo generado por `php artisan gateway:catalogs:locations`. No editar a mano.
 * Fuente: {$source}
 *
{$methods}
 */
final class Province extends Enum
{
    protected static function values(): array
    {
        return [
{$values}
        ];
    }
}

PHP;
    }

    /**
     * @param  array<string, array{value: string, label: string}>  $cities
     */
    private function cityEnum(array $cities, string $source): string
    {
        $methods = collect($cities)->keys()->map(fn ($method) => " * @method static self {$method}()")->implode("\n");
        $values = collect($cities)->map(fn ($city, $method) => "            '{$method}' => '{$city['value']}',")->implode("\n");
        $labels = collect($cities)->map(fn ($city, $method) => "            '{$method}' => '{$city['label']}',")->implode("\n");

        return <<<PHP
<?php

namespace App\Enums;

use Illuminate\Support\Str;
use Spatie\Enum\Laravel\Enum;

/**
 * Ciudades del Ecuador agrupadas por provincia.
 *
 * El valor es "PROVINCIA:CIUDAD" porque hay nombres repetidos entre provincias
 * (BOLÍVAR, OLMEDO, LA CONCORDIA, PICHINCHA). Al proveedor se envía providerName().
 *
 * Archivo generado por `php artisan gateway:catalogs:locations`. No editar a mano.
 * Fuente: {$source}
 *
{$methods}
 */
final class City extends Enum
{
    public function province(): Province
    {
        return Province::from(Str::before(\$this->value, ':'));
    }

    public function providerName(): string
    {
        return Str::after(\$this->value, ':');
    }

    /**
     * @return array<int, self>
     */
    public static function forProvince(Province \$province): array
    {
        return array_values(array_filter(
            self::cases(),
            fn (self \$city) => \$city->province()->equals(\$province),
        ));
    }

    protected static function values(): array
    {
        return [
{$values}
        ];
    }

    protected static function labels(): array
    {
        return [
{$labels}
        ];
    }
}

PHP;
    }
}
