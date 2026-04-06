@extends('layouts.crm')

@section('title', 'Inicio | CRM')

@section('content')
    @php
        $nombreUsuario = strtoupper(auth()->user()->name ?? 'ASESOR FREELANCE');
        $rolUsuario = auth()->user()->role ?? 'asesor';
        $puedeCrearFiltro = in_array($rolUsuario, ['asesor', 'supervisor'], true);
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
                            <button type="button" class="notif-filter-btn" data-filter="read">Consultadas</button>
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
                    <p class="profile-email">{{ auth()->user()->email ?? 'sin-email' }}</p>
                    @if($puedeCrearFiltro)
                        <a href="{{ route('registros') }}" class="mini-link">Crear nuevo filtro</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-mini">Cerrar sesion</button>
                    </form>
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
            <article class="panel panel-report">
                <h3>Crear informe</h3>
                <form class="report-form" action="{{ route('filtros.index') }}" method="GET">
                    <div class="field">
                        <label for="banco">Banco</label>
                        <select id="banco" name="campania">
                            <option>- Seleccione -</option>
                            @foreach($bancos as $banco)
                                <option value="{{ $banco }}">{{ $banco }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="producto">Producto</label>
                        <select id="producto" name="producto">
                            <option>- Seleccione -</option>
                            @foreach($productos as $producto)
                                <option value="{{ $producto }}">{{ $producto }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="canal">Canal</label>
                        <select id="canal" name="canal">
                            <option>- Seleccione -</option>
                            @foreach($canales as $canal)
                                <option value="{{ $canal }}">{{ $canal }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="fecha">Fecha</label>
                        <input id="fecha" name="fecha" type="date" />
                    </div>
                    <div class="field actions">
                        <button type="submit">Buscar</button>
                    </div>
                </form>
            </article>
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
            width: 34px;
            height: 34px;
            border-radius: 6px;
            border: 1px solid #dde5f1;
            background: #fff;
            color: #72839a;
            position: relative;
            cursor: pointer;
        }

        .home-top-icons .has-alert span {
            position: absolute;
            top: -6px;
            right: -4px;
            min-width: 16px;
            height: 16px;
            padding: 0 3px;
            border-radius: 999px;
            background: #ea4658;
            color: #fff;
            font-size: 0.66rem;
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

        /* Calendario eliminado: reglas relacionadas removidas */

        .top-popover {
            position: absolute;
            top: 42px;
            right: 0;
            width: 280px;
            max-height: 320px;
            overflow: auto;
            border: 1px solid #dce6f2;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 16px 24px rgba(29, 43, 67, 0.18);
            z-index: 20;
            padding: 0.6rem;
        }

        .top-popover h4 {
            margin: 0 0 0.45rem;
            color: #3a5270;
            font-size: 0.92rem;
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
            margin-bottom: 0.45rem;
            padding-bottom: 0.45rem;
            border-bottom: 1px solid #e6edf6;
        }

        .notif-filter-btn {
            border: 1px solid #d9e4f1;
            background: #f6f9fd;
            color: #557090;
            border-radius: 999px;
            padding: 0.2rem 0.55rem;
            font-size: 0.72rem;
            font-weight: 700;
            cursor: pointer;
        }

        .notif-filter-btn.is-active {
            background: #138dcc;
            border-color: #138dcc;
            color: #fff;
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
        }

        @media (max-width: 720px) {
            .home-top-icons {
                display: none;
            }

            .report-form {
                grid-template-columns: 1fr;
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
