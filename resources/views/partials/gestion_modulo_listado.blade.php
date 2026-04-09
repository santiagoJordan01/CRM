@php
    $registros = $registros ?? [];
    $moduloContext = $moduloContext ?? [
        'clave' => 'filtros',
        'titulo' => 'Filtro',
        'titulo_gestion' => 'Gestion filtros',
        'showRoute' => 'filtros.show',
        'procesoRoute' => 'filtros.proceso',
    ];

    $opcionesCampania = collect($registros)->pluck('campania')->filter()->unique()->sort()->values();
    $opcionesProducto = collect($registros)->pluck('producto')->filter()->unique()->sort()->values();
    $opcionesCanal = collect($registros)->pluck('canal')->filter()->unique()->sort()->values();

    $estadoOpciones = collect(array_merge(
        $moduloContext['opcionesEstado'] ?? [],
        $moduloContext['opcionesEstadoAsesor'] ?? []
    ))
        ->map(function (array $item): array {
            return [
                'status' => (string) ($item['status'] ?? ''),
                'sub_status' => (string) ($item['sub_status'] ?? ''),
            ];
        })
        ->filter(fn (array $item): bool => $item['status'] !== '' || $item['sub_status'] !== '')
        ->unique(fn (array $item): string => $item['status'] . '|' . $item['sub_status'])
        ->values();

    $opcionesStatus = $estadoOpciones->pluck('status')->filter()->unique()->sort()->values();
    $mapaSubStatusPorStatus = $estadoOpciones
        ->groupBy('status')
        ->map(fn ($items) => $items->pluck('sub_status')->filter()->unique()->sort()->values())
        ->toArray();
    $opcionesSubStatusTodas = $estadoOpciones->pluck('sub_status')->filter()->unique()->sort()->values();

    $statusSeleccionado = request('status');
    $subStatusSeleccionado = request('sub_status');
    $opcionesSubStatus = $statusSeleccionado
        ? collect($mapaSubStatusPorStatus[$statusSeleccionado] ?? [])->values()
        : $opcionesSubStatusTodas;

    $tieneFiltrosActivos = collect([
        request('campania'),
        request('producto'),
        request('canal'),
        request('fecha_desde'),
        request('fecha_hasta'),
        request('status'),
        request('sub_status'),
        request('q'),
    ])->contains(fn ($value) => filled($value));
@endphp

