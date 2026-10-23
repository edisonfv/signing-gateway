<?php

namespace Tests\Unit;

use App\Rules\EcuadorianIdentification;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EcuadorianIdentificationTest extends TestCase
{
    /**
     * @return array<string, array{string, bool}>
     */
    public static function identifications(): array
    {
        return [
            'válida Pichincha' => ['1710034065', true],
            'válida Loja' => ['1104680135', true],
            'dígito verificador incorrecto' => ['1710034064', false],
            'provincia inexistente' => ['2610034065', false],
            'tercer dígito no permitido' => ['1760034065', false],
            'longitud incorrecta' => ['171003406', false],
            'no numérica' => ['17100340A5', false],
        ];
    }

    #[DataProvider('identifications')]
    public function test_validates_ecuadorian_identification(string $identification, bool $expected): void
    {
        $this->assertSame($expected, EcuadorianIdentification::isValid($identification));
    }
}
