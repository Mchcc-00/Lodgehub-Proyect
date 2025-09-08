CREATE DATABASE IF NOT EXISTS Lodgehub;
USE Lodgehub;

-- Tabla usuarios (sin cambios)
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

-- Tabla hotel (sin cambios)
CREATE TABLE IF NOT EXISTS tp_hotel (
    id INT(3) AUTO_INCREMENT NOT NULL,
    nit VARCHAR(20) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(200),
    telefono VARCHAR(15),
    correo VARCHAR(255),
    foto VARCHAR(255),
    descripcion TEXT,
    numDocumentoAdmin VARCHAR(15),
    PRIMARY KEY (id),
    FOREIGN KEY (numDocumentoAdmin) REFERENCES tp_usuarios (numDocumento)
) ENGINE=INNODB; 

-- Tabla personal (sin cambios)
CREATE TABLE IF NOT EXISTS ti_personal (
    id_hotel INT(3) NOT NULL,
    numDocumento VARCHAR(15) NOT NULL,
    roles TEXT NOT NULL,
    PRIMARY KEY (id_hotel, numDocumento),
    FOREIGN KEY (id_hotel) REFERENCES tp_hotel (id),
    FOREIGN KEY (numDocumento) REFERENCES tp_usuarios (numDocumento)
) ENGINE=INNODB;

-- Tabla huéspedes (sin cambios)
CREATE TABLE IF NOT EXISTS tp_huespedes (
    numDocumento VARCHAR(15) NOT NULL,
    numTelefono VARCHAR(15) NOT NULL,
    correo VARCHAR(255) NOT NULL,
    nombres VARCHAR(50) NOT NULL,
    apellidos VARCHAR(50) NOT NULL,
    tipoDocumento ENUM ('Cedula de Ciudadania','Tarjeta de Identidad','Cedula de Extranjeria','Pasaporte','Registro Civil') NOT NULL,
    sexo ENUM ('Hombre','Mujer','Otro','Prefiero no decirlo') NOT NULL,
    fechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fechaActualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (numDocumento),
    UNIQUE KEY uk_correo (correo)
) ENGINE=INNODB;

-- Tabla tipo habitación (MODIFICADA)
CREATE TABLE IF NOT EXISTS td_tipoHabitacion (
    id INT(3) AUTO_INCREMENT NOT NULL,
    descripcion VARCHAR(20) NOT NULL,
    cantidad INT(3) NOT NULL DEFAULT 0,
    id_hotel INT(3) NOT NULL, -- << NUEVO: Para saber a qué hotel pertenece este tipo de habitación
    PRIMARY KEY (id),
    FOREIGN KEY (id_hotel) REFERENCES tp_hotel(id) -- << NUEVO
) ENGINE=INNODB;

-- Tabla habitaciones (MODIFICADA)
CREATE TABLE IF NOT EXISTS tp_habitaciones (
    id INT AUTO_INCREMENT NOT NULL, -- << NUEVO: Llave primaria única
    numero VARCHAR(5) NOT NULL,
    costo DECIMAL(10,2) NOT NULL, 
    capacidad INT(3) NOT NULL,
    tipoHabitacion INT(3) NOT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    descripcion TEXT DEFAULT NULL,
    estado ENUM ('Disponible', 'Reservada', 'Ocupada', 'Mantenimiento') NOT NULL DEFAULT 'Disponible',
    descripcionMantenimiento TEXT DEFAULT NULL,
    estadoMantenimiento ENUM ('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
    id_hotel INT(3) NOT NULL, -- << NUEVO: Llave foránea para vincular al hotel
    PRIMARY KEY (id), -- << MODIFICADO: Nueva llave primaria
    UNIQUE KEY uk_hotel_numero (id_hotel, numero), -- << NUEVO: Asegura que el número de habitación sea único por hotel
    FOREIGN KEY (id_hotel) REFERENCES tp_hotel(id), -- << NUEVO
    FOREIGN KEY (tipoHabitacion) REFERENCES td_tipohabitacion (id)
) ENGINE=INNODB;

-- Tabla PQRS (MODIFICADA)
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
    id_hotel INT(3) NOT NULL, -- << NUEVO: Para saber de qué hotel es la PQRS
    PRIMARY KEY (id),
    FOREIGN KEY (id_hotel) REFERENCES tp_hotel(id), -- << NUEVO
    FOREIGN KEY (numdocumento) REFERENCES tp_usuarios (numDocumento)
) ENGINE=INNODB;

