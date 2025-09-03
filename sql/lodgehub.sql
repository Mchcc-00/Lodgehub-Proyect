-- =============================================
-- INSERTS DE DATOS DE EJEMPLO
-- =============================================

-- Insertar tipos de habitación
INSERT INTO td_tipoHabitacion (descripcion, cantidad) VALUES 
('Individual', 10),
('Doble', 15),
('Suite', 5),
('Familiar', 8),
('Ejecutiva', 6);

-- Insertar usuarios
INSERT INTO tp_usuarios (numDocumento, tipoDocumento, nombres, apellidos, numTelefono, correo, sexo, fechaNacimiento, password, roles) VALUES 
('1234567890', 'Cédula de Ciudadanía', 'Juan Carlos', 'Pérez García', '3001234567', 'admin@lodgehub.com', 'Hombre', '1985-03-15', '$2y$10$hashedpassword1', 'Administrador'),
('9876543210', 'Cédula de Ciudadanía', 'María Elena', 'González López', '3009876543', 'maria.gonzalez@lodgehub.com', 'Mujer', '1990-07-22', '$2y$10$hashedpassword2', 'Colaborador'),
('5555666677', 'Cédula de Ciudadanía', 'Carlos Alberto', 'Rodríguez Martín', '3005556666', 'carlos.rodriguez@lodgehub.com', 'Hombre', '1988-11-10', '$2y$10$hashedpassword3', 'Colaborador'),
('1111222233', 'Cédula de Ciudadanía', 'Ana Patricia', 'Morales Ruiz', '3001112222', 'ana.morales@lodgehub.com', 'Mujer', '1992-05-18', '$2y$10$hashedpassword4', 'Usuario'),
('7777888899', 'Pasaporte', 'Roberto', 'Silva Santos', '3007778888', 'roberto.silva@email.com', 'Hombre', '1987-09-25', '$2y$10$hashedpassword5', 'Usuario');

-- Insertar hotel
INSERT INTO tp_hotel (nit, nombre, direccion, telefono, correo, descripcion, numDocumentoAdmin) VALUES 
('900123456-1', 'Hotel Lodge Hub Premium', 'Calle 123 #45-67, Bogotá, Colombia', '6013334444', 'info@lodgehub.com', 'Hotel de lujo ubicado en el corazón de la ciudad, ofreciendo servicios de alta calidad y comodidad excepcional.', '1234567890');

-- Insertar personal del hotel
INSERT INTO ti_personal (id_hotel, numDocumento, roles) VALUES 
(1, '1234567890', 'Administrador General'),
(1, '9876543210', 'Recepcionista'),
(1, '5555666677', 'Mantenimiento');

-- Insertar habitaciones
INSERT INTO tp_habitaciones (numero, costo, capacidad, tipoHabitacion, descripcion, estado) VALUES 
('101', 80000.00, 1, 1, 'Habitación individual con baño privado, TV, WiFi', 'Disponible'),
('102', 80000.00, 1, 1, 'Habitación individual con vista a la ciudad', 'Disponible'),
('201', 120000.00, 2, 2, 'Habitación doble con cama matrimonial, minibar', 'Disponible'),
('202', 120000.00, 2, 2, 'Habitación doble con dos camas individuales', 'Ocupada'),
('301', 250000.00, 2, 3, 'Suite ejecutiva con sala, jacuzzi, balcón', 'Disponible'),
('401', 180000.00, 4, 4, 'Habitación familiar con litera y cama matrimonial', 'Disponible'),
('501', 200000.00, 2, 5, 'Habitación ejecutiva con escritorio y sala de reuniones', 'Mantenimiento');

-- Insertar huéspedes
INSERT INTO tp_huespedes (numDocumento, numTelefono, correo, nombres, apellidos, tipoDocumento, sexo) VALUES 
('4444555566', '3004445555', 'luis.martinez@email.com', 'Luis Fernando', 'Martínez Castro', 'Cedula de Ciudadania', 'Hombre'),
('6666777788', '3006667777', 'sofia.hernandez@email.com', 'Sofía Isabel', 'Hernández Vega', 'Cedula de Ciudadania', 'Mujer'),
('8888999900', '3008889999', 'pedro.jimenez@email.com', 'Pedro Antonio', 'Jiménez Flores', 'Cedula de Ciudadania', 'Hombre'),
('2222333344', '3002223333', 'laura.torres@email.com', 'Laura Cristina', 'Torres Mendoza', 'Cedula de Ciudadania', 'Mujer'),
('9999000011', '3009990000', 'miguel.vargas@email.com', 'Miguel Ángel', 'Vargas Pineda', 'Pasaporte', 'Hombre');

