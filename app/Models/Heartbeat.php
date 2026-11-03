<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Última señal de vida de una pieza del gateway (ver App\Signing\Health\GatewayHealth).
 *
 * Nombres: scheduler, queue:{cola}, reconcile, ledger-verify, provider:{cuenta}:ok y provider:{cuenta}:error.
 */
class Heartbeat extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $primaryKey = 'name';

    protected $keyType = 'string';

    protected $fillable = ['name', 'beat_at', 'meta'];

    protected function casts(): array
    {
        return [
            'beat_at' => 'immutable_datetime',
            'meta' => 'array',
        ];
    }

    /**
     * @param  array<string, mixed>|null  $meta
     */
    public static function beat(string $name, ?array $meta = null): void
    {
        static::query()->upsert([['name' => $name, 'beat_at' => now(), 'meta' => $meta === null ? null : json_encode($meta)]], ['name'], ['beat_at', 'meta']);
    }

    public static function lastAt(string $name): ?CarbonInterface
    {
        return static::query()->find($name)?->beat_at;
    }
}
