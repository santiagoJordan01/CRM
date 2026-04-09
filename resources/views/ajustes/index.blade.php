@extends('layouts.crm')

@section('title', 'Ajustes')

@section('content')
    <div class="home-top-icons">
        <button type="button" id="btn-fullscreen" aria-label="Pantalla completa"><i class="fas fa-expand"></i></button>
        <button type="button" id="btn-notifications" aria-label="Notificaciones" class="">
            <i class="fas fa-bell"></i>
        </button>
        <button type="button" id="btn-profile" aria-label="Perfil"><i class="fas fa-user-circle"></i></button>

        <div class="top-popover" id="notifications-popover" hidden>
            <h4>Notificaciones</h4>
            <p class="empty-state">No hay notificaciones recientes.</p>
        </div>

        <div class="top-popover" id="profile-popover" hidden>
            <h4>Mi perfil</h4>
            <p class="profile-email">{{ auth()->user()->email ?? 'sin-email' }}</p>
            <a href="{{ route('registros') }}" class="mini-link">Crear nuevo filtro</a>
            <a href="{{ route('ajustes.index') }}" class="mini-link">Ajustes</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-mini">Cerrar sesion</button>
            </form>
        </div>
    </div>

    <style>
        :root {
            --accent: #2a9de0;
            --accent-dark: #1779bd;
            --accent-soft: #e9f3fa;
            --text: #1f2e4a;
            --text-light: #4a627a;
            --muted: #6c86a3;
            --line: #e2e8f0;
            --danger: #dc3545;
            --success: #28a745;
            --warning: #ffc107;
            --bg-card: #ffffff;
            --shadow: 0 8px 20px rgba(0, 0, 0, 0.03), 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .settings-card {
            background: transparent;
            border: none;
            box-shadow: none;
        }

        .card-header {
            margin-bottom: 1.5rem;
        }

        .card-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text);
            margin: 0 0 0.25rem 0;
        }

        .muted {
            color: var(--muted);
            font-size: 0.95rem;
            margin: 0;
        }

        .alert-card {
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.25rem;
            font-size: 0.9rem;
            font-weight: 500;
            border-left: 4px solid;
        }

        .alert-success {
            background: #e8f5e9;
            border-left-color: var(--success);
            color: #1e5a2b;
        }

        .alert-error {
            background: #ffebee;
            border-left-color: var(--danger);
            color: #b71c1c;
        }

        .settings-grid {
            display: grid;
            grid-template-columns: 360px 1fr;
            gap: 1.75rem;
            align-items: start;
            margin-top: 1rem;
        }

        .panel-card {
            background: var(--bg-card);
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            transition: box-shadow 0.2s ease;
        }

        .panel-card:hover {
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.06);
        }

        .panel-card h3 {
            margin-top: 0;
            margin-bottom: 1.25rem;
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text);
            border-left: 3px solid var(--accent);
            padding-left: 0.75rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-light);
            margin-bottom: 0.35rem;
        }

        .form-input, .form-select {
            width: 100%;
            padding: 0.65rem 0.9rem;
            border: 1px solid var(--line);
            border-radius: 12px;
            font-size: 0.9rem;
            color: var(--text);
            background: #fff;
            transition: 0.2s;
        }

        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(42, 157, 224, 0.1);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.6rem 1.2rem;
            border-radius: 40px;
            border: none;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: var(--accent);
            color: white;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .btn-primary:hover {
            background: var(--accent-dark);
            transform: translateY(-1px);
        }

        .btn-ghost {
            background: transparent;
            border: 1px solid var(--line);
            color: var(--text);
        }

        .btn-ghost:hover {
            background: var(--accent-soft);
            border-color: var(--accent);
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
            border-radius: 30px;
        }

        .users-table-wrapper {
            overflow-x: auto;
            border-radius: 16px;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        .users-table thead th {
            text-align: left;
            padding: 0.9rem 1rem;
            background: #f8fafd;
            color: var(--text);
            font-weight: 700;
            border-bottom: 1px solid var(--line);
        }

        .users-table tbody td {
            padding: 0.9rem 1rem;
            border-bottom: 1px solid var(--line);
            vertical-align: middle;
            color: var(--text-light);
        }

        .users-table tbody tr:hover {
            background: #fafcff;
        }

        .badge-role {
            display: inline-block;
            padding: 0.25rem 0.65rem;
            border-radius: 50px;
            background: var(--accent-soft);
            color: var(--accent-dark);
            font-weight: 600;
            font-size: 0.75rem;
        }

        .user-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            align-items: center;
        }

        .user-actions form {
            margin: 0;
        }

        .reset-password-input {
            width: 140px;
            padding: 0.45rem 0.7rem;
            font-size: 0.8rem;
            border-radius: 30px;
        }

        @media (max-width: 900px) {
            .settings-grid {
                grid-template-columns: 1fr;
            }

            .user-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .reset-password-input {
                width: 100%;
            }
        }

        /* Ajustes para los popovers (se mantienen igual) */
        .home-top-icons { position: relative; display: flex; gap: 0.5rem; align-items: center; justify-content: flex-end; margin-bottom: 1rem; }
        .top-popover { position: absolute; top: 45px; right: 0; background: white; border: 1px solid var(--line); border-radius: 16px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); padding: 1rem; min-width: 220px; z-index: 1000; }
        .top-popover h4 { margin: 0 0 0.5rem; }
        .mini-link { display: block; padding: 0.4rem 0; color: var(--accent); text-decoration: none; }
        .logout-mini { background: none; border: none; color: var(--danger); cursor: pointer; padding: 0.4rem 0; width: 100%; text-align: left; }
        .profile-email { font-size: 0.8rem; color: var(--muted); margin-bottom: 0.8rem; }
        .empty-state { color: var(--muted); font-size: 0.85rem; }
    </style>

    <div class="card settings-card">
        <div class="card-header">
            <div>
                <h1>Ajustes</h1>
                <p class="muted">Configuración de la aplicación y gestión de usuarios.</p>
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert-card alert-success">
                    <i class="fas fa-check-circle" style="margin-right: 8px;"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert-card alert-error">
                    <i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i> {{ session('error') }}
                </div>
            @endif

            <p class="muted" style="margin-bottom: 1.2rem;">Aquí puedes modificar los ajustes globales y gestionar los usuarios del sistema.</p>

            <section class="settings-grid">
                @if($users)
                    <div class="panel-card">
                        <h3>Crear usuario</h3>
                        <form action="{{ route('ajustes.users.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="name">Nombre completo</label>
                                <input class="form-input" id="name" name="name" placeholder="Ej: Ana Gómez" required />
                            </div>
                            <div class="form-group">
                                <label for="email">Correo electrónico</label>
                                <input class="form-input" id="email" name="email" type="email" placeholder="correo@ejemplo.com" required />
                            </div>
                            <div class="form-group">
                                <label for="password">Contraseña</label>
                                <input class="form-input" id="password" name="password" type="password" placeholder="••••••••" required />
                            </div>
                            <div class="form-group">
                                <label for="role">Rol</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="asesor">Asesor</option>
                                    <option value="supervisor">Supervisor</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>
                            <div>
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-user-plus"></i> Crear usuario
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="panel-card">
                        <h3>Usuarios existentes</h3>
                        <div class="users-table-wrapper">
                            <table class="users-table">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Rol</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $u)
                                        <tr>
                                            <td>{{ $u->name }}</td>
                                            <td>{{ $u->email }}</td>
                                            <td>
                                                <form action="{{ route('ajustes.users.role', $u->id) }}" method="POST" style="margin:0">
                                                    @csrf
                                                    <select class="form-select" name="role" onchange="this.form.submit()" style="width: auto; min-width: 120px; padding: 0.35rem 0.7rem;">
                                                        <option value="asesor" {{ $u->role === 'asesor' ? 'selected' : '' }}>Asesor</option>
                                                        <option value="supervisor" {{ $u->role === 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                                        <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Administrador</option>
                                                    </select>
                                                </form>
                                            </td>
                                            <td class="user-actions">
                                                <form action="{{ route('ajustes.users.reset', $u->id) }}" method="POST" style="display:inline-flex; gap:6px; align-items:center;">
                                                    @csrf
                                                    <input class="form-input reset-password-input" name="password" placeholder="Nueva contraseña" required />
                                                    <button class="btn btn-sm btn-ghost" type="submit">Actualizar</button>
                                                </form>

                                                <form action="{{ route('ajustes.users.send_reset', $u->id) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    <button class="btn btn-sm btn-ghost" type="submit">Enviar link</button>
                                                </form>

                                                <form action="{{ route('ajustes.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('¿Eliminar usuario permanentemente?');" style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger" type="submit">Eliminar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="panel-card">
                        <p>Solo los usuarios con rol <strong>Administrador</strong> pueden ver o gestionar usuarios.</p>
                    </div>
                @endif
            </section>
        </div>
    </div>

    <script>
        (function() {
            const btnFullscreen = document.getElementById('btn-fullscreen');
            const btnNotifications = document.getElementById('btn-notifications');
            const btnProfile = document.getElementById('btn-profile');
            const popNotifications = document.getElementById('notifications-popover');
            const popProfile = document.getElementById('profile-popover');

            function togglePopover(target) {
                const isHidden = target.hasAttribute('hidden');
                popNotifications?.setAttribute('hidden', 'hidden');
                popProfile?.setAttribute('hidden', 'hidden');
                if (isHidden) {
                    target.removeAttribute('hidden');
                }
            }

            btnFullscreen?.addEventListener('click', function() {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen();
                } else {
                    document.exitFullscreen();
                }
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
        })();
    </script>

@endsection