<?php

namespace App\Http\Controllers;

use App\Models\Propiedad;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Los favoritos se guardan en el navegador del visitante (localStorage), no en
 * la base de datos, así no hace falta que se registre. El JavaScript de la
 * página de favoritos le pide a esta ruta los datos de esas propiedades.
 */
class FavoritoController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'ids' => ['array', 'max:50'],
            'ids.*' => ['integer'],
        ]);

        $propiedades = Propiedad::publicadas()
            ->whereIn('id', $datos['ids'] ?? [])
            ->with('portada')
            ->get()
            ->map(fn (Propiedad $p) => [
                'id' => $p->id,
                'titulo' => $p->titulo,
                'url' => $p->url,
                'imagen' => $p->url_portada,
                'precio' => $p->precio_formateado,
                'operacion' => $p->operacion_nombre,
                'tipo' => $p->tipo_nombre,
                'localidad' => $p->localidad,
                'resumen' => $p->resumen,
            ]);

        return response()->json($propiedades);
    }
}
