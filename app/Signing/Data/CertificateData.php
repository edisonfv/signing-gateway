<?php

namespace App\Signing\Data;

/**
 * p12 emitido (base64) y su contraseña. Solo vive en memoria hasta guardarse en la bóveda.
 */
final readonly class CertificateData
{
    public function __construct(
        #[\SensitiveParameter] public string $certificateBase64,
        #[\SensitiveParameter] public string $password,
        public ?string $serialNumber,
    ) {}

    public function sha256(): string
    {
        return hash('sha256', (string) base64_decode($this->certificateBase64, true));
    }

    /**
     * Comprueba que el p12 sea legible con su contraseña antes de darlo por recibido.
     */
    public function opensWithPassword(): bool
    {
        $binary = base64_decode($this->certificateBase64, true);
        $certificates = [];

        return $binary !== false && openssl_pkcs12_read($binary, $certificates, $this->password);
    }
}
