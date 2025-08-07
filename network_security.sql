-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-05-2025 a las 19:46:58
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
-- Base de datos: `network_security`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ai_recommendations`
--

CREATE TABLE `ai_recommendations` (
  `rec_id` int(11) NOT NULL,
  `scan_id` int(11) DEFAULT NULL,
  `recommendation` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ai_recommendations`
--

INSERT INTO `ai_recommendations` (`rec_id`, `scan_id`, `recommendation`) VALUES
(4, 4, 'Error al obtener recomendaciones de IA: Error code: 429 - {\'error\': {\'message\': \'You exceeded your current quota, please check your plan and billing details. For more information on this error, read the docs: https://platform.openai.com/docs/guides/error-codes/api-errors.\', \'type\': \'insufficient_quota\', \'param\': None, \'code\': \'insufficient_quota\'}}'),
(5, 5, 'Error al obtener recomendaciones de IA: Error code: 429 - {\'error\': {\'message\': \'You exceeded your current quota, please check your plan and billing details. For more information on this error, read the docs: https://platform.openai.com/docs/guides/error-codes/api-errors.\', \'type\': \'insufficient_quota\', \'param\': None, \'code\': \'insufficient_quota\'}}'),
(6, 6, 'Error al obtener recomendaciones de IA: Error code: 429 - {\'error\': {\'message\': \'You exceeded your current quota, please check your plan and billing details. For more information on this error, read the docs: https://platform.openai.com/docs/guides/error-codes/api-errors.\', \'type\': \'insufficient_quota\', \'param\': None, \'code\': \'insufficient_quota\'}}'),
(7, 7, 'Error al obtener recomendaciones de IA: Error code: 429 - {\'error\': {\'message\': \'You exceeded your current quota, please check your plan and billing details. For more information on this error, read the docs: https://platform.openai.com/docs/guides/error-codes/api-errors.\', \'type\': \'insufficient_quota\', \'param\': None, \'code\': \'insufficient_quota\'}}'),
(8, 8, 'Error al obtener recomendaciones de IA: Error code: 429 - {\'error\': {\'message\': \'You exceeded your current quota, please check your plan and billing details. For more information on this error, read the docs: https://platform.openai.com/docs/guides/error-codes/api-errors.\', \'type\': \'insufficient_quota\', \'param\': None, \'code\': \'insufficient_quota\'}}'),
(9, 9, 'Error al obtener recomendaciones de IA: Error code: 429 - {\'error\': {\'message\': \'You exceeded your current quota, please check your plan and billing details. For more information on this error, read the docs: https://platform.openai.com/docs/guides/error-codes/api-errors.\', \'type\': \'insufficient_quota\', \'param\': None, \'code\': \'insufficient_quota\'}}'),
(10, 10, 'Error al obtener recomendaciones de IA: Error code: 429 - {\'error\': {\'message\': \'You exceeded your current quota, please check your plan and billing details. For more information on this error, read the docs: https://platform.openai.com/docs/guides/error-codes/api-errors.\', \'type\': \'insufficient_quota\', \'param\': None, \'code\': \'insufficient_quota\'}}'),
(12, 12, 'Error al obtener recomendaciones de IA: Error code: 429 - {\'error\': {\'message\': \'You exceeded your current quota, please check your plan and billing details. For more information on this error, read the docs: https://platform.openai.com/docs/guides/error-codes/api-errors.\', \'type\': \'insufficient_quota\', \'param\': None, \'code\': \'insufficient_quota\'}}'),
(13, 13, 'Error al obtener recomendaciones de IA: Error code: 429 - {\'error\': {\'message\': \'You exceeded your current quota, please check your plan and billing details. For more information on this error, read the docs: https://platform.openai.com/docs/guides/error-codes/api-errors.\', \'type\': \'insufficient_quota\', \'param\': None, \'code\': \'insufficient_quota\'}}'),
(14, 14, 'Error al obtener recomendaciones de IA: Error code: 429 - {\'error\': {\'message\': \'You exceeded your current quota, please check your plan and billing details. For more information on this error, read the docs: https://platform.openai.com/docs/guides/error-codes/api-errors.\', \'type\': \'insufficient_quota\', \'param\': None, \'code\': \'insufficient_quota\'}}'),
(15, 15, 'Error al obtener recomendaciones de IA: Error code: 429 - {\'error\': {\'message\': \'You exceeded your current quota, please check your plan and billing details. For more information on this error, read the docs: https://platform.openai.com/docs/guides/error-codes/api-errors.\', \'type\': \'insufficient_quota\', \'param\': None, \'code\': \'insufficient_quota\'}}'),
(16, 16, 'Error al obtener recomendaciones de IA: Error code: 429 - {\'error\': {\'message\': \'You exceeded your current quota, please check your plan and billing details. For more information on this error, read the docs: https://platform.openai.com/docs/guides/error-codes/api-errors.\', \'type\': \'insufficient_quota\', \'param\': None, \'code\': \'insufficient_quota\'}}'),
(17, 17, 'Error al obtener recomendaciones de IA: Error code: 429 - {\'error\': {\'message\': \'You exceeded your current quota, please check your plan and billing details. For more information on this error, read the docs: https://platform.openai.com/docs/guides/error-codes/api-errors.\', \'type\': \'insufficient_quota\', \'param\': None, \'code\': \'insufficient_quota\'}}'),
(18, 18, 'Error al obtener recomendaciones de IA: Error code: 429 - {\'error\': {\'message\': \'You exceeded your current quota, please check your plan and billing details. For more information on this error, read the docs: https://platform.openai.com/docs/guides/error-codes/api-errors.\', \'type\': \'insufficient_quota\', \'param\': None, \'code\': \'insufficient_quota\'}}'),
(19, 19, 'Error al obtener recomendaciones de IA: Error code: 429 - {\'error\': {\'message\': \'You exceeded your current quota, please check your plan and billing details. For more information on this error, read the docs: https://platform.openai.com/docs/guides/error-codes/api-errors.\', \'type\': \'insufficient_quota\', \'param\': None, \'code\': \'insufficient_quota\'}}'),
(20, 20, 'Error al obtener recomendaciones de IA: Error code: 429 - {\'error\': {\'message\': \'You exceeded your current quota, please check your plan and billing details. For more information on this error, read the docs: https://platform.openai.com/docs/guides/error-codes/api-errors.\', \'type\': \'insufficient_quota\', \'param\': None, \'code\': \'insufficient_quota\'}}'),
(21, 21, 'Error al obtener recomendaciones de IA: Error code: 429 - {\'error\': {\'message\': \'You exceeded your current quota, please check your plan and billing details. For more information on this error, read the docs: https://platform.openai.com/docs/guides/error-codes/api-errors.\', \'type\': \'insufficient_quota\', \'param\': None, \'code\': \'insufficient_quota\'}}'),
(22, 22, 'Error al obtener recomendaciones de IA: Error code: 429 - {\'error\': {\'message\': \'You exceeded your current quota, please check your plan and billing details. For more information on this error, read the docs: https://platform.openai.com/docs/guides/error-codes/api-errors.\', \'type\': \'insufficient_quota\', \'param\': None, \'code\': \'insufficient_quota\'}}'),
(23, 23, 'Error al obtener recomendaciones de IA: Error code: 429 - {\'error\': {\'message\': \'You exceeded your current quota, please check your plan and billing details. For more information on this error, read the docs: https://platform.openai.com/docs/guides/error-codes/api-errors.\', \'type\': \'insufficient_quota\', \'param\': None, \'code\': \'insufficient_quota\'}}'),
(24, 24, 'Error al obtener recomendaciones de IA: Error code: 429 - {\'error\': {\'message\': \'You exceeded your current quota, please check your plan and billing details. For more information on this error, read the docs: https://platform.openai.com/docs/guides/error-codes/api-errors.\', \'type\': \'insufficient_quota\', \'param\': None, \'code\': \'insufficient_quota\'}}'),
(25, 25, 'Error al obtener recomendaciones de IA: Error code: 429 - {\'error\': {\'message\': \'You exceeded your current quota, please check your plan and billing details. For more information on this error, read the docs: https://platform.openai.com/docs/guides/error-codes/api-errors.\', \'type\': \'insufficient_quota\', \'param\': None, \'code\': \'insufficient_quota\'}}'),
(26, 26, 'Error al obtener recomendaciones de IA: Connection error.'),
(27, 28, 'Error al obtener recomendaciones de IA: Error code: 429 - {\'error\': {\'message\': \'You exceeded your current quota, please check your plan and billing details. For more information on this error, read the docs: https://platform.openai.com/docs/guides/error-codes/api-errors.\', \'type\': \'insufficient_quota\', \'param\': None, \'code\': \'insufficient_quota\'}}'),
(28, 29, 'Error al obtener recomendaciones de IA: Error code: 429 - {\'error\': {\'message\': \'You exceeded your current quota, please check your plan and billing details. For more information on this error, read the docs: https://platform.openai.com/docs/guides/error-codes/api-errors.\', \'type\': \'insufficient_quota\', \'param\': None, \'code\': \'insufficient_quota\'}}'),
(29, 30, 'Error al obtener recomendaciones de IA: Error code: 429 - {\'error\': {\'message\': \'You exceeded your current quota, please check your plan and billing details. For more information on this error, read the docs: https://platform.openai.com/docs/guides/error-codes/api-errors.\', \'type\': \'insufficient_quota\', \'param\': None, \'code\': \'insufficient_quota\'}}'),
(30, 31, 'Error al obtener recomendaciones de IA: Error code: 429 - {\'error\': {\'message\': \'You exceeded your current quota, please check your plan and billing details. For more information on this error, read the docs: https://platform.openai.com/docs/guides/error-codes/api-errors.\', \'type\': \'insufficient_quota\', \'param\': None, \'code\': \'insufficient_quota\'}}');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `devices`
--

