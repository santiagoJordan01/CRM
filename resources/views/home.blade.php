@extends('layouts.crm')

@section('title', 'Inicio | CRM')

@section('content')
    @php
        $nombreUsuario = strtoupper(auth()->user()->name ?? 'ASESOR FREELANCE');
        $rolUsuario = auth()->user()->role ?? 'asesor';
        $puedeCrearFiltro = in_array($rolUsuario, ['asesor', 'supervisor', 'admin'], true);
        $puedeVerInformes = in_array($rolUsuario, ['supervisor', 'admin'], true);
        $nombreRol = $rolUsuario === 'supervisor' ? 'Mesa de Control' : 'Asesor';
        $notificaciones = $notificaciones ?? collect();
        $notificacionesNoLeidas = (int) ($notificacionesNoLeidas ?? 0);
        $notificacionesPendientes = $notificaciones->filter(fn ($n) => empty($n->read_at));
        $notificacionesVistas = $notificaciones->filter(fn ($n) => !empty($n->read_at));
        $recordatorios = $recordatorios ?? collect();
        $bancos = $bancos ?? collect();
        $productos = $productos ?? collect();
        $canales = $canales ?? collect();
        $calendarEvents = $calendarEvents ?? [];
        $informeFiltros = $informeFiltros ?? [
            'campania' => '',
            'producto' => '',
            'canal' => '',
            'fecha' => '',
            'fecha_desde' => '',
            'fecha_hasta' => '',
        ];
        $informeRegistros = $informeRegistros ?? collect();
        $mostrarInforme = (bool) ($mostrarInforme ?? false);
    @endphp

    <section class="home-view">
        <header class="home-header">
            <div>
                <h1>Inicio</h1>
                <p>Bienvenido CRM</p>
            </div>
            <div class="home-top-icons">
                <button type="button" id="btn-fullscreen" aria-label="Pantalla completa"><i class="fas fa-expand"></i></button>
                <button
                    type="button"
                    id="btn-notifications"
                    aria-label="Notificaciones"
                    class="{{ $notificacionesNoLeidas > 0 ? 'has-alert' : '' }}"
                >
                    <i class="fas fa-bell"></i>
                    @if($notificacionesNoLeidas > 0)
                        <span>{{ $notificacionesNoLeidas }}</span>
                    @endif
                </button>
                <button type="button" id="btn-profile" aria-label="Perfil"><i class="fas fa-user-circle"></i></button>

                <div class="top-popover" id="notifications-popover" hidden>
                    <h4>Notificaciones</h4>
                    @if($notificaciones->isEmpty())
                        <p class="empty-state">No hay notificaciones recientes.</p>
                    @else
                        <div class="notif-filters" role="tablist" aria-label="Filtro de notificaciones">
                            <button type="button" class="notif-filter-btn is-active" data-filter="all">Todas</button>
                            <button type="button" class="notif-filter-btn" data-filter="unread">Pendientes</button>
                            <button type="button" class="notif-filter-btn" data-filter="read">Vistas</button>
                        </div>

                        @if($notificacionesPendientes->isNotEmpty())
                            <p class="notif-section-title" data-section="unread">Pendientes</p>
                            @foreach($notificacionesPendientes as $item)
                                <a href="{{ route('notifications.open', $item->id) }}" class="notif-item notif-item-unread" data-state="unread">
                                    <strong>{{ $item->cliente_nombre }}</strong>
                                    <span>
                                        {{ $item->old_status ?? 'Sin status' }} / {{ $item->old_sub_status ?? 'Sin sub status' }}
                                        ->
                                        {{ $item->new_status ?? 'Sin status' }} / {{ $item->new_sub_status ?? 'Sin sub status' }}
                                    </span>
                                    <small>Cambio: {{ $item->created_at?->format('d/m/Y H:i') }}</small>
                                </a>
                            @endforeach
                        @endif

                        @if($notificacionesVistas->isNotEmpty())
                            <p class="notif-section-title" data-section="read">Consultadas</p>
                            @foreach($notificacionesVistas as $item)
                                <a href="{{ route('notifications.open', $item->id) }}" class="notif-item notif-item-read" data-state="read">
                                    <strong>{{ $item->cliente_nombre }}</strong>
                                    <span>
                                        {{ $item->old_status ?? 'Sin status' }} / {{ $item->old_sub_status ?? 'Sin sub status' }}
                                        ->
                                        {{ $item->new_status ?? 'Sin status' }} / {{ $item->new_sub_status ?? 'Sin sub status' }}
                                    </span>
                                    <small>
                                        Cambio: {{ $item->created_at?->format('d/m/Y H:i') }}
                                        @if($item->read_at)
                                            | Vista: {{ $item->read_at?->format('d/m/Y H:i') }}
                                        @endif
                                    </small>
                                </a>
                            @endforeach
                        @endif
                    @endif
                </div>

                <div class="top-popover" id="profile-popover" hidden>
                    <h4>Mi perfil</h4>
                    <div class="profile-body">
                        <p class="profile-email">{{ auth()->user()->email ?? 'sin-email' }}</p>

                        <div class="profile-actions">
                            @if($puedeCrearFiltro)
                                <a href="{{ route('registros') }}" class="mini-link">Crear nuevo filtro</a>
                            @endif
                            <a href="{{ route('ajustes.index') }}" class="mini-link">Ajustes</a>
                        </div>

                        <form method="POST" action="{{ route('logout') }}" class="profile-logout-form">
                            @csrf
                            <button type="submit" class="logout-mini">Cerrar sesión</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <div class="home-grid-top {{ $puedeCrearFiltro ? '' : 'home-grid-top-supervisor' }}">
            <article class="panel panel-profile">
                <div class="avatar-circle">
                    <i class="fas fa-user"></i>
                </div>
                <div class="profile-data">
                    <h2>{{ $nombreUsuario }}</h2>
                    <p><span class="badge-type">{{ $nombreRol }}</span> <span class="badge-status">Activo</span></p>
                    <small>Rol del usuario autenticado</small>
                </div>
            </article>

            @if($puedeCrearFiltro)
                <article class="panel panel-add">
                    <a href="{{ route('registros') }}" class="add-filter-link">
                        <span class="add-title">AGREGAR FILTRO</span>
                        <i class="fas fa-user-plus"></i>
                    </a>
                </article>
            @endif

            <article class="panel panel-reminder">
                <h3>Recordatorio / Tarea / General</h3>
                <div class="reminder-body">
                    @forelse($recordatorios as $recordatorio)
                        <a href="{{ route('filtros.show', $recordatorio->id) }}" class="reminder-item">
                            <strong>{{ $recordatorio->nombre_cliente }}</strong>
                            <span>{{ $recordatorio->recordatorio }}</span>
                            <small>Actualizado: {{ $recordatorio->updated_at?->format('d/m/Y H:i') }}</small>
                        </a>
                    @empty
                        <p class="empty-state">No hay recordatorios registrados.</p>
                    @endforelse
                </div>
            </article>
        </div>

        <div class="home-grid-middle">
            @if($puedeVerInformes)
                <article class="panel panel-report">
                    <h3>Crear informe</h3>
                    <form class="report-form" action="{{ route('home') }}" method="GET">
                        <input type="hidden" name="buscar_informe" value="1">
                    <div class="field">
                        <label for="banco">Banco</label>
                        <select id="banco" name="campania">
                            <option value="">- Seleccione -</option>
                            @foreach($bancos as $banco)
                                <option value="{{ $banco }}" {{ ($informeFiltros['campania'] ?? '') === $banco ? 'selected' : '' }}>{{ $banco }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="producto">Producto</label>
                        <select id="producto" name="producto">
                            <option value="">- Seleccione -</option>
                            @foreach($productos as $producto)
                                <option value="{{ $producto }}" {{ ($informeFiltros['producto'] ?? '') === $producto ? 'selected' : '' }}>{{ $producto }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="canal">Canal</label>
                        <select id="canal" name="canal">
                            <option value="">- Seleccione -</option>
                            @foreach($canales as $canal)
                                <option value="{{ $canal }}" {{ ($informeFiltros['canal'] ?? '') === $canal ? 'selected' : '' }}>{{ $canal }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="fecha">Fecha</label>
                        <input id="fecha" name="fecha" type="date" value="{{ $informeFiltros['fecha'] ?? '' }}" />
                    </div>

                    <div class="field">
                        <label for="fecha_desde">Fecha desde</label>
                        <input id="fecha_desde" name="fecha_desde" type="date" value="{{ $informeFiltros['fecha_desde'] ?? '' }}" />
                    </div>

                    <div class="field">
                        <label for="fecha_hasta">Fecha hasta</label>
                        <input id="fecha_hasta" name="fecha_hasta" type="date" value="{{ $informeFiltros['fecha_hasta'] ?? '' }}" />
                    </div>

                    <div class="field actions report-actions">
                        <button type="submit" class="btn-report btn-search">Buscar</button>
                        <button
                            type="submit"
                            class="btn-report btn-excel"
                            formaction="{{ route('informes.filtros.excel') }}"
                            formmethod="GET"
                            formtarget="_blank"
                        >
                            Exportar Excel
                        </button>
                        <button
                            type="submit"
                            class="btn-report btn-pdf"
                            formaction="{{ route('informes.filtros.pdf') }}"
                            formmethod="GET"
                            formtarget="_blank"
                        >
                            Vista PDF
                        </button>
                    </div>
                    </form>

                    @if($mostrarInforme)
                        <div class="report-results">
                            <div class="report-results-header">
                                <strong>Resultados encontrados: {{ $informeRegistros->count() }}</strong>
                                <small>Vista previa en Inicio (maximo 30 registros).</small>
                            </div>

                            <div class="report-results-table-wrap">
                                <table class="report-results-table">
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($informeRegistros as $registro)
                                            <tr>
                                                <td>{{ $registro->id }}</td>
                                                <td>{{ $registro->created_at?->format('d/m/Y H:i') }}</td>
                                                <td>{{ $registro->campania }}</td>
                                                <td>{{ $registro->producto }}</td>
                                                <td>{{ $registro->canal }}</td>
                                                <td>{{ $registro->nombre_cliente }}</td>
                                                <td>{{ $registro->cedula }}</td>
                                                <td>{{ $registro->status }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="report-results-empty">No se encontraron registros con estos filtros.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </article>
            @else
                <article class="panel panel-report">
                    <h3>Crear informe</h3>
                    <p class="empty-state">Acceso restringido: solo supervisores o administradores pueden generar informes.</p>
                </article>
            @endif
        </div>

        <!-- Calendario eliminado por petición del usuario -->
    </section>

    <style>
        .home-view {
            max-width: 100%;
        }

        .home-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 0.95rem;
        }

        .home-header h1 {
            margin: 0;
            font-size: 1.35rem;
            color: #2d3e58;
            font-weight: 700;
        }

        .home-header p {
            margin: 0.2rem 0 0;
            color: #6c7f97;
            font-size: 0.95rem;
        }

        .home-top-icons {
            display: flex;
            gap: 0.35rem;
            position: relative;
        }

        .home-top-icons button {
            height: 44px;
            min-width: 44px;
            border-radius: 10px;
            border: 1px solid #e6eef6;
            background: #fff;
            color: #516f86;
            position: relative;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            transition: transform .12s ease, box-shadow .12s ease;
            box-shadow: 0 4px 10px rgba(18, 41, 66, 0.04);
            padding: 0;
        }

        .home-top-icons button i {
            font-size: 18px;
            line-height: 1;
        }

        .home-top-icons button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(18, 41, 66, 0.08);
        }

        .home-top-icons .has-alert span {
            position: absolute;
            top: -8px;
            right: -8px;
            min-width: 18px;
            height: 18px;
            padding: 0 4px;
            border-radius: 999px;
            background: #ea4658;
            color: #fff;
            font-size: 0.72rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .home-grid-top,
        .home-grid-middle {
            display: grid;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .home-grid-top {
            grid-template-columns: minmax(260px, 1.3fr) minmax(260px, 1.3fr) minmax(260px, 1fr);
            align-items: start;
        }

        .home-grid-top-supervisor {
            grid-template-columns: repeat(2, minmax(260px, 1fr));
        }

        .home-grid-middle {
            grid-template-columns: 1fr;
        }

        .panel {
            background: #f8fafd;
            border: 1px solid #d9e2ef;
            border-radius: 8px;
            padding: 0.85rem;
            box-shadow: 0 8px 18px rgba(30, 47, 74, 0.04);
            height: fit-content;
            transition: box-shadow 160ms ease, border-color 160ms ease;
        }

        .panel:hover {
            border-color: #c9d8ea;
            box-shadow: 0 12px 24px rgba(28, 44, 69, 0.08);
        }

        .panel h3 {
            margin: 0 0 0.65rem;
            color: #2f425f;
            font-size: 1.08rem;
            font-weight: 700;
        }

        .panel-profile {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            min-height: 132px;
            background: linear-gradient(180deg, #f9fcff, #f4f8fd);
        }

        .avatar-circle {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            border: 1px solid #d9e4f2;
            background: #f0f4f9;
            color: #bec8d7;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            flex-shrink: 0;
        }

        .profile-data {
            display: grid;
            gap: 0.2rem;
        }

        .profile-data h2 {
            margin: 0;
            font-size: 1.05rem;
            color: #344b68;
            font-weight: 700;
        }

        .profile-data p {
            margin: 0.35rem 0;
        }

        .badge-type,
        .badge-status {
            display: inline-block;
            font-size: 0.74rem;
            font-weight: 700;
            padding: 0.12rem 0.38rem;
            border-radius: 4px;
        }

        .badge-type {
            background: #11a7c8;
            color: #fff;
        }

        .badge-status {
            background: #2f9d4b;
            color: #fff;
        }

        .profile-data small {
            color: #6d8199;
            font-size: 0.8rem;
        }

        .panel-add {
            padding: 0;
            overflow: hidden;
            min-height: 132px;
        }

        .add-filter-link {
            min-height: 132px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 0.4rem;
            padding: 0.9rem 1rem;
            background: linear-gradient(135deg, #0f83bd, #1aa5db);
            color: #fff;
            text-decoration: none;
            text-align: center;
            box-sizing: border-box;
        }

        .add-filter-link .add-title {
            font-size: 1.9rem;
            line-height: 1;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .add-filter-link i {
            font-size: 2.2rem;
            opacity: 0.95;
        }

        .add-filter-link:hover {
            filter: brightness(1.02);
        }

        .panel-reminder {
            display: flex;
            flex-direction: column;
            max-height: 340px;
        }

        .panel-reminder h3 {
            display: inline-flex;
            align-items: center;
            min-height: 32px;
            padding: 0 0.7rem;
            border-radius: 6px;
            background: #138dcc;
            color: #fff;
            font-size: 1rem;
            margin-bottom: 0.7rem;
        }

        .reminder-body {
            min-height: 64px;
            border-top: 1px solid #e2eaf4;
            display: grid;
            gap: 0.45rem;
            padding-top: 0.55rem;
            max-height: 276px;
            overflow-y: auto;
            padding-right: 0.35rem;
            align-content: start;
        }

        .reminder-body::-webkit-scrollbar {
            width: 8px;
        }

        .reminder-body::-webkit-scrollbar-thumb {
            background: #cbd8e8;
            border-radius: 999px;
        }

        .reminder-body::-webkit-scrollbar-track {
            background: #eef3fa;
            border-radius: 999px;
        }

        .reminder-item {
            text-decoration: none;
            background: #ffffff;
            border: 1px solid #dfe8f4;
            border-radius: 6px;
            padding: 0.5rem;
            display: grid;
            gap: 0.12rem;
        }

        .reminder-item strong {
            color: #3a5473;
            font-size: 0.85rem;
        }

        .reminder-item span {
            color: #5f7591;
            font-size: 0.8rem;
        }

        .reminder-item small {
            color: #7f90a5;
            font-size: 0.74rem;
        }

        .empty-state {
            margin: 0;
            font-size: 0.82rem;
            color: #7f91a7;
        }

        .panel-report h3 {
            margin-bottom: 0.8rem;
        }

        .report-form {
            display: grid;
            gap: 0.7rem 1rem;
            grid-template-columns: repeat(4, minmax(140px, 1fr));
        }

        .field label {
            display: block;
            color: #4f6480;
            font-size: 0.84rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .field select,
        .field input {
            width: 100%;
            height: 34px;
            border: 1px solid #d3ddea;
            border-radius: 3px;
            background: #fff;
            padding: 0 0.55rem;
            color: #314866;
        }

        .field.actions {
            grid-column: 1 / -1;
            padding-top: 0.5rem;
            border-top: 1px solid #e2e9f2;
        }

        .field.actions button {
            min-height: 34px;
            border: none;
            border-radius: 5px;
            background: #1690ce;
            color: #fff;
            padding: 0 0.9rem;
            font-weight: 700;
            cursor: pointer;
        }

        .report-actions {
            display: flex;
            gap: 0.55rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .report-actions .btn-report {
            min-height: 36px;
            border-radius: 8px;
            border: 1px solid transparent;
            padding: 0 0.9rem;
            font-size: 0.82rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform .12s ease, box-shadow .12s ease, filter .12s ease;
        }

        .report-actions .btn-report:hover {
            transform: translateY(-1px);
            filter: brightness(1.02);
        }

        .report-actions .btn-search {
            background: #1690ce;
            color: #fff;
            border-color: #1690ce;
        }

        .report-actions .btn-excel {
            background: #eaf8ef;
            color: #1f7b3d;
            border-color: #b8e2c7;
        }

        .report-actions .btn-pdf {
            background: #fff3f1;
            color: #a3362a;
            border-color: #f2c7c1;
        }

        .report-results {
            margin-top: 0.8rem;
            border-top: 1px solid #e2e9f2;
            padding-top: 0.75rem;
        }

        .report-results-header {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 0.75rem;
            margin-bottom: 0.45rem;
            color: #3b5573;
        }

        .report-results-header strong {
            font-size: 0.9rem;
        }

        .report-results-header small {
            color: #6f849e;
            font-size: 0.76rem;
        }

        .report-results-table-wrap {
            border: 1px solid #d9e4f1;
            border-radius: 8px;
            overflow: auto;
            max-height: 340px;
            background: #fff;
        }

        .report-results-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 760px;
        }

        .report-results-table th {
            text-align: left;
            background: #eff6fd;
            color: #264462;
            font-size: 0.78rem;
            padding: 0.5rem;
            border-bottom: 1px solid #dbe7f3;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .report-results-table td {
            font-size: 0.77rem;
            color: #3f5a79;
            padding: 0.45rem 0.5rem;
            border-bottom: 1px solid #edf2f8;
            vertical-align: top;
        }

        .report-results-table tbody tr:nth-child(even) {
            background: #fbfdff;
        }

        .report-results-empty {
            text-align: center;
            color: #7086a1;
            padding: 0.9rem;
        }

        /* Calendario eliminado: reglas relacionadas removidas */

        .top-popover {
            position: absolute;
            top: 42px;
            right: 0;
            width: 320px;
            max-width: calc(100vw - 32px);
            max-height: 360px;
            overflow: auto;
            border-radius: 12px;
            background: linear-gradient(180deg, #ffffff, #f7fbff);
            border: 1px solid rgba(29, 43, 67, 0.06);
            box-shadow: 0 12px 28px rgba(20, 40, 80, 0.12);
            z-index: 1000;
            padding: 0;
            transform-origin: top right;
            transition: transform .14s ease, opacity .14s ease;
            -webkit-backdrop-filter: blur(4px);
            backdrop-filter: blur(4px);
        }

        .top-popover[hidden] {
            opacity: 0;
            transform: scale(.98);
            pointer-events: none;
        }

        .top-popover:not([hidden]) {
            opacity: 1;
            transform: scale(1);
            pointer-events: auto;
        }

        .top-popover h4 {
            margin: 0;
            padding: 0.72rem 0.9rem;
            color: #203248;
            font-size: 0.95rem;
            font-weight: 700;
            border-bottom: 1px solid #eef6fb;
            background: linear-gradient(180deg, #ffffff, #f7fbff);
            position: sticky;
            top: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 2;
        }

        .top-popover .empty-state {
            padding: 0.9rem 0.6rem;
            text-align: center;
            color: #6b8198;
            font-size: 0.9rem;
        }

        .top-popover::-webkit-scrollbar {
            width: 8px;
        }

        .top-popover::-webkit-scrollbar-thumb {
            background: #cbd8e8;
            border-radius: 999px;
        }

        .top-popover::-webkit-scrollbar-track {
            background: #eef6fb;
            border-radius: 999px;
        }

        /* Profile popover internals */
        .top-popover .profile-body {
            padding: 0.7rem 0.9rem 0.9rem;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }

        .top-popover .profile-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .top-popover .profile-email {
            margin: 0;
            color: #5f7890;
            font-size: 0.92rem;
            padding-bottom: 0.2rem;
            border-bottom: 1px solid #eef6fb;
        }

        .top-popover .mini-link {
            margin-bottom: 0;
            padding: 0.45rem 0.7rem;
        }

        .top-popover .profile-logout-form {
            margin-top: 0.25rem;
        }

        .notif-section-title {
            margin: 0.45rem 0 0.35rem;
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.45px;
            color: #6f86a3;
            font-weight: 700;
        }

        .notif-filters {
            display: flex;
            gap: 0.35rem;
            margin-bottom: 0.55rem;
            padding: 0.25rem;
            border: 1px solid #dce8f5;
            border-radius: 12px;
            background: linear-gradient(180deg, #f8fbff, #f2f7fd);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.9);
        }

        .notif-filter-btn {
            flex: 1 1 0;
            border: 1px solid transparent;
            background: transparent;
            color: #4e6885;
            border-radius: 9px;
            min-height: 38px;
            padding: 0.35rem 0.6rem;
            font-size: 0.78rem;
            font-weight: 700;
            line-height: 1;
            cursor: pointer;
            transition: background-color .15s ease, color .15s ease, box-shadow .15s ease, transform .12s ease;
        }

        .notif-filter-btn:hover {
            color: #214f78;
            background: rgba(19, 141, 204, 0.08);
        }

        .notif-filter-btn.is-active {
            background: #138dcc;
            border-color: #138dcc;
            color: #fff;
            box-shadow: 0 6px 14px rgba(19, 141, 204, 0.28);
        }

        .notif-item {
            display: grid;
            gap: 0.1rem;
            border: 1px solid #e2eaf5;
            border-radius: 6px;
            padding: 0.45rem;
            text-decoration: none;
            margin-bottom: 0.35rem;
            background: #f8fbff;
        }

        .notif-item-unread {
            border-color: #b8d8f2;
            background: #f2f9ff;
        }

        .notif-item-read {
            background: #fbfcfe;
            border-color: #e7edf5;
            opacity: 0.92;
        }

        .notif-item strong {
            color: #2f4968;
            font-size: 0.82rem;
        }

        .notif-item span {
            color: #5d7592;
            font-size: 0.76rem;
            line-height: 1.3;
        }

        .notif-item small {
            color: #7287a2;
            font-size: 0.75rem;
        }

        .profile-email {
            margin: 0 0 0.45rem;
            color: #6a7e98;
            font-size: 0.82rem;
        }

        .mini-link {
            display: inline-flex;
            align-items: center;
            min-height: 30px;
            border-radius: 5px;
            text-decoration: none;
            background: #eef6ff;
            color: #2f5a83;
            padding: 0 0.55rem;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 0.45rem;
        }

        .logout-mini {
            width: 100%;
            min-height: 32px;
            border: 0;
            border-radius: 6px;
            background: #d94a4a;
            color: #fff;
            cursor: pointer;
            font-weight: 700;
            font-size: 0.8rem;
        }

        @media (max-width: 1200px) {
            .home-grid-top {
                grid-template-columns: 1fr;
            }

            .panel-reminder {
                max-height: none;
            }

            .reminder-body {
                max-height: 240px;
            }

            .home-grid-middle {
                grid-template-columns: 1fr;
            }

            .report-form {
                grid-template-columns: repeat(2, minmax(140px, 1fr));
            }

            .field.actions.report-actions {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 720px) {
            .home-top-icons {
                display: none;
            }

            .notif-filters {
                gap: 0.25rem;
                padding: 0.2rem;
            }

            .notif-filter-btn {
                min-height: 34px;
                font-size: 0.74rem;
                padding: 0.3rem 0.45rem;
            }

            .report-form {
                grid-template-columns: 1fr;
            }

            .report-actions .btn-report {
                width: 100%;
            }

            .add-filter-link .add-title {
                font-size: 1.5rem;
            }
        }
    </style>

    <!-- calendar data removed -->
    <script>
        (function() {
            const btnFullscreen = document.getElementById('btn-fullscreen');
            const btnNotifications = document.getElementById('btn-notifications');
            const btnProfile = document.getElementById('btn-profile');
            const popNotifications = document.getElementById('notifications-popover');
            const popProfile = document.getElementById('profile-popover');

            function togglePopover(target) {
                const isHidden = target.hasAttribute('hidden');
                popNotifications.setAttribute('hidden', 'hidden');
                popProfile.setAttribute('hidden', 'hidden');
                if (isHidden) {
                    target.removeAttribute('hidden');
                }
            }

            btnFullscreen?.addEventListener('click', function() {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen();
                    return;
                }
                document.exitFullscreen();
            });

            btnNotifications?.addEventListener('click', function(e) {
                e.stopPropagation();
                togglePopover(popNotifications);
            });

            btnProfile?.addEventListener('click', function(e) {
                e.stopPropagation();
                togglePopover(popProfile);
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('.home-top-icons')) {
                    popNotifications?.setAttribute('hidden', 'hidden');
                    popProfile?.setAttribute('hidden', 'hidden');
                }
            });

            const notificationFilterButtons = popNotifications?.querySelectorAll('.notif-filter-btn') || [];

            function applyNotificationFilter(filterValue) {
                const notifItems = popNotifications?.querySelectorAll('.notif-item[data-state]') || [];
                const sectionTitles = popNotifications?.querySelectorAll('.notif-section-title[data-section]') || [];

                notifItems.forEach(function(item) {
                    const state = item.getAttribute('data-state');
                    const show = filterValue === 'all' || state === filterValue;
                    item.style.display = show ? '' : 'none';
                });

                sectionTitles.forEach(function(title) {
                    const section = title.getAttribute('data-section');
                    const hasVisibleItems = Array.from(notifItems).some(function(item) {
                        return item.getAttribute('data-state') === section && item.style.display !== 'none';
                    });
                    title.style.display = hasVisibleItems ? '' : 'none';
                });
            }

            notificationFilterButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    notificationFilterButtons.forEach(function(btn) {
                        btn.classList.remove('is-active');
                    });
                    button.classList.add('is-active');
                    applyNotificationFilter(button.getAttribute('data-filter') || 'all');
                });
            });

            applyNotificationFilter('all');
        })();
    </script>
@endsection
