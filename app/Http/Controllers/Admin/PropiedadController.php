<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PropiedadRequest;
use App\Models\Propiedad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PropiedadController extends Controller
{
    /**
     * Listado de todas las propiedades (publicadas y ocultas) con buscador.
     */
    public function index(Request $request): View
    {
        $propiedades = Propiedad::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $texto = '%'.$request->query('q').'%';
                $query->where(fn ($q) => $q->where('titulo', 'like', $texto)->orWhere('localidad', 'like', $texto));
            })
            ->when($request->query('estado') === 'publicadas', fn ($q) => $q->where('publicada', true))
            ->when($request->query('estado') === 'ocultas', fn ($q) => $q->where('publicada', false))
            ->when($request->query('estado') === 'destacadas', fn ($q) => $q->where('destacada', true))
            ->when(isset(Propiedad::TIPOS[$request->query('tipo')]), fn ($q) => $q->where('tipo', $request->query('tipo')))
            ->with('portada')
            ->withCount('consultas')
            ->latest()
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.propiedades.index', compact('propiedades'));
    }

    public function create(): View
    {
        // Valores por defecto del formulario de alta.
        $propiedad = new Propiedad([
            'operacion' => 'venta',
            'tipo' => 'casa',
            'moneda' => 'USD',
            'publicada' => true,
        ]);

        return view('admin.propiedades.create', [
            'propiedad' => $propiedad,
            'localidades' => $this->localidades(),
        ]);
    }

    public function store(PropiedadRequest $request): RedirectResponse
    {
        // Transacción: si falla la subida de una foto no queda la propiedad a medias.
        $propiedad = DB::transaction(function () use ($request) {
            $propiedad = Propiedad::create($request->safe()->except('imagenes'));
            $this->guardarImagenes($propiedad, $request->file('imagenes', []));

            return $propiedad;
        });

        return redirect()
            ->route('admin.propiedades.edit', $propiedad)
            ->with('exito', 'La propiedad se creó correctamente.');
    }

    public function edit(Propiedad $propiedad): View
    {
        $propiedad->load('imagenes')->loadCount('consultas');

        return view('admin.propiedades.edit', [
            'propiedad' => $propiedad,
            'localidades' => $this->localidades(),
        ]);
    }

    public function update(PropiedadRequest $request, Propiedad $propiedad): RedirectResponse
    {
        DB::transaction(function () use ($request, $propiedad) {
            $propiedad->update($request->safe()->except('imagenes'));
            $this->guardarImagenes($propiedad, $request->file('imagenes', []));
        });

        return redirect()
            ->route('admin.propiedades.edit', $propiedad)
            ->with('exito', 'Los cambios se guardaron correctamente.');
    }

    public function destroy(Propiedad $propiedad): RedirectResponse
    {
        $titulo = $propiedad->titulo;
        $propiedad->delete(); // el modelo se encarga de borrar las fotos

        return redirect()
            ->route('admin.propiedades.index')
            ->with('exito', "Se eliminó la propiedad \"{$titulo}\".");
    }

    /**
     * Botones rápidos del listado para publicar/ocultar y destacar/quitar destacado.
     */
    public function cambiarEstado(Request $request, Propiedad $propiedad): RedirectResponse
    {
        $campo = $request->validate(['campo' => ['required', 'in:publicada,destacada']])['campo'];

        $propiedad->update([$campo => ! $propiedad->{$campo}]);

        $mensajes = [
            'publicada' => $propiedad->publicada ? 'La propiedad ahora es visible en el sitio.' : 'La propiedad se ocultó del sitio.',
            'destacada' => $propiedad->destacada ? 'La propiedad ahora aparece en el inicio.' : 'La propiedad ya no aparece en el inicio.',
        ];

        return back()->with('exito', $mensajes[$campo]);
    }

    // Localidades ya cargadas, para sugerirlas en el formulario.
    private function localidades()
    {
        return Propiedad::distinct()->orderBy('localidad')->pluck('localidad');
    }

    /**
     * Guarda las fotos subidas en public/uploads/propiedades/{id}/ y las agrega
     * al final del orden actual.
     *
     * @param  UploadedFile[]  $archivos
     */
    private function guardarImagenes(Propiedad $propiedad, array $archivos): void
    {
        $orden = (int) $propiedad->imagenes()->max('orden');

        foreach ($archivos as $archivo) {
            // store() le pone un nombre aleatorio al archivo, así no se pisan fotos con el mismo nombre.
            $ruta = $archivo->store('propiedades/'.$propiedad->id, 'uploads');

            $propiedad->imagenes()->create([
                'ruta' => $ruta,
                'descripcion' => $propiedad->titulo,
                'orden' => ++$orden,
            ]);
        }
    }
}
