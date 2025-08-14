CREATE DATABASE IF NOT EXISTS Lodgehub;
USE Lodgehub;

CREATE TABLE IF NOT EXISTS tp_usuarios (numDocumento VARCHAR(15) NOT NULL,
                                        tipoDocumento ENUM ('Cédula de Ciudadanía','Tarjeta de Identidad','Cedula de Extranjeria','Pasaporte','Registro Civil') NOT NULL, 
                                        nombres VARCHAR(50) NOT NULL,
                                        apellidos VARCHAR(50) NOT NULL,
                                        numTelefono VARCHAR (15) NOT NULL,
                                        correo VARCHAR(30) NOT NULL,
                                        sexo ENUM ('Hombre','Mujer','Otro','Prefiero no decirlo') NOT NULL,
                                        fechaNacimiento DATE NOT NULL,
                                        password varchar (255) NOT NULL,
                                        foto varchar (255),
                                        solicitarContraseña ENUM('0','1') DEFAULT '0',
                                        tokenPassword varchar (100) ,
                                        sesionCaducada ENUM('1','0') NOT NULL DEFAULT '1',
                                        roles ENUM ('Administrador','Colaborador','Usuario') NOT NULL,
                                        
                                        PRIMARY KEY (numdocumento)          
                                                )ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS tp_huespedes (numDocumento VARCHAR(15) NOT NULL,
                                        numTelefono VARCHAR(15) NOT NULL,
                                        correo VARCHAR(30) NOT NULL,
                                        nombres VARCHAR(50) NOT NULL,
                                        apellidos VARCHAR(50) NOT NULL,
                                        tipoDocumento ENUM ('Cedula de Ciudadania','Tarjeta de Identidad','Cedula de Extranjeria','Pasaporte','Registro Civil') NOT NULL,
                                        sexo ENUM ('Hombre','Mujer','Otro','Prefiero no decirlo') NOT NULL,
                                        -- NUEVO: Campos de auditoría
                                        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                        fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                        
                                        PRIMARY KEY (numDocumento),
                                        UNIQUE KEY uk_correo (correo)
) ENGINE=INNODB;

                                            
CREATE TABLE IF NOT EXISTS td_tipoHabitacion (id INT (3) AUTO_INCREMENT NOT NULL,
                                            descripcion VARCHAR (20) NOT NULL,
                                            cantidad INT (3) NOT NULL DEFAULT 0,

                                            PRIMARY KEY (id)
                                            )ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS tp_habitaciones (numero VARCHAR (5) NOT NULL,
                                            costo DECIMAL (10,2) NOT NULL, 
                                            capacidad INT (3) NOT NULL,
                                            tipoHabitacion INT (3) NOT NULL,
                                            foto VARCHAR (255) DEFAULT NULL,
                                            descripcion TEXT DEFAULT NULL,
                                            estado ENUM ('Disponible', 'Reservada', 'Ocupada', 'Mantenimiento') NOT NULL,
                                            descripcionMantenimiento TEXT DEFAULT NULL,
                                            estadoMantenimiento ENUM ('Activo','Inactivo') NOT NULL DEFAULT 'Activo',

                                            PRIMARY KEY (numero),
                                            FOREIGN KEY (tipoHabitacion) REFERENCES td_tipohabitacion (id)
                                            )ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS tp_pqrs (id INT (10) AUTO_INCREMENT NOT NULL,
                                    fechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
                                    fechaLimite  DATE DEFAULT (DATE_ADD(CURRENT_DATE, INTERVAL 5 DAY)),
                                    tipo ENUM ('Peticiones','Quejas','Reclamos','Sugerencias','Felicitaciones') NOT NULL,
                                    descripcion TEXT NOT NULL,
                                    numdocumento VARCHAR (15) NOT NULL,
                                    prioridad ENUM ('Bajo','Alto') NOT NULL,
                                    categoria ENUM ('Servicio','Habitación','Atención','Otro') NOT  NULL,
                                    estado ENUM ('Pendiente', 'Finalizado') NOT NULL,
                                    fechaFinalizacion DATETIME DEFAULT NULL,
                                    respuesta TEXT DEFAULT NULL,

                                    PRIMARY KEY (id),
                                    FOREIGN KEY (numdocumento) REFERENCES tp_usuarios (numDocumento)
                                    ) ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS tp_reservas (id INT (3) AUTO_INCREMENT NOT NULL,
                                        pagoFinal DECIMAL(30,2) NOT NULL, -- se multiplica la habitación por la cantidad de días
                                        fechainicio DATE NOT NULL,
                                        fechaFin DATE NOT NULL,
                                        cantidadAdultos INT (2),
                                        cantidadNinos INT (2),
                                        cantidadDiscapacitados INT (2), 
                                        motivoReserva ENUM ('Negocios','Personal','Viaje','Familiar', 'Otro') NOT NULL,
                                        numeroHabitacion VARCHAR (10) NOT NULL,
                                        metodoPago ENUM ('Tarjeta','Efectivo','PSE') NOT NULL,
                                        informacionAdicional TEXT,
                                        us_numDocumento VARCHAR (15) NOT NULL,
                                        hue_numDocumento VARCHAR (15) NOT NULL,
                                        estado ENUM ('Activa', 'Cancelada', 'Finalizada', 'Pendiente') NOT NULL,
                                        fechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,


                                        PRIMARY KEY (id),
                                        FOREIGN KEY (numeroHabitacion) REFERENCES tp_habitaciones (numero),
                                        FOREIGN KEY (us_numdocumento) REFERENCES tp_usuarios (numDocumento),
                                        FOREIGN KEY (hue_numdocumento) REFERENCES tp_huespedes (numDocumento)
                                        )ENGINE=INNODB;

