<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\IssuanceLedgerEntryResource;
use App\Models\IssuanceLedgerEntry;
use Carbon\CarbonImmutable;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Registro de emisiones', 'Solo para la credencial de alta de la plataforma (ability clients.manage).', weight: 10)]
class IssuanceController extends Controller
{
    /**
     * Listar emisiones
     *
     * Certificados que emitió el proveedor, en orden de correlativo. Para sincronizar, la plataforma
     * guarda el último `sequence` recibido y pide `filter[after_sequence]` con ese valor.
     */
    #[QueryParameter('filter[after_sequence]', 'Solo emisiones con correlativo mayor a este.', type: 'integer', example: 120)]
    #[QueryParameter('filter[tenant]', 'external_id del afiliado.', type: 'string', example: 'acme')]
    #[QueryParameter('filter[product]', 'Producto, p. ej. `REGULAR`.', type: 'string', example: 'HEADLESS')]
    #[QueryParameter('filter[provider_account_id]', 'Cuenta de proveedor.', type: 'string')]
    #[QueryParameter('filter[issued_from]', 'Emitidas desde esta fecha (inclusive, Y-m-d).', type: 'string', example: '2026-11-01')]
    #[QueryParameter('filter[issued_to]', 'Emitidas hasta esta fecha (inclusive, Y-m-d).', type: 'string', example: '2026-11-30')]
    #[QueryParameter('page', 'Página (100 por página).', type: 'integer', example: 1)]
    public function index(Request $request): AnonymousResourceCollection
    {
        $entries = QueryBuilder::for(IssuanceLedgerEntry::query()->with('tenant'))
            ->allowedFilters(...$this->filters())
            ->defaultSort('sequence')
            ->paginate(100)
            ->withQueryString();

        return IssuanceLedgerEntryResource::collection($entries);
    }

    /**
     * Resumen de emisiones
     *
     * Cantidades agrupadas por afiliado, cuenta de proveedor, producto y vigencia, con los mismos
     * filtros del listado. Sirve para cuadrar con la factura del proveedor y para facturar a cada afiliado.
     */
    #[QueryParameter('filter[issued_from]', 'Emitidas desde esta fecha (inclusive, Y-m-d).', type: 'string', example: '2026-11-01')]
    #[QueryParameter('filter[issued_to]', 'Emitidas hasta esta fecha (inclusive, Y-m-d).', type: 'string', example: '2026-11-30')]
    public function summary(Request $request): JsonResponse
    {
        $rows = QueryBuilder::for(IssuanceLedgerEntry::query())
            ->allowedFilters(...$this->filters())
            ->join('tenants', 'tenants.id', '=', 'issuance_ledger.tenant_id')
            ->select([
                'tenants.external_id as tenant_external_id',
                'issuance_ledger.provider',
                'issuance_ledger.provider_account_id',
                'issuance_ledger.product',
                'issuance_ledger.period',
            ])
            ->selectRaw('count(*) as issued_count')
            ->groupBy('tenants.external_id', 'issuance_ledger.provider', 'issuance_ledger.provider_account_id', 'issuance_ledger.product', 'issuance_ledger.period')
            ->orderBy('tenants.external_id')
            ->orderBy('issuance_ledger.product')
            ->orderBy('issuance_ledger.period')
            ->get();

        $data = $rows->map(fn (IssuanceLedgerEntry $row) => [
            'tenant_external_id' => $row->tenant_external_id,
            'provider' => $row->provider,
            'provider_account_id' => $row->provider_account_id,
            'product' => $row->product->value,
            'period' => $row->period->value,
            'count' => (int) $row->issued_count,
        ]);

        return response()->json(['data' => $data->values(), 'total' => $data->sum('count')]);
    }

    /**
     * @return array<int, AllowedFilter>
     */
    private function filters(): array
    {
        return [
            AllowedFilter::callback('after_sequence', fn (Builder $query, $value) => $query->where('issuance_ledger.sequence', '>', (int) $value)),
            AllowedFilter::callback('tenant', fn (Builder $query, $value) => $query->whereHas('tenant', fn (Builder $tenant) => $tenant->whereIn('external_id', (array) $value))),
            AllowedFilter::exact('product', 'issuance_ledger.product'),
            AllowedFilter::exact('provider_account_id', 'issuance_ledger.provider_account_id'),
            AllowedFilter::callback('issued_from', fn (Builder $query, $value) => $query->where('issuance_ledger.issued_at', '>=', CarbonImmutable::parse($value)->startOfDay())),
            AllowedFilter::callback('issued_to', fn (Builder $query, $value) => $query->where('issuance_ledger.issued_at', '<=', CarbonImmutable::parse($value)->endOfDay())),
        ];
    }
}
