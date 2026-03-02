-- MySQL ya crea la BD 'chat' mediante la variable MYSQL_DATABASE.
-- Aquí solo creamos las tablas necesarias.
USE chat;

CREATE TABLE IF NOT EXISTS `user` (
  `cod`      INT(10)      NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(30)  NOT NULL,
  `password` VARCHAR(80)  NOT NULL,
  `img`      VARCHAR(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
