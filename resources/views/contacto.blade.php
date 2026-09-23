@extends('layouts.app')

@section('titulo', 'Contacto')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/contacto.css') }}">
@endpush

@section('contenido')
    <main class="container">
        <div class="contacto-layout">
            <div class="contacto-contenido">
                <h2>Estamos para ayudarlo</h2>
                <p class="subtitulo-contacto">Vender y comprar puede ser muy estresante y elegir el agente adecuado puede ser un proceso difícil. Llámenos para una charla sin compromiso y algunos consejos útiles sobre la venta de su propiedad.</p>

                @if ($propiedad)
                    <p class="consulta-sobre">Consulta sobre: <a href="{{ $propiedad->url }}">{{ $propiedad->titulo }}</a></p>
                @endif

                @include('partials.form-consulta', ['propiedad' => $propiedad, 'textoBoton' => 'Enviar mensaje'])
            </div>

            <aside class="contacto-datos">
                <h3>Nuestra oficina</h3>
                <ul>
                    <li><strong>Dirección</strong>{{ config('inmobiliaria.direccion') }}</li>
                    <li><strong>Teléfonos</strong>{{ implode(' - ', config('inmobiliaria.telefonos')) }}</li>
                    <li><strong>Email</strong><a href="mailto:{{ config('inmobiliaria.email') }}">{{ config('inmobiliaria.email') }}</a></li>
                    <li><strong>Horario de atención</strong>{{ config('inmobiliaria.horario') }}</li>
                </ul>
                <a class="boton-whatsapp" target="_blank" rel="noopener" href="https://wa.me/{{ config('inmobiliaria.whatsapp') }}">
                    @include('partials.icono-whatsapp') Escribinos por WhatsApp
                </a>
            </aside>
        </div>
    </main>
@endsection
