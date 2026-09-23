@extends('admin.layout')

@section('titulo', 'Nueva propiedad')

@section('contenido')
    {{-- enctype multipart es obligatorio para poder subir archivos --}}
    <form action="{{ route('admin.propiedades.store') }}" method="POST" enctype="multipart/form-data" class="form-admin">
        @csrf

        @include('admin.propiedades._form')

        <div class="acciones-form">
            <a href="{{ route('admin.propiedades.index') }}" class="boton boton-secundario">Cancelar</a>
            <button type="submit" class="boton">Guardar propiedad</button>
        </div>
    </form>
@endsection
