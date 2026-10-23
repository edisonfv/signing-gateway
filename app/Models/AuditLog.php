<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use LogicException;

/**
 * Registro de auditoría de solo inserción: no se puede modificar ni borrar desde la app.
 */
#[Fillable(['actor_type', 'actor_id', 'action', 'subject_type', 'subject_id', 'ip', 'context'])]
class AuditLog extends Model
{
    use HasUuids;

    public const UPDATED_AT = null;

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Los registros de auditoría no se pueden modificar.'));
        static::deleting(fn () => throw new LogicException('Los registros de auditoría no se pueden borrar.'));
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public static function record(string $action, ?Model $actor = null, ?Model $subject = null, array $context = [], ?string $ip = null): self
    {
        return static::create([
            'actor_type' => $actor?->getMorphClass(),
            'actor_id' => $actor?->getKey(),
            'action' => $action,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'ip' => $ip ?? request()?->ip(),
            'context' => $context ?: null,
        ]);
    }

    protected function casts(): array
    {
        return ['context' => 'array'];
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function actor(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
