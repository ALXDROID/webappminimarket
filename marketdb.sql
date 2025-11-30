-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 30, 2025 at 01:33 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `marketdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `activo` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `activo`) VALUES
(1, 'Bebidas', 1),
(2, 'Panadería', 1),
(3, 'Abarrotes', 1),
(4, 'Verduras&Frutas', 1);

-- --------------------------------------------------------

--
-- Table structure for table `detalle_pedido`
--

CREATE TABLE `detalle_pedido` (
  `id` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `tipo` enum('retiro','delivery') NOT NULL,
  `metodo` enum('efectivo','paypal') NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `receptor` varchar(120) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT 0.00,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `hora_entrega` time DEFAULT NULL,
  `estado` enum('pendiente','pagado','anulado') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pedidos`
--

INSERT INTO `pedidos` (`id`, `id_usuario`, `tipo`, `metodo`, `direccion`, `receptor`, `telefono`, `total`, `fecha`, `hora_entrega`, `estado`) VALUES
(1, 3, 'retiro', 'efectivo', NULL, NULL, NULL, '4500.00', '2025-11-27 05:47:12', '14:45:00', 'pagado'),
(2, 62, 'retiro', 'paypal', NULL, NULL, '5454345678', '22500.00', '2025-11-28 05:26:01', '14:24:00', 'pagado'),
(3, 63, 'retiro', 'efectivo', NULL, NULL, '56935947869', '8500.00', '2025-11-29 10:08:12', '19:08:00', 'anulado'),
(4, 64, 'retiro', 'efectivo', NULL, NULL, '5435947869', '5000.00', '2025-11-29 10:16:39', '19:16:00', 'pagado'),
(5, 63, 'retiro', 'efectivo', NULL, NULL, '56935947869', '36500.00', '2025-11-29 10:19:45', '19:19:00', 'anulado'),
(6, 63, 'retiro', 'efectivo', NULL, NULL, '56935947869', '7000.00', '2025-11-29 11:01:54', '20:01:00', 'pendiente'),
(7, 69, 'retiro', 'paypal', NULL, NULL, '5654345678', '21000.00', '2025-11-29 11:03:28', '20:03:00', 'pendiente'),
(8, 72, 'retiro', 'efectivo', NULL, NULL, '55935947869', '52000.00', '2025-11-29 11:06:56', '20:11:00', 'anulado'),
(9, 63, 'retiro', 'efectivo', NULL, NULL, '56935947869', '7500.00', '2025-11-30 02:13:17', '16:12:00', 'pendiente'),
(10, 63, 'retiro', 'efectivo', NULL, NULL, '56935947869', '8500.00', '2025-11-30 02:40:15', '11:40:00', 'pendiente'),
(11, 74, 'retiro', 'efectivo', NULL, NULL, '56123456789', '6000.00', '2025-11-30 03:36:28', '04:42:00', 'pendiente'),
(12, 74, 'retiro', 'efectivo', NULL, NULL, '56123456789', '3000.00', '2025-11-30 03:37:26', '16:37:00', 'pendiente'),
(13, 75, 'retiro', 'efectivo', NULL, NULL, '56987654321', '2000.00', '2025-11-30 04:25:46', '14:25:00', 'anulado'),
(14, 75, 'retiro', 'efectivo', NULL, NULL, '56987654321', '1500.00', '2025-11-30 04:51:31', '20:51:00', 'pendiente'),
(15, 72, 'retiro', 'efectivo', NULL, NULL, '55935947869', '6000.00', '2025-11-30 05:42:01', '14:41:00', 'pendiente');

-- --------------------------------------------------------

--
-- Table structure for table `pedido_items`
--

