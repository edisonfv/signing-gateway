<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\CreateSignatureSession;
use App\Enums\ProductType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSignatureSessionRequest;
use App\Http\Resources\SignatureSessionResource;
use App\Models\ApiClient;
use App\Models\SignatureRequest;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\HeaderParameter;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Flujo por API', 'Emisión de firma electrónica paso a paso (FirmaSegura, Flujo por API).', weight: 2)]
class SignatureSessionController extends Controller
{
    /**
     * Listar solicitudes
     *
     * Solicitudes del tenant de la credencial, más recientes primero.
     */
    #[QueryParameter('filter[status]', 'Estado interno, p. ej. `AWAITING_APPLICANT`. Admite varios separados por coma.', type: 'string', example: 'AWAITING_APPLICANT')]
    #[QueryParameter('sort', 'Orden: `created_at` o `-created_at`.', type: 'string', default: '-created_at', example: '-created_at')]
    #[QueryParameter('page', 'Página.', type: 'integer', example: 1)]
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var ApiClient $client */
        $client = $request->user();

        $requests = QueryBuilder::for(SignatureRequest::query()->whereBelongsTo($client->tenant)->where('product', ProductType::headless()->value))
            ->allowedFilters(AllowedFilter::exact('status'))
            ->allowedSorts('created_at')
            ->defaultSort('-created_at')
            ->paginate(25)
            ->withQueryString();

        return SignatureSessionResource::collection($requests);
    }

    /**
     * Crear solicitud
     *
     * Registra la aceptación de términos y encola la creación de la sesión en el proveedor y
     * la validación en el Registro Civil. Responde 202; el avance llega por notificación o
     * consultando la solicitud. Repetir con el mismo `Idempotency-Key` devuelve la misma solicitud.
     */
    #[HeaderParameter('Idempotency-Key', 'Identificador único de la operación en el SaaS (máx. 100 caracteres).', required: true, type: 'string', example: '9b2f5d0e-6c1a-4f43-9a8e-1e2d3c4b5a60')]
    public function store(StoreSignatureSessionRequest $request, CreateSignatureSession $create): JsonResponse
    {
        $idempotencyKey = (string) $request->header('Idempotency-Key');

        abort_if($idempotencyKey === '' || mb_strlen($idempotencyKey) > 100, 400, 'La cabecera Idempotency-Key es obligatoria (máx. 100 caracteres).');

        ['request' => $signatureRequest, 'created' => $created] = $create($request->user(), $idempotencyKey, $request->validated());

        return SignatureSessionResource::make($signatureRequest->refresh())
            ->response()
            ->setStatusCode($created ? 202 : 200);
    }

    /**
     * Consultar solicitud
     */
    public function show(SignatureRequest $signatureSession): SignatureSessionResource
    {
        Gate::authorize('view', $signatureSession);

        return SignatureSessionResource::make($signatureSession);
    }
}
