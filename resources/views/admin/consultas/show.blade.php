@extends('admin.layout')

@section('titulo', 'Consulta de '.$consulta->nombre)

@section('acciones')
    <a href="{{ route('admin.consultas.index') }}" class="boton boton-secundario">&larr; Volver</a>
@endsection

@section('contenido')
    <div class="dos-columnas">
        <section class="panel">
            <h2>Mensaje</h2>
            <p class="texto-suave">Recibida el {{ $consulta->created_at->format('d/m/Y \a \l\a\s H:i') }} ({{ $consulta->created_at->diffForHumans() }})</p>
            <div class="mensaje-consulta">{!! nl2br(e($consulta->mensaje)) !!}</div>

            <div class="acciones-form">
                <form action="{{ route('admin.consultas.leida', $consulta) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="boton boton-secundario">Marcar como no leída</button>
                </form>
                <form action="{{ route('admin.consultas.destroy', $consulta) }}" method="POST" data-confirmar="¿Eliminar esta consulta?">
                    @csrf @method('DELETE')
                    <button type="submit" class="boton peligro">Eliminar</button>
                </form>
            </div>
        </section>

        <section class="panel">
            <h2>Datos de contacto</h2>
            <dl class="datos">
                <dt>Nombre</dt>
                <dd>{{ $consulta->nombre }}</dd>
                <dt>Email</dt>
                <dd><a href="mailto:{{ $consulta->email }}?subject={{ rawurlencode('Re: tu consulta en '.config('inmobiliaria.nombre')) }}">{{ $consulta->email }}</a></dd>
                <dt>Teléfono</dt>
                <dd>
                    @if ($consulta->telefono)
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $consulta->telefono) }}">{{ $consulta->telefono }}</a>
                    @else
                        No informado
                    @endif
                </dd>
                <dt>Propiedad</dt>
                <dd>
                    @if ($consulta->propiedad)
                        <a href="{{ route('admin.propiedades.edit', $consulta->propiedad) }}">{{ $consulta->propiedad->titulo }}</a>
                    @else
                        Consulta general (desde la página de contacto)
                    @endif
                </dd>
            </dl>

            <a class="boton boton-ancho" href="mailto:{{ $consulta->email }}?subject={{ rawurlencode('Re: tu consulta en '.config('inmobiliaria.nombre')) }}">Responder por email</a>
        </section>
    </div>
@endsection
