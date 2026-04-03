@extends('layouts.crm')

@section('title', 'Registros | CRM')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@section('content')
<section class="card registros-card">
    <h1>Registros</h1>
    <p class="registros-subtitle">Completa la informacion del cliente para registrar la solicitud.</p>
     @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <form class="registros-form" action="{{ route('registros.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="registros-grid">
            <div class="registros-col">
                <div class="form-item">
                    <label for="campania">Campana:</label>
                    <select id="campania" name="campania" class="crm-select" data-placeholder="Seleccione banco" required>
                        <option></option>
                        <option>Banco AV Villas</option>
                        <option>Banco BBVA</option>
                        <option>Banco Caja social</option>
                        <option>Banco de Occidente</option>
                    </select>
                </div>

                <div class="form-item">
                    <label for="producto">Producto:</label>
                    <select id="producto" name="producto" class="crm-select" data-placeholder="Seleccione producto" required>
                        <option></option>
                    </select>
                </div>

                <div class="form-item">
                    <label for="cedula">Cedula:</label>
                    <input id="cedula" name="cedula" type="text" placeholder="Numero de documento" required />
                </div>

                <div class="form-item">
                    <label for="genero">Genero:</label>
                    <select id="genero" name="genero" class="crm-select" data-placeholder="- Seleccione -" required>
                        <option></option>
                        <option>Femenino</option>
                        <option>Masculino</option>
                        <option>Prefiero no responder</option>
                    </select>
                </div>


                <div class="form-item">
                    <label for="email">Correo electrónico:</label>
                    <input id="email" name="email" type="email" placeholder="-Correo electrónico-" required />
                </div>

                <div class="form-item">
                    <label for="departamento">Departamento Residencia Cliente:</label>
                    <select id="departamento" name="departamento_id" class="crm-select" data-placeholder="- Seleccione -" required>
                        <option value="">- Seleccione -</option>
                        @foreach($departamentos as $departamento)
                        <option value="{{ $departamento->id }}">{{ $departamento->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-item">
                    <label for="perfil">Perfil:</label>
                    <select id="perfil" name="perfil" class="crm-select" data-placeholder="- Seleccione -" required>
                        <option></option>
                        <option>Empleado</option>
                        <option>Pensionado</option>
                        <option>Rentista Capital</option>
                        <option>Transportador</option>
                        <option>Profesional independiente</option>
                        <option>Independiente</option>

                    </select>
                </div>

                <div class="form-item">
                    <label for="empresa">Empresa:</label>
                    <input id="empresa" name="empresa" type="text" placeholder="Empresa actual" required />
                </div>

                <div class="form-item">
                    <label for="fecha_vinculacion">Fecha de Vinculacion:</label>
                    <input id="fecha_vinculacion" name="fecha_vinculacion" type="date" required />
                </div>

                <div class="form-item">
                    <label for="canal">Canal:</label>
                    <select id="canal" name="canal" class="crm-select" data-placeholder="- Seleccione -" required>
                        <option></option>
                        <option>Tradicional</option>
                        <option>Digital</option>
                    </select>
                </div>

                <div class="form-item">
                    <label for="plazo">Plazo:</label>
                    <input id="plazo" name="plazo" type="number" min="0" placeholder="Meses" required />
                </div>

                <div class="form-item">
                    <label for="ingreso_principal">Ingreso principal:</label>
                    <input id="ingreso_principal" name="ingreso_principal" type="text" placeholder="$ 0" inputmode="numeric" autocomplete="off" required />
                </div>

                <div class="form-item">
                    <label for="tipo_cliente">Tipo de cliente:</label>
                    <select id="tipo_cliente" name="tipo_cliente" class="crm-select" data-placeholder="- Seleccione -" required>
                        <option></option>
                        <option>Propio</option>
                        <option>Referido Well</option>
                        <option>Base Call</option>
                        <option>Otro</option>

                    </select>
                </div>
            </div>

            <div class="registros-col">
                <div class="form-item">
                    <label for="destino">Destino:</label>
                    <select id="destino" name="destino" class="crm-select" data-placeholder="Seleccione destino" required>
                        <option></option>
                        <option>Compra de Cartera</option>
                        <option>Libre Inversión</option>
                        <option>Retanqueo</option>
                    </select>
                </div>

                <div class="form-item">
                    <label for="nombre_cliente">Nombre(s) Apellido(s) Completo Cliente:</label>
                    <input id="nombre_cliente" name="nombre_cliente" type="text" placeholder="Nombre completo" required />
                </div>

                <div class="form-item">
                    <label for="fecha_nacimiento">Fecha de nacimiento:</label>
                    <input id="fecha_nacimiento" name="fecha_nacimiento" type="date" required />
                </div>

                <div class="form-item">
                    <label for="ciudad">Ciudad Res Cliente:</label>
                    <select id="ciudad" name="ciudad" class="crm-select" data-placeholder="- Seleccione -" required>
                        <option></option>
                    </select>
                </div>

                <div class="form-item">
                    <label for="sector">Sector:</label>
                    <select id="sector" name="sector" class="crm-select" data-placeholder="- Seleccione -" required>
                        <option></option>
                        <option>Fuerzas Armadas</option>
                        <option>Pensionado</option>
                        <option>Docente</option>
                        <option>Oficial</option>
                        <option>Privado</option>

                    </select>
                </div>

                <div class="form-item">
                    <label for="nit_empresa">Nit de empresa:</label>
                    <input id="nit_empresa" name="nit_empresa" type="text" placeholder="Numero sin puntos ni guion" required />
                </div>

                <div class="form-item">
                    <label for="tipo_contrato">Tipo de contrato:</label>
                    <select id="tipo_contrato" name="tipo_contrato" class="crm-select" data-placeholder="- Seleccione -" required>
                        <option></option>
                        <option>Termino Indefinido</option>
                        <option>Termino Fijo</option>
                        <option>Obra Labor</option>
                        <option>Prestación de Servicios</option>


                        <option>Carrera Administrativa</option>
                        <option>Libre Nombramiento</option>
                        <option>En Propiedad</option>
                        <option>Provisional</option>
                        <option>Provisional Temporal</option>
                        <option>Otro</option>





                    </select>
                </div>

                <div class="form-item">
                    <label for="monto_filtrado">Monto filtrado:</label>
                    <input id="monto_filtrado" name="monto_filtrado" type="text" placeholder="$ 0" inputmode="numeric" autocomplete="off" required />
                </div>

                <div class="form-item">
                    <label for="celular_cliente">Celular Cliente:</label>
                    <input id="celular_cliente" name="celular_cliente" type="text" placeholder="10 digitos" required />
                </div>

                <div class="form-item">
                    <label for="otros_ingresos">Otros ingresos:</label>
                    <input id="otros_ingresos" name="otros_ingresos" type="text" placeholder="$ 0" required />
                </div>


                <div class="form-item">
                    <label for="observaciones">Observaciones:</label>
                    <textarea id="observaciones" name="observaciones" rows="4" class="crm-textarea" placeholder="Escriba aquí sus observaciones..." required></textarea>
                </div>


            </div>
        </div>




        <div class="attach-section">
            <h1 class="attach-title">
                <i class="fas fa-paperclip"></i> Adjuntar Archivos
            </h1>
            <p class="attach-subtitle">Adjunte soportes opcionales (máximo 3 archivos). Formatos permitidos: PDF, DOC, DOCX, JPG, JPEG, PNG, MP3, WAV, OGG, M4A, MP4, MOV, AVI, MKV, WEBM.</p>

            <div class="attach-grid">
                <!-- Soporte 1 -->
                <div class="attach-card">
                    <div class="attach-card-header">
                        <i class="fas fa-file-upload"></i>
                        <span>Soporte 1</span>
                    </div>
                    <div class="form-item attach-file">
                        <input type="file" name="soporte_1" id="soporte_1" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp3,.wav,.ogg,.m4a,.mp4,.mov,.avi,.mkv,.webm" />
                        <label for="soporte_1" class="attach-label">
                            <i class="fas fa-cloud-upload-alt"></i> Seleccionar archivo
                        </label>
                        <span class="file-name" id="file-name-1">Ningún archivo seleccionado</span>
                    </div>
                </div>

                <!-- Soporte 2 -->
                <div class="attach-card">
                    <div class="attach-card-header">
                        <i class="fas fa-file-upload"></i>
                        <span>Soporte 2</span>
                    </div>
                    <div class="form-item attach-file">
                        <input type="file" name="soporte_2" id="soporte_2" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp3,.wav,.ogg,.m4a,.mp4,.mov,.avi,.mkv,.webm" />
                        <label for="soporte_2" class="attach-label">
                            <i class="fas fa-cloud-upload-alt"></i> Seleccionar archivo
                        </label>
                        <span class="file-name" id="file-name-2">Ningún archivo seleccionado</span>
                    </div>
                </div>

                <!-- Soporte 3 -->
                <div class="attach-card">
                    <div class="attach-card-header">
                        <i class="fas fa-file-upload"></i>
                        <span>Soporte 3</span>
                    </div>
                    <div class="form-item attach-file">
                        <input type="file" name="soporte_3" id="soporte_3" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp3,.wav,.ogg,.m4a,.mp4,.mov,.avi,.mkv,.webm" />
                        <label for="soporte_3" class="attach-label">
                            <i class="fas fa-cloud-upload-alt"></i> Seleccionar archivo
                        </label>
                        <span class="file-name" id="file-name-3">Ningún archivo seleccionado</span>
                    </div>
                </div>
            </div>
        </div>
        

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Nuevo Filtro</button>
        </div>
    </form>
    

</section>

<style>
    .registros-card {
        max-width: 100%;
        border-radius: 10px;
        background: #f8fafc;
        border-color: #d6dde6;
    }

    .registros-subtitle {
        margin: 0 0 1.2rem;
        font-size: 0.95rem;
        color: #5f6b7a;
    }

    .registros-form {
        background: #eef2f6;
        border: 1px solid #d4dbe6;
        border-radius: 8px;
        padding: 1rem;
    }

    .registros-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(280px, 1fr));
        gap: 1rem 1.25rem;
    }

    .registros-col {
        display: grid;
        gap: 0.72rem;
    }

    .form-item label {
        display: block;
        margin-bottom: 0.22rem;
        font-size: 0.8rem;
        font-weight: 600;
        color: #3a4555;
    }

    .form-item input,
    .form-item select {
        width: 100%;
        height: 34px;
        border: 1px solid #c6cfdb;
        border-radius: 3px;
        background: #fdfefe;
        padding: 0 0.65rem;
        color: #1f2937;
        font-size: 0.85rem;
    }

    .form-item input:focus,
    .form-item select:focus {
        outline: none;
        border-color: #8aa2bf;
        box-shadow: 0 0 0 2px rgba(109, 139, 175, 0.14);
    }

    .form-item .select2-container {
        width: 100% !important;
    }

    .form-item .select2-container--default .select2-selection--single {
        height: 34px;
        border-radius: 3px;
        border: 1px solid #c6cfdb;
        background: #fdfefe;
    }

    .form-item .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 34px;
        padding-left: 10px;
        color: #253244;
        font-size: 0.85rem;
    }

    .form-item .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 34px;
    }

    .form-actions {
        margin-top: 1rem;
        display: flex;
        justify-content: flex-start;
        gap: 0.65rem;
        border-top: 1px solid #cccccc;
        padding-top: 10px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 36px;
        padding: 0 1rem;
        border-radius: 6px;
        border: 1px solid transparent;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
    }

    .btn-primary {
        background: #0f67b7;
        border-color: #0f67b7;
        color: #fff;
    }

    .btn-primary:hover {
        background: #0d5aa0;
    }

    .btn-secondary {
        background: #e5e9f0;
        border-color: #ccd4df;
        color: #253244;
    }

    /* Estilo para el textarea de observaciones */
    .form-item textarea {
        width: 100%;
        border: 1px solid #c6cfdb;
        border-radius: 3px;
        background: #fdfefe;
        padding: 0.5rem 0.65rem;
        color: #1f2937;
        font-size: 0.85rem;
        resize: vertical;
        /* permite que el usuario ajuste la altura verticalmente */
        font-family: inherit;
        /* mantiene la misma fuente del formulario */
    }

    .form-item textarea:focus {
        outline: none;
        border-color: #8aa2bf;
        box-shadow: 0 0 0 2px rgba(109, 139, 175, 0.14);
    }

    .attach-section {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 1.5rem;
        border: 1px solid #e2e8f0;
    }

    .attach-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }

    .attach-title i {
        color: #0f67b7;
        margin-right: 0.5rem;
    }

    .attach-subtitle {
        color: #5f6b7a;
        font-size: 0.85rem;
        margin-bottom: 1.5rem;
        border-left: 3px solid #0f67b7;
        padding-left: 0.75rem;
    }

    .attach-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.25rem;
    }

    .attach-card {
        background: white;
        border-radius: 10px;
        border: 1px solid #e2edf2;
        padding: 1rem;
        transition: box-shadow 0.2s ease;
    }

    .attach-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .attach-card-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        color: #0f67b7;
        margin-bottom: 0.75rem;
        border-bottom: 1px dashed #e2edf2;
        padding-bottom: 0.5rem;
    }

    .attach-card-header i {
        font-size: 1.2rem;
    }

    .attach-file {
        position: relative;
    }

    .attach-file input[type="file"] {
        position: absolute;
        width: 0.1px;
        height: 0.1px;
        opacity: 0;
        overflow: hidden;
        z-index: -1;
    }

    .attach-label {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #eef2ff;
        color: #0f67b7;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s;
        border: 1px solid #cbd5e1;
    }

    .attach-label:hover {
        background: #e0e7ff;
    }

    .file-name {
        display: block;
        margin-top: 0.5rem;
        font-size: 0.75rem;
        color: #5f6b7a;
        word-break: break-all;
    }

    @media (max-width: 1024px) {
        .registros-grid {
            grid-template-columns: 1fr;
        }

        .form-actions {
            justify-content: stretch;
        }

        .form-actions .btn {
            flex: 1;
        }
    }
