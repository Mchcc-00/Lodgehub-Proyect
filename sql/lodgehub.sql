CREATE DATABASE IF NOT EXISTS Lodgehub;
USE Lodgehub;


CREATE TABLE IF NOT EXISTS td_tipoDocumentohuespedes (id INT (3) AUTO_INCREMENT NOT NULL,
                                              descripcion VARCHAR (30) NOT NULL,


                                              PRIMARY KEY (id)
                                              );


CREATE TABLE IF NOT EXISTS td_sexohuespedes (id INT (3) AUTO_INCREMENT NOT NULL,
                                     descripcion VARCHAR (20) NOT NULL,


                                     PRIMARY KEY (id)
                                     );


CREATE TABLE IF NOT EXISTS td_estadoCivilHuespedes (id INT (3) AUTO_INCREMENT NOT NULL,
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
                                        FOREIGN KEY (tipoDocumento) REFERENCES td_tipodocumentoHuespedes (id),
                                        FOREIGN KEY (sexo) REFERENCES td_sexohuespedes (id),
                                        FOREIGN KEY (estadoCivil) REFERENCES td_estadocivilhuespedes (id)
                                        )ENGINE=INNODB;




                                             
CREATE TABLE IF NOT EXISTS td_tipoHabitacion (id INT (3) AUTO_INCREMENT NOT NULL,
                                            descripcion VARCHAR (20) NOT NULL,


                                            PRIMARY KEY (id)
                                            )ENGINE=INNODB;


CREATE TABLE IF NOT EXISTS td_tamaño (id INT (3) AUTO_INCREMENT NOT NULL,
                                    descripcion VARCHAR (20) NOT NULL,


                                    PRIMARY KEY (id)
                                    )ENGINE=INNODB;


CREATE TABLE IF NOT EXISTS td_estadohabitacion (id INT (3) AUTO_INCREMENT NOT NULL,
                                                descripcion VARCHAR (20) NOT NULL,
                                               
                                                PRIMARY KEY (id)
                                                )ENGINE=INNODB;


CREATE TABLE IF NOT EXISTS tp_habitaciones (numero INT (3) NOT NULL,
                                            costo DECIMAL (10,2) NOT NULL,
                                            capacidad INT (3) NOT NULL,
                                            tipoHabitacion INT (3) NOT NULL,
                                            tamaño INT (3) NOT NULL,
                                            estado INT (3) NOT NULL,
                                           
                                            PRIMARY KEY (numero),
                                            FOREIGN KEY (tipoHabitacion) REFERENCES td_tipohabitacion (id),
                                            FOREIGN KEY (tamaño) REFERENCES td_tamaño (id),
                                            FOREIGN KEY (estado) REFERENCES td_estadohabitacion (id)
                                            )ENGINE=INNODB;


CREATE TABLE IF NOT EXISTS td_roles (id INT (3) AUTO_INCREMENT NOT NULL,
                                    descripcion VARCHAR (20) NOT NULL,


                                    PRIMARY KEY (id)
                                    )ENGINE=INNODB;


CREATE TABLE IF NOT EXISTS td_estadoCivilEmpleados (id INT (3) AUTO_INCREMENT NOT NULL,
                                                    descripcion VARCHAR (20) NOT NULL,


                                                    PRIMARY KEY (id)
                                            )ENGINE=INNODB;


CREATE TABLE IF NOT EXISTS td_tipoDocumentoEmpleados (id INT (3) AUTO_INCREMENT NOT NULL,
                                                    descripcion VARCHAR (30) NOT NULL,


                                                    PRIMARY KEY (id)
                                                    );


CREATE TABLE IF NOT EXISTS td_sexoEmpleados (id INT (3) AUTO_INCREMENT NOT NULL,
                                            descripcion VARCHAR (20) NOT NULL,


                                            PRIMARY KEY (id)
                                            )ENGINE=INNODB;