create table if not exists tp_hotel (id INT (3) AUTO_INCREMENT NOT NULL,
                                    nit VARCHAR(20) UNIQUE NOT NULL,    -- NIT único pero no PK
                                    nombre VARCHAR(100) NOT NULL,
                                    direccion VARCHAR(200),
                                    numDocumento VARCHAR(15) NOT NULL,  -- Referencia al administrador del hotel
                                    telefono VARCHAR(15),
                                    correo VARCHAR(100),
                                    foto varchar (255),
                                    descripcion TEXT,

                                    PRIMARY KEY (id),
                                    FOREIGN KEY (numDocumento) REFERENCES tp_usuarios (numDocumento)
                                    )ENGINE=INNODB;                                        
                                    
CREATE TABLE IF NOT EXISTS tp_factura (id INT (3) AUTO_INCREMENT NOT NULL,
                                    infoReserva INT (3) NOT NULL,
                                    fechaFactura DATETIME DEFAULT CURRENT_TIMESTAMP,
                                    infoHotel INT (3) NOT NULL,
                                    total DECIMAL (30,2) NOT NULL,

                                    PRIMARY KEY (id),
                                    FOREIGN KEY (infoReserva) REFERENCES tp_reservas (id),
                                    FOREIGN KEY (infoHotel) REFERENCES tp_hotel (id)
                                    )ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS tp_mantenimiento (id INT (4) AUTO_INCREMENT NOT NULL,
                                            numeroHabitacion VARCHAR (5) NOT NULL,
                                            tipo ENUM ('Limpieza','Estructura','Eléctrico','Otro') NOT NULL,
                                            problemaDescripcion VARCHAR (50) NOT NULL,
                                            fechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
                                            ultimaActualizacion DATETIME DEFAULT CURRENT_TIMESTAMP,
                                            frecuencia ENUM ('Sí', 'No') NOT NULL,
                                            cantFrecuencia ENUM ('Diario', 'Semanal', 'Quincenal', 'Mensual') NOT NULL,
                                            prioridad ENUM ('Bajo', 'Alto') NOT NULL,
                                            numDocumento VARCHAR (15) NOT NULL,
                                            estado ENUM ('Pendiente','Finalizado') NOT NULL DEFAULT 'Pendiente',

                                            PRIMARY KEY (id),
                                            FOREIGN KEY (numeroHabitacion) REFERENCES tp_habitaciones (numero),
                                            FOREIGN KEY (numDocumento) REFERENCES tp_usuarios (numDocumento)
                                            )ENGINE=INNODB;

