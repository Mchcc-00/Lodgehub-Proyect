CREATE TABLE IF NOT EXISTS td_numeroPersonasReservas (numPerRes_id bigint(11) AUTO_INCREMENT not null,
							numPerRes_Adultos int(2) not null,
							numPerRes_Menores int(2) not null,
							numPerRes_Discapacitados int(2) not null,

							primary key (numPerRes_id)
							)ENGINE=InnoDB;



CREATE TABLE IF NOT EXISTS td_motivoReserva (motRes_id int(3) AUTO_INCREMENT not null,
                                            motRes_descripcion VARCHAR (30) NOT NULL,
                                            
                                            primary key (motRes_id)
                                            )ENGINE=InnoDB;



CREATE TABLE IF NOT EXISTS td_metodoPagoReserva (metPagRes_id int(3) AUTO_INCREMENT not null,
                                                metPagRes_descripcion VARCHAR (30) NOT NULL,
                                                
                                                primary key (metPagRes_id)
                                                )ENGINE=InnoDB;



CREATE TABLE IF NOT EXISTS td_tipoDocumento (tipDoc_id int(3) AUTO_INCREMENT not null,
                                            tipDoc_descripcion VARCHAR (30) NOT NULL,
                                            
                                            primary key (tipDoc_id)
                                            )ENGINE=InnoDB;



CREATE TABLE IF NOT EXISTS td_estadoReserva (estRes_id int(3) AUTO_INCREMENT not null,
                                            estRes_descripcion VARCHAR (30) NOT NULL,
                                            
                                            primary key (estRes_id)
                                            )ENGINE=InnoDB;



CREATE TABLE IF NOT EXISTS td_sexo (sex_id int(3) AUTO_INCREMENT not null,
                                    sex_descripcion VARCHAR (30) NOT NULL,
                                    
                                    primary key (sex_id)
                                    )ENGINE=InnoDB;



CREATE TABLE IF NOT EXISTS td_estadoCivil (estCiv_id int(3) AUTO_INCREMENT not null,
                                            estCiv_descripcion VARCHAR (30) NOT NULL,
                                        
                                            primary key (estCiv_id)
                                            )ENGINE=InnoDB;


------------------------------------------------------------------------------------------


CREATE TABLE IF NOT EXISTS tTemporalHabitaciones (temhab_id int(4) not null,
                                                temhab_costo DECIMAL(10,2) NOT NULL,
                                                
                                                primary key (temhab_id)
                                                )ENGINE=InnoDB;



CREATE TABLE IF NOT EXISTS tTemporalEmpleados (temEmp_id BIGINT (11) NOT NULL,
                                                temEmp_nombres VARCHAR (40) NOT NULL,
                                                temEmp_apellidos VARCHAR (40) NOT NULL,
                                                
                                                primary key (temEmp_id)
                                                )ENGINE=InnoDB;



------------------------------------------------------------------------------------------


CREATE TABLE IF NOT EXISTS tp_huespedes (hue_numDocumento BIGINT(11) NOT NULL,
                                        hue_nombres VARCHAR(50) NOT NULL,
                                        hue_apellidos VARCHAR(50) NOT NULL,
                                        hue_tipDoc_tipoDocumento INT(3) NOT NULL,
                                        hue_sex_sexo INT(3) NOT NULL,
                                        hue_estCiv_estadoCivil INT (3) NOT NULL,
                                        hue_numContacto BIGINT(11) NOT NULL,
                                        hue_correo VARCHAR(30) NOT NULL,
                                        
                                        
                                        PRIMARY KEY (hue_numDocumento),
                                        FOREIGN KEY (hue_tipDoc_tipoDocumento) REFERENCES td_tipoDocumento (tipDoc_id),
                                        FOREIGN KEY (hue_sex_sexo) REFERENCES td_sexo (sex_id),
                                        FOREIGN KEY (hue_estCiv_estadoCivil) REFERENCES td_estadoCivil (estCiv_id)
                                        )ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS tp_reservas (res_id bigint(11) AUTO_INCREMENT not null,
                                        res_hue_numDocumento BIGINT (11) NOT NULL,
                                        res_fechaInicio DATE NOT NULL,
                                        res_fechaFin DATE NOT NULL,
                                        res_numPerRes_id bigint(11) NOT NULL,
                                        res_motRes_motivo INT (3) NOT NULL,
                                        res_temhab_numHabitacion int(4) not null,
                                        res_metPagRes_metodoPago int(3) not null,
                                        res_costoReserva DECIMAL(10,2) NOT NULL,
                                        res_informacionAdicional TEXT,
                                        res_temEmp_numDocumento BIGINT (11) NOT NULL,
                                        res_estRes_estadoReserva int(3) NOT NULL,
                                        res_fecRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
                                        
                                        PRIMARY KEY (res_id),
                                        
                                        FOREIGN KEY (res_hue_numDocumento) REFERENCES tp_huespedes (hue_numDocumento),
                                        FOREIGN KEY (res_numPerRes_id) REFERENCES td_numeroPersonasReservas (numPerRes_id),
                                        FOREIGN KEY (res_motRes_motivo) REFERENCES td_motivoReserva (motRes_id),
                                        FOREIGN KEY (res_temhab_numHabitacion) REFERENCES tTemporalHabitaciones (temhab_id),
                                        FOREIGN KEY (res_metPagRes_metodoPago) REFERENCES td_metodoPagoReserva (metPagRes_id),
                                        FOREIGN KEY (res_temEmp_numDocumento) REFERENCES tTemporalEmpleados (temEmp_id),
                                        FOREIGN KEY (res_estRes_estadoReserva) REFERENCES td_estadoReserva (estRes_id)
                                        )ENGINE=INNODB;



------------------------------------------------------------------------------------------



insert into td_estadocivil values (null, 'Soltero/a'),
(null,'Casado/a'),
(null,'Viudo/a'),
(null, 'Union libre');


insert into td_estadoreserva values (null,'Activa'),
(null,'Pendiente'),
(null,'Cancelada'),
(null,'Caducada');


insert into td_metodopagoreserva values (null,'Tarjeta'),
(null,'Efectivo'),
(null,'PSE');


insert into td_motivoreserva values (null,'Negocios'),
(null,'Personal'),
(null,'Viaje'),
(null,'Familiar');


insert into td_sexo values (null,'Hombre'),
(null,'Mujer'),
(null,'Otro'),
(null,'Prefiero no decir');


insert into td_tipodocumento values (null,'CC'),
(null,'TI'),
(null,'CE'),
(null,'PAS'),
(null,'RC');


insert into ttemporalempleados values
(1029384634,'Patroclo','Hernandez Pinzon'),
(1922345555,'Ignacio Marcelo','Lomas'),
(1626478765,'Marta Maria','Muñoz Mendoza');


insert into ttemporalhabitaciones values (666,280000),
(819, 500000),
(73,900000),
(69,1200000);