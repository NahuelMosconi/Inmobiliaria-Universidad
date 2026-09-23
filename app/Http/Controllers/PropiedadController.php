<?php

namespace App\Http\Controllers;

use App\Models\Propiedad;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropiedadController extends Controller
{
    // Parámetros de búsqueda que acepta el listado.
    private const FILTROS = ['q', 'operacion', 'tipo', 'localidad', 'dormitorios', 'moneda', 'precio_min', 'precio_max'];

    /**
     * Listado de propiedades con filtros, orden y paginación.
     */
    public function index(Request $request): View
    {
        $filtros = $request->only(self::FILTROS);
        $orden = $request->query('orden', 'recientes');

        $propiedades = Propiedad::publicadas()
            ->filtrar($filtros)
            ->ordenar($orden)
            ->with('portada')
            ->paginate(config('inmobiliaria.por_pagina'))
            ->withQueryString(); // mantiene los filtros al cambiar de página

        return view('propiedades.index', [
            'propiedades' => $propiedades,
            'filtros' => $filtros,
            'orden' => array_key_exists($orden, Propiedad::ORDENES) ? $orden : 'recientes',
            'localidades' => Propiedad::publicadas()->distinct()->orderBy('localidad')->pluck('localidad'),
            // Sirve para mostrar el botón "limpiar filtros" solo cuando hace falta.
            'hayFiltros' => count(array_filter($filtros, fn ($valor) => $valor !== null && $valor !== '')) > 0,
        ]);
    }

    /**
     * Página de detalle de una propiedad.
     */
    public function show(Request $request, Propiedad $propiedad): View
    {
        // Las no publicadas solo las puede ver alguien logueado en el panel (vista previa).
        abort_if(! $propiedad->publicada && ! $request->user(), 404);

        $propiedad->load('imagenes');

        $this->contarVisita($request, $propiedad);

        // Propiedades parecidas: mismo tipo o misma localidad.
        $similares = Propiedad::publicadas()
            ->whereKeyNot($propiedad->id)
            ->where(fn ($q) => $q->where('tipo', $propiedad->tipo)->orWhere('localidad', $propiedad->localidad))
            ->with('portada')
            ->inRandomOrder()
            ->take(3)
            ->get();

        return view('propiedades.show', compact('propiedad', 'similares'));
    }

    /**
     * Suma una visita, pero una sola vez por sesión y sin contar al personal
     * de la inmobiliaria, para que el número sea más real.
     */
    private function contarVisita(Request $request, Propiedad $propiedad): void
    {
        $clave = 'propiedades_vistas';
        $vistas = $request->session()->get($clave, []);

        if ($request->user() || in_array($propiedad->id, $vistas)) {
            return;
        }

        // Uso el query builder directo para no modificar el updated_at de la propiedad.
        Propiedad::query()->toBase()->where('id', $propiedad->id)->increment('visitas');

        $request->session()->push($clave, $propiedad->id);
    }
}