-- Tabla reservas (MODIFICADA)
CREATE TABLE IF NOT EXISTS tp_reservas (
    id INT(3) AUTO_INCREMENT NOT NULL,
    pagoFinal DECIMAL(30,2) NOT NULL,
    fechainicio DATE NOT NULL,
    fechaFin DATE NOT NULL,
    cantidadAdultos INT(2),
    cantidadNinos INT(2),
    cantidadDiscapacitados INT(2), 
    motivoReserva ENUM ('Negocios','Personal','Viaje','Familiar', 'Otro') NOT NULL,
    id_habitacion INT NOT NULL, -- << MODIFICADO: Se referencia el ID único de la habitación
    metodoPago ENUM ('Tarjeta','Efectivo','PSE') NOT NULL,
    informacionAdicional TEXT,
    us_numDocumento VARCHAR(15) NOT NULL,
    hue_numDocumento VARCHAR(15) NOT NULL,
    estado ENUM ('Activa', 'Cancelada', 'Finalizada', 'Pendiente') NOT NULL,
    fechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_hotel INT(3) NOT NULL, -- << NUEVO: Para saber de qué hotel es la reserva
    PRIMARY KEY (id),
    FOREIGN KEY (id_hotel) REFERENCES tp_hotel(id), -- << NUEVO
    FOREIGN KEY (id_habitacion) REFERENCES tp_habitaciones (id), -- << MODIFICADO
    FOREIGN KEY (us_numDocumento) REFERENCES tp_usuarios (numDocumento),
    FOREIGN KEY (hue_numDocumento) REFERENCES tp_huespedes (numDocumento)
) ENGINE=INNODB;

-- Tabla factura (sin cambios, ya estaba correcta)
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

-- Tabla mantenimiento (MODIFICADA)
CREATE TABLE IF NOT EXISTS tp_mantenimiento (
    id INT(4) AUTO_INCREMENT NOT NULL,
    id_habitacion INT NOT NULL, -- << MODIFICADO: Se referencia el ID único de la habitación
    tipo ENUM ('Limpieza','Estructura','Eléctrico','Otro') NOT NULL,
    problemaDescripcion VARCHAR(50) NOT NULL,
    fechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    ultimaActualizacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    frecuencia ENUM ('Sí', 'No') NOT NULL,
    cantFrecuencia ENUM ('Diario', 'Semanal', 'Quincenal', 'Mensual') NOT NULL,
    prioridad ENUM ('Bajo', 'Alto') NOT NULL,
    numDocumento VARCHAR(15) NOT NULL,
    estado ENUM ('Pendiente','Finalizado') NOT NULL DEFAULT 'Pendiente',
    id_hotel INT(3) NOT NULL, -- << NUEVO: Para saber a qué hotel pertenece el mantenimiento
    PRIMARY KEY (id),
    FOREIGN KEY (id_hotel) REFERENCES tp_hotel(id), -- << NUEVO
    FOREIGN KEY (id_habitacion) REFERENCES tp_habitaciones (id), -- << MODIFICADO
    FOREIGN KEY (numDocumento) REFERENCES tp_usuarios (numDocumento)
) ENGINE=INNODB;

-- =============================================
-- INSERTS DE DATOS DE EJEMPLO
-- =============================================

-- Insertar usuarios (corregido)
INSERT INTO tp_usuarios (numDocumento, tipoDocumento, nombres, apellidos, numTelefono, correo, sexo, fechaNacimiento, password, roles) VALUES 
('1014596349', 'Cédula de Ciudadanía', 'Brayan Felipe', 'Pulido Lopez', '3172509298', 'brayan06.pulido@gmail.com', 'Hombre', '2006-03-03', '123456789', 'Administrador'),
('1000289068', 'Cédula de Ciudadanía', 'Favian Alejandro', 'Machuca Pedraza', '3144235027', 'bleachowl98@gmail.com', 'Hombre', '2003-10-15', '123456789', 'Colaborador'),
('1019987917', 'Cédula de Ciudadanía', 'Camilo Andres', 'Guerrero Yanquen', '3027644457', 'camiloagycr321@gmail.com', 'Hombre', '2006-02-15', '123456789', 'Usuario'),
('1111222233', 'Cédula de Ciudadanía', 'Ana Patricia', 'Morales Ruiz', '3001112222', 'ana.morales@lodgehub.com', 'Mujer', '1992-05-18', '123456789', 'Usuario'),
('7777888899', 'Pasaporte', 'Roberto', 'Silva Santos', '3007778888', 'roberto.silva@email.com', 'Hombre', '1987-09-25', '123456789', 'Usuario'),
('5555666677', 'Cédula de Ciudadanía', 'María Elena', 'Ramírez Castro', '3005556666', 'maria.ramirez@lodgehub.com', 'Mujer', '1985-12-10', '123456789', 'Administrador');

