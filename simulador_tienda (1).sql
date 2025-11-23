-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 23-11-2025 a las 20:25:07
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `simulador_tienda`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carro`
--

CREATE TABLE `carro` (
  `CodCarro` int(11) NOT NULL,
  `Usuario` varchar(100) NOT NULL,
  `Fecha` datetime NOT NULL,
  `Total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Enviado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carro`
--

INSERT INTO `carro` (`CodCarro`, `Usuario`, `Fecha`, `Total`, `Enviado`) VALUES
(4, 'mchaparro@gmail.com', '2025-11-17 19:50:44', 5.97, 1),
(5, 'mchaparro@gmail.com', '2025-11-17 20:19:41', 19.20, 1),
(7, 'mchaparro@gmail.com', '2025-11-20 20:25:10', 1.99, 1),
(8, 'mchaparro@gmail.com', '2025-11-20 21:07:17', 3.50, 1),
(9, 'mchaparro@gmail.com', '2025-11-20 21:17:17', 6.40, 1),
(10, 'mchaparro@gmail.com', '2025-11-20 21:21:40', 7.00, 1),
(11, 'mchaparro@gmail.com', '2025-11-20 21:24:34', 10.50, 1),
(12, 'mchaparro@gmail.com', '2025-11-20 21:52:19', 1.99, 1),
(13, 'mchaparro@gmail.com', '2025-11-20 21:56:42', 1.50, 1),
(14, 'mchaparro@gmail.com', '2025-11-21 23:36:14', 7.00, 1),
(15, 'mchaparro@gmail.com', '2025-11-22 00:27:01', 1.99, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carroproductos`
--

CREATE TABLE `carroproductos` (
  `CodCarroProd` int(11) NOT NULL,
  `CodCarro` int(11) NOT NULL,
  `CodProd` int(11) NOT NULL,
  `Unidades` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carroproductos`
--

INSERT INTO `carroproductos` (`CodCarroProd`, `CodCarro`, `CodProd`, `Unidades`) VALUES
(1, 4, 1, 3),
(2, 5, 5, 6),
(3, 7, 6, 2),
(4, 7, 1, 1),
(5, 8, 5, 1),
(6, 8, 4, 1),
(7, 9, 6, 1),
(8, 9, 5, 2),
(9, 10, 7, 2),
(10, 10, 4, 2),
(13, 11, 4, 3),
(16, 12, 1, 1),
(19, 13, 7, 2),
(22, 14, 4, 2),
(23, 15, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `CodCat` int(11) NOT NULL,
  `Nombre` varchar(45) NOT NULL,
  `Descripcion` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`CodCat`, `Nombre`, `Descripcion`) VALUES
(1, 'Frutas', 'Frutas de españas'),
(2, 'Verduras', 'Verduleria'),
(3, 'Gluten', 'productos con gluten'),
(4, 'Frutos del bosque', 'Frutos del bosque'),
(5, 'Cereales', 'Cereales'),
(6, 'Frutas tropicales', 'Frutas tropicales');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `CodProd` int(11) NOT NULL,
  `Nombre` varchar(45) DEFAULT NULL,
  `Descripcion` varchar(90) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `CodCat` int(11) NOT NULL,
  `Precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`CodProd`, `Nombre`, `Descripcion`, `stock`, `CodCat`, `Precio`) VALUES
(1, 'Manzana', 'Manzana Envy: Crujiente, dulce y jugosa.', 20, 1, 1.99),
(2, 'Avocado', 'Aguacates Hass cremosos, perfectos para guacamole.', 20, 1, 2.80),
(3, 'Bananas', 'Plátanos de Canarias: Dulces, cultivo tradicional.', 20, 6, 1.45),
(4, 'Arandanos', 'Arándanos frescos, alto contenido en fibra.', 25, 4, 3.50),
(5, 'Pan', 'Pan de Masa Madre artesanal. Horneado diariamente.', 30, 3, 3.20),
(6, 'Bulbos', 'Bulbos de Flores surtidos listos para plantar.', 15, 2, 8.99),
(7, 'Zanahorias', 'Zanahorias Gigantes, dulces. Ideales para zumos.', 50, 2, 0.75),
(8, 'Naranjas', 'Naranjas Navel de Valencia. Muy jugosas.', 35, 1, 1.60),
(9, 'Peras', 'Peras Conferencia. Pulpa fina, muy jugosa.', 44, 1, 2.15),
(10, 'Sandias', 'Sandías sin Pepitas. Refrescantes y con agua.', 25, 1, 4.95);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `ID` int(10) NOT NULL,
  `NOMBRE` varchar(100) NOT NULL,
  `GMAIL` varchar(200) NOT NULL,
  `CLAVE` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`ID`, `NOMBRE`, `GMAIL`, `CLAVE`) VALUES
(1, 'Maria', 'mchaparro@gmail.com', '12345');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carro`
--
ALTER TABLE `carro`
  ADD PRIMARY KEY (`CodCarro`),
  ADD KEY `fk_carro_usuario` (`Usuario`);

--
-- Indices de la tabla `carroproductos`
--
ALTER TABLE `carroproductos`
  ADD PRIMARY KEY (`CodCarroProd`),
  ADD KEY `CodCarro` (`CodCarro`),
  ADD KEY `CodProd` (`CodProd`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`CodCat`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`CodProd`),
  ADD KEY `CodCat` (`CodCat`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `GMAIL` (`GMAIL`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carro`
--
ALTER TABLE `carro`
  MODIFY `CodCarro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `carroproductos`
--
ALTER TABLE `carroproductos`
  MODIFY `CodCarroProd` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `CodCat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `CodProd` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carro`
--
ALTER TABLE `carro`
  ADD CONSTRAINT `fk_carro_usuario` FOREIGN KEY (`Usuario`) REFERENCES `usuarios` (`GMAIL`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `carroproductos`
--
ALTER TABLE `carroproductos`
  ADD CONSTRAINT `carroproductos_ibfk_1` FOREIGN KEY (`CodCarro`) REFERENCES `carro` (`CodCarro`),
  ADD CONSTRAINT `carroproductos_ibfk_2` FOREIGN KEY (`CodProd`) REFERENCES `productos` (`CodProd`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`CodCat`) REFERENCES `categoria` (`CodCat`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
