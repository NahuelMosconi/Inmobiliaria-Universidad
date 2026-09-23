<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PropiedadImagen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ImagenController extends Controller
{
    /**
     * Pone la foto elegida como portada (primera del orden) y corre las demás.
     */
    public function portada(PropiedadImagen $imagen): RedirectResponse
    {
        DB::transaction(function () use ($imagen) {
            $resto = $imagen->propiedad->imagenes->reject(fn ($i) => $i->is($imagen))->values();

            $imagen->update(['orden' => 0]);

            foreach ($resto as $indice => $otra) {
                $otra->update(['orden' => $indice + 1]);
            }
        });

        return back()->with('exito', 'Se cambió la foto de portada.');
    }

    public function destroy(PropiedadImagen $imagen): RedirectResponse
    {
        $imagen->delete(); // también borra el archivo (ver PropiedadImagen::booted)

        return back()->with('exito', 'La foto se eliminó.');
    }
}
