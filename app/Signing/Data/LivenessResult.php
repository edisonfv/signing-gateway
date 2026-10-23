<?php

namespace App\Signing\Data;

final readonly class LivenessResult
{
    public function __construct(
        public bool $biometricMatch,
        public ?float $livenessConfidence,
        public ?float $faceMatchSimilarity,
        public ?string $sessionStatus,
    ) {}
}
