@extends('admin.layout')

@section('titulo', 'Consultas')

@section('contenido')
    <form class="barra-filtros" method="GET" action="{{ route('admin.consultas.index') }}">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre, email o mensaje">
        <select name="estado" data-autoenviar>
            <option value="">Todas</option>
            <option value="sin_leer" @selected(request('estado') === 'sin_leer')>Sin leer</option>
            <option value="leidas" @selected(request('estado') === 'leidas')>Leídas</option>
        </select>
        @if (request()->filled('propiedad'))
            <input type="hidden" name="propiedad" value="{{ request('propiedad') }}">
        @endif
        <button type="submit" class="boton boton-secundario">Buscar</button>
        @if (request()->hasAny(['q', 'estado', 'propiedad']))
            <a href="{{ route('admin.consultas.index') }}">Limpiar</a>
        @endif
    </form>

    @if ($consultas->isEmpty())
        <div class="panel vacio">
            <p>No hay consultas para mostrar.</p>
        </div>
    @else
        <div class="tabla-contenedor">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Nombre</th>
                        <th>Propiedad</th>
                        <th>Mensaje</th>
                        <th class="derecha">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($consultas as $consulta)
                        <tr @class(['no-leida' => ! $consulta->leida])>
                            <td class="sin-salto" title="{{ $consulta->created_at->format('d/m/Y H:i') }}">{{ $consulta->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('admin.consultas.show', $consulta) }}" class="tabla-titulo">{{ $consulta->nombre }}</a>
                                <small>{{ $consulta->email }}</small>
                            </td>
                            <td>{{ $consulta->propiedad->titulo ?? 'Consulta general' }}</td>
                            <td class="recortado">{{ \Illuminate\Support\Str::limit($consulta->mensaje, 80) }}</td>
                            <td class="derecha sin-salto">
                                <a href="{{ route('admin.consultas.show', $consulta) }}" class="boton-chico">Abrir</a>
                                <form action="{{ route('admin.consultas.leida', $consulta) }}" method="POST" class="en-linea">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="boton-chico">{{ $consulta->leida ? 'Marcar no leída' : 'Marcar leída' }}</button>
                                </form>
                                <form action="{{ route('admin.consultas.destroy', $consulta) }}" method="POST" class="en-linea" data-confirmar="¿Eliminar la consulta de {{ $consulta->nombre }}?">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="boton-chico peligro">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $consultas->links() }}
    @endif
@endsection
