<?php

namespace App\Providers;

use App\Models\Consulta;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // URLs en español para las rutas "resource": /propiedades/crear y /propiedades/{x}/editar
        Route::resourceVerbs([
            'create' => 'crear',
            'edit' => 'editar',
        ]);

        // Paginación con diseño propio (resources/views/partials/paginacion.blade.php)
        Paginator::defaultView('partials.paginacion');

        // El menú del panel muestra cuántas consultas hay sin leer.
        View::composer('admin.layout', function ($view) {
            $view->with('consultasSinLeer', Consulta::noLeidas()->count());
        });
    }
}
