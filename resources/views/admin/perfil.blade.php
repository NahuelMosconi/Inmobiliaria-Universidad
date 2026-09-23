@extends('admin.layout')

@section('titulo', 'Mi perfil')

@section('contenido')
    <form action="{{ route('admin.perfil.update') }}" method="POST" class="form-admin form-angosto">
        @csrf
        @method('PUT')

        <fieldset class="panel">
            <legend>Datos</legend>
            <div class="campo">
                <label for="name">Nombre</label>
                <input type="text" id="name" name="name" value="{{ old('name', $usuario->name) }}" required maxlength="100">
                @error('name') <p class="error-campo">{{ $message }}</p> @enderror
            </div>
            <div class="campo">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $usuario->email) }}" required maxlength="150">
                @error('email') <p class="error-campo">{{ $message }}</p> @enderror
            </div>
        </fieldset>

        <fieldset class="panel">
            <legend>Cambiar contraseña</legend>
            <p class="ayuda">Dejá estos campos vacíos si no querés cambiarla.</p>
            <div class="campo">
                <label for="password_actual">Contraseña actual</label>
                <input type="password" id="password_actual" name="password_actual" autocomplete="current-password">
                @error('password_actual') <p class="error-campo">{{ $message }}</p> @enderror
            </div>
            <div class="campo">
                <label for="password">Nueva contraseña</label>
                <input type="password" id="password" name="password" autocomplete="new-password" minlength="8">
                <small class="ayuda">Mínimo 8 caracteres, con letras y números.</small>
                @error('password') <p class="error-campo">{{ $message }}</p> @enderror
            </div>
            <div class="campo">
                <label for="password_confirmation">Repetir nueva contraseña</label>
                <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
            </div>
        </fieldset>

        <div class="acciones-form">
            <button type="submit" class="boton">Guardar</button>
        </div>
    </form>
@endsection
