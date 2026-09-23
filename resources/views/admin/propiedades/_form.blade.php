{{--
    Campos del formulario de propiedades. Lo comparten create.blade.php y edit.blade.php.
    old() devuelve lo que se escribió si la validación falló, y si no el valor guardado.
--}}
@php
    // Muestra los montos sin separador de miles para que al volver a guardar no se alteren.
    $monto = fn ($valor) => $valor === null ? '' : rtrim(rtrim(number_format((float) $valor, 2, ',', ''), '0'), ',');
@endphp

@if ($errors->any())
    <div class="alerta alerta-error">
        Hay {{ $errors->count() === 1 ? 'un campo' : $errors->count().' campos' }} con errores. Revisá los datos marcados en rojo.
    </div>
@endif

<fieldset class="panel">
    <legend>Datos principales</legend>

    <div class="campo">
        <label for="titulo">Título <span class="obligatorio">*</span></label>
        <input type="text" id="titulo" name="titulo" value="{{ old('titulo', $propiedad->titulo) }}" maxlength="150" required
               placeholder="Ej: Casa en Venta en Chacras de Coria">
        @error('titulo') <p class="error-campo">{{ $message }}</p> @enderror
    </div>

    <div class="fila-campos">
        <div class="campo">
            <label for="operacion">Operación <span class="obligatorio">*</span></label>
            <select id="operacion" name="operacion" required>
                @foreach (\App\Models\Propiedad::OPERACIONES as $valor => $nombre)
                    <option value="{{ $valor }}" @selected(old('operacion', $propiedad->operacion) === $valor)>{{ $nombre }}</option>
                @endforeach
            </select>
            @error('operacion') <p class="error-campo">{{ $message }}</p> @enderror
        </div>
        <div class="campo">
            <label for="tipo">Tipo <span class="obligatorio">*</span></label>
            <select id="tipo" name="tipo" required>
                @foreach (\App\Models\Propiedad::TIPOS as $valor => $nombre)
                    <option value="{{ $valor }}" @selected(old('tipo', $propiedad->tipo) === $valor)>{{ $nombre }}</option>
                @endforeach
            </select>
            @error('tipo') <p class="error-campo">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="campo">
        <label for="resumen">Resumen <span class="obligatorio">*</span></label>
        <input type="text" id="resumen" name="resumen" value="{{ old('resumen', $propiedad->resumen) }}" maxlength="300" required data-contador
               placeholder="Una o dos líneas que se muestran en el listado">
        @error('resumen') <p class="error-campo">{{ $message }}</p> @enderror
    </div>

    <div class="campo">
        <label for="descripcion">Descripción completa <span class="obligatorio">*</span></label>
        <textarea id="descripcion" name="descripcion" rows="8" maxlength="10000" required>{{ old('descripcion', $propiedad->descripcion) }}</textarea>
        @error('descripcion') <p class="error-campo">{{ $message }}</p> @enderror
    </div>
</fieldset>