/*inserts*/

INSERT INTO tp_usuarios (numDocumento, tipoDocumento, nombres, apellidos, numTelefono, correo, sexo, fechaNacimiento, password, foto, solicitarContraseña, tokenPassword, sesionCaducada, roles) VALUES
('1000289068', 'Cédula de Ciudadanía', 'Favian Alejandro', 'Machuca Pedraza', '3116182673', 'bleachowl98@gmail.com', 'Hombre', '2003-10-15', '123456789', 'foto_favian', '0', NULL, '1', 'Colaborador'),
('1014596349', 'Cédula de Ciudadanía', 'Brayan Felipe', 'Pulido Lopez', '3172509298', 'brayan06.pulido@gmail.com', 'Hombre', '2006-03-03', '123456789', 'foto_brayan.jpg', '0', NULL, '1', 'Administrador'),
('1019987917', 'Cédula de Ciudadanía', 'Camilo Andrés', 'Guerrero Yanquen', '3027644457', 'camiloagycr321@gmail.com', 'Hombre', '2006-02-15', '123456789', 'foto_camilo.jpg', '0', NULL, '1', 'Usuario'),
('1234567890', 'Cédula de Ciudadanía', 'Juan Carlos', 'Pérez García', '3001234567', 'juan.perez@lodgehub.com', 'Hombre', '1985-03-15', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'foto_juan.jpg', '0', NULL, '1', 'Administrador'),
('9876543210', 'Cédula de Ciudadanía', 'María Fernanda', 'González López', '3109876543', 'maria.gonzalez@lodgehub.com', 'Mujer', '1990-07-22', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'foto_maria.jpg', '0', NULL, '1', 'Colaborador'),
('5555666677', 'Cédula de Ciudadanía', 'Carlos Eduardo', 'Ramírez Silva', '3205556666', 'carlos.ramirez@lodgehub.com', 'Hombre', '1988-11-10', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'foto_carlos.jpg', '0', NULL, '1', 'Usuario');




INSERT INTO tp_huespedes (numDocumento, numTelefono, correo, nombres, apellidos, tipoDocumento, sexo) VALUES
('1014596349', '3172509298', 'brayan06.pulido@gmail.com', 'Brayan Felipe', 'Pulido Lopez', 'Cédula de Ciudadanía', 'Hombre'),
('1000289068', '3116182673', 'Bleachowl98@gmail.com', 'Favian ALejandro', 'Machuca Pedraza', 'Cédula de Ciudadanía', 'Hombre'),
('1019987917', '3027644457', 'camiloagycr321@gmail.com', 'Camilo Andrés', 'Guerrero Yanquen', 'Cédula de Ciudadanía', 'Hombre');

INSERT INTO td_tipoHabitacion (descripcion, cantidad) VALUES
('Individual', 10),
('Doble', 15),
('Suite', 5);

INSERT INTO tp_habitaciones (numero, costo, capacidad, tipoHabitacion, foto, descripcion, estado, descripcionMantenimiento, estadoMantenimiento) VALUES
('101', 80000.00, 1, 1, 'hab_101.jpg', 'Habitación individual con vista al jardín, incluye TV, WiFi y baño privado', 'Disponible', NULL, 'Activo'),
('201', 120000.00, 2, 2, 'hab_201.jpg', 'Habitación doble con balcón, aire acondicionado, TV LCD y minibar', 'Ocupado', NULL, 'Activo'),
('301', 250000.00, 4, 3, 'hab_301.jpg', 'Suite presidencial con sala, comedor, jacuzzi y vista panorámica', 'Mantenimiento', 'Reparación del sistema de aire acondicionado', 'Activo');


