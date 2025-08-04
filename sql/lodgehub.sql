CREATE DATABASE IF NOT EXISTS Lodgehub;
USE Lodgehub;



CREATE TABLE IF NOT EXISTS td_tipoDocumento(id INT (3) AUTO_INCREMENT NOT NULL,
                                            descripcion VARCHAR (30) NOT NULL,

                                            PRIMARY KEY (id)
                                            );



CREATE TABLE IF NOT EXISTS td_sexo (id INT (3) AUTO_INCREMENT NOT NULL,
                                    descripcion VARCHAR (20) NOT NULL,

                                    PRIMARY KEY (id)
                                    );



CREATE TABLE IF NOT EXISTS td_estadoCivil (id INT (3) AUTO_INCREMENT NOT NULL,
                                            descripcion VARCHAR (20) NOT NULL,

                                            PRIMARY KEY (id)
                                            );



CREATE TABLE IF NOT EXISTS tp_huespedes (numDocumento VARCHAR(15) NOT NULL,
                                        numTelefono VARCHAR (15) NOT NULL,
                                        correo VARCHAR(30) NOT NULL,
                                        nombres VARCHAR(50) NOT NULL,
                                        apellidos VARCHAR(50) NOT NULL,
                                        tipoDocumento INT(3) NOT NULL,
                                        sexo INT(3) NOT NULL,
                                        estadoCivil INT (3) NOT NULL,


                                        PRIMARY KEY (numDocumento),
                                        FOREIGN KEY (tipoDocumento) REFERENCES td_tipodocumento (id),
                                        FOREIGN KEY (sexo) REFERENCES td_sexo (id),
                                        FOREIGN KEY (estadoCivil) REFERENCES td_estadocivil (id)
                                        )ENGINE=INNODB;



                                            
CREATE TABLE IF NOT EXISTS td_tipoHabitacion (id INT (3) AUTO_INCREMENT NOT NULL,
                                            descripcion VARCHAR (20) NOT NULL,

                                            PRIMARY KEY (id)
                                            )ENGINE=INNODB;



CREATE TABLE IF NOT EXISTS td_tamano (id INT (3) AUTO_INCREMENT NOT NULL,
                                    descripcion VARCHAR (20) NOT NULL,

                                    PRIMARY KEY (id)
                                    )ENGINE=INNODB;



CREATE TABLE IF NOT EXISTS td_estado (id INT (3) AUTO_INCREMENT NOT NULL,
                                          descripcion VARCHAR (20) NOT NULL,
                                          
                                          PRIMARY KEY (id)
                                          )ENGINE=INNODB;



CREATE TABLE IF NOT EXISTS tp_habitaciones (numero VARCHAR (5) NOT NULL,
                                            costo DECIMAL (10,2) NOT NULL,
                                            capacidad INT (3) NOT NULL,
                                            tipoHabitacion INT (3) NOT NULL,
                                            tamano INT (3) NOT NULL,
                                            estado INT (3) NOT NULL,


                                            PRIMARY KEY (numero),
                                            FOREIGN KEY (tipoHabitacion) REFERENCES td_tipohabitacion (id),
                                            FOREIGN KEY (tamano) REFERENCES td_tamano (id),
                                            FOREIGN KEY (estado) REFERENCES td_estado (id)
                                            )ENGINE=INNODB;



CREATE TABLE IF NOT EXISTS td_roles (id INT (3) AUTO_INCREMENT NOT NULL,
                                    descripcion VARCHAR (20) NOT NULL,

                                    PRIMARY KEY (id)
                                    )ENGINE=INNODB;



CREATE TABLE IF NOT EXISTS tp_empleados(numDocumento VARCHAR (15) NOT NULL,
                                nombres VARCHAR (40) NOT NULL,
                                apellidos VARCHAR (40) NOT NULL,
                                direccion VARCHAR (30) NOT NULL,
                                fechaNacimiento DATE NOT NULL,
                                numTelefono VARCHAR (15) NOT NULL,
                                telEmergencia VARCHAR (15) NOT NULL,
                                password varchar (255) NOT NULL,
                                correo VARCHAR (250) NOT NULL,
                                rnt int (10) ,
                                nit int (10) ,
                                foto varchar (255),
                                solicitarContraseña ENUM('0','1') ,
                                tokenPassword varchar (100) ,
                                sesionCaducada INT (1) ,
                                sexo INT (3) NOT NULL,
                                tipoDocumento INT (3) NOT NULL,
                                roles INT (3) NOT NULL,
                                

                                PRIMARY KEY (numdocumento),
                                FOREIGN KEY (sexo) REFERENCES td_sexo (id),
                                FOREIGN KEY (tipoDocumento) REFERENCES td_tipodocumento (id),
                                FOREIGN KEY (roles) REFERENCES td_roles (id)
                                )ENGINE=INNODB;



