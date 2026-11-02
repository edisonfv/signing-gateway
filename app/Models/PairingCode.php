<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Código de un solo uso con el que la plataforma obtiene su credencial (ver App\Actions\PairPlatform).
 */
#[Fillable(['code_hash', 'expires_at', 'failed_attempts', 'used_at', 'used_ip', 'api_client_id'])]
class PairingCode extends Model
{
    use HasUuids;

    /** Sin I, L, O, 0 ni 1: se dicta y se copia sin confusiones. */
    public const ALPHABET = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

    public const LENGTH = 8;

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
            'failed_attempts' => 'integer',
        ];
    }

    public static function hash(string $code): string
    {
        return hash('sha256', self::normalize($code));
    }

    /**
     * Mayúsculas y sin guiones ni espacios: "k7px-92qm" y "K7PX92QM" son el mismo código.
     */
    public static function normalize(string $code): string
    {
        return preg_replace('/[^A-Z0-9]/', '', strtoupper($code)) ?? '';
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->whereNull('used_at')->where('expires_at', '>', now());
    }
}
