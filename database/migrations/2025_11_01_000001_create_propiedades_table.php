<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('propiedades', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('slug')->unique(); // se usa en la URL: /propiedades/casa-en-venta-en-chacras
            $table->string('operacion', 20);  // venta | alquiler
            $table->string('tipo', 20);       // casa, departamento, terreno, etc. (ver Propiedad::TIPOS)

            // Ubicación
            $table->string('localidad', 100);
            $table->string('direccion')->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();

            // Textos
            $table->string('resumen', 300);   // texto corto que se ve en el listado
            $table->text('descripcion');

            // Precio. Si queda en null se muestra "Consultar".
            $table->decimal('precio', 14, 2)->nullable();
            $table->string('moneda', 3)->default('USD'); // USD | ARS
            $table->decimal('expensas', 12, 2)->nullable();

            // Características. Todas opcionales porque un terreno no tiene baños, por ejemplo.
            $table->unsignedTinyInteger('ambientes')->nullable();
            $table->unsignedTinyInteger('dormitorios')->nullable();
            $table->unsignedTinyInteger('banos')->nullable();
            $table->unsignedTinyInteger('cocheras')->nullable();
            $table->unsignedInteger('superficie_total')->nullable();    // m²
            $table->unsignedInteger('superficie_cubierta')->nullable(); // m²
            $table->unsignedSmallInteger('antiguedad')->nullable();     // años
            $table->json('extras')->nullable(); // lista libre: ["Piscina", "Quincho", ...]

            $table->boolean('destacada')->default(false); // aparece en el inicio
            $table->boolean('publicada')->default(true);  // si es false no se ve en el sitio
            $table->unsignedInteger('visitas')->default(0);

            $table->timestamps();

            $table->index(['publicada', 'operacion', 'tipo']);
            $table->index('localidad');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('propiedades');
    }
};