INSERT INTO tp_pqrs (tipo, descripcion, numdocumento, prioridad, categoria, estado, fechaFinalizacion, respuesta) VALUES
('Quejas', 'El aire acondicionado de la habitación 201 no funciona correctamente, hace mucho ruido durante la noche', '5555666677', 'Alto', 'Habitación', 'Pendiente', NULL, NULL),
('Sugerencias', 'Sería excelente si pudieran incluir un servicio de desayuno buffet en el restaurante del hotel', '5555666677', 'Bajo', 'Servicio', 'Finalizado', '2025-01-10 14:30:00', 'Gracias por su sugerencia. La implementaremos en el próximo trimestre.'),
('Peticiones', 'Solicito información sobre tarifas especiales para empresas y descuentos por estadías prolongadas', '5555666677', 'Bajo', 'Servicio', 'Finalizado', '2025-01-08 10:15:00', 'Le enviamos la información a su correo electrónico.');


INSERT INTO tp_hotel (nit, nombre, direccion, numDocumento, telefono, correo) VALUES
('900123456-1', 'LodgeHub Plaza Hotel', 'Carrera 15 #85-23, Zona Rosa, Bogotá', '1234567890', '6015551234', 'info@lodgehubplaza.com'),
('900789123-4', 'LodgeHub Business Center', 'Avenida 68 #45-67, Centro Internacional, Bogotá', '1234567890', '6017891234', 'reservas@lodgehubbusiness.com'),
('900456789-7', 'LodgeHub Garden Resort', 'Km 5 Vía La Calera, Cundinamarca', '9876543210', '6014567890', 'contacto@lodgehubgarden.com'),
('900234567-2', 'LodgeHub Beach Resort', 'Kilómetro 12 Vía Santa Marta, Rodadero', '1000289068', '6054321890', 'reservas@lodgehubbeach.com'),
('900345678-3', 'LodgeHub Mountain Lodge', 'Vereda El Chico, Vía La Vega', '1014596349', '6018765432', 'info@lodgehubmountain.com'),
('900567890-5', 'LodgeHub City Suites', 'Calle 53 #45-25, El Poblado, Medellín', '5555666677', '6043216547', 'contacto@lodgehubcity.com'),
('900678901-6', 'LodgeHub Colonial Inn', 'Carrera 3 #18-56, Centro Histórico, Cartagena', '1234567890', '6057654321', 'reservas@lodgehubcolonial.com'),
('900789012-8', 'LodgeHub Eco Retreat', 'Vía Parque Tayrona, Magdalena', '9876543210', '6056543210', 'info@lodgehubeco.com'),
('900890123-9', 'LodgeHub Executive Tower', 'Avenida Boyacá #15-30, Chapinero Alto, Bogotá', '1014596349', '6019876543', 'reservas@lodgehubexecutive.com');


INSERT INTO tp_reservas (pagoFinal, fechainicio, fechaFin, cantidadAdultos, cantidadNinos, cantidadDiscapacitados, motivoReserva, numeroHabitacion, metodoPago, informacionAdicional, us_numDocumento, hue_numDocumento, estado) VALUES
(240000.00, '2025-02-15', '2025-02-18', 2, 0, 0, 'Personal', '201', 'Tarjeta', 'Solicitan habitación en piso alto con vista. Celebración de aniversario.', '1000289068', '1014596349', 'Activa'),
(160000.00, '2025-02-20', '2025-02-22', 1, 0, 0, 'Negocios', '101', 'PSE', 'Requiere facturación a nombre de la empresa. Check-in tardío.', '1014596349', '1000289068', 'Pendiente');


INSERT INTO tp_factura (infoReserva, infoHotel, total) VALUES
(1, 1, 240000.00),
(2, 1, 160000.00);


INSERT INTO tp_mantenimiento (numeroHabitacion, tipo, problemaDescripcion, frecuencia, cantFrecuencia, prioridad, numDocumento, estado) VALUES
('301', 'Eléctrico', 'Aire acondicionado no enciende correctamente', 'No', 'Diario', 'Alto', '9876543210', 'Pendiente'),
('101', 'Limpieza', 'Limpieza profunda de alfombras y cortinas', 'Sí', 'Mensual', 'Bajo', '9876543210', 'Finalizado'),
('201', 'Estructura', 'Revisión de grietas en el techo del baño', 'No', 'Diario', 'Alto', '1234567890', 'Pendiente');




