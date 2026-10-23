<?php

namespace App\Actions;

use App\Enums\ApplicationType;
use App\Enums\CertificatePeriod;
use App\Enums\DocumentType;
use App\Enums\ProductType;
use App\Enums\RequestStatus;
use App\Jobs\CreateProviderSession;
use App\Jobs\SubmitRegularRequest;
use App\Jobs\ValidateCivilRegistry;
use App\Models\ApiClient;
use App\Models\City;
use App\Models\SignatureDocument;
use App\Models\SignatureRequest;
use App\Security\BlindIndex;
use App\Signing\ApiFlow\TermsCatalog;
use App\Signing\Limits\UsageGuard;
use App\Signing\Routing\ProviderRouter;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Crea una solicitud de firma del producto que eligió el SaaS y encola su primer paso:
 * - HEADLESS: tramo A (crear sesión + Registro Civil).
 * - REGULAR: registro con documentos (los operadores del proveedor validan).
 *
 * Es idempotente por (cliente, Idempotency-Key): repetir la petición devuelve la misma solicitud.
 */
class CreateSignatureSession
{
    public function __construct(
        private UsageGuard $usageGuard,
        private ProviderRouter $router,
        private TermsCatalog $terms,
        private BlindIndex $blindIndex,
    ) {}

    /**
     * @param  array{product: string, application_type: string, period: string, applicant: array<string, string|null>, company?: array<string, string>, documents?: array<string, string>, terms: array<string, string|null>}  $data
     * @return array{request: SignatureRequest, created: bool}
     */
    public function __invoke(ApiClient $client, string $idempotencyKey, array $data): array
    {
        $existing = SignatureRequest::query()
            ->whereBelongsTo($client)
            ->where('idempotency_key', $idempotencyKey)
            ->first();

        if ($existing !== null) {
            return ['request' => $existing, 'created' => false];
        }

        $product = ProductType::from($data['product']);
        $account = $this->router->accountFor($product);
        $currentTerms = $this->terms->current($account);

        if ($data['terms']['id'] !== $currentTerms->id || $data['terms']['version'] !== $currentTerms->version) {
            throw ValidationException::withMessages([
                'terms.id' => 'Los términos aceptados no son los vigentes. Vuelva a consultar GET /v1/terms/current.',
            ]);
        }

        $city = City::with('province')->findOrFail($data['applicant']['city_id']);

        $request = $this->usageGuard->within($client->tenant, fn (): SignatureRequest => DB::transaction(function () use ($client, $idempotencyKey, $data, $product, $account, $currentTerms, $city) {
            $request = SignatureRequest::create([
                'tenant_id' => $client->tenant_id,
                'api_client_id' => $client->getKey(),
                'provider_account_id' => $account->getKey(),
                'idempotency_key' => $idempotencyKey,
                'product' => $product,
                'application_type' => ApplicationType::from($data['application_type']),
                'period' => CertificatePeriod::from($data['period']),
                'status' => RequestStatus::draft(),
                'identification_hash' => $this->blindIndex->for('cedula', $data['applicant']['identification_number']),
                'applicant' => [
                    ...$data['applicant'],
                    'city' => $city->name,
                    'province' => $city->province->name,
                    'company' => $data['company'] ?? null,
                ],
            ]);

            foreach ($data['documents'] ?? [] as $type => $base64) {
                $binary = base64_decode($base64, true);
                $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($binary);
                SignatureDocument::store($request, DocumentType::from($type), $binary, $mime);
            }

            $request->termsAcceptance()->create([
                'provider_terms_id' => $currentTerms->id,
                'version' => $currentTerms->version,
                'content_sha256' => $currentTerms->contentSha256(),
                'accepted_at' => $data['terms']['accepted_at'],
                'applicant_ip' => $data['terms']['applicant_ip'],
                'user_agent' => $data['terms']['user_agent'] ?? null,
            ]);

            $request->transitionTo(RequestStatus::submitting(), 'api');

            if ($product->equals(ProductType::regular())) {
                SubmitRegularRequest::dispatch($request);
            } else {
                Bus::chain([
                    new CreateProviderSession($request),
                    new ValidateCivilRegistry($request),
                ])->dispatch();
            }

            return $request;
        }));

        return ['request' => $request, 'created' => true];
    }
}
