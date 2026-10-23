<?php

namespace App\Models;

use Database\Factories\ProvinceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Provincia del Ecuador, con el nombre exacto que espera el proveedor.
 * Se carga desde resources/catalogs/firmasegura/cities.json (ProvinceSeeder).
 */
#[Fillable(['name'])]
class Province extends Model
{
    /** @use HasFactory<ProvinceFactory> */
    use HasFactory, HasUuids;

    /**
     * @return HasMany<City, $this>
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
