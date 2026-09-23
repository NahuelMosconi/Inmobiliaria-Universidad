{{-- Paginación propia (reemplaza a la de Laravel que viene pensada para Tailwind) --}}
@if ($paginator->hasPages())
    <nav class="paginacion" aria-label="Paginación">
        {{-- Anterior --}}
        @if ($paginator->onFirstPage())
            <span class="paginacion-item deshabilitado">&laquo; Anterior</span>
        @else
            <a class="paginacion-item" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo; Anterior</a>
        @endif

        {{-- Números de página ($elements lo arma Laravel, incluye "..." cuando hay muchas) --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="paginacion-item deshabilitado">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $pagina => $url)
                    @if ($pagina == $paginator->currentPage())
                        <span class="paginacion-item actual" aria-current="page">{{ $pagina }}</span>
                    @else
                        <a class="paginacion-item" href="{{ $url }}">{{ $pagina }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Siguiente --}}
        @if ($paginator->hasMorePages())
            <a class="paginacion-item" href="{{ $paginator->nextPageUrl() }}" rel="next">Siguiente &raquo;</a>
        @else
            <span class="paginacion-item deshabilitado">Siguiente &raquo;</span>
        @endif
    </nav>
@endif
