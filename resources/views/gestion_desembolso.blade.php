@extends('layouts.crm')

@php
    $moduloContext = $moduloContext ?? [
        'clave' => 'desembolso',
        'titulo' => 'Desembolso',
        'titulo_gestion' => 'Gestion desembolso',
        'indexRoute' => 'desembolso.index',
        'showRoute' => 'desembolso.show',
        'procesoRoute' => 'desembolso.proceso',
    ];
    $registros = $registros ?? [];
@endphp

@section('title', ($moduloContext['titulo_gestion'] ?? 'Gestion desembolso') . ' | CRM')

@section('content')
    @include('partials.gestion_modulo_listado')
@endsection
