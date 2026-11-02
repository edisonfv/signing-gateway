<?php

namespace App\Jobs;

use App\Models\AuditLog;
use App\Models\ClientNotification;
use App\Security\NotificationTarget;
use App\Security\RequestSignature;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

/**
 * Entrega una notificación del outbox al SaaS, firmada con el secreto de notificaciones.
 *
 * Cabeceras: X-Gateway-Timestamp y X-Gateway-Signature = HMAC-SHA256("timestamp\ncuerpo").
 * El job solo transporta el UUID de la notificación; el contenido sigue cifrado en la BD.
 */
class DeliverClientNotification implements ShouldBeEncrypted, ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 8;

    public int $timeout = 30;

    /** @var array<int, int> */
    public array $backoff = [10, 30, 60, 300, 900, 1800, 3600];

    public function __construct(public ClientNotification $notification) {}

    public function uniqueId(): string
    {
        return $this->notification->getKey();
    }

    public function handle(): void
    {
        $notification = $this->notification;
        $client = $notification->apiClient;

        if ($notification->delivered_at !== null || blank($client->notification_url)) {
            return;
        }

        // La URL se validó al emitir la credencial; se vuelve a comprobar por si cambió la lista permitida.
        if (! NotificationTarget::isAllowed($client->notification_url)) {
            $notification->update(['failed_at' => now(), 'last_error' => 'Host de notificaciones no permitido.']);
            AuditLog::record('client_notification.blocked_target', subject: $notification, ip: 'queue');

            return;
        }

        $notification->increment('attempts');

        $body = json_encode([
            'id' => $notification->getKey(),
            // Con una sola URL de notificaciones para toda la plataforma, identifican al afiliado
            // y la credencial (cuyo secreto de notificaciones verifica la firma).
            'tenant' => $client->tenant?->external_id,
            'client' => $client->getKey(),
            'event' => $notification->event,
            'occurred_at' => $notification->created_at->toIso8601String(),
            'data' => $notification->payload,
        ], JSON_THROW_ON_ERROR);
        $timestamp = (string) now()->getTimestamp();

        $response = Http::timeout(15)
            ->connectTimeout(5)
            ->withoutRedirecting()
            ->withHeaders([
                'X-Gateway-Timestamp' => $timestamp,
                'X-Gateway-Signature' => RequestSignature::signNotification($client->notification_secret, $timestamp, $body),
            ])
            ->withBody($body, 'application/json')
            ->post($client->notification_url)
            ->throw();

        // Sin seguir redirecciones, un 3xx no es una entrega: solo cuenta un 2xx.
        if (! $response->successful()) {
            throw new RuntimeException("La plataforma respondió {$response->status()}.");
        }

        $notification->update(['delivered_at' => now(), 'last_error' => null]);
    }

    public function failed(?Throwable $exception): void
    {
        $this->notification->update([
            'failed_at' => now(),
            'last_error' => $exception ? mb_substr($exception->getMessage(), 0, 500) : null,
        ]);

        AuditLog::record('client_notification.failed', subject: $this->notification, ip: 'queue');
    }
}
