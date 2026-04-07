@extends('layouts.crm')

@php
    $moduloContext = $moduloContext ?? [
        'clave' => 'filtros',
        'titulo' => 'Filtro',
        'titulo_gestion' => 'Gestion filtros',
        'indexRoute' => 'filtros.index',
        'showRoute' => 'filtros.show',
        'procesoRoute' => 'filtros.proceso',
    ];
    $registros = $registros ?? [];
@endphp

@section('title', ($moduloContext['titulo_gestion'] ?? 'Gestion filtros') . ' | CRM')

@section('content')
    @include('partials.gestion_modulo_listado')
@endsection