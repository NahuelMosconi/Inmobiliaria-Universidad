<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use App\Models\Propiedad;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        // Números para las tarjetas de resumen.
        $resumen = [
            'propiedades' => Propiedad::count(),
            'publicadas' => Propiedad::publicadas()->count(),
            'consultasSinLeer' => Consulta::noLeidas()->count(),
            'consultasMes' => Consulta::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        // Cantidad de propiedades publicadas por operación (venta / alquiler).
        $porOperacion = Propiedad::publicadas()
            ->selectRaw('operacion, count(*) as total')
            ->groupBy('operacion')
            ->pluck('total', 'operacion');

        return view('admin.dashboard', [
            'resumen' => $resumen,
            'porOperacion' => $porOperacion,
            'ultimasConsultas' => Consulta::with('propiedad')->latest()->take(5)->get(),
            'masVisitadas' => Propiedad::publicadas()->orderByDesc('visitas')->take(5)->get(),
        ]);
    }
}
