@extends('layouts.app')

@section('titulo', 'Propiedades')
@section('descripcion', 'Casas, departamentos, terrenos y locales en venta y alquiler en Mendoza.')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/propiedades.css') }}">
@endpush

@section('contenido')
    <main class="container">
        <h2 class="titulo-propiedades">Propiedades</h2>

        <div class="propiedades-layout">
            <!-- FILTROS -->
            <aside class="filtros">
                {{-- En celulares los filtros arrancan cerrados (ver sitio.js) --}}
                <button type="button" class="filtros-toggle" aria-expanded="false" aria-controls="form-filtros">
                    Filtrar búsqueda
                </button>

                <form id="form-filtros" class="filtros-form" action="{{ route('propiedades.index') }}" method="GET">
                    <div class="form-grupo">
                        <label for="q">Buscar</label>
                        <input type="search" id="q" name="q" value="{{ $filtros['q'] ?? '' }}" placeholder="Ej: pileta, Chacras...">
                    </div>

                    <div class="form-grupo">
                        <label for="operacion">Operación</label>
                        <select id="operacion" name="operacion">
                            <option value="">Todas</option>
                            @foreach (\App\Models\Propiedad::OPERACIONES as $valor => $nombre)
                                <option value="{{ $valor }}" @selected(($filtros['operacion'] ?? '') === $valor)>{{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-grupo">
                        <label for="tipo">Tipo</label>
                        <select id="tipo" name="tipo">
                            <option value="">Todos</option>
                            @foreach (\App\Models\Propiedad::TIPOS as $valor => $nombre)
                                <option value="{{ $valor }}" @selected(($filtros['tipo'] ?? '') === $valor)>{{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-grupo">
                        <label for="localidad">Localidad</label>
                        <select id="localidad" name="localidad">
                            <option value="">Todas</option>
                            @foreach ($localidades as $localidad)
                                <option value="{{ $localidad }}" @selected(($filtros['localidad'] ?? '') === $localidad)>{{ $localidad }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-grupo">
                        <label for="dormitorios">Dormitorios</label>
                        <select id="dormitorios" name="dormitorios">
                            <option value="">Indistinto</option>
                            @foreach ([1, 2, 3, 4] as $cantidad)
                                <option value="{{ $cantidad }}" @selected(($filtros['dormitorios'] ?? '') == $cantidad)>{{ $cantidad }} o más</option>
                            @endforeach
                        </select>
                    </div>

                    <fieldset class="form-grupo">
                        <legend>Precio</legend>
                        <select name="moneda" aria-label="Moneda">
                            @foreach (\App\Models\Propiedad::MONEDAS as $valor => $nombre)
                                <option value="{{ $valor }}" @selected(($filtros['moneda'] ?? 'USD') === $valor)>{{ $nombre }}</option>
                            @endforeach
                        </select>
                        <div class="rango-precio">
                            <input type="number" name="precio_min" min="0" step="1000" value="{{ $filtros['precio_min'] ?? '' }}" placeholder="Desde" aria-label="Precio desde">
                            <input type="number" name="precio_max" min="0" step="1000" value="{{ $filtros['precio_max'] ?? '' }}" placeholder="Hasta" aria-label="Precio hasta">
                        </div>
                    </fieldset>

                    {{-- Mantengo el orden elegido al aplicar filtros --}}
                    <input type="hidden" name="orden" value="{{ $orden }}">

                    <button type="submit" class="boton boton-ancho">Aplicar filtros</button>
                    @if ($hayFiltros)
                        <a href="{{ route('propiedades.index') }}" class="limpiar-filtros">Limpiar filtros</a>
                    @endif
                </form>
            </aside>

            <!-- RESULTADOS -->
            <section class="resultados">
                <div class="resultados-barra">
                    <p>
                        @if ($propiedades->total() === 1)
                            Se encontró <strong>1</strong> propiedad
                        @else
                            Se encontraron <strong>{{ $propiedades->total() }}</strong> propiedades
                        @endif
                    </p>

                    {{-- Al cambiar el orden se envía solo (data-autoenviar en sitio.js) --}}
                    <form action="{{ route('propiedades.index') }}" method="GET" class="form-orden">
                        @foreach ($filtros as $nombre => $valor)
                            @if ($valor !== null && $valor !== '')
                                <input type="hidden" name="{{ $nombre }}" value="{{ $valor }}">
                            @endif
                        @endforeach
                        <label for="orden">Ordenar por</label>
                        <select id="orden" name="orden" data-autoenviar>
                            @foreach (\App\Models\Propiedad::ORDENES as $valor => $nombre)
                                <option value="{{ $valor }}" @selected($orden === $valor)>{{ $nombre }}</option>
                            @endforeach
                        </select>
                        <noscript><button type="submit" class="boton">Ordenar</button></noscript>
                    </form>
                </div>

                @if ($propiedades->isEmpty())
                    <div class="sin-resultados">
                        <p>No encontramos propiedades con esos filtros.</p>
                        <p>Probá ampliando la búsqueda o <a href="{{ route('contacto') }}">contanos qué estás buscando</a> y te avisamos cuando tengamos algo.</p>
                    </div>
                @else
                    <div class="grilla-propiedades">
                        @foreach ($propiedades as $propiedad)
                            @include('partials.tarjeta-propiedad')
                        @endforeach
                    </div>

                    {{ $propiedades->links() }}
                @endif
            </section>
        </div>
    </main>
@endsection