</style>

<script>
    $(document).ready(function() {
        var defaultOptions = ['Libranza', 'Consumo', 'Hipotecario', 'Tarjeta de credito'];

        function initSearchableSelect($element, placeholderText) {
            $element.select2({
                placeholder: placeholderText,
                allowClear: true,
                width: '100%'
            });
        }

        function setProductOptions(options) {
            var $producto = $('#producto');

            if ($producto.hasClass('select2-hidden-accessible')) {
                $producto.select2('destroy');
            }

            $producto.empty().append($('<option>'));
            options.forEach(function(optionLabel) {
                $producto.append($('<option>').text(optionLabel).val(optionLabel));
            });

            initSearchableSelect($producto, 'Seleccione producto');
        }

        function updateProductsForBank(bankText) {
            var normalized = (bankText || '').toLowerCase();
            var options = defaultOptions;

            if (normalized.indexOf('av villas') !== -1 || normalized.indexOf('avvillas') !== -1) {
                options = ['Consumo', 'Hipotecario', 'Libranza', 'Tarjeta de credito'];
            } else if (normalized.indexOf('caja') !== -1 && normalized.indexOf('social') !== -1) {
                options = ['Vehiculos', 'Hipotecario'];
            } else if (normalized.indexOf('occidente') !== -1) {
                options = ['Vehiculos'];
            } else if (normalized.indexOf('bbva') !== -1) {
                options = ['Vehiculos'];
            }

            setProductOptions(options);
        }

        $('.crm-select').not('#producto').each(function() {
            var placeholderText = $(this).data('placeholder') || '- Seleccione -';
            initSearchableSelect($(this), placeholderText);
        });

        setProductOptions(defaultOptions);

        $('#campania').on('change', function() {
            updateProductsForBank($(this).val());
        });

        // Cargar municipios dinámicamente cuando cambie el departamento
        $('#departamento').on('change', function() {
            var departamentoId = $(this).val();
            var $ciudad = $('#ciudad');

            if (!departamentoId) {
                if ($ciudad.hasClass('select2-hidden-accessible')) {
                    $ciudad.select2('destroy');
                }
                $ciudad.empty().append($('<option>'));
                initSearchableSelect($ciudad, '- Seleccione -');
                return;
            }

            var municipiosUrl = "{{ url('municipios') }}";
            $.getJSON(municipiosUrl + '/' + departamentoId, function(data) {
                if ($ciudad.hasClass('select2-hidden-accessible')) {
                    $ciudad.select2('destroy');
                }
                $ciudad.empty().append($('<option>'));
                $.each(data, function(i, item) {
                    $ciudad.append($('<option>').val(item.id).text(item.nombre));
                });
                initSearchableSelect($ciudad, '- Seleccione -');
            }).fail(function() {
                console.error('No se pudieron cargar los municipios');
            });
        });

        // Evitar envío accidental del formulario al presionar Enter en la fecha
        $('#fecha_vinculacion').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                return false;
            }
        });

        function formatMilesConPuntos(value) {
            var digits = (value || '').toString().replace(/\D/g, '');
            if (!digits) {
                return '';
            }
            return digits.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }

        function aplicarMascaraMoneda(selector) {
            $(selector).on('input', function() {
                this.value = formatMilesConPuntos(this.value);
            });

            // Si viene con valor desde backend, lo normaliza al cargar
            $(selector).val(formatMilesConPuntos($(selector).val()));
        }

        aplicarMascaraMoneda('#ingreso_principal');
        aplicarMascaraMoneda('#monto_filtrado');
        aplicarMascaraMoneda('#otros_ingresos');

    });

    // Mostrar nombre del archivo seleccionado
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function(e) {
            const fileId = this.id;
            const spanId = fileId.replace('soporte_', 'file-name-');
            const fileNameSpan = document.getElementById(spanId);
            if (fileNameSpan) {
                if (this.files.length > 0) {
                    fileNameSpan.textContent = this.files[0].name;
                } else {
                    fileNameSpan.textContent = 'Ningún archivo seleccionado';
                }
            }
        });
    });
</script>
@endsection