<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropiedadImagen extends Model
{
    use HasFactory;

    protected $table = 'propiedad_imagenes';

    protected $fillable = ['propiedad_id', 'ruta', 'descripcion', 'orden'];

    protected static function booted(): void
    {
        // Cuando se borra el registro también se borra el archivo de public/uploads.
        static::deleted(function (PropiedadImagen $imagen) {
            if (! $imagen->esExterna()) {
                Storage::disk('uploads')->delete($imagen->ruta);
            }
        });
    }

    public function propiedad(): BelongsTo
    {
        return $this->belongsTo(Propiedad::class);
    }

    // Permite cargar imágenes con una URL completa (http...) además de archivos subidos.
    public function esExterna(): bool
    {
        return Str::startsWith($this->ruta, ['http://', 'https://']);
    }

    public function getUrlAttribute(): string
    {
        // Uso asset() y no Storage::url() para que funcione aunque el sitio esté
        // en una subcarpeta (por ejemplo localhost/inmobiliaria/public).
        return $this->esExterna() ? $this->ruta : asset('uploads/'.$this->ruta);
    }
}