CREATE TABLE IF NOT EXISTS td_tipoPqrs (id INT (3) AUTO_INCREMENT NOT NULL,
                                      descripcion VARCHAR (30) NOT NULL,

                                      PRIMARY KEY (id)
                                      )ENGINE=INNODB;



CREATE TABLE IF NOT EXISTS td_categoria (id INT (3) AUTO_INCREMENT NOT NULL,
                                        descripcion VARCHAR (20) NOT NULL,

                                        PRIMARY KEY (id)
                                        )ENGINE=INNODB;



CREATE TABLE IF NOT EXISTS td_prioridad (id INT (3) AUTO_INCREMENT NOT NULL,
                                        descripcion VARCHAR (20) NOT NULL,

                                        PRIMARY KEY (id)
                                        ) ENGINE=INNODB;



CREATE TABLE IF NOT EXISTS tp_pqrs (id INT (10) AUTO_INCREMENT NOT NULL,
                                    fechaRegistro DATE NOT NULL,
                                    descripcion VARCHAR (200) NOT NULL,
                                    fechaCierre DATE,
                                    hue_numdocumento VARCHAR (15) NOT NULL,
                                    prioridad INT (3) NOT NULL,
                                    categoria INT(3) NOT  NULL,
                                    estado INT (3) NOT NULL,
                                    tipo INT(3) NOT NULL,


                                      PRIMARY KEY (id),
                                      FOREIGN KEY (hue_numdocumento) REFERENCES tp_huespedes (numDocumento),
                                      FOREIGN KEY (prioridad) REFERENCES td_prioridad (id),
                                      FOREIGN KEY (categoria) REFERENCES td_categoria (id),
                                      FOREIGN KEY (estado) REFERENCES td_estado (id),
                                      FOREIGN KEY (tipo) REFERENCES td_tipoPqrs (id)
                                      ) ENGINE=INNODB;

CREATE TABLE pqrs (               id INT PRIMARY KEY AUTO_INCREMENT,
                                  fecha DATETIME NOT NULL,
                                  tipo_pqrs VARCHAR(50) NOT NULL,
                                  urgencia VARCHAR(20) NOT NULL,
                                  categoria VARCHAR(50) NOT NULL,
                                  descripcion TEXT NOT NULL,
                                  nombre VARCHAR(100) NOT NULL,
                                  apellido VARCHAR(100) NOT NULL,
                                  empleado VARCHAR(100) NOT NULL,
                                  tipo_documento VARCHAR(10) NOT NULL,
                                  numero_documento VARCHAR(20) NOT NULL,
                                  estado VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Solucionado',
                                   PRIMARY KEY (id)
                                  ) ENGINE=INNODB;  
                              




CREATE TABLE IF NOT EXISTS td_motivoReserva (id INT (3) AUTO_INCREMENT NOT NULL,
                                            descripcion VARCHAR (20) NOT NULL,

                                            PRIMARY KEY (id)
                                            ) ENGINE=INNODB;



CREATE TABLE IF NOT EXISTS td_metodoPago (id INT (3) AUTO_INCREMENT NOT NULL,
                                        descripcion VARCHAR (30) NOT NULL,

                                        PRIMARY KEY (id)
                                        )ENGINE=INNODB;



