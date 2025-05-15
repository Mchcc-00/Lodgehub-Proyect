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
                                         
                                         PRIMARY KEY (emp_numdocumento),
                                         FOREIGN KEY (emp_sexemp_sexo) REFERENCES td_sexoempleados (sexemp_id),
                                         FOREIGN KEY (emp_tipdocemp_tipoDocumento) REFERENCES td_tipodocumentoempleados (tipdocemp_id),
                                         FOREIGN KEY (emp_rol_roles) REFERENCES td_roles (rol_id),
                                         FOREIGN KEY (emp_estcivemp_estadoCivil) REFERENCES td_estadocivilEmpleados (estcivemp_id)
                                         )ENGINE=INNODB;

                                     
CREATE TABLE IF NOT EXISTS td_tipoPqrs (tippqr_id INT (3) AUTO_INCREMENT NOT NULL,
                                        tippqr_descripcion VARCHAR (30) NOT NULL,
                                        
                                        PRIMARY KEY (tippqr_id)
                                        )ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS td_categoria (cat_id INT (3) AUTO_INCREMENT NOT NULL,
                                         cat_descripcion VARCHAR (20) NOT NULL,
                                          
                                         PRIMARY KEY (cat_id)
                                         )ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS td_estadoPQRs (estpqr_id INT (3) AUTO_INCREMENT NOT NULL,
                                          estpqr_descripcion VARCHAR (20) NOT NULL,
                                           
                                          PRIMARY KEY (estpqr_id)
                                          )ENGINE=INNODB; 

CREATE TABLE IF NOT EXISTS td_urgencia (urg_id INT (3) AUTO_INCREMENT NOT NULL,
                                         urg_descripcion VARCHAR (20) NOT NULL,

                                         PRIMARY KEY (urg_id)
                                         ) ENGINE=INNODB;




CREATE TABLE IF NOT EXISTS tp_pqrs (pqr_id INT (10) AUTO_INCREMENT NOT NULL,
                                      pqr_fechaRegistro DATE NOT NULL,
                                      pqr_descripcion VARCHAR (200) NOT NULL,
   pqr_fechaCierre DATE NOT NULL,
                                      pqr_hue_numdocumento BIGINT (11) NOT NULL,
                                      pqr_urg_urgencia INT (3) NOT NULL,
                                      pqr_cat_categoria INT(3) NOT  NULL,
                                      pqr_estpqr_estado INT (3) NOT NULL,
                                      pqr_tippqr_tipo INT(3) NOT NULL,
			  

                                      PRIMARY KEY (pqr_id),
                                      FOREIGN KEY (pqr_hue_numdocumento) REFERENCES tp_huespedes (hue_numdocumento),
                                      FOREIGN KEY (pqr_urg_urgencia) REFERENCES td_urgencia (urg_id),
                                      FOREIGN KEY (pqr_cat_categoria) REFERENCES td_categoria (cat_id),
                                      FOREIGN KEY (pqr_estpqr_estado) REFERENCES td_estadopqrs (estpqr_id),
                                      FOREIGN KEY (pqr_tippqr_tipo) REFERENCES td_tipopqrs (tippqr_id)
                                      ) ENGINE=INNODB;



CREATE TABLE IF NOT EXISTS td_estadoReserva (estres_id INT (3) AUTO_INCREMENT NOT NULL,
                                       		  estres_descripcion VARCHAR (20) NOT NULL,
                                       
                                       		  PRIMARY KEY (estres_id)
                                      		 ) ENGINE=INNODB;
                                      
CREATE TABLE IF NOT EXISTS td_motivoReserva (mot_id INT (3) AUTO_INCREMENT NOT NULL,
                                              mot_descripcion VARCHAR (20) NOT NULL,
                                              
                                              PRIMARY KEY (mot_id)
                                              ) ENGINE=INNODB;






