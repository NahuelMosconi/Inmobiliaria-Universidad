<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex">
    <title>@yield('titulo', 'Panel') - Administración</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="admin">
    <!-- Barra lateral -->
    <aside class="admin-lateral">
        <a href="{{ route('admin.dashboard') }}" class="admin-marca">
            <img src="{{ asset('img/logo.png') }}" alt="">
            <span>Panel Triángulo</span>
        </a>

        <nav class="admin-menu">
            <a href="{{ route('admin.dashboard') }}" @class(['activo' => request()->routeIs('admin.dashboard')])>Resumen</a>
            <a href="{{ route('admin.propiedades.index') }}" @class(['activo' => request()->routeIs('admin.propiedades.index', 'admin.propiedades.edit')])>Propiedades</a>
            <a href="{{ route('admin.propiedades.create') }}" @class(['activo' => request()->routeIs('admin.propiedades.create')])>+ Nueva propiedad</a>
            <a href="{{ route('admin.consultas.index') }}" @class(['activo' => request()->routeIs('admin.consultas.*')])>
                Consultas
                @if ($consultasSinLeer > 0)
                    <span class="insignia" title="Consultas sin leer">{{ $consultasSinLeer }}</span>
                @endif
            </a>
            <a href="{{ route('admin.perfil.edit') }}" @class(['activo' => request()->routeIs('admin.perfil.*')])>Mi perfil</a>
            <a href="{{ route('inicio') }}" target="_blank">Ver sitio &nearr;</a>
        </nav>

        <form action="{{ route('admin.logout') }}" method="POST" class="admin-salir">
            @csrf
            <p>{{ auth()->user()->name }}</p>
            <button type="submit">Cerrar sesión</button>
        </form>
    </aside>

    <div class="admin-principal">
        <header class="admin-encabezado">
            <button type="button" class="admin-menu-boton" aria-label="Abrir menú">&#9776;</button>
            <h1>@yield('titulo', 'Panel')</h1>
            <div class="admin-encabezado-acciones">@yield('acciones')</div>
        </header>

        @if (session('exito'))
            <div class="alerta alerta-exito" role="status">{{ session('exito') }}</div>
        @endif
        @if (session('error'))
            <div class="alerta alerta-error" role="alert">{{ session('error') }}</div>
        @endif

        <main class="admin-contenido">
            @yield('contenido')
        </main>
    </div>

    <script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
