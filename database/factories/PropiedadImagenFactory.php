<?php

namespace Database\Factories;

use App\Models\Propiedad;
use App\Models\PropiedadImagen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PropiedadImagen>
 */
class PropiedadImagenFactory extends Factory
{
    public function definition(): array
    {
        return [
            'propiedad_id' => Propiedad::factory(),
            'ruta' => 'propiedades/'.$this->faker->uuid().'.jpg',
            'descripcion' => $this->faker->sentence(3),
            'orden' => 0,
        ];
    }
}
