<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * Provincias del Ecuador con el nombre exacto que espera el proveedor.
 *
 * Archivo generado por `php artisan gateway:catalogs:locations`. No editar a mano.
 * Fuente: https://docs.firmaseguraec.com/lista-de-ciudades-y-provincias-1820331m0
 *
 * @method static self azuay()
 * @method static self bolivar()
 * @method static self canar()
 * @method static self carchi()
 * @method static self chimborazo()
 * @method static self cotopaxi()
 * @method static self elOro()
 * @method static self esmeraldas()
 * @method static self galapagos()
 * @method static self guayas()
 * @method static self imbabura()
 * @method static self loja()
 * @method static self losRios()
 * @method static self manabi()
 * @method static self moronaSantiago()
 * @method static self napo()
 * @method static self orellana()
 * @method static self pastaza()
 * @method static self pichincha()
 * @method static self santaElena()
 * @method static self santoDomingoDeLosTsachilas()
 * @method static self sucumbios()
 * @method static self tungurahua()
 * @method static self zamoraChinchipe()
 */
final class Province extends Enum
{
    protected static function values(): array
    {
        return [
            'azuay' => 'AZUAY',
            'bolivar' => 'BOLÍVAR',
            'canar' => 'CAÑAR',
            'carchi' => 'CARCHI',
            'chimborazo' => 'CHIMBORAZO',
            'cotopaxi' => 'COTOPAXI',
            'elOro' => 'EL ORO',
            'esmeraldas' => 'ESMERALDAS',
            'galapagos' => 'GALÁPAGOS',
            'guayas' => 'GUAYAS',
            'imbabura' => 'IMBABURA',
            'loja' => 'LOJA',
            'losRios' => 'LOS RÍOS',
            'manabi' => 'MANABÍ',
            'moronaSantiago' => 'MORONA SANTIAGO',
            'napo' => 'NAPO',
            'orellana' => 'ORELLANA',
            'pastaza' => 'PASTAZA',
            'pichincha' => 'PICHINCHA',
            'santaElena' => 'SANTA ELENA',
            'santoDomingoDeLosTsachilas' => 'SANTO DOMINGO DE LOS TSÁCHILAS',
            'sucumbios' => 'SUCUMBÍOS',
            'tungurahua' => 'TUNGURAHUA',
            'zamoraChinchipe' => 'ZAMORA CHINCHIPE',
        ];
    }
}
