{{-- Base para las páginas de error. Usa el mismo diseño del sitio. --}}
@extends('layouts.app')

@section('contenido')
    <main class="container pagina-mensaje">
        <p class="codigo-error">@yield('codigo')</p>
        <h2>@yield('mensaje')</h2>
        <p>@yield('detalle')</p>
        <div class="pagina-mensaje-botones">
            <a href="{{ route('inicio') }}" class="boton">Volver al inicio</a>
            <a href="{{ route('propiedades.index') }}" class="boton boton-secundario">Ver propiedades</a>
        </div>
    </main>
@endsection