CREATE TABLE IF NOT EXISTS tp_reservas (id INT (3) AUTO_INCREMENT NOT NULL,
                                        costo DECIMAL(10,2) NOT NULL,
                                        fechainicio DATE NOT NULL,
                                        fechaFin DATE NOT NULL,
                                        cantidadAdultos INT (2) NULL,
                                        cantidadNinos INT (2) NULL,
                                        cantidadDiscapacitados INT (2) NULL,
                                        motivoReserva INT (3) NOT NULL,
                                        numeroHabitacion VARCHAR (5) NOT NULL,
                                        metodoPago int (3) NOT NULL,
                                        informacionAdicional TEXT,
                                        emp_numdocumento VARCHAR (15) NOT NULL,
                                        estado INT (3) NOT NULL,
                                        hue_numdocumento VARCHAR (15) NOT NULL,
                                        fechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,


                                        PRIMARY KEY (id),
                                        FOREIGN KEY (motivoReserva) REFERENCES td_motivoreserva (id),
                                        FOREIGN KEY (numeroHabitacion) REFERENCES tp_habitaciones (numero),
                                        FOREIGN KEY (emp_numdocumento) REFERENCES tp_empleados (numDocumento),
                                        FOREIGN KEY (estado) REFERENCES td_estado(id),
                                        FOREIGN KEY (hue_numdocumento) REFERENCES tp_huespedes (numDocumento),
                                        FOREIGN KEY (metodoPago) REFERENCES td_metodopago (id)
                                        )ENGINE=INNODB;



CREATE TABLE IF NOT EXISTS tp_historialMantenimiento (id INT (4) AUTO_INCREMENT NOT NULL,
                                                      problemaDescripcion VARCHAR (50) NOT NULL,
                                                      accion VARCHAR (50) NOT NULL,
                                                      fechaRegistro DATE NOT NULL,
                                                      ultimaActualizacion DATE NOT NULL,
                                                      frecuencia VARCHAR (50) NOT NULL,
                                                      prioridad INT (3) NOT NULL,
                                                      numeroHabitacion VARCHAR (5) NOT NULL,
                                                      emp_numDocumento VARCHAR (15) NOT NULL,
                                                      estado INT (3) NOT NULL,


                                                      PRIMARY KEY (id),
                                                      FOREIGN KEY (prioridad) REFERENCES td_prioridad (id),
                                                      FOREIGN KEY (numeroHabitacion) REFERENCES tp_habitaciones (numero),
                                                      FOREIGN KEY (emp_numDocumento) REFERENCES tp_empleados (numDocumento),
                                                      FOREIGN KEY (estado) REFERENCES td_estado (id)
                                                      )ENGINE=INNODB;



CREATE TABLE IF NOT EXISTS ti_responder (id INT (3) AUTO_INCREMENT NOT NULL,
                                        descripcion VARCHAR (50) NOT NULL,
                                        fechaRespuesta DATE NOT NULL,
                                        pqr_id INT (3) NOT NULL,
                                        emp_numDocumento VARCHAR (15) NOT NULL,


                                        PRIMARY KEY (id),
                                        FOREIGN KEY (pqr_id) REFERENCES tp_pqrs (id),
                                        FOREIGN KEY (emp_numDocumento) REFERENCES tp_empleados (numDocumento)
                                        )ENGINE=INNODB;


/*inserts*/

insert into td_motivoreserva values
(null,'Negocios'),
(null,'Personal'),
(null,'Viaje'),
(null,'Familiar');


insert into td_estado values
(null,'Activo'),
(null,'Inactivo'),
(null,'En uso'),
(null,'Finalizado'),
(null,'Pendiente'),
(null,'Cancelado');


insert into td_estadocivil values
(null, 'Soltero/a'),
(null,'Casado/a'),
(null,'Viudo/a'),
(null, 'Unión libre');


insert into td_sexo values
(null,'Hombre'),
(null,'Mujer'),
(null,'Otro'),
(null,'Prefiero no decirlo');


insert into td_tipodocumento values
(null,'Cedula de Ciudadanía'),
(null,'Tarjeta de Identidad'),
(null,'Cedula de Extranjeria'),
(null,'Pasaporte'),
(null,'Registro Civil');


INSERT INTO td_tipohabitacion VALUES
(NULL, 'Individual'),
(NULL, 'Doble'),
(NULL, 'Triple'),
(NULL, 'Suite'),
(NULL, 'Confort');


INSERT INTO td_tamano VALUES
(NULL, 'Pequeño'),
(NULL, 'Medio'),
(NULL, 'Grande'),
(NULL, 'Extra Grande');


INSERT INTO td_tipopqrs VALUES
(NULL, 'Peticiones'),
(NULL, 'Quejas'),
(NULL, 'Reclamos'),
(NULL, 'Sugerencias'),
(NULL, 'Felicitaciones');


insert into td_prioridad values
(null,'Bajo'),
(null,'Medio'),
(null,'Alto');


