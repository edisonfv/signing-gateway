<?php

namespace App\Models;

use App\Enums\DocumentType;
use App\Security\KeyRing;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Documento de una solicitud de Firmas Regulares (cédula, selfie, PDFs, video).
 *
 * El contenido vive cifrado (llave de la bóveda) en el disco "documents" solo hasta enviarse
 * al proveedor. Después se purga; la fila queda con el hash como trazabilidad.
 */
#[Fillable(['signature_request_id', 'type', 'mime', 'size', 'sha256', 'path', 'purged_at'])]
class SignatureDocument extends Model
{
    use HasUuids, Prunable;

    private const DISK = 'documents';

    public static function store(SignatureRequest $request, DocumentType $type, #[\SensitiveParameter] string $binary, string $mime): self
    {
        $path = "{$request->getKey()}/{$type->value}";

        Storage::disk(self::DISK)->put($path, app(KeyRing::class)->encrypter(KeyRing::VAULT)->encryptString(base64_encode($binary)));

        return static::create([
            'signature_request_id' => $request->getKey(),
            'type' => $type,
            'mime' => $mime,
            'size' => strlen($binary),
            'sha256' => hash('sha256', $binary),
            'path' => $path,
        ]);
    }

    /**
     * Contenido en base64, descifrado. Solo para armar la petición al proveedor.
     */
    public function base64Content(): string
    {
        return app(KeyRing::class)->encrypter(KeyRing::VAULT)->decryptString(Storage::disk(self::DISK)->get($this->path));
    }

    public function purge(): void
    {
        if ($this->path !== null) {
            Storage::disk(self::DISK)->delete($this->path);
        }

        $this->forceFill(['path' => null, 'purged_at' => now()])->save();
    }

    /**
     * Documentos ya enviados al proveedor, o abandonados hace más de 48 horas.
     */
    public function prunable(): Builder
    {
        return static::query()
            ->whereNull('purged_at')
            ->where(fn (Builder $query) => $query
                ->whereHas('signatureRequest', fn (Builder $request) => $request->whereNotNull('provider_request_id'))
                ->orWhere('created_at', '<=', now()->subHours(48)));
    }

    public function prune(): ?bool
    {
        $this->purge();

        return true;
    }

    protected function casts(): array
    {
        return [
            'type' => DocumentType::class,
            'size' => 'integer',
            'purged_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<SignatureRequest, $this>
     */
    public function signatureRequest(): BelongsTo
    {
        return $this->belongsTo(SignatureRequest::class);
    }
}
