@extends('layouts.crm')

@section('title', 'Inicio | CRM')

@section('content')
    <section class="card">
        <h1>Inicio</h1>
        <p>Bienvenido al CRM. Usa la barra lateral para navegar entre los modulos.</p>
        <div style="margin-top:1.5rem;">
            <a href="{{ route('registros') }}" class="btn btn-primary" style="font-size:1.25rem;padding:1rem 1.5rem;border-radius:8px;display:inline-block;">Agregar filtro</a>
        </div>
    </section>
@endsection
