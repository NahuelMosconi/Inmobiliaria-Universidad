{{-- Tarjeta de una propiedad. Se usa en el inicio, el listado y "propiedades similares". --}}
<article class="tarjeta">
    <a href="{{ $propiedad->url }}" class="tarjeta-imagen">
        <img src="{{ $propiedad->url_portada }}" alt="{{ $propiedad->titulo }}" loading="lazy">
        <span class="etiqueta etiqueta-{{ $propiedad->operacion }}">{{ $propiedad->operacion_nombre }}</span>
    </a>

    {{-- El corazón lo maneja favoritos.js usando el data-id --}}
    <button type="button" class="boton-favorito" data-favorito="{{ $propiedad->id }}" aria-label="Agregar a favoritos" title="Agregar a favoritos">&#9829;</button>

    <div class="tarjeta-info">
        <p class="tarjeta-tipo">{{ $propiedad->tipo_nombre }} &middot; {{ $propiedad->localidad }}</p>
        <h3><a href="{{ $propiedad->url }}">{{ $propiedad->titulo }}</a></h3>
        <p class="tarjeta-resumen">{{ $propiedad->resumen }}</p>

        <ul class="tarjeta-datos">
            @if ($propiedad->superficie_total)
                <li>{{ number_format($propiedad->superficie_total, 0, ',', '.') }} m²</li>
            @endif
            @if ($propiedad->dormitorios)
                <li>{{ $propiedad->dormitorios }} {{ $propiedad->dormitorios == 1 ? 'dormitorio' : 'dormitorios' }}</li>
            @endif
            @if ($propiedad->banos)
                <li>{{ $propiedad->banos }} {{ $propiedad->banos == 1 ? 'baño' : 'baños' }}</li>
            @endif
        </ul>

        <p class="tarjeta-precio">{{ $propiedad->precio_formateado }}</p>
    </div>
</article>
