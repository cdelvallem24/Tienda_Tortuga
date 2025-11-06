-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-10-2025 a las 20:10:01
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
-- Base de datos: `tienda`
--
CREATE DATABASE IF NOT EXISTS `tienda` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `tienda`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodos_pago`
--

CREATE TABLE `metodos_pago` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `metodos_pago`
--

INSERT INTO `metodos_pago` (`id`, `nombre`, `descripcion`, `activo`) VALUES
(1, 'Tarjeta de crédito', 'Pago con tarjeta de crédito o débito', 1),
(2, 'PayPal', 'Pago mediante cuenta PayPal', 1),
(3, 'Contra entrega', 'Pago al recibir el producto', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mis_productos`
--

CREATE TABLE `mis_productos` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `price` float(10,2) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `stock` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `mis_productos`
--

INSERT INTO `mis_productos` (`id`, `name`, `description`, `price`, `imagen`, `created`, `modified`, `status`, `stock`) VALUES
(1, 'Reloj Inteligente X20', 'Reloj inteligente con monitor de ritmo cardíaco y pantalla táctil HD.', 150.00, 'reloj_x20.jpg', '2025-10-14 10:47:20', '2025-10-23 11:12:12', '1', 9),
(2, 'Celular Nova A7', 'Smartphone con pantalla de 6.5 pulgadas, 128GB de almacenamiento y cámara dual.', 150.00, 'celular_nova_a7.jpg', '2025-10-14 10:47:20', '2025-10-14 10:47:20', '1', 10),
(3, 'Cargador Rápido USB-C', 'Cargador rápido de 25W compatible con dispositivos Android y USB-C.', 150.00, 'cargador_usb_c.jpg', '2025-10-14 10:47:20', '2025-10-14 10:47:20', '1', 10),
(4, 'Iphone 17', 'Iphone17', 15000.00, 'iphone17.jpg', '2025-10-22 16:40:24', '2025-10-23 11:11:24', '1', 2),
(5, 'iphone 11', 'iphone 11', 10000.00, 'iphone11.jpg', '2025-10-22 16:41:31', '2025-10-23 11:12:12', '1', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden`
--

CREATE TABLE `orden` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `total_price` float(10,2) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `metodo_pago_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden_articulos`
--

CREATE TABLE `orden_articulos` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `orden_articulos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `direccion` text NOT NULL,
  `celular` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `rol` enum('cliente','admin') NOT NULL DEFAULT 'cliente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `direccion`, `celular`, `created_at`, `rol`) VALUES
(1, 'Williams Yos', 'wiliamsyos@gmail.com', '$2y$10$pGLX/dMg3Glpttf9ru2hOOVPqhFMU25d.GCJ0akNYErekky2jlici', '7av 4-77 San Jose Pinula', '43976779', '2025-10-14 15:37:50', 'admin'),
(2, 'dan', 'wyos@gmail.com', '$2y$10$4m6jIlxvmZybYkhNLLrKMO0FOUZaIJnyS/VYpyxLEOl4p/VU7dRoS', 'Guatemala', '43976778', '2025-10-14 21:04:54', 'cliente');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `metodos_pago`
--
ALTER TABLE `metodos_pago`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mis_productos`
--
ALTER TABLE `mis_productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `orden`
--
ALTER TABLE `orden`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `fk_metodo_pago` (`metodo_pago_id`);

--
-- Indices de la tabla `orden_articulos`
--
ALTER TABLE `orden_articulos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `metodos_pago`
--
ALTER TABLE `metodos_pago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `mis_productos`
--
ALTER TABLE `mis_productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `orden`
--
ALTER TABLE `orden`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT de la tabla `orden_articulos`
--
ALTER TABLE `orden_articulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `orden`
--
ALTER TABLE `orden`
  ADD CONSTRAINT `fk_metodo_pago` FOREIGN KEY (`metodo_pago_id`) REFERENCES `metodos_pago` (`id`),
  ADD CONSTRAINT `fk_orden_user` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Filtros para la tabla `orden_articulos`
--
ALTER TABLE `orden_articulos`
  ADD CONSTRAINT `orden_articulos_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orden` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
