<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Propiedad extends Model
{
    use HasFactory;

    // Laravel pluraliza en inglés ("propiedads"), así que indico la tabla a mano.
    protected $table = 'propiedades';

    // Valores posibles de los campos "operacion", "tipo" y "moneda".
    // La clave es lo que se guarda en la base y el valor es lo que se muestra.
    public const OPERACIONES = [
        'venta' => 'Venta',
        'alquiler' => 'Alquiler',
    ];

    public const TIPOS = [
        'casa' => 'Casa',
        'departamento' => 'Departamento',
        'ph' => 'PH / Dúplex',
        'terreno' => 'Terreno',
        'local' => 'Local comercial',
        'oficina' => 'Oficina',
        'finca' => 'Finca / Campo',
    ];

    public const MONEDAS = [
        'USD' => 'Dólares (USD)',
        'ARS' => 'Pesos (ARS)',
    ];

    // Opciones de orden del listado público.
    public const ORDENES = [
        'recientes' => 'Más recientes',
        'precio_asc' => 'Menor precio',
        'precio_desc' => 'Mayor precio',
        'superficie' => 'Mayor superficie',
    ];

    protected $fillable = [
        'titulo', 'operacion', 'tipo', 'localidad', 'direccion', 'latitud', 'longitud',
        'resumen', 'descripcion', 'precio', 'moneda', 'expensas',
        'ambientes', 'dormitorios', 'banos', 'cocheras',
        'superficie_total', 'superficie_cubierta', 'antiguedad', 'extras',
        'destacada', 'publicada',
    ];

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'expensas' => 'decimal:2',
            'latitud' => 'float',
            'longitud' => 'float',
            'extras' => 'array',
            'destacada' => 'boolean',
            'publicada' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // El slug se genera solo a partir del título la primera vez que se guarda.
        // No lo regenero al editar el título para no romper links que ya se compartieron.
        static::creating(function (Propiedad $propiedad) {
            if (empty($propiedad->slug)) {
                $propiedad->slug = static::generarSlugUnico($propiedad->titulo);
            }
        });

        // Al borrar la propiedad borro las imágenes una por una para que también
        // se eliminen los archivos del disco (el cascade de la base solo borra los registros).
        static::deleting(function (Propiedad $propiedad) {
            $propiedad->imagenes->each->delete();
        });
    }

    /**
     * Arma un slug a partir del título y le agrega un número si ya existe.
     * Ej: "casa-en-venta", "casa-en-venta-2", "casa-en-venta-3"...
     */
    public static function generarSlugUnico(string $titulo): string
    {
        $base = Str::slug($titulo) ?: 'propiedad';
        $slug = $base;
        $numero = 2;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$numero++;
        }

        return $slug;
    }

    /* ------------------------------------------------------------------
     | Relaciones
     * ------------------------------------------------------------------ */

    public function imagenes(): HasMany
    {
        return $this->hasMany(PropiedadImagen::class)->orderBy('orden')->orderBy('id');
    }

    // Primera imagen según el orden. Se usa en los listados para no traer todas las fotos.
    public function portada(): HasOne
    {
        return $this->hasOne(PropiedadImagen::class)->ofMany(['orden' => 'min', 'id' => 'min']);
    }

    public function consultas(): HasMany
    {
        return $this->hasMany(Consulta::class);
    }

    /* ------------------------------------------------------------------
     | Scopes (filtros reutilizables en las consultas)
     * ------------------------------------------------------------------ */

    public function scopePublicadas(Builder $query): Builder
    {
        return $query->where('publicada', true);
    }

    public function scopeDestacadas(Builder $query): Builder
    {
        return $query->where('destacada', true);
    }

    /**
     * Aplica los filtros del buscador. Recibe lo que viene por GET y descarta
     * cualquier valor que no sea válido en lugar de tirar error, así una URL
     * mal armada simplemente muestra todo.
     */
    public function scopeFiltrar(Builder $query, array $filtros): Builder
    {
        // Búsqueda por texto libre en título, localidad y resumen.
        if (! empty($filtros['q'])) {
            $texto = '%'.trim($filtros['q']).'%';
            $query->where(function (Builder $q) use ($texto) {
                $q->where('titulo', 'like', $texto)
                    ->orWhere('localidad', 'like', $texto)
                    ->orWhere('resumen', 'like', $texto);
            });
        }

        if (isset(self::OPERACIONES[$filtros['operacion'] ?? ''])) {
            $query->where('operacion', $filtros['operacion']);
        }

        if (isset(self::TIPOS[$filtros['tipo'] ?? ''])) {
            $query->where('tipo', $filtros['tipo']);
        }

        if (! empty($filtros['localidad'])) {
            $query->where('localidad', $filtros['localidad']);
        }

        if (is_numeric($filtros['dormitorios'] ?? null)) {
            $query->where('dormitorios', '>=', (int) $filtros['dormitorios']);
        }

        // El precio solo se puede comparar dentro de la misma moneda, así que si
        // se filtra por precio también se filtra por moneda (por defecto USD).
        $min = is_numeric($filtros['precio_min'] ?? null) ? (float) $filtros['precio_min'] : null;
        $max = is_numeric($filtros['precio_max'] ?? null) ? (float) $filtros['precio_max'] : null;

        if ($min !== null || $max !== null) {
            $moneda = isset(self::MONEDAS[$filtros['moneda'] ?? '']) ? $filtros['moneda'] : 'USD';
            $query->where('moneda', $moneda)->whereNotNull('precio');

            if ($min !== null) {
                $query->where('precio', '>=', $min);
            }
            if ($max !== null) {
                $query->where('precio', '<=', $max);
            }
        }

        return $query;
    }

    public function scopeOrdenar(Builder $query, ?string $orden): Builder
    {
        // "precio IS NULL" manda al final las propiedades con precio a consultar.
        return match ($orden) {
            'precio_asc' => $query->orderByRaw('precio IS NULL')->orderBy('precio'),
            'precio_desc' => $query->orderByRaw('precio IS NULL')->orderByDesc('precio'),
            'superficie' => $query->orderByRaw('superficie_total IS NULL')->orderByDesc('superficie_total'),
            default => $query->latest()->orderByDesc('id'),
        };
    }

    /* ------------------------------------------------------------------
     | Atributos calculados (se usan en las vistas como $propiedad->algo)
     * ------------------------------------------------------------------ */

    public function getOperacionNombreAttribute(): string
    {
        return self::OPERACIONES[$this->operacion] ?? ucfirst($this->operacion);
    }

    public function getTipoNombreAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? ucfirst($this->tipo);
    }

    /**
     * Precio listo para mostrar: "USD 250.000", "$ 900.000 / mes" o "Consultar".
     */
    public function getPrecioFormateadoAttribute(): string
    {
        if ($this->precio === null) {
            return 'Consultar';
        }

        $texto = self::formatearMonto($this->precio, $this->moneda);

        if ($this->operacion === 'alquiler') {
            $texto .= ' / mes';
        }

        return $texto;
    }

    public function getExpensasFormateadasAttribute(): ?string
    {
        return $this->expensas === null ? null : self::formatearMonto($this->expensas, 'ARS');
    }

    public static function formatearMonto($monto, string $moneda): string
    {
        $simbolo = $moneda === 'USD' ? 'USD' : '$';

        return $simbolo.' '.number_format((float) $monto, 0, ',', '.');
    }

    // URL de la foto principal o una imagen genérica si todavía no tiene fotos.
    public function getUrlPortadaAttribute(): string
    {
        $portada = $this->relationLoaded('portada')
            ? $this->portada
            : $this->imagenes->first();

        return $portada?->url ?? asset('img/sin-imagen.svg');
    }

    public function getUrlAttribute(): string
    {
        return route('propiedades.show', $this);
    }

    // Link de Google Maps para "cómo llegar". Si no hay coordenadas busca por dirección.
    public function getUrlMapaAttribute(): ?string
    {
        if ($this->latitud !== null && $this->longitud !== null) {
            return 'https://www.google.com/maps/search/?api=1&query='.$this->latitud.','.$this->longitud;
        }

        if ($this->direccion) {
            return 'https://www.google.com/maps/search/?api=1&query='.urlencode($this->direccion.', '.$this->localidad.', Mendoza');
        }

        return null;
    }

    public function tieneUbicacionEnMapa(): bool
    {
        return $this->latitud !== null && $this->longitud !== null;
    }

    // Las rutas públicas usan el slug en vez del id (/propiedades/casa-en-chacras).
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
