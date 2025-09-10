<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Huéspedes</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* ============================================
           ESTILOS PARA EL MÓDULO DE HUÉSPEDES
           ============================================ */

        /* Variables CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #83b3fa 0%, #b7d0f7 100%) !important;
            min-height: 100vh;
            color: #333;
            padding-top: 20px;
        }

        :root {
            --huespedes-primary: #0d6efd;
            --huespedes-success: #198754;
            --huespedes-danger: #dc3545;
            --huespedes-warning: #ffc107;
            --huespedes-info: #0dcaf0;
            --huespedes-light: #f8f9fa;
            --huespedes-dark: #212529;
            --huespedes-border: #dee2e6;
            --huespedes-border-radius: 8px;
            --huespedes-shadow: 0 2px 4px rgba(0,0,0,0.1);
            --huespedes-shadow-hover: 0 4px 8px rgba(0,0,0,0.15);
        }

        /* ============================================
           CONTENEDOR PRINCIPAL
           ============================================ */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* ============================================
           HEADER DE PÁGINAS
           ============================================ */
        .header {
            text-align: center;
            margin-bottom: 2rem;
            padding: 2rem 0;
            background: linear-gradient(135deg, var(--huespedes-primary), #4c84ff);
            color: white;
            border-radius: var(--huespedes-border-radius);
            box-shadow: var(--huespedes-shadow);
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0 0 0.5rem 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .header p {
            font-size: 1.1rem;
            margin: 0;
            opacity: 0.9;
        }

        /* ============================================
           NAVEGACIÓN DE PESTAÑAS
           ============================================ */
        .nav-tabs-container {
            background: white;
            border-radius: var(--huespedes-border-radius);
            box-shadow: var(--huespedes-shadow);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .nav-tabs .nav-link {
            border: none;
            border-radius: 0;
            padding: 1rem 1.5rem;
            font-weight: 600;
            color: var(--huespedes-dark);
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-tabs .nav-link:hover {
            background: rgba(13, 110, 253, 0.1);
            color: var(--huespedes-primary);
        }

        .nav-tabs .nav-link.active {
            background: var(--huespedes-primary);
            color: white;
            border-bottom: 3px solid #0056b3;
        }

        /* ============================================
           SECCIÓN DE BÚSQUEDA Y FILTROS
           ============================================ */
        .search-section {
            background: white;
            padding: 1.5rem;
            border-radius: var(--huespedes-border-radius);
            box-shadow: var(--huespedes-shadow);
            margin-bottom: 2rem;
        }

        .search-section .input-group {
            margin-bottom: 1rem;
        }

        /* ============================================
           FORMULARIO DE REGISTRO
           ============================================ */
        .form-section {
            background: white;
            border-radius: var(--huespedes-border-radius);
            box-shadow: var(--huespedes-shadow);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .form-title {
            background: linear-gradient(135deg, var(--huespedes-success), #20c997);
            color: white;
            padding: 1.5rem 2rem;
            margin: 0 0 2rem 0;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .form-title i {
            font-size: 1.75rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            padding: 0 2rem;
            margin-bottom: 2rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--huespedes-dark);
            font-size: 0.95rem;
        }

        .form-group .required {
            color: var(--huespedes-danger);
            font-weight: 700;
        }

        .form-group input,
        .form-group select {
            padding: 0.75rem;
            border: 2px solid var(--huespedes-border);
            border-radius: var(--huespedes-border-radius);
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--huespedes-primary);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .form-group input.valid,
        .form-group select.valid {
            border-color: var(--huespedes-success);
            background: #f8fff9;
        }

        .form-group input.invalid,
        .form-group select.invalid {
            border-color: var(--huespedes-danger);
            background: #fff8f8;
        }

        .form-text {
            font-size: 0.875rem;
            margin-top: 0.25rem;
            color: #6c757d;
        }

        /* ============================================
           BOTONES DE ACCIÓN
           ============================================ */
        .form-actions {
            padding: 2rem;
            background: var(--huespedes-light);
            border-top: 1px solid var(--huespedes-border);
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: var(--huespedes-border-radius);
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--huespedes-shadow-hover);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        /* ============================================
           TABLA DE HUÉSPEDES
           ============================================ */
        .table-responsive {
            background: white;
            border-radius: var(--huespedes-border-radius);
            box-shadow: var(--huespedes-shadow);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .table {
            margin: 0;
            font-size: 0.9rem;
        }

        .table thead th {
            background: linear-gradient(135deg, #495057, #6c757d);
            color: white;
            border: none;
            padding: 1rem 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background: rgba(13, 110, 253, 0.05);
        }

        .table tbody td {
            padding: 0.75rem;
            border-top: 1px solid #f1f3f4;
            vertical-align: middle;
        }

        /* ============================================
           BADGES Y ESTADOS
           ============================================ */
        .badge {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-genero {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .badge-documento {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            color: white;
        }

        /* ============================================
           MENSAJES
           ============================================ */
        .success-message,
        .error-message {
            padding: 1rem 1.5rem;
            border-radius: var(--huespedes-border-radius);
            margin-bottom: 2rem;
            font-size: 1rem;
            box-shadow: var(--huespedes-shadow);
            display: none;
        }

        .success-message {
            background: linear-gradient(135deg, #d1edff, #e8f5e8);
            border: 1px solid var(--huespedes-success);
            color: #155724;
        }

        .error-message {
            background: linear-gradient(135deg, #ffeaa7, #fab1a0);
            border: 1px solid var(--huespedes-danger);
            color: #721c24;
        }

        /* ============================================
           MODAL DETALLES
           ============================================ */
        .modal-content {
            border-radius: var(--huespedes-border-radius);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            border: none;
        }

        .modal-header {
            border-bottom: 1px solid var(--huespedes-border);
            background: linear-gradient(135deg, var(--huespedes-light), white);
        }

        .modal-title {
            font-weight: 600;
            color: var(--huespedes-dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .info-item {
            display: flex;
            margin-bottom: 1rem;
            align-items: flex-start;
            gap: 1rem;
            padding: 0.5rem;
            border-radius: 6px;
            background: #f8f9fa;
        }

        .info-label {
            font-weight: 600;
            min-width: 140px;
            color: var(--huespedes-dark);
        }

        .info-value {
            flex: 1;
            word-break: break-word;
        }

        /* ============================================
           RESPONSIVE DESIGN
           ============================================ */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
                padding: 0 1rem;
            }
            
            .form-actions {
                flex-direction: column;
                align-items: stretch;
            }
            
            .btn {
                justify-content: center;
            }
            
            .search-section .row {
                flex-direction: column;
            }
        }

        /* ============================================
           ANIMACIONES
           ============================================ */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideIn {
            from { transform: translateX(-20px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .container > * {
            animation: fadeIn 0.6s ease forwards;
        }

        .success-message,
        .error-message {
            animation: slideIn 0.4s ease forwards;
        }

        /* ============================================
           UTILIDADES ADICIONALES
           ============================================ */
        .text-truncate-custom {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .shadow-sm {
            box-shadow: var(--huespedes-shadow) !important;
        }

        .shadow-hover:hover {
            box-shadow: var(--huespedes-shadow-hover) !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-users"></i> Sistema de Gestión de Huéspedes</h1>
            <p>Registro y administración integral de huéspedes del hotel</p>
        </div>

        <!-- Mensajes de estado -->
        <div id="success-message" class="success-message">
            <i class="fas fa-check-circle"></i> <span id="success-text"></span>
        </div>
        <div id="error-message" class="error-message">
            <i class="fas fa-exclamation-triangle"></i> <span id="error-text"></span>
        </div>

        <!-- Navegación por pestañas -->
        <div class="nav-tabs-container">
            <ul class="nav nav-tabs" id="mainTabs">
                <li class="nav-item">
                    <button class="nav-link active" id="registro-tab" data-bs-toggle="tab" data-bs-target="#registro">
                        <i class="fas fa-user-plus"></i> Registrar Huésped
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="listado-tab" data-bs-toggle="tab" data-bs-target="#listado">
                        <i class="fas fa-list"></i> Lista de Huéspedes
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="busqueda-tab" data-bs-toggle="tab" data-bs-target="#busqueda">
                        <i class="fas fa-search"></i> Búsqueda Avanzada
                    </button>
                </li>
            </ul>
        </div>

        <!-- Contenido de las pestañas -->
        <div class="tab-content" id="mainTabContent">
            
            <!-- Pestaña de Registro -->
            <div class="tab-pane fade show active" id="registro">
                <div class="form-section">
                    <div class="form-title">
                        <i class="fas fa-user-plus"></i>
                        Registro de Nuevo Huésped
                    </div>
                    
                    <form id="form-huesped">
                        <div class="form-grid">
                            <!-- Información Personal -->
                            <div class="form-group">
                                <label for="nombres">Nombres <span class="required">*</span></label>
                                <input type="text" id="nombres" name="nombres" class="form-control" required maxlength="50">
                                <div class="form-text">Ingrese los nombres completos</div>
                            </div>

                            <div class="form-group">
                                <label for="apellidos">Apellidos <span class="required">*</span></label>
                                <input type="text" id="apellidos" name="apellidos" class="form-control" required maxlength="50">
                                <div class="form-text">Ingrese los apellidos completos</div>
                            </div>

                            <div class="form-group">
                                <label for="tipoDocumento">Tipo de Documento <span class="required">*</span></label>
                                <select id="tipoDocumento" name="tipoDocumento" class="form-control" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="Cedula de Ciudadanía">Cédula de Ciudadanía</option>
                                    <option value="Tarjeta de Identidad">Tarjeta de Identidad</option>
                                    <option value="Cedula de Extranjeria">Cédula de Extranjería</option>
                                    <option value="Pasaporte">Pasaporte</option>
                                    <option value="Registro Civil">Registro Civil</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="numDocumento">Número de Documento <span class="required">*</span></label>
                                <input type="text" id="numDocumento" name="numDocumento" class="form-control" required maxlength="15">
                                <div class="form-text">Documento único de identificación</div>
                            </div>

                            <div class="form-group">
                                <label for="sexo">Sexo <span class="required">*</span></label>
                                <select id="sexo" name="sexo" class="form-control" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="Hombre">Hombre</option>
                                    <option value="Mujer">Mujer</option>
                                    <option value="Otro">Otro</option>
                                    <option value="Prefiero no decirlo">Prefiero no decirlo</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="numTelefono">Teléfono <span class="required">*</span></label>
                                <input type="tel" id="numTelefono" name="numTelefono" class="form-control" required maxlength="15">
                                <div class="form-text">Número de contacto principal</div>
                            </div>

                            <div class="form-group full-width">
                                <label for="correo">Correo Electrónico <span class="required">*</span></label>
                                <input type="email" id="correo" name="correo" class="form-control" required maxlength="255">
                                <div class="form-text">Dirección de correo electrónico válida</div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Registrar Huésped
                            </button>
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="fas fa-undo"></i> Limpiar Formulario
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Pestaña de Listado -->
            <div class="tab-pane fade" id="listado">
                <div class="search-section">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" id="filtro-busqueda" 
                                       placeholder="Buscar por documento, nombre, apellido o correo...">
                                <button class="btn btn-outline-primary" type="button" onclick="filtrarHuespedes()">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select id="filtro-tipo-doc" class="form-control" onchange="filtrarHuespedes()">
                                <option value="">Todos los tipos de documento</option>
                                <option value="Cedula de Ciudadanía">Cédula de Ciudadanía</option>
                                <option value="Tarjeta de Identidad">Tarjeta de Identidad</option>
                                <option value="Cedula de Extranjeria">Cédula de Extranjería</option>
                                <option value="Pasaporte">Pasaporte</option>
                                <option value="Registro Civil">Registro Civil</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="filtro-sexo" class="form-control" onchange="filtrarHuespedes()">
                                <option value="">Todos los géneros</option>
                                <option value="Hombre">Hombre</option>
                                <option value="Mujer">Mujer</option>
                                <option value="Otro">Otro</option>
                                <option value="Prefiero no decirlo">Prefiero no decirlo</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Documento</th>
                                <th>Nombres</th>
                                <th>Apellidos</th>
                                <th>Tipo Doc.</th>
                                <th>Sexo</th>
                                <th>Teléfono</th>
                                <th>Correo</th>
                                <th>Fecha Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-huespedes">
                            <tr>
                                <td colspan="9" class="text-center">
                                    <div class="loading">
                                        <i class="fas fa-spinner fa-spin"></i> Cargando huéspedes...
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pestaña de Búsqueda Avanzada -->
            <div class="tab-pane fade" id="busqueda">
                <div class="search-section">
                    <h4 class="mb-3"><i class="fas fa-filter"></i> Búsqueda Avanzada</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Rango de fechas de registro</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="date" id="fecha-desde" class="form-control" placeholder="Desde">
                                </div>
                                <div class="col-6">
                                    <input type="date" id="fecha-hasta" class="form-control" placeholder="Hasta">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Dominio de correo</label>
                            <input type="text" id="dominio-correo" class="form-control" placeholder="@gmail.com">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-info w-100" onclick="busquedaAvanzada()">
                                <i class="fas fa-search-plus"></i> Buscar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ver detalles -->
    <div class="modal fade" id="modal-detalles" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user"></i> Detalles del Huésped
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modal-detalles-content">
                    <!-- Contenido se carga dinámicamente -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-warning" onclick="editarHuesped()">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simulación de base de datos en memoria
        let huespedes = [];
        let huespedsSeleccionado = null;

        // Datos de ejemplo
        const datosEjemplo = [
            {
                numDocumento: '12345678',
                numTelefono: '3001234567',
                correo: 'juan.perez@gmail.com',
                nombres: 'Juan Carlos',
                apellidos: 'Pérez González',
                tipoDocumento: 'Cedula de Ciudadanía',
                sexo: 'Hombre',
                fechaCreacion: new Date('2024-01-15'),
                fechaActualizacion: new Date('2024-01-15')
            },
            {
                numDocumento: '87654321',
                numTelefono: '3109876543',
                correo: 'maria.rodriguez@hotmail.com',
                nombres: 'María Elena',
                apellidos: 'Rodríguez Silva',
                tipoDocumento: 'Cedula de Ciudadanía',
                sexo: 'Mujer',
                fechaCreacion: new Date('2024-02-10'),
                fechaActualizacion: new Date('2024-02-10')
            },
            {
                numDocumento: 'P123456789',
                numTelefono: '3205551234',
                correo: 'john.smith@yahoo.com',
                nombres: 'John Michael',
                apellidos: 'Smith Johnson',
                tipoDocumento: 'Pasaporte',
                sexo: 'Hombre',
                fechaCreacion: new Date('2024-03-05'),
                fechaActualizacion: new Date('2024-03-05')
            }
        ];

        // Inicializar datos de ejemplo
        huespedes = [...datosEjemplo];

        // Función para mostrar mensajes
        function mostrarMensaje(tipo, mensaje) {
            const elementoMensaje = document.getElementById(tipo + '-message');
            const textoMensaje = document.getElementById(tipo + '-text');
            
            textoMensaje.textContent = mensaje;
            elementoMensaje.style.display = 'block';
            
            setTimeout(() => {
                elementoMensaje.style.display = 'none';
            }, 5000);
        }

        // Validación del formulario
        function validarFormulario() {
            const campos = ['nombres', 'apellidos', 'tipoDocumento', 'numDocumento', 'sexo', 'numTelefono', 'correo'];
            let valido = true;

            campos.forEach(campo => {
                const elemento = document.getElementById(campo);
                if (!elemento.value.trim()) {
                    elemento.classList.add('invalid');
                    elemento.classList.remove('valid');
                    valido = false;
                } else {
                    elemento.classList.add('valid');
                    elemento.classList.remove('invalid');
                }
            });

            // Validación específica de correo
            const correo = document.getElementById('correo');
            const regexCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!regexCorreo.test(correo.value)) {
                correo.classList.add('invalid');
                correo.classList.remove('valid');
                valido = false;
            }

            // Verificar duplicados
            const numDoc = document.getElementById('numDocumento').value;
            const correoVal = document.getElementById('correo').value;
            
            if (huespedes.some(h => h.numDocumento === numDoc)) {
                document.getElementById('numDocumento').classList.add('invalid');
                mostrarMensaje('error', 'Ya existe un huésped con este número de documento');
                valido = false;
            }
            
            if (huespedes.some(h => h.correo === correoVal)) {
                document.getElementById('correo').classList.add('invalid');
                mostrarMensaje('error', 'Ya existe un huésped con este correo electrónico');
                valido = false;
            }

            return valido;
        }

        // Función para registrar huésped
        function registrarHuesped(evento) {
            evento.preventDefault();
            
            if (!validarFormulario()) {
                return false;
            }

            const formData = new FormData(evento.target);
            const nuevoHuesped = {
                numDocumento: formData.get('numDocumento'),
                numTelefono: formData.get('numTelefono'),
                correo: formData.get('correo'),
                nombres: formData.get('nombres'),
                apellidos: formData.get('apellidos'),
                tipoDocumento: formData.get('tipoDocumento'),
                sexo: formData.get('sexo'),
                fechaCreacion: new Date(),
                fechaActualizacion: new Date()
            };

            huespedes.push(nuevoHuesped);
            mostrarMensaje('success', `Huésped ${nuevoHuesped.nombres} ${nuevoHuesped.apellidos} registrado exitosamente`);
            
            // Limpiar formulario
            evento.target.reset();
            document.querySelectorAll('.form-control').forEach(el => {
                el.classList.remove('valid', 'invalid');
            });

            // Actualizar tabla si está visible
            if (document.getElementById('listado').classList.contains('active')) {
                cargarTablaHuespedes();
            }
        }

        // Función para cargar la tabla de huéspedes
        function cargarTablaHuespedes(lista = huespedes) {
            const tbody = document.getElementById('tabla-huespedes');
            
            if (lista.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center text-muted">
                            <i class="fas fa-users"></i> No se encontraron huéspedes
                        </td>
                    </tr>`;
                return;
            }

            tbody.innerHTML = lista.map(huesped => `
                <tr>
                    <td><strong>${huesped.numDocumento}</strong></td>
                    <td>${huesped.nombres}</td>
                    <td>${huesped.apellidos}</td>
                    <td>
                        <span class="badge badge-documento">
                            ${huesped.tipoDocumento}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-genero">
                            ${huesped.sexo}
                        </span>
                    </td>
                    <td>
                        <a href="tel:${huesped.numTelefono}" class="text-decoration-none">
                            <i class="fas fa-phone"></i> ${huesped.numTelefono}
                        </a>
                    </td>
                    <td class="text-truncate-custom" title="${huesped.correo}">
                        <a href="mailto:${huesped.correo}" class="text-decoration-none">
                            <i class="fas fa-envelope"></i> ${huesped.correo}
                        </a>
                    </td>
                    <td>
                        <small class="text-muted">
                            ${huesped.fechaCreacion.toLocaleDateString('es-CO')}
                        </small>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-info" onclick="verDetalles('${huesped.numDocumento}')" 
                                    title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning" onclick="editarHuesped('${huesped.numDocumento}')" 
                                    title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger" onclick="eliminarHuesped('${huesped.numDocumento}')" 
                                    title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // Función para filtrar huéspedes
        function filtrarHuespedes() {
            const textoBusqueda = document.getElementById('filtro-busqueda').value.toLowerCase();
            const tipoDoc = document.getElementById('filtro-tipo-doc').value;
            const sexo = document.getElementById('filtro-sexo').value;

            const huespedesFiltrados = huespedes.filter(huesped => {
                const coincideTexto = !textoBusqueda || 
                    huesped.numDocumento.toLowerCase().includes(textoBusqueda) ||
                    huesped.nombres.toLowerCase().includes(textoBusqueda) ||
                    huesped.apellidos.toLowerCase().includes(textoBusqueda) ||
                    huesped.correo.toLowerCase().includes(textoBusqueda);

                const coincideTipoDoc = !tipoDoc || huesped.tipoDocumento === tipoDoc;
                const coincideSexo = !sexo || huesped.sexo === sexo;

                return coincideTexto && coincideTipoDoc && coincideSexo;
            });

            cargarTablaHuespedes(huespedesFiltrados);
        }

        // Función para búsqueda avanzada
        function busquedaAvanzada() {
            const fechaDesde = document.getElementById('fecha-desde').value;
            const fechaHasta = document.getElementById('fecha-hasta').value;
            const dominioCorreo = document.getElementById('dominio-correo').value;

            let huespedesFiltrados = [...huespedes];

            if (fechaDesde) {
                const fechaDesdeObj = new Date(fechaDesde);
                huespedesFiltrados = huespedesFiltrados.filter(h => h.fechaCreacion >= fechaDesdeObj);
            }

            if (fechaHasta) {
                const fechaHastaObj = new Date(fechaHasta);
                fechaHastaObj.setHours(23, 59, 59, 999); // Final del día
                huespedesFiltrados = huespedesFiltrados.filter(h => h.fechaCreacion <= fechaHastaObj);
            }

            if (dominioCorreo) {
                huespedesFiltrados = huespedesFiltrados.filter(h => 
                    h.correo.toLowerCase().includes(dominioCorreo.toLowerCase())
                );
            }

            // Cambiar a la pestaña de listado y mostrar resultados
            const tabListado = new bootstrap.Tab(document.getElementById('listado-tab'));
            tabListado.show();
            
            cargarTablaHuespedes(huespedesFiltrados);
            
            if (huespedesFiltrados.length === 0) {
                mostrarMensaje('error', 'No se encontraron huéspedes que coincidan con los criterios de búsqueda');
            } else {
                mostrarMensaje('success', `Se encontraron ${huespedesFiltrados.length} huésped(es) que coinciden con la búsqueda`);
            }
        }

        // Función para ver detalles
        function verDetalles(numDocumento) {
            const huesped = huespedes.find(h => h.numDocumento === numDocumento);
            if (!huesped) return;

            huespedsSeleccionado = huesped;
            
            const modalContent = document.getElementById('modal-detalles-content');
            modalContent.innerHTML = `
                <div class="row g-0">
                    <div class="col-12">
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-id-card"></i> Documento:</div>
                            <div class="info-value">
                                <strong>${huesped.numDocumento}</strong>
                                <br><small class="text-muted">${huesped.tipoDocumento}</small>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-user"></i> Nombre Completo:</div>
                            <div class="info-value">
                                <strong>${huesped.nombres} ${huesped.apellidos}</strong>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-venus-mars"></i> Sexo:</div>
                            <div class="info-value">
                                <span class="badge badge-genero">${huesped.sexo}</span>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-phone"></i> Teléfono:</div>
                            <div class="info-value">
                                <a href="tel:${huesped.numTelefono}" class="text-decoration-none">
                                    ${huesped.numTelefono}
                                </a>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-envelope"></i> Correo:</div>
                            <div class="info-value">
                                <a href="mailto:${huesped.correo}" class="text-decoration-none">
                                    ${huesped.correo}
                                </a>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-calendar-plus"></i> Fecha de Registro:</div>
                            <div class="info-value">
                                ${huesped.fechaCreacion.toLocaleDateString('es-CO', {
                                    weekday: 'long',
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                })}
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-calendar-edit"></i> Última Actualización:</div>
                            <div class="info-value">
                                ${huesped.fechaActualizacion.toLocaleDateString('es-CO', {
                                    weekday: 'long',
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                })}
                            </div>
                        </div>
                    </div>
                </div>
            `;

            const modal = new bootstrap.Modal(document.getElementById('modal-detalles'));
            modal.show();
        }

        // Función para editar huésped
        function editarHuesped(numDocumento = null) {
            const doc = numDocumento || (huespedsSeleccionado ? huespedsSeleccionado.numDocumento : null);
            if (!doc) return;

            const huesped = huespedes.find(h => h.numDocumento === doc);
            if (!huesped) return;

            // Cerrar modal si está abierto
            const modal = bootstrap.Modal.getInstance(document.getElementById('modal-detalles'));
            if (modal) modal.hide();

            // Cambiar a la pestaña de registro
            const tabRegistro = new bootstrap.Tab(document.getElementById('registro-tab'));
            tabRegistro.show();

            // Llenar el formulario con los datos actuales
            document.getElementById('numDocumento').value = huesped.numDocumento;
            document.getElementById('nombres').value = huesped.nombres;
            document.getElementById('apellidos').value = huesped.apellidos;
            document.getElementById('tipoDocumento').value = huesped.tipoDocumento;
            document.getElementById('sexo').value = huesped.sexo;
            document.getElementById('numTelefono').value = huesped.numTelefono;
            document.getElementById('correo').value = huesped.correo;

            // Deshabilitar el campo de documento (clave primaria)
            document.getElementById('numDocumento').disabled = true;
            
            // Cambiar el texto del botón
            const btnSubmit = document.querySelector('#form-huesped button[type="submit"]');
            btnSubmit.innerHTML = '<i class="fas fa-save"></i> Actualizar Huésped';
            btnSubmit.classList.remove('btn-primary');
            btnSubmit.classList.add('btn-warning');

            mostrarMensaje('info', 'Editando huésped. El número de documento no se puede modificar.');
        }

        // Función para eliminar huésped
        function eliminarHuesped(numDocumento) {
            const huesped = huespedes.find(h => h.numDocumento === numDocumento);
            if (!huesped) return;

            if (confirm(`¿Está seguro de eliminar al huésped ${huesped.nombres} ${huesped.apellidos}?`)) {
                huespedes = huespedes.filter(h => h.numDocumento !== numDocumento);
                cargarTablaHuespedes();
                mostrarMensaje('success', `Huésped ${huesped.nombres} ${huesped.apellidos} eliminado correctamente`);
            }
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Formulario de registro
            document.getElementById('form-huesped').addEventListener('submit', registrarHuesped);

            // Evento para cargar tabla cuando se cambia a la pestaña de listado
            document.getElementById('listado-tab').addEventListener('shown.bs.tab', function() {
                cargarTablaHuespedes();
            });

            // Filtro en tiempo real
            document.getElementById('filtro-busqueda').addEventListener('input', filtrarHuespedes);

            // Reset del formulario
            document.getElementById('form-huesped').addEventListener('reset', function() {
                // Restaurar estado original del formulario
                document.getElementById('numDocumento').disabled = false;
                const btnSubmit = document.querySelector('#form-huesped button[type="submit"]');
                btnSubmit.innerHTML = '<i class="fas fa-save"></i> Registrar Huésped';
                btnSubmit.classList.remove('btn-warning');
                btnSubmit.classList.add('btn-primary');
                
                // Limpiar clases de validación
                document.querySelectorAll('.form-control').forEach(el => {
                    el.classList.remove('valid', 'invalid');
                });
            });

            // Validación en tiempo real
            document.querySelectorAll('.form-control').forEach(campo => {
                campo.addEventListener('blur', function() {
                    if (this.value.trim()) {
                        this.classList.add('valid');
                        this.classList.remove('invalid');
                    } else {
                        this.classList.add('invalid');
                        this.classList.remove('valid');
                    }
                });
            });

            // Establecer fecha máxima para los campos de fecha (hoy)
            const hoy = new Date().toISOString().split('T')[0];
            document.getElementById('fecha-desde').max = hoy;
            document.getElementById('fecha-hasta').max = hoy;
        });

        // Función para exportar datos (bonus)
        function exportarDatos() {
            const datosExportacion = huespedes.map(h => ({
                Documento: h.numDocumento,
                Nombres: h.nombres,
                Apellidos: h.apellidos,
                TipoDocumento: h.tipoDocumento,
                Sexo: h.sexo,
                Telefono: h.numTelefono,
                Correo: h.correo,
                FechaRegistro: h.fechaCreacion.toLocaleDateString('es-CO')
            }));

            const csv = [
                Object.keys(datosExportacion[0]).join(','),
                ...datosExportacion.map(row => Object.values(row).join(','))
            ].join('\n');

            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `huespedes_${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>