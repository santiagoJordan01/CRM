<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'CRM')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --bg: #f3f6fb;
            --sidebar: #ffffff;
            --line: #e3e7ee;
            --text: #2b3240;
            --muted: #5c677a;
            --accent: #1f8dd6;
            --accent-soft: #e7f4ff;
            --danger: #d83b3b;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at 10% 10%, #f7fbff 0%, var(--bg) 55%);
            color: var(--text);
        }

        .sidebar {
            width: 285px;
            height: 100vh;
            background: var(--sidebar);
            border-right: 1px solid var(--line);
            box-shadow: 6px 0 24px rgba(30, 45, 72, 0.06);
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar-top {
            padding: 26px 18px 12px;
        }

        .brand {
            font-size: 30px;
            font-weight: 800;
            color: #1e3b5a;
            letter-spacing: 0.4px;
            margin-bottom: 24px;
        }

        .brand small {
            display: block;
            margin-top: 3px;
            font-size: 15px;
            font-weight: 500;
            color: #8a95a8;
        }

        .nav-links {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .nav-link {
            text-decoration: none;
            color: var(--text);
            font-size: 18px;
            font-weight: 500;
            padding: 11px 14px;
            border-radius: 10px;
            transition: background-color .2s ease, color .2s ease, transform .2s ease;
        }

        .nav-link:hover {
            background: #f5f8fd;
            transform: translateX(2px);
        }

        .nav-link.active {
            background: var(--accent-soft);
            color: var(--accent);
            font-weight: 700;
        }

        .sidebar-bottom {
            margin: 0 18px 24px;
            padding-top: 14px;
            border-top: 1px solid var(--line);
        }

        .logout-btn {
            width: 100%;
            border: none;
            border-radius: 10px;
            background: var(--danger);
            color: #fff;
            font-size: 17px;
            font-weight: 600;
            padding: 10px 16px;
            cursor: pointer;
            transition: filter .2s ease;
        }

        .logout-btn:hover {
            filter: brightness(0.9);
        }

        .main-content {
            margin-left: 285px;
            min-height: 100vh;
            padding: 34px;
        }

        .card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 16px 28px rgba(24, 40, 72, 0.05);
            max-width: 100%;
        }

        .card h1 {
            margin-top: 0;
            margin-bottom: 8px;
            font-size: 32px;
            color: #1f2f4b;
        }

        .card p {
            margin: 0;
            font-size: 18px;
            color: var(--muted);
        }

        .logo-container {
    width: 100%;       /* Ocupa el ancho de la sidebar */
    height: 80px;      /* Ajusta esta altura según te guste */
    overflow: hidden;  /* Esto "corta" lo que sobre al agrandar la imagen */
    display: flex;
    align-items: center;
    justify-content: center;
}

.logo-container img {
    width: 100%;
    height: auto;
    /* EL TRUCO: Aumenta el scale para eliminar el espacio vacío de los lados */
    /* 1.5 es un ejemplo, súbelo a 1.8 o 2.0 si todavía sobra mucho aire */
    transform: scale(1.25); 
    object-fit: contain;
}

        .logo-link {
            display: block;
            text-decoration: none;
            color: inherit;
            border-radius: 8px;
        }

        .logo-link:focus {
            outline: 3px solid rgba(31,141,214,0.14);
            outline-offset: 2px;
        }

        @media (max-width: 980px) {
            .sidebar {
                width: 100%;
                height: auto;
                min-height: auto;
                position: static;
                overflow: visible;
            }

            .sidebar-bottom {
                margin-top: 12px;
            }

            .main-content {
                margin-left: 0;
                padding: 18px;
            }

            .card h1 {
                font-size: 26px;
            }

            .card p {
                font-size: 16px;
            }
        }
    </style>
</head>

<body>
    @php
        $rolSidebar = auth()->user()->role ?? 'asesor';
        $puedeVerLeadsWeb = in_array($rolSidebar, ['supervisor', 'admin'], true);
    @endphp
    <aside class="sidebar">
        <div class="sidebar-top">
            <div class="brand">
                <a href="{{ route('home') }}" class="logo-link" aria-label="Ir a Inicio" title="Ir a Inicio">
                    <div class="logo-container">
                        <img src="{{ asset('img/LOGO_CALAMBAS_MARTINEZ.png') }}" alt="Logo Calambas Martinez">
                    </div>
                </a>
                <small>Panel principal</small>
            </div>


            <nav class="nav-links">
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Inicio
                </a>
                @if($puedeVerLeadsWeb)
                    <a href="{{ route('web-leads.index') }}" class="nav-link {{ request()->routeIs('web-leads.*') ? 'active' : '' }}">
                        <i class="fas fa-bullhorn"></i> Leads web
                    </a>
                @endif
                <a href="{{ route('filtros.index') }}" class="nav-link {{ request()->routeIs('filtros.*') ? 'active' : '' }}">
                    <i class="fas fa-filter"></i> Gestion filtros
                </a>
                <a href="{{ route('radicados.index') }}" class="nav-link {{ request()->routeIs('radicados.*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i> Gestion radicados
                </a>
                <a href="{{ route('aprobados.index') }}" class="nav-link {{ request()->routeIs('aprobados.*') ? 'active' : '' }}">
                    <i class="fas fa-check-circle"></i> Gestion aprobados
                </a>
                <a href="{{ route('desembolso.index') }}" class="nav-link {{ request()->routeIs('desembolso.*') ? 'active' : '' }}">
                    <i class="fas fa-hand-holding-usd"></i> Gestion desembolso
                </a>
            </nav>
        </div>

        <div class="sidebar-bottom">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-btn" type="submit">Salir</button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        @yield('content')
    </main>
</body>

</html>