-- Insertar reservas
INSERT INTO tp_reservas (pagoFinal, fechainicio, fechaFin, cantidadAdultos, cantidadNinos, cantidadDiscapacitados, motivoReserva, numeroHabitacion, metodoPago, informacionAdicional, us_numDocumento, hue_numDocumento, estado) VALUES 
(240000.00, '2025-09-10', '2025-09-12', 2, 0, 0, 'Personal', '202', 'Tarjeta', 'Luna de miel', '1111222233', '4444555566', 'Activa'),
(500000.00, '2025-09-15', '2025-09-17', 2, 0, 0, 'Negocios', '301', 'PSE', 'Reunión empresarial', '7777888899', '6666777788', 'Activa'),
(160000.00, '2025-09-05', '2025-09-07', 1, 0, 0, 'Personal', '101', 'Efectivo', NULL, '1111222233', '8888999900', 'Finalizada'),
(360000.00, '2025-09-20', '2025-09-22', 3, 1, 0, 'Familiar', '401', 'Tarjeta', 'Vacaciones familiares', '7777888899', '2222333344', 'Pendiente');

-- Insertar PQRS
INSERT INTO tp_pqrs (tipo, descripcion, numdocumento, prioridad, categoria, estado) VALUES 
('Quejas', 'El aire acondicionado de la habitación 202 no funcionaba correctamente durante mi estadía.', '1111222233', 'Alto', 'Habitación', 'Finalizado'),
('Sugerencias', 'Sería genial si pudieran agregar más opciones vegetarianas en el menú del restaurante.', '7777888899', 'Bajo', 'Servicio', 'Pendiente'),
('Felicitaciones', 'Excelente atención del personal de recepción, muy amables y profesionales.', '1111222233', 'Bajo', 'Atención', 'Finalizado'),
('Peticiones', 'Solicito información sobre descuentos para estadías prolongadas.', '7777888899', 'Bajo', 'Otro', 'Pendiente');

-- Insertar mantenimientos
INSERT INTO tp_mantenimiento (numeroHabitacion, tipo, problemaDescripcion, frecuencia, cantFrecuencia, prioridad, numDocumento, estado) VALUES 
('501', 'Eléctrico', 'Falla en el sistema de iluminación LED', 'No', 'Diario', 'Alto', '5555666677', 'Pendiente'),
('202', 'Estructura', 'Aire acondicionado requiere limpieza filtros', 'Sí', 'Mensual', 'Bajo', '5555666677', 'Finalizado'),
('301', 'Limpieza', 'Limpieza profunda de alfombras', 'Sí', 'Quincenal', 'Bajo', '9876543210', 'Pendiente');

-- Insertar facturas
INSERT INTO tp_factura (infoReserva, infoHotel, total) VALUES 
(3, 1, 160000.00),
(1, 1, 240000.00);

-- =============================================
-- VISTAS
-- =============================================

-- Vista: Información completa de reservas
CREATE VIEW v_reservas_completas AS
SELECT 
    r.id AS reserva_id,
    r.fechainicio,
    r.fechaFin,
    r.pagoFinal,
    r.estado AS estado_reserva,
    h.numero AS habitacion,
    h.costo AS costo_habitacion,
    th.descripcion AS tipo_habitacion,
    hu.nombres AS huesped_nombres,
    hu.apellidos AS huesped_apellidos,
    hu.numDocumento AS huesped_documento,
    hu.correo AS huesped_correo,
    u.nombres AS usuario_nombres,
    u.apellidos AS usuario_apellidos,
    r.cantidadAdultos,
    r.cantidadNinos,
    r.motivoReserva,
    r.metodoPago,
    DATEDIFF(r.fechaFin, r.fechainicio) AS dias_estancia
FROM tp_reservas r
JOIN tp_habitaciones h ON r.numeroHabitacion = h.numero
JOIN td_tipoHabitacion th ON h.tipoHabitacion = th.id
JOIN tp_huespedes hu ON r.hue_numDocumento = hu.numDocumento
JOIN tp_usuarios u ON r.us_numDocumento = u.numDocumento;

