@extends('layouts.crm')

@section('title', 'Detalle filtro | CRM')

@section('content')
    <section class="gfd-view">
        <header class="gfd-head">
            <h1>Datos Filtro</h1>
            <p><span>Inicio</span> <strong>&rsaquo;</strong> <span>Listado General</span> <strong>&rsaquo;</strong> Detalle</p>
        </header>

        <div class="gfd-top-grid">
            <article class="gfd-card gfd-main-card">
                <div class="gfd-main-top">
                    <span class="gfd-tag">Proceso -A</span>
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
                    <p><strong>Supervisor:</strong> {{ $registro['supervisor'] }}</p>
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
                    <div class="gfd-mini-title alt">Observaciones</div>
                    <div class="gfd-mini-body">
                        <p>{{ $registro['recordatorio'] }}</p>
                    </div>
                </article>
            </aside>
        </div>

        <article class="gfd-result-card">
            <div class="gfd-result-head">
                <span class="text">Ultimo Resultado filtro:</span>
                <span class="badge ok">{{ $registro['status'] }}</span>
                <span class="badge sub">{{ $registro['sub_status'] }}</span>
                <span class="date">{{ $registro['resultado_fecha'] }}</span>
            </div>

            <div class="gfd-result-observacion">
                <strong>Observacion:</strong> {{ $registro['observaciones'] }}
            </div>

            <div class="gfd-financieras">
                <div class="fin-card">Financiera 1: <span class="up">☂</span></div>
                <div class="fin-card">Financiera 2: <span class="up">☂</span></div>
                <div class="fin-card">Financiera 3:</div>
            </div>
        </article>

        <div class="gfd-actions">
            <a href="{{ route('filtros.index') }}" class="gfd-back-btn">Volver al listado</a>
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
            grid-template-columns: repeat(3, minmax(180px, 1fr));
            gap: 0.9rem;
        }

        .fin-card {
            background: #fff;
            border: 1px solid #d7e7f5;
            border-radius: 6px;
            min-height: 78px;
            padding: 0.8rem;
            color: #3e6087;
            font-size: 1.7rem;
            font-weight: 700;
            display: flex;
            align-items: center;
        }

        .up {
            margin-left: 0.3rem;
            color: #30b05f;
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
        }
    </style>
@endsection