/*vistas*/


CREATE OR REPLACE VIEW Vista_usuarios AS
SELECT 
    u.numDocumento,
    u.tipoDocumento,
    u.nombres,
    u.apellidos,
    CONCAT(u.nombres, ' ', u.apellidos) AS nombreCompleto,
    u.numTelefono,
    u.correo,
    u.sexo,
    u.fechaNacimiento,
    TIMESTAMPDIFF(YEAR, u.fechaNacimiento, CURDATE()) AS edad,
    u.foto,
    u.sesionCaducada AS estadoSesion,
    u.roles,
    -- Contar reservas del usuario
    COUNT(DISTINCT r.id) AS totalReservas,
    -- Contar PQRS del usuario
    COUNT(DISTINCT p.id) AS totalPQRS,
    -- Contar mantenimientos registrados por el usuario
    COUNT(DISTINCT m.id) AS mantenimientosRegistrados
FROM tp_usuarios u
    LEFT JOIN tp_reservas r ON u.numDocumento = r.us_numDocumento
    LEFT JOIN tp_pqrs p ON u.numDocumento = p.numdocumento
    LEFT JOIN tp_mantenimiento m ON u.numDocumento = m.numDocumento
GROUP BY u.numDocumento;

-- 2. VISTA_RESERVAS
-- Muestra información completa de reservas con datos relacionados
CREATE OR REPLACE VIEW Vista_reservas AS
SELECT 
    r.id AS reservaId,
    r.pagoFinal,
    r.fechainicio,
    r.fechaFin,
    DATEDIFF(r.fechaFin, r.fechainicio) AS diasEstadia,
    r.cantidadAdultos,
    r.cantidadNinos,
    r.cantidadDiscapacitados,
    (r.cantidadAdultos + r.cantidadNinos + r.cantidadDiscapacitados) AS totalPersonas,
    r.motivoReserva,
    r.metodoPago,
    r.informacionAdicional,
    r.estado AS estadoReserva,
    r.fechaRegistro,
    -- Datos del usuario que hizo la reserva
    u.nombres AS usuarioNombres,
    u.apellidos AS usuarioApellidos,
    CONCAT(u.nombres, ' ', u.apellidos) AS usuarioCompleto,
    u.correo AS usuarioCorreo,
    u.numTelefono AS usuarioTelefono,
    -- Datos del huésped
    h.nombres AS huespedNombres,
    h.apellidos AS huespedApellidos,
    CONCAT(h.nombres, ' ', h.apellidos) AS huespedCompleto,
    h.correo AS huespedCorreo,
    h.numTelefono AS huespedTelefono,
    h.tipoDocumento AS huespedTipoDoc,
    -- Datos de la habitación
    hab.numero AS numeroHabitacion,
    hab.costo AS costoHabitacion,
    hab.capacidad,
    hab.estado AS estadoHabitacion,
    th.descripcion AS tipoHabitacion,
    -- Cálculo del costo por día
    ROUND(r.pagoFinal / DATEDIFF(r.fechaFin, r.fechainicio), 2) AS costoPorDia
FROM tp_reservas r
    INNER JOIN tp_usuarios u ON r.us_numDocumento = u.numDocumento
    INNER JOIN tp_huespedes h ON r.hue_numDocumento = h.numDocumento
    INNER JOIN tp_habitaciones hab ON r.numeroHabitacion = hab.numero
    INNER JOIN td_tipoHabitacion th ON hab.tipoHabitacion = th.id;

