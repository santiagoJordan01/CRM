@extends('layouts.crm')

@section('title', 'Gestion filtros | CRM')

@section('content')
    <section class="gf-view">
        <header class="gf-head">
            <h1>Filtro</h1>
            <p><span>Filtro</span> <strong>&rsaquo;</strong> Listado General</p>
        </header>

        @if(session('success'))
            <div class="gf-alert-success">{{ session('success') }}</div>
        @endif

        <div class="gf-panel">
            <div class="gf-panel-title">
                <span>Filtro</span>
                <button type="button" aria-label="Cerrar">&times;</button>
            </div>

            <div class="gf-actions">
                <button type="button" class="gf-btn-main">Ver Busquedas</button>
            </div>

            <div class="gf-summary">
                <p><strong>Total:</strong> {{ count($registros) }} Registros</p>
                <p><strong>Pagina:</strong> (1) - Resultados del 1 al {{ count($registros) }} de {{ count($registros) }}</p>
                <p><strong>Paginas:</strong> 1</p>
            </div>

            <div class="gf-pagination">
                <span class="active">1</span>
            </div>

            <div class="gf-table-wrap">
                <table class="gf-table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Fecha Creacion</th>
                            <th>Ultima Modificacion</th>
                            <th>Gestion</th>
                            <th>Campania</th>
                            <th>Producto</th>
                            <th>Canal</th>
                            <th>Cedula Cliente</th>
                            <th>Nombre Cliente</th>
                            <th>Perfil</th>
                            <th>Empresa</th>
                            <th>Monto</th>
                            <th>Plazo</th>
                            <th>Status</th>
                            <th>Sub Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($registros as $registro)
                            <tr>
                                <td>{{ $registro['id'] }}</td>
                                <td>{{ $registro['fecha'] }}</td>
                                <td>{{ $registro['modificacion'] }}</td>
                                <td class="gf-gestion">{{ $registro['gestion'] }}</td>
                                <td>{{ $registro['campania'] }}</td>
                                <td>{{ $registro['producto'] }}</td>
                                <td>{{ $registro['canal'] }}</td>
                                <td>{{ $registro['cedula'] }}</td>
                                <td>{{ $registro['nombre'] }}</td>
                                <td>{{ $registro['perfil'] }}</td>
                                <td>{{ $registro['empresa'] }}</td>
                                <td>{{ $registro['monto'] }}</td>
                                <td>{{ $registro['plazo'] }}</td>
                                <td>
                                    <span class="gf-badge {{ $registro['status'] === 'Viable' ? 'ok' : 'bad' }}">
                                        {{ $registro['status'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="gf-badge gf-sub {{ $registro['sub_status'] === 'Color Semaforo' ? 'warn' : 'ok' }}">
                                        {{ $registro['sub_status'] }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('filtros.show', $registro['id']) }}" class="gf-btn-mini" title="Ver detalle">
                                        J.
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <style>
        .gf-view {
            max-width: 100%;
        }

        .gf-head {
            margin-bottom: 0.95rem;
        }

        .gf-head h1 {
            margin: 0;
            font-size: 1.6rem;
            color: #2a3f5b;
            font-weight: 700;
        }

        .gf-head p {
            margin: 0.25rem 0 0;
            font-size: 0.9rem;
            color: #5e6d82;
        }

        .gf-head p span {
            font-weight: 600;
        }

        .gf-head p strong {
            margin: 0 0.25rem;
            color: #8fa3bc;
        }

        .gf-panel {
            background: #f7f9fc;
            border: 1px solid #d8e0ec;
            border-radius: 10px;
            padding: 0.95rem;
            box-shadow: 0 8px 18px rgba(20, 48, 90, 0.06);
        }

        .gf-alert-success {
            margin-bottom: 0.75rem;
            border: 1px solid #bde6c8;
            background: #eaf9ef;
            color: #1f6b36;
            border-radius: 8px;
            padding: 0.65rem 0.75rem;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .gf-panel-title {
            height: 50px;
            border: 1px solid #d2e4f8;
            background: #dfeeff;
            color: #214a73;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 0.8rem;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .gf-panel-title button {
            border: none;
            background: transparent;
            color: #45607d;
            font-size: 1.1rem;
            cursor: pointer;
        }

        .gf-actions {
            margin: 0.7rem 0 1rem;
        }

        .gf-btn-main {
            border: none;
            border-radius: 7px;
            background: linear-gradient(180deg, #2a9de0, #1779bd);
            color: #fff;
            font-size: 1.25rem;
            font-weight: 700;
            padding: 0.48rem 1rem;
            cursor: pointer;
            box-shadow: 0 6px 14px rgba(29, 123, 185, 0.25);
        }

        .gf-summary {
            border: 1px solid #d2e0f2;
            background: #e7f1ff;
            border-radius: 6px;
            padding: 0.85rem 0.75rem;
            margin-bottom: 0.8rem;
        }

        .gf-summary p {
            margin: 0.2rem 0;
            color: #37506e;
            font-size: 0.9rem;
        }

        .gf-pagination {
            margin-bottom: 0.55rem;
        }

        .gf-pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 700;
        }

        .gf-pagination .active {
            background: #5868cf;
            color: #fff;
        }

        .gf-table-wrap {
            overflow-x: auto;
            border: 1px solid #d8e0eb;
            border-radius: 8px;
            background: #fff;
        }

        .gf-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1380px;
        }

        .gf-table th,
        .gf-table td {
            border-bottom: 1px solid #e7edf5;
            border-right: 1px solid #eef3f9;
            text-align: left;
            vertical-align: middle;
            padding: 0.5rem 0.4rem;
            font-size: 0.77rem;
            color: #2f435d;
        }

        .gf-table th {
            background: #f8fbff;
            color: #556f8d;
            font-weight: 700;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .gf-table tr:hover td {
            background: #f6faff;
        }

        .gf-gestion {
            color: #c74e4e;
            font-weight: 700;
        }

        .gf-badge {
            display: inline-block;
            border-radius: 4px;
            padding: 0.12rem 0.35rem;
            font-weight: 700;
            font-size: 0.72rem;
            line-height: 1.3;
            white-space: nowrap;
            color: #fff;
        }

        .gf-badge.ok {
            background: #3aaa58;
        }

        .gf-badge.bad {
            background: #22262f;
        }

        .gf-badge.gf-sub {
            background: #6fbf47;
        }

        .gf-badge.gf-sub.warn {
            background: #d65b5b;
        }

        .gf-btn-mini {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border: 0;
            border-radius: 6px;
            background: #1f89c8;
            color: #fff;
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 700;
            cursor: pointer;
        }

        .gf-btn-mini:hover {
            filter: brightness(0.95);
        }

        @media (max-width: 980px) {
            .gf-panel {
                padding: 0.7rem;
            }

            .gf-btn-main {
                width: 100%;
                font-size: 1rem;
            }
        }
    </style>
@endsection
