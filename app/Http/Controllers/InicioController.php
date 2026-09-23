<?php

namespace App\Http\Controllers;

use App\Models\Propiedad;
use Illuminate\View\View;

class InicioController extends Controller
{
    public function __invoke(): View
    {
        // Propiedades marcadas como destacadas desde el panel.
        $destacadas = Propiedad::publicadas()
            ->destacadas()
            ->with('portada')
            ->latest()
            ->take(6)
            ->get();

        // Si hay pocas destacadas completo con las últimas cargadas para que
        // la sección del inicio no quede vacía.
        if ($destacadas->count() < 3) {
            $destacadas = $destacadas->concat(
                Propiedad::publicadas()
                    ->whereNotIn('id', $destacadas->pluck('id'))
                    ->with('portada')
                    ->latest()
                    ->take(3 - $destacadas->count())
                    ->get()
            );
        }

        return view('inicio', [
            'destacadas' => $destacadas,
            'localidades' => $this->localidades(),
            'totalPropiedades' => Propiedad::publicadas()->count(),
        ]);
    }

    private function localidades()
    {
        return Propiedad::publicadas()->distinct()->orderBy('localidad')->pluck('localidad');
    }
}
