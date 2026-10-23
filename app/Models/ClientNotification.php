<?php

namespace App\Models;

use App\Jobs\DeliverClientNotification;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Notificación pendiente o entregada al SaaS (outbox).
 */
#[Fillable(['api_client_id', 'signature_request_id', 'event', 'payload', 'attempts', 'last_error', 'delivered_at', 'failed_at'])]
#[Hidden(['payload'])]
class ClientNotification extends Model
{
    use HasUuids;

    /**
     * Registra la notificación y encola su entrega cuando la transacción confirme.
     *
     * @param  array<string, mixed>  $payload
     */
    public static function queue(ApiClient $client, string $event, array $payload, ?SignatureRequest $request = null): self
    {
        $notification = static::create([
            'api_client_id' => $client->getKey(),
            'signature_request_id' => $request?->getKey(),
            'event' => $event,
            'payload' => $payload,
        ]);

        DeliverClientNotification::dispatch($notification)->afterCommit();

        return $notification;
    }

    protected function casts(): array
    {
        return [
            'payload' => 'encrypted:array',
            'attempts' => 'integer',
            'delivered_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<ApiClient, $this>
     */
    public function apiClient(): BelongsTo
    {
        return $this->belongsTo(ApiClient::class);
    }

    /**
     * @return BelongsTo<SignatureRequest, $this>
     */
    public function signatureRequest(): BelongsTo
    {
        return $this->belongsTo(SignatureRequest::class);
    }
}