-- Insertar hoteles
INSERT INTO tp_hotel (nit, nombre, direccion, telefono, correo, descripcion, numDocumentoAdmin) VALUES 
('900123456-1', 'Hotel Lodge Hub Premium', 'Calle 123 #45-67, Bogotá, Colombia', '6013334444', 'info@lodgehub.com', 'Hotel de lujo ubicado en el corazón de la ciudad, ofreciendo servicios de alta calidad y comodidad excepcional.', '1014596349'),
('900987654-2', 'Lodge Hub Business', 'Carrera 15 #80-25, Medellín, Colombia', '6044445555', 'medellin@lodgehub.com', 'Hotel especializado en turismo de negocios con salas de conferencias y servicios corporativos.', '5555666677'),
('900555777-3', 'Lodge Hub Resort', 'Km 5 Vía Cartagena-Barú, Cartagena, Colombia', '6055556666', 'cartagena@lodgehub.com', 'Resort frente al mar con spa, piscinas y actividades recreativas para toda la familia.', '1014596349');

-- Insertar personal del hotel
INSERT INTO ti_personal (id_hotel, numDocumento, roles) VALUES 
(1, '1014596349', 'Administrador,Recepcionista'),
(1, '1000289068', 'Colaborador,Mantenimiento'),
(2, '5555666677', 'Administrador,Gerente'),
(2, '1111222233', 'Colaborador,Recepcionista'),
(3, '1014596349', 'Administrador'),
(3, '7777888899', 'Colaborador,Animador');

-- Insertar tipos de habitación por hotel
INSERT INTO td_tipoHabitacion (descripcion, cantidad, id_hotel) VALUES 
-- Hotel Premium Bogotá
('Individual', 10, 1),
('Doble', 15, 1),
('Suite', 5, 1),
('Familiar', 8, 1),
('Ejecutiva', 6, 1),
-- Hotel Business Medellín
('Individual', 8, 2),
('Doble', 12, 2),
('Suite Ejecutiva', 4, 2),
('Familiar', 6, 2),
('Presidencial', 2, 2),
-- Resort Cartagena
('Standard', 20, 3),
('Ocean View', 15, 3),
('Villa', 8, 3),
('Master Suite', 5, 3),
('Penthouse', 2, 3);

-- Insertar habitaciones
INSERT INTO tp_habitaciones (numero, costo, capacidad, tipoHabitacion, descripcion, estado, id_hotel) VALUES 
-- Hotel Premium Bogotá (id_hotel = 1)
('101', 80000.00, 1, 1, 'Habitación individual con baño privado, TV, WiFi', 'Disponible', 1),
('102', 80000.00, 1, 1, 'Habitación individual con vista a la ciudad', 'Disponible', 1),
('201', 120000.00, 2, 2, 'Habitación doble con cama matrimonial, minibar', 'Disponible', 1),
('202', 120000.00, 2, 2, 'Habitación doble con dos camas individuales', 'Ocupada', 1),
('301', 250000.00, 2, 3, 'Suite ejecutiva con sala, jacuzzi, balcón', 'Disponible', 1),
('401', 180000.00, 4, 4, 'Habitación familiar con litera y cama matrimonial', 'Disponible', 1),
('501', 200000.00, 2, 5, 'Habitación ejecutiva con escritorio y sala de reuniones', 'Mantenimiento', 1),