<fieldset class="panel">
    <legend>Precio</legend>
    <div class="fila-campos">
        <div class="campo">
            <label for="precio">Precio</label>
            <input type="text" inputmode="decimal" id="precio" name="precio" value="{{ old('precio', $monto($propiedad->precio)) }}" placeholder="Vacío = &quot;Consultar&quot;">
            <small class="ayuda">Sin puntos. Si se deja vacío se muestra "Consultar".</small>
            @error('precio') <p class="error-campo">{{ $message }}</p> @enderror
        </div>
        <div class="campo">
            <label for="moneda">Moneda</label>
            <select id="moneda" name="moneda">
                @foreach (\App\Models\Propiedad::MONEDAS as $valor => $nombre)
                    <option value="{{ $valor }}" @selected(old('moneda', $propiedad->moneda) === $valor)>{{ $nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="campo">
            <label for="expensas">Expensas (ARS)</label>
            <input type="text" inputmode="decimal" id="expensas" name="expensas" value="{{ old('expensas', $monto($propiedad->expensas)) }}">
            @error('expensas') <p class="error-campo">{{ $message }}</p> @enderror
        </div>
    </div>
</fieldset>

<fieldset class="panel">
    <legend>Características</legend>
    <p class="ayuda">Dejá vacío lo que no corresponda (por ejemplo, un terreno no tiene dormitorios).</p>

    <div class="fila-campos">
        @foreach ([
            'superficie_total' => 'Sup. total (m²)',
            'superficie_cubierta' => 'Sup. cubierta (m²)',
            'ambientes' => 'Ambientes',
            'dormitorios' => 'Dormitorios',
            'banos' => 'Baños',
            'cocheras' => 'Cocheras',
            'antiguedad' => 'Antigüedad (años)',
        ] as $campo => $etiqueta)
            <div class="campo campo-chico">
                <label for="{{ $campo }}">{{ $etiqueta }}</label>
                <input type="number" id="{{ $campo }}" name="{{ $campo }}" min="0" value="{{ old($campo, $propiedad->{$campo}) }}">
                @error($campo) <p class="error-campo">{{ $message }}</p> @enderror
            </div>
        @endforeach
    </div>

    <div class="campo">
        <label for="extras">Extras</label>
        <textarea id="extras" name="extras" rows="5" placeholder="Uno por renglón o separados por coma. Ej: Piscina, Quincho, Riego por aspersión">{{ old('extras', implode("\n", $propiedad->extras ?? [])) }}</textarea>
        @error('extras') <p class="error-campo">{{ $message }}</p> @enderror
        @error('extras.*') <p class="error-campo">{{ $message }}</p> @enderror
    </div>
</fieldset>

<fieldset class="panel">
    <legend>Ubicación</legend>
    <div class="fila-campos">
        <div class="campo">
            <label for="localidad">Localidad <span class="obligatorio">*</span></label>
            <input type="text" id="localidad" name="localidad" value="{{ old('localidad', $propiedad->localidad) }}" list="lista-localidades" maxlength="100" required>
            {{-- Sugiere las localidades ya cargadas para que no queden escritas de formas distintas --}}
            <datalist id="lista-localidades">
                @foreach ($localidades as $localidad)
                    <option value="{{ $localidad }}">
                @endforeach
            </datalist>
            @error('localidad') <p class="error-campo">{{ $message }}</p> @enderror
        </div>
        <div class="campo">
            <label for="direccion">Dirección</label>
            <input type="text" id="direccion" name="direccion" value="{{ old('direccion', $propiedad->direccion) }}" maxlength="255">
            @error('direccion') <p class="error-campo">{{ $message }}</p> @enderror
        </div>
    </div>
    <div class="fila-campos">
        <div class="campo">
            <label for="latitud">Latitud</label>
            <input type="text" inputmode="decimal" id="latitud" name="latitud" value="{{ old('latitud', $propiedad->latitud) }}" placeholder="-32.8895">
            @error('latitud') <p class="error-campo">{{ $message }}</p> @enderror
        </div>
        <div class="campo">
            <label for="longitud">Longitud</label>
            <input type="text" inputmode="decimal" id="longitud" name="longitud" value="{{ old('longitud', $propiedad->longitud) }}" placeholder="-68.8458">
            @error('longitud') <p class="error-campo">{{ $message }}</p> @enderror
        </div>
    </div>
    <p class="ayuda">
        Para obtener las coordenadas: en Google Maps hacé clic derecho sobre el lugar y copiá los números
        (el primero es la latitud y el segundo la longitud). También podés pegarlos juntos en el campo latitud.
    </p>
</fieldset>

<fieldset class="panel">
    <legend>Fotos</legend>
    <div class="campo">
        <label for="imagenes">{{ $propiedad->exists ? 'Agregar fotos' : 'Fotos' }}</label>
        <input type="file" id="imagenes" name="imagenes[]" accept="image/jpeg,image/png,image/webp" multiple data-vista-previa="#vista-previa">
        <small class="ayuda">JPG, PNG o WEBP de hasta 4 MB cada una. Máximo {{ \App\Http\Requests\PropiedadRequest::MAX_IMAGENES }} fotos por propiedad. La primera es la portada.</small>
        @error('imagenes') <p class="error-campo">{{ $message }}</p> @enderror
        @foreach ($errors->get('imagenes.*') as $mensajes)
            <p class="error-campo">{{ $mensajes[0] }}</p>
        @endforeach
    </div>
    {{-- admin.js muestra acá las fotos elegidas antes de subirlas --}}
    <div id="vista-previa" class="grilla-fotos"></div>
</fieldset>

<fieldset class="panel">
    <legend>Publicación</legend>
    <label class="check">
        <input type="checkbox" name="publicada" value="1" @checked(old('publicada', $propiedad->publicada))>
        Publicada (se ve en el sitio)
    </label>
    <label class="check">
        <input type="checkbox" name="destacada" value="1" @checked(old('destacada', $propiedad->destacada))>
        Destacada (aparece en el inicio)
    </label>
</fieldset>
