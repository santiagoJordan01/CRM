<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Calambas Martinez | Solicita tu crédito</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #0a5c8e;
            --primary-dark: #074269;
            --primary-light: #eef5fc;
            --accent: #f5a623;
            --gray-100: #f8fafd;
            --gray-200: #eef2f8;
            --gray-300: #dfe6ef;
            --gray-400: #cbd5e1;
            --gray-600: #64748b;
            --gray-800: #1e2c3a;
            --success: #0f7b3a;
            --success-bg: #e6f6ec;
            --error: #b91c1c;
            --error-bg: #fee2e2;
            --shadow-sm: 0 4px 12px rgba(0, 0, 0, 0.03), 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
            --shadow-lg: 0 20px 35px -12px rgba(0, 0, 0, 0.1);
            --radius: 1.25rem;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, sans-serif;
            background: linear-gradient(145deg, #f0f6fe 0%, #ffffff 100%);
            color: var(--gray-800);
            line-height: 1.5;
        }

        .container {
            max-width: 1320px;
            margin: 0 auto;
            padding: 1.5rem 1.5rem 3rem;
        }

        /* header con logo estilo CRM */
        .header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--gray-300);
        }

        /* Estilos exactos del logo tomados del layout CRM */
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

        .logo-container {
            width: 150%;
            height: 95px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-container img {
            width: 100%;
            height: auto;
            transform: scale(1.25);
            object-fit: contain;
        }

        /* Ajuste para que el logo no ocupe todo el ancho en el header */
        .logo-wrapper {
            width: 180px;
            flex-shrink: 0;
        }

        .brand-text {
            font-weight: 700;
            font-size: 1.2rem;
            letter-spacing: -0.3px;
            color: var(--primary-dark);
            white-space: nowrap;
        }

        .badge-header {
            background: var(--primary-light);
            padding: 0.5rem 1rem;
            border-radius: 100px;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--primary);
            border: 1px solid rgba(10, 92, 142, 0.15);
        }

        /* hero grid */
        .hero-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 3rem;
        }

        /* hero left */
        .hero-content {
            background: linear-gradient(135deg, var(--primary) 0%, #0b6b9e 100%);
            border-radius: var(--radius);
            padding: 2rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
        }

        .hero-content::after {
            content: '';
            position: absolute;
            top: -20%;
            right: -10%;
            width: 260px;
            height: 260px;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .hero-tag {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(4px);
            padding: 0.3rem 1rem;
            border-radius: 100px;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
        }

        .hero-content h1 {
            font-size: clamp(1.8rem, 4vw, 2.4rem);
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1rem;
        }

        .hero-content p {
            opacity: 0.9;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }

        .feature-list {
            list-style: none;
            margin-bottom: 1.5rem;
        }

        .feature-list li {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
        }

        .feature-list li::before {
            content: "✓";
            background: rgba(255,255,255,0.2);
            width: 22px;
            height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: bold;
        }

        .stats {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1rem;
        }

        .stat {
            flex: 1;
            background: rgba(255,255,255,0.1);
            border-radius: 1rem;
            padding: 0.75rem;
            text-align: center;
            backdrop-filter: blur(4px);
        }

        .stat strong {
            display: block;
            font-size: 1.3rem;
            font-weight: 800;
        }

        .stat span {
            font-size: 0.7rem;
            opacity: 0.85;
        }

        /* form card */
        .form-card {
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow-md);
            padding: 1.75rem;
            border: 1px solid var(--gray-200);
            transition: box-shadow 0.2s;
        }

        .form-card h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--primary-dark);
        }

        .form-card > p {
            color: var(--gray-600);
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .alert {
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .alert-success {
            background: var(--success-bg);
            border-left: 4px solid var(--success);
            color: var(--success);
        }

        .alert-error {
            background: var(--error-bg);
            border-left: 4px solid var(--error);
            color: var(--error);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .field-full {
            grid-column: span 2;
        }

        label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--gray-800);
        }

        input, select, textarea {
            padding: 0.7rem 0.9rem;
            border: 1px solid var(--gray-300);
            border-radius: 0.8rem;
            font-size: 0.9rem;
            transition: all 0.2s;
            background-color: #fff;
            font-family: inherit;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 92, 142, 0.15);
        }

        small {
            font-size: 0.7rem;
            color: var(--gray-600);
        }

        .btn-submit {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.85rem 1.5rem;
            border-radius: 2rem;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s;
            width: 100%;
            margin-top: 0.5rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }

        .btn-submit:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .legal-note {
            font-size: 0.7rem;
            text-align: center;
            margin-top: 1rem;
            color: var(--gray-600);
        }

        /* steps */
        .steps-section {
            background: white;
            border-radius: var(--radius);
            padding: 1.5rem;
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow-sm);
        }

        .steps-section h3 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .steps-section > p {
            color: var(--gray-600);
            margin-bottom: 1.5rem;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .step {
            background: var(--gray-100);
            padding: 1rem;
            border-radius: 1rem;
            border: 1px solid var(--gray-200);
        }

        .step-number {
            background: var(--primary);
            color: white;
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 60px;
            font-weight: bold;
            font-size: 0.8rem;
            margin-bottom: 0.75rem;
        }

        .step h4 {
            font-weight: 700;
            margin-bottom: 0.3rem;
            font-size: 1rem;
        }

        .step span {
            font-size: 0.8rem;
            color: var(--gray-600);
            line-height: 1.4;
        }

        /* responsive */
        @media (max-width: 900px) {
            .hero-grid {
                grid-template-columns: 1fr;
            }
            .container {
                padding: 1rem;
            }
            .stats {
                flex-direction: column;
            }
        }

        @media (max-width: 640px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            .field-full {
                grid-column: span 1;
            }
            .steps-grid {
                grid-template-columns: 1fr;
            }
            .header {
                flex-direction: column;
                text-align: center;
            }
            .logo-wrapper {
                width: 160px;
                margin: 0 auto;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div class="logo-wrapper">
                <a href="{{ route('landing') }}" class="logo-link" aria-label="Ir a Inicio" title="Ir a Inicio">
                    <div class="logo-container">
                        <img src="{{ asset('img/LOGO_CALAMBAS_MARTINEZ.png') }}" alt="Logo Calambas Martinez">
                    </div>
                </a>
            </div>
        </div>
        <div class="badge-header">Asesoría financiera personalizada</div>
    </div>

    <div class="hero-grid">
        <div class="hero-content">
            <div class="hero-tag">SOLICITUD DIGITAL DE CRÉDITO</div>
            <h1>Tu crédito empieza aquí</h1>
            <p>Completa tus datos y nuestro equipo de mesa de control recibirá tu solicitud para iniciar el estudio de crédito y contactarte lo antes posible.</p>
            <ul class="feature-list">
                <li>Proceso guiado para créditos de vehículo, hipotecario y más</li>
                <li>Ingreso inmediato al CRM comercial</li>
                <li>Análisis inicial y contacto por especialistas</li>
            </ul>
            <div class="stats">
                <div class="stat"><strong>100%</strong><span>Registro digital</span></div>
                <div class="stat"><strong>+24h</strong><span>Respuesta estimada</span></div>
                <div class="stat"><strong>Directo</strong><span>A mesa de control</span></div>
            </div>
        </div>

        <div class="form-card">
            <h2>Solicita tu crédito ahora</h2>
            <p>Completa el formulario y avanza en tu proceso comercial.</p>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('web-leads.store') }}">
                @csrf
                <div class="form-grid">
                    <div class="field field-full">
                        <label>Nombre completo *</label>
                        <small>Como aparece en tu documento</small>
                        <input type="text" name="nombre_cliente" value="{{ old('nombre_cliente') }}" required>
                    </div>
                    <div class="field">
                        <label>Documento</label>
                        <input type="text" name="cedula" value="{{ old('cedula') }}">
                    </div>
                    <div class="field">
                        <label>Celular de contacto *</label>
                        <input type="text" name="celular_cliente" value="{{ old('celular_cliente') }}" required>
                    </div>
                    <div class="field field-full">
                        <label>Correo electrónico *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required>
                    </div>
                    <div class="field">
                        <label>Entidad financiera *</label>
                        <select name="campania" required>
                            <option value="">Seleccione...</option>
                            @foreach($bancos as $banco)
                                <option value="{{ $banco }}" {{ old('campania') === $banco ? 'selected' : '' }}>{{ $banco }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Tipo de crédito *</label>
                        <select name="producto" required>
                            <option value="">Seleccione...</option>
                            @foreach($productos as $producto)
                                <option value="{{ $producto }}" {{ old('producto') === $producto ? 'selected' : '' }}>{{ $producto }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Canal preferido</label>
                        <select name="canal">
                            <option value="">Seleccione...</option>
                            @foreach($canales as $canal)
                                <option value="{{ $canal }}" {{ old('canal') === $canal ? 'selected' : '' }}>{{ $canal }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Género</label>
                        <select name="genero">
                            <option value="">Seleccione...</option>
                            <option value="Femenino" {{ old('genero') === 'Femenino' ? 'selected' : '' }}>Femenino</option>
                            <option value="Masculino" {{ old('genero') === 'Masculino' ? 'selected' : '' }}>Masculino</option>
                            <option value="Otro" {{ old('genero') === 'Otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>Departamento</label>
                        <select name="departamento_id" id="departamento_id">
                            <option value="">Seleccione...</option>
                            @foreach($departamentos as $departamento)
                                <option value="{{ $departamento->id }}" {{ old('departamento_id') == $departamento->id ? 'selected' : '' }}>{{ $departamento->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Municipio</label>
                        <select name="municipio_id" id="municipio_id">
                            <option value="">Seleccione un departamento primero</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>Fecha de nacimiento</label>
                        <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}">
                    </div>
                    <div class="field">
                        <label>Monto a financiar</label>
                        <input type="text" name="monto_referido" placeholder="Ej: 15000000" value="{{ old('monto_referido') }}">
                    </div>
                    <div class="field">
                        <label>Ingreso mensual aprox.</label>
                        <input type="text" name="ingreso_referido" placeholder="Ej: 3000000" value="{{ old('ingreso_referido') }}">
                    </div>
                    <div class="field field-full">
                        <label>Cuéntanos sobre tu solicitud</label>
                        <textarea name="observaciones" rows="3" placeholder="Ejemplo: crédito para vehículo, plazo 60 meses, ya tengo cuota inicial...">{{ old('observaciones') }}</textarea>
                    </div>
                </div>
                <button class="btn-submit" type="submit">Quiero solicitar mi crédito</button>
                <p class="legal-note"><strong>Autorización de contacto:</strong> Al enviar, autorizas que nuestro equipo comercial te contacte para gestionar tu solicitud de crédito.</p>
            </form>
        </div>
    </div>

    <div class="steps-section">
        <h3>¿Cómo avanza tu solicitud?</h3>
        <p>Tu información ingresa al equipo de mesa de control para clasificación y gestión comercial.</p>
        <div class="steps-grid">
            <div class="step">
                <div class="step-number">1</div>
                <h4>Registro digital</h4>
                <span>Diligencia el formulario con tus datos y necesidad de crédito.</span>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <h4>Validación interna</h4>
                <span>Mesa de control recibe y prioriza tu caso para gestión inicial.</span>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <h4>Contacto comercial</h4>
                <span>Un asesor te contacta para continuar con documentación y alternativas.</span>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        const deptSelect = document.getElementById('departamento_id');
        const munSelect = document.getElementById('municipio_id');
        const oldMunicipio = "{{ old('municipio_id') }}";

        function setPlaceholder(text) {
            munSelect.innerHTML = '';
            let opt = document.createElement('option');
            opt.value = '';
            opt.textContent = text;
            munSelect.appendChild(opt);
        }

        async function loadMunicipios(departamentoId, selectedMun) {
            if (!departamentoId) {
                setPlaceholder('Seleccione un departamento primero...');
                return;
            }
            setPlaceholder('Cargando municipios...');
            try {
                let res = await fetch(`/municipios/${departamentoId}`);
                if (!res.ok) throw new Error();
                let data = await res.json();
                munSelect.innerHTML = '';
                let placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = data.length ? 'Seleccione...' : 'Sin municipios disponibles';
                munSelect.appendChild(placeholder);
                data.forEach(m => {
                    let opt = document.createElement('option');
                    opt.value = m.id;
                    opt.textContent = m.nombre;
                    if (selectedMun && String(selectedMun) === String(m.id)) opt.selected = true;
                    munSelect.appendChild(opt);
                });
            } catch(e) {
                setPlaceholder('Error al cargar municipios');
            }
        }

        deptSelect?.addEventListener('change', function() {
            loadMunicipios(this.value, null);
        });
        if (deptSelect?.value) {
            loadMunicipios(deptSelect.value, oldMunicipio);
        }

        // --- Formato de moneda para los inputs de la landing (sin jQuery) ---
        function formatMilesConPuntos(value) {
            var digits = (value || '').toString().replace(/\D/g, '');
            if (!digits) {
                return '';
            }
            return '$' + digits.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }

        function aplicarMascaraMonedaSelector(selector) {
            var elements = document.querySelectorAll(selector);
            elements.forEach(function(el) {
                el.addEventListener('input', function() {
                    el.value = formatMilesConPuntos(el.value);
                    el.setSelectionRange(el.value.length, el.value.length);
                });
                el.value = formatMilesConPuntos(el.value);
            });
        }

        aplicarMascaraMonedaSelector('input[name="monto_referido"]');
        aplicarMascaraMonedaSelector('input[name="ingreso_referido"]');

    })();
</script>
</body>
</html>