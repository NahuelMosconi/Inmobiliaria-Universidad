@extends('layouts.app')

@section('titulo', 'Inicio')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
@endpush

@section('contenido')
    <!-- HERO con el buscador -->
    <section class="hero">
        <form class="buscador" action="{{ route('propiedades.index') }}" method="GET">
            <div class="buscador-campo">
                <label for="buscador-operacion">Operación</label>
                <select id="buscador-operacion" name="operacion">
                    <option value="">Todas</option>
                    @foreach (\App\Models\Propiedad::OPERACIONES as $valor => $nombre)
                        <option value="{{ $valor }}">{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="buscador-campo">
                <label for="buscador-tipo">Tipo de propiedad</label>
                <select id="buscador-tipo" name="tipo">
                    <option value="">Todos</option>
                    @foreach (\App\Models\Propiedad::TIPOS as $valor => $nombre)
                        <option value="{{ $valor }}">{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="buscador-campo">
                <label for="buscador-localidad">Localidad</label>
                <select id="buscador-localidad" name="localidad">
                    <option value="">Todas</option>
                    @foreach ($localidades as $localidad)
                        <option value="{{ $localidad }}">{{ $localidad }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="boton">Buscar</button>
        </form>
    </section>

    <!-- Sección Bienvenida -->
    <section class="intro">
        <div class="container">
            <h2>Bienvenido a Triángulo Inmobiliaria</h2>
            <p>¡Te damos la más cordial bienvenida a Triángulo Inmobiliaria! En este espacio, nuestro principal objetivo es acompañarte en uno de los pasos más importantes de tu vida: encontrar el lugar perfecto para llamar hogar o la inversión ideal para tu futuro. Con un profundo conocimiento del mercado local, nos especializamos en la compra, venta y alquiler de una diversa gama de propiedades —desde acogedoras casas y modernos departamentos hasta amplios terrenos— en el corazón de la hermosa provincia de <strong>Mendoza</strong>.</p>
            <p>Nuestro equipo de profesionales está comprometido a brindarte una asesoría personalizada, transparente y eficiente. Te invitamos a explorar nuestras propiedades destacadas y a descubrir todo lo que tenemos para ofrecerte. No dudes en contactarte ante cualquier duda desde nuestra sección de contacto.</p>
        </div>
    </section>

    <!-- Propiedades destacadas (se eligen desde el panel) -->
    @if ($destacadas->isNotEmpty())
        <section class="destacadas">
            <div class="container">
                <h2>Propiedades destacadas</h2>
                <div class="grilla-propiedades">
                    @foreach ($destacadas as $propiedad)
                        @include('partials.tarjeta-propiedad')
                    @endforeach
                </div>
                <div class="centrado">
                    <a href="{{ route('propiedades.index') }}" class="boton boton-secundario">
                        Ver las {{ $totalPropiedades }} propiedades disponibles
                    </a>
                </div>
            </div>
        </section>
    @endif

    <!-- Por qué elegirnos -->
    <section class="beneficios">
        <div class="container beneficios-grilla">
            <div class="beneficio">
                <span class="beneficio-icono">&#9733;</span>
                <h3>Asesoramiento personalizado</h3>
                <p>Te acompañamos en todo el proceso, desde la primera visita hasta la firma.</p>
            </div>
            <div class="beneficio">
                <span class="beneficio-icono">&#9873;</span>
                <h3>Conocemos Mendoza</h3>
                <p>Trabajamos en Ciudad, Godoy Cruz, Luján de Cuyo, Maipú, Guaymallén y Las Heras.</p>
            </div>
            <div class="beneficio">
                <span class="beneficio-icono">&#10004;</span>
                <h3>Operaciones seguras</h3>
                <p>Documentación revisada y tasaciones realistas, sin sorpresas.</p>
            </div>
        </div>
    </section>

    <!-- Llamado a la acción -->
    <section class="cta">
        <div class="container">
            <h2>¿Querés vender o alquilar tu propiedad?</h2>
            <p>Hacemos la tasación sin cargo. Dejanos tus datos y te llamamos.</p>
            <a href="{{ route('contacto') }}" class="boton boton-claro">Quiero que me contacten</a>
        </div>
    </section>
@endsection