-- Hotel Business Medellín (id_hotel = 2)
('B101', 75000.00, 1, 6, 'Habitación individual con escritorio de trabajo', 'Disponible', 2),
('B102', 75000.00, 1, 6, 'Habitación individual con vista al valle', 'Disponible', 2),
('B201', 110000.00, 2, 7, 'Habitación doble con área de trabajo amplia', 'Disponible', 2),
('B301', 300000.00, 2, 8, 'Suite ejecutiva con sala de juntas privada', 'Disponible', 2),
('B401', 170000.00, 4, 9, 'Habitación familiar con conexión de alta velocidad', 'Disponible', 2),

-- Resort Cartagena (id_hotel = 3)
('C101', 150000.00, 2, 11, 'Habitación standard con aire acondicionado', 'Disponible', 3),
('C201', 220000.00, 2, 12, 'Habitación con vista al océano y balcón', 'Disponible', 3),
('C301', 400000.00, 4, 13, 'Villa con piscina privada y jardín', 'Disponible', 3),
('C501', 600000.00, 6, 14, 'Master suite con jacuzzi y terraza panorámica', 'Disponible', 3),
('C601', 1200000.00, 8, 15, 'Penthouse con vista 360° y servicio personalizado', 'Disponible', 3);

-- Insertar huéspedes (corregido el tipo de documento)
INSERT INTO tp_huespedes (numDocumento, numTelefono, correo, nombres, apellidos, tipoDocumento, sexo) VALUES 
('1140915008', '3170560930', 'angelo.gonzalez@gmail.com', 'ANGELO', 'GONZALEZ', 'Cedula de Ciudadania', 'Hombre'),
('6666777788', '3006667777', 'sofia.hernandez@email.com', 'Sofía Isabel', 'Hernández Vega', 'Cedula de Ciudadania', 'Mujer'),
('8888999900', '3008889999', 'pedro.jimenez@email.com', 'Pedro Antonio', 'Jiménez Flores', 'Cedula de Ciudadania', 'Hombre'),
('2222333344', '3002223333', 'laura.torres@email.com', 'Laura Cristina', 'Torres Mendoza', 'Cedula de Ciudadania', 'Mujer'),
('9999000011', '3009990000', 'miguel.vargas@email.com', 'Miguel Ángel', 'Vargas Pineda', 'Pasaporte', 'Hombre'),
('3333444455', '3003334444', 'carla.ospina@email.com', 'Carla Andrea', 'Ospina Mejía', 'Cedula de Ciudadania', 'Mujer'),
('4444555566', '3004445555', 'diego.castro@email.com', 'Diego Fernando', 'Castro López', 'Cedula de Ciudadania', 'Hombre');

-- Insertar reservas (corregido para usar id_habitacion)
INSERT INTO tp_reservas (pagoFinal, fechainicio, fechaFin, cantidadAdultos, cantidadNinos, cantidadDiscapacitados, motivoReserva, id_habitacion, metodoPago, informacionAdicional, us_numDocumento, hue_numDocumento, estado, id_hotel) VALUES 
(240000.00, '2025-09-10', '2025-09-12', 2, 0, 0, 'Personal', 4, 'Tarjeta', 'Luna de miel', '1000289068', '1140915008', 'Activa', 1),
(500000.00, '2025-09-15', '2025-09-17', 2, 0, 0, 'Negocios', 5, 'PSE', 'Reunión empresarial', '7777888899', '6666777788', 'Activa', 1),
(160000.00, '2025-09-05', '2025-09-07', 1, 0, 0, 'Personal', 1, 'Efectivo', NULL, '1111222233', '8888999900', 'Finalizada', 1),
(360000.00, '2025-09-20', '2025-09-22', 3, 1, 0, 'Familiar', 6, 'Tarjeta', 'Vacaciones familiares', '7777888899', '2222333344', 'Pendiente', 1),
(220000.00, '2025-09-25', '2025-09-27', 2, 0, 0, 'Negocios', 10, 'PSE', 'Conferencia médica', '5555666677', '3333444455', 'Activa', 2),
(800000.00, '2025-10-01', '2025-10-05', 4, 2, 0, 'Familiar', 14, 'Tarjeta', 'Vacaciones en familia', '1014596349', '4444555566', 'Pendiente', 3);

