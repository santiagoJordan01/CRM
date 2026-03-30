@extends('layouts.crm')

@section('title', 'Registros | CRM')

@section('content')
<section class="card">
    <h1>Registros</h1>
    <p class="muted" style="margin-bottom:.75rem;font-size:0.95rem;color:#6b7280;">Nuevo filtro</p>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-top:0.5rem;">


        <div>
            <label for="campania" style="display:block;font-weight:600;margin-bottom:.25rem;">Campaña (Banco)</label>
            <select id="campania" name="campania" style="width:100%;">
                <option></option>
                <option>Banco AV Villas</option>
                <option>Banco de BBVA</option>
                <option>Banco Caja social</option>
                <option>Banco de Occidente</option>
            </select>
        </div>


        <div>
            <label for="producto" style="display:block;font-weight:600;margin-bottom:.25rem;">Producto</label>
            <select id="producto" name="producto" style="width:100%;">
                <option></option>
                <option>Libranza</option>
                <option>Consumo</option>
                <option>Hipotecario</option>
                <option>Tarjeta de credito</option>
            </select>
        </div>


    </div>

    <!-- Include jQuery and Select2 from CDN for searchable selects -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#campania').select2({
                placeholder: 'Buscar o seleccionar banco...',
                allowClear: true,
                width: '100%'
            });
            $('#producto').select2({
                placeholder: 'Seleccionar producto...',
                allowClear: true,
                width: '100%'
            });
        });
    </script>

    <p style="margin-top:1rem;">
        <a href="{{ route('home') }}" class="btn btn-secondary">Volver</a>
    </p>
</section>
@endsection