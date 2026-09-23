/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.20-11.8.9-MariaDB, for debian-linux-gnu (aarch64)
--
-- Host: localhost    Database: eventos_db
-- ------------------------------------------------------
-- Server version	11.8.9-MariaDB-ubu2404

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `eventos`
--

DROP TABLE IF EXISTS `eventos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `eventos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo_id` int(10) unsigned NOT NULL,
  `fecha_evento` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `lugar` varchar(100) NOT NULL,
  `cupo` int(10) unsigned NOT NULL,
  `inscritos` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_eventos_fecha` (`fecha_evento`),
  KEY `idx_eventos_tipo` (`tipo_id`),
  CONSTRAINT `fk_eventos_tipo` FOREIGN KEY (`tipo_id`) REFERENCES `tipos_evento` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `chk_eventos_cupo` CHECK (`cupo` between 1 and 300),
  CONSTRAINT `chk_eventos_inscritos` CHECK (`inscritos` <= `cupo`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `eventos`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `eventos` WRITE;
/*!40000 ALTER TABLE `eventos` DISABLE KEYS */;
INSERT INTO `eventos` VALUES
(1,'Conferencia de innovación educativa','Tendencias tecnológicas aplicadas a la educación superior.',1,'2026-10-15','09:00:00','Auditorio Principal',150,82),
(2,'Taller de desarrollo web','Práctica guiada de PHP, bases de datos y APIs.',2,'2026-11-05','14:00:00','Laboratorio 2',35,30),
(3,'Seminario de investigación','Buenas prácticas para formular proyectos de investigación.',3,'2026-12-02','10:00:00','Sala de Conferencias',80,44),
(4,'Hackatón de soluciones sociales','Jornada colaborativa para crear prototipos con impacto local.',4,'2026-10-24','08:00:00','Centro de Innovación',100,97),
(5,'Conferencia de ciclo anterior','Evento histórico conservado para probar restricciones de eliminación.',1,'2026-08-20','16:00:00','Auditorio Principal',120,120);
/*!40000 ALTER TABLE `eventos` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `tipos_evento`
--

DROP TABLE IF EXISTS `tipos_evento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_evento` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(60) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_evento`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `tipos_evento` WRITE;
/*!40000 ALTER TABLE `tipos_evento` DISABLE KEYS */;
INSERT INTO `tipos_evento` VALUES
(1,'Conferencia'),
(4,'Hackatón'),
(3,'Seminario'),
(2,'Taller');
/*!40000 ALTER TABLE `tipos_evento` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-09-23  1:00:59