-- Insertar PQRS (agregado id_hotel)
INSERT INTO tp_pqrs (tipo, descripcion, numdocumento, prioridad, categoria, estado, id_hotel, respuesta, fechaFinalizacion) VALUES 
('Quejas', 'El aire acondicionado de la habitación 202 no funcionaba correctamente durante mi estadía.', '1014596349', 'Alto', 'Habitación', 'Finalizado', 1, 'Se realizó mantenimiento correctivo al aire acondicionado. Disculpas por las molestias.', '2025-09-02 14:30:00'),
('Sugerencias', 'Sería genial si pudieran agregar más opciones vegetarianas en el menú del restaurante.', '1000289068', 'Bajo', 'Servicio', 'Pendiente', 1, NULL, NULL),
('Felicitaciones', 'Excelente atención del personal de recepción, muy amables y profesionales.', '1019987917', 'Bajo', 'Atención', 'Finalizado', 1, 'Gracias por sus comentarios. Los transmitiremos al equipo.', '2025-09-03 09:15:00'),
('Peticiones', 'Solicito información sobre descuentos para estadías prolongadas.', '7777888899', 'Bajo', 'Otro', 'Pendiente', 1, NULL, NULL),
('Quejas', 'La conexión WiFi en la habitación B201 era muy lenta.', '5555666677', 'Alto', 'Habitación', 'Pendiente', 2, NULL, NULL),
('Sugerencias', 'Podrían implementar un servicio de spa en el resort.', '1014596349', 'Bajo', 'Servicio', 'Pendiente', 3, NULL, NULL);

-- Insertar mantenimientos (corregido para usar id_habitacion)
INSERT INTO tp_mantenimiento (id_habitacion, tipo, problemaDescripcion, frecuencia, cantFrecuencia, prioridad, numDocumento, estado, id_hotel) VALUES 
(7, 'Eléctrico', 'Falla en el sistema de iluminación LED', 'No', 'Diario', 'Alto', '1014596349', 'Pendiente', 1),
(4, 'Estructura', 'Aire acondicionado requiere limpieza filtros', 'Sí', 'Mensual', 'Bajo', '1000289068', 'Finalizado', 1),
(5, 'Limpieza', 'Limpieza profunda de alfombras', 'Sí', 'Quincenal', 'Bajo', '1019987917', 'Pendiente', 1),
(10, 'Eléctrico', 'Revisión sistema de iluminación sala juntas', 'Sí', 'Mensual', 'Bajo', '5555666677', 'Pendiente', 2),
(14, 'Limpieza', 'Mantenimiento jacuzzi master suite', 'Sí', 'Semanal', 'Alto', '7777888899', 'Pendiente', 3);

-- Insertar facturas
INSERT INTO tp_factura (infoReserva, infoHotel, total) VALUES 
(3, 1, 160000.00),
(1, 1, 240000.00),
(5, 2, 220000.00);

-- =============================================
-- VISTAS
-- =============================================

-- Vista: Información completa de habitaciones por hotel
CREATE OR REPLACE VIEW vw_habitaciones_completa AS
SELECT 
    h.id,
    h.numero,
    h.costo,
    h.capacidad,
    h.descripcion,
    h.estado,
    h.descripcionMantenimiento,
    h.estadoMantenimiento,
    th.descripcion AS tipoHabitacion,
    ht.nombre AS nombreHotel,
    ht.id AS id_hotel
FROM tp_habitaciones h
INNER JOIN td_tipoHabitacion th ON h.tipoHabitacion = th.id
INNER JOIN tp_hotel ht ON h.id_hotel = ht.id;

-- Vista: Reservas con información completa
CREATE OR REPLACE VIEW vw_reservas_completa AS
SELECT 
    r.id,
    r.pagoFinal,
    r.fechainicio,
    r.fechaFin,
    r.cantidadAdultos,
    r.cantidadNinos,
    r.cantidadDiscapacitados,
    r.motivoReserva,
    r.metodoPago,
    r.informacionAdicional,
    r.estado,
    r.fechaRegistro,
    h.numero AS numeroHabitacion,
    th.descripcion AS tipoHabitacion,
    ht.nombre AS nombreHotel,
    CONCAT(u.nombres, ' ', u.apellidos) AS nombreUsuario,
    CONCAT(hu.nombres, ' ', hu.apellidos) AS nombreHuesped,
    hu.correo AS correoHuesped,
    hu.numTelefono AS telefonoHuesped,
    DATEDIFF(r.fechaFin, r.fechainicio) AS diasEstadia
