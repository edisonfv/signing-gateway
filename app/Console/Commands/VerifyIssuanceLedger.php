<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Heartbeat;
use App\Models\IssuanceLedgerEntry;
use App\Signing\Ledger\IssuanceLedger;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

#[Signature('gateway:ledger:verify')]
#[Description('Verifica que el registro de emisiones no tenga huecos ni entradas alteradas')]
class VerifyIssuanceLedger extends Command
{
    public function handle(IssuanceLedger $ledger): int
    {
        $problem = $ledger->verify();
        Heartbeat::beat('ledger-verify', ['ok' => $problem === null, ...($problem ?? [])]);

        if ($problem !== null) {
            AuditLog::record('issuance_ledger.tampered', context: $problem, ip: 'cli');
            Log::critical('El registro de emisiones no es íntegro.', $problem);
            $this->components->error("Registro de emisiones alterado en el correlativo {$problem['sequence']}: {$problem['reason']}.");

            return self::FAILURE;
        }

        $this->components->info('Registro de emisiones íntegro: '.IssuanceLedgerEntry::query()->count().' entradas.');

        return self::SUCCESS;
    }
}
