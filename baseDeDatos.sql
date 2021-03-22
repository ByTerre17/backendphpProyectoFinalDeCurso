-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-03-2021 a las 20:51:56
-- Versión del servidor: 10.4.17-MariaDB
-- Versión de PHP: 8.0.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `proyectophp`
--
CREATE DATABASE IF NOT EXISTS `proyectophp` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `proyectophp`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentario`
--

CREATE TABLE `comentario` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `texto` varchar(255) NOT NULL,
  `nota` int(11) NOT NULL,
  `idJuego` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `comentario`
--

INSERT INTO `comentario` (`id`, `titulo`, `texto`, `nota`, `idJuego`, `idUsuario`) VALUES
(2, 'Prueba', 'Tremendisimo', 10, 1, 1),
(5, 'Maravilloso ', 'El mejejor juego que existira jamas me ha encantado.', 9, 3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `juego`
--

CREATE TABLE `juego` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `fechaDeLanzamiento` varchar(255) NOT NULL,
  `comprar` varchar(255) NOT NULL,
  `edad` varchar(255) NOT NULL,
  `creador` varchar(255) NOT NULL,
  `genero` varchar(255) NOT NULL,
  `numeroDeJugadores` varchar(255) NOT NULL,
  `fechaDePublicacion` date NOT NULL DEFAULT current_timestamp(),
  `imagen` varchar(255) NOT NULL,
  `nota` int(11) NOT NULL,
  `resumen` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `juego`
--

INSERT INTO `juego` (`id`, `nombre`, `fechaDeLanzamiento`, `comprar`, `edad`, `creador`, `genero`, `numeroDeJugadores`, `fechaDePublicacion`, `imagen`, `nota`, `resumen`) VALUES
(1, 'Tetris', '20/01/2000', 'dfsdfsdf', '1', 'dsdsfsf', 'sdfsddfsdf', '1', '2021-03-13', 'tetris', 8, 'asdsadsadasda'),
(3, 'PACMAN', '22 de mayo de 1980', 'No a la venta', '+3', 'Toru Iwatani', 'Arcade', '1', '2021-03-16', 'foto', 9, 'Pac Man es un videojuego arcade creado por el diseñador de videojuegos Toru Iwatani de la empresa Namco, y distribuido por Midway Games al mercado estadounidense a principios de los años 1980. Desde que Pac-Man fue lanzado el 21 de mayo de 1980, fue un éx'),
(4, 'Buscaminas', '1980', 'Gratis', '+3', 'Microsoft', 'Puzzle', '1', '2021-03-16', 'Imagen', 7, 'Buscaminas es un videojuego para un jugador inventado por Robert Donner en 1989. El objetivo del juego es despejar un campo de minas sin detonar ninguna. El juego ha sido programado para muchos sistemas operativos, pero debe su popularidad a las versiones que vienen con Microsoft Windows desde su versión 3.1.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `foto` varchar(255) NOT NULL,
  `usuario` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `email`, `foto`, `usuario`, `password`, `rol`) VALUES
(2, 'manuelpalomino080@gmail.com', 'no_foto', 'Juan12343', '$2y$10$oqluhgCBzyWgiGnrVvYe4uCFah4o1D/09MOenrZFyYr799Z/kaPNW', 'user'),
(3, 'manuelpalomino484@gmail.com', 'no_foto', 'admin', '$2y$10$LDOfAkH9USf7T8eM0Imlre7J1w3Dco5k1r3K.RRwGjIhUW4aIyKH2', 'user'),
(6, 'manuelpalomino4884@gmail.com', 'no_foto', 'Manuel1234', '$2y$10$HCd4Ixng6Q4TSqifHgJMGe6Pvc1lYrs8UqBSFEQ52lEE.eu/pYeKS', 'user');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comentario`
--
ALTER TABLE `comentario`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `juego`
--
ALTER TABLE `juego`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `comentario`
--
ALTER TABLE `comentario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `juego`
--
ALTER TABLE `juego`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
