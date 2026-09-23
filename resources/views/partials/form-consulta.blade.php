{{--
    Formulario de consulta. Se usa en la página de Contacto y en el detalle de cada propiedad.
    Variables opcionales:
      $propiedad      -> si viene, la consulta queda asociada a esa propiedad
      $textoBoton     -> texto del botón de enviar
    El atributo data-ajax hace que sitio.js lo envíe con fetch() sin recargar la página.
    Si el navegador no tiene JavaScript se envía de la forma normal y también funciona.
--}}
<form class="formulario-contacto" action="{{ route('contacto.store') }}" method="POST" data-ajax novalidate>
    @csrf

    @isset($propiedad)
        <input type="hidden" name="propiedad_id" value="{{ $propiedad->id }}">
    @endisset

    {{-- Campo trampa para bots: está oculto, una persona nunca lo completa --}}
    <div class="campo-trampa" aria-hidden="true">
        <label for="sitio_web">No completar este campo</label>
        <input type="text" id="sitio_web" name="sitio_web" tabindex="-1" autocomplete="off">
    </div>

    <div class="form-grupo">
        <label for="nombre">Nombre <span class="obligatorio">*</span></label>
        <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" placeholder="Ingrese su nombre"
               required minlength="2" maxlength="100" autocomplete="name" @class(['invalido' => $errors->has('nombre')])>
        @error('nombre') <p class="error-campo">{{ $message }}</p> @enderror
    </div>

    <div class="form-grupo">
        <label for="telefono">Número de teléfono</label>
        <input type="tel" id="telefono" name="telefono" value="{{ old('telefono') }}" placeholder="Ej: 261 1234567"
               maxlength="30" pattern="[0-9\s\-\+\(\)]{6,30}" autocomplete="tel" @class(['invalido' => $errors->has('telefono')])>
        @error('telefono') <p class="error-campo">{{ $message }}</p> @enderror
    </div>

    <div class="form-grupo">
        <label for="email">Email <span class="obligatorio">*</span></label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Ingrese su mail"
               required maxlength="150" autocomplete="email" @class(['invalido' => $errors->has('email')])>
        @error('email') <p class="error-campo">{{ $message }}</p> @enderror
    </div>

    <div class="form-grupo">
        <label for="mensaje">Mensaje <span class="obligatorio">*</span></label>
        <textarea id="mensaje" name="mensaje" rows="6" placeholder="Ingrese su mensaje" required minlength="10" maxlength="2000"
                  data-contador @class(['invalido' => $errors->has('mensaje')])>{{ old('mensaje', isset($propiedad) ? "Hola, me interesa la propiedad \"{$propiedad->titulo}\". ¿Me podrían dar más información?" : '') }}</textarea>
        @error('mensaje') <p class="error-campo">{{ $message }}</p> @enderror
    </div>

    <button type="submit" class="btn-enviar">{{ $textoBoton ?? 'Enviar mensaje' }}</button>

    {{-- Acá sitio.js muestra el resultado del envío --}}
    <div class="form-resultado" role="status" aria-live="polite" hidden></div>
</form>
