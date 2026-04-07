@extends('layouts.crm')

@php
    $moduloContext = $moduloContext ?? [
        'clave' => 'aprobados',
        'titulo' => 'Aprobado',
        'titulo_gestion' => 'Gestion aprobados',
        'indexRoute' => 'aprobados.index',
        'showRoute' => 'aprobados.show',
        'procesoRoute' => 'aprobados.proceso',
    ];
    $registros = $registros ?? [];
@endphp

@section('title', ($moduloContext['titulo_gestion'] ?? 'Gestion aprobados') . ' | CRM')

@section('content')
    @include('partials.gestion_modulo_listado')
@endsection
