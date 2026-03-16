<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CRM Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --panel-bg: #f4f5f6;
            --text-main: #2d3748;
            --text-soft: #718096;
            --line: #dde1e6;
            --brand-dark: #002b26;
            --brand-lime: #b8d000;
            --btn: #1f8dd6;
            --btn-hover: #177ab9;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Outfit', sans-serif;
            color: var(--text-main);
            background-image: linear-gradient(to right, rgba(0, 0, 0, 0.18), rgba(0, 0, 0, 0.2)), url('/img/wallpaper_login.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            overflow-x: hidden;
        }

        .layout {
            min-height: 100vh;
            display: flex;
        }

        .left-panel {
            width: min(470px, 100%);
            background: var(--panel-bg);
            border-right: 1px solid #d7d9dd;
            padding: 30px 38px 28px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            backdrop-filter: blur(1px);
        }

        .home-btn {
            position: fixed;
            top: 20px;
            right: 26px;
            width: 44px;
            height: 44px;
            border: none;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.22);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            text-decoration: none;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
            transition: transform .2s ease, background-color .2s ease;
        }

        .home-btn:hover {
            background: rgba(255, 255, 255, 0.34);
            transform: translateY(-1px);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 74px;
            font-weight: 800;
            letter-spacing: -2px;
            color: var(--brand-dark);
            line-height: 0.9;
            margin-bottom: 22px;
        }

        .logo-mark {
            width: 40px;
            height: 34px;
            background: linear-gradient(145deg, var(--brand-lime), #90aa00);
            clip-path: polygon(0 0, 55% 0, 38% 100%, 0 100%, 17% 46%);
            transform: skewX(-10deg);
            margin-right: -3px;
        }

        .section-title {
            border-top: 1px solid var(--line);
            padding-top: 20px;
            margin-bottom: 26px;
            text-align: center;
        }

        .section-title h1 {
            margin: 0;
            font-size: 40px;
            color: #505966;
            font-weight: 700;
            letter-spacing: .3px;
        }

        .welcome {
            margin-bottom: 30px;
        }

        .welcome h2 {
            margin: 0;
            font-size: 38px;
            font-weight: 700;
            color: #4a5568;
            line-height: 1.1;
        }

        .welcome p {
            margin-top: 6px;
            color: var(--text-soft);
            font-size: 30px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-size: 34px;
            font-weight: 600;
            margin-bottom: 7px;
            color: #3e4755;
        }

        input {
            width: 100%;
            border: 1px solid #cfd5dc;
            border-radius: 8px;
            padding: 11px 13px;
            font-size: 28px;
            outline: none;
            font-family: inherit;
            color: #2f3744;
            background-color: #fff;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        input:focus {
            border-color: #3da4e6;
            box-shadow: 0 0 0 3px rgba(61, 164, 230, 0.15);
        }

        input::placeholder {
            color: #a0a7b2;
        }

        .submit-wrap {
            display: flex;
            justify-content: flex-end;
            margin-top: 22px;
        }

        .btn-login {
            border: none;
            border-radius: 8px;
            background: var(--btn);
            color: #fff;
            font-family: inherit;
            font-size: 33px;
            font-weight: 600;
            padding: 9px 26px;
            min-width: 170px;
            cursor: pointer;
            transition: background-color .2s ease, transform .2s ease;
        }

        .btn-login:hover {
            background: var(--btn-hover);
            transform: translateY(-1px);
        }

        .error-box {
            margin-bottom: 16px;
            border: 1px solid #f3c4c4;
            background: #ffe9e9;
            color: #942b2b;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 22px;
        }

        .footer {
            text-align: center;
            color: #2285e4;
            font-size: 30px;
            font-weight: 700;
            margin-top: 34px;
        }

        @media (max-width: 1200px) {
            .left-panel {
                width: 420px;
                padding: 24px;
            }

            .logo {
                font-size: 62px;
            }

            .section-title h1 {
                font-size: 34px;
            }

            .welcome h2 {
                font-size: 30px;
            }

            .welcome p {
                font-size: 24px;
            }

            label {
                font-size: 26px;
            }

            input {
                font-size: 22px;
            }

            .btn-login {
                font-size: 24px;
            }

            .footer {
                font-size: 24px;
            }
        }

        @media (max-width: 900px) {
            .layout {
                justify-content: center;
                padding: 24px 16px;
            }

            .left-panel {
                width: 100%;
                max-width: 520px;
                border-radius: 20px;
                border: 1px solid rgba(255, 255, 255, 0.5);
                box-shadow: 0 24px 42px rgba(0, 0, 0, 0.25);
                background: rgba(244, 245, 246, 0.95);
                min-height: auto;
            }

            .home-btn {
                top: 14px;
                right: 14px;
            }
        }
    </style>
</head>

<body>
    <!-- <a class="home-btn" href="{{ url('/') }}" aria-label="Volver al inicio" title="Inicio">
        <svg width="25" height="25" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M12 3.2 3 10v10h6.5v-6.3h5V20H21V10l-9-6.8Zm0 2.5L19 11v7h-2.5v-6.3h-9V18H5v-7l7-5.3Z" />
        </svg>
    </a> -->

    <main class="layout">
        <section class="left-panel">
            <div>
                <div class="logo">
                    <!-- <span class="logo-mark" aria-hidden="true"></span> -->
                    <span>KC</span>
                </div>

                <div class="section-title">
                    <h1>CRM - LOGIN</h1>
                </div>

                <div class="welcome">
                    <h2>Bienvenido</h2>
                    <p>Inicia sesion.</p>
                </div>

                @if (session('error'))
                    <div class="error-box">{{ session('error') }}</div>
                @endif

                @if ($errors->any())
                    <div class="error-box">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" autocomplete="off">
                    @csrf

                    <div class="form-group">
                        <label for="email">Nombre de Usuario</label>
                        <input id="email" type="text" name="email" value="{{ old('email') }}" placeholder="Ingrese Usuario" required autofocus>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password" placeholder="Ingrese password" required>
                    </div>

                    <div class="submit-wrap">
                        <button class="btn-login" type="submit">Ingresar</button>
                    </div>
                </form>
            </div>

            <p class="footer">&copy; {{ date('Y') }} CRM</p>
        </section>
    </main>
</body>

</html>