insert into td_categoria values
(null,'Servicio'),
(null,'Habitacion'),
(null,'Atencion'),
(null,'Otro');


INSERT INTO td_roles VALUES
(NULL, 'Administrador'),
(NULL, 'Recepcionista'),
(NULL, 'Atención al Cliente');


insert into td_metodoPago values
(null,'Tarjeta'),
(null,'Efectivo'),
(null,'PSE');


INSERT INTO tp_huespedes VALUES
(1000289068, 3116182673, 'Bleachowl98@gmail.com', 'Favian Alejandro', 'Machuca Pedraza', 1, 1, 4),
(1019987917, 3014053025, 'camiloagycr321@gmail.com', 'Camilo Andrés', 'Guerrero Yanquen', 1, 1, 1),
(1098785643, 3214566786, 'Jhonny@gmail.com', 'Jonathan David', 'Fernández López', 1, 1, 1),
(1014596349, 3172509298, 'brayan06.pulido@gmail.com', 'Brayan Felipe', 'Pulido López', 1, 1, 3),
(1012099089, 302099086, 'Willy@gmail.com', 'William Steven', 'Daza Delgado', 1, 1, 2),
(1001234567, 3101234567, 'juan.perez@gmail.com', 'Juan', 'Pérez García', 1, 1, 2),
(1002345678, 3122345678, 'maria.gomez@gmail.com', 'María', 'Gómez Rodríguez', 1, 1, 1),
(1003456789, 3133456789, 'carlos.sanchez@gmail.com', 'Carlos', 'Sánchez Martínez', 1, 1, 3),
(1004567890, 3144567890, 'ana.lopez@gmail.com', 'Ana', 'López Fernández', 1, 1, 4),
(1005678901, 3155678901, 'pedro.ramirez@gmail.com', 'Pedro', 'Ramírez Díaz', 1, 1, 1),
(1006789012, 3166789012, 'laura.torres@gmail.com', 'Laura', 'Torres Vargas', 1, 1, 2),
(1007890123, 3177890123, 'david.ruiz@gmail.com', 'David', 'Ruiz Castro', 1, 1, 3),
(1008901234, 3188901234, 'sofia.morales@gmail.com', 'Sofía', 'Morales Herrera', 1, 1, 4),
(1009012345, 3199012345, 'daniel.jimenez@gmail.com', 'Daniel', 'Jiménez Navarro', 1, 1, 1),
(1010123456, 3200123456, 'valeria.gil@gmail.com', 'Valeria', 'Gil Ortega', 1, 1, 2),
(1011234567, 3211234567, 'andres.rojas@gmail.com', 'Andrés', 'Rojas Soto', 1, 1, 3),
(1012345678, 3222345678, 'camila.vega@gmail.com', 'Camila', 'Vega Pardo', 1, 1, 4),
(1013456789, 3233456789, 'ricardo.cruz@gmail.com', 'Ricardo', 'Cruz Mendoza', 1, 1, 1),
(1014567890, 3244567890, 'paula.guerrero@gmail.com', 'Paula', 'Guerrero Bravo', 1, 1, 2),
(1015678901, 3255678901, 'jorge.serrano@gmail.com', 'Jorge', 'Serrano Castillo', 1, 1, 3);


