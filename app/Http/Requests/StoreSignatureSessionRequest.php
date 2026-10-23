<?php

namespace App\Http\Requests;

use App\Enums\ApplicationType;
use App\Enums\CertificatePeriod;
use App\Enums\DocumentType;
use App\Enums\ProductType;
use App\Models\ApiClient;
use App\Rules\EcuadorianIdentification;
use App\Rules\EcuadorianRuc;
use App\Rules\ValidDocument;
use App\Signing\ProductCatalog;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Spatie\Enum\Laravel\Rules\EnumRule;

/**
 * Datos para crear una solicitud de firma. El SaaS elige el flujo con `product`
 * (según las condiciones comerciales del tenant, ver GET /v1/products):
 *
 * - HEADLESS (Flujo por API): identidad por prueba de vida; persona natural con o sin RUC.
 * - REGULAR (Firmas Regulares): identidad por documentos que validan operadores; admite
 *   representante legal; video de autorización obligatorio desde los 65 años.
 *
 * No usa el trait TransformsEnums de spatie/laravel-enum: su método abstracto enums()
 * choca con Illuminate\Http\Request::enums() de Laravel 13.
 */
class StoreSignatureSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $catalog = app(ProductCatalog::class);
        $product = ProductType::tryFrom((string) $this->input('product'));
        $isRegular = $product?->equals(ProductType::regular()) ?? false;
        $applicationTypes = $product ? $catalog->applicationTypes($product) : [];
        $ruc = ApplicationType::naturalPersonRuc()->value;
        $legal = ApplicationType::legalRepresentative()->value;

        return [
            'product' => ['required', new EnumRule(ProductType::class), Rule::in(array_map(fn (ProductType $item) => $item->value, $catalog->implemented()))],
            'application_type' => ['required', new EnumRule(ApplicationType::class), Rule::in(array_map(fn (ApplicationType $type) => $type->value, $applicationTypes))],
            'period' => ['required', new EnumRule(CertificatePeriod::class), 'not_in:'.CertificatePeriod::oneDay()->value],

            'applicant' => ['required', 'array:identification_number,finger_code,email,cellphone,address,city_id,person_ruc,birth_date'],
            'applicant.identification_number' => ['required', 'string', new EcuadorianIdentification],
            'applicant.finger_code' => ['required', 'string', 'size:10', 'alpha_num:ascii'],
            'applicant.email' => ['required', 'email:rfc', 'max:150'],
            'applicant.cellphone' => ['required', 'string', 'regex:/^\+5939\d{8}$/'],
            'applicant.address' => ['required', 'string', 'between:1,100'],
            'applicant.city_id' => ['required', 'uuid', 'exists:cities,id'],
            'applicant.person_ruc' => ['nullable', "required_if:application_type,{$ruc}", "prohibited_unless:application_type,{$ruc}", new EcuadorianRuc],
            'applicant.birth_date' => [Rule::requiredIf($isRegular), 'nullable', 'date_format:Y-m-d', 'before:today'],

            'company' => [Rule::requiredIf($isRegular && $this->input('application_type') === $legal), "prohibited_unless:application_type,{$legal}", 'array:ruc,name,position,appointment_expires_at'],
            'company.ruc' => ['required_with:company', 'string', 'regex:/^\d{10}001$/'],
            'company.name' => ['required_with:company', 'string', 'max:250'],
            'company.position' => ['required_with:company', 'string', 'max:100'],
            'company.appointment_expires_at' => ['required_with:company', 'date_format:Y-m-d', 'after:today'],

            'documents' => [Rule::requiredIf($isRegular), Rule::prohibitedIf($product !== null && ! $isRegular), 'array:'.implode(',', DocumentType::toValues())],
            ...collect(DocumentType::cases())->mapWithKeys(fn (DocumentType $type) => [
                "documents.{$type->value}" => ['nullable', 'string', new ValidDocument($type)],
            ])->all(),

            'terms' => ['required', 'array:id,version,accepted_at,applicant_ip,user_agent'],
            'terms.id' => ['required', 'string'],
            'terms.version' => ['required', 'string', 'max:30'],
            'terms.accepted_at' => ['required', 'date', 'before_or_equal:now'],
            'terms.applicant_ip' => ['required', 'ip'],
            'terms.user_agent' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Reglas que dependen de varios campos: producto contratado, edad y documentos requeridos.
     * El contenido de cada archivo (tipo real y tamaño) lo valida ValidDocument.
     *
     * @return array<int, Closure(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $product = ProductType::from($this->input('product'));

                /** @var ApiClient $client */
                $client = $this->user();

                if (! $client->tenant->allowsProduct($product)) {
                    $validator->errors()->add('product', 'El producto no está contratado para este cliente.');

                    return;
                }

                if ($product->equals(ProductType::regular())) {
                    $this->validateRegularRequirements($validator);
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'applicant.cellphone.regex' => 'El celular debe tener el formato +5939XXXXXXXX.',
            'company.ruc.regex' => 'El RUC de la empresa debe tener 13 dígitos y terminar en 001.',
            'application_type.in' => 'El tipo de solicitante no está disponible para el producto elegido.',
        ];
    }

    private function validateRegularRequirements(Validator $validator): void
    {
        $age = CarbonImmutable::parse($this->input('applicant.birth_date'))->age;

        if ($age < 18) {
            $validator->errors()->add('applicant.birth_date', 'El titular debe ser mayor de edad.');

            return;
        }

        $required = app(ProductCatalog::class)->requiredDocuments(ProductType::regular(), ApplicationType::from($this->input('application_type')));

        if ($age >= ProductCatalog::ELDERLY_AGE) {
            $required[] = DocumentType::authorizationVideo();
        }

        $documents = (array) $this->input('documents', []);

        foreach ($required as $type) {
            if (blank($documents[$type->value] ?? null)) {
                $validator->errors()->add("documents.{$type->value}", "Falta el documento: {$type->label}.");
            }
        }

    }
}