CREATE TABLE IF NOT EXISTS tp_empleados(numDocumento BIGINT (11) NOT NULL,
                                nombres VARCHAR (40) NOT NULL,
                                apellidos VARCHAR (40) NOT NULL,
                                direccion VARCHAR (30) NOT NULL,
                                fechaNacimiento DATE NOT NULL,
                                numTelefono BIGINT (11) NOT NULL,
                                contactoPersonal BIGINT (11) NOT NULL,
                                correo VARCHAR (30) NOT NULL,
                                sexo INT (3) NOT NULL,
                                tipoDocumento INT (3) NOT NULL,
                                roles INT (3) NOT NULL,
                                estadoCivil INT (3) NOT NULL,
                                         
                                         PRIMARY KEY (numdocumento),
                                         FOREIGN KEY (sexo) REFERENCES td_sexoempleados (id),
                                         FOREIGN KEY (tipoDocumento) REFERENCES td_tipodocumentoempleados (id),
                                         FOREIGN KEY (roles) REFERENCES td_roles (id),
                                         FOREIGN KEY (estadoCivil) REFERENCES td_estadocivilEmpleados (id)
                                         )ENGINE=INNODB;


                                     
CREATE TABLE IF NOT EXISTS td_tipoPqrs (id INT (3) AUTO_INCREMENT NOT NULL,
                                        descripcion VARCHAR (30) NOT NULL,
                                       
                                        PRIMARY KEY (id)
                                        )ENGINE=INNODB;


CREATE TABLE IF NOT EXISTS td_categoria (id INT (3) AUTO_INCREMENT NOT NULL,
                                         descripcion VARCHAR (20) NOT NULL,
                                         
                                         PRIMARY KEY (id)
                                         )ENGINE=INNODB;


CREATE TABLE IF NOT EXISTS td_estadoPQRs (id INT (3) AUTO_INCREMENT NOT NULL,
                                          descripcion VARCHAR (20) NOT NULL,
                                           
                                          PRIMARY KEY (id)
                                          )ENGINE=INNODB;


CREATE TABLE IF NOT EXISTS td_urgencia (id INT (3) AUTO_INCREMENT NOT NULL,
                                        descripcion VARCHAR (20) NOT NULL,


                                        PRIMARY KEY (id)
                                        ) ENGINE=INNODB;








CREATE TABLE IF NOT EXISTS tp_pqrs (id INT (10) AUTO_INCREMENT NOT NULL,
                                    fechaRegistro DATE NOT NULL,
                                    descripcion VARCHAR (200) NOT NULL,
                                    fechaCierre DATE NOT NULL,
                                    numdocumento BIGINT (11) NOT NULL,
                                    urgencia INT (3) NOT NULL,
                                    categoria INT(3) NOT  NULL,
                                    estado INT (3) NOT NULL,
                                    tipo INT(3) NOT NULL,
                         


                                      PRIMARY KEY (id),
                                      FOREIGN KEY (numdocumento) REFERENCES tp_huespedes (numDocumento),
                                      FOREIGN KEY (urgencia) REFERENCES td_urgencia (id),
                                      FOREIGN KEY (categoria) REFERENCES td_categoria (id),
                                      FOREIGN KEY (estado) REFERENCES td_estadoPqrs (id),
                                      FOREIGN KEY (tipo) REFERENCES td_tipoPqrs (id)
                                      ) ENGINE=INNODB;






CREATE TABLE IF NOT EXISTS td_estadoReserva (id INT (3) AUTO_INCREMENT NOT NULL,
                                            descripcion VARCHAR (20) NOT NULL,
                                       
                                            PRIMARY KEY (id)
                                        ) ENGINE=INNODB;
                                     
CREATE TABLE IF NOT EXISTS td_motivoReserva (id INT (3) AUTO_INCREMENT NOT NULL,
                                             descripcion VARCHAR (20) NOT NULL,
                                             
                                              PRIMARY KEY (id)
                                              ) ENGINE=INNODB;












