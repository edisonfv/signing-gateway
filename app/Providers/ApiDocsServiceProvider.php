<?php

namespace App\Providers;

use App\Security\RequestSignature;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\Generator\Parameter;
use Dedoc\Scramble\Support\Generator\Response;
use Dedoc\Scramble\Support\Generator\Schema;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Dedoc\Scramble\Support\Generator\Types\ObjectType;
use Dedoc\Scramble\Support\Generator\Types\StringType;
use Dedoc\Scramble\Support\RouteInfo;
use Illuminate\Support\ServiceProvider;

/**
 * Documentación OpenAPI (Scramble) que se importa en Apidog.
 *
 * Las rutas firmadas declaran X-Timestamp, X-Nonce y X-Signature; el script de
 * pre-procesamiento de Apidog (docs/apidog/firma-hmac.js) los rellena en cada envío.
 */
class ApiDocsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi): void {
                $openApi->secure(
                    SecurityScheme::http('bearer')->setDescription('Token de acceso del cliente (admin del SaaS).'),
                );
            })
            ->withOperationTransformers(function (Operation $operation, RouteInfo $routeInfo): void {
                if (! in_array('api.signature', $routeInfo->route->gatherMiddleware(), true)) {
                    return;
                }

                $operation->addParameters([
                    $this->header(RequestSignature::HEADER_TIMESTAMP, 'Epoch en segundos. Ventana de ±5 minutos.', '1761200000'),
                    $this->header(RequestSignature::HEADER_NONCE, 'Valor único por petición (16–64 caracteres).', 'b1f0c3e9a7d24c6f9e2a5b8c7d1e0f3a'),
                    $this->header(RequestSignature::HEADER_SIGNATURE, 'HMAC-SHA256 hex de MÉTODO\\n/ruta?query\\nX-Timestamp\\nX-Nonce\\nsha256(cuerpo) con el secreto de firma.', str_repeat('0', 64)),
                ]);

                $documented = array_map(fn ($response) => (string) ($response->code ?? ''), $operation->responses ?? []);

                foreach ([
                    401 => 'Token inválido o vencido, o firma, timestamp o nonce inválidos.',
                    403 => 'Credencial revocada, tenant suspendido, IP no permitida o token sin la ability requerida.',
                    429 => 'Se superó el límite de peticiones de la credencial.',
                ] as $code => $description) {
                    if (! in_array((string) $code, $documented, true)) {
                        $operation->addResponse($this->errorResponse($code, $description));
                    }
                }
            });
    }

    private function errorResponse(int $code, string $description): Response
    {
        return Response::make($code)
            ->setDescription($description)
            ->setContent('application/json', Schema::fromType((new ObjectType)->addProperty('message', new StringType)));
    }

    private function header(string $name, string $description, string $example): Parameter
    {
        return Parameter::make($name, 'header')
            ->required(true)
            ->description($description)
            ->setSchema(Schema::fromType(new StringType))
            ->example($example);
    }
}
