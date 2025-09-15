<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nueva Reserva</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Variables CSS para el módulo de reservas */
        :root {
            --reservas-primary: #0d6efd;
            --reservas-success: #198754;
            --reservas-danger: #dc3545;
            --reservas-warning: #ffc107;
            --reservas-info: #0dcaf0;
            --reservas-light: #f8f9fa;
            --reservas-dark: #212529;
            --reservas-border: #dee2e6;
            --reservas-border-radius: 8px;
            --reservas-shadow: 0 2px 4px rgba(0,0,0,0.1);
            --reservas-shadow-hover: 0 4px 8px rgba(0,0,0,0.15);
        }

        /* Estilos base */
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

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header section */
        .header-section {
            background: linear-gradient(135deg, var(--reservas-primary), #4c84ff);
            color: white;
            border-radius: var(--reservas-border-radius);
            box-shadow: var(--reservas-shadow);
            margin-bottom: 2rem;
            padding: 2rem 0;
        }

        .header-section h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0 0 0.5rem 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .header-section p {
            font-size: 1.1rem;
            margin: 0;
            opacity: 0.9;
        }

        /* Form sections */
        .form-section {
            background: white;
            border-radius: var(--reservas-border-radius);
            box-shadow: var(--reservas-shadow);
            overflow: hidden;
            margin-bottom: 2rem;
            animation: fadeIn 0.6s ease forwards;
        }

        .section-title {
            background: linear-gradient(135deg, var(--reservas-success), #20c997);
            color: white;
            padding: 1.5rem 2rem;
            margin: 0 0 2rem 0;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title i {
            font-size: 1.75rem;
        }

        /* Form inputs styling */
        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--reservas-dark);
            font-size: 0.95rem;
        }

        .required-field::after {
            content: " *";
            color: var(--reservas-danger);
            font-weight: 700;
        }

        .form-control,
        .form-select {
            padding: 0.75rem;
            border: 2px solid var(--reservas-border);
            border-radius: var(--reservas-border-radius);
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--reservas-primary);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        /* Input groups */
        .input-group .btn {
            border: 2px solid var(--reservas-border);
            border-left: none;
            transition: all 0.3s ease;
        }

        .input-group .form-control:focus + .btn {
            border-color: var(--reservas-primary);
        }

        /* Guest counter styling */
        .guest-counter {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .guest-counter .btn {
            width: 40px;
            height: 40px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .guest-counter .btn:hover {
            transform: scale(1.1);
            box-shadow: var(--reservas-shadow);
        }

        .guest-counter .form-control {
            max-width: 80px;
            border-radius: var(--reservas-border-radius);
        }

        /* Price display */
        .price-display {
            background: linear-gradient(135deg, var(--reservas-info), #4dd0e1);
            color: white;
            padding: 2rem;
            border-radius: var(--reservas-border-radius);
            text-align: center;
            box-shadow: var(--reservas-shadow);
        }

        .price-display h5 {
            margin: 0 0 1rem 0;
            font-weight: 600;
            opacity: 0.9;
        }

        .price-display h2 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        /* Buttons styling */
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: var(--reservas-border-radius);
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
            box-shadow: var(--reservas-shadow-hover);
        }

        .btn-custom {
            background: linear-gradient(135deg, var(--reservas-success), #20c997);
            color: white;
            border: none;
        }

        .btn-custom:hover {
            background: linear-gradient(135deg, #157347, #198754);
            color: white;
        }

        .btn-light {
            background: white;
            color: var(--reservas-dark);
            border: 2px solid var(--reservas-border);
        }

        .btn-light:hover {
            background: var(--reservas-light);
            border-color: var(--reservas-primary);
            color: var(--reservas-primary);
        }

        /* Alert styling */
        .alert {
            border-radius: var(--reservas-border-radius);
            border: none;
            font-size: 0.9rem;
            padding: 0.75rem 1rem;
        }

        .alert-success {
            background: linear-gradient(135deg, #d1edff, #e8f5e8);
            color: #155724;
            border-left: 3px solid var(--reservas-success);
        }

        .alert-warning {
            background: linear-gradient(135deg, #fff8e1, #ffeaa7);
            color: #856404;
            border-left: 3px solid var(--reservas-warning);
        }

        /* Modal styling */
        .modal-content {
            border-radius: var(--reservas-border-radius);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            border: none;
        }

        .modal-header {
            border-bottom: 1px solid var(--reservas-border);
            background: linear-gradient(135deg, var(--reservas-success), #20c997);
        }

        .modal-title {
            font-weight: 600;
            color: white;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .modal-body {
            padding: 2rem;
            text-align: center;
        }

        .modal-footer {
            border-top: 1px solid var(--reservas-border);
            background: var(--reservas-light);
            justify-content: center;
            gap: 1rem;
        }

        /* Form validation */
        .form-control.is-valid,
        .form-select.is-valid {
            border-color: var(--reservas-success);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='m2.3 6.73.94-.94 1.94 1.94 4.08-4.08.94.94-5.02 5.02L2.3 6.73z'/%3e%3c/svg%3e");
        }

        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: var(--reservas-danger);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5' fill='none' stroke='%23dc3545'/%3e%3cpath d='m5.8 4.6 2.4 2.4m0-2.4-2.4 2.4' stroke='%23dc3545'/%3e%3c/svg%3e");
        }

        .invalid-feedback {
            display: block;
            color: var(--reservas-danger);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideIn {
            from { transform: translateX(-20px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .header-section h1 {
                font-size: 2rem;
            }
            
            .section-title {
                font-size: 1.25rem;
                padding: 1rem 1.5rem;
            }
            
            .price-display h2 {
                font-size: 2rem;
            }
            
            .guest-counter {
                justify-content: center;
            }
        }

        /* Form content padding */
        .form-section .container,
        .form-section .row {
            padding-left: 2rem;
            padding-right: 2rem;
        }

        .form-section .row:last-child {
            padding-bottom: 2rem;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fas fa-plus-circle me-3"></i>Nueva Reserva</h1>
                    <p class="mb-0">Crea una nueva reserva en el sistema</p>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-light" onclick="window.location.href='listaReservas.php'">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <form id="formReserva" novalidate>
            <!-- Información del Cliente -->
            <div class="form-section">
                <h4 class="section-title"><i class="fas fa-user me-2"></i>Información del Cliente</h4>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label required-field">Usuario (Documento)</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="us_numDocumento" name="us_numDocumento" required>
                            <button type="button" class="btn btn-outline-primary" onclick="buscarUsuario()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback">Por favor ingrese el documento del usuario</div>
                        <div id="infoUsuario" class="mt-2"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required-field">Huésped (Documento)</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="hue_numDocumento" name="hue_numDocumento" required>
                            <button type="button" class="btn btn-outline-primary" onclick="buscarHuesped()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback">Por favor ingrese el documento del huésped</div>
                        <div id="infoHuesped" class="mt-2"></div>
                    </div>
                </div>
            </div>

            <!-- Información de la Reserva -->
            <div class="form-section">
                <h4 class="section-title"><i class="fas fa-calendar me-2"></i>Información de la Reserva</h4>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label required-field">Hotel</label>
                        <select class="form-select" id="id_hotel" name="id_hotel" required onchange="cargarHabitaciones()">
                            <option value="">Seleccione un hotel</option>
                            <!-- Se cargan dinámicamente -->
                        </select>
                        <div class="invalid-feedback">Por favor seleccione un hotel</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required-field">Habitación</label>
                        <select class="form-select" id="id_habitacion" name="id_habitacion" required onchange="calcularPrecio()">
                            <option value="">Seleccione una habitación</option>
                            <!-- Se cargan dinámicamente -->
                        </select>
                        <div class="invalid-feedback">Por favor seleccione una habitación</div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label required-field">Fecha de Inicio</label>
                        <input type="date" class="form-control" id="fechainicio" name="fechainicio" required onchange="calcularPrecio()">
                        <div class="invalid-feedback">Por favor ingrese la fecha de inicio</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required-field">Fecha de Fin</label>
                        <input type="date" class="form-control" id="fechaFin" name="fechaFin" required onchange="calcularPrecio()">
                        <div class="invalid-feedback">Por favor ingrese la fecha de fin</div>
                    </div>
                </div>
            </div>

            <!-- Huéspedes -->
            <div class="form-section">
                <h4 class="section-title"><i class="fas fa-users me-2"></i>Cantidad de Huéspedes</h4>
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Adultos</label>
                        <div class="guest-counter">
                            <button type="button" class="btn btn-outline-primary" onclick="cambiarCantidad('cantidadAdultos', -1)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="form-control text-center" id="cantidadAdultos" name="cantidadAdultos" value="1" min="0" max="10" onchange="calcularPrecio()">
                            <button type="button" class="btn btn-outline-primary" onclick="cambiarCantidad('cantidadAdultos', 1)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Niños</label>
                        <div class="guest-counter">
                            <button type="button" class="btn btn-outline-primary" onclick="cambiarCantidad('cantidadNinos', -1)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="form-control text-center" id="cantidadNinos" name="cantidadNinos" value="0" min="0" max="10" onchange="calcularPrecio()">
                            <button type="button" class="btn btn-outline-primary" onclick="cambiarCantidad('cantidadNinos', 1)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Personas con Discapacidad</label>
                        <div class="guest-counter">
                            <button type="button" class="btn btn-outline-primary" onclick="cambiarCantidad('cantidadDiscapacitados', -1)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="form-control text-center" id="cantidadDiscapacitados" name="cantidadDiscapacitados" value="0" min="0" max="10" onchange="calcularPrecio()">
                            <button type="button" class="btn btn-outline-primary" onclick="cambiarCantidad('cantidadDiscapacitados', 1)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles Adicionales -->
            <div class="form-section">
                <h4 class="section-title"><i class="fas fa-info-circle me-2"></i>Detalles Adicionales</h4>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label required-field">Motivo de la Reserva</label>
                        <select class="form-select" id="motivoReserva" name="motivoReserva" required>
                            <option value="">Seleccione un motivo</option>
                            <option value="Negocios">Negocios</option>
                            <option value="Personal">Personal</option>
                            <option value="Viaje">Viaje</option>
                            <option value="Familiar">Familiar</option>
                            <option value="Otro">Otro</option>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione el motivo</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required-field">Método de Pago</label>
                        <select class="form-select" id="metodoPago" name="metodoPago" required>
                            <option value="">Seleccione método de pago</option>
                            <option value="Tarjeta">Tarjeta</option>
                            <option value="Efectivo">Efectivo</option>
                            <option value="PSE">PSE</option>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione el método de pago</div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <label class="form-label">Información Adicional</label>
                        <textarea class="form-control" id="informacionAdicional" name="informacionAdicional" rows="3" placeholder="Ingrese cualquier información adicional sobre la reserva..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Resumen de Precio -->
            <div class="form-section">
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="section-title"><i class="fas fa-calculator me-2"></i>Resumen de la Reserva</h4>
                        <div id="resumenReserva">
                            <p><strong>Noches:</strong> <span id="totalNoches">0</span></p>
                            <p><strong>Huéspedes:</strong> <span id="totalHuespedes">1</span></p>
                            <p><strong>Precio por noche:</strong> $<span id="precioPorNoche">0</span></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="price-display">
                            <h5>Total a Pagar</h5>
                            <h2 id="pagoFinal">$0.00</h2>
                            <input type="hidden" id="pagoFinalHidden" name="pagoFinal" value="0">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="text-center mb-4">
                <button type="button" class="btn btn-secondary me-3" onclick="limpiarFormulario()">
                    <i class="fas fa-eraser me-2"></i>Limpiar
                </button>
                <button type="submit" class="btn btn-custom btn-lg">
                    <i class="fas fa-save me-2"></i>Crear Reserva
                </button>
            </div>
        </form>
    </div>

    <!-- Modal de éxito -->
    <div class="modal fade" id="modalExito" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i>Reserva Creada</h5>
                </div>
                <div class="modal-body">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h4>¡Reserva creada exitosamente!</h4>
                    <p>La reserva ha sido registrada en el sistema.</p>
                    <p><strong>ID de Reserva: </strong><span id="reservaId"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="window.location.href='listaReservas.php'">Ver Reservas</button>
                    <button type="button" class="btn btn-primary" onclick="nuevaReserva()">Nueva Reserva</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            cargarHoteles();
            establecerFechaMinima();
        });

        // Establecer fecha mínima como hoy
        function establecerFechaMinima() {
            const hoy = new Date().toISOString().split('T')[0];
            document.getElementById('fechainicio').min = hoy;
            document.getElementById('fechaFin').min = hoy;
        }

        // Cambiar cantidad de huéspedes
        function cambiarCantidad(campo, cambio) {
            const input = document.getElementById(campo);
            let valor = parseInt(input.value) + cambio;
            if (valor < 0) valor = 0;
            if (valor > 10) valor = 10;
            input.value = valor;
            calcularPrecio();
        }

        // Cargar hoteles
        async function cargarHoteles() {
            try {
                // Simulación de datos para la demo
                const hoteles = [
                    {id: 1, nombre: "Hotel Plaza Central"},
                    {id: 2, nombre: "Gran Hotel Metropolitano"},
                    {id: 3, nombre: "Hotel Boutique Del Mar"}
                ];
                
                const select = document.getElementById('id_hotel');
                select.innerHTML = '<option value="">Seleccione un hotel</option>';
                hoteles.forEach(hotel => {
                    select.innerHTML += `<option value="${hotel.id}">${hotel.nombre}</option>`;
                });
            } catch (error) {
                console.error('Error al cargar hoteles:', error);
            }
        }

        // Cargar habitaciones según el hotel seleccionado
        async function cargarHabitaciones() {
            const hotelId = document.getElementById('id_hotel').value;
            const habitacionSelect = document.getElementById('id_habitacion');
            
            if (!hotelId) {
                habitacionSelect.innerHTML = '<option value="">Seleccione una habitación</option>';
                return;
            }

            try {
                // Simulación de datos para la demo
                const habitaciones = [
                    {id: 1, numero: "101", tipo: "Standard", precio: 120},
                    {id: 2, numero: "201", tipo: "Deluxe", precio: 180},
                    {id: 3, numero: "301", tipo: "Suite", precio: 250}
                ];
                
                habitacionSelect.innerHTML = '<option value="">Seleccione una habitación</option>';
                habitaciones.forEach(habitacion => {
                    habitacionSelect.innerHTML += `<option value="${habitacion.id}" data-precio="${habitacion.precio}">${habitacion.numero} - ${habitacion.tipo} ($${habitacion.precio}/noche)</option>`;
                });
            } catch (error) {
                console.error('Error al cargar habitaciones:', error);
            }
        }

        // Calcular precio total
        function calcularPrecio() {
            const fechaInicio = document.getElementById('fechainicio').value;
            const fechaFin = document.getElementById('fechaFin').value;
            const habitacionSelect = document.getElementById('id_habitacion');
            const adultos = parseInt(document.getElementById('cantidadAdultos').value) || 0;
            const ninos = parseInt(document.getElementById('cantidadNinos').value) || 0;
            const discapacitados = parseInt(document.getElementById('cantidadDiscapacitados').value) || 0;

            if (!fechaInicio || !fechaFin || !habitacionSelect.value) return;

            const fecha1 = new Date(fechaInicio);
            const fecha2 = new Date(fechaFin);
            const noches = Math.ceil((fecha2 - fecha1) / (1000 * 60 * 60 * 24));
            
            if (noches <= 0) {
                alert('La fecha de fin debe ser posterior a la fecha de inicio');
                return;
            }

            const precio = parseFloat(habitacionSelect.selectedOptions[0].getAttribute('data-precio')) || 0;
            const totalHuespedes = adultos + ninos + discapacitados;
            const total = precio * noches;

            document.getElementById('totalNoches').textContent = noches;
            document.getElementById('totalHuespedes').textContent = totalHuespedes;
            document.getElementById('precioPorNoche').textContent = precio.toFixed(2);
            document.getElementById('pagoFinal').textContent = '$' + total.toFixed(2);
            document.getElementById('pagoFinalHidden').value = total.toFixed(2);
        }

        // Buscar usuario
        async function buscarUsuario() {
            const documento = document.getElementById('us_numDocumento').value;
            if (!documento) return;

            try {
                // Simulación para la demo
                const usuario = {
                    success: true,
                    data: {
                        nombre: "Juan Pérez",
                        email: "juan.perez@email.com"
                    }
                };
                
                const infoDiv = document.getElementById('infoUsuario');
                if (usuario.success) {
                    infoDiv.innerHTML = `<div class="alert alert-success"><strong>${usuario.data.nombre}</strong><br>Email: ${usuario.data.email}</div>`;
                } else {
                    infoDiv.innerHTML = `<div class="alert alert-warning">Usuario no encontrado</div>`;
                }
            } catch (error) {
                console.error('Error al buscar usuario:', error);
            }
        }

        // Buscar huésped
        async function buscarHuesped() {
            const documento = document.getElementById('hue_numDocumento').value;
            if (!documento) return;

            try {
                // Simulación para la demo
                const huesped = {
                    success: true,
                    data: {
                        nombre: "María García",
                        telefono: "+57 300 123 4567"
                    }
                };
                
                const infoDiv = document.getElementById('infoHuesped');
                if (huesped.success) {
                    infoDiv.innerHTML = `<div class="alert alert-success"><strong>${huesped.data.nombre}</strong><br>Teléfono: ${huesped.data.telefono}</div>`;
                } else {
                    infoDiv.innerHTML = `<div class="alert alert-warning">Huésped no encontrado</div>`;
                }
            } catch (error) {
                console.error('Error al buscar huésped:', error);
            }
        }

        // Limpiar formulario
        function limpiarFormulario() {
            document.getElementById('formReserva').reset();
            document.getElementById('infoUsuario').innerHTML = '';
            document.getElementById('infoHuesped').innerHTML = '';
            document.getElementById('pagoFinal').textContent = '$0.00';
            document.getElementById('pagoFinalHidden').value = '0';
            document.getElementById('totalNoches').textContent = '0';
            document.getElementById('totalHuespedes').textContent = '1';
            document.getElementById('precioPorNoche').textContent = '0';
            establecerFechaMinima();
        }

        // Nueva reserva
        function nuevaReserva() {
            limpiarFormulario();
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalExito'));
            modal.hide();
        }

        // Enviar formulario
        document.getElementById('formReserva').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!this.checkValidity()) {
                e.stopPropagation();
                this.classList.add('was-validated');
                return;
            }

            const formData = new FormData(this);
            formData.append('action', 'crearReserva');

            try {
                // Simulación para la demo
                const result = {
                    success: true,
                    reserva_id: 'RES-' + Math.floor(Math.random() * 10000)
                };
                
                if (result.success) {
                    document.getElementById('reservaId').textContent = result.reserva_id;
                    const modal = new bootstrap.Modal(document.getElementById('modalExito'));
                    modal.show();
                    this.classList.remove('was-validated');
                } else {
                    alert('Error al crear la reserva: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
            }
        });

        // Validar fechas
        document.getElementById('fechainicio').addEventListener('change', function() {
            const fechaFin = document.getElementById('fechaFin');
            fechaFin.min = this.value;
            if (fechaFin.value && fechaFin.value < this.value) {
                fechaFin.value = '';
            }
        });
    </script>
</body>
</html>