@extends('errors.layout')

@section('titulo', 'Demasiados intentos')
@section('codigo', '429')
@section('mensaje', 'Demasiados intentos')
@section('detalle', 'Hiciste muchos envíos seguidos. Esperá un minuto y probá de nuevo.')
