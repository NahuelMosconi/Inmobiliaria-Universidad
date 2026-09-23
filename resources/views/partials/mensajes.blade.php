{{-- Mensajes "flash" que dejan los controladores con ->with('exito', ...) o ->with('error', ...) --}}
@if (session('exito'))
    <div class="alerta alerta-exito" role="status">
        <div class="container">{{ session('exito') }}</div>
        <button type="button" class="alerta-cerrar" aria-label="Cerrar">&times;</button>
    </div>
@endif

@if (session('error'))
    <div class="alerta alerta-error" role="alert">
        <div class="container">{{ session('error') }}</div>
        <button type="button" class="alerta-cerrar" aria-label="Cerrar">&times;</button>
    </div>
@endif
