<?php

namespace App\Signing\Data;

/**
 * Términos y condiciones vigentes del proveedor.
 */
final readonly class TermsData
{
    public function __construct(
        public string $id,
        public string $version,
        public string $content,
    ) {}

    public function contentSha256(): string
    {
        return hash('sha256', $this->content);
    }

    /**
     * @return array{id: string, version: string, content: string}
     */
    public function toArray(): array
    {
        return ['id' => $this->id, 'version' => $this->version, 'content' => $this->content];
    }

    /**
     * @param  array{id: string, version: string, content: string}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self($data['id'], $data['version'], $data['content']);
    }
}