CREATE TABLE IF NOT EXISTS tp_reservas (res_id INT (3) AUTO_INCREMENT NOT NULL,
                                        res_costo FLOAT (10.5) NOT NULL,
                                        res_fechainicio DATE NOT NULL,
                                        res_fechaFin DATE NOT NULL,
                                        res_canPersonas INT (3) NOT NULL,
                                        res_mot_motivoReserva INT (3) NOT NULL,
                                        res_hab_numero INT (3) NOT NULL,
                                        res_emp_numdocumento BIGINT (11) NOT NULL,
                                        res_estres_estado INT (3) NOT NULL,
                                        res_hue_numdocumento BIGINT (11) NOT NULL,
                                        
                                        PRIMARY KEY (res_id),
                                        FOREIGN KEY (res_mot_motivoReserva) REFERENCES td_motivoreserva (mot_id),
                                        FOREIGN KEY (res_hab_numero) REFERENCES tp_habitaciones (hab_numero),
                                        FOREIGN KEY (res_emp_numdocumento) REFERENCES tp_empleados (emp_numDocumento),
                                        FOREIGN KEY (res_estres_estado) REFERENCES td_estadoreserva (estres_id),
                                        FOREIGN KEY (res_hue_numdocumento) REFERENCES tp_huespedes (hue_numDocumento)
                                        )ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS td_estadoMantenimiento (estman_id INT (3) AUTO_INCREMENT NOT NULL,
                                                   estman_descripcion VARCHAR (20) NOT NULL,
                                                  PRIMARY KEY (estman_id)
                                                  )ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS tp_historialMantenimiento (hisman_id INT (4) AUTO_INCREMENT NOT NULL,
                                                      hisman_problemaDescripcion VARCHAR (50) NOT NULL,
                                                      hisman_accion VARCHAR (50) NOT NULL,
                                                      hisman_fechaRegistro DATE NOT NULL,
                                                      hisman_ultimaActualización DATE NOT NULL,
                                                      hisman_frecuencia VARCHAR (50) NOT NULL,
                                                      hisman_hab_numero INT (3) NOT NULL,
                                                      hisman_emp_numDocumento BIGINT (11) NOT NULL,
                                                      hisman_estman_estadoMantenimiento INT (3) NOT NULL, 
                                                      PRIMARY KEY (hisman_id),
                                                      FOREIGN KEY (hisman_hab_numero) REFERENCES tp_habitaciones (hab_numero),
                                                      FOREIGN KEY (hisman_emp_numDocumento) REFERENCES tp_empleados (emp_numDocumento),
                                                      FOREIGN KEY (hisman_estman_estadoMantenimiento) REFERENCES td_estadoMantenimiento (estman_id)
                                                      )ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS ti_atender (ate_id INT (3) AUTO_INCREMENT NOT NULL,
                                       ate_res_id INT (3) NOT NULL,
                                       ate_hue_numDocumento BIGINT (11) NOT NULL,
                                       ate_emp_numDocumento BIGINT (11) NOT NULL,
                                       
                                       PRIMARY KEY (ate_id),
                                       FOREIGN KEY (ate_res_id) REFERENCES tp_reservas (res_id),
                                       FOREIGN KEY (ate_hue_numDocumento) REFERENCES tp_huespedes (hue_numDocumento),
                                       FOREIGN KEY (ate_emp_numDocumento) REFERENCES tp_empleados (emp_numDocumento)
                                       )ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS ti_responder (resp_id INT (3) AUTO_INCREMENT NOT NULL,
                                         resp_descripcion VARCHAR (50) NOT NULL,
                                         resp_fechaRespuesta DATE NOT NULL,
                                         resp_pqr_id INT (3) NOT NULL,
                                         resp_emp_numDocumento BIGINT (11) NOT NULL,
                                         
                                         PRIMARY KEY (resp_id),
                                         FOREIGN KEY (resp_pqr_id) REFERENCES tp_pqrs (pqr_id),
                                         FOREIGN KEY (resp_emp_numDocumento) REFERENCES tp_empleados (emp_numDocumento)
                                         )ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS tp_login (log_id INT (4) AUTO_INCREMENT NOT NULL,
                                     log_emp_numDocumento BIGINT (11) NOT NULL,  
  log_contraseña VARCHAR (20) NOT NULL,
                                     
                                     PRIMARY KEY (log_id),
                                     FOREIGN KEY (log_emp_numDocumento) REFERENCES tp_empleados (emp_numDocumento)
                                     )ENGINE=INNODB;