-- 3. VISTA_HABITACIONES
-- Muestra información completa de habitaciones
CREATE OR REPLACE VIEW Vista_habitaciones AS
SELECT 
    h.numero,
    h.costo,
    h.capacidad,
    h.foto,
    h.descripcion,
    h.estado,
    h.descripcionMantenimiento,
    h.estadoMantenimiento,
    -- Información del tipo de habitación
    th.id AS tipoHabitacionId,
    th.descripcion AS tipoHabitacion,
    th.cantidad AS cantidadTipoDisponible,
    -- Estadísticas de reservas
    COUNT(DISTINCT r.id) AS totalReservas,
    COUNT(CASE WHEN r.estado = 'Activa' THEN 1 END) AS reservasActivas,
    COUNT(CASE WHEN r.estado = 'Pendiente' THEN 1 END) AS reservasPendientes,
    -- Ingresos generados
    SUM(CASE WHEN r.estado != 'Cancelada' THEN r.pagoFinal ELSE 0 END) AS ingresosTotales,
    -- Promedio de ingresos por reserva
    AVG(CASE WHEN r.estado != 'Cancelada' THEN r.pagoFinal ELSE NULL END) AS promedioIngresos,
    -- Información de mantenimiento
    COUNT(DISTINCT m.id) AS totalMantenimientos,
    COUNT(CASE WHEN m.estado = 'Pendiente' THEN 1 END) AS mantenimientosPendientes,
    MAX(m.fechaRegistro) AS ultimoMantenimiento,
    -- Última reserva
    MAX(r.fechaRegistro) AS ultimaReserva,
    -- Próxima reserva
    MIN(CASE WHEN r.fechainicio > CURDATE() AND r.estado IN ('Activa', 'Pendiente') 
             THEN r.fechainicio ELSE NULL END) AS proximaReserva
FROM tp_habitaciones h
    INNER JOIN td_tipoHabitacion th ON h.tipoHabitacion = th.id
    LEFT JOIN tp_reservas r ON h.numero = r.numeroHabitacion
    LEFT JOIN tp_mantenimiento m ON h.numero = m.numeroHabitacion
GROUP BY h.numero;

-- 5. VISTA_MANTENIMIENTO
-- Muestra información completa de mantenimientos
CREATE OR REPLACE VIEW Vista_mantenimiento AS
SELECT 
    m.id AS mantenimientoId,
    m.tipo,
    m.problemaDescripcion,
    m.fechaRegistro,
    m.ultimaActualizacion,
    m.frecuencia,
    m.cantFrecuencia,
    m.prioridad,
    m.estado,
    -- Días transcurridos desde el registro
    DATEDIFF(CURDATE(), DATE(m.fechaRegistro)) AS diasTranscurridos,
    -- Información de la habitación
    h.numero AS numeroHabitacion,
    h.estado AS estadoHabitacion,
    h.descripcionMantenimiento,
    th.descripcion AS tipoHabitacion,
    h.costo AS costoHabitacion,
    -- Información del usuario que registró
    u.nombres AS responsableNombres,
    u.apellidos AS responsableApellidos,
    CONCAT(u.nombres, ' ', u.apellidos) AS responsableCompleto,
    u.roles AS rolResponsable,
    u.numTelefono AS telefonoResponsable,
    -- Estado crítico (más de 7 días pendiente con prioridad alta)
    CASE 
        WHEN m.estado = 'Pendiente' AND m.prioridad = 'Alto' 
             AND DATEDIFF(CURDATE(), DATE(m.fechaRegistro)) > 7 
        THEN 'CRÍTICO'
        WHEN m.estado = 'Pendiente' AND DATEDIFF(CURDATE(), DATE(m.fechaRegistro)) > 15 
        THEN 'URGENTE'
        ELSE 'NORMAL'
    END AS nivelUrgencia
FROM tp_mantenimiento m
    INNER JOIN tp_habitaciones h ON m.numeroHabitacion = h.numero
    INNER JOIN td_tipoHabitacion th ON h.tipoHabitacion = th.id
    INNER JOIN tp_usuarios u ON m.numDocumento = u.numDocumento;

