<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validación del formulario de contacto / consulta por una propiedad.
 * Reemplaza al viejo contacto.php, que guardaba lo que llegaba sin validar.
 */
class ConsultaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // cualquiera puede enviar una consulta
    }

    protected function prepareForValidation(): void
    {
        // Saco espacios de más antes de validar.
        $this->merge([
            'nombre' => trim((string) $this->input('nombre')),
            'email' => trim((string) $this->input('email')),
            'telefono' => trim((string) $this->input('telefono')) ?: null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            // Números, espacios, guiones, paréntesis y el + del código de país.
            'telefono' => ['nullable', 'string', 'max:30', 'regex:/^[0-9\s\-\+\(\)]{6,30}$/'],
            'mensaje' => ['required', 'string', 'min:10', 'max:2000'],
            'propiedad_id' => ['nullable', 'integer', 'exists:propiedades,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'telefono.regex' => 'El teléfono solo puede tener números, espacios, guiones y paréntesis.',
        ];
    }
}