CREATE TABLE `devices` (
  `device_id` int(11) NOT NULL,
  `scan_id` int(11) DEFAULT NULL,
  `ip_address` varchar(15) DEFAULT NULL,
  `hostname` varchar(100) DEFAULT NULL,
  `last_seen` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `devices`
--

INSERT INTO `devices` (`device_id`, `scan_id`, `ip_address`, `hostname`, `last_seen`) VALUES
(10, 4, '192.168.0.1', 'Unknown', '2025-05-14 23:39:59'),
(11, 4, '192.168.0.190', 'AndresBrown.www.tendawifi.com', '2025-05-14 23:39:59'),
(12, 5, '192.168.42.2', 'Unknown', '2025-05-15 00:34:27'),
(13, 5, '192.168.42.241', 'AndresBrown', '2025-05-15 00:34:27'),
(14, 6, '192.168.20.1', 'dlinkrouter.www.tendawifi.com', '2025-05-15 00:58:56'),
(15, 6, '192.168.20.133', 'AndresBrown.www.tendawifi.com', '2025-05-15 00:58:56'),
(16, 7, '192.168.20.1', 'dlinkrouter.www.tendawifi.com', '2025-05-15 18:51:50'),
(17, 7, '192.168.20.133', 'AndresBrown.www.tendawifi.com', '2025-05-15 18:51:50'),
(18, 8, '192.168.20.1', 'dlinkrouter.www.tendawifi.com', '2025-05-15 18:52:21'),
(19, 8, '192.168.20.133', 'AndresBrown.www.tendawifi.com', '2025-05-15 18:52:21'),
(20, 9, '192.168.20.1', 'dlinkrouter.www.tendawifi.com', '2025-05-15 18:54:15'),
(21, 9, '192.168.20.133', 'AndresBrown.www.tendawifi.com', '2025-05-15 18:54:15'),
(22, 10, '192.168.20.1', 'dlinkrouter.www.tendawifi.com', '2025-05-15 18:55:38'),
(23, 10, '192.168.20.133', 'AndresBrown.www.tendawifi.com', '2025-05-15 18:55:38'),
(26, 12, '192.168.20.1', 'dlinkrouter.www.tendawifi.com', '2025-05-15 19:04:25'),
(27, 12, '192.168.20.133', 'AndresBrown.www.tendawifi.com', '2025-05-15 19:04:25'),
(28, 13, '192.168.20.1', 'dlinkrouter.www.tendawifi.com', '2025-05-15 19:05:34'),
(29, 13, '192.168.20.133', 'AndresBrown.www.tendawifi.com', '2025-05-15 19:05:34'),
(30, 14, '192.168.20.1', 'dlinkrouter.www.tendawifi.com', '2025-05-15 19:06:20'),
(31, 14, '192.168.20.133', 'AndresBrown.www.tendawifi.com', '2025-05-15 19:06:20'),
(32, 15, '192.168.20.1', 'dlinkrouter.www.tendawifi.com', '2025-05-15 19:11:56'),
(33, 15, '192.168.20.133', 'AndresBrown.www.tendawifi.com', '2025-05-15 19:11:56'),
(34, 16, '192.168.20.1', 'dlinkrouter.www.tendawifi.com', '2025-05-15 19:12:28'),
(35, 16, '192.168.20.133', 'AndresBrown.www.tendawifi.com', '2025-05-15 19:12:28'),
(36, 17, '192.168.20.1', 'dlinkrouter.www.tendawifi.com', '2025-05-15 19:17:18'),
(37, 17, '192.168.20.133', 'AndresBrown.www.tendawifi.com', '2025-05-15 19:17:18'),
(38, 18, '192.168.20.1', 'dlinkrouter.www.tendawifi.com', '2025-05-15 19:28:51'),
(39, 18, '192.168.20.133', 'AndresBrown.www.tendawifi.com', '2025-05-15 19:28:51'),
(40, 19, '192.168.20.1', 'dlinkrouter.www.tendawifi.com', '2025-05-15 19:43:13'),
(41, 19, '192.168.20.133', 'AndresBrown.www.tendawifi.com', '2025-05-15 19:43:13'),
(42, 20, '192.168.20.1', 'dlinkrouter.www.tendawifi.com', '2025-05-15 19:44:52'),
(43, 20, '192.168.20.133', 'AndresBrown.www.tendawifi.com', '2025-05-15 19:44:52'),
(44, 21, '192.168.20.1', 'dlinkrouter.www.tendawifi.com', '2025-05-16 18:28:41'),
(45, 21, '192.168.20.133', 'AndresBrown.www.tendawifi.com', '2025-05-16 18:28:41'),
(46, 22, '192.168.42.2', 'Unknown', '2025-05-16 19:13:29'),
(47, 22, '192.168.42.241', 'AndresBrown', '2025-05-16 19:13:29'),
(48, 23, '192.168.20.1', 'dlinkrouter.www.tendawifi.com', '2025-05-16 21:17:42'),
(49, 23, '192.168.20.133', 'AndresBrown.www.tendawifi.com', '2025-05-16 21:17:42'),
(50, 23, '192.168.20.161', 'DESKTOP-EMJC36H.www.tendawifi.com', '2025-05-16 21:17:42'),
(51, 24, '192.168.20.1', 'dlinkrouter.www.tendawifi.com', '2025-05-17 21:41:31'),
(52, 24, '192.168.20.133', 'AndresBrown.www.tendawifi.com', '2025-05-17 21:41:31'),
(53, 25, '192.168.42.2', 'Unknown', '2025-05-17 21:43:26'),
(54, 25, '192.168.42.241', 'AndresBrown', '2025-05-17 21:43:26'),
(212, 28, '192.168.20.1', 'dlinkrouter.www.tendawifi.com', '2025-05-17 21:58:43'),
(213, 28, '192.168.20.133', 'AndresBrown.www.tendawifi.com', '2025-05-17 21:58:43'),
(214, 29, '192.168.20.1', 'dlinkrouter.www.tendawifi.com', '2025-05-21 13:46:17'),
(215, 29, '192.168.20.133', 'AndresBrown.www.tendawifi.com', '2025-05-21 13:46:17'),
(216, 30, '192.168.20.1', 'dlinkrouter.www.tendawifi.com', '2025-05-21 19:11:22'),
(217, 30, '192.168.20.133', 'AndresBrown.www.tendawifi.com', '2025-05-21 19:11:22'),
(218, 31, '192.168.20.1', 'dlinkrouter.www.tendawifi.com', '2025-05-23 12:39:03'),
(219, 31, '192.168.20.133', 'AndresBrown.www.tendawifi.com', '2025-05-23 12:39:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login_attempts`
--

CREATE TABLE `login_attempts` (
  `attempt_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `attempted_at` datetime DEFAULT current_timestamp(),
  `success` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `open_ports`
--

CREATE TABLE `open_ports` (
  `port_id` int(11) NOT NULL,
  `device_id` int(11) DEFAULT NULL,
  `port_number` int(11) DEFAULT NULL,
  `service_name` varchar(50) DEFAULT NULL,
  `banner` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `open_ports`
--

INSERT INTO `open_ports` (`port_id`, `device_id`, `port_number`, `service_name`, `banner`) VALUES
(28, 10, 80, 'HTTP', NULL),
(29, 11, 3306, 'MySQL', 'H\0\0\0jHost \'192.168.0.190\' is not allowed to connect to this MariaDB server'),
(30, 11, 443, 'HTTPS', NULL),
(31, 11, 445, 'SMB', NULL),
(32, 11, 8080, 'HTTP-Proxy', NULL),
(33, 11, 80, 'HTTP', NULL),
(34, 12, 53, 'DNS', NULL),
(35, 13, 3306, 'MySQL', 'F\0\0\0jHost \'AndresBrown\' is not allowed to connect to this MariaDB server'),
(36, 13, 443, 'HTTPS', NULL),
(37, 13, 445, 'SMB', NULL),
(38, 13, 8080, 'HTTP-Proxy', NULL),
(39, 13, 80, 'HTTP', NULL),
(40, 14, 443, 'HTTPS', NULL),
(41, 14, 80, 'HTTP', NULL),
(42, 14, 53, 'DNS', NULL),
(43, 15, 3306, 'MySQL', 'X\0\0\0jHost \'AndresBrown.www.tendawifi.com\' is not allowed to connect to this MariaDB server'),
(44, 15, 8080, 'HTTP-Proxy', NULL),
(45, 15, 443, 'HTTPS', NULL),
(46, 15, 80, 'HTTP', NULL),
(47, 15, 445, 'SMB', NULL),
(48, 16, 80, 'HTTP', NULL),
(49, 16, 53, 'DNS', NULL),
(50, 16, 443, 'HTTPS', NULL),
(51, 17, 3306, 'MySQL', 'X\0\0\0jHost \'AndresBrown.www.tendawifi.com\' is not allowed to connect to this MariaDB server'),
(52, 17, 80, 'HTTP', NULL),
(53, 17, 443, 'HTTPS', NULL),
(54, 17, 8080, 'HTTP-Proxy', NULL),
(55, 17, 445, 'SMB', NULL),
(56, 18, 80, 'HTTP', NULL),
(57, 18, 53, 'DNS', NULL),
(58, 18, 443, 'HTTPS', NULL),
(59, 19, 3306, 'MySQL', NULL),
(60, 19, 443, 'HTTPS', NULL),
(61, 19, 445, 'SMB', NULL),
(62, 19, 80, 'HTTP', NULL),
(63, 19, 8080, 'HTTP-Proxy', NULL),
(64, 20, 53, 'DNS', NULL),
(65, 20, 80, 'HTTP', NULL),
(66, 20, 443, 'HTTPS', NULL),
(67, 21, 3306, 'MySQL', NULL),
(68, 21, 443, 'HTTPS', NULL),
(69, 21, 80, 'HTTP', NULL),
(70, 21, 445, 'SMB', NULL),
(71, 21, 8080, 'HTTP-Proxy', NULL),
(72, 22, 53, 'DNS', NULL),
(73, 22, 80, 'HTTP', NULL),
(74, 22, 443, 'HTTPS', NULL),
(75, 23, 3306, 'MySQL', NULL),
(76, 23, 443, 'HTTPS', NULL),
(77, 23, 80, 'HTTP', NULL),
(78, 23, 445, 'SMB', NULL),
(79, 23, 8080, 'HTTP-Proxy', NULL),
(88, 26, 443, 'HTTPS', NULL),
(89, 26, 53, 'DNS', NULL),
(90, 26, 80, 'HTTP', NULL),
(91, 27, 3306, 'MySQL', NULL),
(92, 27, 8080, 'HTTP-Proxy', NULL),
(93, 27, 445, 'SMB', NULL),
(94, 27, 80, 'HTTP', NULL),
(95, 27, 443, 'HTTPS', NULL),
(96, 28, 53, 'DNS', NULL),
(97, 28, 80, 'HTTP', NULL),
(98, 28, 443, 'HTTPS', NULL),
(99, 29, 3306, 'MySQL', NULL),
(100, 29, 445, 'SMB', NULL),
(101, 29, 80, 'HTTP', NULL),
(102, 29, 443, 'HTTPS', NULL),
(103, 29, 8080, 'HTTP-Proxy', NULL),
(104, 30, 53, 'DNS', NULL),
(105, 30, 443, 'HTTPS', NULL),
(106, 30, 80, 'HTTP', NULL),
(107, 31, 3306, 'MySQL', NULL),
(108, 31, 443, 'HTTPS', NULL),
(109, 31, 445, 'SMB', NULL),
(110, 31, 80, 'HTTP', NULL),
(111, 31, 8080, 'HTTP-Proxy', NULL),
(112, 32, 80, 'HTTP', NULL),
(113, 32, 53, 'DNS', NULL),
(114, 32, 443, 'HTTPS', NULL),
(115, 33, 3306, 'MySQL', NULL),
(116, 33, 80, 'HTTP', NULL),
(117, 33, 445, 'SMB', NULL),
(118, 33, 443, 'HTTPS', NULL),
(119, 33, 8080, 'HTTP-Proxy', NULL),
(120, 34, 80, 'HTTP', NULL),
(121, 34, 53, 'DNS', NULL),
(122, 34, 443, 'HTTPS', NULL),
(123, 35, 3306, 'MySQL', 'X\0\0\0jHost \'AndresBrown.www.tendawifi.com\' is not allowed to connect to this MariaDB server'),
(124, 35, 443, 'HTTPS', NULL),
(125, 35, 80, 'HTTP', NULL),
(126, 35, 445, 'SMB', NULL),
(127, 35, 8080, 'HTTP-Proxy', NULL),
(128, 36, 80, 'HTTP', NULL),
(129, 36, 53, 'DNS', NULL),
(130, 36, 443, 'HTTPS', NULL),
(131, 37, 3306, 'MySQL', NULL),
(132, 37, 80, 'HTTP', NULL),
(133, 37, 445, 'SMB', NULL),
(134, 37, 443, 'HTTPS', NULL),
(135, 37, 8080, 'HTTP-Proxy', NULL),
(136, 38, 443, 'HTTPS', NULL),
(137, 38, 80, 'HTTP', NULL),
(138, 38, 53, 'DNS', NULL),
(139, 39, 3306, 'MySQL', NULL),
(140, 39, 80, 'HTTP', NULL),
(141, 39, 445, 'SMB', NULL),
(142, 39, 443, 'HTTPS', NULL),
(143, 39, 8080, 'HTTP-Proxy', NULL),
(144, 40, 443, 'HTTPS', NULL),
(145, 40, 53, 'DNS', NULL),
(146, 40, 80, 'HTTP', NULL),
(147, 41, 3306, 'MySQL', 'X\0\0\0jHost \'AndresBrown.www.tendawifi.com\' is not allowed to connect to this MariaDB server'),
(148, 41, 80, 'HTTP', NULL),
(149, 41, 445, 'SMB', NULL),
(150, 41, 443, 'HTTPS', NULL),
(151, 41, 8080, 'HTTP-Proxy', NULL),
(152, 42, 53, 'DNS', NULL),
(153, 42, 80, 'HTTP', NULL),
(154, 42, 443, 'HTTPS', NULL),
(155, 43, 3306, 'MySQL', 'X\0\0\0jHost \'AndresBrown.www.tendawifi.com\' is not allowed to connect to this MariaDB server'),
(156, 43, 443, 'HTTPS', NULL),
(157, 43, 80, 'HTTP', NULL),
(158, 43, 445, 'SMB', NULL),
(159, 43, 8080, 'HTTP-Proxy', NULL),
(160, 44, 443, 'HTTPS', NULL),
(161, 44, 80, 'HTTP', NULL),
(162, 44, 53, 'DNS', NULL),
(163, 45, 3306, 'MySQL', 'X\0\0\0jHost \'AndresBrown.www.tendawifi.com\' is not allowed to connect to this MariaDB server'),
(164, 45, 80, 'HTTP', NULL),
(165, 45, 443, 'HTTPS', NULL),
(166, 45, 445, 'SMB', NULL),
(167, 45, 8080, 'HTTP-Proxy', NULL),
(168, 46, 53, 'DNS', NULL),
(169, 47, 3306, 'MySQL', NULL),
(170, 47, 443, 'HTTPS', NULL),
(171, 47, 80, 'HTTP', NULL),
(172, 47, 445, 'SMB', NULL),
(173, 47, 8080, 'HTTP-Proxy', NULL),
(174, 48, 443, 'HTTPS', NULL),
(175, 48, 80, 'HTTP', NULL),
(176, 48, 53, 'DNS', NULL),
(177, 49, 3306, 'MySQL', NULL),
(178, 49, 443, 'HTTPS', NULL),
(179, 49, 80, 'HTTP', NULL),
(180, 49, 445, 'SMB', NULL),
(181, 49, 8080, 'HTTP-Proxy', NULL),
(182, 50, 80, 'HTTP', NULL),
(183, 51, 80, 'HTTP', NULL),
(184, 51, 53, 'DNS', NULL),
(185, 51, 443, 'HTTPS', NULL),
(186, 52, 3306, 'MySQL', 'X\0\0\0jHost \'AndresBrown.www.tendawifi.com\' is not allowed to connect to this MariaDB server'),
(187, 52, 80, 'HTTP', NULL),
(188, 52, 443, 'HTTPS', NULL),
(189, 52, 445, 'SMB', NULL),
(190, 52, 8080, 'HTTP-Proxy', NULL),
(191, 53, 53, 'DNS', NULL),
(192, 54, 3306, 'MySQL', 'F\0\0\0jHost \'AndresBrown\' is not allowed to connect to this MariaDB server'),
(193, 54, 80, 'HTTP', NULL),
(194, 54, 445, 'SMB', NULL),
(195, 54, 8080, 'HTTP-Proxy', NULL),
(196, 54, 443, 'HTTPS', NULL),
(982, 212, 53, 'DNS', NULL),
(983, 212, 443, 'HTTPS', NULL),
(984, 212, 80, 'HTTP', NULL),
(985, 213, 3306, 'MySQL', 'X\0\0\0jHost \'AndresBrown.www.tendawifi.com\' is not allowed to connect to this MariaDB server'),
(986, 213, 443, 'HTTPS', NULL),
(987, 213, 80, 'HTTP', NULL),
(988, 213, 445, 'SMB', NULL),
(989, 213, 8080, 'HTTP-Proxy', NULL),
(990, 214, 80, 'HTTP', NULL),
(991, 214, 53, 'DNS', NULL),
(992, 214, 443, 'HTTPS', NULL),
(993, 215, 3306, 'MySQL', 'X\0\0\0jHost \'AndresBrown.www.tendawifi.com\' is not allowed to connect to this MariaDB server'),
(994, 215, 445, 'SMB', NULL),
(995, 215, 80, 'HTTP', NULL),
(996, 215, 443, 'HTTPS', NULL),
(997, 215, 8080, 'HTTP-Proxy', NULL),
(998, 216, 53, 'DNS', NULL),
(999, 216, 443, 'HTTPS', NULL),
(1000, 216, 80, 'HTTP', NULL),
(1001, 217, 3306, 'MySQL', 'X\0\0\0jHost \'AndresBrown.www.tendawifi.com\' is not allowed to connect to this MariaDB server'),
(1002, 217, 445, 'SMB', NULL),
(1003, 217, 443, 'HTTPS', NULL),
(1004, 217, 80, 'HTTP', NULL),
(1005, 217, 8080, 'HTTP-Proxy', NULL),
(1006, 218, 443, 'HTTPS', NULL),
(1007, 218, 53, 'DNS', NULL),
(1008, 218, 80, 'HTTP', NULL),
(1009, 219, 3306, 'MySQL', 'X\0\0\0jHost \'AndresBrown.www.tendawifi.com\' is not allowed to connect to this MariaDB server'),
(1010, 219, 80, 'HTTP', NULL),
(1011, 219, 443, 'HTTPS', NULL),
(1012, 219, 8080, 'HTTP-Proxy', NULL),
(1013, 219, 445, 'SMB', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `scans`
--

CREATE TABLE `scans` (
  `scan_id` int(11) NOT NULL,
  `scan_date` datetime DEFAULT current_timestamp(),
  `scan_duration` float DEFAULT NULL,
  `total_devices` int(11) DEFAULT NULL,
  `total_vulnerabilities` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `scans`
--

INSERT INTO `scans` (`scan_id`, `scan_date`, `scan_duration`, `total_devices`, `total_vulnerabilities`) VALUES
(4, '2025-05-14 23:39:54', 13.0899, 2, 2),
(5, '2025-05-15 00:34:22', 10.0389, 2, 4),
(6, '2025-05-15 00:58:56', 10.4396, 2, 3),
(7, '2025-05-15 18:51:50', 21.621, 2, 3),
(8, '2025-05-15 18:52:21', 20.3178, 2, 3),
(9, '2025-05-15 18:54:15', 20.5663, 2, 3),
(10, '2025-05-15 18:55:38', 23.7718, 2, 3),
(12, '2025-05-15 19:04:25', 20.0346, 2, 3),
(13, '2025-05-15 19:05:34', 21.0424, 2, 3),
(14, '2025-05-15 19:06:20', 20.3467, 2, 3),
(15, '2025-05-15 19:11:56', 20.148, 2, 3),
(16, '2025-05-15 19:12:28', 20.1422, 2, 3),
(17, '2025-05-15 19:17:18', 22.5508, 2, 3),
(18, '2025-05-15 19:28:51', 21.0437, 2, 3),
(19, '2025-05-15 19:43:13', 20.5336, 2, 3),
(20, '2025-05-15 19:44:52', 20.8894, 2, 3),
(21, '2025-05-16 18:28:41', 21.3057, 2, 3),
(22, '2025-05-16 19:13:24', 10.0552, 2, 4),
(23, '2025-05-16 21:17:42', 22.2002, 3, 3),
(24, '2025-05-17 21:41:30', 21.7079, 2, 3),
(25, '2025-05-17 21:43:21', 10.9457, 2, 3),
(26, '2025-05-17 21:44:15', 3.60358, 0, 1),
(28, '2025-05-17 21:58:43', 21.5462, 2, 3),
(29, '2025-05-21 13:46:17', 20.9624, 2, 3),
(30, '2025-05-21 19:11:22', 21.4186, 2, 3),
(31, '2025-05-23 12:39:03', 20.9951, 2, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` datetime DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1 COMMENT '1=activo, 0=inactivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `role`, `created_at`, `last_login`, `status`) VALUES
(1, 'Administrador', 'admin@example.com', '$2y$10$Z4Uc3SmzLlqwxA3iibQbj.S0nSMYXTCUJH/vNq6q.YDV5rECRzzXi', 'admin', '2025-05-14 23:50:20', NULL, 1),
(2, 'Andres', 'andres@gmail.com', '$2y$10$OccBnROCARxq5y3f/JOrpOGJxyJJGWYVt8OMMF0vnQye.em5Xm852', 'user', '2025-05-14 23:52:02', '2025-05-23 12:34:26', 1),
(3, 'test', 'test@gmail.com', '$2y$10$ByAfaGEBejx8O40EhIOXLegv9W3rdD2A86rQYis4/5D.COfBQzCUi', 'user', '2025-05-21 19:33:03', NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_sessions`
--

CREATE TABLE `user_sessions` (
  `session_id` varchar(128) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_time` datetime DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vulnerabilities`
--

CREATE TABLE `vulnerabilities` (
  `vuln_id` int(11) NOT NULL,
  `scan_id` int(11) DEFAULT NULL,
  `vuln_type` varchar(50) DEFAULT NULL,
  `severity` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `affected_device` varchar(15) DEFAULT NULL,
  `details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vulnerabilities`
--

INSERT INTO `vulnerabilities` (`vuln_id`, `scan_id`, `vuln_type`, `severity`, `description`, `affected_device`, `details`) VALUES
(10, 4, 'authentication', 'medium', 'Método de autenticación no óptimo', 'Unknown', NULL),
(11, 4, 'open_port', 'high', 'Puerto potencialmente peligroso abierto: 445 (SMB)', '192.168.0.190', NULL),
(12, 5, 'authentication', 'high', 'Método de autenticación no óptimo', 'Unknown', NULL),
(13, 5, 'encryption', 'high', 'Método de cifrado débil', 'Unknown', NULL),
(14, 5, 'password', 'high', 'Contraseña débil', 'Unknown', 'Longitud menor a 12 caracteres'),
(15, 5, 'open_port', 'high', 'Puerto potencialmente peligroso abierto: 445 (SMB)', '192.168.42.241', NULL),
(16, 6, 'authentication', 'medium', 'Método de autenticación no óptimo', 'Unknown', NULL),
(17, 6, 'password', 'high', 'Contraseña débil', 'Unknown', 'Longitud menor a 12 caracteres'),
(18, 6, 'open_port', 'high', 'Puerto potencialmente peligroso abierto: 445 (SMB)', '192.168.20.133', NULL),
(19, 7, 'authentication', 'medium', 'Método de autenticación no óptimo', 'Unknown', NULL),
(20, 7, 'password', 'high', 'Contraseña débil', 'Unknown', 'Longitud menor a 12 caracteres'),
(21, 7, 'open_port', 'high', 'Puerto potencialmente peligroso abierto: 445 (SMB)', '192.168.20.133', NULL),
(22, 8, 'authentication', 'medium', 'Método de autenticación no óptimo', 'Unknown', NULL),
(23, 8, 'password', 'high', 'Contraseña débil', 'Unknown', 'Longitud menor a 12 caracteres'),
(24, 8, 'open_port', 'high', 'Puerto potencialmente peligroso abierto: 445 (SMB)', '192.168.20.133', NULL),
(25, 9, 'authentication', 'medium', 'Método de autenticación no óptimo', 'Unknown', NULL),
(26, 9, 'password', 'high', 'Contraseña débil', 'Unknown', 'Longitud menor a 12 caracteres'),
(27, 9, 'open_port', 'high', 'Puerto potencialmente peligroso abierto: 445 (SMB)', '192.168.20.133', NULL),
(28, 10, 'authentication', 'medium', 'Método de autenticación no óptimo', 'Unknown', NULL),
(29, 10, 'password', 'high', 'Contraseña débil', 'Unknown', 'Longitud menor a 12 caracteres'),
(30, 10, 'open_port', 'high', 'Puerto potencialmente peligroso abierto: 445 (SMB)', '192.168.20.133', NULL),
(34, 12, 'authentication', 'medium', 'Método de autenticación no óptimo', 'Unknown', NULL),
(35, 12, 'password', 'high', 'Contraseña débil', 'Unknown', 'Longitud menor a 12 caracteres'),
(36, 12, 'open_port', 'high', 'Puerto potencialmente peligroso abierto: 445 (SMB)', '192.168.20.133', NULL),
(37, 13, 'authentication', 'medium', 'Método de autenticación no óptimo', 'Unknown', NULL),
(38, 13, 'password', 'high', 'Contraseña débil', 'Unknown', 'Longitud menor a 12 caracteres'),
(39, 13, 'open_port', 'high', 'Puerto potencialmente peligroso abierto: 445 (SMB)', '192.168.20.133', NULL),
(40, 14, 'authentication', 'medium', 'Método de autenticación no óptimo', 'Unknown', NULL),
(41, 14, 'password', 'high', 'Contraseña débil', 'Unknown', 'Longitud menor a 12 caracteres'),
(42, 14, 'open_port', 'high', 'Puerto potencialmente peligroso abierto: 445 (SMB)', '192.168.20.133', NULL),
(43, 15, 'authentication', 'medium', 'Método de autenticación no óptimo', 'Unknown', NULL),
(44, 15, 'password', 'high', 'Contraseña débil', 'Unknown', 'Longitud menor a 12 caracteres'),
(45, 15, 'open_port', 'high', 'Puerto potencialmente peligroso abierto: 445 (SMB)', '192.168.20.133', NULL),
(46, 16, 'authentication', 'medium', 'Método de autenticación no óptimo', 'Unknown', NULL),
(47, 16, 'password', 'high', 'Contraseña débil', 'Unknown', 'Longitud menor a 12 caracteres'),
(48, 16, 'open_port', 'high', 'Puerto potencialmente peligroso abierto: 445 (SMB)', '192.168.20.133', NULL),
(49, 17, 'authentication', 'medium', 'Método de autenticación no óptimo', 'Unknown', NULL),
(50, 17, 'password', 'high', 'Contraseña débil', 'Unknown', 'Longitud menor a 12 caracteres'),
(51, 17, 'open_port', 'high', 'Puerto potencialmente peligroso abierto: 445 (SMB)', '192.168.20.133', NULL),
(52, 18, 'authentication', 'medium', 'Método de autenticación no óptimo', 'Unknown', NULL),
(53, 18, 'password', 'high', 'Contraseña débil', 'Unknown', 'Longitud menor a 12 caracteres'),
(54, 18, 'open_port', 'high', 'Puerto potencialmente peligroso abierto: 445 (SMB)', '192.168.20.133', NULL),
(55, 19, 'authentication', 'medium', 'Método de autenticación no óptimo', 'Unknown', NULL),
(56, 19, 'password', 'high', 'Contraseña débil', 'Unknown', 'Longitud menor a 12 caracteres'),
(57, 19, 'open_port', 'high', 'Puerto potencialmente peligroso abierto: 445 (SMB)', '192.168.20.133', NULL),
(58, 20, 'authentication', 'medium', 'Método de autenticación no óptimo', 'Unknown', NULL),
(59, 20, 'password', 'high', 'Contraseña débil', 'Unknown', 'Longitud menor a 12 caracteres'),
(60, 20, 'open_port', 'high', 'Puerto potencialmente peligroso abierto: 445 (SMB)', '192.168.20.133', NULL),
(61, 21, 'authentication', 'medium', 'Método de autenticación no óptimo', 'Unknown', NULL),
(62, 21, 'password', 'high', 'Contraseña débil', 'Unknown', 'Longitud menor a 12 caracteres'),
(63, 21, 'open_port', 'high', 'Puerto potencialmente peligroso abierto: 445 (SMB)', '192.168.20.133', NULL),
(64, 22, 'authentication', 'high', 'Método de autenticación no óptimo', 'Unknown', NULL),
(65, 22, 'encryption', 'high', 'Método de cifrado débil', 'Unknown', NULL),
(66, 22, 'password', 'high', 'Contraseña débil', 'Unknown', 'Longitud menor a 12 caracteres'),
(67, 22, 'open_port', 'high', 'Puerto potencialmente peligroso abierto: 445 (SMB)', '192.168.42.241', NULL),
(68, 23, 'authentication', 'medium', 'Método de autenticación no óptimo', 'Unknown', NULL),
(69, 23, 'password', 'high', 'Contraseña débil', 'Unknown', 'Longitud menor a 12 caracteres'),
(70, 23, 'open_port', 'high', 'Puerto potencialmente peligroso abierto: 445 (SMB)', '192.168.20.133', NULL),
(71, 24, 'authentication', 'medium', 'Método de autenticación no óptimo', 'Unknown', NULL),
(72, 24, 'password', 'high', 'Contraseña débil', 'Unknown', 'Longitud menor a 12 caracteres'),
(73, 24, 'open_port', 'high', 'Puerto potencialmente peligroso abierto: 445 (SMB)', '192.168.20.133', NULL),
(74, 25, 'authentication', 'high', 'Método de autenticación no óptimo', 'Unknown', NULL),
(75, 25, 'encryption', 'high', 'Método de cifrado débil', 'Unknown', NULL),
(76, 25, 'open_port', 'high', 'Puerto potencialmente peligroso abierto: 445 (SMB)', '192.168.42.241', NULL),
(77, 26, 'authentication', 'medium', 'Método de autenticación no óptimo', 'Unknown', NULL),
(78, 28, 'authentication', 'medium', 'Método de autenticación no óptimo', 'Unknown', NULL),
(79, 28, 'password', 'high', 'Contraseña débil', 'Unknown', 'Longitud menor a 12 caracteres'),
(80, 28, 'open_port', 'high', 'Puerto potencialmente peligroso abierto: 445 (SMB)', '192.168.20.133', NULL),
(81, 29, 'authentication', 'medium', 'Método de autenticación no óptimo', 'Unknown', NULL),
(82, 29, 'password', 'high', 'Contraseña débil', 'Unknown', 'Longitud menor a 12 caracteres'),
(83, 29, 'open_port', 'high', 'Puerto potencialmente peligroso abierto: 445 (SMB)', '192.168.20.133', NULL),
(84, 30, 'authentication', 'medium', 'Método de autenticación no óptimo', 'Unknown', NULL),
(85, 30, 'password', 'high', 'Contraseña débil', 'Unknown', 'Longitud menor a 12 caracteres'),
(86, 30, 'open_port', 'high', 'Puerto potencialmente peligroso abierto: 445 (SMB)', '192.168.20.133', NULL),
(87, 31, 'authentication', 'medium', 'Método de autenticación no óptimo', 'Unknown', NULL),
(88, 31, 'password', 'high', 'Contraseña débil', 'Unknown', 'Longitud menor a 12 caracteres'),
(89, 31, 'open_port', 'high', 'Puerto potencialmente peligroso abierto: 445 (SMB)', '192.168.20.133', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wifi_networks`
--

CREATE TABLE `wifi_networks` (
  `network_id` int(11) NOT NULL,
  `scan_id` int(11) DEFAULT NULL,
  `ssid` varchar(100) DEFAULT NULL,
  `authentication` varchar(50) DEFAULT NULL,
  `encryption` varchar(50) DEFAULT NULL,
  `signal` varchar(20) DEFAULT NULL,
  `security_key` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `wifi_networks`
--

INSERT INTO `wifi_networks` (`network_id`, `scan_id`, `ssid`, `authentication`, `encryption`, `signal`, `security_key`) VALUES
(4, 4, 'Vivetel@DN', 'WPA2-Personal', 'CCMP', '46%', 'LupeMaria2711'),
(5, 5, 'Andres Brown', 'Abierta', 'Ninguna', '99%', '12345678'),
(6, 6, 'Vivetel@P1P3', 'WPA2-Personal', 'CCMP', '81%', 'BolanosP'),
(7, 7, 'Vivetel@P1P3', 'WPA2-Personal', 'CCMP', '83%', 'BolanosP'),
(8, 8, 'Vivetel@P1P3', 'WPA2-Personal', 'CCMP', '89%', 'BolanosP'),
(9, 9, 'Vivetel@P1P3', 'WPA2-Personal', 'CCMP', '82%', 'BolanosP'),
(10, 10, 'Vivetel@P1P3', 'WPA2-Personal', 'CCMP', '86%', 'BolanosP'),
(12, 12, 'Vivetel@P1P3', 'WPA2-Personal', 'CCMP', '85%', 'BolanosP'),
(13, 13, 'Vivetel@P1P3', 'WPA2-Personal', 'CCMP', '85%', 'BolanosP'),
(14, 14, 'Vivetel@P1P3', 'WPA2-Personal', 'CCMP', '86%', 'BolanosP'),
(15, 15, 'Vivetel@P1P3', 'WPA2-Personal', 'CCMP', '87%', 'BolanosP'),
(16, 16, 'Vivetel@P1P3', 'WPA2-Personal', 'CCMP', '84%', 'BolanosP'),
(17, 17, 'Vivetel@P1P3', 'WPA2-Personal', 'CCMP', '86%', 'BolanosP'),
(18, 18, 'Vivetel@P1P3', 'WPA2-Personal', 'CCMP', '85%', 'BolanosP'),
(19, 19, 'Vivetel@P1P3', 'WPA2-Personal', 'CCMP', '85%', 'BolanosP'),
(20, 20, 'Vivetel@P1P3', 'WPA2-Personal', 'CCMP', '87%', 'BolanosP'),
(21, 21, 'Vivetel@P1P3', 'WPA2-Personal', 'CCMP', '85%', 'BolanosP'),
(22, 22, 'Andres Brown', 'Abierta', 'Ninguna', '94%', '12345678'),
(23, 23, 'Vivetel@P1P3', 'WPA2-Personal', 'CCMP', '82%', 'BolanosP'),
(24, 24, 'Vivetel@P1P3', 'WPA2-Personal', 'CCMP', '82%', 'BolanosP'),
(25, 25, 'Andres Brown', 'Abierta', 'Ninguna', '95%', 'Unknown'),
(26, 26, 'Vivetel@DN', 'WPA2-Personal', 'CCMP', '28%', 'LupeMaria2711'),
(28, 28, 'Vivetel@P1P3', 'WPA2-Personal', 'CCMP', '83%', 'BolanosP'),
(29, 29, 'Vivetel@P1P3', 'WPA2-Personal', 'CCMP', '81%', 'BolanosP'),
(30, 30, 'Vivetel@P1P3', 'WPA2-Personal', 'CCMP', '83%', 'BolanosP'),
(31, 31, 'Vivetel@P1P3', 'WPA2-Personal', 'CCMP', '82%', 'BolanosP');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `ai_recommendations`
--
ALTER TABLE `ai_recommendations`
  ADD PRIMARY KEY (`rec_id`),
  ADD KEY `scan_id` (`scan_id`);

--
-- Indices de la tabla `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`device_id`),
  ADD KEY `scan_id` (`scan_id`);

--
-- Indices de la tabla `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`attempt_id`);

--
-- Indices de la tabla `open_ports`
--
ALTER TABLE `open_ports`
  ADD PRIMARY KEY (`port_id`),
  ADD KEY `device_id` (`device_id`);

--
-- Indices de la tabla `scans`
--
ALTER TABLE `scans`
  ADD PRIMARY KEY (`scan_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_user_email` (`email`),
  ADD KEY `idx_user_status` (`status`);

--
-- Indices de la tabla `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `vulnerabilities`
--
ALTER TABLE `vulnerabilities`
  ADD PRIMARY KEY (`vuln_id`),
  ADD KEY `scan_id` (`scan_id`);

--
-- Indices de la tabla `wifi_networks`
--
ALTER TABLE `wifi_networks`
  ADD PRIMARY KEY (`network_id`),
  ADD KEY `scan_id` (`scan_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `ai_recommendations`
--
ALTER TABLE `ai_recommendations`
  MODIFY `rec_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `devices`
--
ALTER TABLE `devices`
  MODIFY `device_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=220;

--
-- AUTO_INCREMENT de la tabla `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `attempt_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `open_ports`
--
ALTER TABLE `open_ports`
  MODIFY `port_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1014;

--
-- AUTO_INCREMENT de la tabla `scans`
--
ALTER TABLE `scans`
  MODIFY `scan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `vulnerabilities`
--
ALTER TABLE `vulnerabilities`
  MODIFY `vuln_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT de la tabla `wifi_networks`
--
ALTER TABLE `wifi_networks`
  MODIFY `network_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ai_recommendations`
--
ALTER TABLE `ai_recommendations`
  ADD CONSTRAINT `ai_recommendations_ibfk_1` FOREIGN KEY (`scan_id`) REFERENCES `scans` (`scan_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `devices`
--
ALTER TABLE `devices`
  ADD CONSTRAINT `devices_ibfk_1` FOREIGN KEY (`scan_id`) REFERENCES `scans` (`scan_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `open_ports`
--
ALTER TABLE `open_ports`
  ADD CONSTRAINT `open_ports_ibfk_1` FOREIGN KEY (`device_id`) REFERENCES `devices` (`device_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `vulnerabilities`
--
ALTER TABLE `vulnerabilities`
  ADD CONSTRAINT `vulnerabilities_ibfk_1` FOREIGN KEY (`scan_id`) REFERENCES `scans` (`scan_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `wifi_networks`
--
ALTER TABLE `wifi_networks`
  ADD CONSTRAINT `wifi_networks_ibfk_1` FOREIGN KEY (`scan_id`) REFERENCES `scans` (`scan_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
