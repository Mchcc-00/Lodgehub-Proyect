CREATE DATABASE IF NOT EXISTS Lodgehub;
USE Lodgehub;

-- Tabla usuarios (debe crearse primero)
CREATE TABLE IF NOT EXISTS tp_usuarios (
    numDocumento VARCHAR(15) NOT NULL,
    tipoDocumento ENUM ('Cédula de Ciudadanía','Tarjeta de Identidad','Cedula de Extranjeria','Pasaporte','Registro Civil') NOT NULL, 
    nombres VARCHAR(50) NOT NULL,
    apellidos VARCHAR(50) NOT NULL,
    numTelefono VARCHAR(15) NOT NULL,
    correo VARCHAR(255) NOT NULL,
    sexo ENUM ('Hombre','Mujer','Otro','Prefiero no decirlo') NOT NULL,
    fechaNacimiento DATE NOT NULL,
    password VARCHAR(255) NOT NULL,
    foto VARCHAR(255),
    solicitarContraseña ENUM('0','1') DEFAULT '0',
    tokenPassword VARCHAR(100),
    sesionCaducada ENUM('1','0') DEFAULT '1',
    roles ENUM ('Administrador','Colaborador','Usuario') NOT NULL,
    
    PRIMARY KEY (numDocumento)          
) ENGINE=INNODB;

-- Tabla hotel (corregida - removida FK inexistente)
CREATE TABLE IF NOT EXISTS tp_hotel (
    id INT(3) AUTO_INCREMENT NOT NULL,
    nit VARCHAR(20) UNIQUE NOT NULL,    -- NIT único pero no PK
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(200),
    telefono VARCHAR(15),
    correo VARCHAR(100),
    foto VARCHAR(255),
    descripcion TEXT,
    -- Si necesitas asociar hotel con usuario administrador, agrega:
    numDocumentoAdmin VARCHAR(15),

    PRIMARY KEY (id),
    FOREIGN KEY (numDocumentoAdmin) REFERENCES tp_usuarios (numDocumento)
) ENGINE=INNODB; 

-- Tabla personal (CORREGIDA)
CREATE TABLE IF NOT EXISTS ti_personal (
    id_hotel INT(3) NOT NULL,
    numDocumento VARCHAR(15) NOT NULL,  -- CORREGIDO: debe ser VARCHAR como en tp_usuarios
    roles TEXT NOT NULL,

    PRIMARY KEY (id_hotel, numDocumento),
    FOREIGN KEY (id_hotel) REFERENCES tp_hotel (id),
    FOREIGN KEY (numDocumento) REFERENCES tp_usuarios (numDocumento)
) ENGINE=INNODB;  -- CORREGIDO: removida coma extra

-- Tabla huéspedes
CREATE TABLE IF NOT EXISTS tp_huespedes (
    numDocumento VARCHAR(15) NOT NULL,
    numTelefono VARCHAR(15) NOT NULL,
    correo VARCHAR(30) NOT NULL,
    nombres VARCHAR(50) NOT NULL,
    apellidos VARCHAR(50) NOT NULL,
    tipoDocumento ENUM ('Cedula de Ciudadania','Tarjeta de Identidad','Cedula de Extranjeria','Pasaporte','Registro Civil') NOT NULL,
    sexo ENUM ('Hombre','Mujer','Otro','Prefiero no decirlo') NOT NULL,
    -- NUEVO: Campos de auditoría
    fechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fechaActualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (numDocumento),
    UNIQUE KEY uk_correo (correo)
) ENGINE=INNODB;

-- Tabla tipo habitación
CREATE TABLE IF NOT EXISTS td_tipoHabitacion (
    id INT(3) AUTO_INCREMENT NOT NULL,
    descripcion VARCHAR(20) NOT NULL,
    cantidad INT(3) NOT NULL DEFAULT 0,

    PRIMARY KEY (id)
) ENGINE=INNODB;

-- Tabla habitaciones
CREATE TABLE IF NOT EXISTS tp_habitaciones (
    numero VARCHAR(5) NOT NULL,
    costo DECIMAL(10,2) NOT NULL, 
    capacidad INT(3) NOT NULL,
    tipoHabitacion INT(3) NOT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    descripcion TEXT DEFAULT NULL,
    estado ENUM ('Disponible', 'Reservada', 'Ocupada', 'Mantenimiento') NOT NULL DEFAULT 'Disponible',
    descripcionMantenimiento TEXT DEFAULT NULL,
    estadoMantenimiento ENUM ('Activo','Inactivo') NOT NULL DEFAULT 'Activo',

    PRIMARY KEY (numero),
    UNIQUE KEY uk_numero (numero),
    FOREIGN KEY (tipoHabitacion) REFERENCES td_tipohabitacion (id)
) ENGINE=INNODB;

