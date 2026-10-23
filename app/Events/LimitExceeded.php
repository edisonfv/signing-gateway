<?php

namespace App\Events;

use App\Models\Tenant;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Se alcanzó un tope duro de emisión. Los listeners alertan a los operadores.
 */
class LimitExceeded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Tenant $tenant,
        public string $scope,
        public int $limit,
        public int $current,
    ) {}
}
