@extends('layouts.app')

@section('titulo', 'Mis favoritos')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/propiedades.css') }}">
@endpush

@section('contenido')
    <main class="container">
        <h2>Mis favoritos</h2>
        <p class="centrado texto-suave">Las propiedades que marcás con &#9829; quedan guardadas en este navegador.</p>

        {{-- favoritos.js completa esta grilla pidiendo los datos a route('favoritos.datos') --}}
        <div id="lista-favoritos" class="grilla-propiedades" data-url="{{ route('favoritos.datos') }}"></div>

        <div id="favoritos-vacio" class="sin-resultados" hidden>
            <p>Todavía no guardaste ninguna propiedad.</p>
            <a href="{{ route('propiedades.index') }}" class="boton">Ver propiedades</a>
        </div>

        <p id="favoritos-cargando" class="centrado texto-suave">Cargando...</p>
    </main>
@endsection
