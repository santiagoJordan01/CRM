@extends('layouts.crm')

@section('title', 'Leads Web | CRM')

@section('content')
    <section class="web-leads-view">
        <header class="view-header">
            <div>
                <h1>Clientes potenciales (Web)</h1>
                <p>Leads captados desde la pagina publica para convertir a Gestion Filtros.</p>
            </div>
        </header>

        @if(session('success'))
            <div class="flash success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="flash error">{{ $errors->first() }}</div>
        @endif

        <article class="panel">
            <h3>Pendientes por convertir</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Contacto</th>
                            <th>Banco / Producto</th>
                            <th>Ubicacion</th>
                            <th>Accion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leadsPendientes as $lead)
                            <tr>
                                <td>{{ $lead->created_at?->format('d/m/Y H:i') }}</td>
                                <td>
                                    <strong>{{ $lead->nombre_cliente }}</strong>
                                    <small>Cedula: {{ $lead->cedula ?: 'N/A' }}</small>
                                </td>
                                <td>
                                    <strong>{{ $lead->celular_cliente }}</strong>
                                    <small>{{ $lead->email }}</small>
                                </td>
                                <td>
                                    <strong>{{ $lead->campania }}</strong>
                                    <small>{{ $lead->producto }}{{ $lead->canal ? ' | ' . $lead->canal : '' }}</small>
                                </td>
                                <td>
                                    <strong>{{ $lead->departamento?->nombre ?: 'Sin departamento' }}</strong>
                                    <small>{{ $lead->municipio?->nombre ?: 'Sin municipio' }}</small>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('web-leads.convertir', $lead->id) }}">
                                        @csrf
                                        <button type="submit" class="btn-convertir">Pasar a Gestion Filtros</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty">No hay leads pendientes en este momento.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrap">
                {{ $leadsPendientes->links() }}
            </div>
        </article>

        <article class="panel">
            <h3>Ultimos convertidos</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Convertido por</th>
                            <th>Fecha</th>
                            <th>Filtro creado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leadsConvertidos as $lead)
                            <tr>
                                <td>{{ $lead->nombre_cliente }}</td>
                                <td>{{ strtoupper($lead->convertedBy?->name ?? 'N/A') }}</td>
                                <td>{{ $lead->converted_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                <td>
                                    @if($lead->converted_cliente_id)
                                        <a href="{{ route('filtros.show', $lead->converted_cliente_id) }}" class="link-filter">
                                            Ver filtro #{{ $lead->converted_cliente_id }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty">Aun no hay leads convertidos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>

    <style>
        .web-leads-view {
            display: grid;
            gap: 1rem;
        }

        .view-header h1 {
            margin: 0;
            color: #274261;
            font-size: 1.4rem;
        }

        .view-header p {
            margin: 0.25rem 0 0;
            color: #67809b;
        }

        .flash {
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 0.86rem;
            border: 1px solid transparent;
        }

        .flash.success {
            background: #eaf8ef;
            border-color: #bde3ca;
            color: #216f3e;
        }

        .flash.error {
            background: #fff1ef;
            border-color: #efc8c1;
            color: #a9352e;
        }

        .panel {
            background: #fff;
            border: 1px solid #dce6f1;
            border-radius: 12px;
            padding: 0.85rem;
        }

        .panel h3 {
            margin: 0 0 0.65rem;
            color: #2d4868;
            font-size: 1rem;
        }

        .table-wrap {
            overflow: auto;
        }

        table {
            width: 100%;
            min-width: 820px;
            border-collapse: collapse;
        }

        thead th {
            text-align: left;
            background: #eff6fd;
            color: #2c4968;
            font-size: 0.8rem;
            padding: 8px;
            border-bottom: 1px solid #dce8f4;
        }

        tbody td {
            border-bottom: 1px solid #ecf2f8;
            padding: 8px;
            color: #3d5a79;
            font-size: 0.8rem;
            vertical-align: top;
        }

        tbody td strong {
            display: block;
            color: #274566;
            font-size: 0.82rem;
        }

        tbody td small {
            display: block;
            margin-top: 2px;
            color: #6f87a2;
            font-size: 0.75rem;
        }

        .empty {
            text-align: center;
            color: #6f87a2;
            padding: 14px;
        }

        .btn-convertir {
            border: 1px solid #15a25c;
            background: #17b265;
            color: #fff;
            border-radius: 8px;
            min-height: 34px;
            padding: 0 10px;
            font-weight: 700;
            cursor: pointer;
            font-size: 0.78rem;
        }

        .btn-convertir:hover {
            filter: brightness(0.96);
        }

        .link-filter {
            color: #1f6fa9;
            font-weight: 700;
            text-decoration: none;
        }

        .pagination-wrap {
            margin-top: 0.65rem;
        }
    </style>
@endsection
