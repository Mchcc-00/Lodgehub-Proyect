<?php
require_once ('../../config/conexionGlobal.php');

// Obtener la conexión PDO
$pdo = conexionDB();

if (!$pdo) {
    die("Error: No se pudo establecer la conexión a la base de datos.");
}

$mensaje = '';
$tipoMensaje = '';

// Procesar el formulario cuando se envía
if ($_POST) {
    try {
        // Validar datos requeridos
        if (empty($_POST['pagoFinal']) || empty($_POST['fechainicio']) || empty($_POST['fechaFin']) || 
            empty($_POST['motivoReserva']) || empty($_POST['id_habitacion']) || empty($_POST['metodoPago']) ||
            empty($_POST['us_numDocumento']) || empty($_POST['hue_numDocumento']) || empty($_POST['estado']) ||
            empty($_POST['id_hotel'])) {
            throw new Exception("Todos los campos obligatorios deben ser completados.");
        }
        
        // Validar fechas
        if (strtotime($_POST['fechainicio']) >= strtotime($_POST['fechaFin'])) {
            throw new Exception("La fecha de inicio debe ser anterior a la fecha de fin.");
        }
        
        if (strtotime($_POST['fechainicio']) < time()) {
            throw new Exception("La fecha de inicio no puede ser anterior a la fecha actual.");
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
        $stmt->execute([
            ':pagoFinal' => floatval($_POST['pagoFinal']),
            ':fechainicio' => $_POST['fechainicio'],
            ':fechaFin' => $_POST['fechaFin'],
            ':cantidadAdultos' => intval($_POST['cantidadAdultos'] ?? 0),
            ':cantidadNinos' => intval($_POST['cantidadNinos'] ?? 0),
            ':cantidadDiscapacitados' => intval($_POST['cantidadDiscapacitados'] ?? 0),
            ':motivoReserva' => $_POST['motivoReserva'],
            ':id_habitacion' => intval($_POST['id_habitacion']),
            ':metodoPago' => $_POST['metodoPago'],
            ':informacionAdicional' => $_POST['informacionAdicional'] ?? null,
            ':us_numDocumento' => $_POST['us_numDocumento'],
            ':hue_numDocumento' => $_POST['hue_numDocumento'],
            ':estado' => $_POST['estado'],
            ':id_hotel' => intval($_POST['id_hotel'])
        ]);
        
        $mensaje = "Reserva creada exitosamente con ID: " . $pdo->lastInsertId();
        $tipoMensaje = "success";
        
    } catch(Exception $e) {
        $mensaje = "Error al crear la reserva: " . $e->getMessage();
        $tipoMensaje = "error";
    }
}

// Obtener datos para los selects
try {
    // Obtener hoteles
    $stmtHoteles = $pdo->query("SELECT id, nombre FROM tp_hotel ORDER BY nombre");
    $hoteles = $stmtHoteles->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener habitaciones
    $stmtHabitaciones = $pdo->query("SELECT h.id, h.numero, h.tipo, ho.nombre as hotel_nombre 
                                     FROM tp_habitaciones h 
                                     INNER JOIN tp_hotel ho ON h.id_hotel = ho.id 
                                     ORDER BY ho.nombre, h.numero");
    $habitaciones = $stmtHabitaciones->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener usuarios
    $stmtUsuarios = $pdo->query("SELECT numDocumento, nombre, apellido FROM tp_usuarios ORDER BY nombre");
    $usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener huéspedes
    $stmtHuespedes = $pdo->query("SELECT numDocumento, nombre, apellido FROM tp_huespedes ORDER BY nombre");
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
    <title>Crear Nueva Reserva</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-group {
            flex: 1;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input, select, textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        textarea {
            height: 80px;
            resize: vertical;
        }
        .required {
            color: red;
        }
        .btn {
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .btn-secondary:hover {
            background-color: #545b62;
        }
        .mensaje {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .form-actions {
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Crear Nueva Reserva</h1>
        
        <?php if ($mensaje): ?>
            <div class="mensaje <?php echo $tipoMensaje; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="id_hotel">Hotel <span class="required">*</span></label>
                    <select name="id_hotel" id="id_hotel" required>
                        <option value="">Seleccionar hotel</option>
                        <?php foreach ($hoteles as $hotel): ?>
                            <option value="<?php echo $hotel['id']; ?>" <?php echo (isset($_POST['id_hotel']) && $_POST['id_hotel'] == $hotel['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($hotel['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="id_habitacion">Habitación <span class="required">*</span></label>
                    <select name="id_habitacion" id="id_habitacion" required>
                        <option value="">Seleccionar habitación</option>
                        <?php foreach ($habitaciones as $habitacion): ?>
                            <option value="<?php echo $habitacion['id']; ?>" <?php echo (isset($_POST['id_habitacion']) && $_POST['id_habitacion'] == $habitacion['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($habitacion['hotel_nombre'] . ' - Hab. ' . $habitacion['numero'] . ' (' . $habitacion['tipo'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="fechainicio">Fecha de Inicio <span class="required">*</span></label>
                    <input type="date" name="fechainicio" id="fechainicio" required value="<?php echo $_POST['fechainicio'] ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label for="fechaFin">Fecha de Fin <span class="required">*</span></label>
                    <input type="date" name="fechaFin" id="fechaFin" required value="<?php echo $_POST['fechaFin'] ?? ''; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="cantidadAdultos">Cantidad de Adultos</label>
                    <input type="number" name="cantidadAdultos" id="cantidadAdultos" min="0" max="99" value="<?php echo $_POST['cantidadAdultos'] ?? 1; ?>">
                </div>
                <div class="form-group">
                    <label for="cantidadNinos">Cantidad de Niños</label>
                    <input type="number" name="cantidadNinos" id="cantidadNinos" min="0" max="99" value="<?php echo $_POST['cantidadNinos'] ?? 0; ?>">
                </div>
                <div class="form-group">
                    <label for="cantidadDiscapacitados">Cantidad de Discapacitados</label>
                    <input type="number" name="cantidadDiscapacitados" id="cantidadDiscapacitados" min="0" max="99" value="<?php echo $_POST['cantidadDiscapacitados'] ?? 0; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="us_numDocumento">Usuario <span class="required">*</span></label>
                    <select name="us_numDocumento" id="us_numDocumento" required>
                        <option value="">Seleccionar usuario</option>
                        <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?php echo $usuario['numDocumento']; ?>" <?php echo (isset($_POST['us_numDocumento']) && $_POST['us_numDocumento'] == $usuario['numDocumento']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido'] . ' (' . $usuario['numDocumento'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="hue_numDocumento">Huésped <span class="required">*</span></label>
                    <select name="hue_numDocumento" id="hue_numDocumento" required>
                        <option value="">Seleccionar huésped</option>
                        <?php foreach ($huespedes as $huesped): ?>
                            <option value="<?php echo $huesped['numDocumento']; ?>" <?php echo (isset($_POST['hue_numDocumento']) && $_POST['hue_numDocumento'] == $huesped['numDocumento']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($huesped['nombre'] . ' ' . $huesped['apellido'] . ' (' . $huesped['numDocumento'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="motivoReserva">Motivo de la Reserva <span class="required">*</span></label>
                    <select name="motivoReserva" id="motivoReserva" required>
                        <option value="">Seleccionar motivo</option>
                        <option value="Negocios" <?php echo (isset($_POST['motivoReserva']) && $_POST['motivoReserva'] == 'Negocios') ? 'selected' : ''; ?>>Negocios</option>
                        <option value="Personal" <?php echo (isset($_POST['motivoReserva']) && $_POST['motivoReserva'] == 'Personal') ? 'selected' : ''; ?>>Personal</option>
                        <option value="Viaje" <?php echo (isset($_POST['motivoReserva']) && $_POST['motivoReserva'] == 'Viaje') ? 'selected' : ''; ?>>Viaje</option>
                        <option value="Familiar" <?php echo (isset($_POST['motivoReserva']) && $_POST['motivoReserva'] == 'Familiar') ? 'selected' : ''; ?>>Familiar</option>
                        <option value="Otro" <?php echo (isset($_POST['motivoReserva']) && $_POST['motivoReserva'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="metodoPago">Método de Pago <span class="required">*</span></label>
                    <select name="metodoPago" id="metodoPago" required>
                        <option value="">Seleccionar método</option>
                        <option value="Tarjeta" <?php echo (isset($_POST['metodoPago']) && $_POST['metodoPago'] == 'Tarjeta') ? 'selected' : ''; ?>>Tarjeta</option>
                        <option value="Efectivo" <?php echo (isset($_POST['metodoPago']) && $_POST['metodoPago'] == 'Efectivo') ? 'selected' : ''; ?>>Efectivo</option>
                        <option value="PSE" <?php echo (isset($_POST['metodoPago']) && $_POST['metodoPago'] == 'PSE') ? 'selected' : ''; ?>>PSE</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="pagoFinal">Pago Final <span class="required">*</span></label>
                    <input type="number" name="pagoFinal" id="pagoFinal" step="0.01" min="0" required value="<?php echo $_POST['pagoFinal'] ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label for="estado">Estado <span class="required">*</span></label>
                    <select name="estado" id="estado" required>
                        <option value="">Seleccionar estado</option>
                        <option value="Activa" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'Activa') ? 'selected' : ''; ?>>Activa</option>
                        <option value="Cancelada" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'Cancelada') ? 'selected' : ''; ?>>Cancelada</option>
                        <option value="Finalizada" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'Finalizada') ? 'selected' : ''; ?>>Finalizada</option>
                        <option value="Pendiente" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="informacionAdicional">Información Adicional</label>
                <textarea name="informacionAdicional" id="informacionAdicional" placeholder="Información adicional sobre la reserva..."><?php echo $_POST['informacionAdicional'] ?? ''; ?></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn">Crear Reserva</button>
                <a href="listarReservas.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
    
    <script>
        // Script para filtrar habitaciones por hotel seleccionado
        document.getElementById('id_hotel').addEventListener('change', function() {
            const hotelId = this.value;
            const habitacionSelect = document.getElementById('id_habitacion');
            const options = habitacionSelect.getElementsByTagName('option');
            
            for (let i = 1; i < options.length; i++) {
                const option = options[i];
                const text = option.text;
                
                if (hotelId === '') {
                    option.style.display = 'block';
                } else {
                    // Esta es una implementación básica. Idealmente deberías hacer una llamada AJAX
                    // para obtener solo las habitaciones del hotel seleccionado
                    option.style.display = 'block';
                }
            }
            
            habitacionSelect.value = '';
        });
        
        // Validar fechas en el cliente
        document.getElementById('fechainicio').addEventListener('change', function() {
            const fechaInicio = new Date(this.value);
            const fechaFin = document.getElementById('fechaFin');
            
            if (fechaFin.value && fechaInicio >= new Date(fechaFin.value)) {
                alert('La fecha de inicio debe ser anterior a la fecha de fin');
                this.value = '';
            }
        });
        
        document.getElementById('fechaFin').addEventListener('change', function() {
            const fechaFin = new Date(this.value);
            const fechaInicio = document.getElementById('fechainicio');
            
            if (fechaInicio.value && fechaFin <= new Date(fechaInicio.value)) {
                alert('La fecha de fin debe ser posterior a la fecha de inicio');
                this.value = '';
            }
        });
    </script>
</body>
</html>