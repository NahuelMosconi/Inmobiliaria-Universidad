@extends('admin.layout')

@section('titulo', 'Propiedades')

@section('acciones')
    <a href="{{ route('admin.propiedades.create') }}" class="boton">+ Nueva propiedad</a>
@endsection

@section('contenido')
    <!-- Filtros -->
    <form class="barra-filtros" method="GET" action="{{ route('admin.propiedades.index') }}">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por título o localidad">
        <select name="estado" data-autoenviar>
            <option value="">Todos los estados</option>
            <option value="publicadas" @selected(request('estado') === 'publicadas')>Publicadas</option>
            <option value="ocultas" @selected(request('estado') === 'ocultas')>Ocultas</option>
            <option value="destacadas" @selected(request('estado') === 'destacadas')>Destacadas</option>
        </select>
        <select name="tipo" data-autoenviar>
            <option value="">Todos los tipos</option>
            @foreach (\App\Models\Propiedad::TIPOS as $valor => $nombre)
                <option value="{{ $valor }}" @selected(request('tipo') === $valor)>{{ $nombre }}</option>
            @endforeach
        </select>
        <button type="submit" class="boton boton-secundario">Buscar</button>
        @if (request()->hasAny(['q', 'estado', 'tipo']))
            <a href="{{ route('admin.propiedades.index') }}">Limpiar</a>
        @endif
    </form>

    @if ($propiedades->isEmpty())
        <div class="panel vacio">
            <p>No hay propiedades para mostrar.</p>
            <a href="{{ route('admin.propiedades.create') }}" class="boton">Cargar la primera</a>
        </div>
    @else
        <div class="tabla-contenedor">
            <table class="tabla">
                <thead>
                    <tr>
                        <th></th>
                        <th>Propiedad</th>
                        <th>Precio</th>
                        <th>Visitas</th>
                        <th>Consultas</th>
                        <th>Estado</th>
                        <th class="derecha">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($propiedades as $propiedad)
                        <tr>
                            <td><img src="{{ $propiedad->url_portada }}" alt="" class="miniatura"></td>
                            <td>
                                <a href="{{ route('admin.propiedades.edit', $propiedad) }}" class="tabla-titulo">{{ $propiedad->titulo }}</a>
                                <small>{{ $propiedad->tipo_nombre }} en {{ strtolower($propiedad->operacion_nombre) }} &middot; {{ $propiedad->localidad }}</small>
                            </td>
                            <td class="sin-salto">{{ $propiedad->precio_formateado }}</td>
                            <td>{{ $propiedad->visitas }}</td>
                            <td>{{ $propiedad->consultas_count }}</td>
                            <td class="sin-salto">
                                {{-- Botones para cambiar el estado sin entrar a editar --}}
                                <form action="{{ route('admin.propiedades.estado', $propiedad) }}" method="POST" class="en-linea">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="campo" value="publicada">
                                    <button type="submit" @class(['chip', 'chip-verde' => $propiedad->publicada, 'chip-gris' => ! $propiedad->publicada])
                                            title="Clic para {{ $propiedad->publicada ? 'ocultar' : 'publicar' }}">
                                        {{ $propiedad->publicada ? 'Publicada' : 'Oculta' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.propiedades.estado', $propiedad) }}" method="POST" class="en-linea">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="campo" value="destacada">
                                    <button type="submit" @class(['chip', 'chip-amarillo' => $propiedad->destacada, 'chip-gris' => ! $propiedad->destacada])
                                            title="Clic para {{ $propiedad->destacada ? 'quitar de' : 'mostrar en' }} el inicio">
                                        {{ $propiedad->destacada ? '★ Destacada' : '☆ Destacar' }}
                                    </button>
                                </form>
                            </td>
                            <td class="derecha sin-salto">
                                <a href="{{ route('propiedades.show', $propiedad) }}" target="_blank" class="boton-chico">Ver</a>
                                <a href="{{ route('admin.propiedades.edit', $propiedad) }}" class="boton-chico">Editar</a>
                                <form action="{{ route('admin.propiedades.destroy', $propiedad) }}" method="POST" class="en-linea"
                                      data-confirmar="¿Seguro que querés eliminar &quot;{{ $propiedad->titulo }}&quot;? Se borran también sus fotos.">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="boton-chico peligro">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $propiedades->links() }}
    @endif
@endsection
