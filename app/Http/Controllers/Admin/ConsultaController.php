<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Bandeja de consultas que llegan desde el formulario de contacto
 * y desde el detalle de cada propiedad.
 */
class ConsultaController extends Controller
{
    public function index(Request $request): View
    {
        $consultas = Consulta::query()
            ->with('propiedad')
            ->when($request->query('estado') === 'sin_leer', fn ($q) => $q->noLeidas())
            ->when($request->query('estado') === 'leidas', fn ($q) => $q->leidas())
            // Filtro por propiedad (se usa desde la pantalla de edición de una propiedad).
            ->when($request->filled('propiedad'), fn ($q) => $q->where('propiedad_id', $request->integer('propiedad')))
            ->when($request->filled('q'), function ($query) use ($request) {
                $texto = '%'.$request->query('q').'%';
                $query->where(fn ($q) => $q
                    ->where('nombre', 'like', $texto)
                    ->orWhere('email', 'like', $texto)
                    ->orWhere('mensaje', 'like', $texto));
            })
            ->latest()
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.consultas.index', compact('consultas'));
    }

    public function show(Consulta $consulta): View
    {
        // Al abrirla se marca como leída automáticamente.
        $consulta->marcarComoLeida();
        $consulta->load('propiedad');

        return view('admin.consultas.show', compact('consulta'));
    }

    public function alternarLeida(Consulta $consulta): RedirectResponse
    {
        if ($consulta->leida) {
            $consulta->marcarComoNoLeida();

            // Si la marco como no leída desde el detalle vuelvo a la bandeja,
            // porque si me quedo en el detalle se volvería a marcar como leída.
            return redirect()->route('admin.consultas.index')->with('exito', 'La consulta quedó como no leída.');
        }

        $consulta->marcarComoLeida();

        return back()->with('exito', 'La consulta se marcó como leída.');
    }

    public function destroy(Consulta $consulta): RedirectResponse
    {
        $consulta->delete();

        return redirect()->route('admin.consultas.index')->with('exito', 'La consulta se eliminó.');
    }
}