-- Vista: Dashboard de habitaciones
CREATE VIEW v_dashboard_habitaciones AS
SELECT 
    h.numero,
    h.estado,
    h.costo,
    h.capacidad,
    th.descripcion AS tipo_habitacion,
    CASE 
        WHEN h.estado = 'Ocupada' THEN r.fechaFin
        ELSE NULL 
    END AS fecha_liberacion,
    CASE 
        WHEN h.estado = 'Mantenimiento' THEN m.problemaDescripcion
        ELSE NULL 
    END AS motivo_mantenimiento
FROM tp_habitaciones h
LEFT JOIN td_tipoHabitacion th ON h.tipoHabitacion = th.id
LEFT JOIN tp_reservas r ON h.numero = r.numeroHabitacion 
    AND r.estado = 'Activa' 
    AND CURDATE() BETWEEN r.fechainicio AND r.fechaFin
LEFT JOIN tp_mantenimiento m ON h.numero = m.numeroHabitacion 
    AND m.estado = 'Pendiente'
    AND h.estado = 'Mantenimiento';

-- Vista: Estadísticas de ocupación
CREATE VIEW v_estadisticas_ocupacion AS
SELECT 
    th.descripcion AS tipo_habitacion,
    COUNT(h.numero) AS total_habitaciones,
    SUM(CASE WHEN h.estado = 'Disponible' THEN 1 ELSE 0 END) AS disponibles,
    SUM(CASE WHEN h.estado = 'Ocupada' THEN 1 ELSE 0 END) AS ocupadas,
    SUM(CASE WHEN h.estado = 'Reservada' THEN 1 ELSE 0 END) AS reservadas,
    SUM(CASE WHEN h.estado = 'Mantenimiento' THEN 1 ELSE 0 END) AS mantenimiento,
    ROUND((SUM(CASE WHEN h.estado IN ('Ocupada', 'Reservada') THEN 1 ELSE 0 END) / COUNT(h.numero)) * 100, 2) AS porcentaje_ocupacion
FROM tp_habitaciones h
JOIN td_tipoHabitacion th ON h.tipoHabitacion = th.id
GROUP BY th.id, th.descripcion;

-- Vista: PQRS pendientes con información del usuario
CREATE VIEW v_pqrs_pendientes AS
SELECT 
    p.id,
    p.fechaRegistro,
    p.fechaLimite,
    p.tipo,
    p.categoria,
    p.prioridad,
    p.descripcion,
    u.nombres,
    u.apellidos,
    u.correo,
    u.numTelefono,
    DATEDIFF(p.fechaLimite, CURDATE()) AS dias_restantes
FROM tp_pqrs p
JOIN tp_usuarios u ON p.numdocumento = u.numDocumento
WHERE p.estado = 'Pendiente'
ORDER BY p.prioridad DESC, p.fechaLimite ASC;

-- Vista: Mantenimientos activos
CREATE VIEW v_mantenimientos_activos AS
SELECT 
    m.id,
    m.numeroHabitacion,
    m.tipo,
    m.problemaDescripcion,
    m.fechaRegistro,
    m.prioridad,
    u.nombres AS tecnico_nombres,
    u.apellidos AS tecnico_apellidos,
    h.estado AS estado_habitacion,
    DATEDIFF(CURDATE(), m.fechaRegistro) AS dias_pendiente
FROM tp_mantenimiento m
JOIN tp_usuarios u ON m.numDocumento = u.numDocumento
JOIN tp_habitaciones h ON m.numeroHabitacion = h.numero
WHERE m.estado = 'Pendiente'
ORDER BY m.prioridad DESC, m.fechaRegistro ASC;

-- Vista: Ingresos por período
CREATE VIEW v_ingresos_mensuales AS
SELECT 
    YEAR(r.fechaRegistro) AS año,
    MONTH(r.fechaRegistro) AS mes,
    MONTHNAME(r.fechaRegistro) AS nombre_mes,
    COUNT(r.id) AS total_reservas,
    SUM(r.pagoFinal) AS ingresos_total,
    AVG(r.pagoFinal) AS ingreso_promedio,
    SUM(CASE WHEN r.estado = 'Activa' THEN r.pagoFinal ELSE 0 END) AS ingresos_activos,
    SUM(CASE WHEN r.estado = 'Cancelada' THEN r.pagoFinal ELSE 0 END) AS ingresos_cancelados
