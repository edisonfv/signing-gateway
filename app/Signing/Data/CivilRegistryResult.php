<?php

namespace App\Signing\Data;

final readonly class CivilRegistryResult
{
    public function __construct(
        public bool $verified,
    ) {}
}
