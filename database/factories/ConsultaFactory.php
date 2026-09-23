<?php

namespace Database\Factories;

use App\Models\Consulta;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Consulta>
 */
class ConsultaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->name(),
            'telefono' => '261 '.$this->faker->numerify('#######'),
            'email' => $this->faker->safeEmail(),
            'mensaje' => $this->faker->paragraph(),
            'ip' => $this->faker->ipv4(),
        ];
    }

    public function leida(): static
    {
        return $this->state(['leida_at' => now()]);
    }
}