<section class="gf-view">
    <header class="gf-head">
        <h1>{{ $moduloContext['titulo'] ?? 'Filtro' }}</h1>
        <p><span>{{ $moduloContext['titulo'] ?? 'Filtro' }}</span> <strong>&rsaquo;</strong> Listado General</p>
    </header>

    @if(session('success'))
    <div class="gf-alert-success">{{ session('success') }}</div>
    @endif

    <div class="gf-panel">
        <div class="gf-panel-title">
            <span>{{ $moduloContext['titulo'] ?? 'Filtro' }}</span>
            <button type="button" aria-label="Cerrar">&times;</button>
        </div>

        <div class="gf-actions">
            <button type="button" class="gf-btn-main">Ver Busquedas</button>
        </div>

        <div
            class="gf-search-shell {{ $tieneFiltrosActivos ? 'is-open' : '' }}"
            id="gf-search-shell"
            data-sub-status-map='@json($mapaSubStatusPorStatus)'
            data-all-sub-status='@json($opcionesSubStatusTodas->values())'
        >
            <div class="gf-search-grid">
                <form method="GET" action="{{ route($moduloContext['indexRoute'] ?? 'filtros.index') }}" class="gf-search-box gf-search-advanced">
                    @if(request()->filled('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif

                    <h3>Busqueda Avanzada</h3>

                    <div class="gf-search-fields">
                        <div class="gf-field">
                            <label for="gf-campania">Banco</label>
                            <select id="gf-campania" name="campania">
                                <option value="">- Seleccione -</option>
                                @foreach($opcionesCampania as $campania)
                                    <option value="{{ $campania }}" {{ request('campania') === $campania ? 'selected' : '' }}>{{ $campania }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="gf-field">
                            <label for="gf-producto">Producto</label>
                            <select id="gf-producto" name="producto">
                                <option value="">- Seleccione -</option>
                                @foreach($opcionesProducto as $producto)
                                    <option value="{{ $producto }}" {{ request('producto') === $producto ? 'selected' : '' }}>{{ $producto }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="gf-field">
                            <label for="gf-canal">Canal</label>
                            <select id="gf-canal" name="canal">
                                <option value="">- Seleccione -</option>
                                @foreach($opcionesCanal as $canal)
                                    <option value="{{ $canal }}" {{ request('canal') === $canal ? 'selected' : '' }}>{{ $canal }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="gf-field gf-field-date-range">
                            <label>Fecha</label>
                            <div class="gf-date-range">
                                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" aria-label="Fecha desde">
                                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" aria-label="Fecha hasta">
                            </div>
                        </div>

                        <div class="gf-field">
                            <label for="gf-status-select">Status</label>
                            <select id="gf-status-select" name="status">
                                <option value="">- Seleccione -</option>
                                @foreach($opcionesStatus as $status)
                                    <option value="{{ $status }}" {{ $statusSeleccionado === $status ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="gf-field">
                            <label for="gf-sub-status-select">Sub status</label>
                            <select id="gf-sub-status-select" name="sub_status">
                                <option value="">- Seleccione -</option>
                                @foreach($opcionesSubStatus as $subStatus)
                                    <option value="{{ $subStatus }}" {{ $subStatusSeleccionado === $subStatus ? 'selected' : '' }}>{{ $subStatus }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="gf-search-actions">
                        <button type="submit" class="gf-btn-main gf-btn-main-small">Buscar</button>
                        <a href="{{ route($moduloContext['indexRoute'] ?? 'filtros.index') }}" class="gf-btn-clear">Limpiar</a>
                    </div>
                </form>

                <form method="GET" action="{{ route($moduloContext['indexRoute'] ?? 'filtros.index') }}" class="gf-search-box gf-search-general">
                    <input type="hidden" name="campania" value="{{ request('campania') }}">
                    <input type="hidden" name="producto" value="{{ request('producto') }}">
                    <input type="hidden" name="canal" value="{{ request('canal') }}">
                    <input type="hidden" name="fecha_desde" value="{{ request('fecha_desde') }}">
                    <input type="hidden" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <input type="hidden" name="sub_status" value="{{ request('sub_status') }}">

                    <h3>Busqueda General</h3>
                    <div class="gf-field">
                        <label for="gf-q" class="sr-only">Termino de busqueda</label>
                        <input id="gf-q" type="text" name="q" value="{{ request('q') }}" placeholder="Nombre, cedula, empresa, id...">
                    </div>

                    <div class="gf-search-actions">
                        <button type="submit" class="gf-btn-main gf-btn-main-small">Buscar</button>
                    </div>
                </form>
            </div>
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
                        <th>Detalle</th>
                        <th>Proceso</th>
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
                            @php
                                $statusClass = 'bad';
                                if (in_array($registro['status'], ['Viable', 'Preradicacion Comercial', 'Envio Digital Docs', 'Radicacion Iniciada', 'En Estudio', 'Radicado', 'Aprobado', 'Contabilizacion aceptada', 'Desembolsado'], true)) {
                                    $statusClass = 'ok';
                                } elseif (in_array($registro['status'], ['Inicia Filtro', 'Pendiente', 'Contabilizacion Pendiente'], true)) {
                                    $statusClass = 'pending';
                                }

                                $subStatusWarn = in_array($registro['sub_status'], ['Expo Titular Color Semaforo', 'No Gestionable'], true);
                            @endphp
                            <span class="gf-badge {{ $statusClass }}">
                                {{ $registro['status'] }}
                            </span>
                        </td>
                        <td>
                            <span class="gf-badge gf-sub {{ $subStatusWarn ? 'warn' : 'ok' }}">
                                {{ $registro['sub_status'] }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route($moduloContext['showRoute'] ?? 'filtros.show', $registro['id']) }}" class="gf-btn-mini" title="Ver detalle">
                                <i class="fas fa-user"></i>
                            </a>
                        </td>
                        <td>
                            <a href="{{ route($moduloContext['procesoRoute'] ?? 'filtros.proceso', $registro['id']) }}" class="gf-btn-mini gf-btn-mini-alt" title="Ver proceso">
                                <i class="fas fa-chart-line"></i>
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

    .gf-search-shell {
        margin-bottom: 1rem;
        display: none;
    }

    .gf-search-shell.is-open {
        display: block;
    }

    .gf-search-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 0.9rem;
    }

    .gf-search-box {
        border: 1px solid #d8e0ec;
        background: #f2f4f7;
        border-radius: 7px;
        padding: 0.8rem;
    }

    .gf-search-box h3 {
        margin: 0 0 0.6rem;
        font-size: 1.65rem;
        color: #2f435d;
        font-weight: 700;
    }

    .gf-search-fields {
        display: grid;
        gap: 0.55rem 0.7rem;
        grid-template-columns: repeat(4, minmax(130px, 1fr));
    }

    .gf-field {
        display: grid;
        gap: 0.25rem;
    }

    .gf-field label {
        font-size: 0.82rem;
        font-weight: 700;
        color: #4b617d;
    }

    .gf-field input,
    .gf-field select {
        height: 32px;
        border: 1px solid #d5dde9;
        border-radius: 3px;
        background: #fff;
        color: #324b68;
        padding: 0 0.5rem;
        font-size: 0.82rem;
    }

    .gf-field-date-range {
        grid-column: span 2;
    }

    .gf-date-range {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.45rem;
    }

    .gf-search-actions {
        margin-top: 0.7rem;
        display: flex;
        gap: 0.55rem;
        align-items: center;
    }

    .gf-btn-main.gf-btn-main-small {
        font-size: 0.96rem;
        padding: 0.4rem 0.9rem;
        border-radius: 5px;
        min-width: 90px;
    }

    .gf-btn-clear {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 32px;
        border: 1px solid #ced8e6;
        border-radius: 5px;
        background: #fff;
        color: #2f4868;
        text-decoration: none;
        font-size: 0.82rem;
        padding: 0 0.7rem;
        font-weight: 600;
    }

    .gf-search-general .gf-field {
        margin-top: 0.35rem;
    }

    .gf-search-general .gf-field input {
        width: 100%;
    }

    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
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
        min-width: 1460px;
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

    .gf-badge.pending {
        background: #f4a321;
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

    .gf-btn-mini.gf-btn-mini-alt {
        background: #3668b5;
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

        .gf-search-grid {
            grid-template-columns: 1fr;
        }

        .gf-search-fields {
            grid-template-columns: repeat(2, minmax(120px, 1fr));
        }

        .gf-field-date-range {
            grid-column: auto;
        }
    }
</style>

<script>
    (function () {
        const btnMain = document.querySelector('.gf-btn-main');
        const searchShell = document.getElementById('gf-search-shell');
        const statusSelect = document.getElementById('gf-status-select');
        const subStatusSelect = document.getElementById('gf-sub-status-select');
        const subStatusByStatus = JSON.parse(searchShell?.dataset.subStatusMap || '{}');
        const allSubStatus = JSON.parse(searchShell?.dataset.allSubStatus || '[]');

        function setMainLabel() {
            if (!btnMain || !searchShell) {
                return;
            }

            btnMain.textContent = searchShell.classList.contains('is-open')
                ? 'Ocultar Busquedas'
                : 'Ver Busquedas';
        }

        function renderSubStatusOptions() {
            if (!statusSelect || !subStatusSelect) {
                return;
            }

            const selectedStatus = statusSelect.value || '';
            const selectedSubStatus = subStatusSelect.value || '';
            const options = selectedStatus !== ''
                ? (subStatusByStatus[selectedStatus] || [])
                : allSubStatus;

            subStatusSelect.innerHTML = '<option value="">- Seleccione -</option>';

            options.forEach(function (value) {
                const option = document.createElement('option');
                option.value = value;
                option.textContent = value;
                if (selectedSubStatus === value) {
                    option.selected = true;
                }
                subStatusSelect.appendChild(option);
            });
        }

        if (btnMain && searchShell) {
            setMainLabel();
            btnMain.addEventListener('click', function () {
                searchShell.classList.toggle('is-open');
                setMainLabel();
            });
        }

        if (statusSelect && subStatusSelect) {
            statusSelect.addEventListener('change', function () {
                subStatusSelect.value = '';
                renderSubStatusOptions();
            });

            renderSubStatusOptions();
        }
    })();
</script>