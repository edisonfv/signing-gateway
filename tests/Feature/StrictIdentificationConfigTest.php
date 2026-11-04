<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StrictIdentificationConfigTest extends TestCase
{
    private const VARIABLES = ['GATEWAY_STRICT_IDENTIFICATION', 'GATEWAY_PROVIDER_ENVIRONMENT'];

    /** @var array<string, array{server: mixed, env: mixed}> */
    private array $original = [];

    protected function setUp(): void
    {
        parent::setUp();

        foreach (self::VARIABLES as $name) {
            $this->original[$name] = ['server' => $_SERVER[$name] ?? null, 'env' => $_ENV[$name] ?? null];
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->original as $name => $values) {
            $this->setVariable($name, $values['server']);
            $_ENV[$name] = $values['env'];
        }

        parent::tearDown();
    }

    /**
     * @return array<string, array{string, ?string, bool}>
     */
    public static function settings(): array
    {
        return [
            'vacía en producción' => ['production', '', true],
            'sin definir en producción' => ['production', null, true],
            'vacía en sandbox' => ['sandbox', '', false],
            'apagada a propósito en producción' => ['production', 'false', false],
            'encendida en sandbox' => ['sandbox', 'true', true],
        ];
    }

    #[DataProvider('settings')]
    public function test_a_blank_value_falls_back_to_the_provider_environment(string $environment, ?string $strict, bool $expected): void
    {
        $this->setVariable('GATEWAY_PROVIDER_ENVIRONMENT', $environment);
        $this->setVariable('GATEWAY_STRICT_IDENTIFICATION', $strict);

        $config = require config_path('gateway.php');

        $this->assertSame($expected, $config['strict_identification']);
    }

    private function setVariable(string $name, ?string $value): void
    {
        if ($value === null) {
            unset($_SERVER[$name], $_ENV[$name]);
            putenv($name);

            return;
        }

        $_SERVER[$name] = $_ENV[$name] = $value;
        putenv("{$name}={$value}");
    }
}
