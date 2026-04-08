@extends('layouts.crm')

@php
    $moduloContext = $moduloContext ?? [
        'titulo' => 'Filtro',
        'indexRoute' => 'filtros.index',
        'showRoute' => 'filtros.show',
    ];

    $opcionesEstado = $opcionesEstado ?? [
        ['status' => 'Viable', 'sub_status' => 'Pendiente Radicar'],
        ['status' => 'No Viable', 'sub_status' => 'Expo Titular Color Semaforo'],
    ];

    $opcionesEstadoAsesor = $opcionesEstadoAsesor ?? [
        ['status' => 'Preradicacion Comercial', 'sub_status' => 'Envio Digital Docs'],
    ];
    $catalogoStatusGeneral = $catalogoStatusGeneral ?? [];
    $catalogoSubStatusGeneral = $catalogoSubStatusGeneral ?? [];
    $mostrarPanelAsesor = $mostrarPanelAsesor ?? false;
    $esAsesor = $esAsesor ?? false;
    $puedeActualizarAsesor = $puedeActualizarAsesor ?? false;
    $puedeActualizarSupervisor = $puedeActualizarSupervisor ?? false;
    $puedeCrearNuevoFiltroAsesor = $puedeCrearNuevoFiltroAsesor ?? false;

    $historialSeccionado = $historialSeccionado ?? [
        [
            'clave' => $moduloContext['clave'] ?? 'filtros',
            'titulo' => $moduloContext['titulo_gestion'] ?? 'Gestion filtros',
            'items' => $historial ?? [],
        ],
    ];

    $statusPositivos = ['Viable', 'Preradicacion Comercial', 'Envio Digital Docs', 'Radicacion Iniciada', 'En Estudio', 'Radicado', 'Aprobado', 'Contabilizacion aceptada', 'Desembolsado'];
@endphp

@section('title', 'Proceso ' . strtolower($moduloContext['titulo'] ?? 'filtro') . ' | CRM')

