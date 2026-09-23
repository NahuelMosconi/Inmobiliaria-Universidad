<?php

namespace App\Http\Requests;

use App\Models\Propiedad;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validación del formulario de alta / edición de propiedades del panel.
 */
class PropiedadRequest extends FormRequest
{
    // Máximo de fotos por propiedad (entre las que ya tiene y las nuevas).
    public const MAX_IMAGENES = 15;

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        // Los "extras" se escriben uno por renglón (o separados por coma) en un
        // textarea; acá los convierto en un array limpio para guardarlos como JSON.
        $extras = preg_split('/[\r\n,]+/', (string) $this->input('extras'));
        $extras = array_values(array_unique(array_filter(array_map('trim', $extras))));

        $this->merge([
            'extras' => $extras,
            // Los checkbox destildados no se envían, por eso los fuerzo a true/false.
            'destacada' => $this->boolean('destacada'),
            'publicada' => $this->boolean('publicada'),
            // En los montos saco los puntos de miles por si alguien escribe "250.000".
            'precio' => $this->limpiarMonto($this->input('precio')),
            'expensas' => $this->limpiarMonto($this->input('expensas')),
        ]);
    }

    public function rules(): array
    {
        $imagenesActuales = $this->route('propiedad')?->imagenes()->count() ?? 0;

        return [
            'titulo' => ['required', 'string', 'max:150'],
            'operacion' => ['required', Rule::in(array_keys(Propiedad::OPERACIONES))],
            'tipo' => ['required', Rule::in(array_keys(Propiedad::TIPOS))],
            'localidad' => ['required', 'string', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'latitud' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitud'],
            'longitud' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitud'],
            'resumen' => ['required', 'string', 'max:300'],
            'descripcion' => ['required', 'string', 'max:10000'],
            'precio' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'moneda' => ['required', Rule::in(array_keys(Propiedad::MONEDAS))],
            'expensas' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'ambientes' => ['nullable', 'integer', 'min:0', 'max:50'],
            'dormitorios' => ['nullable', 'integer', 'min:0', 'max:50'],
            'banos' => ['nullable', 'integer', 'min:0', 'max:50'],
            'cocheras' => ['nullable', 'integer', 'min:0', 'max:50'],
            'superficie_total' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            // Solo comparo con la total si se cargó la total.
            'superficie_cubierta' => array_merge(
                ['nullable', 'integer', 'min:0', 'max:100000000'],
                $this->filled('superficie_total') ? ['lte:superficie_total'] : []
            ),
            'antiguedad' => ['nullable', 'integer', 'min:0', 'max:500'],
            'extras' => ['array', 'max:30'],
            'extras.*' => ['string', 'max:60'],
            'destacada' => ['boolean'],
            'publicada' => ['boolean'],
            'imagenes' => ['nullable', 'array', 'max:'.(self::MAX_IMAGENES - $imagenesActuales)],
            'imagenes.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'], // 4 MB por foto
        ];
    }

    public function messages(): array
    {
        return [
            'imagenes.max' => 'Una propiedad puede tener como máximo '.self::MAX_IMAGENES.' fotos.',
            'superficie_cubierta.lte' => 'La superficie cubierta no puede ser mayor a la total.',
            'latitud.required_with' => 'Si cargás la longitud también tenés que cargar la latitud.',
            'longitud.required_with' => 'Si cargás la latitud también tenés que cargar la longitud.',
        ];
    }

    public function attributes(): array
    {
        return [
            'banos' => 'baños',
            'superficie_total' => 'superficie total',
            'superficie_cubierta' => 'superficie cubierta',
            'antiguedad' => 'antigüedad',
            'descripcion' => 'descripción',
            'direccion' => 'dirección',
            'operacion' => 'operación',
            'titulo' => 'título',
            'imagenes.*' => 'foto',
            'extras.*' => 'extra',
        ];
    }

    private function limpiarMonto($valor): ?string
    {
        if ($valor === null || trim((string) $valor) === '') {
            return null;
        }

        // "250.000" -> "250000" y "1.500,50" -> "1500.50"
        $valor = str_replace(['.', ' ', '$'], '', (string) $valor);

        return str_replace(',', '.', $valor);
    }
}
