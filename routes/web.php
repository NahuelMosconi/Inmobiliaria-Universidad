<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ConsultaController as AdminConsultaController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ImagenController;
use App\Http\Controllers\Admin\PerfilController;
use App\Http\Controllers\Admin\PropiedadController as AdminPropiedadController;
use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\PropiedadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Sitio público
|--------------------------------------------------------------------------
*/

Route::get('/', InicioController::class)->name('inicio');

Route::get('/propiedades', [PropiedadController::class, 'index'])->name('propiedades.index');
Route::get('/propiedades/{propiedad}', [PropiedadController::class, 'show'])->name('propiedades.show');

Route::view('/nosotros', 'nosotros')->name('nosotros');

Route::get('/contacto', [ConsultaController::class, 'create'])->name('contacto');
// throttle: máximo 5 envíos por minuto por IP para frenar spam.
Route::post('/contacto', [ConsultaController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contacto.store');
Route::view('/gracias', 'gracias')->name('gracias');

// Favoritos: se guardan en el navegador (localStorage) y esta ruta devuelve los datos en JSON.
Route::view('/favoritos', 'favoritos')->name('favoritos');
Route::get('/favoritos/datos', FavoritoController::class)->name('favoritos.datos');

/*
|--------------------------------------------------------------------------
| Panel de administración
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {

    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'create'])->name('login');
        Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:10,1')->name('login.store');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

        Route::get('/', DashboardController::class)->name('dashboard');

        // Propiedades (ABM)
        Route::resource('propiedades', AdminPropiedadController::class)
            ->parameters(['propiedades' => 'propiedad'])
            ->except('show');
        Route::patch('/propiedades/{propiedad}/estado', [AdminPropiedadController::class, 'cambiarEstado'])
            ->name('propiedades.estado');

        // Imágenes de una propiedad
        Route::patch('/imagenes/{imagen}/portada', [ImagenController::class, 'portada'])->name('imagenes.portada');
        Route::delete('/imagenes/{imagen}', [ImagenController::class, 'destroy'])->name('imagenes.destroy');

        // Consultas recibidas
        Route::get('/consultas', [AdminConsultaController::class, 'index'])->name('consultas.index');
        Route::get('/consultas/{consulta}', [AdminConsultaController::class, 'show'])->name('consultas.show');
        Route::patch('/consultas/{consulta}/leida', [AdminConsultaController::class, 'alternarLeida'])->name('consultas.leida');
        Route::delete('/consultas/{consulta}', [AdminConsultaController::class, 'destroy'])->name('consultas.destroy');

        // Datos del usuario logueado
        Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
        Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
    });
});
