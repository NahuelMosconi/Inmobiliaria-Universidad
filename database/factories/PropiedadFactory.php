<?php

namespace Database\Factories;

use App\Models\Propiedad;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Genera propiedades con datos al azar. Se usa en los tests.
 *
 * @extends Factory<Propiedad>
 */
class PropiedadFactory extends Factory
{
    public function definition(): array
    {
        $tipo = $this->faker->randomElement(['casa', 'departamento', 'ph']);
        $operacion = $this->faker->randomElement(array_keys(Propiedad::OPERACIONES));
        $localidad = $this->faker->randomElement(['Ciudad de Mendoza', 'Godoy Cruz', 'Luján de Cuyo', 'Maipú', 'Guaymallén']);
        $superficie = $this->faker->numberBetween(50, 500);

        return [
            'titulo' => Propiedad::TIPOS[$tipo].' en '.Propiedad::OPERACIONES[$operacion].' en '.$localidad,
            'operacion' => $operacion,
            'tipo' => $tipo,
            'localidad' => $localidad,
            'direccion' => $this->faker->streetAddress(),
            'resumen' => $this->faker->sentence(12),
            'descripcion' => $this->faker->paragraphs(2, true),
            'precio' => $operacion === 'venta'
                ? $this->faker->numberBetween(50, 400) * 1000
                : $this->faker->numberBetween(300, 1500) * 1000,
            'moneda' => $operacion === 'venta' ? 'USD' : 'ARS',
            'dormitorios' => $this->faker->numberBetween(1, 4),
            'banos' => $this->faker->numberBetween(1, 3),
            'superficie_total' => $superficie,
            'superficie_cubierta' => (int) ($superficie * 0.7),
            'publicada' => true,
            'destacada' => false,
        ];
    }

    public function oculta(): static
    {
        return $this->state(['publicada' => false]);
    }

    public function destacada(): static
    {
        return $this->state(['destacada' => true]);
    }
}
