<x-mail::message>
# Nueva consulta

Llegó una consulta desde el sitio web.

@if ($consulta->propiedad)
**Propiedad:** {{ $consulta->propiedad->titulo }} ({{ $consulta->propiedad->precio_formateado }})
@endif

**Nombre:** {{ $consulta->nombre }}<br>
**Email:** {{ $consulta->email }}<br>
**Teléfono:** {{ $consulta->telefono ?? 'No informado' }}

<x-mail::panel>
{{ $consulta->mensaje }}
</x-mail::panel>

<x-mail::button :url="route('admin.consultas.show', $consulta)">
Ver en el panel
</x-mail::button>

{{ config('inmobiliaria.nombre') }}
</x-mail::message>
