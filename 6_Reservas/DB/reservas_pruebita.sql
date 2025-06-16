-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-05-2025 a las 18:30:16
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `reservas_pruebita`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `td_estadocivil`
--

CREATE TABLE `td_estadocivil` (
  `estCiv_id` int(3) NOT NULL,
  `estCiv_descripcion` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `td_estadocivil`
--

INSERT INTO `td_estadocivil` (`estCiv_id`, `estCiv_descripcion`) VALUES
(1, 'Soltero/a'),
(2, 'Casado/a'),
(3, 'Viudo/a'),
(4, 'Union libre');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `td_estadoreserva`
--

CREATE TABLE `td_estadoreserva` (
  `estRes_id` int(3) NOT NULL,
  `estRes_descripcion` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `td_estadoreserva`
--

INSERT INTO `td_estadoreserva` (`estRes_id`, `estRes_descripcion`) VALUES
(1, 'Activa'),
(2, 'Pendiente'),
(3, 'Cancelada'),
(4, 'Caducada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `td_metodopagoreserva`
--

CREATE TABLE `td_metodopagoreserva` (
  `metPagRes_id` int(3) NOT NULL,
  `metPagRes_descripcion` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `td_metodopagoreserva`
--

INSERT INTO `td_metodopagoreserva` (`metPagRes_id`, `metPagRes_descripcion`) VALUES
(1, 'Tarjeta'),
(2, 'Efectivo'),
(3, 'PSE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `td_motivoreserva`
--

CREATE TABLE `td_motivoreserva` (
  `motRes_id` int(3) NOT NULL,
  `motRes_descripcion` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `td_motivoreserva`
--

INSERT INTO `td_motivoreserva` (`motRes_id`, `motRes_descripcion`) VALUES
(1, 'Negocios'),
(2, 'Personal'),
(3, 'Viaje'),
(4, 'Familiar');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `td_numeropersonasreservas`
--

CREATE TABLE `td_numeropersonasreservas` (
  `numPerRes_id` bigint(11) NOT NULL,
  `numPerRes_Adultos` int(2) NOT NULL,
  `numPerRes_Menores` int(2) NOT NULL,
  `numPerRes_Discapacitados` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `td_numeropersonasreservas`
--

INSERT INTO `td_numeropersonasreservas` (`numPerRes_id`, `numPerRes_Adultos`, `numPerRes_Menores`, `numPerRes_Discapacitados`) VALUES
(1, 2, 2, 10),
(2, 2, 2, 10),
(3, 2, 2, 10),
(4, 2, 2, 10),
(5, 2, 2, 10),
(6, 2, 2, 10),
(7, 2, 2, 10),
(8, 34, 34, 43),
(9, 34, 34, 43),
(10, 34, 34, 43),
(11, 34, 34, 43),
(12, 23, 24, 23),
(13, 2, 0, 0),
(14, 1, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `td_sexo`
--

CREATE TABLE `td_sexo` (
  `sex_id` int(3) NOT NULL,
  `sex_descripcion` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `td_sexo`
--

INSERT INTO `td_sexo` (`sex_id`, `sex_descripcion`) VALUES
(1, 'Hombre'),
(2, 'Mujer'),
(3, 'Otro'),
(4, 'Prefiero no decir');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `td_tipodocumento`
--

CREATE TABLE `td_tipodocumento` (
  `tipDoc_id` int(3) NOT NULL,
  `tipDoc_descripcion` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `td_tipodocumento`
--

INSERT INTO `td_tipodocumento` (`tipDoc_id`, `tipDoc_descripcion`) VALUES
(1, 'CC'),
(2, 'TI'),
(3, 'CE'),
(4, 'PAS'),
(5, 'RC');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tp_huespedes`
--

CREATE TABLE `tp_huespedes` (
  `hue_numDocumento` bigint(11) NOT NULL,
  `hue_nombres` varchar(50) NOT NULL,
  `hue_apellidos` varchar(50) NOT NULL,
  `hue_tipDoc_tipoDocumento` int(3) NOT NULL,
  `hue_sex_sexo` int(3) NOT NULL,
  `hue_estCiv_estadoCivil` int(3) NOT NULL,
  `hue_numContacto` bigint(11) NOT NULL,
  `hue_correo` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tp_huespedes`
--

INSERT INTO `tp_huespedes` (`hue_numDocumento`, `hue_nombres`, `hue_apellidos`, `hue_tipDoc_tipoDocumento`, `hue_sex_sexo`, `hue_estCiv_estadoCivil`, `hue_numContacto`, `hue_correo`) VALUES
(1010258461, 'Brayan Alejandro', 'Machuca Lopez', 1, 4, 2, 3014032520, 'BraFARC@gmail.com'),
(1212121212, 'jhonny', 'el picado', 1, 2, 1, 2112212112, '12122112@gmail.com'),
(1454504534, 'gesse', 'gegrgesg', 1, 1, 1, 4534720453, 'efawefsefe'),
(2323234321, 'puchain', 'marruecos', 4, 4, 3, 3122132121, 'puchainitapipiip@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tp_reservas`
--

CREATE TABLE `tp_reservas` (
  `res_id` bigint(11) NOT NULL,
  `res_hue_numDocumento` bigint(11) NOT NULL,
  `res_fechaInicio` date NOT NULL,
  `res_fechaFin` date NOT NULL,
  `res_numPerRes_id` bigint(11) NOT NULL,
  `res_motRes_motivo` int(3) NOT NULL,
  `res_temhab_numHabitacion` int(4) NOT NULL,
  `res_metPagRes_metodoPago` int(3) NOT NULL,
  `res_costoReserva` decimal(10,2) NOT NULL,
  `res_informacionAdicional` text DEFAULT NULL,
  `res_temEmp_numDocumento` bigint(11) NOT NULL,
  `res_estRes_estadoReserva` int(3) NOT NULL,
  `res_fecRegistro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tp_reservas`
--

INSERT INTO `tp_reservas` (`res_id`, `res_hue_numDocumento`, `res_fechaInicio`, `res_fechaFin`, `res_numPerRes_id`, `res_motRes_motivo`, `res_temhab_numHabitacion`, `res_metPagRes_metodoPago`, `res_costoReserva`, `res_informacionAdicional`, `res_temEmp_numDocumento`, `res_estRes_estadoReserva`, `res_fecRegistro`) VALUES
(2, 1010258461, '2025-05-12', '2025-05-28', 2, 4, 69, 1, 5000000.00, '', 1029384634, 1, '2025-05-12 07:36:30'),
(3, 1010258461, '2025-05-12', '2025-05-28', 3, 4, 69, 1, 5000000.00, '', 1029384634, 1, '2025-05-12 07:36:32'),
(4, 1010258461, '2025-05-12', '2025-05-28', 4, 4, 69, 1, 5000000.00, '', 1029384634, 1, '2025-05-12 07:37:00'),
(5, 1010258461, '2025-05-12', '2025-05-28', 5, 4, 69, 1, 5000000.00, '', 1029384634, 1, '2025-05-12 07:41:58'),
(6, 1010258461, '2025-05-12', '2025-05-28', 6, 4, 69, 1, 5000000.00, '', 1029384634, 1, '2025-05-12 07:42:00'),
(7, 1010258461, '2025-05-12', '2025-05-28', 7, 4, 69, 1, 5000000.00, '', 1029384634, 1, '2025-05-12 07:42:03'),
(12, 1454504534, '2025-05-21', '2025-05-31', 12, 1, 666, 2, 99999999.99, 'sdsaasvdvasgeg sdetawetmewsemundrnyucdrny esm yzse', 1626478765, 1, '2025-05-12 07:53:31'),
(13, 2323234321, '2025-05-13', '2025-05-26', 13, 2, 69, 2, 99999999.99, 'ummmmmm ilovvepuchainaaassasasasasasas uwuwuwuuwuw', 1626478765, 1, '2025-05-12 10:11:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ttemporalempleados`
--

CREATE TABLE `ttemporalempleados` (
  `temEmp_id` bigint(11) NOT NULL,
  `temEmp_nombres` varchar(40) NOT NULL,
  `temEmp_apellidos` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ttemporalempleados`
--

INSERT INTO `ttemporalempleados` (`temEmp_id`, `temEmp_nombres`, `temEmp_apellidos`) VALUES
(1029384634, 'Patroclo', 'Hernandez Pinzon'),
(1626478765, 'Marta Maria', 'Muñoz Mendoza'),
(1922345555, 'Ignacio Marcelo', 'Lomas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ttemporalhabitaciones`
--

CREATE TABLE `ttemporalhabitaciones` (
  `temhab_id` int(4) NOT NULL,
  `temhab_costo` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ttemporalhabitaciones`
--

INSERT INTO `ttemporalhabitaciones` (`temhab_id`, `temhab_costo`) VALUES
(69, 1200000.00),
(73, 900000.00),
(666, 280000.00),
(819, 500000.00);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `td_estadocivil`
--
ALTER TABLE `td_estadocivil`
  ADD PRIMARY KEY (`estCiv_id`);

--
-- Indices de la tabla `td_estadoreserva`
--
ALTER TABLE `td_estadoreserva`
  ADD PRIMARY KEY (`estRes_id`);

--
-- Indices de la tabla `td_metodopagoreserva`
--
ALTER TABLE `td_metodopagoreserva`
  ADD PRIMARY KEY (`metPagRes_id`);

--
-- Indices de la tabla `td_motivoreserva`
--
ALTER TABLE `td_motivoreserva`
  ADD PRIMARY KEY (`motRes_id`);

--
-- Indices de la tabla `td_numeropersonasreservas`
--
ALTER TABLE `td_numeropersonasreservas`
  ADD PRIMARY KEY (`numPerRes_id`);

--
-- Indices de la tabla `td_sexo`
--
ALTER TABLE `td_sexo`
  ADD PRIMARY KEY (`sex_id`);

--
-- Indices de la tabla `td_tipodocumento`
--
ALTER TABLE `td_tipodocumento`
  ADD PRIMARY KEY (`tipDoc_id`);

--
-- Indices de la tabla `tp_huespedes`
--
ALTER TABLE `tp_huespedes`
  ADD PRIMARY KEY (`hue_numDocumento`),
  ADD KEY `hue_tipDoc_tipoDocumento` (`hue_tipDoc_tipoDocumento`),
  ADD KEY `hue_sex_sexo` (`hue_sex_sexo`),
  ADD KEY `hue_estCiv_estadoCivil` (`hue_estCiv_estadoCivil`);

--
-- Indices de la tabla `tp_reservas`
--
ALTER TABLE `tp_reservas`
  ADD PRIMARY KEY (`res_id`),
  ADD KEY `res_hue_numDocumento` (`res_hue_numDocumento`),
  ADD KEY `res_numPerRes_id` (`res_numPerRes_id`),
  ADD KEY `res_motRes_motivo` (`res_motRes_motivo`),
  ADD KEY `res_temhab_numHabitacion` (`res_temhab_numHabitacion`),
  ADD KEY `res_metPagRes_metodoPago` (`res_metPagRes_metodoPago`),
  ADD KEY `res_temEmp_numDocumento` (`res_temEmp_numDocumento`),
  ADD KEY `res_estRes_estadoReserva` (`res_estRes_estadoReserva`);

--
-- Indices de la tabla `ttemporalempleados`
--
ALTER TABLE `ttemporalempleados`
  ADD PRIMARY KEY (`temEmp_id`);

--
-- Indices de la tabla `ttemporalhabitaciones`
--
ALTER TABLE `ttemporalhabitaciones`
  ADD PRIMARY KEY (`temhab_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `td_estadocivil`
--
ALTER TABLE `td_estadocivil`
  MODIFY `estCiv_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `td_estadoreserva`
--
ALTER TABLE `td_estadoreserva`
  MODIFY `estRes_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `td_metodopagoreserva`
--
ALTER TABLE `td_metodopagoreserva`
  MODIFY `metPagRes_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `td_motivoreserva`
--
ALTER TABLE `td_motivoreserva`
  MODIFY `motRes_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `td_numeropersonasreservas`
--
ALTER TABLE `td_numeropersonasreservas`
  MODIFY `numPerRes_id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `td_sexo`
--
ALTER TABLE `td_sexo`
  MODIFY `sex_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `td_tipodocumento`
--
ALTER TABLE `td_tipodocumento`
  MODIFY `tipDoc_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tp_reservas`
--
ALTER TABLE `tp_reservas`
  MODIFY `res_id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tp_huespedes`
--
ALTER TABLE `tp_huespedes`
  ADD CONSTRAINT `tp_huespedes_ibfk_1` FOREIGN KEY (`hue_tipDoc_tipoDocumento`) REFERENCES `td_tipodocumento` (`tipDoc_id`),
  ADD CONSTRAINT `tp_huespedes_ibfk_2` FOREIGN KEY (`hue_sex_sexo`) REFERENCES `td_sexo` (`sex_id`),
  ADD CONSTRAINT `tp_huespedes_ibfk_3` FOREIGN KEY (`hue_estCiv_estadoCivil`) REFERENCES `td_estadocivil` (`estCiv_id`);

--
-- Filtros para la tabla `tp_reservas`
--
ALTER TABLE `tp_reservas`
  ADD CONSTRAINT `tp_reservas_ibfk_1` FOREIGN KEY (`res_hue_numDocumento`) REFERENCES `tp_huespedes` (`hue_numDocumento`),
  ADD CONSTRAINT `tp_reservas_ibfk_2` FOREIGN KEY (`res_numPerRes_id`) REFERENCES `td_numeropersonasreservas` (`numPerRes_id`),
  ADD CONSTRAINT `tp_reservas_ibfk_3` FOREIGN KEY (`res_motRes_motivo`) REFERENCES `td_motivoreserva` (`motRes_id`),
  ADD CONSTRAINT `tp_reservas_ibfk_4` FOREIGN KEY (`res_temhab_numHabitacion`) REFERENCES `ttemporalhabitaciones` (`temhab_id`),
  ADD CONSTRAINT `tp_reservas_ibfk_5` FOREIGN KEY (`res_metPagRes_metodoPago`) REFERENCES `td_metodopagoreserva` (`metPagRes_id`),
  ADD CONSTRAINT `tp_reservas_ibfk_6` FOREIGN KEY (`res_temEmp_numDocumento`) REFERENCES `ttemporalempleados` (`temEmp_id`),
  ADD CONSTRAINT `tp_reservas_ibfk_7` FOREIGN KEY (`res_estRes_estadoReserva`) REFERENCES `td_estadoreserva` (`estRes_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
