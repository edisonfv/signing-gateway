<?php

namespace App\Listeners;

use App\Models\Heartbeat;
use App\Models\ProviderAccount;
use Illuminate\Http\Client\Events\ConnectionFailed;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Registra cómo responde cada cuenta de proveedor (última llamada correcta y último error), para el
 * monitor de salud. Escucha los eventos del cliente HTTP: cubre todas las llamadas de todos los
 * drivers sin tocarlas. Ignora las demás llamadas salientes (p. ej. las notificaciones a la plataforma).
 * Nunca guarda cuerpos: solo estado HTTP, ruta y mensaje de error recortado.
 */
class RecordProviderCall
{
    public function handle(ResponseReceived|ConnectionFailed $event): void
    {
        $url = $event->request->url();
        $account = $this->accountFor($url);

        if ($account === null) {
            return;
        }

        $path = (string) parse_url($url, PHP_URL_PATH);

        if ($event instanceof ConnectionFailed) {
            Heartbeat::beat("provider:{$account}:error", ['status' => null, 'path' => $path, 'message' => Str::limit($event->exception->getMessage(), 200)]);

            return;
        }

        $status = $event->response->status();

        // 4xx de negocio (datos rechazados, sesión inexistente) no indican que el proveedor esté caído.
        if ($status >= 500 || in_array($status, [401, 403, 429], true)) {
            Heartbeat::beat("provider:{$account}:error", ['status' => $status, 'path' => $path, 'message' => null]);

            return;
        }

        Heartbeat::beat("provider:{$account}:ok", ['status' => $status, 'path' => $path]);
    }

    /**
     * Cuenta cuya base_url coincide con la llamada (lista en caché un minuto).
     */
    private function accountFor(string $url): ?string
    {
        /** @var array<string, string> $accounts */
        $accounts = Cache::remember('health:provider-base-urls', 60, fn () => ProviderAccount::query()->pluck('base_url', 'id')->all());

        foreach ($accounts as $id => $baseUrl) {
            if ($baseUrl !== '' && str_starts_with($url, rtrim($baseUrl, '/'))) {
                return $id;
            }
        }

        return null;
    }
}
