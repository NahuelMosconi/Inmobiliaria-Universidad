@extends('layouts.app')

@section('titulo', 'Gracias')

@section('contenido')
    <main class="container pagina-mensaje">
        <h2>¡Gracias por contactarnos!</h2>
        <p>Hemos recibido tu mensaje y te responderemos a la brevedad.</p>
        <div class="pagina-mensaje-botones">
            <a href="{{ route('propiedades.index') }}" class="boton">Ver propiedades</a>
            <a href="{{ route('inicio') }}" class="boton boton-secundario">Volver al inicio</a>
        </div>
    </main>
@endsection
