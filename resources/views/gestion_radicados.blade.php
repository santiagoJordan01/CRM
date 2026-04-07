@extends('layouts.crm')

@php
    $moduloContext = $moduloContext ?? [
        'clave' => 'radicados',
        'titulo' => 'Radicado',
        'titulo_gestion' => 'Gestion radicados',
        'indexRoute' => 'radicados.index',
        'showRoute' => 'radicados.show',
        'procesoRoute' => 'radicados.proceso',
    ];
    $registros = $registros ?? [];
@endphp

@section('title', ($moduloContext['titulo_gestion'] ?? 'Gestion radicados') . ' | CRM')

@section('content')
    @include('partials.gestion_modulo_listado')
@endsection