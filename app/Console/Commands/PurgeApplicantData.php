<?php

namespace App\Console\Commands;

use App\Enums\RequestStatus;
use App\Enums\SignatureFlow;
use App\Models\AuditLog;
use App\Models\SignatureRequest;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

#[Signature('gateway:purge-applicant-data')]
#[Description('Borra los datos personales del titular de las solicitudes cerradas hace más de gateway.retention.applicant_data_days días')]
class PurgeApplicantData extends Command
{
    public function handle(): int
    {
        $threshold = now()->subDays(config('gateway.retention.applicant_data_days'));

        // Cerradas, o emitidas por un proveedor que entrega el p12 al titular (ya no queda nada por hacer).
        $total = SignatureRequest::query()
            ->whereNull('applicant_purged_at')
            ->where(fn (Builder $query) => $query
                ->where('closed_at', '<=', $threshold)
                ->orWhere(fn (Builder $issued) => $issued
                    ->where('flow', SignatureFlow::documentReview()->value)
                    ->where('status', RequestStatus::issued()->value)
                    ->where('issued_at', '<=', $threshold)))
            ->update(['applicant' => null, 'applicant_password' => null, 'last_error' => null, 'applicant_purged_at' => now()]);

        if ($total > 0) {
            AuditLog::record('signature_requests.applicant_purged', context: ['count' => $total], ip: 'cli');
        }

        $this->components->info("Solicitudes con datos personales purgados: {$total}.");

        return self::SUCCESS;
    }
}