@section('content')
<section class="pro-view">
    <header class="pro-head">
        <h1>Datos {{ $moduloContext['titulo'] ?? 'Filtro' }}</h1>
        <p>
            <a href="{{ route('home') }}">Inicio</a> <strong>&rsaquo;</strong>
            <a href="{{ route($moduloContext['indexRoute'] ?? 'filtros.index') }}">{{ $moduloContext['titulo'] ?? 'Filtro' }}</a> <strong>&rsaquo;</strong>
            <a href="{{ route($moduloContext['indexRoute'] ?? 'filtros.index') }}">Listado General</a> <strong>&rsaquo;</strong>
            <a href="{{ route($moduloContext['showRoute'] ?? 'filtros.show', $registro['id']) }}">Ver perfil</a> <strong>&rsaquo;</strong>
            Detalle
        </p>
    </header>

    @if($errors->any())
        <div class="pro-alert-error">
            <strong>No se pudo actualizar el estado.</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="pro-top-grid">
        <article class="pro-card pro-main-card">
            <div class="pro-main-top">
                <a href="{{ route($moduloContext['showRoute'] ?? 'filtros.show', $registro['id']) }}" class="pro-tag">Ver perfil</a>
            </div>

            <div class="pro-main-content">
                <p><strong>Id:</strong> {{ $registro['id'] }}</p>
                <p><strong>Sra:</strong> {{ $registro['nombre'] }}</p>
                <p><strong>Cedula:</strong> {{ $registro['cedula'] }}</p>
                <p><strong>Perfil:</strong> {{ $registro['perfil'] }}</p>
                <p><strong>Ciudad:</strong> {{ $registro['ciudad'] }} <span class="sep">|</span> <strong>Departamento:</strong> {{ strtoupper($registro['departamento']) }}</p>
            </div>

            <div class="pro-main-footer">
                <p><strong>Asesor:</strong> {{ $registro['asesor'] }}</p>
                <p><strong>Supervisor:</strong> {{ $registro['mesa_control'] }}</p>
            </div>
        </article>

        <aside class="pro-side-stack">
            <article class="pro-card">
                <div class="pro-mini-title">Recordatorio / Tarea</div>
                <div class="pro-mini-body">
                    <p><strong>Fecha Filtro:</strong> {{ $registro['fecha'] }}</p>
                    <p><strong>Ultimo Status:</strong> {{ $registro['resultado_fecha'] }}</p>
                </div>
            </article>
        </aside>
    </div>

    <article class="pro-card pro-history-card">
        <h3>Historial {{ $moduloContext['titulo'] ?? 'Filtro' }}</h3>
        @foreach($historialSeccionado as $seccion)
            <section class="pro-history-section">
                <h4>{{ $seccion['titulo'] }}</h4>
                <div class="pro-table-wrap">
                    <table class="pro-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Status</th>
                                <th>Sub Status</th>
                                <th>Comentario</th>
                                <th>Respuesta</th>
                                <th>Soporte</th>
                                <th>Autor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(($seccion['items'] ?? []) as $item)
                                <tr>
                                    <td>{{ $item['fecha'] }}</td>
                                    <td>
                                        <span class="pro-badge {{ in_array($item['status'], $statusPositivos, true) ? 'ok' : 'warn' }}">{{ $item['status'] }}</span>
                                    </td>
                                    <td>
                                        <span class="pro-badge sub">{{ $item['sub_status'] }}</span>
                                    </td>
                                    <td>{{ $item['comentario'] }}</td>
                                    <td>
                                        <div class="pro-history-files">
                                            @forelse(($item['respuesta_archivos'] ?? []) as $archivo)
                                                <a href="{{ $archivo['url'] }}" target="_blank" rel="noopener" class="pro-history-file-link" title="{{ $archivo['nombre'] }}">
                                                    <i class="fas fa-cloud-download-alt"></i>
                                                </a>
                                            @empty
                                                <span class="pro-history-file-empty">--</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td>
                                        <div class="pro-history-files">
                                            @forelse(($item['soporte_archivos'] ?? []) as $archivo)
                                                <a href="{{ $archivo['url'] }}" target="_blank" rel="noopener" class="pro-history-file-link" title="{{ $archivo['nombre'] }}">
                                                    <i class="fas fa-cloud-download-alt"></i>
                                                </a>
                                            @empty
                                                <span class="pro-history-file-empty">--</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td>{{ $item['autor'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endforeach
    </article>

    <article class="pro-card pro-support-card">
        <h3>Soporte {{ $moduloContext['titulo'] ?? 'Filtro' }}</h3>
        <div class="pro-support-grid">
            @foreach($soportesCreacionSlots as $slot)
                <article class="pro-support-item">
                    <h4>{{ $slot['titulo'] }}</h4>
                    @if($slot['archivo'])
                        <a href="{{ $slot['archivo']['url'] }}" target="_blank" rel="noopener" class="pro-support-link">
                            <i class="fas fa-cloud-download-alt"></i>
                            <span>{{ $slot['archivo']['nombre'] }}</span>
                        </a>
                    @else
                        <p class="pro-support-empty">Sin archivo</p>
                    @endif
                </article>
            @endforeach
        </div>
    </article>

    @php
        $statusActual = $registro['status'] ?? 'Sin status';
        $subStatusActual = $registro['sub_status'] ?? 'Sin sub status';
    @endphp

    @if($puedeActualizarSupervisor)
        @php
            $estadosSupervisor = collect($opcionesEstado)->pluck('status')->unique()->values();
            $subEstadosSupervisor = collect($opcionesEstado)->pluck('sub_status')->unique()->values();
        @endphp

        <article class="pro-card pro-update-card">
            <div class="pro-update-panel">
                <header class="pro-update-head">
                    <h3>Actualizar {{ $moduloContext['titulo'] ?? 'Filtro' }} (Mesa de Control)</h3>
                    <span class="pro-badge {{ in_array($statusActual, $statusPositivos, true) ? 'ok' : 'warn' }}">{{ $statusActual }}</span>
                    <span class="pro-badge sub">{{ $subStatusActual }}</span>
                </header>

                <form class="pro-update-form" action="{{ route($moduloContext['responderRoute'] ?? 'filtros.responder', $registro['id']) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="pro-update-field">
                        <label for="pro_status_supervisor">Status:</label>
                        <select id="pro_status_supervisor" name="status" required>
                            <option value="">- Seleccione -</option>
                            @foreach($estadosSupervisor as $estado)
                                <option value="{{ $estado }}" {{ old('status') === $estado ? 'selected' : '' }}>{{ $estado }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pro-update-field">
                        <label for="pro_sub_status_supervisor">Sub Status:</label>
                        <select id="pro_sub_status_supervisor" name="sub_status" required>
                            <option value="">- Seleccione -</option>
                            @foreach($subEstadosSupervisor as $subEstado)
                                <option value="{{ $subEstado }}" {{ old('sub_status') === $subEstado ? 'selected' : '' }}>{{ $subEstado }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pro-update-field">
                        <label for="pro_mesa_soporte_1">Soporte 1:</label>
                        <input id="pro_mesa_soporte_1" name="mesa_soporte_1" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp3,.wav,.ogg,.m4a,.mp4,.mov,.avi,.mkv,.webm" />
                    </div>

                    <div class="pro-update-field">
                        <label for="pro_mesa_soporte_2">Soporte 2:</label>
                        <input id="pro_mesa_soporte_2" name="mesa_soporte_2" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp3,.wav,.ogg,.m4a,.mp4,.mov,.avi,.mkv,.webm" />
                    </div>

                    <div class="pro-update-field">
                        <label for="pro_mesa_soporte_3">Soporte 3:</label>
                        <input id="pro_mesa_soporte_3" name="mesa_soporte_3" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp3,.wav,.ogg,.m4a,.mp4,.mov,.avi,.mkv,.webm" />
                    </div>
                    <div class="pro-update-field">
                        <label for="pro_mesa_soporte_4">Soporte 4:</label>
                        <input id="pro_mesa_soporte_4" name="mesa_soporte_4" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp3,.wav,.ogg,.m4a,.mp4,.mov,.avi,.mkv,.webm" />
                    </div>
                    <div class="pro-update-field">
                        <label for="pro_mesa_soporte_5">Soporte 5:</label>
                        <input id="pro_mesa_soporte_5" name="mesa_soporte_5" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp3,.wav,.ogg,.m4a,.mp4,.mov,.avi,.mkv,.webm" />
                    </div>

                    <div class="pro-update-field">
                        <label for="pro_mesa_soporte_6">Soporte 6:</label>
                        <input id="pro_mesa_soporte_6" name="mesa_soporte_6" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp3,.wav,.ogg,.m4a,.mp4,.mov,.avi,.mkv,.webm" />
                    </div>

                    <div class="pro-update-field pro-update-field-full">
                        <label for="pro_observacion_mesa_control">Observaciones:</label>
                        <textarea id="pro_observacion_mesa_control" name="observacion_mesa_control" rows="4" required placeholder="Ingresa observaciones de Mesa de Control">{{ old('observacion_mesa_control') }}</textarea>
                    </div>

                    <div class="pro-update-actions">
                        <button type="submit" class="pro-update-btn">Guardar respuesta</button>
                    </div>
                </form>
            </div>
        </article>
    @elseif($mostrarPanelAsesor)
        @php
            $estadosAsesorPermitidos = collect($opcionesEstadoAsesor)->pluck('status')->unique()->values();
            $subEstadosAsesorPermitidos = collect($opcionesEstadoAsesor)->pluck('sub_status')->unique()->values();

            $estadosAsesorVisibles = collect($catalogoStatusGeneral)->filter()->unique()->values();
            $subEstadosAsesorVisibles = collect($catalogoSubStatusGeneral)->filter()->unique()->values();

            if ($estadosAsesorVisibles->isEmpty()) {
                $estadosAsesorVisibles = $estadosAsesorPermitidos;
            }

            if ($subEstadosAsesorVisibles->isEmpty()) {
                $subEstadosAsesorVisibles = $subEstadosAsesorPermitidos;
            }

            $soloLecturaTexto = ' (solo lectura)';
        @endphp

        <article class="pro-card pro-update-card">
            <div class="pro-update-panel">
                <header class="pro-update-head">
                    <h3>Actualizar {{ $moduloContext['titulo'] ?? 'Filtro' }}</h3>
                    <span class="pro-badge {{ in_array($statusActual, $statusPositivos, true) ? 'ok' : 'warn' }}">{{ $statusActual }}</span>
                    <span class="pro-badge sub">{{ $subStatusActual }}</span>
                </header>

                <form class="pro-update-form" action="{{ route('filtros.asesor.update', $registro['id']) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="pro-update-field pro-update-field-full">
                        <p class="pro-update-help">
                            Puedes visualizar todos los status y sub status del flujo. Solo puedes seleccionar los permitidos para asesor.
                        </p>
                        @if(! $puedeActualizarAsesor)
                            <p class="pro-update-note">
                                Actualizacion no disponible: el cliente debe estar en <strong>Viable / Pendiente Radicar</strong>.
                            </p>
                        @endif
                    </div>

                    <div class="pro-update-field">
                        <label for="pro_status_asesor">Status:</label>
                        <select id="pro_status_asesor" name="status" required>
                            <option value="">- Seleccione -</option>
                            @foreach($estadosAsesorVisibles as $estado)
                                @php
                                    $permitidoStatus = $estadosAsesorPermitidos->contains($estado);
                                @endphp
                                <option value="{{ $estado }}" {{ old('status') === $estado ? 'selected' : '' }} {{ $permitidoStatus ? '' : 'disabled' }}>
                                    {{ $estado }}{{ $permitidoStatus ? '' : $soloLecturaTexto }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pro-update-field">
                        <label for="pro_sub_status_asesor">Sub Status:</label>
                        <select id="pro_sub_status_asesor" name="sub_status" required>
                            <option value="">- Seleccione -</option>
                            @foreach($subEstadosAsesorVisibles as $subEstado)
                                @php
                                    $permitidoSubStatus = $subEstadosAsesorPermitidos->contains($subEstado);
                                @endphp
                                <option value="{{ $subEstado }}" {{ old('sub_status') === $subEstado ? 'selected' : '' }} {{ $permitidoSubStatus ? '' : 'disabled' }}>
                                    {{ $subEstado }}{{ $permitidoSubStatus ? '' : $soloLecturaTexto }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pro-update-field">
                        <label for="pro_soporte_1">Soporte 1:</label>
                        <input id="pro_soporte_1" name="soporte_1" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp3,.wav,.ogg,.m4a,.mp4,.mov,.avi,.mkv,.webm" />
                    </div>

                    <div class="pro-update-field">
                        <label for="pro_soporte_2">Soporte 2:</label>
                        <input id="pro_soporte_2" name="soporte_2" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp3,.wav,.ogg,.m4a,.mp4,.mov,.avi,.mkv,.webm" />
                    </div>

                    <div class="pro-update-field">
                        <label for="pro_soporte_3">Soporte 3:</label>
                        <input id="pro_soporte_3" name="soporte_3" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp3,.wav,.ogg,.m4a,.mp4,.mov,.avi,.mkv,.webm" />
                    </div>

                    <div class="pro-update-field">
                        <label for="pro_soporte_4">Soporte 4:</label>
                        <input id="pro_soporte_4" name="soporte_4" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp3,.wav,.ogg,.m4a,.mp4,.mov,.avi,.mkv,.webm" />
                    </div>

                    <div class="pro-update-field">
                        <label for="pro_soporte_5">Soporte 5:</label>
                        <input id="pro_soporte_5" name="soporte_5" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp3,.wav,.ogg,.m4a,.mp4,.mov,.avi,.mkv,.webm" />
                    </div>

                    <div class="pro-update-field">
                        <label for="pro_soporte_6">Soporte 6:</label>
                        <input id="pro_soporte_6" name="soporte_6" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp3,.wav,.ogg,.m4a,.mp4,.mov,.avi,.mkv,.webm" />
                    </div>

                    <div class="pro-update-field pro-update-field-full">
                        <label for="pro_observaciones">Observaciones:</label>
                        <textarea id="pro_observaciones" name="observaciones" rows="4" placeholder="Ingresa observaciones de la gestion">{{ old('observaciones') }}</textarea>
                    </div>

                    <div class="pro-update-actions">
                        <button type="submit" class="pro-update-btn" {{ $puedeActualizarAsesor ? '' : 'disabled' }}>
                            {{ $puedeActualizarAsesor ? 'Actualizar Filtro' : 'Actualizacion no disponible' }}
                        </button>
                    </div>
                </form>
            </div>
        </article>
    @endif

    @if($puedeCrearNuevoFiltroAsesor)
        <article class="pro-card pro-update-card">
            <div class="pro-update-panel">
                <header class="pro-update-head">
                    <h3>Nuevo Filtro Disponible</h3>
                    <span class="pro-badge ok">Reconsideracion</span>
                </header>
                <p class="pro-update-help">
                    Este cliente quedo en estado <strong>Negado</strong> con sub status <strong>{{ $registro['sub_status'] }}</strong>.
                    Puedes crear un nuevo filtro y adjuntar nuevas evidencias para reintentar la gestion.
                </p>
                <div class="pro-update-actions">
                    <a href="{{ route('registros') }}" class="pro-update-btn" style="text-decoration:none; display:inline-flex; align-items:center;">
                        Crear nuevo filtro
                    </a>
                </div>
            </div>
        </article>
    @endif

    <div class="pro-actions">
        <a href="{{ route($moduloContext['showRoute'] ?? 'filtros.show', $registro['id']) }}" class="pro-back">Volver al detalle</a>
    </div>
</section>

<style>
    .pro-view {
        max-width: 100%;
    }

    .pro-head {
        margin-bottom: 1rem;
    }

    .pro-head h1 {
        margin: 0;
        font-size: 1.35rem;
        color: #243b58;
        font-weight: 700;
    }

    .pro-head p {
        margin: 0.3rem 0 0;
        color: #6e7f96;
        font-size: 0.92rem;
    }

    .pro-alert-error {
        margin-bottom: 0.9rem;
        border: 1px solid #efc7c7;
        background: #fff2f2;
        color: #8f3030;
        border-radius: 8px;
        padding: 0.65rem 0.75rem;
        font-size: 0.9rem;
    }

    .pro-alert-error ul {
        margin: 0.35rem 0 0;
        padding-left: 1.1rem;
    }

    .pro-top-grid {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(260px, 1fr);
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .pro-card {
        background: #f9fbfd;
        border: 1px solid #dbe3ef;
        border-radius: 8px;
        box-shadow: 0 8px 16px rgba(26, 48, 78, 0.04);
        padding: 0.9rem;
    }

    .pro-main-top {
        border-bottom: 1px solid #e4ebf4;
        padding-bottom: 0.65rem;
        margin-bottom: 0.65rem;
    }

    .pro-tag {
        display: inline-flex;
        align-items: center;
        padding: 0.32rem 0.62rem;
        border-radius: 6px;
        background: linear-gradient(180deg, #f7c443, #eba71f);
        color: #fff;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .pro-main-content p,
    .pro-main-footer p {
        margin: 0.3rem 0;
        color: #334861;
        font-size: 0.92rem;
    }

    .pro-main-footer {
        border-top: 1px solid #e4ebf4;
        padding-top: 0.55rem;
        margin-top: 0.7rem;
    }

    .pro-mini-title {
        padding: 0.5rem 0.65rem;
        border-radius: 6px;
        background: #1f89c8;
        color: #fff;
        display: inline-flex;
        font-size: 0.9rem;
        font-weight: 700;
    }

    .pro-mini-body p {
        margin: 0.45rem 0 0;
        color: #334861;
        font-size: 0.9rem;
    }

    .pro-history-card h3,
    .pro-support-card h3 {
        margin: 0 0 0.75rem;
        color: #2f4f75;
        font-size: 1rem;
    }

    .pro-history-section + .pro-history-section {
        margin-top: 1rem;
        padding-top: 0.9rem;
        border-top: 1px dashed #d7e3f2;
    }

    .pro-history-section h4 {
        margin: 0 0 0.55rem;
        color: #1f89c8;
        font-size: 0.92rem;
        font-weight: 700;
    }

    .pro-table-wrap {
        overflow-x: auto;
    }

    .pro-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 980px;
    }

    .pro-table th,
    .pro-table td {
        border-bottom: 1px solid #e7edf5;
        text-align: left;
        vertical-align: top;
        padding: 0.46rem 0.35rem;
        font-size: 0.8rem;
        color: #2f435d;
    }

    .pro-table th {
        color: #506b8a;
        font-weight: 700;
    }

    .pro-history-files {
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem;
        align-items: center;
        min-height: 22px;
    }

    .pro-history-file-link {
        color: #30a94a;
        text-decoration: none;
        font-size: 0.95rem;
        line-height: 1;
    }

    .pro-history-file-link:hover {
        filter: brightness(0.9);
    }

    .pro-history-file-empty {
        color: #93a4b9;
        font-size: 0.78rem;
    }

    .pro-badge {
        display: inline-block;
        border-radius: 4px;
        padding: 0.12rem 0.35rem;
        color: #fff;
        font-size: 0.72rem;
        font-weight: 700;
    }

    .pro-badge.ok {
        background: #3aaa58;
    }

    .pro-badge.warn {
        background: #d65b5b;
    }

    .pro-badge.sub {
        background: #24a4d8;
    }

    .pro-support-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(130px, 1fr));
        gap: 0.7rem;
        margin-bottom: 0.9rem;
    }

    .pro-support-item {
        border: 1px solid #dbe8f5;
        border-radius: 6px;
        background: #f8fbff;
        padding: 0.55rem;
        min-height: 78px;
        display: grid;
        gap: 0.35rem;
        align-content: start;
    }

    .pro-support-item h4 {
        margin: 0;
        color: #2d5076;
        font-size: 0.79rem;
        font-weight: 700;
    }

    .pro-support-link {
        color: #1275b0;
        text-decoration: none;
        display: flex;
        gap: 0.3rem;
        align-items: flex-start;
        word-break: break-all;
        font-size: 0.76rem;
    }

    .pro-support-link:hover {
        text-decoration: underline;
    }

    .pro-support-empty {
        margin: 0;
        color: #7a8ea8;
        font-size: 0.76rem;
    }

    .pro-update-card {
        margin-top: 1rem;
        padding: 0.75rem;
        display: grid;
        gap: 0.95rem;
    }


    .pro-update-support-box {
        border: 1px solid #dbe8f5;
        border-radius: 6px;
        background: #f5f7fa;
        padding: 0.45rem;
        min-height: 58px;
        display: grid;
        gap: 0.3rem;
        align-content: center;
        justify-items: start;
    }

    .pro-update-support-box h4 {
        margin: 0;
        color: #2d5076;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .pro-update-support-link {
        color: #2e9e38;
        font-size: 1rem;
        text-decoration: none;
    }

    .pro-update-support-empty {
        color: #8a9bb0;
        font-size: 0.72rem;
    }

    .pro-update-panel {
        border: 1px solid #d4dce8;
        border-radius: 6px;
        background: #f2f2f2;
        padding: 0.8rem;
    }

    .pro-update-head {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        border-bottom: 1px solid #d9dee6;
        padding-bottom: 0.5rem;
        margin-bottom: 0.8rem;
    }

    .pro-update-head h3 {
        margin: 0;
        color: #243b58;
        font-size: 1rem;
    }

    .pro-update-form {
        display: grid;
        grid-template-columns: repeat(2, minmax(220px, 1fr));
        gap: 0.75rem;
    }

    .pro-update-field {
        display: grid;
        gap: 0.35rem;
    }

    .pro-update-field-full {
        grid-column: 1 / -1;
    }

    .pro-update-field label {
        color: #334861;
        font-size: 0.84rem;
        font-weight: 700;
    }

    .pro-update-field select,
    .pro-update-field input,
    .pro-update-field textarea {
        width: 100%;
        border: 1px solid #d2dae6;
        border-radius: 4px;
        background: #fff;
        color: #2f435d;
        font-size: 0.82rem;
        padding: 0.55rem;
    }

    .pro-update-field textarea {
        min-height: 90px;
        resize: vertical;
    }

    .pro-update-actions {
        grid-column: 1 / -1;
    }

    .pro-update-help,
    .pro-update-note {
        margin: 0;
        font-size: 0.82rem;
        line-height: 1.35;
    }

    .pro-update-help {
        color: #31567f;
    }

    .pro-update-note {
        margin-top: 0.25rem;
        color: #8f3030;
    }

    .pro-update-field select option:disabled {
        color: #8d9cb0;
    }

    .pro-update-btn {
        border: none;
        border-radius: 6px;
        background: #39ad56;
        color: #fff;
        font-size: 0.86rem;
        font-weight: 700;
        padding: 0.54rem 1rem;
        cursor: pointer;
    }

    .pro-update-btn:hover {
        filter: brightness(0.94);
    }

    .pro-update-btn:disabled {
        background: #8ca3bb;
        cursor: not-allowed;
        filter: none;
    }

    .pro-actions {
        margin-top: 1rem;
    }

    .pro-back {
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

    .pro-back:hover {
        background: #e8f4ff;
    }

    @media (max-width: 1100px) {
        .pro-top-grid {
            grid-template-columns: 1fr;
        }

        .pro-support-grid {
            grid-template-columns: repeat(2, minmax(130px, 1fr));
        }

        
    }

    @media (max-width: 780px) {
        .pro-support-grid {
            grid-template-columns: 1fr;
        }

        .pro-update-form {
            grid-template-columns: 1fr;
        }

       
    }

 
</style>
@endsection