-- Tabla PQRS
CREATE TABLE IF NOT EXISTS tp_pqrs (
    id INT(10) AUTO_INCREMENT NOT NULL,
    fechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    fechaLimite DATE DEFAULT (DATE_ADD(CURRENT_DATE, INTERVAL 5 DAY)),
    tipo ENUM ('Peticiones','Quejas','Reclamos','Sugerencias','Felicitaciones') NOT NULL,
    descripcion TEXT NOT NULL,
    numdocumento VARCHAR(15) NOT NULL,
    prioridad ENUM ('Bajo','Alto') NOT NULL,
    categoria ENUM ('Servicio','Habitación','Atención','Otro') NOT NULL,
    estado ENUM ('Pendiente', 'Finalizado') NOT NULL,
    fechaFinalizacion DATETIME DEFAULT NULL,
    respuesta TEXT DEFAULT NULL,

    PRIMARY KEY (id),
    FOREIGN KEY (numdocumento) REFERENCES tp_usuarios (numDocumento)
) ENGINE=INNODB;

-- Tabla reservas (CORREGIDA)
CREATE TABLE IF NOT EXISTS tp_reservas (
    id INT(3) AUTO_INCREMENT NOT NULL,
    pagoFinal DECIMAL(30,2) NOT NULL, -- se multiplica la habitación por la cantidad de días
    fechainicio DATE NOT NULL,
    fechaFin DATE NOT NULL,
    cantidadAdultos INT(2),
    cantidadNinos INT(2),
    cantidadDiscapacitados INT(2), 
    motivoReserva ENUM ('Negocios','Personal','Viaje','Familiar', 'Otro') NOT NULL,
    numeroHabitacion VARCHAR(10) NOT NULL,
    metodoPago ENUM ('Tarjeta','Efectivo','PSE') NOT NULL,
    informacionAdicional TEXT,
    us_numDocumento VARCHAR(15) NOT NULL,
    hue_numDocumento VARCHAR(15) NOT NULL,
    estado ENUM ('Activa', 'Cancelada', 'Finalizada', 'Pendiente') NOT NULL,
    fechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    FOREIGN KEY (numeroHabitacion) REFERENCES tp_habitaciones (numero),
    FOREIGN KEY (us_numDocumento) REFERENCES tp_usuarios (numDocumento),  -- CORREGIDO: nombre de columna
    FOREIGN KEY (hue_numDocumento) REFERENCES tp_huespedes (numDocumento) -- CORREGIDO: nombre de columna
) ENGINE=INNODB;

-- Tabla factura
CREATE TABLE IF NOT EXISTS tp_factura (
    id INT(3) AUTO_INCREMENT NOT NULL,
    infoReserva INT(3) NOT NULL,
    fechaFactura DATETIME DEFAULT CURRENT_TIMESTAMP,
    infoHotel INT(3) NOT NULL,
    total DECIMAL(30,2) NOT NULL,

    PRIMARY KEY (id),
    FOREIGN KEY (infoReserva) REFERENCES tp_reservas (id),
    FOREIGN KEY (infoHotel) REFERENCES tp_hotel (id)
) ENGINE=INNODB;

-- Tabla mantenimiento
CREATE TABLE IF NOT EXISTS tp_mantenimiento (
    id INT(4) AUTO_INCREMENT NOT NULL,
    numeroHabitacion VARCHAR(5) NOT NULL,
    tipo ENUM ('Limpieza','Estructura','Eléctrico','Otro') NOT NULL,
    problemaDescripcion VARCHAR(50) NOT NULL,
    fechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    ultimaActualizacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    frecuencia ENUM ('Sí', 'No') NOT NULL,
    cantFrecuencia ENUM ('Diario', 'Semanal', 'Quincenal', 'Mensual') NOT NULL,
    prioridad ENUM ('Bajo', 'Alto') NOT NULL,
    numDocumento VARCHAR(15) NOT NULL,
    estado ENUM ('Pendiente','Finalizado') NOT NULL DEFAULT 'Pendiente',

    PRIMARY KEY (id),
    FOREIGN KEY (numeroHabitacion) REFERENCES tp_habitaciones (numero),
    FOREIGN KEY (numDocumento) REFERENCES tp_usuarios (numDocumento)
) ENGINE=INNODB;

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
('1014596349', 'Cédula de Ciudadanía', 'Brayan Felipe', 'Pulido Lopez', '3172509298', 'brayan06.pulido@gmail.com', 'Hombre', '2006-03-03', '123456789', 'Administrador'),
('1000289068', 'Cédula de Ciudadanía', 'Favian Alejandro', 'Machuca Pedraza', '3144235027', 'bleachowl98@gmail.com', 'Mujer', '2003-10-15', '123456789', 'Colaborador'),
('1019987917', 'Cédula de Ciudadanía', 'Camilo Andres', 'Guerrero Yanquen', '3027644457', 'camiloagycr321@gmail.com', 'Hombre', '2006-02-15', '123456789', 'Usuario'),
('1111222233', 'Cédula de Ciudadanía', 'Ana Patricia', 'Morales Ruiz', '3001112222', 'ana.morales@lodgehub.com', 'Mujer', '1992-05-18', '$2y$10$hashedpassword4', 'Usuario'),
('7777888899', 'Pasaporte', 'Roberto', 'Silva Santos', '3007778888', 'roberto.silva@email.com', 'Hombre', '1987-09-25', '$2y$10$hashedpassword5', 'Usuario');

