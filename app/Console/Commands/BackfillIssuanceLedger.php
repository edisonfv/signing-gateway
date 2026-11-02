<?php

namespace App\Console\Commands;

use App\Enums\RequestStatus;
use App\Models\AuditLog;
use App\Models\SignatureRequest;
use App\Signing\Ledger\IssuanceLedger;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

#[Signature('gateway:ledger:backfill')]
#[Description('Registra en el registro de emisiones los certificados emitidos antes de que existiera (idempotente, sin notificar)')]
class BackfillIssuanceLedger extends Command
{
    public function handle(IssuanceLedger $ledger): int
    {
        $total = 0;

        SignatureRequest::query()
            ->with(['providerAccount', 'vaultEntry'])
            ->whereDoesntHave('issuanceLedgerEntry')
            ->where(fn (Builder $query) => $query
                ->whereNotNull('issued_at')
                ->orWhereIn('status', [RequestStatus::issued()->value, RequestStatus::certificateStored()->value, RequestStatus::delivered()->value])
                ->orWhereIn('id', AuditLog::query()->where('action', 'certificate.issued_not_stored')->select('subject_id')))
            ->orderByRaw('COALESCE(issued_at, created_at)')
            ->orderBy('id')
            ->each(function (SignatureRequest $request) use ($ledger, &$total) {
                $ledger->record($request, 'backfill', notify: false);
                $total++;
            });

        if ($total > 0) {
            AuditLog::record('issuance_ledger.backfilled', context: ['count' => $total], ip: 'cli');
        }

        $this->components->info("Emisiones registradas: {$total}.");

        return self::SUCCESS;
    }
}