insert into tp_empleados values
(1000289068,'Favian Alejandro','Machuca Pedraza','Calle 73 D#8C', "2003-02-15", 3116182673,3028732645, '1234567485','Bleachowl98@gmail.com', NULL, NULL, NULL,'0','hola', '0', 1, 1, 1),
(1014596349, 'Brayan Felipe', 'Pulido López', 'calle 47 sur numero 1 f 20 este', '2006-03-03', 3172509298, 3126354874, '123456789', 'brayan06.pulido@gmail.com', '4987432145', '8765219854', 'asdasdasdasdqwrewf', '0', 'hola','1', 1, 1, 1),
(1019987917, 'Camilo Andrés', 'Guerrero Yanquen', 'calle 61 30', '2025-07-29', 3216848548, 3014053025, '987654321', 'camiloagycr321@gmail.com', NULL, NULL, NULL, '0', NULL, '1', 1, 1, 1),
(1001001001,'Ana María','López García','Carrera 10 #20-30', "1990-01-01", 3101112233,3001001001, '1112223334','ana.lopez@example.com', NULL, NULL, NULL,'0','Primer contacto', '0', 1, 1, 1),
(1001001002,'Luis Alberto','Ramírez Díaz','Avenida Siempre Viva 123', "1985-05-10", 3112223344,3012012012, '2223334445','luis.ramirez@example.com', '1234567890', NULL, NULL, '0', 'Cliente potencial','1', 1, 1, 1),
(1001001003,'Sofía Valentina','Martínez Castro','Calle 5 #45-67', "1992-11-20", 3123334455,3023023023, '3334445556','sofia.martinez@example.com', NULL, NULL, 'Nota importante','0','Información enviada', '0', 1, 1, 1),
(1001001004,'Diego Fernando','Sánchez Pardo','Transversal 80 #15-90', "1978-03-25", 3134445566,3034034034, '4445556667','diego.sanchez@example.com', '0987654321', '1122334455', NULL, '0', NULL, '1', 1, 1, 1),
(1001001005,'Laura Camila','Gómez Vargas','Diagonal 20 #30-40 Sur', "2000-07-12", 3145556677,3045045045, '5556667778','laura.gomez@example.com', NULL, NULL, NULL,'0','Seguimiento pendiente', '0', 1, 1, 1),
(1001001006,'Carlos Eduardo','Fernández Rojas','Carrera 50 #100-10', "1995-09-01", 3156667788,3056056056, '6667778889','carlos.fernandez@example.com', '6789012345', NULL, 'Comentario','0', 'Revisión agendada','1', 1, 1, 1),
(1001001007,'Isabella Nicole','Torres Herrera','Calle 10 #5-20', "1998-02-28", 3167778899,3067067067, '7778889990','isabella.torres@example.com', NULL, NULL, NULL,'0','Sin novedades', '0', 1, 1, 1),
(1001001008,'Javier Andrés','Ruiz Morales','Avenida El Dorado #80-100', "1982-12-05", 3178889900,3078078078, '8889990001','javier.ruiz@example.com', '2345678901', '9876543210', NULL, '0', 'Propuesta enviada', '1', 1, 1, 1),
(1001001009,'Valentina Sofía','Jiménez Ortega','Transversal 70 #1-50 Este', "2001-04-18", 3189990011,3089089089, '9990001112','valentina.jimenez@example.com', NULL, NULL, 'Recuerdo','0','Llamar en una semana', '0', 1, 1, 1),
(1001001010,'Felipe Andrés','Vega Pardo','Calle 90 #1-1 Este', "1993-06-30", 3190001122,3090909090, '0001112223','felipe.vega@example.com', '3456789012', NULL, NULL, '0', NULL, '1', 1, 1, 1),
(1001001011,'Mariana José','Cruz Mendoza','Carrera 40 #70-20', "1987-10-03", 3201112233,3101010101, '1112223334','mariana.cruz@example.com', NULL, NULL, 'Otro comentario','0','Reunión programada', '0', 1, 1, 1),
(1001001012,'Sebastián Camilo','Guerrero Bravo','Calle 20 #1-10 Oeste', "1999-01-08", 3212223344,3112112112, '2223334445','sebastian.guerrero@example.com', '4567890123', '0123456789', NULL, '0', 'En espera de respuesta', '1', 1, 1, 1),
(1001001013,'Andrea Carolina','Serrano Castillo','Avenida 68 #40-50', "1980-04-15", 3223334455,3123123123, '3334445556','andrea.serrano@example.com', NULL, NULL, NULL,'0','Nota adicional', '0', 1, 1, 1),
(1001001014,'Miguel Ángel','Díaz Gallardo','Diagonal 70 #10-20', "1994-08-07", 3234445566,3134134134, '4445556667','miguel.diaz@example.com', '5678901234', NULL, NULL, '0', 'Información actualizada','1', 1, 1, 1),
(1001001015,'Natalia Alejandra','Prieto Salas','Transversal 30 #5-15', "1989-11-22", 3245556677,3145145145, '5556667778','natalia.prieto@example.com', NULL, NULL, 'Ultima nota','0','Contacto realizado', '0', 1, 1, 1),
(1001001016,'Ricardo José','Ramos Núñez','Calle 80 #60-30', "1975-02-01", 3256667788,3156156156, '6667778889','ricardo.ramos@example.com', '6789012345', '7890123456', NULL, '0', 'Nuevo requerimiento', '1', 1, 1, 1),
(1001001020,'Gabriela Alejandra','Muñoz López','Diagonal 120 #30-40', "1996-12-01", 3290001122,3190190190, '0001112223','gabriela.munoz@example.com', '8901234567', '4321098765', NULL, '0', 'Listo para siguiente fase', '1', 1, 1, 1);

