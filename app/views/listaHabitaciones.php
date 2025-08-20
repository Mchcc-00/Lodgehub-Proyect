<?php
require_once 'validarSesion.php';
require_once 'validarHome.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Habitaciones - LodgeHub</title>
    <!-- Bootstrap y Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    
    <!-- Estilos de los layouts -->
    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet">
    <style>
        .container {
            margin-top: 20px;
            margin-left: 260px;
            padding: 0 20px;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
        }

        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .search-section {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }

        .table-responsive {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .table {
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            background: #f8f9fa;
            border: none;
            padding: 20px 15px;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #e9ecef;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .table tbody td {
            padding: 15px;
            border-bottom: 1px solid #f1f3f4;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: #f8f9ff;
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }

        .estado-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            min-width: 90px;
            text-align: center;
        }

        .estado-disponible {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .estado-reservada {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .estado-ocupada {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .estado-mantenimiento {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .capacidad-badge {
            background-color: #e3f2fd;
            color: #1565c0;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .costo-badge {
            background-color: #e8f5e8;
            color: #2e7d32;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .btn-action {
            margin: 0 2px;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.2s ease;
            border: none;
            font-size: 0.85rem;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            font-size: 1.1rem;
        }

        .pagination {
            justify-content: center;
            margin-top: 30px;
        }

        .pagination .page-link {
            border-radius: 8px;
            margin: 0 2px;
            border: 1px solid #dee2e6;
            color: #495057;
        }

        .pagination .page-item.active .page-link {
            background-color: #667eea;
            border-color: #667eea;
        }

        .success-message, .error-message {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.95rem;
            animation: slideDown 0.3s ease;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .modal-header {
            border-bottom: 1px solid #e9ecef;
            border-radius: 15px 15px 0 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-close {
            filter: invert(1);
        }

        .required {
            color: #dc3545;
            font-weight: bold;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 10px 12px;
            font-size: 0.95rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .habitacion-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
            transition: all 0.3s ease;
            position: relative;
            border: 3px solid transparent;
        }

        .habitacion-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }

        .habitacion-card.disponible {
            border-color: #28a745;
        }

        .habitacion-card.en-uso {
            border-color: #dc3545;
        }

        .habitacion-card.reservada {
            border-color: #fd7e14;
        }

        .habitacion-card.mantenimiento {
            border-color: #6c757d;
        }

        .habitacion-imagen {
            height: 200px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .habitacion-imagen.sin-imagen {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }

        .estado-tag {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .estado-tag.disponible {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        .estado-tag.en-uso {
            background: linear-gradient(135deg, #dc3545, #e74c3c);
        }

        .estado-tag.reservada {
            background: linear-gradient(135deg, #fd7e14, #f39c12);
        }

        .estado-tag.mantenimiento {
            background: linear-gradient(135deg, #6c757d, #495057);
        }

        .habitacion-contenido {
            padding: 25px;
        }

        .habitacion-numero {
            font-size: 1.8rem;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .habitacion-tipo {
            color: #f39c12;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .habitacion-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .info-item {
            text-align: center;
        }

        .info-label {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .info-valor {
            font-weight: 700;
            color: #2c3e50;
            font-size: 1rem;
        }

        .info-valor.capacidad {
            color: #3498db;
        }

        .info-valor.estado {
            font-size: 0.9rem;
        }

        .habitacion-precio {
            text-align: center;
            margin: 20px 0;
            font-size: 1.5rem;
            font-weight: 800;
            color: #27ae60;
        }

        .habitacion-descripcion {
            color: #6c757d;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 20px;
            height: 60px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }

        .habitacion-acciones {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .btn-card {
            flex: 1;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.85rem;
            border: none;
            transition: all 0.2s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .btn-editar {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }

        .btn-estado {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
        }

        .btn-eliminar {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state h4 {
            margin-bottom: 10px;
            color: #495057;
        }

        @media (max-width: 768px) {
            .habitacion-card {
                margin-bottom: 20px;
            }
            
            .habitacion-contenido {
                padding: 20px;
            }
            
            .habitacion-numero {
                font-size: 1.5rem;
            }
            
            .habitacion-precio {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>

    <?php
        include "layouts/sidebar.php";
        include "layouts/navbar.php";
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>

    <div class="container">
        <div class="header">
            <h1>Lista de Habitaciones</h1>
            <p>Gestiona todas las habitaciones del hotel: disponibilidad, precios, mantenimiento y más</p>
        </div>

        <!-- Sección de búsqueda y filtros -->
        <div class="search-section">
            <div class="row align-items-center mb-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control" id="buscar-input" placeholder="Buscar por número, tipo o descripción...">
                        <button class="btn btn-outline-primary" type="button" id="buscar-btn">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
                <div class="col-md-8 text-end">
                    <div class="btn-group me-2">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-filter"></i> Filtros
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item filter-option" href="#" data-filter="all">Todas</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">Por Estado</h6></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="Disponible">Disponibles</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="Reservada">Reservadas</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="Ocupada">Ocupadas</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="Mantenimiento">En Mantenimiento</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">Por Capacidad</h6></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="capacidad-1">1 Persona</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="capacidad-2">2 Personas</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="capacidad-3">3+ Personas</a></li>
                        </ul>
                    </div>
                    <button class="btn btn-success" id="refresh-btn">
                        <i class="fas fa-sync-alt"></i> Actualizar
                    </button>
                    <a href="crearHabitacion.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Habitación
                    </a>
                </div>
            </div>
        </div>

        <!-- Mensajes -->
        <div id="success-message" class="success-message" style="display: none;">
            ✅ <strong id="success-text">Operación exitosa</strong>
        </div>

        <div id="error-message" class="error-message" style="display: none;">
            ❌ <strong id="error-text">Error en la operación</strong>
        </div>

        <!-- Grid de Habitaciones -->
        <div id="habitaciones-grid" class="row">
            <div class="col-12">
                <div class="loading text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-3 text-muted">Cargando habitaciones...</p>
                </div>
            </div>
        </div>

        <!-- Paginación -->
        <nav aria-label="Paginación de Habitaciones" id="paginacion-container" style="display: none;">
            <ul class="pagination" id="paginacion">
                <!-- Generado dinámicamente -->
            </ul>
        </nav>
    </div>

    <!-- Modal de edición -->
    <div class="modal fade" id="editarModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit"></i> Editar Habitación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="form-editar" enctype="multipart/form-data">
                        <input type="hidden" id="edit-numero-original">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-numero" class="form-label">Número de Habitación <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="edit-numero" name="numero" required maxlength="5">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-tipo" class="form-label">Tipo de Habitación <span class="required">*</span></label>
                                    <select class="form-select" id="edit-tipo" name="tipoHabitacion" required>
                                        <!-- Opciones cargadas dinámicamente desde td_tipoHabitacion -->
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit-costo" class="form-label">Costo por Noche <span class="required">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="edit-costo" name="costo" required min="0" step="0.01">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit-capacidad" class="form-label">Capacidad <span class="required">*</span></label>
                                    <input type="number" class="form-control" id="edit-capacidad" name="capacidad" required min="1" max="10">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit-estado" class="form-label">Estado <span class="required">*</span></label>
                                    <select class="form-select" id="edit-estado" name="estado" required>
                                        <option value="Disponible">Disponible</option>
                                        <option value="Reservada">Reservada</option>
                                        <option value="Ocupada">Ocupada</option>
                                        <option value="Mantenimiento">Mantenimiento</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-foto" class="form-label">Foto de la Habitación</label>
                                    <input type="file" class="form-control" id="edit-foto" name="foto" accept="image/*">
                                    <div class="form-text">Formatos permitidos: JPG, PNG, WEBP. Tamaño máximo: 2MB</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-estadoMantenimiento" class="form-label">Estado de Mantenimiento</label>
                                    <select class="form-select" id="edit-estadoMantenimiento" name="estadoMantenimiento">
                                        <option value="Activo">Activo</option>
                                        <option value="Inactivo">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit-descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="edit-descripcion" name="descripcion" rows="3" maxlength="1000" placeholder="Descripción general de la habitación..."></textarea>
                        </div>

                        <div class="mb-3" id="mantenimiento-container" style="display: none;">
                            <label for="edit-descripcionMantenimiento" class="form-label">Descripción del Mantenimiento</label>
                            <textarea class="form-control" id="edit-descripcionMantenimiento" name="descripcionMantenimiento" rows="3" maxlength="1000" placeholder="Detalles sobre el mantenimiento requerido..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="guardar-edicion">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de visualización completa -->
    <div class="modal fade" id="verModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-eye"></i> Detalles de Habitación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detalles-habitacion">
                    <!-- Contenido dinámico -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div class="modal fade" id="eliminarModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar esta habitación?</p>
                    <div class="alert alert-info">
                        <strong id="eliminar-info">Información de Habitación</strong><br>
                        <small>Número: <span id="eliminar-numero">-</span></small>
                    </div>
                    <p class="text-danger">
                        <i class="fas fa-warning"></i> 
                        Esta acción no se puede deshacer y afectará todas las reservas asociadas.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmar-eliminacion">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script adaptado para gestión de habitaciones
        let habitaciones = [];
        let habitacionesFiltradas = [];
        let paginaActual = 1;
        const habitacionesPorPagina = 10;
        let filtroActual = 'all';
        let busquedaActual = '';

        document.addEventListener('DOMContentLoaded', function() {
            cargarHabitaciones();
            cargarTiposHabitacion();
            
            // Event listeners
            document.getElementById('buscar-btn').addEventListener('click', buscarHabitaciones);
            document.getElementById('buscar-input').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') buscarHabitaciones();
            });
            document.getElementById('refresh-btn').addEventListener('click', cargarHabitaciones);
            document.getElementById('guardar-edicion').addEventListener('click', guardarEdicion);
            document.getElementById('confirmar-eliminacion').addEventListener('click', eliminarHabitacion);

            // Filtros
            document.querySelectorAll('.filter-option').forEach(option => {
                option.addEventListener('click', function(e) {
                    e.preventDefault();
                    filtroActual = this.getAttribute('data-filter');
                    aplicarFiltros();
                });
            });

            // Control de estado para mostrar descripción de mantenimiento
            document.getElementById('edit-estado').addEventListener('change', function() {
                const contenedor = document.getElementById('mantenimiento-container');
                if (this.value === 'Mantenimiento') {
                    contenedor.style.display = 'block';
                } else {
                    contenedor.style.display = 'none';
                    document.getElementById('edit-descripcionMantenimiento').value = '';
                }
            });
        });

        function cargarHabitaciones() {
            // Simular carga de datos - aquí harías la petición AJAX real
            setTimeout(() => {
                habitaciones = [
                    {
                        numero: '101',
                        costo: 150000.00,
                        capacidad: 2,
                        tipoHabitacion: 1,
                        tipoNombre: 'Estándar',
                        foto: 'habitacion1.jpg',
                        descripcion: 'Habitación cómoda con vista al jardín',
                        estado: 'Disponible',
                        descripcionMantenimiento: null,
                        estadoMantenimiento: 'Activo'
                    },
                    {
                        numero: '102',
                        costo: 200000.00,
                        capacidad: 4,
                        tipoHabitacion: 2,
                        tipoNombre: 'Suite',
                        foto: null,
                        descripcion: 'Suite ejecutiva con sala de estar',
                        estado: 'Ocupada',
                        descripcionMantenimiento: null,
                        estadoMantenimiento: 'Activo'
                    },
                    {
                        numero: '201',
                        costo: 120000.00,
                        capacidad: 1,
                        tipoHabitacion: 3,
                        tipoNombre: 'Individual',
                        foto: 'habitacion2.jpg',
                        descripcion: 'Habitación individual para viajeros de negocios',
                        estado: 'Mantenimiento',
                        descripcionMantenimiento: 'Reparación del aire acondicionado',
                        estadoMantenimiento: 'Activo'
                    }
                ];
                
                aplicarFiltros();
                mostrarMensaje('Habitaciones cargadas correctamente', 'success');
            }, 1000);
        }

        function cargarTiposHabitacion() {
            // Simular carga de tipos - aquí harías la petición AJAX real
            const tipos = [
                { id: 1, descripcion: 'Estándar' },
                { id: 2, descripcion: 'Suite' },
                { id: 3, descripcion: 'Individual' },
                { id: 4, descripcion: 'Familiar' }
            ];

            const select = document.getElementById('edit-tipo');
            select.innerHTML = '';
            tipos.forEach(tipo => {
                const option = document.createElement('option');
                option.value = tipo.id;
                option.textContent = tipo.descripcion;
                select.appendChild(option);
            });
        }

        function aplicarFiltros() {
            habitacionesFiltradas = habitaciones.filter(habitacion => {
                const cumpleFiltro = filtroActual === 'all' || 
                                   habitacion.estado === filtroActual ||
                                   (filtroActual.startsWith('capacidad-') && 
                                    habitacion.capacidad >= parseInt(filtroActual.split('-')[1]));
                
                const cumpleBusqueda = busquedaActual === '' ||
                                     habitacion.numero.toLowerCase().includes(busquedaActual.toLowerCase()) ||
                                     habitacion.tipoNombre.toLowerCase().includes(busquedaActual.toLowerCase()) ||
                                     (habitacion.descripcion && habitacion.descripcion.toLowerCase().includes(busquedaActual.toLowerCase()));
                
                return cumpleFiltro && cumpleBusqueda;
            });

            paginaActual = 1;
            mostrarHabitaciones();
            mostrarPaginacion();
        }

        function buscarHabitaciones() {
            busquedaActual = document.getElementById('buscar-input').value;
            aplicarFiltros();
        }

        function mostrarHabitaciones() {
            const grid = document.getElementById('habitaciones-grid');
            const inicio = (paginaActual - 1) * habitacionesPorPagina;
            const fin = inicio + habitacionesPorPagina;
            const habitacionesPagina = habitacionesFiltradas.slice(inicio, fin);

            if (habitacionesPagina.length === 0) {
                grid.innerHTML = `
                    <div class="col-12">
                        <div class="empty-state">
                            <i class="fas fa-bed"></i>
                            <h4>No se encontraron habitaciones</h4>
                            <p>Intenta ajustar los filtros de búsqueda</p>
                        </div>
                    </div>
                `;
                return;
            }

            grid.innerHTML = habitacionesPagina.map(habitacion => `
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="habitacion-card ${habitacion.estado.toLowerCase().replace(' ', '-')}">
                        <div class="habitacion-imagen ${!habitacion.foto ? 'sin-imagen' : ''}" 
                             ${habitacion.foto ? `style="background-image: url('../../public/uploads/habitaciones/${habitacion.foto}')"` : ''}>
                            ${!habitacion.foto ? '<i class="fas fa-bed"></i>' : ''}
                            <div class="estado-tag ${getEstadoClass(habitacion.estado)}">
                                ${habitacion.estado}
                            </div>
                        </div>
                        
                        <div class="habitacion-contenido">
                            <div class="habitacion-numero">Habitación ${habitacion.numero}</div>
                            <div class="habitacion-tipo">
                                <i class="fas fa-tag"></i> ${habitacion.tipoNombre}
                            </div>
                            
                            <div class="habitacion-info">
                                <div class="info-item">
                                    <div class="info-label">Capacidad</div>
                                    <div class="info-valor capacidad">${habitacion.capacidad} pers.</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Estado</div>
                                    <div class="info-valor estado">${getEstadoTexto(habitacion.estado)}</div>
                                </div>
                            </div>
                            
                            <div class="habitacion-precio">
                                ${habitacion.costo.toLocaleString()}/noche
                            </div>
                            
                            <div class="habitacion-descripcion">
                                ${habitacion.descripcion || 'Sin descripción disponible'}
                            </div>
                            
                            <div class="habitacion-acciones">
                                <button class="btn btn-card btn-editar" onclick="editarHabitacion('${habitacion.numero}')" title="Editar">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button class="btn btn-card btn-estado" onclick="verHabitacion('${habitacion.numero}')" title="Ver detalles">
                                    <i class="fas fa-eye"></i> Estado
                                </button>
                                <button class="btn btn-card btn-eliminar" onclick="confirmarEliminar('${habitacion.numero}')" title="Eliminar">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function getEstadoClass(estado) {
            switch(estado) {
                case 'Disponible': return 'disponible';
                case 'Reservada': return 'reservada';
                case 'Ocupada': return 'en-uso';
                case 'Mantenimiento': return 'mantenimiento';
                default: return 'disponible';
            }
        }

        function getEstadoTexto(estado) {
            switch(estado) {
                case 'Disponible': return 'Libre';
                case 'Reservada': return 'Reservada';
                case 'Ocupada': return 'En uso';
                case 'Mantenimiento': return 'Mantenim.';
                default: return estado;
            }
        }

        function getEstadoIcon(estado) {
            switch(estado) {
                case 'Disponible': return '<i class="fas fa-check-circle"></i>';
                case 'Reservada': return '<i class="fas fa-calendar-check"></i>';
                case 'Ocupada': return '<i class="fas fa-user-friends"></i>';
                case 'Mantenimiento': return '<i class="fas fa-tools"></i>';
                default: return '<i class="fas fa-question-circle"></i>';
            }
        }

        function mostrarPaginacion() {
            const totalPaginas = Math.ceil(habitacionesFiltradas.length / habitacionesPorPagina);
            const contenedor = document.getElementById('paginacion-container');
            const paginacion = document.getElementById('paginacion');

            if (totalPaginas <= 1) {
                contenedor.style.display = 'none';
                return;
            }

            contenedor.style.display = 'block';
            let html = '';

            // Botón anterior
            html += `
                <li class="page-item ${paginaActual === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="cambiarPagina(${paginaActual - 1})">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
            `;

            // Páginas
            for (let i = 1; i <= totalPaginas; i++) {
                html += `
                    <li class="page-item ${i === paginaActual ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="cambiarPagina(${i})">${i}</a>
                    </li>
                `;
            }

            // Botón siguiente
            html += `
                <li class="page-item ${paginaActual === totalPaginas ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="cambiarPagina(${paginaActual + 1})">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            `;

            paginacion.innerHTML = html;
        }

        function cambiarPagina(nuevaPagina) {
            const totalPaginas = Math.ceil(habitacionesFiltradas.length / habitacionesPorPagina);
            if (nuevaPagina >= 1 && nuevaPagina <= totalPaginas) {
                paginaActual = nuevaPagina;
                mostrarHabitaciones();
                mostrarPaginacion();
            }
        }

        function verHabitacion(numero) {
            const habitacion = habitaciones.find(h => h.numero === numero);
            if (!habitacion) return;

            const detalles = document.getElementById('detalles-habitacion');
            detalles.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body">
                                <h6 class="card-title text-primary"><i class="fas fa-info-circle"></i> Información General</h6>
                                <hr>
                                <div class="row">
                                    <div class="col-6"><strong>Número:</strong></div>
                                    <div class="col-6">${habitacion.numero}</div>
                                </div>
                                <div class="row">
                                    <div class="col-6"><strong>Tipo:</strong></div>
                                    <div class="col-6">${habitacion.tipoNombre}</div>
                                </div>
                                <div class="row">
                                    <div class="col-6"><strong>Capacidad:</strong></div>
                                    <div class="col-6"><i class="fas fa-user"></i> ${habitacion.capacidad} persona(s)</div>
                                </div>
                                <div class="row">
                                    <div class="col-6"><strong>Costo/Noche:</strong></div>
                                    <div class="col-6 text-success"><strong>${habitacion.costo.toLocaleString()}</strong></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body">
                                <h6 class="card-title text-warning"><i class="fas fa-cogs"></i> Estado y Mantenimiento</h6>
                                <hr>
                                <div class="row">
                                    <div class="col-6"><strong>Estado:</strong></div>
                                    <div class="col-6">
                                        <span class="estado-badge estado-${habitacion.estado.toLowerCase()}">
                                            ${getEstadoIcon(habitacion.estado)} ${habitacion.estado}
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6"><strong>Mantenimiento:</strong></div>
                                    <div class="col-6">
                                        <span class="badge ${habitacion.estadoMantenimiento === 'Activo' ? 'bg-success' : 'bg-secondary'}">
                                            ${habitacion.estadoMantenimiento}
                                        </span>
                                    </div>
                                </div>
                                ${habitacion.descripcionMantenimiento ? `
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <small class="text-muted"><strong>Descripción del mantenimiento:</strong></small>
                                            <p class="mt-1">${habitacion.descripcionMantenimiento}</p>
                                        </div>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>
                
                ${habitacion.foto ? `
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <h6 class="card-title text-info"><i class="fas fa-camera"></i> Fotografía</h6>
                            <hr>
                            <div class="text-center">
                                <img src="../../public/uploads/habitaciones/${habitacion.foto}" 
                                     class="img-fluid rounded" 
                                     style="max-height: 300px; object-fit: cover;" 
                                     alt="Habitación ${habitacion.numero}">
                            </div>
                        </div>
                    </div>
                ` : ''}
                
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title text-success"><i class="fas fa-align-left"></i> Descripción</h6>
                        <hr>
                        <p>${habitacion.descripcion || '<em class="text-muted">No hay descripción disponible</em>'}</p>
                    </div>
                </div>
            `;

            new bootstrap.Modal(document.getElementById('verModal')).show();
        }

        function editarHabitacion(numero) {
            const habitacion = habitaciones.find(h => h.numero === numero);
            if (!habitacion) return;

            // Llenar el formulario
            document.getElementById('edit-numero-original').value = habitacion.numero;
            document.getElementById('edit-numero').value = habitacion.numero;
            document.getElementById('edit-tipo').value = habitacion.tipoHabitacion;
            document.getElementById('edit-costo').value = habitacion.costo;
            document.getElementById('edit-capacidad').value = habitacion.capacidad;
            document.getElementById('edit-estado').value = habitacion.estado;
            document.getElementById('edit-estadoMantenimiento').value = habitacion.estadoMantenimiento;
            document.getElementById('edit-descripcion').value = habitacion.descripcion || '';
            document.getElementById('edit-descripcionMantenimiento').value = habitacion.descripcionMantenimiento || '';

            // Mostrar/ocultar descripción de mantenimiento
            const contenedor = document.getElementById('mantenimiento-container');
            if (habitacion.estado === 'Mantenimiento') {
                contenedor.style.display = 'block';
            } else {
                contenedor.style.display = 'none';
            }

            new bootstrap.Modal(document.getElementById('editarModal')).show();
        }

        function guardarEdicion() {
            const form = document.getElementById('form-editar');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Aquí harías la petición AJAX para guardar
            mostrarMensaje('Habitación actualizada correctamente', 'success');
            bootstrap.Modal.getInstance(document.getElementById('editarModal')).hide();
            cargarHabitaciones();
        }

        function confirmarEliminar(numero) {
            const habitacion = habitaciones.find(h => h.numero === numero);
            if (!habitacion) return;

            document.getElementById('eliminar-numero').textContent = numero;
            document.getElementById('eliminar-info').innerHTML = `
                Habitación ${numero} - ${habitacion.tipoNombre}<br>
                Estado: ${habitacion.estado} | Capacidad: ${habitacion.capacidad} personas
            `;

            // Guardar el número para el proceso de eliminación
            document.getElementById('confirmar-eliminacion').setAttribute('data-numero', numero);
            
            new bootstrap.Modal(document.getElementById('eliminarModal')).show();
        }

        function eliminarHabitacion() {
            const numero = document.getElementById('confirmar-eliminacion').getAttribute('data-numero');
            
            // Aquí harías la petición AJAX para eliminar
            mostrarMensaje('Habitación eliminada correctamente', 'success');
            bootstrap.Modal.getInstance(document.getElementById('eliminarModal')).hide();
            cargarHabitaciones();
        }

        function mostrarMensaje(mensaje, tipo) {
            const elementoMensaje = document.getElementById(`${tipo}-message`);
            const textoMensaje = document.getElementById(`${tipo}-text`);
            
            textoMensaje.textContent = mensaje;
            elementoMensaje.style.display = 'block';
            
            setTimeout(() => {
                elementoMensaje.style.display = 'none';
            }, 5000);
        }

        // Prevenir el envío del formulario al presionar Enter
        document.addEventListener('keypress', function(e) {
            if (e.target.matches('input[type="text"], input[type="number"]') && e.key === 'Enter') {
                e.preventDefault();
            }
        });
    </script>

</body>
</html>