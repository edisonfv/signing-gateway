<?php

namespace Tests\Feature;

use Tests\TestCase;

class EnvironmentIsolationTest extends TestCase
{
    protected function tearDown(): void
    {
        putenv('GATEWAY_DEFAULT_PROVIDER');

        parent::tearDown();
    }

    /**
     * En Laragon (Apache mod_php) la plataforma y el gateway comparten el proceso de PHP: lo que una
     * app deja con putenv() no debe cambiar la configuración de la otra.
     */
    public function test_a_variable_left_in_the_process_by_another_app_is_ignored(): void
    {
        putenv('GATEWAY_DEFAULT_PROVIDER=otra-app');

        $this->refreshApplication();

        $this->assertSame('firmasegura', config('gateway.default_provider'));
    }
}