CREATE TABLE IF NOT EXISTS tp_reservas (id INT (3) AUTO_INCREMENT NOT NULL,
                                        costo FLOAT (10.5) NOT NULL,
                                        fechainicio DATE NOT NULL,
                                        fechaFin DATE NOT NULL,
                                        canPersonas INT (3) NOT NULL,
                                        motivoReserva INT (3) NOT NULL,
                                        numero INT (3) NOT NULL,
                                        emp_numdocumento BIGINT (11) NOT NULL,
                                        estado INT (3) NOT NULL,
                                        hue_numdocumento BIGINT (11) NOT NULL,
                                       
                                        PRIMARY KEY (id),
                                        FOREIGN KEY (motivoReserva) REFERENCES td_motivoreserva (id),
                                        FOREIGN KEY (numero) REFERENCES tp_habitaciones (numero),
                                        FOREIGN KEY (emp_numdocumento) REFERENCES tp_empleados (numDocumento),
                                        FOREIGN KEY (estado) REFERENCES td_estadoreserva (id),
                                        FOREIGN KEY (hue_numdocumento) REFERENCES tp_huespedes (numDocumento)
                                        )ENGINE=INNODB;


CREATE TABLE IF NOT EXISTS td_estadoMantenimiento (id INT (3) AUTO_INCREMENT NOT NULL,
                                                   descripcion VARCHAR (20) NOT NULL,
                                                  PRIMARY KEY (id)
                                                  )ENGINE=INNODB;


CREATE TABLE IF NOT EXISTS tp_historialMantenimiento (id INT (4) AUTO_INCREMENT NOT NULL,
                                                      problemaDescripcion VARCHAR (50) NOT NULL,
                                                      accion VARCHAR (50) NOT NULL,
                                                      fechaRegistro DATE NOT NULL,
                                                      ultimaActualización DATE NOT NULL,
                                                      frecuencia VARCHAR (50) NOT NULL,
                                                      numero INT (3) NOT NULL,
                                                      emp_numDocumento BIGINT (11) NOT NULL,
                                                      estadoMantenimiento INT (3) NOT NULL,
                                                      PRIMARY KEY (id),
                                                      FOREIGN KEY (numero) REFERENCES tp_habitaciones (numero),
                                                      FOREIGN KEY (emp_numDocumento) REFERENCES tp_empleados (numDocumento),
                                                      FOREIGN KEY (estadoMantenimiento) REFERENCES td_estadoMantenimiento (id)
                                                      )ENGINE=INNODB;