FROM tp_reservas r
INNER JOIN tp_habitaciones h ON r.id_habitacion = h.id
INNER JOIN td_tipoHabitacion th ON h.tipoHabitacion = th.id
INNER JOIN tp_hotel ht ON r.id_hotel = ht.id
INNER JOIN tp_usuarios u ON r.us_numDocumento = u.numDocumento
INNER JOIN tp_huespedes hu ON r.hue_numDocumento = hu.numDocumento;

-- Vista: Dashboard de ocupación por hotel
CREATE OR REPLACE VIEW vw_ocupacion_hotel AS
SELECT 
    h.id AS id_hotel,
    h.nombre AS nombreHotel,
    COUNT(hab.id) AS totalHabitaciones,
    SUM(CASE WHEN hab.estado = 'Disponible' THEN 1 ELSE 0 END) AS habitacionesDisponibles,
    SUM(CASE WHEN hab.estado = 'Ocupada' THEN 1 ELSE 0 END) AS habitacionesOcupadas,
    SUM(CASE WHEN hab.estado = 'Reservada' THEN 1 ELSE 0 END) AS habitacionesReservadas,
    SUM(CASE WHEN hab.estado = 'Mantenimiento' THEN 1 ELSE 0 END) AS habitacionesMantenimiento,
    ROUND((SUM(CASE WHEN hab.estado IN ('Ocupada', 'Reservada') THEN 1 ELSE 0 END) / COUNT(hab.id)) * 100, 2) AS porcentajeOcupacion
FROM tp_hotel h
LEFT JOIN tp_habitaciones hab ON h.id = hab.id_hotel
WHERE hab.estadoMantenimiento = 'Activo'
GROUP BY h.id, h.nombre;

-- Vista: PQRS con información completa
CREATE OR REPLACE VIEW vw_pqrs_completa AS
SELECT 
    p.id,
    p.fechaRegistro,
    p.fechaLimite,
    p.tipo,
    p.descripcion,
    p.prioridad,
    p.categoria,
    p.estado,
    p.fechaFinalizacion,
    p.respuesta,
    CONCAT(u.nombres, ' ', u.apellidos) AS nombreUsuario,
    u.correo AS correoUsuario,
    h.nombre AS nombreHotel,
    DATEDIFF(COALESCE(p.fechaFinalizacion, NOW()), p.fechaRegistro) AS diasTranscurridos,
    CASE 
        WHEN p.estado = 'Pendiente' AND p.fechaLimite < CURDATE() THEN 'Vencida'
        WHEN p.estado = 'Pendiente' AND DATEDIFF(p.fechaLimite, CURDATE()) <= 1 THEN 'Por vencer'
        ELSE 'En tiempo'
    END AS estadoTiempo
FROM tp_pqrs p
INNER JOIN tp_usuarios u ON p.numdocumento = u.numDocumento
INNER JOIN tp_hotel h ON p.id_hotel = h.id;

-- Vista: Mantenimientos con información completa
CREATE OR REPLACE VIEW vw_mantenimientos_completa AS
SELECT 
    m.id,
    m.tipo,
    m.problemaDescripcion,
    m.fechaRegistro,
    m.ultimaActualizacion,
    m.frecuencia,
    m.cantFrecuencia,
    m.prioridad,
    m.estado,
    hab.numero AS numeroHabitacion,
    th.descripcion AS tipoHabitacion,
    CONCAT(u.nombres, ' ', u.apellidos) AS nombreTecnico,
    h.nombre AS nombreHotel,
    DATEDIFF(NOW(), m.fechaRegistro) AS diasPendientes
FROM tp_mantenimiento m
INNER JOIN tp_habitaciones hab ON m.id_habitacion = hab.id
INNER JOIN td_tipoHabitacion th ON hab.tipoHabitacion = th.id
INNER JOIN tp_usuarios u ON m.numDocumento = u.numDocumento
INNER JOIN tp_hotel h ON m.id_hotel = h.id;