FROM tp_reservas r
GROUP BY YEAR(r.fechaRegistro), MONTH(r.fechaRegistro)
ORDER BY año DESC, mes DESC;

-- =============================================
-- ÍNDICES PARA OPTIMIZACIÓN
-- =============================================

-- Índices en tp_usuarios
CREATE INDEX idx_usuarios_correo ON tp_usuarios(correo);
CREATE INDEX idx_usuarios_roles ON tp_usuarios(roles);
CREATE INDEX idx_usuarios_tipo_documento ON tp_usuarios(tipoDocumento);

-- Índices en tp_habitaciones
CREATE INDEX idx_habitaciones_estado ON tp_habitaciones(estado);
CREATE INDEX idx_habitaciones_tipo ON tp_habitaciones(tipoHabitacion);
CREATE INDEX idx_habitaciones_costo ON tp_habitaciones(costo);

-- Índices en tp_reservas
CREATE INDEX idx_reservas_fechas ON tp_reservas(fechainicio, fechaFin);
CREATE INDEX idx_reservas_estado ON tp_reservas(estado);
CREATE INDEX idx_reservas_huesped ON tp_reservas(hue_numDocumento);
CREATE INDEX idx_reservas_usuario ON tp_reservas(us_numDocumento);
CREATE INDEX idx_reservas_habitacion ON tp_reservas(numeroHabitacion);
CREATE INDEX idx_reservas_fecha_registro ON tp_reservas(fechaRegistro);

-- Índices en tp_huespedes
CREATE INDEX idx_huespedes_nombres ON tp_huespedes(nombres, apellidos);
CREATE INDEX idx_huespedes_tipo_doc ON tp_huespedes(tipoDocumento);
CREATE INDEX idx_huespedes_fecha_creacion ON tp_huespedes(fechaCreacion);

-- Índices en tp_pqrs
CREATE INDEX idx_pqrs_estado ON tp_pqrs(estado);
CREATE INDEX idx_pqrs_tipo ON tp_pqrs(tipo);
CREATE INDEX idx_pqrs_prioridad ON tp_pqrs(prioridad);
CREATE INDEX idx_pqrs_fecha_limite ON tp_pqrs(fechaLimite);
CREATE INDEX idx_pqrs_categoria ON tp_pqrs(categoria);

-- Índices en tp_mantenimiento
CREATE INDEX idx_mantenimiento_estado ON tp_mantenimiento(estado);
CREATE INDEX idx_mantenimiento_habitacion ON tp_mantenimiento(numeroHabitacion);
CREATE INDEX idx_mantenimiento_tipo ON tp_mantenimiento(tipo);
CREATE INDEX idx_mantenimiento_prioridad ON tp_mantenimiento(prioridad);
CREATE INDEX idx_mantenimiento_fecha ON tp_mantenimiento(fechaRegistro);

-- Índices en tp_factura
CREATE INDEX idx_factura_fecha ON tp_factura(fechaFactura);
CREATE INDEX idx_factura_hotel ON tp_factura(infoHotel);

-- Índices compuestos para consultas frecuentes
CREATE INDEX idx_reservas_activas ON tp_reservas(estado, fechainicio, fechaFin);
CREATE INDEX idx_habitaciones_disponibles ON tp_habitaciones(estado, tipoHabitacion);
CREATE INDEX idx_pqrs_pendientes ON tp_pqrs(estado, prioridad, fechaLimite);

-- =============================================
-- CONSULTAS DE EJEMPLO PARA PROBAR LAS VISTAS
-- =============================================

-- Consultar todas las reservas activas
-- SELECT * FROM v_reservas_completas WHERE estado_reserva = 'Activa';

-- Ver dashboard de habitaciones
-- SELECT * FROM v_dashboard_habitaciones;

-- Estadísticas de ocupación
-- SELECT * FROM v_estadisticas_ocupacion;

-- PQRS pendientes urgentes
-- SELECT * FROM v_pqrs_pendientes WHERE dias_restantes <= 2;

-- Mantenimientos prioritarios
-- SELECT * FROM v_mantenimientos_activos WHERE prioridad = 'Alto';

-- Ingresos del mes actual
-- SELECT * FROM v_ingresos_mensuales WHERE año = YEAR(CURDATE()) AND mes = MONTH(CURDATE());