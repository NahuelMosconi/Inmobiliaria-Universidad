<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Carga los datos iniciales: el usuario administrador y las propiedades de ejemplo.
     * Se ejecuta con: php artisan db:seed  (o migrate:fresh --seed para empezar de cero)
     */
    public function run(): void
    {
        // firstOrCreate: si el admin ya existe no lo duplica ni le cambia la contraseña.
        User::firstOrCreate(
            ['email' => config('inmobiliaria.admin.email')],
            [
                'name' => config('inmobiliaria.admin.nombre'),
                'password' => config('inmobiliaria.admin.password'),
            ]
        );

        $this->call(PropiedadSeeder::class);
    }
}
