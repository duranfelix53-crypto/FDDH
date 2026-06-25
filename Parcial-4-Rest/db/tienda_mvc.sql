-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-06-2026 a las 20:17:33
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
-- Base de datos: `tienda_mvc`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora`
--

CREATE TABLE `bitacora` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `usuario_nombre` varchar(120) NOT NULL,
  `accion` varchar(255) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `bitacora`
--

INSERT INTO `bitacora` (`id`, `usuario_id`, `usuario_nombre`, `accion`, `fecha`) VALUES
(1, 1, 'admin', 'Actualizó producto: Cargador', '2026-06-08 02:33:46'),
(2, 1, 'admin', 'Actualizó producto: Cargador', '2026-06-08 02:34:51'),
(3, 1, 'admin', 'Registró producto: Cristopher Emmanuel Lizárraga Hernández', '2026-06-08 02:36:17'),
(4, 1, 'admin', 'Actualizó producto: Cargador', '2026-06-08 02:36:50'),
(5, 1, 'admin', 'Actualizó producto: Cargador', '2026-06-08 02:37:20'),
(6, 1, 'admin', 'Actualizó producto: Cargador', '2026-06-08 02:37:53'),
(7, 1, 'admin', 'Registró producto: audifonos', '2026-06-08 02:45:21'),
(8, 1, 'admin', 'Eliminó producto ID: 3', '2026-06-08 03:30:01'),
(9, 1, 'admin', 'Eliminó producto ID: 2', '2026-06-08 03:30:03'),
(10, 1, 'admin', 'Eliminó producto ID: 6', '2026-06-08 03:30:05'),
(11, 1, 'admin', 'Eliminó producto ID: 7', '2026-06-08 03:30:07'),
(12, 1, 'admin', 'Eliminó producto ID: 8', '2026-06-08 03:30:09'),
(13, 1, 'admin', 'Eliminó producto ID: 9', '2026-06-08 03:30:11'),
(14, 1, 'admin', 'Registró producto: camara', '2026-06-08 03:31:30'),
(15, 1, 'admin', 'Actualizó producto: camara', '2026-06-08 03:32:57'),
(16, 1, 'admin', 'Registró producto: laptop', '2026-06-08 03:47:30'),
(17, 1, 'admin', 'Actualizó producto: camara', '2026-06-08 03:47:43'),
(18, 1, 'admin', 'Registró producto: Cargador', '2026-06-08 04:00:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `precio_compra` decimal(10,2) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `existencia` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `sku`, `nombre`, `descripcion`, `precio_compra`, `precio_venta`, `existencia`, `created_at`, `updated_at`, `imagen`) VALUES
(10, '654t67y', 'camara', 'camara sony', 5000.00, 10000.00, 5, '2026-06-08 10:31:30', '2026-06-08 10:47:43', 'prod_6a269dcf3a282.jpg'),
(11, '4565g547', 'laptop', 'laptop hp', 3000.00, 6000.00, 10, '2026-06-08 10:47:30', '2026-06-08 10:47:30', 'prod_6a269dc284790.jpg'),
(12, 'f54h4', 'Cargador', 'cargador 20 w', 50.00, 100.00, 50, '2026-06-08 11:00:08', '2026-06-08 11:00:08', 'prod_6a26a0b8801b9.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre_completo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`, `nombre_completo`) VALUES
(1, 'admin', '$2y$10$OSuA92FejZe3jjwhAndU6.omKWElHgRhvwd.jY6UZhiTZhmTqv4FK', 'Administrador General');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
