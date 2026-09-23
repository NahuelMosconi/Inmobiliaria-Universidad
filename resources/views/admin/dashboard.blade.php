@extends('admin.layout')

@section('titulo', 'Resumen')

@section('acciones')
    <a href="{{ route('admin.propiedades.create') }}" class="boton">+ Nueva propiedad</a>
@endsection

@section('contenido')
    <!-- Tarjetas con los números principales -->
    <div class="tarjetas-resumen">
        <a href="{{ route('admin.propiedades.index') }}" class="tarjeta-resumen">
            <span class="numero">{{ $resumen['propiedades'] }}</span>
            <span>Propiedades cargadas</span>
        </a>
        <a href="{{ route('admin.propiedades.index', ['estado' => 'publicadas']) }}" class="tarjeta-resumen">
            <span class="numero">{{ $resumen['publicadas'] }}</span>
            <span>Publicadas en el sitio</span>
            <small>{{ $porOperacion['venta'] ?? 0 }} en venta &middot; {{ $porOperacion['alquiler'] ?? 0 }} en alquiler</small>
        </a>
        <a href="{{ route('admin.consultas.index', ['estado' => 'sin_leer']) }}" @class(['tarjeta-resumen', 'resaltada' => $resumen['consultasSinLeer'] > 0])>
            <span class="numero">{{ $resumen['consultasSinLeer'] }}</span>
            <span>Consultas sin leer</span>
        </a>
        <div class="tarjeta-resumen">
            <span class="numero">{{ $resumen['consultasMes'] }}</span>
            <span>Consultas en los últimos 30 días</span>
        </div>
    </div>

    <div class="dos-columnas">
        <section class="panel">
            <h2>Últimas consultas</h2>
            @forelse ($ultimasConsultas as $consulta)
                <a href="{{ route('admin.consultas.show', $consulta) }}" @class(['fila-consulta', 'no-leida' => ! $consulta->leida])>
                    <strong>{{ $consulta->nombre }}</strong>
                    <span>{{ $consulta->propiedad->titulo ?? 'Consulta general' }}</span>
                    <small>{{ $consulta->created_at->diffForHumans() }}</small>
                </a>
            @empty
                <p class="texto-suave">Todavía no llegaron consultas.</p>
            @endforelse
            <a href="{{ route('admin.consultas.index') }}" class="link-mas">Ver todas &rarr;</a>
        </section>

        <section class="panel">
            <h2>Propiedades más visitadas</h2>
            @forelse ($masVisitadas as $propiedad)
                <a href="{{ route('admin.propiedades.edit', $propiedad) }}" class="fila-consulta">
                    <strong>{{ $propiedad->titulo }}</strong>
                    <span>{{ $propiedad->precio_formateado }}</span>
                    <small>{{ $propiedad->visitas }} {{ $propiedad->visitas == 1 ? 'visita' : 'visitas' }}</small>
                </a>
            @empty
                <p class="texto-suave">No hay propiedades publicadas.</p>
            @endforelse
        </section>
    </div>
@endsection