CREATE TABLE IF NOT EXISTS ti_atender (id INT (3) AUTO_INCREMENT NOT NULL,
                                       res_id INT (3) NOT NULL,
                                       hue_numDocumento BIGINT (11) NOT NULL,
                                       emp_numDocumento BIGINT (11) NOT NULL,
                                       
                                       PRIMARY KEY (id),
                                       FOREIGN KEY (res_id) REFERENCES tp_reservas (id),
                                       FOREIGN KEY (hue_numDocumento) REFERENCES tp_huespedes (numDocumento),
                                       FOREIGN KEY (emp_numDocumento) REFERENCES tp_empleados (numDocumento)
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


CREATE TABLE IF NOT EXISTS tp_login (id INT (4) AUTO_INCREMENT NOT NULL,
                                     emp_numDocumento BIGINT (11) NOT NULL,  
                                     contraseña VARCHAR (20) NOT NULL,
                                     
                                     PRIMARY KEY (id),
                                     FOREIGN KEY (emp_numDocumento) REFERENCES tp_empleados (numDocumento)
                                     )ENGINE=INNODB;

/*inserts*/

insert into td_motivoreserva values (null,'Negocios'),
(null,'Personal'),
(null,'Viaje'),
(null,'Familiar');

insert into td_estadoreserva values (null,'Activo'),
(null,'Pendiente'),
(null,'Cancelado');

insert into td_estadocivilhuespedes (id, descripcion) values (null, 'Soltero/a'),
(null,'Casado/a'),
(null,'Viudo/a'),
(null, 'Union libre');

insert into td_sexohuespedes values (null,'Hombre'),
(null,'Mujer'),
(null,'Otro');

insert into td_tipodocumentohuespedes values (null,'Cedula de Ciudadanía'),
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

INSERT INTO td_tamaño 
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

insert into td_urgencia values (null,'Bajo'),
(null,'Medio'),
(null,'Alto');

insert into td_categoria values (null,'Servicio'),
(null,'Habitacion'),
(null,'Atencion'),
(null,'Otro');

insert into td_estadopqrs values (null,'Solucionado'),
(null,'Pendiente'),
(null,'Nuevo');

INSERT INTO td_estadoHabitacion
VALUES (NULL, 'EN USO'),
(NULL, 'DISPONIBLE'),
(NULL, 'INACTIVO');

INSERT INTO td_sexoEmpleados
VALUES (NULL, 'HOMBRE'),
(NULL, 'MUJER'),
(NULL, 'OTRO');

INSERT INTO td_estadomantenimiento
VALUES (NULL, 'EN PROCESO'),
(NULL, 'FINALIZADO'),
(NULL, 'PENDIENTE');

INSERT INTO td_tipodocumentoempleados
VALUES (NULL, 'CEDULA DE CIUDADANIA'),
(NULL, 'TARJETA DE IDENTIDAD'),
(NULL, 'CÉDULA DE EXTRANJERÍA'),
(NULL, 'PASAPORTE'),
(NULL, 'REGISTRO CIVIL');

INSERT INTO td_estadocivilempleados 
VALUES (NULL, 'SOLTERO/A'),
(NULL, 'CASADO/A'),
(NULL, 'VIUDO/A'),
(NULL, 'UNIÓN LIBRE');

INSERT INTO td_roles
VALUES (NULL, 'ADMINISTRADOR'),
(NULL, 'RECEPCIONISTA'),
(NULL, 'ATENCIÓN AL CLIENTE'),
(NULL, 'GERENTE');

INSERT INTO tp_huespedes
VALUES (1000289068, 3116182673, 'Bleachowl98@gmail.com', 'Favian Alejandro', 'Machuca Pedraza', 1, 1, 4),
(1234098756, 3124233442, 'Camilín@gmail.com', 'Camilo Andrés', 'Guerrero Yanquen', 1, 1, 1),
(1098785643, 3214566786, 'Jhonny@gmail.com', 'Jonathan David', 'Fernández López', 1, 1, 1),
(1002455665, 3144235027, 'Bray@gmail.com', 'Brayan Felipe', 'Pulido López', 1, 1, 3),
(1012099089, 302099086, 'Willy@gmail.com', 'William Steven', 'Daza Delgado', 1, 1, 2);

insert into tp_empleados values (1122123456,'Joaquin Diomedes','Gonzales Chaz','Calle 73 D#8C', "2003-02-15", 3118273847,3028732645,'GonzChaz@gmail.com',1,1,1,1),
(1837263544,'Fernando Luis','Quintero','Cra 18-13','2003-06-20', 3124327658,3014765897,'FerLuch321@gmail.com',1,3,4,3),
(1029384634,'Patroclo','Hernandez Pinzon','Calle 98 cra 10','2000-10-10',3107384576,3011945998,'PatrocloPinpin@gmail.com',1,2,2,1),
(1922345555,'Ignacio Marcelo','Lomas','Cra 87-23 #65','1999-12-24',3224857743,3019476534,'IgMaLomas456@gmail.com',1,3,2,3),
(1626478765,'Marta Maria','Muñoz Mendoza','Calle 14 #45D','2001-04-01',3216457866,3205766453,'MMMMendoza000@gmail.com',2,1,3,2);

insert into tp_login values (null,1122123456,'Elcacique123'),
(null,1837263544,'Quinterito1990'),
(null,1029384634,'PinzonElHernandez71'),
(null,1922345555,'LomasVaMarcelo15'),
(null,1626478765,'MartaLaMartita23');

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
(null,'Bombillos defectuosos, próximos a dañarse','Reemplazo de bombillos',20220505,20211001,'No aplica',819,1122123456,2),
(null,'Cortinas rasgadas','Reemplazo de cortinas',20230413,20230413,'No aplica',666,1626478765,2),
(null,'Gotera en la llave del lavamanos','Revisar y reparar la fuga de la llave de agua',20241126,20220617,'No aplica',69,1626478765,1),
(null,'Olor raro proveniente del baño','Realizar aseo',20241115,20241115,'No aplica',819,1029384634,3),
(null,'Piso sucio, manchas pegajosas','Realizar aseo en el piso',20240220,20220330,'No aplica',10,1122123456,2);

INSERT INTO td_estadoreserva VALUES (NULL, 'Expirada'),
(NULL, 'Caducado');

INSERT INTO tp_reservas
VALUES (NULL, 350000.00, 20240624, 20240626, 1, 1, 10, 1029384634, 1, 1002455665),
(NULL, 330000.00, 20241028,20241031, 1, 2, 666, 1029384634, 3, 1098785643),
(NULL, 4000000.00, 20251215, 20251220, 4, 4, 73, 1029384634, 1, 1234098756),
(NULL, 1300000.00, 20240321, 20240324, 2, 3, 819, 1922345555, 4, 1000289068);

INSERT INTO ti_responder 
VALUES 
(NULL, '¡Gracias por tu comentario! Lamentamos que hayas tenido una mala experiencia en el hotel Chimbanadas. Tu comentario nos ayuda a mejorar día a día.', 20240916, 1, 1029384634),
(NULL, '¡Gracias por tu comentario! Lamentamos que hayas tenido una mala experiencia en el hotel Patroclín. Tu comentario nos ayuda a mejorar nuestros servicios día a día.', 20241018, 2, 1626478765),
(NULL, '¡Gracias por tu sugerencia! Tus comentarios nos ayudan a mejorar nuestros servicios. Atte: Hotel Bondiola', 20230515, 3, 1922345555);

INSERT INTO ti_atender
VALUES
(NULL, 1, 1002455665,1029384634),
(NULL, 2, 1098785643,1029384634),
(NULL, 3, 1000289068,1922345555),
(NULL, 4, 1234098756,1029384634);



/*vistas*/

create view vista_empleados as
select e.numDocumento as Documento, e.nombres as Nombres, e.apellidos as Apellidos, e.direccion as Direccion, e.numTelefono as Telefono, e.contactoPersonal as Contacto_Personal, e.correo as Correo, s.descripcion as Sexo, d.descripcion as Tipo_Documento, r.descripcion as Rol, ec.descripcion as Estado_Civil
from tp_empleados e
inner join td_sexoempleados s on  e.sexo = s.id 
inner join td_tipodocumentoempleados d on e.tipoDocumento = d.id
inner join td_roles r on e.roles = r.id
inner join td_estadocivilempleados ec on  e.estadoCivil = ec.id;


CREATE VIEW vista_habitaciones AS
SELECT h.numero as Numero_Habitacion, h.costo as Costo, h.capacidad as Capacidad_Personas, t.descripcion as Tipo_Habitacion, ñ.descripcion as Tamaño_Habitacion, e.descripcion as Estado_Habitacion
FROM tp_habitaciones h
INNER JOIN td_tipohabitacion t ON  h.tipoHabitacion = t.id
INNER JOIN td_tamaño ñ ON h.tamaño = ñ.id
INNER JOIN td_estadohabitacion e ON h.estado = e.id;


create view vista_historialMantenimeinto as
select h.id as id, h.problemaDescripcion as Problema, h.accion as Accion, h.fechaRegistro as Fecha_Registro, h.ultimaActualización as Ultima_Actualización, h.frecuencia as Frecuencia, h.numero as Numero_Habitacion, e.nombres as Reporta_Empleado, e.apellidos as Reporta_Apellidos, em.descripcion as Estado
from tp_historialmantenimiento h
inner join tp_empleados e on h.emp_numDocumento = e.numDocumento
inner join td_estadomantenimiento em on h.estadoMantenimiento = em.id;

CREATE VIEW vista_huespedes AS
SELECT u.numDocumento as Documento, u.numTelefono as Telefono, u.correo as Correo, u.nombres as Nombres, u.apellidos as Apellidos, d.descripcion as Tipo_Documento, s.descripcion as Sexo, c.descripcion as Estado_Civil
FROM tp_huespedes u
INNER JOIN td_tipodocumentohuespedes d ON u.tipoDocumento = d.id
INNER JOIN td_sexohuespedes s ON u.sexo = s.id
INNER JOIN td_estadocivilhuespedes c ON u.estadoCivil = c.id;

create view vista_pqrs AS
select p.id as id, p.fechaRegistro as Fecha_Registro, p.descripcion as Descripcion, p.fechaCierre as Fecha_Cierre, h.nombres as Reporta_Huesped, h.apellidos as Reporta_Apellidos, u.descripcion as Urgencia, c.descripcion as Categoria, e.descripcion as Estado, t.descripcion as Tipo
from tp_pqrs p
inner join tp_huespedes h on p.numdocumento = h.numDocumento
inner join td_urgencia u on p.urgencia = u.id
inner join td_categoria c on p.categoria = c.id
inner join td_estadopqrs e on p.estado = e.id
inner join td_tipopqrs t on p.tipo = t.id;




CREATE VIEW vista_respuestas AS
SELECT r.id AS id,
r.descripcion AS Respuesta,
r.fechaRespuesta AS Fecha_respuesta,
p.descripcion AS Descripcion_pqrs,
e.nombres AS Nombres_empleado,
e.apellidos AS Apellidos_empleado
FROM ti_responder r
INNER JOIN tp_pqrs p ON r.descripcion = p.id
INNER JOIN tp_empleados e ON r.emp_numDocumento = e.numDocumento;


CREATE VIEW vista_atender AS
SELECT a.id AS id,
r.id AS id_reserva,
r.costo AS Costo,
r.fechainicio AS Inicio_reserva,
r.fechafin AS Fin_reserva,
r.canPersonas AS Cantidad_personas,
m.descripcion AS Motivo_reserva,
hab.numero AS Número_habitación,
t.descripcion AS Tipo_habitación,
es.descripcion AS Estado_reserva,
h.nombres AS Nombres_huesped,
h.apellidos AS Apellidos_huesped,
e.nombres AS Nombres_empleado,
e.apellidos AS Apellidos_empleado
FROM ti_atender a
INNER JOIN tp_reservas r ON a.res_id = r.id
INNER JOIN td_motivoreserva m ON r.motivoReserva = m.id
INNER JOIN tp_empleados e ON r.emp_numdocumento = e.numDocumento
INNER JOIN td_estadoreserva es ON r.estado = es.id
INNER JOIN tp_huespedes h ON r.hue_numdocumento = h.numDocumento
INNER JOIN tp_habitaciones hab ON hab.numero = r.numero
INNER JOIN td_tipohabitacion t ON hab.tipoHabitacion = t.id;

CREATE VIEW vista_reservas AS
SELECT r.res_id AS id,
r.res_costo AS Costo,
r.res_fechainicio AS Inicio_reserva,
r.res_fechaFin AS Fin_reserva,
r.res_canPersonas AS Cantidad_personas,
m.mot_descripcion AS Motivo_reserva, 
hab.hab_numero AS Número_habitación,
t.tiphab_descripcion AS Tipo_habitación,
es.estres_descripcion AS Estado_reserva,
h.hue_nombres AS Nombres_huesped,
h.hue_apellidos AS Apellidos_huesped,
e.emp_nombres AS Nombres_empleado, 
e.emp_apellidos AS Apellidos_empleado
FROM tp_reservas r
INNER JOIN td_motivoreserva m ON r.res_mot_motivoReserva = m.mot_id
INNER JOIN tp_empleados e ON r.res_emp_numdocumento = e.emp_numDocumento
INNER JOIN td_estadoreserva es ON r.res_estres_estado = es.estres_id
INNER JOIN tp_huespedes h ON r.res_hue_numdocumento = h.hue_numDocumento
INNER JOIN tp_habitaciones hab ON hab.hab_numero = r.res_hab_numero
INNER JOIN td_tipohabitacion t ON hab.hab_tiphab_tipoHabitacion = t.tiphab_id;




CREATE VIEW lista_usuarios AS
SELECT 
l.log_id AS Id_cuenta,
e.emp_numDocumento AS Número_documento,
e.emp_nombres AS Nombres,
e.emp_apellidos AS Apellidos,
r.rol_descripcion AS Rol,
l.log_contraseña AS Contraseña
FROM tp_empleados e
INNER JOIN tp_login l ON l.log_emp_numDocumento = e.emp_numDocumento
INNER JOIN td_roles r ON e.emp_rol_roles = r.rol_id;

