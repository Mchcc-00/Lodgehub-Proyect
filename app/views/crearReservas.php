<?php
require_once ('../../config/conexionGlobal.php');

// Obtener la conexión PDO
$pdo = conexionDB();

if (!$pdo) {
    die("Error: No se pudo establecer la conexión a la base de datos.");
}

$mensaje = '';
$tipoMensaje = '';

// VALIDACIÓN: Asegurarse de que un hotel ha sido seleccionado
$hotelSeleccionado = isset($_SESSION['hotel_id']) && !empty($_SESSION['hotel_id']);
$hotel_id_sesion = $_SESSION['hotel_id'] ?? null;
$hotel_nombre_sesion = $_SESSION['hotel_nombre'] ?? 'No asignado';

// Procesar el formulario cuando se envía
if ($_POST) {
    try {
        // Validar datos requeridos
        if (empty($_POST['pagoFinal']) || empty($_POST['fechainicio']) || empty($_POST['fechaFin']) || 
            empty($_POST['motivoReserva']) || empty($_POST['id_habitacion']) || empty($_POST['metodoPago']) ||
            empty($_POST['us_numDocumento']) || empty($_POST['hue_numDocumento']) || empty($_POST['estado']) ||
            empty($hotel_id_sesion)) {
            throw new Exception("Todos los campos obligatorios deben ser completados.");
        }
        
        // Validar fechas
        if (strtotime($_POST['fechainicio']) >= strtotime($_POST['fechaFin'])) {
            throw new Exception("La fecha de inicio debe ser anterior a la fecha de fin.");
        }
        
        if (strtotime($_POST['fechainicio']) < strtotime(date('Y-m-d'))) {
            throw new Exception("La fecha de inicio no puede ser anterior a la fecha actual.");
        }
        
        // Validar que la habitación pertenezca al hotel seleccionado
        $stmtValidacion = $pdo->prepare("SELECT id FROM tp_habitaciones WHERE id = :id_habitacion AND id_hotel = :id_hotel");
        $stmtValidacion->execute([
            ':id_habitacion' => intval($_POST['id_habitacion']),
            ':id_hotel' => intval($hotel_id_sesion)
        ]);
        
        if (!$stmtValidacion->fetch()) {
            throw new Exception("La habitación seleccionada no pertenece al hotel actual.");
        }
        
        // Verificar que no haya conflictos de fechas para la habitación
        $stmtConflicto = $pdo->prepare("
            SELECT COUNT(*) FROM tp_reservas 
            WHERE id_habitacion = :id_habitacion 
            AND estado IN ('Activa', 'Pendiente')
            AND ((fechainicio <= :fecha_inicio AND fechaFin > :fecha_inicio) 
                OR (fechainicio < :fecha_fin AND fechaFin >= :fecha_fin)
                OR (fechainicio >= :fecha_inicio AND fechaFin <= :fecha_fin))
        ");
        
        $stmtConflicto->execute([
            ':id_habitacion' => intval($_POST['id_habitacion']),
            ':fecha_inicio' => $_POST['fechainicio'],
            ':fecha_fin' => $_POST['fechaFin']
        ]);
        
        if ($stmtConflicto->fetchColumn() > 0) {
            throw new Exception("La habitación ya está reservada para las fechas seleccionadas.");
        }
        
        // Preparar la consulta de inserción
        $sql = "INSERT INTO tp_reservas (
                    pagoFinal, fechainicio, fechaFin, cantidadAdultos, cantidadNinos, 
                    cantidadDiscapacitados, motivoReserva, id_habitacion, metodoPago, 
                    informacionAdicional, us_numDocumento, hue_numDocumento, estado, id_hotel
                ) VALUES (
                    :pagoFinal, :fechainicio, :fechaFin, :cantidadAdultos, :cantidadNinos,
                    :cantidadDiscapacitados, :motivoReserva, :id_habitacion, :metodoPago,
                    :informacionAdicional, :us_numDocumento, :hue_numDocumento, :estado, :id_hotel
                )";
        
        $stmt = $pdo->prepare($sql);
        
        // Ejecutar la consulta con los datos del formulario
        $resultado = $stmt->execute([
            ':pagoFinal' => floatval($_POST['pagoFinal']),
            ':fechainicio' => $_POST['fechainicio'],
            ':fechaFin' => $_POST['fechaFin'],
            ':cantidadAdultos' => intval($_POST['cantidadAdultos'] ?? 1),
            ':cantidadNinos' => intval($_POST['cantidadNinos'] ?? 0),
            ':cantidadDiscapacitados' => intval($_POST['cantidadDiscapacitados'] ?? 0),
            ':motivoReserva' => $_POST['motivoReserva'],
            ':id_habitacion' => intval($_POST['id_habitacion']),
            ':metodoPago' => $_POST['metodoPago'],
            ':informacionAdicional' => $_POST['informacionAdicional'] ?? null,
            ':us_numDocumento' => $_POST['us_numDocumento'],
            ':hue_numDocumento' => $_POST['hue_numDocumento'],
            ':estado' => $_POST['estado'],
            ':id_hotel' => intval($hotel_id_sesion)
        ]);
        
        if ($resultado) {
            $mensaje = "Reserva creada exitosamente con ID: " . $pdo->lastInsertId();
            $tipoMensaje = "success";
            // Limpiar el formulario
            $_POST = [];
        } else {
            throw new Exception("Error al insertar la reserva en la base de datos.");
        }
        
    } catch(Exception $e) {
        $mensaje = "Error al crear la reserva: " . $e->getMessage();
        $tipoMensaje = "error";
    }
}