insert into tp_habitaciones values
(666,280000,1,1,1,2),
(819, 500000,2,2,3,1),
(10,300000,1,1,3,1),
(73,900000,4,4,3,3),
(18,1200000,5,5,4,2),
(101, 250000.00, 2, 2, 2, 1),
(102, 250000.00, 2, 2, 2, 1),
(103, 180000.00, 1, 1, 1, 1),
(104, 350000.00, 3, 3, 2, 1),
(105, 420000.00, 4, 4, 3, 1),
(201, 280000.00, 2, 2, 2, 1),
(202, 190000.00, 1, 1, 1, 1),
(203, 400000.00, 3, 3, 3, 1),
(204, 550000.00, 5, 5, 4, 1),
(205, 260000.00, 2, 2, 2, 1),
(301, 300000.00, 2, 2, 2, 1),
(302, 200000.00, 1, 1, 1, 1),
(303, 450000.00, 4, 4, 3, 1),
(304, 600000.00, 5, 5, 4, 1),
(305, 320000.00, 3, 3, 2, 1),
(401, 380000.00, 3, 3, 3, 1),
(402, 220000.00, 1, 1, 1, 1),
(403, 500000.00, 4, 4, 3, 1),
(404, 700000.00, 5, 5, 4, 1),
(405, 290000.00, 2, 2, 2, 1);


INSERT INTO tp_pqrs VALUES
(NULL, 20241018, 'El huésped reporta que la habitación no contaba con servicio de agua',20241019,1011234567, 3, 2, 2, 1),
(NULL, 20240312, 'Se reporta que las cobijas de la cama se encuentra en mal estado(sucias y manchadas),no hicieron el debido aseo en la habitación 73', 20240313, 1098785643, 2, 2, 1, 3),
(null, 20241126,'Se reporta una fuga de agua en la habitación 666', null, 1000289068,3,2,3,2),
(null,20220425,'Se reporta un extraño olor en el pasillo',null, 1000289068,2,3,1,1),
(null,20230901,'El huésped sugiere colocar revistas en la sala de espera',20230515,1000289068,1,1,1,4),
(NULL, 20240916,'Se  reporta la falta de materiales de aseo personal (jabón) en la habitación 819', 20240916,1000289068,2,2,1,1);


insert into tp_historialmantenimiento values
(null,'Bombillos defectuosos, próximos a dañarse','Reemplazo de bombillos',20220505,20211001,'No aplica', 1, 819,1014596349,2),
(null,'Cortinas rasgadas','Reemplazo de cortinas',20230413,20230413,'No aplica',1,666,1019987917,2),
(null,'Gotera en la llave del lavamanos','Revisar y reparar la fuga de la llave de agua',20241126,20220617,'No aplica',1,18,1000289068,1);


