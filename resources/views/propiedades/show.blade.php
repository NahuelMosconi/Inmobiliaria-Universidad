@extends('layouts.app')

@section('titulo', $propiedad->titulo)
@section('descripcion', $propiedad->resumen)

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/propiedades.css') }}">
    <link rel="stylesheet" href="{{ asset('css/propiedad-detalle.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/galeria.js') }}"></script>
@endpush

@section('contenido')
    <main class="container detalle">

        @unless ($propiedad->publicada)
            <p class="aviso-borrador">Esta propiedad está oculta: solo la ves porque iniciaste sesión en el panel.</p>
        @endunless

        <!-- Migas de pan -->
        <nav class="migas" aria-label="Ubicación en el sitio">
            <a href="{{ route('inicio') }}">Inicio</a> /
            <a href="{{ route('propiedades.index') }}">Propiedades</a> /
            <a href="{{ route('propiedades.index', ['tipo' => $propiedad->tipo]) }}">{{ $propiedad->tipo_nombre }}</a> /
            <span>{{ $propiedad->titulo }}</span>
        </nav>

        <!-- Encabezado -->
        <div class="detalle-encabezado">
            <div>
                <span class="etiqueta etiqueta-{{ $propiedad->operacion }}">{{ $propiedad->operacion_nombre }}</span>
                <h2>{{ $propiedad->titulo }}</h2>
                <p class="detalle-localidad">{{ $propiedad->direccion ? $propiedad->direccion.', ' : '' }}{{ $propiedad->localidad }}, Mendoza</p>
            </div>
            <div class="detalle-precio-caja">
                <p class="precio-detalle">{{ $propiedad->precio_formateado }}</p>
                @if ($propiedad->expensas_formateadas)
                    <p class="expensas">+ {{ $propiedad->expensas_formateadas }} de expensas</p>
                @endif
                <div class="detalle-acciones">
                    <button type="button" class="boton-favorito boton-favorito-texto" data-favorito="{{ $propiedad->id }}">&#9829; <span>Guardar</span></button>
                    <button type="button" class="boton-compartir" data-compartir data-titulo="{{ $propiedad->titulo }}">Compartir</button>
                </div>
            </div>
        </div>

        <!-- Galería de imágenes (galeria.js arma el visor a pantalla completa) -->
        @if ($propiedad->imagenes->isNotEmpty())
            <div class="galeria-propiedad" data-galeria>
                <button type="button" class="galeria-principal">
                    <img src="{{ $propiedad->imagenes->first()->url }}" alt="{{ $propiedad->imagenes->first()->descripcion ?? $propiedad->titulo }}">
                    @if ($propiedad->imagenes->count() > 1)
                        <span class="galeria-cantidad">Ver las {{ $propiedad->imagenes->count() }} fotos</span>
                    @endif
                </button>
                <div class="galeria-miniaturas">
                    @foreach ($propiedad->imagenes as $imagen)
                        <button type="button" data-src="{{ $imagen->url }}" @class(['activa' => $loop->first])>
                            <img src="{{ $imagen->url }}" alt="{{ $imagen->descripcion ?? $propiedad->titulo }}" loading="lazy">
                        </button>
                    @endforeach
                </div>
            </div>
        @else
            <div class="galeria-propiedad">
                <img src="{{ asset('img/sin-imagen.svg') }}" alt="Propiedad sin fotos" class="galeria-vacia">
            </div>
        @endif

        <div class="detalle-grilla">
            <!-- Columna izquierda: información -->
            <div class="detalle-info">

                <!-- Datos principales en iconos -->
                <ul class="datos-principales">
                    @if ($propiedad->superficie_total)
                        <li><strong>{{ number_format($propiedad->superficie_total, 0, ',', '.') }} m²</strong> totales</li>
                    @endif
                    @if ($propiedad->superficie_cubierta)
                        <li><strong>{{ number_format($propiedad->superficie_cubierta, 0, ',', '.') }} m²</strong> cubiertos</li>
                    @endif
                    @if ($propiedad->ambientes)
                        <li><strong>{{ $propiedad->ambientes }}</strong> ambientes</li>
                    @endif
                    @if ($propiedad->dormitorios)
                        <li><strong>{{ $propiedad->dormitorios }}</strong> dormitorios</li>
                    @endif
                    @if ($propiedad->banos)
                        <li><strong>{{ $propiedad->banos }}</strong> {{ $propiedad->banos == 1 ? 'baño' : 'baños' }}</li>
                    @endif
                    @if ($propiedad->cocheras)
                        <li><strong>{{ $propiedad->cocheras }}</strong> {{ $propiedad->cocheras == 1 ? 'cochera' : 'cocheras' }}</li>
                    @endif
                </ul>

                <section>
                    <h3>Descripción completa</h3>
                    {{-- e() escapa el HTML y nl2br respeta los saltos de línea que se cargaron en el panel --}}
                    <div class="descripcion">{!! nl2br(e($propiedad->descripcion)) !!}</div>
                </section>

                <section>
                    <h3>Características</h3>
                    <ul class="caracteristicas">
                        <li><strong>Tipo:</strong> {{ $propiedad->tipo_nombre }}</li>
                        <li><strong>Operación:</strong> {{ $propiedad->operacion_nombre }}</li>
                        @if ($propiedad->superficie_total)
                            <li><strong>Superficie total:</strong> {{ number_format($propiedad->superficie_total, 0, ',', '.') }} m²</li>
                        @endif
                        @if ($propiedad->superficie_cubierta)
                            <li><strong>Superficie cubierta:</strong> {{ number_format($propiedad->superficie_cubierta, 0, ',', '.') }} m²</li>
                        @endif
                        @if ($propiedad->ambientes)
                            <li><strong>Ambientes:</strong> {{ $propiedad->ambientes }}</li>
                        @endif
                        @if ($propiedad->dormitorios)
                            <li><strong>Dormitorios:</strong> {{ $propiedad->dormitorios }}</li>
                        @endif
                        @if ($propiedad->banos)
                            <li><strong>Baños:</strong> {{ $propiedad->banos }}</li>
                        @endif
                        @if ($propiedad->cocheras)
                            <li><strong>Cocheras:</strong> {{ $propiedad->cocheras }}</li>
                        @endif
                        @if ($propiedad->antiguedad !== null)
                            <li><strong>Antigüedad:</strong> {{ $propiedad->antiguedad == 0 ? 'A estrenar' : $propiedad->antiguedad.' años' }}</li>
                        @endif
                        @if ($propiedad->expensas_formateadas)
                            <li><strong>Expensas:</strong> {{ $propiedad->expensas_formateadas }} aprox.</li>
                        @endif
                    </ul>

                    @if (! empty($propiedad->extras))
                        <h4>Extras</h4>
                        <ul class="extras">
                            @foreach ($propiedad->extras as $extra)
                                <li>{{ $extra }}</li>
                            @endforeach
                        </ul>
                    @endif
                </section>

                <!-- Ubicación -->
                <section class="ubicacion">
                    <h3>Ubicación</h3>
                    <p>{{ $propiedad->direccion ? $propiedad->direccion.', ' : '' }}{{ $propiedad->localidad }}, Mendoza</p>

                    @if ($propiedad->tieneUbicacionEnMapa())
                        {{-- Mapa de OpenStreetMap: no necesita API key --}}
                        @php
                            $margen = 0.006;
                            $bbox = implode(',', [
                                $propiedad->longitud - $margen, $propiedad->latitud - $margen,
                                $propiedad->longitud + $margen, $propiedad->latitud + $margen,
                            ]);
                        @endphp
                        <iframe class="mapa" loading="lazy" title="Mapa de la ubicación"
                                src="https://www.openstreetmap.org/export/embed.html?bbox={{ $bbox }}&layer=mapnik&marker={{ $propiedad->latitud }},{{ $propiedad->longitud }}"></iframe>
                    @endif

                    @if ($propiedad->url_mapa)
                        <a href="{{ $propiedad->url_mapa }}" target="_blank" rel="noopener" class="link-mapa">Ver en Google Maps &rarr;</a>
                    @endif
                </section>
            </div>

            <!-- Columna derecha: formulario de consulta -->
            <aside class="detalle-contacto">
                <div class="caja-consulta">
                    <h3>¿Te interesa esta propiedad?</h3>
                    <p>Dejanos tus datos y te respondemos a la brevedad.</p>

                    @include('partials.form-consulta', ['textoBoton' => 'Enviar consulta'])

                    <a class="boton-whatsapp" target="_blank" rel="noopener"
                       href="https://wa.me/{{ config('inmobiliaria.whatsapp') }}?text={{ urlencode('Hola! Me interesa la propiedad "'.$propiedad->titulo.'": '.$propiedad->url) }}">
                        @include('partials.icono-whatsapp') Consultar por WhatsApp
                    </a>
                </div>
            </aside>
        </div>

        <!-- Propiedades similares -->
        @if ($similares->isNotEmpty())
            <section class="similares">
                <h3>Propiedades similares</h3>
                <div class="grilla-propiedades">
                    @foreach ($similares as $similar)
                        @include('partials.tarjeta-propiedad', ['propiedad' => $similar])
                    @endforeach
                </div>
            </section>
        @endif
    </main>
@endsection
