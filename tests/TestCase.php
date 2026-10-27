<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * Stops before RefreshDatabase runs if the tests did not get the testing
     * environment (e.g. a cached config): they would wipe the development database.
     */
    protected function refreshApplication(): void
    {
        parent::refreshApplication();

        $connection = (string) config('database.default');
        $database = (string) config("database.connections.{$connection}.database");

        if (! $this->app->environment('testing') || ! preg_match('/_test(ing)?$/', $database)) {
            throw new RuntimeException("Las pruebas no corren sobre la base de pruebas (entorno «{$this->app->environment()}», base «{$database}»). ¿Hay una caché de configuración? Nada se tocó.");
        }
    }
}