// Obtener datos para los selects
try {
    // Obtener habitaciones del hotel actual
    if ($hotelSeleccionado) {
        $stmtHabitaciones = $pdo->prepare("
            SELECT h.id, h.numero, h.tipo, h.precio_noche
            FROM tp_habitaciones h 
            WHERE h.id_hotel = :id_hotel 
            ORDER BY h.numero
        ");
        $stmtHabitaciones->execute([':id_hotel' => $hotel_id_sesion]);
        $habitaciones = $stmtHabitaciones->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $habitaciones = [];
    }
    
    // Obtener usuarios
    $stmtUsuarios = $pdo->query("SELECT numDocumento, nombres, apellidos FROM tp_usuarios ORDER BY nombres");
    $usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener huéspedes
    $stmtHuespedes = $pdo->query("SELECT numDocumento, nombres, apellidos FROM tp_huespedes ORDER BY nombres");
    $huespedes = $stmtHuespedes->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    $mensaje = "Error al cargar los datos: " . $e->getMessage();
    $tipoMensaje = "error";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nueva Reserva - Sistema de Gestión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet"> 
    <link href="../../public/assets/css/stylesReservas.css" rel="stylesheet"> 
</head>
<body>
    <?php
        include "layouts/sidebar.php";
        include "layouts/navbar.php";
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>

    <div class="container reservas-container">
        <?php if (!$hotelSeleccionado): ?>
            <div class="alert alert-danger mt-4" role="alert">
                <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> ¡Acción Requerida!</h4>
                <p>Para poder registrar una nueva reserva, primero debes <strong>seleccionar un hotel</strong> desde el panel principal (Home).</p>
                <hr>
                <p class="mb-0">Por favor, regresa al <a href="homepage.php" class="alert-link">Home</a> y elige el hotel donde deseas trabajar.</p>
            </div>
        <?php else: ?>

        <!-- Header Section -->
        <div class="header">
            <h1><i class="fas fa-calendar-plus"></i> Crear Nueva Reserva</h1>
            <p>Complete el formulario para registrar una nueva reserva en el sistema</p>
        </div>
        
        <!-- Messages Section -->
        <?php if ($mensaje): ?>
            <div class="alert alert-<?php echo $tipoMensaje === 'success' ? 'success' : 'danger'; ?>" role="alert">
                <i class="fas fa-<?php echo $tipoMensaje === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>
        
        <!-- Form Section -->
        <div class="form-section">
            <div class="form-header">
                <i class="fas fa-edit"></i>
                <h2>Información de la Reserva</h2>
            </div>
            
            <div class="form-body">
                <form method="POST" class="reservas-form" id="form-reserva">
                    <!-- Sección: Ubicación -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="cantidadNinos" class="form-label">
                                <i class="fas fa-child"></i>
                                Cantidad de Niños
                            </label>
                            <input type="number" name="cantidadNinos" id="cantidadNinos" class="form-control" 
                                   min="0" max="99" value="<?php echo $_POST['cantidadNinos'] ?? 0; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="cantidadDiscapacitados" class="form-label">
                                <i class="fas fa-wheelchair"></i>
                                Cantidad de Personas con Discapacidad
                            </label>
                            <input type="number" name="cantidadDiscapacitados" id="cantidadDiscapacitados" class="form-control" 
                                   min="0" max="99" value="<?php echo $_POST['cantidadDiscapacitados'] ?? 0; ?>">
                        </div>
                    </div>
                    
                    <!-- Sección: Personas -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="us_numDocumento" class="form-label">
                                <i class="fas fa-user-tie"></i>
                                Usuario <span class="required">*</span>
                            </label>
                            <select name="us_numDocumento" id="us_numDocumento" class="form-control" required>
                                <option value="">Seleccionar usuario</option>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <option value="<?php echo $usuario['numDocumento']; ?>" 
                                            <?php echo (isset($_POST['us_numDocumento']) && $_POST['us_numDocumento'] == $usuario['numDocumento']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos'] . ' (' . $usuario['numDocumento'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="hue_numDocumento" class="form-label">
                                <i class="fas fa-user-friends"></i>
                                Huésped <span class="required">*</span>
                            </label>
                            <select name="hue_numDocumento" id="hue_numDocumento" class="form-control" required>
                                <option value="">Seleccionar huésped</option>
                                <?php foreach ($huespedes as $huesped): ?>
                                    <option value="<?php echo $huesped['numDocumento']; ?>" 
                                            <?php echo (isset($_POST['hue_numDocumento']) && $_POST['hue_numDocumento'] == $huesped['numDocumento']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($huesped['nombres'] . ' ' . $huesped['apellidos'] . ' (' . $huesped['numDocumento'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Sección: Detalles de Reserva -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="motivoReserva" class="form-label">
                                <i class="fas fa-comment-alt"></i>
                                Motivo de la Reserva <span class="required">*</span>
                            </label>
                            <select name="motivoReserva" id="motivoReserva" class="form-control" required>
                                <option value="">Seleccionar motivo</option>
                                <option value="Negocios" <?php echo (isset($_POST['motivoReserva']) && $_POST['motivoReserva'] == 'Negocios') ? 'selected' : ''; ?>>Negocios</option>
                                <option value="Personal" <?php echo (isset($_POST['motivoReserva']) && $_POST['motivoReserva'] == 'Personal') ? 'selected' : ''; ?>>Personal</option>
                                <option value="Viaje" <?php echo (isset($_POST['motivoReserva']) && $_POST['motivoReserva'] == 'Viaje') ? 'selected' : ''; ?>>Viaje</option>
                                <option value="Familiar" <?php echo (isset($_POST['motivoReserva']) && $_POST['motivoReserva'] == 'Familiar') ? 'selected' : ''; ?>>Familiar</option>
                                <option value="Otro" <?php echo (isset($_POST['motivoReserva']) && $_POST['motivoReserva'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="metodoPago" class="form-label">
                                <i class="fas fa-credit-card"></i>
                                Método de Pago <span class="required">*</span>
                            </label>
                            <select name="metodoPago" id="metodoPago" class="form-control" required>
                                <option value="">Seleccionar método</option>
                                <option value="Tarjeta" <?php echo (isset($_POST['metodoPago']) && $_POST['metodoPago'] == 'Tarjeta') ? 'selected' : ''; ?>>Tarjeta</option>
                                <option value="Efectivo" <?php echo (isset($_POST['metodoPago']) && $_POST['metodoPago'] == 'Efectivo') ? 'selected' : ''; ?>>Efectivo</option>
                                <option value="PSE" <?php echo (isset($_POST['metodoPago']) && $_POST['metodoPago'] == 'PSE') ? 'selected' : ''; ?>>PSE</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Sección: Pago y Estado -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="pagoFinal" class="form-label">
                                <i class="fas fa-dollar-sign"></i>
                                Pago Final <span class="required">*</span>
                            </label>
                            <input type="number" name="pagoFinal" id="pagoFinal" class="form-control" 
                                   step="0.01" min="0" required value="<?php echo $_POST['pagoFinal'] ?? ''; ?>">
                            <small class="form-text text-muted" id="precio-sugerido"></small>
                        </div>
                        
                        <div class="form-group">
                            <label for="estado" class="form-label">
                                <i class="fas fa-info-circle"></i>
                                Estado <span class="required">*</span>
                            </label>
                            <select name="estado" id="estado" class="form-control" required>
                                <option value="">Seleccionar estado</option>
                                <option value="Activa" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'Activa') ? 'selected' : ''; ?>>Activa</option>
                                <option value="Cancelada" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'Cancelada') ? 'selected' : ''; ?>>Cancelada</option>
                                <option value="Finalizada" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'Finalizada') ? 'selected' : ''; ?>>Finalizada</option>
                                <option value="Pendiente" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Sección: Información Adicional -->
                    <div class="form-group full-width">
                        <label for="informacionAdicional" class="form-label">
                            <i class="fas fa-sticky-note"></i>
                            Información Adicional
                        </label>
                        <textarea name="informacionAdicional" id="informacionAdicional" class="form-control" rows="4" 
                                  placeholder="Escriba cualquier información adicional sobre la reserva..."><?php echo $_POST['informacionAdicional'] ?? ''; ?></textarea>
                    </div>
                    
                    <!-- Botones de Acción -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-success btn-lg" id="btn-crear">
                            <i class="fas fa-save"></i>
                            Crear Reserva
                        </button>
                        <a href="listarReservas.php" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; // Fin del bloque de validación ?>
    </div>
    
    <script>
        // Calcular precio sugerido basado en habitación y fechas
        function calcularPrecioSugerido() {
            const habitacionSelect = document.getElementById('id_habitacion');
            const fechaInicio = document.getElementById('fechainicio').value;
            const fechaFin = document.getElementById('fechaFin').value;
            const precioSugeridoDiv = document.getElementById('precio-sugerido');
            
            if (habitacionSelect.value && fechaInicio && fechaFin) {
                const precioNoche = parseFloat(habitacionSelect.selectedOptions[0].dataset.precio || 0);
                const inicio = new Date(fechaInicio);
                const fin = new Date(fechaFin);
                const noches = (fin - inicio) / (1000 * 60 * 60 * 24);
                
                if (noches > 0 && precioNoche > 0) {
                    const precioTotal = precioNoche * noches;
                    precioSugeridoDiv.innerHTML = `Precio sugerido: ${precioTotal.toLocaleString('es-CO')} (${noches} noches × ${precioNoche.toLocaleString('es-CO')})`;
                    precioSugeridoDiv.style.color = '#28a745';
                } else {
                    precioSugeridoDiv.innerHTML = '';
                }
            } else {
                precioSugeridoDiv.innerHTML = '';
            }
        }
        
        // Event listeners para cálculo automático
        document.getElementById('id_habitacion').addEventListener('change', calcularPrecioSugerido);
        document.getElementById('fechainicio').addEventListener('change', function() {
            validarFecha(this, true);
            calcularPrecioSugerido();
            // Actualizar fecha mínima para fecha fin
            const fechaFin = document.getElementById('fechaFin');
            if (this.value) {
                const fechaMinima = new Date(this.value);
                fechaMinima.setDate(fechaMinima.getDate() + 1);
                fechaFin.min = fechaMinima.toISOString().split('T')[0];
            }
        });
        document.getElementById('fechaFin').addEventListener('change', function() {
            validarFecha(this, false);
            calcularPrecioSugerido();
        });
        
        // Validar fechas en el cliente
        function validarFecha(elemento, esFechaInicio = true) {
            const fecha = new Date(elemento.value);
            const fechaComparacion = esFechaInicio ? 
                document.getElementById('fechaFin').value : 
                document.getElementById('fechainicio').value;
            
            if (fechaComparacion) {
                const fechaComp = new Date(fechaComparacion);
                
                if (esFechaInicio && fecha >= fechaComp) {
                    mostrarError(elemento, 'La fecha de inicio debe ser anterior a la fecha de fin');
                    return false;
                } else if (!esFechaInicio && fecha <= fechaComp) {
                    mostrarError(elemento, 'La fecha de fin debe ser posterior a la fecha de inicio');
                    return false;
                }
            }
            
            if (esFechaInicio && fecha < new Date()) {
                mostrarError(elemento, 'La fecha de inicio no puede ser anterior a hoy');
                return false;
            }
            
            limpiarError(elemento);
            return true;
        }
        
        function mostrarError(elemento, mensaje) {
            elemento.style.borderColor = '#dc3545';
            elemento.style.backgroundColor = 'rgba(220, 53, 69, 0.05)';
            
            // Remover mensaje anterior si existe
            const errorAnterior = elemento.parentNode.querySelector('.error-message');
            if (errorAnterior) {
                errorAnterior.remove();
            }
            
            // Crear nuevo mensaje de error
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.style.color = '#dc3545';
            errorDiv.style.fontSize = '0.875rem';
            errorDiv.style.marginTop = '0.25rem';
            errorDiv.textContent = mensaje;
            elemento.parentNode.appendChild(errorDiv);
        }
        
        function limpiarError(elemento) {
            elemento.style.borderColor = '#198754';
            elemento.style.backgroundColor = 'rgba(25, 135, 84, 0.05)';
            
            const errorMsg = elemento.parentNode.querySelector('.error-message');
            if (errorMsg) {
                errorMsg.remove();
            }
        }
        
        // Validación del formulario antes del envío
        document.getElementById('form-reserva').addEventListener('submit', function(e) {
            const fechaInicio = document.getElementById('fechainicio');
            const fechaFin = document.getElementById('fechaFin');
            const btnCrear = document.getElementById('btn-crear');
            
            // Deshabilitar botón para evitar doble envío
            btnCrear.disabled = true;
            btnCrear.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
            
            if (!validarFecha(fechaInicio, true) || !validarFecha(fechaFin, false)) {
                e.preventDefault();
                btnCrear.disabled = false;
                btnCrear.innerHTML = '<i class="fas fa-save"></i> Crear Reserva';
                
                // Scroll hacia el primer error
                const primerError = document.querySelector('.error-message');
                if (primerError) {
                    primerError.parentNode.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
        
        // Efecto de carga para el formulario
        document.addEventListener('DOMContentLoaded', function() {
            const formGroups = document.querySelectorAll('.form-group');
            
            formGroups.forEach((group, index) => {
                group.style.opacity = '0';
                group.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    group.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                    group.style.opacity = '1';
                    group.style.transform = 'translateY(0)';
                }, index * 50);
            });
        });
        
        // Mejorar la experiencia de usuario en campos numéricos
        const numericos = document.querySelectorAll('input[type="number"]');
        numericos.forEach(input => {
            input.addEventListener('focus', function() {
                this.style.backgroundColor = 'rgba(13, 110, 253, 0.05)';
            });
            
            input.addEventListener('blur', function() {
                this.style.backgroundColor = '';
            });
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
                            <label for="hotel_nombre" class="form-label">
                                <i class="fas fa-hotel"></i>
                                Hotel <span class="required">*</span>
                            </label>
                            <input type="text" id="hotel_nombre" name="hotel_nombre" class="form-control" 
                                   value="<?php echo htmlspecialchars($hotel_nombre_sesion); ?>" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="id_habitacion" class="form-label">
                                <i class="fas fa-bed"></i>
                                Habitación <span class="required">*</span>
                            </label>
                            <select name="id_habitacion" id="id_habitacion" class="form-control" required>
                                <option value="">Seleccionar habitación</option>
                                <?php foreach ($habitaciones as $habitacion): ?>
                                    <option value="<?php echo $habitacion['id']; ?>" 
                                            data-precio="<?php echo $habitacion['precio_noche'] ?? 0; ?>"
                                            <?php echo (isset($_POST['id_habitacion']) && $_POST['id_habitacion'] == $habitacion['id']) ? 'selected' : ''; ?>>
                                        Hab. <?php echo htmlspecialchars($habitacion['numero'] . ' (' . $habitacion['tipo'] . ')'); ?>
                                        <?php if (isset($habitacion['precio_noche'])): ?>
                                            - $<?php echo number_format($habitacion['precio_noche'], 0, ',', '.'); ?>/noche
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Sección: Fechas -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="fechainicio" class="form-label">
                                <i class="fas fa-calendar-check"></i>
                                Fecha de Inicio <span class="required">*</span>
                            </label>
                            <input type="date" name="fechainicio" id="fechainicio" class="form-control" required 
                                   min="<?php echo date('Y-m-d'); ?>" 
                                   value="<?php echo $_POST['fechainicio'] ?? ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="fechaFin" class="form-label">
                                <i class="fas fa-calendar-times"></i>
                                Fecha de Fin <span class="required">*</span>
                            </label>
                            <input type="date" name="fechaFin" id="fechaFin" class="form-control" required 
                                   value="<?php echo $_POST['fechaFin'] ?? ''; ?>">
                        </div>
                    </div>
                    
                    <!-- Sección: Huéspedes -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="cantidadAdultos" class="form-label">
                                <i class="fas fa-users"></i>
                                Cantidad de Adultos
                            </label>
                            <input type="number" name="cantidadAdultos" id="cantidadAdultos" class="form-control" 
                                   min="1" max="99" value="<?php echo $_POST['cantidadAdultos'] ?? 1; ?>">
                        </div>
                        
                        <div class="form-group">