/*inserts*/

insert into td_motivoreserva values (null,'Negocios'),
(null,'Personal'),
(null,'Viaje'),
(null,'Familiar');

insert into td_estadoreserva values (null,'Activo'),
(null,'Pendiente'),
(null,'Cancelado');

insert into td_estadocivilhuespedes (estcivhue_id, estcivhue_descripcion) values (null, 'Soltero/a'),
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

insert into tp_empleados values (1122123456,'Joaquin Diomedes','Gonzales Chaz','Calle 73 D#8C',3118273847,3028732645,'GonzChaz@gmail.com',1,1,1,1),
(1837263544,'Fernando Luis','Quintero','Cra 18-13',3124327658,3014765897,'FerLuch321@gmail.com',1,3,4,3),
(1029384634,'Patroclo','Hernandez Pinzon','Calle 98 cra 10',3107384576,3011945998,'PatrocloPinpin@gmail.com',1,2,2,1),
(1922345555,'Ignacio Marcelo','Lomas','Cra 87-23 #65',3224857743,3019476534,'IgMaLomas456@gmail.com',1,3,2,3),
(1626478765,'Marta Maria','Muñoz Mendoza','Calle 14 #45D',3216457866,3205766453,'MMMMendoza000@gmail.com',2,1,3,2);

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
select e.emp_numDocumento as Documento, e.emp_nombres as Nombres, e.emp_apellidos as Apellidos, e.emp_direccion as Direccion, e.emp_numTelefono as Telefono, e.emp_contactoPersonal as Contacto_Personal, e.emp_correo as Correo, s.sexemp_descripcion as Sexo, d.tipdocemp_descripcion as Tipo_Documento, r.rol_descripcion as Rol, ec.estcivemp_descripcion as Estado_Civil
from tp_empleados e
inner join td_sexoempleados s on  e.emp_sexemp_sexo = s.sexemp_id 
inner join td_tipodocumentoempleados d on e.emp_tipdocemp_tipoDocumento = d.tipdocemp_id
inner join td_roles r on e.emp_rol_roles = r.rol_id
inner join td_estadocivilempleados ec on  e.emp_estcivemp_estadoCivil = ec.estcivemp_id;


CREATE VIEW vista_habitaciones AS
SELECT h.hab_numero as Numero_Habitacion, h.hab_costo as Costo, h.hab_capacidad as Capacidad_Personas, t.tiphab_descripcion as Tipo_Habitacion, ñ.tam_descripcion as Tamaño_Habitacion, e.esthab_descripcion as Estado_Habitacion
FROM tp_habitaciones h
INNER JOIN td_tipohabitacion t ON  h.hab_tiphab_tipoHabitacion = t.tiphab_id
INNER JOIN td_tamaño ñ ON h.hab_tam_tamaño = ñ.tam_id
INNER JOIN td_estadohabitacion e ON h.hab_esthab_estado = e.esthab_id;


create view vista_historialMantenimeinto as
select h.hisman_id as id, h.hisman_problemaDescripcion as Problema, h.hisman_accion as Accion, h.hisman_fechaRegistro as Fecha_Registro, h.hisman_ultimaActualización as Ultima_Actualización, h.hisman_frecuencia as Frecuencia, h.hisman_hab_numero as Numero_Habitacion, e.emp_nombres as Reporta_Empleado, e.emp_apellidos as Reporta_Apellidos, em.estman_descripcion as Descripcion
from tp_historialmantenimiento h
inner join tp_empleados e on h.hisman_emp_numDocumento = e.emp_numDocumento
inner join td_estadomantenimiento em on h.hisman_estman_estadoMantenimiento = em.estman_id;

