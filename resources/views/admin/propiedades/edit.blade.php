@extends('admin.layout')

@section('titulo', 'Editar propiedad')

@section('acciones')
    <a href="{{ route('propiedades.show', $propiedad) }}" target="_blank" class="boton boton-secundario">Ver en el sitio &nearr;</a>
@endsection

@section('contenido')
    <p class="texto-suave">
        {{ $propiedad->visitas }} visitas &middot;
        <a href="{{ route('admin.consultas.index', ['propiedad' => $propiedad->id]) }}">{{ $propiedad->consultas_count }} consultas</a> &middot;
        Última modificación {{ $propiedad->updated_at->diffForHumans() }}
    </p>

    {{--
        Las fotos actuales van fuera del formulario principal porque cada botón
        (portada / eliminar) es su propio formulario, y en HTML no se pueden anidar forms.
    --}}
    <section class="panel panel-fotos">
        <h2>Fotos actuales ({{ $propiedad->imagenes->count() }})</h2>

        @if ($propiedad->imagenes->isEmpty())
            <p class="texto-suave">Esta propiedad todavía no tiene fotos. Podés agregarlas más abajo.</p>
        @else
            <div class="grilla-fotos">
                @foreach ($propiedad->imagenes as $imagen)
                    <div @class(['foto', 'foto-portada' => $loop->first])>
                        <img src="{{ $imagen->url }}" alt="{{ $imagen->descripcion }}">
                        <div class="foto-acciones">
                            @if ($loop->first)
                                <span class="chip chip-amarillo">Portada</span>
                            @else
                                <form action="{{ route('admin.imagenes.portada', $imagen) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="boton-chico">Usar de portada</button>
                                </form>
                            @endif
                            <form action="{{ route('admin.imagenes.destroy', $imagen) }}" method="POST" data-confirmar="¿Eliminar esta foto?">
                                @csrf @method('DELETE')
                                <button type="submit" class="boton-chico peligro">Eliminar</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <form action="{{ route('admin.propiedades.update', $propiedad) }}" method="POST" enctype="multipart/form-data" class="form-admin">
        @csrf
        @method('PUT')

        @include('admin.propiedades._form')

        <div class="acciones-form">
            <a href="{{ route('admin.propiedades.index') }}" class="boton boton-secundario">Volver al listado</a>
            <button type="submit" class="boton">Guardar cambios</button>
        </div>
    </form>

    <section class="panel zona-peligro">
        <h2>Eliminar propiedad</h2>
        <p>Se borran la propiedad y todas sus fotos. Las consultas recibidas se conservan. No se puede deshacer.</p>
        <form action="{{ route('admin.propiedades.destroy', $propiedad) }}" method="POST"
              data-confirmar="¿Seguro que querés eliminar esta propiedad? No se puede deshacer.">
            @csrf @method('DELETE')
            <button type="submit" class="boton peligro">Eliminar propiedad</button>
        </form>
    </section>
@endsection
