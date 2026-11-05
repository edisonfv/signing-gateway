<?php

namespace App\Security;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\File;

/**
 * Certificados con los que la API interna se presenta a la plataforma. deploy/tls-rotate.sh (root)
 * los genera en gateway.tls.path: current.crt, el que sirve nginx, y desde 30 días antes de que
 * venza, next.crt. La plataforma los recibe al emparejar y los descarga a diario de
 * GET /v1/platform/tls, así que confía en el siguiente antes del cambio y nadie copia archivos.
 *
 * Solo certificados, que son públicos: las llaves privadas son de root y la aplicación no las lee.
 */
class TlsTrust
{
    public function isManaged(): bool
    {
        return filled(config('gateway.tls.path'));
    }

    /**
     * @return array{pem: string, fingerprint: string, expires_at: CarbonImmutable}|null
     */
    public function current(): ?array
    {
        return $this->read('current.crt');
    }

    /**
     * @return array{pem: string, fingerprint: string, expires_at: CarbonImmutable}|null
     */
    public function next(): ?array
    {
        return $this->read('next.crt');
    }

    /**
     * Los certificados en los que la plataforma debe confiar: el vigente y, si existe, el siguiente.
     *
     * @return list<array{pem: string, fingerprint: string, expires_at: CarbonImmutable}>
     */
    public function certificates(): array
    {
        return array_values(array_filter([$this->current(), $this->next()]));
    }

    /**
     * Registra que la plataforma ya tiene el siguiente certificado: desde ahora se puede cambiar a él.
     */
    public function acknowledge(): void
    {
        $next = $this->next();

        if ($next === null) {
            return;
        }

        File::ensureDirectoryExists(dirname(config('gateway.tls.ack_file')));
        File::put(config('gateway.tls.ack_file'), $next['fingerprint']);
    }

    public function isNextAcknowledged(): bool
    {
        $next = $this->next();
        $ackFile = config('gateway.tls.ack_file');

        return $next !== null && File::exists($ackFile) && trim(File::get($ackFile)) === $next['fingerprint'];
    }

    /**
     * @return array{pem: string, fingerprint: string, expires_at: CarbonImmutable}|null
     */
    private function read(string $name): ?array
    {
        if (! $this->isManaged()) {
            return null;
        }

        $path = rtrim(config('gateway.tls.path'), '/').'/'.$name;

        if (! File::isReadable($path)) {
            return null;
        }

        $pem = File::get($path);
        $parsed = openssl_x509_parse($pem);

        if ($parsed === false) {
            return null;
        }

        return [
            'pem' => $pem,
            'fingerprint' => (string) openssl_x509_fingerprint($pem, 'sha256'),
            'expires_at' => CarbonImmutable::createFromTimestamp($parsed['validTo_time_t']),
        ];
    }
}
