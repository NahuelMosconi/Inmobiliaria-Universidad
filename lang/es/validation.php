<?php

/*
|--------------------------------------------------------------------------
| Mensajes de validación en español
|--------------------------------------------------------------------------
| Laravel trae estos mensajes solo en inglés. Están traducidas las reglas
| que usa el proyecto y las más comunes.
*/

return [

    'accepted' => 'El campo :attribute debe ser aceptado.',
    'array' => 'El campo :attribute debe ser una lista.',
    'between' => [
        'numeric' => 'El campo :attribute debe estar entre :min y :max.',
        'string' => 'El campo :attribute debe tener entre :min y :max caracteres.',
        'array' => 'El campo :attribute debe tener entre :min y :max elementos.',
        'file' => 'El archivo :attribute debe pesar entre :min y :max kilobytes.',
    ],
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'current_password' => 'La contraseña actual no es correcta.',
    'email' => 'El campo :attribute debe ser un email válido.',
    'exists' => 'El valor elegido en :attribute no es válido.',
    'file' => 'El campo :attribute debe ser un archivo.',
    'image' => 'El archivo :attribute debe ser una imagen.',
    'in' => 'El valor elegido en :attribute no es válido.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'lte' => [
        'numeric' => 'El campo :attribute debe ser menor o igual a :value.',
    ],
    'max' => [
        'array' => 'El campo :attribute no puede tener más de :max elementos.',
        'file' => 'El archivo :attribute no puede pesar más de :max kilobytes.',
        'numeric' => 'El campo :attribute no puede ser mayor a :max.',
        'string' => 'El campo :attribute no puede tener más de :max caracteres.',
    ],
    'mimes' => 'El archivo :attribute debe ser de tipo: :values.',
    'min' => [
        'array' => 'El campo :attribute debe tener al menos :min elementos.',
        'file' => 'El archivo :attribute debe pesar al menos :min kilobytes.',
        'numeric' => 'El campo :attribute debe ser como mínimo :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'numeric' => 'El campo :attribute debe ser un número.',
    'password' => [
        'letters' => 'La :attribute debe tener al menos una letra.',
        'mixed' => 'La :attribute debe tener al menos una mayúscula y una minúscula.',
        'numbers' => 'La :attribute debe tener al menos un número.',
        'symbols' => 'La :attribute debe tener al menos un símbolo.',
        'uncompromised' => 'La :attribute apareció en una filtración de datos. Elegí otra.',
    ],
    'regex' => 'El formato de :attribute no es válido.',
    'required' => 'El campo :attribute es obligatorio.',
    'required_with' => 'El campo :attribute es obligatorio cuando se completa :values.',
    'string' => 'El campo :attribute debe ser texto.',
    'unique' => 'El :attribute ya está en uso.',
    'uploaded' => 'No se pudo subir :attribute (puede que pese demasiado).',
    'url' => 'El campo :attribute debe ser una URL válida.',

    // Nombres "lindos" de los campos para que los mensajes se lean bien.
    'attributes' => [
        'nombre' => 'nombre',
        'email' => 'email',
        'telefono' => 'teléfono',
        'mensaje' => 'mensaje',
        'password' => 'contraseña',
        'propiedad_id' => 'propiedad',
    ],

];