-- 6. VISTA_FACTURA
-- Muestra información completa de facturas
CREATE OR REPLACE VIEW Vista_factura AS
SELECT 
    f.id AS facturaId,
    f.fechaFactura,
    f.total,
    -- Información de la reserva
    r.id AS reservaId,
    r.fechainicio,
    r.fechaFin,
    DATEDIFF(r.fechaFin, r.fechainicio) AS diasEstadia,
    r.pagoFinal,
    r.estado AS estadoReserva,
    r.metodoPago,
    -- Información del huésped
    h.nombres AS huespedNombres,
    h.apellidos AS huespedApellidos,
    CONCAT(h.nombres, ' ', h.apellidos) AS huespedCompleto,
    h.numDocumento AS huespedDocumento,
    h.correo AS huespedCorreo,
    -- Información de la habitación
    hab.numero AS numeroHabitacion,
    th.descripcion AS tipoHabitacion,
    hab.costo AS costoHabitacion,
    -- Información del hotel
    hotel.id AS hotelId,
    hotel.nit AS hotelNit,
    hotel.nombre AS hotelNombre,
    hotel.direccion AS hotelDireccion,
    hotel.telefono AS hotelTelefono,
    hotel.correo AS hotelCorreo,
    -- Información del administrador del hotel
    admin.nombres AS adminNombres,
    admin.apellidos AS adminApellidos,
    CONCAT(admin.nombres, ' ', admin.apellidos) AS adminCompleto,
    -- Cálculos
    ROUND(f.total / DATEDIFF(r.fechaFin, r.fechainicio), 2) AS costoPorDia,
    CASE 
        WHEN f.total = r.pagoFinal THEN 'COMPLETO'
        WHEN f.total < r.pagoFinal THEN 'PARCIAL'
        ELSE 'EXCEDIDO'
    END AS tipoFacturacion
FROM tp_factura f
    INNER JOIN tp_reservas r ON f.infoReserva = r.id
    INNER JOIN tp_huespedes h ON r.hue_numDocumento = h.numDocumento
    INNER JOIN tp_habitaciones hab ON r.numeroHabitacion = hab.numero
    INNER JOIN td_tipoHabitacion th ON hab.tipoHabitacion = th.id
    INNER JOIN tp_hotel hotel ON f.infoHotel = hotel.id
    INNER JOIN tp_usuarios admin ON hotel.numDocumento = admin.numDocumento;

-- 7. VISTA_HOTEL
-- Muestra información completa de hoteles
CREATE OR REPLACE VIEW Vista_hotel AS
SELECT 
    h.id AS hotelId,
    h.nit,
    h.nombre,
    h.direccion,
    h.telefono,
    h.correo,
    -- Información del administrador
    u.nombres AS adminNombres,
    u.apellidos AS adminApellidos,
    CONCAT(u.nombres, ' ', u.apellidos) AS adminCompleto,
    u.numDocumento AS adminDocumento,
    u.numTelefono AS adminTelefono,
    u.correo AS adminCorreo,
    -- Estadísticas de habitaciones
    COUNT(DISTINCT hab.numero) AS totalHabitaciones,
    COUNT(CASE WHEN hab.estado = 'Disponible' THEN 1 END) AS habitacionesDisponibles,
    COUNT(CASE WHEN hab.estado = 'Ocupado' THEN 1 END) AS habitacionesOcupadas,
    COUNT(CASE WHEN hab.estado = 'Mantenimiento' THEN 1 END) AS habitacionesMantenimiento,
    -- Estadísticas de reservas
    COUNT(DISTINCT r.id) AS totalReservas,
    COUNT(CASE WHEN r.estado = 'Activa' THEN 1 END) AS reservasActivas,
    COUNT(CASE WHEN r.estado = 'Pendiente' THEN 1 END) AS reservasPendientes,
    -- Estadísticas financieras
    SUM(CASE WHEN r.estado != 'Cancelada' THEN r.pagoFinal ELSE 0 END) AS ingresosTotales,
    COUNT(DISTINCT f.id) AS totalFacturas,
    SUM(f.total) AS montoFacturado,
    -- Promedio de ocupación
    ROUND((COUNT(CASE WHEN hab.estado = 'Ocupado' THEN 1 END) * 100.0 / 
           NULLIF(COUNT(DISTINCT hab.numero), 0)), 2) AS porcentajeOcupacion,
    -- Información de capacidad
    SUM(hab.capacidad) AS capacidadTotal,
    AVG(hab.costo) AS costoPromedio