-- Insertar hotel
INSERT INTO tp_hotel (nit, nombre, direccion, telefono, correo, descripcion, numDocumentoAdmin) VALUES 
('900123456-1', 'Hotel Lodge Hub Premium', 'Calle 123 #45-67, Bogotá, Colombia', '6013334444', 'info@lodgehub.com', 'Hotel de lujo ubicado en el corazón de la ciudad, ofreciendo servicios de alta calidad y comodidad excepcional.', '1014596349');

-- Insertar personal del hotel
INSERT INTO ti_personal (id_hotel, numDocumento, roles) VALUES 
(1, '1014596349', 'Administrador'),
(1, '1000289068', 'colaborador');


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
('1140915008', '3170560930', '4198126@gmail..com', 'ANGELO', 'gONZALEZ', 'Cedula de Ciudadania', 'Mujer'),
('6666777788', '3006667777', 'sofia.hernandez@email.com', 'Sofía Isabel', 'Hernández Vega', 'Cedula de Ciudadania', 'Mujer'),
('8888999900', '3008889999', 'pedro.jimenez@email.com', 'Pedro Antonio', 'Jiménez Flores', 'Cedula de Ciudadania', 'Hombre'),
('2222333344', '3002223333', 'laura.torres@email.com', 'Laura Cristina', 'Torres Mendoza', 'Cedula de Ciudadania', 'Mujer'),
('9999000011', '3009990000', 'miguel.vargas@email.com', 'Miguel Ángel', 'Vargas Pineda', 'Pasaporte', 'Hombre');

-- Insertar reservas
INSERT INTO tp_reservas (pagoFinal, fechainicio, fechaFin, cantidadAdultos, cantidadNinos, cantidadDiscapacitados, motivoReserva, numeroHabitacion, metodoPago, informacionAdicional, us_numDocumento, hue_numDocumento, estado) VALUES 
(240000.00, '2025-09-10', '2025-09-12', 2, 0, 0, 'Personal', '202', 'Tarjeta', 'Luna de miel', '1000289068', '1140915008', 'Activa'),
(500000.00, '2025-09-15', '2025-09-17', 2, 0, 0, 'Negocios', '301', 'PSE', 'Reunión empresarial', '7777888899', '6666777788', 'Activa'),
(160000.00, '2025-09-05', '2025-09-07', 1, 0, 0, 'Personal', '101', 'Efectivo', NULL, '1111222233', '8888999900', 'Finalizada'),
(360000.00, '2025-09-20', '2025-09-22', 3, 1, 0, 'Familiar', '401', 'Tarjeta', 'Vacaciones familiares', '7777888899', '2222333344', 'Pendiente');

-- Insertar PQRS
INSERT INTO tp_pqrs (tipo, descripcion, numdocumento, prioridad, categoria, estado) VALUES 
('Quejas', 'El aire acondicionado de la habitación 202 no funcionaba correctamente durante mi estadía.', '1140915008', 'Alto', 'Habitación', 'Finalizado'),
('Sugerencias', 'Sería genial si pudieran agregar más opciones vegetarianas en el menú del restaurante.', '7777888899', 'Bajo', 'Servicio', 'Pendiente'),
('Felicitaciones', 'Excelente atención del personal de recepción, muy amables y profesionales.', '1111222233', 'Bajo', 'Atención', 'Finalizado'),
('Peticiones', 'Solicito información sobre descuentos para estadías prolongadas.', '7777888899', 'Bajo', 'Otro', 'Pendiente');

-- Insertar mantenimientos
INSERT INTO tp_mantenimiento (numeroHabitacion, tipo, problemaDescripcion, frecuencia, cantFrecuencia, prioridad, numDocumento, estado) VALUES 
('501', 'Eléctrico', 'Falla en el sistema de iluminación LED', 'No', 'Diario', 'Alto', '1000289068', 'Pendiente'),
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