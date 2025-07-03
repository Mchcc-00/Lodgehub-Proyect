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




CREATE TABLE IF NOT EXISTS tp_huespedes (numDocumento BIGINT(11) NOT NULL,
                                        numTelefono BIGINT(11) NOT NULL,
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




CREATE TABLE IF NOT EXISTS tp_habitaciones (numero INT (3) NOT NULL,
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






CREATE TABLE IF NOT EXISTS tp_empleados(numDocumento BIGINT (11) NOT NULL,
                                nombres VARCHAR (40) NOT NULL,
                                apellidos VARCHAR (40) NOT NULL,
                                direccion VARCHAR (30) NOT NULL,
                                fechaNacimiento DATE NOT NULL,
                                numTelefono BIGINT (11) NOT NULL,
                                telEmergencia BIGINT (11) NOT NULL,
                                password varchar (255) NOT NULL,
                                correo VARCHAR (30) NOT NULL,
                                rnt int (10) ,
                                nit int (10) ,
                                foto varchar (255) ,
                                sexo INT (3) NOT NULL,
                                tipoDocumento INT (3) NOT NULL,
                                roles INT (3) NOT NULL,
                                estadoCivil INT (3) NOT NULL,

                                PRIMARY KEY (numdocumento),
                                FOREIGN KEY (sexo) REFERENCES td_sexo (id),
                                FOREIGN KEY (tipoDocumento) REFERENCES td_tipodocumento (id),
                                FOREIGN KEY (roles) REFERENCES td_roles (id),
                                FOREIGN KEY (estadoCivil) REFERENCES td_estadocivil (id)
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
                                    hue_numdocumento BIGINT (11) NOT NULL,
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



CREATE TABLE IF NOT EXISTS td_motivoReserva (id INT (3) AUTO_INCREMENT NOT NULL,
                                            descripcion VARCHAR (20) NOT NULL,

                                            PRIMARY KEY (id)
                                            ) ENGINE=INNODB;


CREATE TABLE IF NOT EXISTS td_metodoPago (id INT (3) AUTO_INCREMENT NOT NULL,
                                         descripcion VARCHAR (30) NOT NULL,


                                         PRIMARY KEY (id)
                                         )ENGINE=INNODB;








CREATE TABLE IF NOT EXISTS tp_reservas (id INT (3) AUTO_INCREMENT NOT NULL,
                                        costo FLOAT (10.5) NOT NULL,
                                        fechainicio DATE NOT NULL,
                                        fechaFin DATE NOT NULL,
                                        cantidadAdultos INT (3) NOT NULL,
                                        cantidadNinos INT (3) NOT NULL,
                                        cantidadDiscapacitados INT (3) NOT NULL,
                                        motivoReserva INT (3) NOT NULL,
                                        numeroHabitacion INT (3) NOT NULL,
                                        metodoPago int (3) NOT NULL,
                                        informacionAdicional TEXT,
                                        emp_numdocumento BIGINT (11) NOT NULL,
                                        estado INT (3) NOT NULL,
                                        hue_numdocumento BIGINT (11) NOT NULL,
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
                                                      numero INT (3) NOT NULL,
                                                      emp_numDocumento BIGINT (11) NOT NULL,
                                                      estado INT (3) NOT NULL,
                                                      PRIMARY KEY (id),
                                                      FOREIGN KEY (prioridad) REFERENCES td_prioridad (id),
                                                      FOREIGN KEY (numero) REFERENCES tp_habitaciones (numero),
                                                      FOREIGN KEY (emp_numDocumento) REFERENCES tp_empleados (numDocumento),
                                                      FOREIGN KEY (estado) REFERENCES td_estado (id)
                                                      )ENGINE=INNODB;










CREATE TABLE IF NOT EXISTS ti_responder (id INT (3) AUTO_INCREMENT NOT NULL,
                                         descripcion VARCHAR (50) NOT NULL,
                                         fechaRespuesta DATE NOT NULL,
                                         pqr_id INT (3) NOT NULL,
                                         emp_numDocumento BIGINT (11) NOT NULL,
                                         
                                         PRIMARY KEY (id),
                                         FOREIGN KEY (pqr_id) REFERENCES tp_pqrs (id),
                                         FOREIGN KEY (emp_numDocumento) REFERENCES tp_empleados (numDocumento)
                                         )ENGINE=INNODB;


/*inserts*/


insert into td_motivoreserva values (null,'Negocios'),
(null,'Personal'),
(null,'Viaje'),
(null,'Familiar');


insert into td_estado values (null,'Activo'),
(null,'Inactivo'),
(null,'En uso'),
(null,'Finalizado'),
(null,'Pendiente'),
(null,'Cancelado');


insert into td_estadocivil values (null, 'Soltero/a'),
(null,'Casado/a'),
(null,'Viudo/a'),
(null, 'Unión libre');


insert into td_sexo values (null,'Hombre'),
(null,'Mujer'),
(null,'Otro');


insert into td_tipodocumento values (null,'Cedula de Ciudadanía'),
(null,'Tarjeta de Identidad'),
(null,'Cedula de Extranjeria'),
(null,'Pasaporte'),
(null,'Registro Civil');


INSERT INTO td_tipohabitacion
VALUES (NULL, 'INDIVIDUAL'),
       (NULL, 'DOBLE'),
       (NULL, 'TRIPLE'),
       (NULL, 'SUITE'),
       (NULL, 'CONFORT');


INSERT INTO td_tamano
VALUES (NULL, 'PEQUEÑO'),
(NULL, 'MEDIANO'),
(NULL, 'GRANDE'),
(NULL, 'EXTRAGRANDE');


INSERT INTO td_tipopqrs
VALUES (NULL, 'Peticiones'),
(NULL, 'Quejas'),
(NULL, 'Reclamos'),
(NULL, 'Sugerencias'),
(NULL, 'Felicitaciones');


insert into td_prioridad values (null,'Bajo'),
(null,'Medio'),
(null,'Alto');


insert into td_categoria values (null,'Servicio'),
(null,'Habitacion'),
(null,'Atencion'),
(null,'Otro');


INSERT INTO td_roles
VALUES (NULL, 'ADMINISTRADOR'),
(NULL, 'RECEPCIONISTA'),
(NULL, 'ATENCIÓN AL CLIENTE');


insert into td_metodoPago values (null,'Tarjeta'),
(null,'Efectivo'),
(null,'PSE');


INSERT INTO tp_huespedes
VALUES (1000289068, 3116182673, 'Bleachowl98@gmail.com', 'Favian Alejandro', 'Machuca Pedraza', 1, 1, 4),
(1234098756, 3124233442, 'Camilín@gmail.com', 'Camilo Andrés', 'Guerrero Yanquen', 1, 1, 1),
(1098785643, 3214566786, 'Jhonny@gmail.com', 'Jonathan David', 'Fernández López', 1, 1, 1),
(1002455665, 3144235027, 'Bray@gmail.com', 'Brayan Felipe', 'Pulido López', 1, 1, 3),
(1012099089, 302099086, 'Willy@gmail.com', 'William Steven', 'Daza Delgado', 1, 1, 2);


insert into tp_empleados values (1122123456,'Joaquin Diomedes','Gonzales Chaz','Calle 73 D#8C', "2003-02-15", 3118273847,3028732645, '1234567485','GonzChaz@gmail.com', NULL, NULL, NULL,1,1,1,1),
(1122123856,'Joaquin Diomedes','Gonzales Chaz','Calle 73 D#8C', "2003-02-15", 3118273847,3028732645, '1234567485','GonzChaz@gmail.com', NULL, NULL, NULL,1,1,1,1),
(1122143456,'Joaquin Diomedes','Gonzales Chaz','Calle 73 D#8C', "2003-02-15", 3118273847,3028732645, '1234567485','GonzChaz@gmail.com', NULL, NULL, NULL,1,1,1,1),
(1122123656,'Joaquin Diomedes','Gonzales Chaz','Calle 73 D#8C', "2003-02-15", 3118273847,3028732645, '1234567485','GonzChaz@gmail.com', NULL, NULL, NULL,1,1,1,1),
(1132123456,'Joaquin Diomedes','Gonzales Chaz','Calle 73 D#8C', "2003-02-15", 3118273847,3028732645, '1234567485','GonzChaz@gmail.com', NULL, NULL, NULL,1,1,1,1);




insert into tp_habitaciones values (666,280000,1,1,1,2),
(819, 500000,2,2,3,1),
(10,300000,1,1,3,1),
(73,900000,4,4,3,3),
(69,1200000,5,5,4,2);


INSERT INTO tp_pqrs
VALUES (NULL, 20241018, 'El huésped reporta que la habitación no contaba con servicio de agua',20241019,1000289068, 3, 2, 2, 1),
(NULL, 20240312, 'Se reporta que las cobijas de la cama se encuentra en mal estado(sucias y manchadas),no hicieron el debido aseo en la habitación 73', 20240313, 1098785643, 2, 2, 1, 3),
(null, 20241126,'Se reporta una fuga de agua en la habitación 666', null, 1002455665,3,2,3,2),
(null,20220425,'Se reporta un extraño olor en el pasillo',null, 1234098756,2,3,1,1),
(null,20230901,'El huésped sugiere colocar revistas en la sala de espera',20230515,1012099089,1,1,1,4),
(NULL, 20240916,'Se  reporta la falta de materiales de aseo personal (jabón) en la habitación 819', 20240916,1098785643,2,2,1,1);


insert into tp_historialmantenimiento values
(null,'Bombillos defectuosos, próximos a dañarse','Reemplazo de bombillos',20220505,20211001,'No aplica', 1, 819,1122123456,2),
(null,'Cortinas rasgadas','Reemplazo de cortinas',20230413,20230413,'No aplica',1,666,1122123856,2),
(null,'Gotera en la llave del lavamanos','Revisar y reparar la fuga de la llave de agua',20241126,20220617,'No aplica',1,69,1122143456,1),
(null,'Olor raro proveniente del baño','Realizar aseo',20241115,20241115,'No aplica',1,819,1122123656,3),
(null,'Piso sucio, manchas pegajosas','Realizar aseo en el piso',20240220,20220330,'No aplica',1,10,1132123456,2);


INSERT INTO tp_reservas
VALUES (NULL, 350000.00, 20240624, 20240626, 1, 0, 0, 1, 10, 1, NULL,1122123456,1, 1002455665, '20240624 10:00:00');

INSERT INTO ti_responder 
VALUES 
(NULL, '¡Gracias por tu comentario! Lamentamos que hayas tenido una mala experiencia en el hotel Chimbanadas. Tu comentario nos ayuda a mejorar día a día.', 20240916, 1, 1122123856),
(NULL, '¡Gracias por tu comentario! Lamentamos que hayas tenido una mala experiencia en el hotel Patroclín. Tu comentario nos ayuda a mejorar nuestros servicios día a día.', 20241018, 2, 1132123456),
(NULL, '¡Gracias por tu sugerencia! Tus comentarios nos ayudan a mejorar nuestros servicios. Atte: Hotel Bondiola', 20230515, 3, 1122123456);




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
r.descripcion as Rol, 
ec.descripcion as Estado_Civil
from tp_empleados e
inner join td_sexo s on  e.sexo = s.id 
inner join td_tipodocumento d on e.tipoDocumento = d.id
inner join td_roles r on e.roles = r.id
inner join td_estadocivil ec on  e.estadoCivil = ec.id;


CREATE VIEW vista_habitaciones AS
SELECT h.numero as Numero_Habitacion, h.costo as Costo, h.capacidad as Capacidad_Personas, t.descripcion as Tipo_Habitacion, ta.descripcion as Tamaño_Habitacion, e.descripcion as Estado_Habitacion
FROM tp_habitaciones h
INNER JOIN td_tipohabitacion t ON  h.tipoHabitacion = t.id
INNER JOIN td_tamano ta ON h.tamano = ta.id
INNER JOIN td_estado e ON h.estado = e.id;


create view vista_historialMantenimeinto as
select h.id as id, h.problemaDescripcion as Problema, h.accion as Accion, h.fechaRegistro as Fecha_Registro, h.ultimaActualizacion as Ultima_Actualización, p.descripcion as Prioridad, h.frecuencia as Frecuencia, h.numero as Numero_Habitacion, e.nombres as Reporta_Empleado, e.apellidos as Reporta_Apellidos, es.descripcion as Estado
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


