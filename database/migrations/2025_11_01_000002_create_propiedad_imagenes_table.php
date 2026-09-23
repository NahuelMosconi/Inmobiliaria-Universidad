<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('propiedad_imagenes', function (Blueprint $table) {
            $table->id();
            // Si se borra la propiedad se borran sus imágenes (los archivos los borra el modelo).
            $table->foreignId('propiedad_id')->constrained('propiedades')->cascadeOnDelete();
            $table->string('ruta');                 // ruta relativa dentro de public/uploads
            $table->string('descripcion')->nullable(); // texto alternativo (alt) de la imagen
            $table->unsignedSmallInteger('orden')->default(0); // la de orden más bajo es la portada
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('propiedad_imagenes');
    }
};