CREATE TABLE `pedido_items` (
  `id` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unit` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pedido_items`
--

INSERT INTO `pedido_items` (`id`, `id_pedido`, `id_producto`, `cantidad`, `precio_unit`, `subtotal`) VALUES
(1, 1, 1, 1, '3000.00', '3000.00'),
(2, 1, 5, 1, '1500.00', '1500.00'),
(3, 2, 16, 10, '1500.00', '15000.00'),
(4, 2, 17, 5, '1500.00', '7500.00'),
(5, 3, 7, 1, '3000.00', '3000.00'),
(6, 3, 8, 1, '1000.00', '1000.00'),
(7, 3, 2, 3, '1500.00', '4500.00'),
(8, 4, 16, 1, '1500.00', '1500.00'),
(9, 4, 17, 1, '1500.00', '1500.00'),
(10, 4, 9, 1, '2000.00', '2000.00'),
(11, 5, 7, 10, '3000.00', '30000.00'),
(12, 5, 8, 2, '1000.00', '2000.00'),
(13, 5, 2, 3, '1500.00', '4500.00'),
(14, 6, 20, 1, '5000.00', '5000.00'),
(15, 6, 18, 1, '2000.00', '2000.00'),
(16, 7, 18, 3, '2000.00', '6000.00'),
(17, 7, 20, 3, '5000.00', '15000.00'),
(18, 8, 20, 8, '5000.00', '40000.00'),
(19, 8, 18, 6, '2000.00', '12000.00'),
(20, 9, 20, 1, '5000.00', '5000.00'),
(21, 9, 12, 1, '2500.00', '2500.00'),
(22, 10, 20, 1, '5000.00', '5000.00'),
(23, 10, 12, 1, '2500.00', '2500.00'),
(24, 10, 8, 1, '1000.00', '1000.00'),
(25, 11, 2, 2, '1500.00', '3000.00'),
(26, 11, 5, 2, '1500.00', '3000.00'),
(27, 12, 8, 3, '1000.00', '3000.00'),
(28, 13, 6, 1, '2000.00', '2000.00'),
(29, 14, 5, 1, '1500.00', '1500.00'),
(36, 15, 16, 1, '1500.00', '1500.00'),
(37, 15, 17, 1, '1500.00', '1500.00'),
(38, 15, 6, 1, '2000.00', '2000.00'),
(39, 15, 15, 1, '1000.00', '1000.00');

-- --------------------------------------------------------

--
-- Table structure for table `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `precio`, `categoria`, `imagen`, `stock`, `activo`) VALUES
(1, 'lentejas kg', '3000.00', 'Abarrotes', 'img_69232618c6393.jpg', 10, 1),
(2, 'coca-cola 1.5 lts', '1500.00', 'Bebidas', 'img_69218ae8de826.jpg', 10, 1),
(3, 'coca-cola zero 1.5 lts', '1500.00', 'Bebidas', 'img_69218b0bab275.jpg', 10, 0),
(4, 'coca-cola zero 1.5 lts', '1500.00', 'Bebidas', 'img_69218b756508f.jpg', 10, 0),
(5, 'fanta 1.5 lts', '1500.00', 'Bebidas', 'img_69218b88bd1e4.jpg', 10, 1),
(6, 'monster', '2000.00', 'Bebidas', 'img_69218ba95b153.jpg', 10, 1),
(7, 'redbull', '3000.00', 'Bebidas', 'img_69218bc525202.jpg', 10, 1),
(8, 'donut', '1000.00', 'Panadería', 'img_69218be30c2ef.jpg', 10, 1),
(9, 'arroz tucapel', '2000.00', 'Abarrotes', 'img_69218c11715b8.jpg', 10, 1),
(10, 'aceite 1lt', '1300.00', 'Abarrotes', 'img_69218c3b443bb.jpg', 10, 1),
(11, 'atun van camps', '2000.00', 'Abarrotes', 'img_69218c59182b1.jpg', 10, 1),
(12, 'cafe monterrey', '2500.00', 'Abarrotes', 'img_69218c77a3e40.jpg', 10, 1),
(13, 'tallarines', '2500.00', 'Abarrotes', 'img_6922cd44bcd83.jpg', 10, 1),
(14, 'pretzel', '1000.00', 'Panadería', 'img_6922d1d9bc72c.png', 10, 0),
(15, 'pretzel', '1000.00', 'Panadería', 'img_6923228233b34.jpg', 10, 1),
(16, 'palta kg', '1500.00', 'Verduras&Frutas', 'img_692353d18e255.jpg', 10, 1),
(17, 'Tomate kg', '1500.00', 'Verduras&Frutas', 'img_6924cac62eb2b.jpg', 10, 1),
(18, 'Limones Malla 1Kg', '2000.00', 'Verduras&Frutas', 'img_692ad08a35c6c.jpg', 10, 1),
(19, 'Limones Malla 1Kg', '2000.00', 'Verduras&Frutas', 'img_692acacc5038c.jfif', 10, 0),
(20, 'sandia', '5000.00', 'Verduras&Frutas', 'img_692ad13687af1.jpg', 10, 1);

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `avatar` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `telefono`, `creado_en`, `avatar`) VALUES
(1, 'test', '123', '2025-11-22 05:42:50', NULL),
(2, 'uu', '765', '2025-11-22 10:02:12', NULL),
(3, 'uu', '935847869', '2025-11-22 10:41:28', NULL),
(6, 'as', '234', '2025-11-22 11:16:36', NULL),
(7, 'Alexis', '635241789', '2025-11-22 11:24:29', NULL),
(9, 'Erence', '6969', '2025-11-22 13:41:58', NULL),
(10, 'Www', '123456', '2025-11-22 13:43:48', NULL),
(13, 'Raúl gay', '12345678', '2025-11-22 21:15:31', NULL),
(21, 'Dana', '0123456', '2025-11-23 04:37:31', NULL),
(22, 'Oscar', '984335703', '2025-11-23 07:04:09', NULL),
(23, 'byron', '78909876', '2025-11-23 15:07:04', NULL),
(25, 'saul', '12321234', '2025-11-23 16:50:16', NULL),
(36, 'Fee', '123456789', '2025-11-23 18:44:28', NULL),
(37, 'Nancy', '029371', '2025-11-23 19:48:48', NULL),
(52, 'Juanito', '321', '2025-11-24 04:47:16', NULL),
(53, 'Eraldo Hermógenes Ermindio', '98884405', '2025-11-24 18:08:50', NULL),
(60, 'lala', '56935847869', '2025-11-28 04:56:04', NULL),
(62, 'checlaudia', '5454345678', '2025-11-28 05:24:24', NULL),
(63, 'Alx', '56935947869', '2025-11-29 09:37:36', 'http://localhost/MiniMarket/img/avatar18_ibhr6v.png'),
(64, 'hime', '5435947869', '2025-11-29 10:16:06', NULL),
(68, 'satoshi', '5435947870', '2025-11-29 10:48:35', NULL),
(69, 'lala', '5654345678', '2025-11-29 11:02:50', NULL),
(70, 'kani', '1935947869', '2025-11-29 11:04:57', NULL),
(72, 'Kani', '55935947869', '2025-11-29 11:06:33', 'http://localhost/MiniMarket/img/avatar18_ibhr6v.png'),
(73, 'kani', '11935947869', '2025-11-30 03:17:38', 'http://localhost/MiniMarket/img/avatar18_ibhr6v.png'),
(74, 'pauly', '56123456789', '2025-11-30 03:35:06', 'http://localhost/MiniMarket/img/avatar18_ibhr6v.png'),
(75, 'ren', '56987654321', '2025-11-30 04:24:48', 'http://localhost/MiniMarket/img/avatar18_ibhr6v.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indexes for table `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indexes for table `pedido_items`
--
ALTER TABLE `pedido_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indexes for table `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `telefono` (`telefono`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `pedido_items`
--
ALTER TABLE `pedido_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD CONSTRAINT `detalle_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id`),
  ADD CONSTRAINT `detalle_pedido_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`);

--
-- Constraints for table `pedido_items`
--
ALTER TABLE `pedido_items`
  ADD CONSTRAINT `pedido_items_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
