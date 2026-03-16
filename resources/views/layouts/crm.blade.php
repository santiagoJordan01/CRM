<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'CRM')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
            display: flex;
        }

        .sidebar {
            width: 285px;
            min-height: 100vh;
            background: var(--sidebar);
            border-right: 1px solid var(--line);
            box-shadow: 6px 0 24px rgba(30, 45, 72, 0.06);
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
            flex: 1;
            padding: 34px;
        }

        .card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 16px 28px rgba(24, 40, 72, 0.05);
            max-width: 980px;
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

        @media (max-width: 980px) {
            body {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                min-height: auto;
            }

            .sidebar-bottom {
                margin-top: 12px;
            }

            .main-content {
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
    <aside class="sidebar">
        <div class="sidebar-top">
            <div class="brand">
                CRM
                <small>Panel principal</small>
            </div>

            <nav class="nav-links">
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Inicio</a>
                <a href="{{ route('filtros.index') }}" class="nav-link {{ request()->routeIs('filtros.*') ? 'active' : '' }}">Gestion filtros</a>
                <a href="{{ route('radicados.index') }}" class="nav-link {{ request()->routeIs('radicados.*') ? 'active' : '' }}">Gestion radicados</a>
                <a href="{{ route('aprobados.index') }}" class="nav-link {{ request()->routeIs('aprobados.*') ? 'active' : '' }}">Gestion aprobados</a>
                <a href="{{ route('desembolso.index') }}" class="nav-link {{ request()->routeIs('desembolso.*') ? 'active' : '' }}">Gestion desembolso</a>
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