INSERT INTO tp_reservas VALUES
(NULL, 350000.00, 20240624, 20240626, 1, 0, 0, 1, 10, 1, NULL,1014596349,1, 1000289068, '20240624 10:00:00'),
(NULL, 280000.00, '2025-08-10', '2025-08-12', 2, 0, 0, 2, '666', 1, NULL, '1001001001', 1, '1000289068', '2025-08-01 10:00:00'),
(NULL, 500000.00, '2025-08-15', '2025-08-19', 3, 1, 0, 1, '819', 2, 'Incluye cena especial.', '1019987917', 2, '1019987917', '2025-08-02 11:30:00'),
(NULL, 300000.00, '2025-08-20', '2025-08-23', 1, 0, 1, 3, '10', 3, NULL, '1014596349', 1, '1098785643', '2025-08-03 09:00:00'),
(NULL, 900000.00, '2025-08-25', '2025-08-28', 4, 2, 0, 4, '73', 1, 'Reserva familiar, se requiere camas adicionales.', '1001001004', 2, '1014596349', '2025-08-04 14:00:00'),
(NULL, 1200000.00, '2025-09-01', '2025-09-05', 5, 3, 0, 1, '18', 2, NULL, '1001001006', 1, '1012099089', '2025-08-05 16:00:00'),
(NULL, 280000.00, '2025-09-08', '2025-09-10', 2, 0, 0, 2, '666', 3, NULL, '1019987917', 2, '1001234567', '2025-08-06 09:30:00'),
(NULL, 500000.00, '2025-09-12', '2025-09-16', 3, 0, 0, 1, '819', 1, 'Solicita vista a la ciudad.', '1001001001', 1, '1002345678', '2025-08-07 10:45:00'),
(NULL, 300000.00, '2025-09-18', '2025-09-20', 1, 1, 0, 3, '10', 2, NULL, '1014596349', 2, '1003456789', '2025-08-08 13:00:00'),
(NULL, 900000.00, '2025-09-22', '2025-09-26', 4, 0, 1, 4, '73', 3, 'Necesita acceso para silla de ruedas.', '1001001004', 1, '1004567890', '2025-08-09 15:15:00'),
(NULL, 1200000.00, '2025-09-28', '2025-10-02', 5, 0, 0, 1, '18', 1, NULL, '1001001006', 2, '1005678901', '2025-08-10 17:00:00'),
(NULL, 280000.00, '2025-10-05', '2025-10-07', 2, 1, 0, 2, '666', 2, NULL, '1019987917', 1, '1006789012', '2025-08-11 09:00:00'),
(NULL, 500000.00, '2025-10-10', '2025-10-14', 3, 0, 0, 3, '819', 3, 'Reserva para delegación.', '1001001001', 2, '1007890123', '2025-08-12 11:00:00'),
(NULL, 300000.00, '2025-10-16', '2025-10-18', 1, 0, 0, 4, '10', 1, NULL, '1014596349', 1, '1008901234', '2025-08-13 14:00:00'),
(NULL, 900000.00, '2025-10-20', '2025-10-24', 4, 1, 0, 1, '73', 2, 'Necesita Early Check-in.', '1001001004', 2, '1009012345', '2025-08-14 16:30:00'),
(NULL, 1200000.00, '2025-10-26', '2025-10-30', 5, 0, 0, 2, '18', 3, NULL, '1001001006', 1, '1010123456', '2025-08-15 08:00:00'),
(NULL, 280000.00, '2025-11-01', '2025-11-03', 2, 0, 0, 3, '666', 1, NULL, '1019987917', 2, '1011234567', '2025-08-16 10:10:00'),
(NULL, 500000.00, '2025-11-05', '2025-11-09', 3, 2, 0, 4, '819', 2, 'Reserva de último minuto.', '1001001001', 1, '1012345678', '2025-08-17 12:40:00'),
(NULL, 300000.00, '2025-11-11', '2025-11-13', 1, 0, 0, 1, '10', 3, NULL, '1014596349', 2, '1013456789', '2025-08-18 15:00:00'),
(NULL, 900000.00, '2025-11-15', '2025-11-19', 4, 0, 0, 2, '73', 1, 'Pago anticipado.', '1001001004', 1, '1014567890', '2025-08-19 09:20:00'),
(NULL, 1200000.00, '2025-11-20', '2025-11-24', 5, 0, 1, 3, '18', 2, NULL, '1001001006', 2, '1015678901', '2025-08-20 11:55:00');


INSERT INTO ti_responder VALUES
(NULL, '¡Gracias por tu comentario! Lamentamos que hayas tenido una mala experiencia en el hotel Chimbanadas. Tu comentario nos ayuda a mejorar día a día.', 20240916, 1, 1014596349),
(NULL, '¡Gracias por tu comentario! Lamentamos que hayas tenido una mala experiencia en el hotel Patroclín. Tu comentario nos ayuda a mejorar nuestros servicios día a día.', 20241018, 2, 1019987917),
(NULL, '¡Gracias por tu sugerencia! Tus comentarios nos ayudan a mejorar nuestros servicios. Atte: Hotel Bondiola', 20230515, 3, 1000289068);



/*vistas*/

create view vista_empleados as
select d.descripcion as Tipo_Documento, 
e.numDocumento as Documento, 
e.nombres as Nombres, 
e.apellidos as Apellidos, 
e.direccion as Direccion, 
e.numTelefono as Telefono, 
e.telEmergencia as Telefono_Emergencia, 
e.correo as Correo, 
s.descripcion as Sexo,  
r.descripcion as Rol
from tp_empleados e
inner join td_sexo s on  e.sexo = s.id 
inner join td_tipodocumento d on e.tipoDocumento = d.id
inner join td_roles r on e.roles = r.id;


