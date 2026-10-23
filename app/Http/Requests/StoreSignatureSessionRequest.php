<?php

namespace App\Http\Requests;

use App\Enums\ApplicationType;
use App\Enums\CertificatePeriod;
use App\Rules\EcuadorianIdentification;
use App\Rules\EcuadorianRuc;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\Enum\Laravel\Rules\EnumRule;

/**
 * Datos para iniciar una solicitud del Flujo por API.
 *
 * El flujo admite persona natural con o sin RUC; representante legal no está en el
 * esquema del proveedor para este flujo.
 *
 * No usa el trait TransformsEnums de spatie/laravel-enum: su método abstracto enums()
 * choca con Illuminate\Http\Request::enums() de Laravel 13. Los valores se convierten
 * a enum en CreateSignatureSession.
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
        return [
            'application_type' => ['required', new EnumRule(ApplicationType::class), 'in:'.ApplicationType::naturalPerson()->value.','.ApplicationType::naturalPersonRuc()->value],
            'period' => ['required', new EnumRule(CertificatePeriod::class), 'not_in:'.CertificatePeriod::oneDay()->value],
            'applicant' => ['required', 'array:identification_number,finger_code,email,cellphone,address,city_id,person_ruc'],
            'applicant.identification_number' => ['required', 'string', new EcuadorianIdentification],
            'applicant.finger_code' => ['required', 'string', 'size:10', 'alpha_num:ascii'],
            'applicant.email' => ['required', 'email:rfc', 'max:150'],
            'applicant.cellphone' => ['required', 'string', 'regex:/^\+5939\d{8}$/'],
            'applicant.address' => ['required', 'string', 'between:1,100'],
            'applicant.city_id' => ['required', 'uuid', 'exists:cities,id'],
            'applicant.person_ruc' => ['nullable', 'required_if:application_type,'.ApplicationType::naturalPersonRuc()->value, 'prohibited_unless:application_type,'.ApplicationType::naturalPersonRuc()->value, new EcuadorianRuc],
            'terms' => ['required', 'array:id,version,accepted_at,applicant_ip,user_agent'],
            'terms.id' => ['required', 'string'],
            'terms.version' => ['required', 'string', 'max:30'],
            'terms.accepted_at' => ['required', 'date', 'before_or_equal:now'],
            'terms.applicant_ip' => ['required', 'ip'],
            'terms.user_agent' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'applicant.cellphone.regex' => 'El celular debe tener el formato +5939XXXXXXXX.',
        ];
    }
}