-- Vista: Ingresos por hotel
CREATE OR REPLACE VIEW vw_ingresos_hotel AS
SELECT 
    h.id AS id_hotel,
    h.nombre AS nombreHotel,
    DATE_FORMAT(r.fechaRegistro, '%Y-%m') AS mesAno,
    COUNT(r.id) AS totalReservas,
    SUM(CASE WHEN r.estado = 'Finalizada' THEN r.pagoFinal ELSE 0 END) AS ingresosFinalizado,
    SUM(CASE WHEN r.estado = 'Activa' THEN r.pagoFinal ELSE 0 END) AS ingresosActivos,
    SUM(r.pagoFinal) AS ingresosTotales,
    AVG(r.pagoFinal) AS promedioReserva
FROM tp_hotel h
LEFT JOIN tp_reservas r ON h.id = r.id_hotel
WHERE r.fechaRegistro >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
GROUP BY h.id, h.nombre, DATE_FORMAT(r.fechaRegistro, '%Y-%m')
ORDER BY h.nombre, mesAno DESC;

-- Vista: Personal por hotel
CREATE OR REPLACE VIEW vw_personal_hotel AS
SELECT 
    h.id AS id_hotel,
    h.nombre AS nombreHotel,
    CONCAT(u.nombres, ' ', u.apellidos) AS nombreCompleto,
    u.numDocumento,
    u.correo,
    u.numTelefono,
    u.roles AS rolSistema,
    p.roles AS rolesHotel
FROM tp_hotel h
INNER JOIN ti_personal p ON h.id = p.id_hotel
INNER JOIN tp_usuarios u ON p.numDocumento = u.numDocumento;

-- ========================================
-- ÍNDICES PARA MEJORAR RENDIMIENTO
-- ========================================

-- Índices para tp_habitaciones
CREATE INDEX idx_habitaciones_hotel_estado ON tp_habitaciones(id_hotel, estado);
CREATE INDEX idx_habitaciones_tipo ON tp_habitaciones(tipoHabitacion);
CREATE INDEX idx_habitaciones_estado_mant ON tp_habitaciones(estadoMantenimiento);

-- Índices para tp_reservas
CREATE INDEX idx_reservas_hotel ON tp_reservas(id_hotel);
CREATE INDEX idx_reservas_fechas ON tp_reservas(fechainicio, fechaFin);
CREATE INDEX idx_reservas_estado ON tp_reservas(estado);
CREATE INDEX idx_reservas_usuario ON tp_reservas(us_numDocumento);
CREATE INDEX idx_reservas_huesped ON tp_reservas(hue_numDocumento);
CREATE INDEX idx_reservas_habitacion ON tp_reservas(id_habitacion);

-- Índices para tp_pqrs
CREATE INDEX idx_pqrs_hotel ON tp_pqrs(id_hotel);
CREATE INDEX idx_pqrs_estado ON tp_pqrs(estado);
CREATE INDEX idx_pqrs_prioridad ON tp_pqrs(prioridad);
CREATE INDEX idx_pqrs_fecha_limite ON tp_pqrs(fechaLimite);
CREATE INDEX idx_pqrs_usuario ON tp_pqrs(numdocumento);

-- Índices para tp_mantenimiento
CREATE INDEX idx_mantenimiento_hotel ON tp_mantenimiento(id_hotel);
CREATE INDEX idx_mantenimiento_habitacion ON tp_mantenimiento(id_habitacion);
CREATE INDEX idx_mantenimiento_estado ON tp_mantenimiento(estado);
CREATE INDEX idx_mantenimiento_prioridad ON tp_mantenimiento(prioridad);
CREATE INDEX idx_mantenimiento_fecha ON tp_mantenimiento(fechaRegistro);

-- Índices para td_tipoHabitacion
CREATE INDEX idx_tipo_habitacion_hotel ON td_tipoHabitacion(id_hotel);

-- Índices para ti_personal
CREATE INDEX idx_personal_hotel ON ti_personal(id_hotel);

-- Índices para tp_usuarios
CREATE INDEX idx_usuarios_email ON tp_usuarios(correo);
CREATE INDEX idx_usuarios_rol ON tp_usuarios(roles);

-- Índices para tp_huespedes
CREATE INDEX idx_huespedes_email ON tp_huespedes(correo);
CREATE INDEX idx_huespedes_fecha ON tp_huespedes(fechaCreacion);

-- Índices para tp_factura
CREATE INDEX idx_factura_hotel ON tp_factura(infoHotel);
CREATE INDEX idx_factura_reserva ON tp_factura(infoReserva);
CREATE INDEX idx_factura_fecha ON tp_factura(fechaFactura);