CREATE VIEW vista_habitaciones AS
SELECT h.numero as Numero_Habitacion, h.costo as Costo, h.capacidad as Capacidad_Personas, t.descripcion as Tipo_Habitacion, ta.descripcion as Tamaño_Habitacion, e.descripcion as Estado_Habitacion
FROM tp_habitaciones h
INNER JOIN td_tipohabitacion t ON  h.tipoHabitacion = t.id
INNER JOIN td_tamano ta ON h.tamano = ta.id
INNER JOIN td_estado e ON h.estado = e.id;


create view vista_historialMantenimeinto as
select h.id as id, h.problemaDescripcion as Problema, h.accion as Accion, h.fechaRegistro as Fecha_Registro, h.ultimaActualizacion as Ultima_Actualización, p.descripcion as Prioridad, h.frecuencia as Frecuencia, h.numeroHabitacion as Numero_Habitacion, e.nombres as Reporta_Empleado, e.apellidos as Reporta_Apellidos, es.descripcion as Estado
from tp_historialmantenimiento h
inner JOIN td_prioridad p on h.prioridad = p.id
inner join tp_empleados e on h.emp_numDocumento = e.numDocumento
inner join td_estado es on h.estado = es.id;


CREATE VIEW vista_huespedes AS
SELECT h.numDocumento as Documento, h.numTelefono as Telefono, h.correo as Correo, h.nombres as Nombres, h.apellidos as Apellidos, d.descripcion as Tipo_Documento, s.descripcion as Sexo, c.descripcion as Estado_Civil
FROM tp_huespedes h
INNER JOIN td_tipodocumento d ON h.tipoDocumento = d.id
INNER JOIN td_sexo s ON h.sexo = s.id
INNER JOIN td_estadocivil c ON h.estadoCivil = c.id;


create view vista_pqrs AS
select p.id as id, p.fechaRegistro as Fecha_Registro, p.descripcion as Descripcion, p.fechaCierre as Fecha_Cierre, h.nombres as Reporta_Huesped, h.apellidos as Reporta_Apellidos, pr.descripcion as Prioridad, c.descripcion as Categoria, e.descripcion as Estado, t.descripcion as Tipo
from tp_pqrs p
inner join tp_huespedes h on p.hue_numdocumento = h.numDocumento
inner join td_prioridad pr on p.prioridad = pr.id
inner join td_categoria c on p.categoria = c.id
inner join td_estado e on p.estado = e.id
inner join td_tipopqrs t on p.tipo = t.id;


CREATE VIEW vista_respuestas AS
SELECT r.id AS id,
r.descripcion AS Respuesta,
r.fechaRespuesta AS Fecha_respuesta,
p.descripcion AS Descripcion_pqrs,
e.nombres AS Nombres_empleado,
e.apellidos AS Apellidos_empleado
FROM ti_responder r
INNER JOIN tp_pqrs p ON r.pqr_id = p.id
INNER JOIN tp_empleados e ON r.emp_numDocumento = e.numDocumento;


CREATE VIEW vista_reservas AS
SELECT r.id AS id,
r.costo AS Costo,
r.fechainicio AS Inicio_reserva,
r.fechaFin AS Fin_reserva,
r.cantidadAdultos AS Cantidad_Adultos,
r.cantidadNinos AS Cantidad_Niños,
r.cantidadDiscapacitados AS Cantidad_Discapacitados,
m.descripcion AS Motivo_reserva, 
hab.numero AS Número_habitación,
t.descripcion AS Tipo_habitación,
r.informacionAdicional AS Información_Adicional,
es.descripcion AS Estado_reserva,
h.nombres AS Nombres_huesped,
h.apellidos AS Apellidos_huesped,
e.nombres AS Nombres_empleado, 
e.apellidos AS Apellidos_empleado
FROM tp_reservas r
INNER JOIN td_motivoreserva m ON r.motivoReserva = m.id
INNER JOIN tp_empleados e ON r.emp_numdocumento = e.numDocumento
INNER JOIN td_estado es ON r.estado = es.id
INNER JOIN tp_huespedes h ON r.hue_numdocumento = h.numDocumento
INNER JOIN tp_habitaciones hab ON hab.numero = r.numeroHabitacion
INNER JOIN td_tipohabitacion t ON hab.tipoHabitacion = t.id;


