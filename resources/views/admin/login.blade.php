<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Ingresar - Administración</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="admin-login">
    <main class="login-caja">
        <img src="{{ asset('img/logo.png') }}" alt="Logo de {{ config('inmobiliaria.nombre') }}" class="login-logo">
        <h1>Panel de administración</h1>

        <form action="{{ route('admin.login.store') }}" method="POST">
            @csrf

            @error('email')
                <div class="alerta alerta-error">{{ $message }}</div>
            @enderror

            <div class="campo">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            </div>

            <div class="campo">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>

            <label class="check">
                <input type="checkbox" name="recordarme" value="1"> Mantener la sesión iniciada
            </label>

            <button type="submit" class="boton boton-ancho">Ingresar</button>
        </form>

        <a href="{{ route('inicio') }}" class="login-volver">&larr; Volver al sitio</a>
    </main>
</body>
</html>
