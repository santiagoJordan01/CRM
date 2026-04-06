@extends('layouts.crm')

@php
    $moduloContext = $moduloContext ?? [
        'clave' => 'filtros',
        'titulo' => 'Filtro',
        'indexRoute' => 'filtros.index',
        'procesoRoute' => 'filtros.proceso',
        'responderRoute' => 'filtros.responder',
    ];

    $opcionesEstado = $opcionesEstado ?? [
        ['status' => 'Viable', 'sub_status' => 'Pendiente Radicar'],
        ['status' => 'No Viable', 'sub_status' => 'Expo Titular Color Semaforo'],
    ];
@endphp

@section('title', 'Detalle ' . strtolower($moduloContext['titulo'] ?? 'filtro') . ' | CRM')

@section('content')
@php
$rolUsuario = auth()->user()->role ?? 'asesor';
$esSupervisor = $rolUsuario === 'supervisor';
$subEstadosRespuesta = collect($opcionesEstado)->pluck('sub_status')->unique()->values();
@endphp

<section class="gfd-view">
    <header class="gfd-head">
        <h1>Datos {{ $moduloContext['titulo'] ?? 'Filtro' }}</h1>
        <p>
            <a href="{{ route('home') }}">Inicio</a> <strong>&rsaquo;</strong>
            <a href="{{ route($moduloContext['indexRoute'] ?? 'filtros.index') }}">{{ $moduloContext['titulo'] ?? 'Filtro' }}</a> <strong>&rsaquo;</strong>
            <a href="{{ route($moduloContext['indexRoute'] ?? 'filtros.index') }}">Listado General</a> <strong>&rsaquo;</strong>
            Detalle
        </p>
    </header>

    @if(session('success'))
    <div class="gfd-alert-success">{{ session('success') }}</div>
    @endif

    <div class="gfd-top-grid">
        <article class="gfd-card gfd-main-card">
            <div class="gfd-main-top">
                <a href="{{ route($moduloContext['procesoRoute'] ?? 'filtros.proceso', ['id' => $registro['id']]) }}" class="gfd-tag">
                    <i class="fas fa-chart-line"></i> Proceso
                </a>
            </div>

            <div class="gfd-main-content">
                <p><strong>Id:</strong> {{ $registro['id'] }}</p>
                <p><strong>Sra:</strong> {{ $registro['nombre'] }}</p>
                <p><strong>Cedula:</strong> {{ $registro['cedula'] }}</p>
                <p><strong>Perfil:</strong> {{ $registro['perfil'] }}</p>
                <p><strong>Ciudad:</strong> {{ $registro['ciudad'] }} <span class="sep">|</span> <strong>Departamento:</strong> {{ strtoupper($registro['departamento']) }}</p>
            </div>

            <div class="gfd-main-footer">
                <p><strong>Asesor:</strong> {{ $registro['asesor'] }}</p>
                <p><strong>Mesa de Control:</strong> {{ $registro['mesa_control'] }}</p>
            </div>
        </article>

        <aside class="gfd-side-stack">
            <article class="gfd-card">
                <div class="gfd-mini-title">Recordatorio / Tarea</div>
                <div class="gfd-mini-body">
                    <p><strong>Fecha Filtro:</strong> {{ $registro['fecha'] }}</p>
                    <p><strong>Ultima modificacion:</strong> {{ $registro['modificacion'] }}</p>
                </div>
            </article>

            <article class="gfd-card">
                <div class="gfd-mini-title alt">Observacion del Asesor</div>
                <div class="gfd-mini-body">
                    <p>{{ $registro['observaciones'] }}</p>
                </div>
            </article>
        </aside>
    </div>

    <article class="gfd-result-card">
        <div class="gfd-result-head">
            <span class="text">Ultimo Resultado {{ strtolower($moduloContext['titulo'] ?? 'filtro') }}:</span>
            <span class="badge ok">{{ $registro['status'] }}</span>
            <span class="badge sub">{{ $registro['sub_status'] }}</span>
            <span class="date">{{ $registro['resultado_fecha'] }}</span>
        </div>

        <div class="gfd-result-observacion">
            <strong>Observacion de Mesa de Control:</strong>
            {{ $registro['observacion_mesa_control'] ?: 'Pendiente respuesta de Mesa de Control.' }}
        </div>

        <div class="gfd-financieras">

            <div class="fin-card support-full">
                <h4>Soportes de Mesa de Control</h4>
                <div class="support-slots">
                    @foreach($soportesMesaControlSlots as $slot)
                    <article class="support-slot">
                        <h5>{{ $slot['titulo'] }}</h5>
                        @if($slot['archivo'])
                        <a href="{{ $slot['archivo']['url'] }}" target="_blank" rel="noopener" class="support-slot-link">
                            <i class="fas fa-file-download"></i>
                            <span>{{ $slot['archivo']['nombre'] }}</span>
                        </a>
                        @else
                        <p class="support-slot-empty">Sin archivo adjunto.</p>
                        @endif
                    </article>
                    @endforeach
                </div>
            </div>
        </div>
    </article>

    @if($puedeResponderMesaControl)
    <article class="gfd-card gfd-response-card">
        <h3>Respuesta de Mesa de Control</h3>
        <form action="{{ route($moduloContext['responderRoute'] ?? 'filtros.responder', $registro['id']) }}" method="POST" enctype="multipart/form-data" class="gfd-response-form">
            @csrf
            <div class="field">
                <label for="status">Estado de respuesta</label>
                <select id="status" name="status" required>
                    <option value="">Seleccione</option>
                    @foreach($opcionesEstado as $opcion)
                        <option value="{{ $opcion['status'] }}">{{ $opcion['status'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="sub_status">Sub status de respuesta</label>
                <select id="sub_status" name="sub_status" required>
                    <option value="">Seleccione</option>
                    @foreach($subEstadosRespuesta as $subEstado)
                        <option value="{{ $subEstado }}">{{ $subEstado }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field full">
                <label for="observacion_mesa_control">Observacion</label>
                <textarea id="observacion_mesa_control" name="observacion_mesa_control" rows="4" required></textarea>
            </div>

            <div class="field">
                <label for="mesa_soporte_1">Soporte 1</label>
                <input type="file" id="mesa_soporte_1" name="mesa_soporte_1" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp3,.wav,.ogg,.m4a,.mp4,.mov,.avi,.mkv,.webm">
            </div>

            <div class="field">
                <label for="mesa_soporte_2">Soporte 2</label>
                <input type="file" id="mesa_soporte_2" name="mesa_soporte_2" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp3,.wav,.ogg,.m4a,.mp4,.mov,.avi,.mkv,.webm">
            </div>

            <div class="field">
                <label for="mesa_soporte_3">Soporte 3</label>
                <input type="file" id="mesa_soporte_3" name="mesa_soporte_3" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp3,.wav,.ogg,.m4a,.mp4,.mov,.avi,.mkv,.webm">
            </div>

            <div class="field full">
                <button type="submit" class="gfd-submit-btn">Guardar respuesta</button>
            </div>
        </form>
    </article>
    @elseif($esSupervisor && empty($registro['observacion_mesa_control']))
    <article class="gfd-card gfd-response-card">
        <p class="gfd-note">Este registro ya no esta en estado pendiente para este modulo y no requiere nueva respuesta.</p>
    </article>
    @endif

    <div class="gfd-actions">
        <a href="{{ route($moduloContext['indexRoute'] ?? 'filtros.index') }}" class="gfd-back-btn">Volver al listado</a>
    </div>
</section>

<style>
    .gfd-view {
        max-width: 100%;
    }

    .gfd-head {
        margin-bottom: 1rem;
    }

    .gfd-head h1 {
        margin: 0;
        font-size: 1.35rem;
        color: #243b58;
        font-weight: 700;
    }

    .gfd-head p {
        margin: 0.3rem 0 0;
        color: #6e7f96;
        font-size: 0.92rem;
    }

    .gfd-head p span {
        color: #4f6380;
        font-weight: 600;
    }

    .gfd-head p strong {
        color: #90a4be;
        margin: 0 0.28rem;
    }

    .gfd-alert-success {
        margin-bottom: 0.85rem;
        border: 1px solid #bde6c8;
        background: #eaf9ef;
        color: #1f6b36;
        border-radius: 8px;
        padding: 0.65rem 0.75rem;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .gfd-top-grid {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(260px, 1fr);
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .gfd-card {
        background: #f9fbfd;
        border: 1px solid #dbe3ef;
        border-radius: 8px;
        box-shadow: 0 10px 20px rgba(26, 48, 78, 0.04);
    }

    .gfd-main-card {
        padding: 0.85rem 0.95rem;
    }

    .gfd-main-top {
        border-bottom: 1px solid #e4ebf4;
        padding-bottom: 0.65rem;
        margin-bottom: 0.65rem;
    }

    .gfd-tag {
        display: inline-flex;
        align-items: center;
        padding: 0.36rem 0.65rem;
        border-radius: 6px;
        background: linear-gradient(180deg, #f7c443, #eba71f);
        color: #fff;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .gfd-main-content p,
    .gfd-main-footer p,
    .gfd-mini-body p {
        margin: 0.3rem 0;
        color: #334861;
        font-size: 0.95rem;
        line-height: 1.35;
    }

    .gfd-main-content strong,
    .gfd-main-footer strong,
    .gfd-mini-body strong {
        color: #3b587e;
        font-weight: 700;
    }

    .gfd-main-footer {
        border-top: 1px solid #e4ebf4;
        padding-top: 0.55rem;
        margin-top: 0.7rem;
    }

    .gfd-side-stack {
        display: grid;
        gap: 1rem;
    }

    .gfd-mini-title {
        padding: 0.62rem 0.78rem;
        border-bottom: 1px solid #e4ebf4;
        background: #1f89c8;
        color: #fff;
        border-radius: 8px 8px 0 0;
        font-size: 0.9rem;
        font-weight: 700;
    }

    .gfd-mini-title.alt {
        background: #f2f6fb;
        color: #29405f;
    }

    .gfd-mini-body {
        padding: 0.7rem 0.78rem;
    }

    .gfd-result-card {
        background: #eaf6ff;
        border: 1px solid #c9e4f8;
        border-radius: 9px;
        padding: 0.9rem;
    }

    .gfd-result-head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.36rem;
        color: #28658f;
        font-size: 1.02rem;
        font-weight: 700;
        margin-bottom: 0.7rem;
        border-bottom: 1px solid #b8dbf3;
        padding-bottom: 0.5rem;
    }

    .gfd-result-head .date {
        color: #366e93;
        font-weight: 600;
    }

    .badge {
        display: inline-block;
        border-radius: 4px;
        padding: 0.16rem 0.45rem;
        color: #fff;
        font-size: 0.78rem;
        line-height: 1.2;
        font-weight: 700;
    }

    .badge.ok {
        background: #39ad56;
    }

    .badge.sub {
        background: #24a4d8;
    }

    .gfd-result-observacion {
        background: #fff;
        border: 1px solid #deedf9;
        border-radius: 6px;
        color: #32506f;
        padding: 0.78rem;
        font-size: 0.93rem;
        margin-bottom: 0.9rem;
    }

    .gfd-financieras {
        display: grid;
        grid-template-columns: repeat(2, minmax(220px, 1fr));
        gap: 0.9rem;
    }

    .fin-card {
        background: #fff;
        border: 1px solid #d7e7f5;
        border-radius: 6px;
        min-height: 120px;
        padding: 0.8rem;
        color: #3e6087;
        display: grid;
        gap: 0.35rem;
        align-content: start;
    }

    .fin-card h4 {
        margin: 0;
        font-size: 0.93rem;
        color: #2f4f75;
    }

    .fin-card a {
        color: #1275b0;
        font-size: 0.86rem;
        text-decoration: none;
        word-break: break-all;
    }

    .fin-card a:hover {
        text-decoration: underline;
    }

    .fin-card p {
        margin: 0;
        color: #6d839e;
        font-size: 0.86rem;
    }

    .support-slots {
        display: grid;
        grid-template-columns: repeat(3, minmax(140px, 1fr));
        gap: 0.6rem;
    }

    .support-slot {
        border: 1px solid #dbe8f5;
        border-radius: 6px;
        background: #f8fbff;
        padding: 0.55rem;
        min-height: 86px;
        display: grid;
        gap: 0.35rem;
        align-content: start;
    }

    .support-slot h5 {
        margin: 0;
        color: #2d5076;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .support-slot-link {
        color: #1275b0;
        font-size: 0.8rem;
        text-decoration: none;
        display: flex;
        gap: 0.3rem;
        align-items: flex-start;
        word-break: break-all;
    }

    .support-slot-link:hover {
        text-decoration: underline;
    }

    .support-slot-empty {
        margin: 0;
        color: #7a8ea8;
        font-size: 0.78rem;
    }

    .fin-card.support-full {
        grid-column: 1 / -1;
    }

    .gfd-response-card {
        margin-top: 1rem;
        padding: 0.9rem;
    }

    .gfd-response-card h3 {
        margin: 0 0 0.8rem;
        color: #2b4465;
        font-size: 1.05rem;
    }

    .gfd-response-form {
        display: grid;
        grid-template-columns: repeat(3, minmax(180px, 1fr));
        gap: 0.8rem;
    }

    .gfd-response-form .field {
        display: grid;
        gap: 0.28rem;
    }

    .gfd-response-form .field.full {
        grid-column: 1 / -1;
    }

    .gfd-response-form label {
        font-size: 0.83rem;
        color: #355476;
        font-weight: 700;
    }

    .gfd-response-form select,
    .gfd-response-form textarea,
    .gfd-response-form input[type="file"] {
        width: 100%;
        border: 1px solid #c8d8ea;
        border-radius: 6px;
        background: #fff;
        color: #2f4966;
        font-size: 0.86rem;
        padding: 0.52rem 0.62rem;
    }

    .gfd-submit-btn {
        border: 0;
        border-radius: 7px;
        background: linear-gradient(180deg, #2a9de0, #1779bd);
        color: #fff;
        font-size: 0.9rem;
        font-weight: 700;
        padding: 0.56rem 1rem;
        cursor: pointer;
        width: fit-content;
    }

    .gfd-note {
        margin: 0;
        color: #4d6481;
        font-size: 0.92rem;
    }

    .gfd-actions {
        margin-top: 1rem;
    }

    .gfd-back-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 36px;
        border-radius: 6px;
        border: 1px solid #88a8c6;
        color: #31567f;
        text-decoration: none;
        padding: 0.45rem 0.95rem;
        font-weight: 700;
        background: #f5fbff;
    }

    .gfd-back-btn:hover {
        background: #e8f4ff;
    }

    @media (max-width: 980px) {
        .gfd-top-grid {
            grid-template-columns: 1fr;
        }

        .gfd-financieras {
            grid-template-columns: 1fr;
        }

        .support-slots {
            grid-template-columns: 1fr;
        }

        .gfd-response-form {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection