<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consulta extends Model
{
    use HasFactory;

    protected $fillable = ['propiedad_id', 'nombre', 'telefono', 'email', 'mensaje', 'ip'];

    protected function casts(): array
    {
        return [
            'leida_at' => 'datetime',
        ];
    }

    public function propiedad(): BelongsTo
    {
        return $this->belongsTo(Propiedad::class);
    }

    public function scopeNoLeidas(Builder $query): Builder
    {
        return $query->whereNull('leida_at');
    }

    public function scopeLeidas(Builder $query): Builder
    {
        return $query->whereNotNull('leida_at');
    }

    public function getLeidaAttribute(): bool
    {
        return $this->leida_at !== null;
    }

    public function marcarComoLeida(): void
    {
        if (! $this->leida) {
            $this->forceFill(['leida_at' => now()])->save();
        }
    }

    public function marcarComoNoLeida(): void
    {
        $this->forceFill(['leida_at' => null])->save();
    }
}