CREATE VIEW vista_huespedes AS
SELECT u.hue_numDocumento as Documento, u.hue_numTelefono as Telefono, u.hue_correo as Correo, u.hue_nombres as Nombres, u.hue_apellidos as Apellidos, d.tipdochue_descripcion as Tipo_Documento, s.sexhue_descripcion as Sexo, c.estcivhue_descripcion as Estado_Civil
FROM tp_huespedes u
INNER JOIN td_tipodocumentohuespedes d ON u.hue_tipdochue_tipoDocumento = d.tipdochue_id
INNER JOIN td_sexohuespedes s ON u.hue_sexhue_sexo = s.sexhue_id
INNER JOIN td_estadocivilhuespedes c ON u.hue_estcivhue_estadoCivil = c.estcivhue_id;

create view vista_pqrs AS
select p.pqr_id as id, p.pqr_fechaRegistro as Fecha_Registro, p.pqr_descripcion as Descripcion, p.pqr_fechaCierre as Fecha_Cierre, h.hue_nombres as Reporta_Huesped, h.hue_apellidos as Reporta_Apellidos, u.urg_descripcion as Urgencia, c.cat_descripcion as Categoria, e.estpqr_descripcion as Estado, t.tippqr_descripcion as Tipo
from tp_pqrs p
inner join tp_huespedes h on p.pqr_hue_numdocumento = h.hue_numDocumento
inner join td_urgencia u on p.pqr_urg_urgencia = u.urg_id
inner join td_categoria c on p.pqr_cat_categoria = c.cat_id
inner join td_estadopqrs e on p.pqr_estpqr_estado = e.estpqr_id
inner join td_tipopqrs t on p.pqr_tippqr_tipo = t.tippqr_id;




CREATE VIEW vista_respuestas AS
SELECT r.resp_id AS id,
r.resp_descripcion AS Respuesta,
r.resp_fechaRespuesta AS Fecha_respuesta,
p.pqr_descripcion AS Descripcion_pqrs,
e.emp_nombres AS Nombres_empleado,
e.emp_apellidos AS Apellidos_empleado
FROM ti_responder r
INNER JOIN tp_pqrs p ON p.pqr_descripcion = p.pqr_id
INNER JOIN tp_empleados e ON r.resp_emp_numDocumento = e.emp_numDocumento;


CREATE VIEW vista_atender AS
SELECT a.ate_id AS id,
r.res_id AS id_reserva,
r.res_costo AS Costo,
r.res_fechainicio AS Inicio_reserva,
r.res_fechafin AS Fin_reserva,
r.res_canPersonas AS Cantidad_personas,
m.mot_descripcion AS Motivo_reserva,
hab.hab_numero AS Número_habitación,
t.tiphab_descripcion AS Tipo_habitación,
es.estres_descripcion AS Estado_reserva,
h.hue_nombres AS Nombres_huesped,
h.hue_apellidos AS Apellidos_huesped,
e.emp_nombres AS Nombres_empleado,
e.emp_apellidos AS Apellidos_empleado
FROM ti_atender a
INNER JOIN tp_reservas r ON a.ate_res_id = r.res_id
INNER JOIN td_motivoreserva m ON r.res_mot_motivoReserva = m.mot_id
INNER JOIN tp_empleados e ON r.res_emp_numdocumento = e.emp_numDocumento
INNER JOIN td_estadoreserva es ON r.res_estres_estado = es.estres_id
INNER JOIN tp_huespedes h ON r.res_hue_numdocumento = h.hue_numDocumento
INNER JOIN tp_habitaciones hab ON hab.hab_numero = r.res_hab_numero
INNER JOIN td_tipohabitacion t ON hab.hab_tiphab_tipoHabitacion = t.tiphab_id;

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

