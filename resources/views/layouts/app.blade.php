<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('descripcion', 'Compra, venta y alquiler de casas, departamentos, terrenos y locales en Mendoza.')">
    <title>@yield('titulo', 'Inicio') - {{ config('inmobiliaria.nombre') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/general.css') }}">
    @stack('estilos')
</head>
<body>
    <!-- HEADER (es el mismo en todas las páginas) -->
    <header class="header">
        <div class="header-contenido">
            <a href="{{ route('inicio') }}" class="logoTitulo">
                <img src="{{ asset('img/logo.png') }}" alt="Logo de {{ config('inmobiliaria.nombre') }}">
                <span>{{ config('inmobiliaria.nombre') }}</span>
            </a>

            <!-- Botón hamburguesa: solo se ve en celulares (ver sitio.js) -->
            <button type="button" class="menu-boton" aria-label="Abrir menú" aria-expanded="false" aria-controls="menu-principal">
                <span></span><span></span><span></span>
            </button>

            <nav id="menu-principal">
                <ul class="nav">
                    <li><a href="{{ route('inicio') }}" @class(['activo' => request()->routeIs('inicio')])>Inicio</a></li>
                    <li><a href="{{ route('propiedades.index') }}" @class(['activo' => request()->routeIs('propiedades.*')])>Propiedades</a></li>
                    <li><a href="{{ route('nosotros') }}" @class(['activo' => request()->routeIs('nosotros')])>Nosotros</a></li>
                    <li><a href="{{ route('contacto') }}" @class(['activo' => request()->routeIs('contacto')])>Contacto</a></li>
                    <li>
                        <a href="{{ route('favoritos') }}" @class(['activo' => request()->routeIs('favoritos')]) title="Mis favoritos">
                            &#9829; Favoritos <span class="contador-favoritos" hidden>0</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    @include('partials.mensajes')

    @yield('contenido')

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container footer-grid">
            <div class="logo-footer">
                <img src="{{ asset('img/logo.png') }}" alt="Logo de {{ config('inmobiliaria.nombre') }}" loading="lazy">
            </div>
            <div class="footer-columna">
                <h3>Ubicación / Contacto</h3>
                <ul>
                    <li>{{ config('inmobiliaria.direccion') }}</li>
                    <li>{{ implode(' - ', config('inmobiliaria.telefonos')) }}</li>
                    <li><a href="mailto:{{ config('inmobiliaria.email') }}">{{ config('inmobiliaria.email') }}</a></li>
                    <li>{{ config('inmobiliaria.horario') }}</li>
                </ul>
            </div>
            <div class="footer-columna">
                <h3>Navegación</h3>
                <ul>
                    <li><a href="{{ route('propiedades.index', ['operacion' => 'venta']) }}">Propiedades en venta</a></li>
                    <li><a href="{{ route('propiedades.index', ['operacion' => 'alquiler']) }}">Propiedades en alquiler</a></li>
                    <li><a href="{{ route('nosotros') }}">Nosotros</a></li>
                    <li><a href="{{ route('contacto') }}">Contacto</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-inferior">
            <p>&copy; {{ date('Y') }} {{ config('inmobiliaria.nombre') }} &middot; <a href="{{ route('admin.login') }}">Acceso administración</a></p>
        </div>
    </footer>

    <!-- Botón flotante de WhatsApp -->
    <a href="https://wa.me/{{ config('inmobiliaria.whatsapp') }}?text={{ urlencode('Hola! Quería hacer una consulta.') }}"
       class="whatsapp-flotante" target="_blank" rel="noopener" aria-label="Escribinos por WhatsApp">
        @include('partials.icono-whatsapp')
    </a>

    <script src="{{ asset('js/favoritos.js') }}"></script>
    <script src="{{ asset('js/sitio.js') }}"></script>
    @stack('scripts')
</body>
</html>
