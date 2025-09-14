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
    tipoDocumento ENUM ('Cedula de Ciudadanía','Tarjeta de Identidad','Cedula de Extranjeria','Pasaporte','Registro Civil') NOT NULL,
    sexo ENUM ('Hombre','Mujer','Otro','Prefiero no decirlo') NOT NULL,
    id_hotel INT(3) NOT NULL,
    fechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fechaActualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (numDocumento),
    FOREIGN KEY (id_hotel) REFERENCES tp_hotel (id),
    UNIQUE KEY uk_correo (correo)
) ENGINE=INNODB;

-- Tabla tipo habitación (MODIFICADA)
CREATE TABLE IF NOT EXISTS td_tipoHabitacion (
    id INT(3) AUTO_INCREMENT NOT NULL,
    descripcion VARCHAR(20) NOT NULL,
    cantidad INT(3) NOT NULL DEFAULT 0,
    id_hotel INT(3) NOT NULL, 
    PRIMARY KEY (id),
    FOREIGN KEY (id_hotel) REFERENCES tp_hotel(id)
) ENGINE=INNODB;

-- Tabla habitaciones (MODIFICADA)
CREATE TABLE IF NOT EXISTS tp_habitaciones (
    id INT AUTO_INCREMENT NOT NULL, 
    numero VARCHAR(5) NOT NULL,
    costo DECIMAL(10,2) NOT NULL, 
    capacidad INT(3) NOT NULL,
    tipoHabitacion INT(3) NOT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    descripcion TEXT DEFAULT NULL,
    estado ENUM ('Disponible', 'Reservada', 'Ocupada', 'Mantenimiento') NOT NULL DEFAULT 'Disponible',
    descripcionMantenimiento TEXT DEFAULT NULL,
    estadoMantenimiento ENUM ('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
    id_hotel INT(3) NOT NULL, 
    PRIMARY KEY (id),
    UNIQUE KEY uk_hotel_numero (id_hotel, numero), 
    FOREIGN KEY (id_hotel) REFERENCES tp_hotel(id), 
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
    id_hotel INT(3) NOT NULL, 
    PRIMARY KEY (id),
    FOREIGN KEY (id_hotel) REFERENCES tp_hotel(id),
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
    id_habitacion INT NOT NULL, 
    metodoPago ENUM ('Tarjeta','Efectivo','PSE') NOT NULL,
    informacionAdicional TEXT,
    us_numDocumento VARCHAR(15) NOT NULL,
    hue_numDocumento VARCHAR(15) NOT NULL,
    estado ENUM ('Activa', 'Cancelada', 'Finalizada', 'Pendiente') NOT NULL,
    fechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_hotel INT(3) NOT NULL, 
    PRIMARY KEY (id),
    FOREIGN KEY (id_hotel) REFERENCES tp_hotel(id), 
    FOREIGN KEY (id_habitacion) REFERENCES tp_habitaciones (id), 
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
    id_habitacion INT NOT NULL, 
    tipo ENUM ('Limpieza','Estructura','Eléctrico','Otro') NOT NULL,
    problemaDescripcion VARCHAR(50) NOT NULL,
    fechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    ultimaActualizacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    frecuencia ENUM ('Sí', 'No') NOT NULL,
    cantFrecuencia ENUM ('Diario', 'Semanal', 'Quincenal', 'Mensual') NOT NULL,
    prioridad ENUM ('Bajo', 'Alto') NOT NULL,
    numDocumento VARCHAR(15) NOT NULL,
    estado ENUM ('Pendiente','Finalizado') NOT NULL DEFAULT 'Pendiente',
    id_hotel INT(3) NOT NULL, 
    observaciones TEXT,
    PRIMARY KEY (id),
    FOREIGN KEY (id_hotel) REFERENCES tp_hotel(id),
    FOREIGN KEY (id_habitacion) REFERENCES tp_habitaciones (id),
    FOREIGN KEY (numDocumento) REFERENCES tp_usuarios (numDocumento)
) ENGINE=INNODB;

-- =============================================
-- INSERTS DE DATOS DE EJEMPLO
-- =============================================

-- Insertar usuarios
INSERT INTO tp_usuarios (numDocumento, tipoDocumento, nombres, apellidos, numTelefono, correo, sexo, fechaNacimiento, password, roles) VALUES 
('1014596349', 'Cédula de Ciudadanía', 'Brayan Felipe', 'Pulido Lopez', '3172509298', 'brayan06.pulido@gmail.com', 'Hombre', '2006-03-03', '123456789', 'Administrador'),
('1000289068', 'Cédula de Ciudadanía', 'Favian Alejandro', 'Machuca Pedraza', '3144235027', 'bleachowl98@gmail.com', 'Hombre', '2003-10-15', '123456789', 'Colaborador'),
('1019987917', 'Cédula de Ciudadanía', 'Camilo Andres', 'Guerrero Yanquen', '3027644457', 'camiloagycr321@gmail.com', 'Hombre', '2006-02-15', '123456789', 'Usuario'),
('1014596348', 'Cédula de Ciudadanía', 'Brayan', 'Pulido', '3172509298', 'brayanpulido941@gmail.com', 'Hombre', '2006-03-03', '123456789', 'Administrador'),
('7777888899', 'Pasaporte', 'Roberto', 'Silva Santos', '3007778888', 'roberto.silva@email.com', 'Hombre', '1987-09-25', '123456789', 'Usuario'),
('5555666677', 'Cédula de Ciudadanía', 'María Elena', 'Ramírez Castro', '3005556666', 'maria.ramirez@lodgehub.com', 'Mujer', '1985-12-10', '123456789', 'Administrador'),
('1111222233', 'Cédula de Ciudadanía', 'Usuario', 'Temporal', '3001112222', 'usuario.temporal@email.com', 'Hombre', '1990-01-01', '123456789', 'Usuario'),
('1234567890', 'Cédula de Ciudadanía', 'Ana María', 'González Pérez', '3101234567', 'ana.gonzalez@lodgehub.com', 'Mujer', '1988-05-15', '123456789', 'Colaborador'),
('0987654321', 'Cédula de Ciudadanía', 'Carlos Eduardo', 'Martínez López', '3109876543', 'carlos.martinez@lodgehub.com', 'Hombre', '1992-11-20', '123456789', 'Colaborador');

-- Insertar hoteles
INSERT INTO tp_hotel (id, nit, nombre, direccion, telefono, correo, foto, descripcion, numDocumentoAdmin) VALUES 
(null, '900123456-1', 'Hotel Lodge Hub Premium', 'Calle 123 #45-67, Bogotá, Colombia', '6013334444', 'info@lodgehub.com', '/lodgehub/public/uploads/hoteles/hotel1.png', 'Hotel de lujo ubicado en el corazón de la ciudad, ofreciendo servicios de alta calidad y comodidad excepcional.', '1014596349'),
(null, '900987654-2', 'Lodge Hub Business', 'Carrera 15 #80-25, Medellín, Colombia', '6044445555', 'medellin@lodgehub.com', '/lodgehub/public/uploads/hoteles/hotel1.png', 'Hotel especializado en turismo de negocios con salas de conferencias y servicios corporativos.', '5555666677'),
(null, '900555777-3', 'Lodge Hub Resort', 'Km 5 Vía Cartagena-Barú, Cartagena, Colombia', '6055556666', 'cartagena@lodgehub.com', '/lodgehub/public/uploads/hoteles/hotel2.png', 'Resort frente al mar con spa, piscinas y actividades recreativas para toda la familia.', '1014596348'),
(null, '900111222-4', 'Lodge Hub Express', 'Avenida El Dorado #50-30, Bogotá, Colombia', '6011112222', 'express@lodgehub.com', '/lodgehub/public/uploads/hoteles/hotel1.png', 'Hotel económico con servicios básicos de calidad para viajeros de negocios.', '1234567890');

-- Insertar personal del hotel
INSERT INTO ti_personal (id_hotel, numDocumento, roles) VALUES 
(1, '1014596349', 'Administrador'),
(1, '1000289068', 'Colaborador'),
(1, '1234567890', 'Colaborador'),
(2, '5555666677', 'Administrador'),
(2, '7777888899', 'Colaborador'),
(2, '0987654321', 'Colaborador'),
(3, '1014596348', 'Administrador'),
(3, '7777888899', 'Colaborador'),
(4, '1234567890', 'Administrador'),
(4, '0987654321', 'Colaborador');

-- Insertar tipos de habitación por hotel
INSERT INTO td_tipoHabitacion (descripcion, cantidad, id_hotel) VALUES 
('Individual', 15, 1),
('Doble', 20, 1),
('Suite Junior', 8, 1),
('Suite Ejecutiva', 5, 1),
('Suite Presidencial', 2, 1),

('Individual Business', 12, 2),
('Doble Business', 15, 2),
('Suite Ejecutiva', 6, 2),
('Sala de Juntas', 4, 2),

('Standard Ocean', 25, 3),
('Superior Ocean View', 20, 3),
('Villa Familiar', 10, 3),
('Master Suite', 6, 3),
('Penthouse', 2, 3),

('Individual Express', 20, 4),
('Doble Express', 15, 4);

-- Insertar habitaciones
INSERT INTO tp_habitaciones (numero, costo, capacidad, tipoHabitacion, descripcion, estado, id_hotel) VALUES 

('101', 120000.00, 1, 1, 'Habitación individual premium con baño privado, TV LED 42", WiFi de alta velocidad', 'Disponible', 1),
('102', 120000.00, 1, 1, 'Habitación individual con vista a la ciudad y minibar', 'Disponible', 1),
('103', 120000.00, 1, 1, 'Habitación individual con escritorio ejecutivo', 'Disponible', 1),
('201', 180000.00, 2, 2, 'Habitación doble con cama king, balcón y vista panorámica', 'Disponible', 1),
('202', 180000.00, 2, 2, 'Habitación doble con dos camas queen y sala de estar', 'Disponible', 1),
('203', 180000.00, 2, 2, 'Habitación doble con jacuzzi y vista a la ciudad', 'Ocupada', 1),
('301', 350000.00, 3, 3, 'Suite junior con sala independiente, minibar y terraza', 'Disponible', 1),
('302', 350000.00, 3, 3, 'Suite junior con área de trabajo y sofá cama', 'Disponible', 1),
('401', 500000.00, 4, 4, 'Suite ejecutiva con sala de reuniones, jacuzzi y balcón amplio', 'Disponible', 1),
('501', 800000.00, 6, 5, 'Suite presidencial con salón, comedor, cocina y terraza', 'Disponible', 1),


('B101', 100000.00, 1, 6, 'Habitación individual business con escritorio amplio y silla ergonómica', 'Disponible', 2),
('B102', 100000.00, 1, 6, 'Habitación individual con vista al valle de Aburrá', 'Disponible', 2),
('B201', 150000.00, 2, 7, 'Habitación doble business con área de trabajo para dos personas', 'Disponible', 2),
('B202', 150000.00, 2, 7, 'Habitación doble con conexión empresarial de alta velocidad', 'Disponible', 2),
('B301', 400000.00, 4, 8, 'Suite ejecutiva con sala de juntas para 8 personas', 'Disponible', 2),
('B302', 400000.00, 4, 8, 'Suite ejecutiva con equipos de videoconferencia', 'Disponible', 2),
('SALA1', 0.00, 12, 9, 'Sala de juntas principal con proyector y sistema de audio', 'Disponible', 2),


('C101', 200000.00, 2, 10, 'Habitación standard con vista parcial al océano y aire acondicionado', 'Disponible', 3),
('C102', 200000.00, 2, 10, 'Habitación standard con balcón y acceso directo a jardines', 'Disponible', 3),
('C201', 280000.00, 2, 11, 'Habitación superior con vista completa al océano y balcón amplio', 'Disponible', 3),
('C202', 280000.00, 2, 11, 'Habitación superior con hamaca y vista al atardecer', 'Disponible', 3),
('C301', 450000.00, 4, 12, 'Villa familiar con piscina privada, jardín y área de BBQ', 'Disponible', 3),
('C302', 450000.00, 4, 12, 'Villa familiar con sala, comedor y cocina equipada', 'Disponible', 3),
('C401', 700000.00, 6, 13, 'Master suite con jacuzzi, terraza panorámica y mayordomo', 'Disponible', 3),
('C501', 1200000.00, 8, 14, 'Penthouse con vista 360°, piscina privada y servicio exclusivo', 'Disponible', 3),


('E101', 80000.00, 1, 15, 'Habitación individual económica con servicios básicos de calidad', 'Disponible', 4),
('E102', 80000.00, 1, 15, 'Habitación individual con WiFi y TV cable', 'Disponible', 4),
('E201', 110000.00, 2, 16, 'Habitación doble económica con baño privado', 'Disponible', 4),
('E202', 110000.00, 2, 16, 'Habitación doble con escritorio y área de trabajo', 'Disponible', 4);

-- Insertar huéspedes
INSERT INTO tp_huespedes (numDocumento, numTelefono, correo, nombres, apellidos, tipoDocumento, sexo, id_hotel) VALUES 
('1140915008', '3170560930', 'angelo.gonzalez@gmail.com', 'Angelo', 'González', 'Cédula de Ciudadanía', 'Hombre', 1),
('6666777788', '3006667777', 'sofia.hernandez@email.com', 'Sofía Isabel', 'Hernández Vega', 'Cédula de Ciudadanía', 'Mujer', 1),
('8888999900', '3008889999', 'pedro.jimenez@email.com', 'Pedro Antonio', 'Jiménez Flores', 'Cédula de Ciudadanía', 'Hombre', 1),
('2222333344', '3002223333', 'laura.torres@email.com', 'Laura Cristina', 'Torres Mendoza', 'Cédula de Ciudadanía', 'Mujer', 1),
('9999000011', '3009990000', 'miguel.vargas@email.com', 'Miguel Ángel', 'Vargas Pineda', 'Pasaporte', 'Hombre', 3),
('3333444455', '3003334444', 'carla.ospina@email.com', 'Carla Andrea', 'Ospina Mejía', 'Cédula de Ciudadanía', 'Mujer', 3),
('4444555566', '3004445555', 'diego.castro@email.com', 'Diego Fernando', 'Castro López', 'Cédula de Ciudadanía', 'Hombre', 3),
('5555666699', '3005556669', 'isabella.rodriguez@email.com', 'Isabella', 'Rodríguez Morales', 'Cédula de Ciudadanía', 'Mujer', 3),
('7777888811', '3007778881', 'fernando.lopez@email.com', 'Fernando', 'López Gutiérrez', 'Cédula de Ciudadanía', 'Hombre', 3),
('1122334455', '3011223344', 'patricia.silva@email.com', 'Patricia', 'Silva Ramírez', 'Cédula de Ciudadanía', 'Mujer', 3);

-- Insertar reservas
INSERT INTO tp_reservas (pagoFinal, fechainicio, fechaFin, cantidadAdultos, cantidadNinos, cantidadDiscapacitados, motivoReserva, id_habitacion, metodoPago, informacionAdicional, us_numDocumento, hue_numDocumento, estado, id_hotel) VALUES 
-- Reservas Hotel Premium Bogotá
(360000.00, '2025-09-10', '2025-09-12', 2, 0, 0, 'Personal', 4, 'Tarjeta', 'Luna de miel - solicitan decoración especial', '1000289068', '1140915008', 'Activa', 1),
(700000.00, '2025-09-15', '2025-09-17', 3, 1, 0, 'Familiar', 7, 'PSE', 'Familia con niño de 5 años', '1019987917', '6666777788', 'Activa', 1),
(240000.00, '2025-09-05', '2025-09-07', 1, 0, 0, 'Negocios', 1, 'Efectivo', 'Ejecutivo en viaje de trabajo', '1111222233', '8888999900', 'Finalizada', 1),
(500000.00, '2025-09-20', '2025-09-22', 4, 0, 0, 'Negocios', 9, 'Tarjeta', 'Reunión ejecutiva importante', '1014596349', '2222333344', 'Pendiente', 1),

-- Reservas Hotel Business Medellín  
(300000.00, '2025-09-25', '2025-09-27', 2, 0, 0, 'Negocios', 13, 'PSE', 'Conferencia empresarial', '5555666677', '3333444455', 'Activa', 2),
(800000.00, '2025-10-01', '2025-10-03', 4, 0, 0, 'Negocios', 15, 'Tarjeta', 'Junta directiva - suite ejecutiva', '7777888899', '4444555566', 'Pendiente', 2),

-- Reservas Resort Cartagena
(560000.00, '2025-10-05', '2025-10-07', 2, 0, 0, 'Personal', 19, 'Tarjeta', 'Aniversario de bodas', '1014596348', '5555666699', 'Pendiente', 3),
(900000.00, '2025-10-10', '2025-10-13', 4, 2, 0, 'Familiar', 21, 'PSE', 'Vacaciones familiares con niños', '1000289068', '7777888811', 'Pendiente', 3),

-- Reservas Hotel Express Bogotá
(160000.00, '2025-09-28', '2025-09-30', 1, 0, 0, 'Negocios', 25, 'Efectivo', 'Viaje de trabajo económico', '1234567890', '1122334455', 'Activa', 4),
(220000.00, '2025-10-15', '2025-10-17', 2, 0, 0, 'Personal', 27, 'Tarjeta', 'Pareja joven - presupuesto ajustado', '0987654321', '9999000011', 'Pendiente', 4);

-- Insertar PQRS
INSERT INTO tp_pqrs (tipo, descripcion, numdocumento, prioridad, categoria, estado, id_hotel, respuesta, fechaFinalizacion) VALUES 
-- PQRS Hotel Premium
('Quejas', 'El aire acondicionado de la habitación 203 presentó fallas durante la estadía. Se escuchaban ruidos extraños.', '1014596349', 'Alto', 'Habitación', 'Finalizado', 1, 'Se realizó mantenimiento correctivo al sistema de aire acondicionado. Se cambió el compresor. Disculpas por las molestias ocasionadas.', '2025-09-02 14:30:00'),
('Sugerencias', 'Sería excelente si pudieran agregar más opciones vegetarianas y veganas en el menú del restaurante del hotel.', '1000289068', 'Bajo', 'Servicio', 'Pendiente', 1, NULL, NULL),
('Felicitaciones', 'Excelente atención del personal de recepción, especialmente de la señorita Ana. Muy amables y profesionales en todo momento.', '1019987917', 'Bajo', 'Atención', 'Finalizado', 1, 'Muchas gracias por sus comentarios positivos. Los transmitiremos al equipo de recepción y especialmente a Ana.', '2025-09-03 09:15:00'),
('Peticiones', 'Solicito información detallada sobre descuentos para estadías prolongadas y tarifas corporativas.', '7777888899', 'Bajo', 'Otro', 'Pendiente', 1, NULL, NULL),

-- PQRS Hotel Business
('Quejas', 'La conexión WiFi en la habitación B201 era extremadamente lenta, afectando mi trabajo remoto.', '5555666677', 'Alto', 'Habitación', 'Pendiente', 2, NULL, NULL),
('Peticiones', 'Necesito información sobre salas de conferencias disponibles para el próximo mes y sus tarifas.', '7777888899', 'Bajo', 'Servicio', 'Finalizado', 2, 'Se envió por correo la información completa de nuestras salas de conferencias, disponibilidad y tarifas especiales.', '2025-09-04 16:20:00'),

-- PQRS Resort Cartagena
('Sugerencias', 'Podrían implementar un servicio de spa con masajes en la playa y tratamientos con productos naturales del Caribe.', '1014596348', 'Bajo', 'Servicio', 'Pendiente', 3, NULL, NULL),
('Felicitaciones', 'El resort superó todas nuestras expectativas. La villa familiar es espectacular y el servicio excepcional.', '1000289068', 'Bajo', 'Atención', 'Finalizado', 3, 'Gracias por elegirnos para sus vacaciones familiares. Nos alegra saber que disfrutaron su estadía.', '2025-09-05 11:30:00'),

-- PQRS Hotel Express
('Quejas', 'La habitación E102 tenía problemas con el agua caliente en la ducha durante las mañanas.', '1234567890', 'Alto', 'Habitación', 'Pendiente', 4, NULL, NULL),
('Sugerencias', 'Sería útil tener un servicio de desayuno continental básico incluido en la tarifa.', '0987654321', 'Bajo', 'Servicio', 'Pendiente', 4, NULL, NULL);

-- Insertar mantenimientos
INSERT INTO tp_mantenimiento (id_habitacion, tipo, problemaDescripcion, frecuencia, cantFrecuencia, prioridad, numDocumento, estado, id_hotel, observaciones) VALUES 
-- Mantenimientos Hotel Premium
(6, 'Eléctrico', 'Falla en sistema de aire acondicionado - ruidos anómalos', 'No', NULL, 'Alto', '1234567890', 'Finalizado', 1, 'Se cambió compresor y se realizó limpieza completa del sistema'),
(9, 'Limpieza', 'Limpieza profunda de alfombras y tapicería de suite ejecutiva', 'Sí', 'Quincenal', 'Bajo', '1000289068', 'Pendiente', 1, NULL ),
(10, 'Estructura', 'Revisión y mantenimiento de jacuzzi en suite presidencial', 'Sí', 'Mensual', 'Bajo', '1234567890', 'Pendiente', 1, NULL),

-- Mantenimientos Hotel Business
(13, 'Eléctrico', 'Mejora de conexión WiFi y cableado de red en habitación business', 'No', NULL, 'Alto', '0987654321', 'Pendiente', 2, NULL),
(17, 'Limpieza', 'Mantenimiento equipos audiovisuales sala de juntas', 'Sí', 'Semanal', 'Bajo', '0987654321', 'Pendiente', 2, NULL),

-- Mantenimientos Resort Cartagena
(21, 'Limpieza', 'Limpieza y mantenimiento de piscina privada en villa familiar', 'Sí', 'Semanal', 'Alto', '7777888899', 'Pendiente', 3, NULL),
(24, 'Estructura', 'Mantenimiento de jacuzzi y sistemas de hidromasaje', 'Sí', 'Quincenal', 'Alto', '7777888899', 'Pendiente', 3, NULL),

-- Mantenimientos Hotel Express
(26, 'Eléctrico', 'Reparación sistema agua caliente habitación individual', 'No', NULL, 'Alto', '1234567890', 'Pendiente', 4, NULL),
(27, 'Limpieza', 'Pintura y retoque de paredes habitación doble', 'No', NULL, 'Bajo', '0987654321', 'Pendiente', 4, NULL);

-- Insertar facturas
INSERT INTO tp_factura (id, infoReserva, fechaFactura, infoHotel, total) VALUES 
(NULL, 3, '2025-06-12', 1, 240000.00),    
(NULL, 1, '2025-06-12', 1, 360000.00),     
(NULL, 5, '2025-06-12', 2, 300000.00), 
(NULL, 9, '2025-06-12', 4, 160000.00);    
-- =============================================
-- VISTAS PARA LODGEHUB
-- =============================================

-- Vista: Información completa de habitaciones
CREATE OR REPLACE VIEW v_habitaciones_completa AS
SELECT 
    h.id,
    h.numero,
    h.costo,
    h.capacidad,
    h.foto,
    h.descripcion,
    h.estado,
    h.descripcionMantenimiento,
    h.estadoMantenimiento,
    th.descripcion AS tipoHabitacion,
    hot.nombre AS nombreHotel,
    hot.id AS id_hotel,
    hot.direccion AS direccionHotel
FROM tp_habitaciones h
INNER JOIN td_tipohabitacion th ON h.tipoHabitacion = th.id
INNER JOIN tp_hotel hot ON h.id_hotel = hot.id;

-- Vista: Reservas con información detallada
CREATE OR REPLACE VIEW v_reservas_detalle AS
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
    -- Información del usuario que hizo la reserva
    u.nombres AS nombreUsuario,
    u.apellidos AS apellidoUsuario,
    u.correo AS correoUsuario,
    u.numTelefono AS telefonoUsuario,
    -- Información del huésped
    hue.nombres AS nombreHuesped,
    hue.apellidos AS apellidoHuesped,
    hue.correo AS correoHuesped,
    hue.numTelefono AS telefonoHuesped,
    -- Información de la habitación
    h.numero AS numeroHabitacion,
    h.costo AS costoHabitacion,
    th.descripcion AS tipoHabitacion,
    -- Información del hotel
    hot.nombre AS nombreHotel,
    hot.direccion AS direccionHotel,
    hot.telefono AS telefonoHotel,
    -- Cálculos útiles
    DATEDIFF(r.fechaFin, r.fechainicio) AS diasEstadia,
    (r.cantidadAdultos + r.cantidadNinos + r.cantidadDiscapacitados) AS totalPersonas
FROM tp_reservas r
INNER JOIN tp_usuarios u ON r.us_numDocumento = u.numDocumento
INNER JOIN tp_huespedes hue ON r.hue_numDocumento = hue.numDocumento
INNER JOIN tp_habitaciones h ON r.id_habitacion = h.id
INNER JOIN td_tipohabitacion th ON h.tipoHabitacion = th.id
INNER JOIN tp_hotel hot ON r.id_hotel = hot.id;

-- Vista: PQRS con información del usuario y hotel
CREATE OR REPLACE VIEW v_pqrs_detalle AS
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
    -- Información del usuario
    u.nombres AS nombreUsuario,
    u.apellidos AS apellidoUsuario,
    u.correo AS correoUsuario,
    u.numTelefono AS telefonoUsuario,
    -- Información del hotel
    h.nombre AS nombreHotel,
    h.direccion AS direccionHotel,
    -- Cálculo de días transcurridos
    DATEDIFF(CURRENT_DATE, DATE(p.fechaRegistro)) AS diasTranscurridos,
    CASE 
        WHEN p.estado = 'Pendiente' AND CURRENT_DATE > p.fechaLimite THEN 'Vencida'
        WHEN p.estado = 'Pendiente' AND CURRENT_DATE <= p.fechaLimite THEN 'En plazo'
        ELSE 'Finalizada'
    END AS estadoPlazo
FROM tp_pqrs p
INNER JOIN tp_usuarios u ON p.numdocumento = u.numDocumento
INNER JOIN tp_hotel h ON p.id_hotel = h.id;

-- Vista: Mantenimientos con información detallada
CREATE OR REPLACE VIEW v_mantenimiento_detalle AS
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
    m.observaciones,
    -- Información de la habitación
    h.numero AS numeroHabitacion,
    th.descripcion AS tipoHabitacion,
    h.estado AS estadoHabitacion,
    -- Información del responsable
    u.nombres AS nombreResponsable,
    u.apellidos AS apellidoResponsable,
    u.correo AS correoResponsable,
    -- Información del hotel
    hot.nombre AS nombreHotel,
    -- Cálculos útiles
    DATEDIFF(CURRENT_DATE, DATE(m.fechaRegistro)) AS diasPendientes
FROM tp_mantenimiento m
INNER JOIN tp_habitaciones h ON m.id_habitacion = h.id
INNER JOIN td_tipohabitacion th ON h.tipoHabitacion = th.id
INNER JOIN tp_usuarios u ON m.numDocumento = u.numDocumento
INNER JOIN tp_hotel hot ON m.id_hotel = hot.id;

-- Vista: Personal del hotel con información completa
CREATE OR REPLACE VIEW v_personal_hotel AS
SELECT 
    p.id_hotel,
    p.numDocumento,
    p.roles AS rolesPersonal,
    -- Información del usuario
    u.nombres,
    u.apellidos,
    u.correo,
    u.numTelefono,
    u.tipoDocumento,
    u.sexo,
    u.fechaNacimiento,
    u.roles AS rolSistema,
    -- Información del hotel
    h.nombre AS nombreHotel,
    h.direccion AS direccionHotel,
    h.telefono AS telefonoHotel,
    h.correo AS correoHotel
FROM ti_personal p
INNER JOIN tp_usuarios u ON p.numDocumento = u.numDocumento
INNER JOIN tp_hotel h ON p.id_hotel = h.id;

-- Vista: Dashboard de ocupación por hotel
CREATE OR REPLACE VIEW v_ocupacion_hotel AS
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

-- Vista: Estadísticas de reservas por mes
CREATE OR REPLACE VIEW v_estadisticas_reservas AS
SELECT 
    YEAR(r.fechaRegistro) AS año,
    MONTH(r.fechaRegistro) AS mes,
    MONTHNAME(r.fechaRegistro) AS nombreMes,
    r.id_hotel,
    h.nombre AS nombreHotel,
    COUNT(r.id) AS totalReservas,
    SUM(r.pagoFinal) AS ingresoTotal,
    AVG(r.pagoFinal) AS promedioReserva,
    SUM(CASE WHEN r.estado = 'Activa' THEN 1 ELSE 0 END) AS reservasActivas,
    SUM(CASE WHEN r.estado = 'Finalizada' THEN 1 ELSE 0 END) AS reservasFinalizadas,
    SUM(CASE WHEN r.estado = 'Cancelada' THEN 1 ELSE 0 END) AS reservasCanceladas
FROM tp_reservas r
INNER JOIN tp_hotel h ON r.id_hotel = h.id
GROUP BY YEAR(r.fechaRegistro), MONTH(r.fechaRegistro), r.id_hotel, h.nombre;

-- Vista: Habitaciones disponibles por fechas
CREATE OR REPLACE VIEW v_habitaciones_disponibles AS
SELECT 
    h.id,
    h.numero,
    h.costo,
    h.capacidad,
    h.foto,
    h.descripcion,
    th.descripcion AS tipoHabitacion,
    hot.id AS id_hotel,
    hot.nombre AS nombreHotel,
    hot.direccion AS direccionHotel
FROM tp_habitaciones h
INNER JOIN td_tipohabitacion th ON h.tipoHabitacion = th.id
INNER JOIN tp_hotel hot ON h.id_hotel = hot.id
WHERE h.estado = 'Disponible' 
  AND h.estadoMantenimiento = 'Activo'
  AND h.id NOT IN (
      SELECT r.id_habitacion 
      FROM tp_reservas r 
      WHERE r.estado IN ('Activa', 'Pendiente')
  );

-- =============================================
-- ÍNDICES PARA OPTIMIZACIÓN DE CONSULTAS
-- =============================================

-- Índices para tabla tp_usuarios
CREATE INDEX idx_usuarios_correo ON tp_usuarios (correo);
CREATE INDEX idx_usuarios_roles ON tp_usuarios (roles);
CREATE INDEX idx_usuarios_tipo_doc ON tp_usuarios (tipoDocumento);

-- Índices para tabla tp_hotel
CREATE INDEX idx_hotel_admin ON tp_hotel (numDocumentoAdmin);
CREATE INDEX idx_hotel_nombre ON tp_hotel (nombre);

-- Índices para tabla tp_habitaciones
CREATE INDEX idx_habitaciones_estado ON tp_habitaciones (estado);
CREATE INDEX idx_habitaciones_hotel_estado ON tp_habitaciones (id_hotel, estado);
CREATE INDEX idx_habitaciones_tipo ON tp_habitaciones (tipoHabitacion);
CREATE INDEX idx_habitaciones_costo ON tp_habitaciones (costo);
CREATE INDEX idx_habitaciones_capacidad ON tp_habitaciones (capacidad);
CREATE INDEX idx_habitaciones_estado_mant ON tp_habitaciones (estadoMantenimiento);

-- Índices para tabla tp_reservas
CREATE INDEX idx_reservas_fechas ON tp_reservas (fechainicio, fechaFin);
CREATE INDEX idx_reservas_estado ON tp_reservas (estado);
CREATE INDEX idx_reservas_hotel ON tp_reservas (id_hotel);
CREATE INDEX idx_reservas_habitacion ON tp_reservas (id_habitacion);
CREATE INDEX idx_reservas_usuario ON tp_reservas (us_numDocumento);
CREATE INDEX idx_reservas_huesped ON tp_reservas (hue_numDocumento);
CREATE INDEX idx_reservas_fecha_registro ON tp_reservas (fechaRegistro);
CREATE INDEX idx_reservas_hotel_estado ON tp_reservas (id_hotel, estado);
CREATE INDEX idx_reservas_fechas_estado ON tp_reservas (fechainicio, fechaFin, estado);

-- Índices para tabla tp_pqrs
CREATE INDEX idx_pqrs_estado ON tp_pqrs (estado);
CREATE INDEX idx_pqrs_tipo ON tp_pqrs (tipo);
CREATE INDEX idx_pqrs_prioridad ON tp_pqrs (prioridad);
CREATE INDEX idx_pqrs_categoria ON tp_pqrs (categoria);
CREATE INDEX idx_pqrs_hotel ON tp_pqrs (id_hotel);
CREATE INDEX idx_pqrs_fecha_registro ON tp_pqrs (fechaRegistro);
CREATE INDEX idx_pqrs_fecha_limite ON tp_pqrs (fechaLimite);
CREATE INDEX idx_pqrs_usuario ON tp_pqrs (numdocumento);
CREATE INDEX idx_pqrs_hotel_estado ON tp_pqrs (id_hotel, estado);

-- Índices para tabla tp_mantenimiento
CREATE INDEX idx_mantenimiento_estado ON tp_mantenimiento (estado);
CREATE INDEX idx_mantenimiento_tipo ON tp_mantenimiento (tipo);
CREATE INDEX idx_mantenimiento_prioridad ON tp_mantenimiento (prioridad);
CREATE INDEX idx_mantenimiento_habitacion ON tp_mantenimiento (id_habitacion);
CREATE INDEX idx_mantenimiento_hotel ON tp_mantenimiento (id_hotel);
CREATE INDEX idx_mantenimiento_responsable ON tp_mantenimiento (numDocumento);
CREATE INDEX idx_mantenimiento_fecha ON tp_mantenimiento (fechaRegistro);
CREATE INDEX idx_mantenimiento_hotel_estado ON tp_mantenimiento (id_hotel, estado);

-- Índices para tabla tp_huespedes
CREATE INDEX idx_huespedes_correo ON tp_huespedes (correo);
CREATE INDEX idx_huespedes_nombres ON tp_huespedes (nombres, apellidos);
CREATE INDEX idx_huespedes_tipo_doc ON tp_huespedes (tipoDocumento);
CREATE INDEX idx_huespedes_fecha_creacion ON tp_huespedes (fechaCreacion);

-- Índices para tabla ti_personal
CREATE INDEX idx_personal_hotel ON ti_personal (id_hotel);
CREATE INDEX idx_personal_roles ON ti_personal (roles);

-- Índices para tabla tp_factura
CREATE INDEX idx_factura_reserva ON tp_factura (infoReserva);
CREATE INDEX idx_factura_hotel ON tp_factura (infoHotel);
CREATE INDEX idx_factura_fecha ON tp_factura (fechaFactura);

-- Índices para tabla td_tipoHabitacion
CREATE INDEX idx_tipo_habitacion_hotel ON td_tipohabitacion (id_hotel);
CREATE INDEX idx_tipo_habitacion_desc ON td_tipohabitacion (descripcion);

-- =============================================
-- ÍNDICES COMPUESTOS PARA CONSULTAS ESPECÍFICAS
-- =============================================

-- Para búsquedas de disponibilidad de habitaciones por fechas y hotel
CREATE INDEX idx_reservas_disponibilidad ON tp_reservas (id_hotel, id_habitacion, fechainicio, fechaFin, estado);

-- Para reportes de ocupación
CREATE INDEX idx_habitaciones_ocupacion ON tp_habitaciones (id_hotel, estado, estadoMantenimiento);

-- Para estadísticas por período
CREATE INDEX idx_reservas_estadisticas ON tp_reservas (id_hotel, fechaRegistro, estado);

-- Para consultas de mantenimiento pendiente
CREATE INDEX idx_mantenimiento_pendiente ON tp_mantenimiento (id_hotel, estado, prioridad, fechaRegistro);

-- Para PQRS por prioridad y estado
CREATE INDEX idx_pqrs_gestion ON tp_pqrs (id_hotel, estado, prioridad, fechaRegistro);

