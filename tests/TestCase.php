<?php

namespace Tests;

use Illuminate\Foundation\Testing\Attributes\Seed;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

/**
 * Los catálogos (roles, permisos, provincias y ciudades) se siembran una sola vez por proceso,
 * junto con migrate:fresh de RefreshDatabase; cada test los ve dentro de su transacción.
 */
#[Seed]
abstract class TestCase extends BaseTestCase
{
    /**
     * Stops before RefreshDatabase runs if the tests did not get the testing
     * environment (e.g. a cached config): they would wipe the development database.
     * In parallel (`php artisan test --parallel`) Laravel adds "_test_{token}" to the database name.
     */
    protected function refreshApplication(): void
    {
        parent::refreshApplication();

        $connection = (string) config('database.default');
        $database = (string) config("database.connections.{$connection}.database");

        if (! $this->app->environment('testing') || ! preg_match('/_test(ing)?(_test_\d+)?$/', $database)) {
            throw new RuntimeException("Las pruebas no corren sobre la base de pruebas (entorno «{$this->app->environment()}», base «{$database}»). ¿Hay una caché de configuración? Nada se tocó.");
        }
    }
}
