<?php

namespace App\Logging;

use Illuminate\Log\Logger;
use Monolog\LogRecord;

/**
 * "Tap" de Monolog que enmascara datos sensibles antes de escribir el log:
 * claves conocidas del contexto (password, token, certificado, cédula…) y cualquier
 * número de 10 o 13 dígitos (cédula o RUC) en el mensaje.
 */
class MaskSensitiveData
{
    private const SENSITIVE_KEYS = [
        'password', 'certificate', 'certificatebase64', 'token', 'secret', 'signing_secret',
        'notification_secret', 'authorization', 'identification_number', 'identificationnumber',
        'fingercode', 'finger_code', 'email', 'cellphone', 'cellphonenumber', 'applicant',
        'accesskeyid', 'secretaccesskey', 'sessiontoken', 'credentials',
    ];

    public function __invoke(Logger $logger): void
    {
        $logger->pushProcessor(fn (LogRecord $record): LogRecord => $record->with(
            message: $this->maskIdentifiers($record->message),
            context: $this->maskArray($record->context),
            extra: $this->maskArray($record->extra),
        ));
    }

    /**
     * @param  array<array-key, mixed>  $data
     * @return array<array-key, mixed>
     */
    private function maskArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($key) && in_array(strtolower($key), self::SENSITIVE_KEYS, true)) {
                $data[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                $data[$key] = $this->maskArray($value);
            } elseif (is_string($value)) {
                $data[$key] = $this->maskIdentifiers($value);
            }
        }

        return $data;
    }

    private function maskIdentifiers(string $text): string
    {
        return preg_replace('/(?<!\d)(\d{2})\d{6,9}(\d{2})(?!\d)/', '$1******$2', $text) ?? $text;
    }
}
