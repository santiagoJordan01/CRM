@extends('layouts.crm')

@section('title', 'Inicio | CRM')

@section('content')
    @php
        $nombreUsuario = strtoupper(auth()->user()->name ?? 'ASESOR FREELANCE');
    @endphp

    <section class="home-view">
        <header class="home-header">
            <div>
                <h1>Inicio</h1>
                <p>Bienvenido CRM</p>
            </div>
            <div class="home-top-icons">
                <button type="button" aria-label="Pantalla completa"><i class="fas fa-expand"></i></button>
                <button type="button" aria-label="Notificaciones" class="has-alert">
                    <i class="fas fa-bell"></i>
                    <span>0</span>
                </button>
                <button type="button" aria-label="Perfil"><i class="fas fa-user-circle"></i></button>
            </div>
        </header>

        <div class="home-grid-top">
            <article class="panel panel-profile">
                <div class="avatar-circle">
                    <i class="fas fa-user"></i>
                </div>
                <div class="profile-data">
                    <h2>{{ $nombreUsuario }}</h2>
                    <p><span class="badge-type">Asesor Freelance</span> <span class="badge-status">Activo</span></p>
                    <small>User: ANDREA GONZALEZ MONJE</small>
                </div>
            </article>

            <article class="panel panel-add">
                <a href="{{ route('registros') }}" class="add-filter-link">
                    <span class="add-title">AGREGAR FILTRO</span>
                    <i class="fas fa-user-plus"></i>
                </a>
            </article>

            <article class="panel panel-reminder">
                <h3>Recordatorio / Tarea / General</h3>
                <div class="reminder-body"></div>
            </article>
        </div>

        <div class="home-grid-middle">
            <article class="panel panel-report">
                <h3>Crear informe</h3>
                <form class="report-form" action="#" method="GET" onsubmit="return false;">
                    <div class="field">
                        <label for="banco">Banco</label>
                        <select id="banco" name="banco">
                            <option>- Seleccione -</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="producto">Producto</label>
                        <select id="producto" name="producto">
                            <option>- Seleccione -</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="canal">Canal</label>
                        <select id="canal" name="canal">
                            <option>- Seleccione -</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="fecha">Fecha</label>
                        <input id="fecha" name="fecha" type="text" />
                    </div>
                    <div class="field actions">
                        <button type="submit">Buscar</button>
                    </div>
                </form>
            </article>
        </div>

        <article class="panel panel-calendar">
            <h3>Calendario de Actividades</h3>
            <div class="calendar-toolbar">
                <div class="calendar-nav">
                    <button type="button" aria-label="Anterior">&lt;</button>
                    <button type="button" aria-label="Siguiente">&gt;</button>
                </div>
                <strong>MARZO 2026</strong>
                <div class="calendar-modes">
                    <button type="button" class="active">Mes</button>
                    <button type="button">Semana</button>
                    <button type="button">Dia</button>
                </div>
            </div>

            <table class="calendar-grid">
                <thead>
                    <tr>
                        <th>LUN.</th>
                        <th>MAR.</th>
                        <th>MIE.</th>
                        <th>JUE.</th>
                        <th>VIE.</th>
                        <th>SAB.</th>
                        <th>DOM.</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="muted">28</td>
                        <td>1</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>3</td>
                        <td>4</td>
                        <td>5</td>
                        <td>6</td>
                        <td>7</td>
                        <td>8</td>
                    </tr>
                </tbody>
            </table>
        </article>
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
        }

        .avatar-circle {
            width: 64px;
            height: 64px;
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
        }

        .add-filter-link {
            min-height: 112px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 0.4rem;
            padding: 0.9rem 1rem;
            background: linear-gradient(135deg, #0f83bd, #1aa5db);
            color: #fff;
            text-decoration: none;
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
        }

        .feed-item {
            display: flex;
            gap: 0.6rem;
            align-items: flex-start;
        }

        .feed-item .dot {
            margin-top: 0.35rem;
            width: 11px;
            height: 11px;
            border-radius: 50%;
            background: #17a4bf;
            flex-shrink: 0;
        }

        .feed-item small {
            color: #7a8da4;
            font-size: 0.78rem;
            font-weight: 600;
        }

        .feed-item p {
            margin: 0.22rem 0;
            color: #3b516d;
            font-size: 0.9rem;
            line-height: 1.35;
        }

        .feed-item a {
            color: #2a8ecb;
            text-decoration: none;
            font-size: 0.84rem;
            font-weight: 600;
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

        .panel-calendar {
            margin-bottom: 0.2rem;
        }

        .calendar-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.65rem;
            gap: 0.6rem;
        }

        .calendar-toolbar strong {
            color: #3b4f6d;
            font-size: 1rem;
        }

        .calendar-nav {
            display: flex;
            gap: 0.25rem;
        }

        .calendar-nav button,
        .calendar-modes button {
            height: 30px;
            border: 1px solid #d4deec;
            background: #fff;
            color: #5b6f8a;
            border-radius: 3px;
            padding: 0 0.6rem;
            cursor: pointer;
            font-weight: 600;
        }

        .calendar-modes {
            display: flex;
            gap: 0.22rem;
        }

        .calendar-modes .active {
            background: #6070cf;
            border-color: #6070cf;
            color: #fff;
        }

        .calendar-grid {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            background: #fff;
            border: 1px solid #dde6f2;
        }

        .calendar-grid th,
        .calendar-grid td {
            border: 1px solid #e7edf6;
            text-align: center;
            padding: 0.5rem 0.3rem;
            color: #5a6e88;
            font-size: 0.85rem;
        }

        .calendar-grid th {
            background: #f6f9fd;
            color: #435c7b;
            font-weight: 700;
        }

        .calendar-grid td {
            height: 50px;
            vertical-align: top;
        }

        .calendar-grid .muted {
            color: #b0bccd;
        }

        @media (max-width: 1200px) {
            .home-grid-top {
                grid-template-columns: 1fr;
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
@endsection
