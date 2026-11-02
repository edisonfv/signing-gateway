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
use App\Signing\Data\ProductOffering;
use App\Signing\ProductCatalog;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Spatie\Enum\Laravel\Rules\EnumRule;

/**
 * Datos para crear una solicitud de firma. El SaaS elige `product` según GET /v1/products; los
 * tipos de solicitante, vigencias, contraseña y documentos los fija la oferta del proveedor que
 * atiende al tenant (ProductOffering), no reglas propias del gateway.
 *
 * No usa el trait TransformsEnums de spatie/laravel-enum: su método abstracto enums()
 * choca con Illuminate\Http\Request::enums() de Laravel 13.
 */
class StoreSignatureSessionRequest extends FormRequest
{
    private ?ProductOffering $offering = null;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $product = ProductType::tryFrom((string) $this->input('product'));
        $offering = $this->offering();
        $acceptsDocuments = $offering?->acceptsDocuments() ?? false;
        $ruc = ApplicationType::naturalPersonRuc()->value;
        $legal = ApplicationType::legalRepresentative()->value;

        return [
            'product' => ['required', new EnumRule(ProductType::class), function (string $attribute, mixed $value, Closure $fail) use ($product, $offering) {
                if ($product !== null && $offering === null) {
                    $fail('El producto no está contratado para este cliente o no hay proveedor disponible.');
                }
            }],
            'application_type' => ['required', new EnumRule(ApplicationType::class), Rule::in(array_map(fn (ApplicationType $type) => $type->value, $offering->applicationTypes ?? []))],
            'period' => ['required', new EnumRule(CertificatePeriod::class), Rule::in(array_map(fn (CertificatePeriod $period) => $period->value, $offering->periods ?? []))],

            /**
             * Contraseña del certificado elegida por el titular (solo productos con `allows_holder_password`, p. ej. HEADLESS). Opcional y no recomendada:
             * si se omite, el proveedor genera una aleatoria de 16 caracteres que nadie conoce antes de la
             * emisión. Política del proveedor: al menos 8 caracteres, una mayúscula, una minúscula y un número.
             */
            'password' => ['nullable', Rule::prohibitedIf($offering !== null && ! $offering->allowsHolderPassword), 'string', 'max:64', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)\S{8,}$/'],

            'applicant' => ['required', 'array:identification_number,finger_code,email,cellphone,address,city_id,person_ruc,birth_date'],
            'applicant.identification_number' => ['required', 'string', new EcuadorianIdentification],
            'applicant.finger_code' => ['required', 'string', 'size:10', 'alpha_num:ascii'],
            'applicant.email' => ['required', 'email:rfc', 'max:150'],
            'applicant.cellphone' => ['required', 'string', 'regex:/^\+5939\d{8}$/'],
            'applicant.address' => ['required', 'string', 'between:1,100'],
            'applicant.city_id' => ['required', 'uuid', 'exists:cities,id'],
            'applicant.person_ruc' => ['nullable', "required_if:application_type,{$ruc}", "prohibited_unless:application_type,{$ruc}", new EcuadorianRuc],
            'applicant.birth_date' => [Rule::requiredIf($offering?->requiresBirthDate ?? false), 'nullable', 'date_format:Y-m-d', 'before:today'],

            'company' => [Rule::requiredIf($this->input('application_type') === $legal), "prohibited_unless:application_type,{$legal}", 'array:ruc,name,position,appointment_expires_at'],
            'company.ruc' => ['required_with:company', 'string', 'regex:/^\d{10}001$/'],
            'company.name' => ['required_with:company', 'string', 'max:250'],
            'company.position' => ['required_with:company', 'string', 'max:100'],
            'company.appointment_expires_at' => ['required_with:company', 'date_format:Y-m-d', 'after:today'],

            'documents' => [Rule::requiredIf($acceptsDocuments), Rule::prohibitedIf($offering !== null && ! $acceptsDocuments), 'array:'.implode(',', DocumentType::toValues())],
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
     * Reglas que dependen de varios campos: edad y documentos que exige la oferta del proveedor.
     * El contenido de cada archivo (tipo real y tamaño) lo valida ValidDocument.
     *
     * @return array<int, Closure(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $offering = $this->offering();

                if ($validator->errors()->isNotEmpty() || $offering === null || ! $offering->acceptsDocuments()) {
                    return;
                }

                $this->validateDocumentRequirements($validator, $offering);
            },
        ];
    }

    /**
     * Oferta del producto para el tenant de la credencial (null si no lo contrató o no hay proveedor).
     */
    public function offering(): ?ProductOffering
    {
        $product = ProductType::tryFrom((string) $this->input('product'));

        if ($product === null) {
            return null;
        }

        /** @var ApiClient $client */
        $client = $this->user();

        return $this->offering ??= app(ProductCatalog::class)->offeringFor($product, $client->tenant);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'applicant.cellphone.regex' => 'El celular debe tener el formato +5939XXXXXXXX.',
            'password.regex' => 'La contraseña debe tener al menos 8 caracteres, sin espacios, con una mayúscula, una minúscula y un número.',
            'password.prohibited' => 'El producto elegido no permite que el titular elija la contraseña.',
            'period.in' => 'La vigencia no está disponible para el producto elegido.',
            'company.ruc.regex' => 'El RUC de la empresa debe tener 13 dígitos y terminar en 001.',
            'application_type.in' => 'El tipo de solicitante no está disponible para el producto elegido.',
        ];
    }

    private function validateDocumentRequirements(Validator $validator, ProductOffering $offering): void
    {
        $age = $this->filled('applicant.birth_date') ? CarbonImmutable::parse($this->input('applicant.birth_date'))->age : null;

        if ($age !== null && $age < 18) {
            $validator->errors()->add('applicant.birth_date', 'El titular debe ser mayor de edad.');

            return;
        }

        $documents = (array) $this->input('documents', []);

        foreach ($offering->documentsFor(ApplicationType::from($this->input('application_type')), $age) as $type) {
            if (blank($documents[$type->value] ?? null)) {
                $validator->errors()->add("documents.{$type->value}", "Falta el documento: {$type->label}.");
            }
        }
    }
}
