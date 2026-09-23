@extends('layouts.app')

@section('titulo', 'Nosotros')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/nosotros.css') }}">
@endpush

@section('contenido')
    <!-- Hero Nosotros -->
    <section class="hero-nosotros"></section>

    <!-- Contenido Nosotros -->
    <main class="container">
        <div class="nosotros-contenido">
            <h2>Nosotros</h2>
            <p>Nuestro objetivo es trabajar en el camino de la excelencia, proyectándonos y creciendo con éxito junto a nuestros clientes, sobre la base de la confianza, la seriedad y la trayectoria, en la búsqueda de maximizar el valor de sus inversiones y la satisfacción de sus necesidades.</p>
            <p>Asesoramiento integral que permita solucionar todas sus necesidades relacionadas con el mercado inmobiliario. Convirtiéndonos en la primera opción cuando sea el momento de vender, comprar o alquilar una vivienda.</p>
        </div>

        <div class="valores">
            <div class="valor">
                <h3>Misión</h3>
                <p>Acompañar a cada cliente en la compra, venta o alquiler de su propiedad con información clara y un trato cercano.</p>
            </div>
            <div class="valor">
                <h3>Visión</h3>
                <p>Ser la inmobiliaria de referencia en Mendoza por la confianza que generamos en cada operación.</p>
            </div>
            <div class="valor">
                <h3>Valores</h3>
                <p>Honestidad, compromiso, transparencia y conocimiento del mercado local.</p>
            </div>
        </div>

        <div class="nosotros-contenido">
            <p>No dudes en contactarnos.</p>
            <a href="{{ route('contacto') }}" class="boton">Ir a contacto</a>
        </div>
    </main>
@endsection
