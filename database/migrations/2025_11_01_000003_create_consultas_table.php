<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Reemplaza a la tabla "contacto" de la primera versión.
 * Ahora una consulta puede estar asociada a una propiedad (cuando se envía
 * desde la página de detalle) o ser general (desde la página de Contacto).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultas', function (Blueprint $table) {
            $table->id();
            // Si se borra la propiedad la consulta se conserva, solo pierde la relación.
            $table->foreignId('propiedad_id')->nullable()->constrained('propiedades')->nullOnDelete();
            $table->string('nombre', 100);
            $table->string('telefono', 30)->nullable();
            $table->string('email', 150);
            $table->text('mensaje');
            $table->string('ip', 45)->nullable();
            $table->timestamp('leida_at')->nullable(); // null = sin leer
            $table->timestamps();

            $table->index('leida_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultas');
    }
};