FROM tp_hotel h
    INNER JOIN tp_usuarios u ON h.numDocumento = u.numDocumento
    LEFT JOIN tp_habitaciones hab ON 1=1  -- Todas las habitaciones (asumiendo un solo hotel)
    LEFT JOIN tp_reservas r ON hab.numero = r.numeroHabitacion
    LEFT JOIN tp_factura f ON h.id = f.infoHotel
GROUP BY h.id;

-- 8. VISTA_PQRS
-- Muestra información completa de PQRS
CREATE OR REPLACE VIEW Vista_pqrs AS
SELECT 
    p.id AS pqrsId,
    p.fechaRegistro,
    p.fechaLimite,
    p.tipo,
    p.descripcion,
    p.prioridad,
    p.categoria,
    p.estado,
    p.fechaFinalizacion,
    p.respuesta,
    -- Días transcurridos
    DATEDIFF(CURDATE(), DATE(p.fechaRegistro)) AS diasTranscurridos,
    -- Días para vencer
    DATEDIFF(p.fechaLimite, CURDATE()) AS diasParaVencer,
    -- Estado de tiempo
    CASE 
        WHEN p.estado = 'Finalizado' THEN 'FINALIZADO'
        WHEN CURDATE() > p.fechaLimite THEN 'VENCIDO'
        WHEN DATEDIFF(p.fechaLimite, CURDATE()) <= 1 THEN 'POR_VENCER'
        ELSE 'EN_TIEMPO'
    END AS estadoTiempo,
    -- Información del usuario
    u.nombres AS usuarioNombres,
    u.apellidos AS usuarioApellidos,
    CONCAT(u.nombres, ' ', u.apellidos) AS usuarioCompleto,
    u.numDocumento AS usuarioDocumento,
    u.correo AS usuarioCorreo,
    u.numTelefono AS usuarioTelefono,
    u.roles AS usuarioRol,
    -- Tiempo de respuesta (si está finalizado)
    CASE 
        WHEN p.estado = 'Finalizado' AND p.fechaFinalizacion IS NOT NULL
        THEN DATEDIFF(p.fechaFinalizacion, p.fechaRegistro)
        ELSE NULL
    END AS tiempoRespuestaDias,
    -- Clasificación por urgencia
    CASE 
        WHEN p.estado = 'Pendiente' AND p.prioridad = 'Alto' 
             AND CURDATE() > p.fechaLimite THEN 'CRÍTICO'
        WHEN p.estado = 'Pendiente' AND DATEDIFF(p.fechaLimite, CURDATE()) <= 1 
             THEN 'URGENTE'
        WHEN p.estado = 'Pendiente' AND p.prioridad = 'Alto' THEN 'IMPORTANTE'
        ELSE 'NORMAL'
    END AS nivelUrgencia
FROM tp_pqrs p
    INNER JOIN tp_usuarios u ON p.numdocumento = u.numDocumento;

-- ============================================
-- ÍNDICES RECOMENDADOS PARA OPTIMIZAR LAS VISTAS
-- ============================================

-- Índices para mejorar el rendimiento de las vistas
CREATE INDEX idx_reservas_usuario ON tp_reservas(us_numDocumento);
CREATE INDEX idx_reservas_huesped ON tp_reservas(hue_numDocumento);
CREATE INDEX idx_reservas_habitacion ON tp_reservas(numeroHabitacion);
CREATE INDEX idx_reservas_fechas ON tp_reservas(fechainicio, fechaFin);
CREATE INDEX idx_mantenimiento_habitacion ON tp_mantenimiento(numeroHabitacion);
CREATE INDEX idx_mantenimiento_usuario ON tp_mantenimiento(numDocumento);
CREATE INDEX idx_pqrs_usuario ON tp_pqrs(numdocumento);
CREATE INDEX idx_habitaciones_tipo ON tp_habitaciones(tipoHabitacion);
CREATE INDEX idx_factura_reserva ON tp_factura(infoReserva);
CREATE INDEX idx_factura_hotel ON tp_factura(infoHotel);


