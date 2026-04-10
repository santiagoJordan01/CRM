<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de filtros</title>
    <style>
        :root {
            --ink: #1e3349;
            --muted: #6f8196;
            --line: #dce7f3;
            --bg: #f3f7fc;
            --accent: #0f82bd;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            color: var(--ink);
            background: var(--bg);
        }

        .toolbar {
            width: min(1080px, calc(100% - 32px));
            margin: 16px auto 0;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .toolbar button {
            border: 1px solid #bdd3e8;
            background: #fff;
            color: #2b567f;
            border-radius: 8px;
            min-height: 36px;
            padding: 0 14px;
            font-weight: 700;
            cursor: pointer;
        }

        .sheet {
            width: min(1080px, calc(100% - 32px));
            margin: 12px auto 24px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 18px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            gap: 12px;
            margin-bottom: 14px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--line);
        }

        .header h1 {
            margin: 0;
            font-size: 1.2rem;
            color: #173959;
        }

        .header p {
            margin: 3px 0 0;
            color: var(--muted);
            font-size: 0.86rem;
        }

        .filters {
            display: grid;
            grid-template-columns: repeat(3, minmax(120px, 1fr));
            gap: 8px;
            margin-bottom: 14px;
        }

        .filter-chip {
            background: #f7fbff;
            border: 1px solid #d9e9f7;
            border-radius: 9px;
            padding: 8px 10px;
        }

        .filter-chip small {
            display: block;
            color: #7890a8;
            font-size: 0.72rem;
            margin-bottom: 3px;
        }

        .filter-chip strong {
            color: #294a6a;
            font-size: 0.84rem;
            word-break: break-word;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
        }

        thead th {
            text-align: left;
            color: #21415f;
            background: #eef6fd;
            border-bottom: 1px solid #d5e5f4;
            padding: 9px 8px;
            position: sticky;
            top: 0;
        }

        tbody td {
            border-bottom: 1px solid #e6eef7;
            padding: 8px;
            vertical-align: top;
            color: #3a5674;
        }

        tbody tr:nth-child(even) {
            background: #fbfdff;
        }

        .empty {
            text-align: center;
            padding: 18px;
            color: #748aa3;
        }

        @media print {
            @page {
                size: letter portrait;
                margin: 10mm;
            }

            body {
                background: #fff;
            }

            .toolbar {
                display: none;
            }

            .sheet {
                width: 100%;
                margin: 0;
                border: 0;
                border-radius: 0;
                padding: 0;
            }

            thead th {
                position: static;
            }
        }
    </style>
</head>

<body>
    <div class="toolbar">
        <button type="button" onclick="window.print()">Imprimir / Guardar PDF</button>
    </div>

    <main class="sheet">
        <header class="header">
            <div>
                <h1>Informe de gestion de filtros</h1>
                <p>Generado por: {{ strtoupper($usuario?->name ?? 'USUARIO CRM') }}</p>
            </div>
            <p>Fecha de generacion: {{ $generadoEn->format('d/m/Y H:i') }}</p>
        </header>

        <section class="filters">
            <div class="filter-chip">
                <small>Banco</small>
                <strong>{{ $filtros['campania'] !== '' ? $filtros['campania'] : 'Todos' }}</strong>
            </div>
            <div class="filter-chip">
                <small>Producto</small>
                <strong>{{ $filtros['producto'] !== '' ? $filtros['producto'] : 'Todos' }}</strong>
            </div>
            <div class="filter-chip">
                <small>Canal</small>
                <strong>{{ $filtros['canal'] !== '' ? $filtros['canal'] : 'Todos' }}</strong>
            </div>
            <div class="filter-chip">
                <small>Fecha exacta</small>
                <strong>{{ $filtros['fecha'] !== '' ? $filtros['fecha'] : 'No aplicada' }}</strong>
            </div>
            <div class="filter-chip">
                <small>Fecha desde</small>
                <strong>{{ $filtros['fecha_desde'] !== '' ? $filtros['fecha_desde'] : 'No aplicada' }}</strong>
            </div>
            <div class="filter-chip">
                <small>Fecha hasta</small>
                <strong>{{ $filtros['fecha_hasta'] !== '' ? $filtros['fecha_hasta'] : 'No aplicada' }}</strong>
            </div>
        </section>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Banco</th>
                    <th>Producto</th>
                    <th>Canal</th>
                    <th>Cliente</th>
                    <th>Cedula</th>
                    <th>Estado</th>
                    <th>Sub estado</th>
                    <th>Asesor</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registros as $cliente)
                    <tr>
                        <td>{{ $cliente->id }}</td>
                        <td>{{ $cliente->created_at?->format('d/m/Y H:i') }}</td>
                        <td>{{ $cliente->campania }}</td>
                        <td>{{ $cliente->producto }}</td>
                        <td>{{ $cliente->canal }}</td>
                        <td>{{ $cliente->nombre_cliente }}</td>
                        <td>{{ $cliente->cedula }}</td>
                        <td>{{ $cliente->status }}</td>
                        <td>{{ $cliente->sub_status }}</td>
                        <td>{{ strtoupper($cliente->user?->name ?? 'ASESOR FREELANCE') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="empty">No se encontraron registros con los filtros seleccionados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </main>
</body>

</html>
