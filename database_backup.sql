-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: assidcoff_inventory
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=263 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (1,NULL,'login_failed','Failed login attempt for user: admin','2024-11-22 21:25:02'),(2,NULL,'login_failed','Failed login attempt for user: admin','2024-11-23 20:07:26'),(3,NULL,'login_failed','Failed login attempt for user: admin','2024-11-23 20:16:17'),(4,NULL,'login_failed','Failed login attempt for user: admin','2024-11-23 20:17:37'),(5,1,'login','Successful login','2024-11-23 20:39:51'),(6,1,'dashboard_access','User accessed dashboard','2024-11-23 20:39:51'),(7,1,'dashboard_access','User accessed dashboard','2024-11-23 20:45:01'),(8,1,'dashboard_access','User accessed dashboard','2024-11-23 20:46:59'),(9,NULL,'login_failed','Failed login attempt for user: admin','2024-11-23 20:51:59'),(10,1,'login','Successful login','2024-11-23 20:52:21'),(11,1,'dashboard_access','User accessed dashboard','2024-11-23 20:52:21'),(12,1,'dashboard_access','User accessed dashboard','2024-11-23 20:52:30'),(13,1,'dashboard_access','User accessed dashboard','2024-11-23 20:54:09'),(14,1,'dashboard_access','User accessed dashboard','2024-11-23 20:56:21'),(15,1,'dashboard_access','User accessed dashboard','2024-11-23 20:56:22'),(16,1,'dashboard_access','User accessed dashboard','2024-11-23 20:56:23'),(17,1,'dashboard_access','User accessed dashboard','2024-11-23 20:56:23'),(18,1,'dashboard_access','User accessed dashboard','2024-11-23 20:56:24'),(19,1,'dashboard_access','User accessed dashboard','2024-11-23 21:09:58'),(20,1,'dashboard_access','User accessed dashboard','2024-11-23 21:10:33'),(21,1,'dashboard_access','User accessed dashboard','2024-11-23 21:14:52'),(22,1,'dashboard_access','User accessed dashboard','2024-11-23 21:15:10'),(23,1,'dashboard_access','User accessed dashboard','2024-11-23 21:17:57'),(24,1,'dashboard_access','User accessed dashboard','2024-11-23 21:20:13'),(25,1,'dashboard_access','User accessed dashboard','2024-11-23 21:26:53'),(26,1,'dashboard_access','User accessed dashboard','2024-11-23 21:28:36'),(27,1,'dashboard_access','User accessed dashboard','2024-11-23 21:28:44'),(28,1,'dashboard_access','User accessed dashboard','2024-11-23 21:28:47'),(29,1,'dashboard_access','User accessed dashboard','2024-11-23 21:28:50'),(30,1,'dashboard_access','User accessed dashboard','2024-11-23 21:45:33'),(31,1,'dashboard_access','User accessed dashboard','2024-11-23 21:45:54'),(32,1,'dashboard_access','User accessed dashboard','2024-11-23 21:50:23'),(33,1,'dashboard_access','User accessed dashboard','2024-11-23 21:51:51'),(34,1,'dashboard_access','User accessed dashboard','2024-11-23 21:53:05'),(35,1,'dashboard_access','User accessed dashboard','2024-11-23 21:53:34'),(36,1,'dashboard_access','User accessed dashboard','2024-11-23 21:54:03'),(37,1,'dashboard_access','User accessed dashboard','2024-11-23 21:55:39'),(38,1,'dashboard_access','User accessed dashboard','2024-11-23 22:26:49'),(39,1,'dashboard_access','User accessed dashboard','2024-11-24 18:16:17'),(40,1,'login','Successful login','2024-11-25 07:11:14'),(41,1,'dashboard_access','User accessed dashboard','2024-11-25 07:11:14'),(42,1,'dashboard_access','User accessed dashboard','2024-11-25 07:17:38'),(43,1,'login','Successful login','2024-11-25 07:18:17'),(44,1,'dashboard_access','User accessed dashboard','2024-11-25 07:18:17'),(45,1,'dashboard_access','User accessed dashboard','2024-11-25 07:23:05'),(46,1,'dashboard_access','User accessed dashboard','2024-11-25 07:23:09'),(47,1,'dashboard_access','User accessed dashboard','2024-11-25 07:23:14'),(48,1,'dashboard_access','User accessed dashboard','2024-11-25 07:23:16'),(49,1,'dashboard_access','User accessed dashboard','2024-11-25 07:24:22'),(50,1,'dashboard_access','User accessed dashboard','2024-11-25 08:25:18'),(51,1,'login','Successful login','2024-11-25 08:25:43'),(52,1,'dashboard_access','User accessed dashboard','2024-11-25 08:25:43'),(53,1,'dashboard_access','User accessed dashboard','2024-11-25 08:28:56'),(54,1,'dashboard_access','User accessed dashboard','2024-11-25 08:32:00'),(55,1,'dashboard_access','User accessed dashboard','2024-11-25 08:32:35'),(56,1,'dashboard_access','User accessed dashboard','2024-11-25 08:34:58'),(57,1,'dashboard_access','User accessed dashboard','2024-11-25 08:44:41'),(58,1,'dashboard_access','User accessed dashboard','2024-11-25 08:47:01'),(59,1,'dashboard_access','User accessed dashboard','2024-11-25 19:13:32'),(60,1,'login','Successful login','2024-11-25 19:21:26'),(61,1,'dashboard_access','User accessed dashboard','2024-11-25 19:21:26'),(62,1,'dashboard_access','User accessed dashboard','2024-11-25 19:21:30'),(63,1,'dashboard_access','User accessed dashboard','2024-11-25 19:22:31'),(64,1,'dashboard_access','User accessed dashboard','2024-11-25 19:27:27'),(65,1,'dashboard_access','User accessed dashboard','2024-11-25 19:35:18'),(66,1,'login','Successful login','2024-11-26 07:48:56'),(67,1,'dashboard_access','User accessed dashboard','2024-11-26 07:48:56'),(68,1,'dashboard_access','User accessed dashboard','2024-11-26 08:07:16'),(69,1,'login','Successful login','2024-11-26 08:21:13'),(70,1,'dashboard_access','User accessed dashboard','2024-11-26 08:21:13'),(71,1,'login','Successful login','2024-11-26 09:27:09'),(72,1,'dashboard_access','User accessed dashboard','2024-11-26 09:27:09'),(73,1,'login','Successful login','2024-11-26 09:28:55'),(74,1,'dashboard_access','User accessed dashboard','2024-11-26 09:28:55'),(75,1,'login','Successful login','2024-11-28 21:26:20'),(76,1,'dashboard_access','User accessed dashboard','2024-11-28 21:26:20'),(77,1,'dashboard_access','User accessed dashboard','2024-11-28 21:30:44'),(78,1,'dashboard_access','User accessed dashboard','2024-11-28 21:35:24'),(79,1,'dashboard_access','User accessed dashboard','2024-11-28 21:35:42'),(80,1,'dashboard_access','User accessed dashboard','2024-11-28 21:36:16'),(81,1,'dashboard_access','User accessed dashboard','2024-11-28 21:52:33'),(82,1,'dashboard_access','User accessed dashboard','2024-11-28 21:56:08'),(83,1,'dashboard_access','User accessed dashboard','2024-11-28 22:06:15'),(84,1,'dashboard_access','User accessed dashboard','2024-11-28 22:06:35'),(85,1,'dashboard_access','User accessed dashboard','2024-11-28 22:22:11'),(86,1,'dashboard_access','User accessed dashboard','2024-11-28 22:32:32'),(87,1,'login','Successful login','2024-11-29 20:44:35'),(88,1,'dashboard_access','User accessed dashboard','2024-11-29 20:44:35'),(89,1,'login','Successful login','2024-11-29 20:51:50'),(90,1,'dashboard_access','User accessed dashboard','2024-11-29 20:51:50'),(91,1,'dashboard_access','User accessed dashboard','2024-11-29 20:58:33'),(92,1,'dashboard_access','User accessed dashboard','2024-11-29 20:59:45'),(93,1,'logout','User logged out','2024-11-29 21:05:25'),(94,1,'login','Successful login','2024-11-29 21:05:54'),(95,1,'dashboard_access','User accessed dashboard','2024-11-29 21:05:54'),(96,1,'logout','User logged out','2024-11-29 21:07:19'),(97,3,'login','Successful login','2024-11-29 21:07:32'),(98,3,'dashboard_access','User accessed dashboard','2024-11-29 21:07:32'),(99,3,'dashboard_access','User accessed dashboard','2024-11-29 21:08:06'),(100,3,'logout','User logged out','2024-11-29 21:08:46'),(101,1,'login','Successful login','2024-11-29 21:08:50'),(102,1,'dashboard_access','User accessed dashboard','2024-11-29 21:08:50'),(103,1,'logout','User logged out','2024-11-29 21:09:27'),(104,4,'login','Successful login','2024-11-29 21:09:35'),(105,4,'dashboard_access','User accessed dashboard','2024-11-29 21:09:35'),(106,4,'dashboard_access','User accessed dashboard','2024-11-29 21:09:46'),(107,4,'dashboard_access','User accessed dashboard','2024-11-29 21:18:39'),(108,4,'dashboard_access','User accessed dashboard','2024-11-29 21:18:41'),(109,4,'dashboard_access','User accessed dashboard','2024-11-29 21:21:19'),(110,4,'dashboard_access','User accessed dashboard','2024-11-29 21:25:54'),(111,4,'dashboard_access','User accessed dashboard','2024-11-29 21:25:57'),(112,4,'dashboard_access','User accessed dashboard','2024-11-29 21:26:02'),(113,4,'dashboard_access','User accessed dashboard','2024-11-29 21:26:07'),(114,4,'dashboard_access','User accessed dashboard','2024-11-29 21:47:18'),(115,4,'dashboard_access','User accessed dashboard','2024-11-29 21:47:33'),(116,4,'logout','User logged out','2024-11-29 21:47:37'),(117,4,'login','User logged in successfully','2024-11-29 21:47:46'),(118,4,'login','User logged in successfully','2024-11-29 21:49:51'),(119,4,'dashboard_access','User accessed dashboard','2024-11-29 21:49:53'),(120,4,'dashboard_access','User accessed dashboard','2024-11-29 21:49:58'),(121,4,'dashboard_access','User accessed dashboard','2024-11-29 21:49:59'),(122,4,'dashboard_access','User accessed dashboard','2024-11-29 21:50:01'),(123,4,'logout','User logged out','2024-11-29 21:50:41'),(124,4,'login','User logged in successfully','2024-11-29 21:50:49'),(125,4,'dashboard_access','User accessed dashboard','2024-11-29 21:51:48'),(126,4,'dashboard_access','User accessed dashboard','2024-11-29 21:51:55'),(127,4,'dashboard_access','User accessed dashboard','2024-11-29 21:51:56'),(128,4,'logout','User logged out','2024-11-29 21:51:59'),(129,3,'login','User logged in successfully','2024-11-29 21:52:02'),(130,1,'logout','User logged out','2024-11-29 21:52:27'),(131,3,'login','User logged in successfully','2024-11-29 21:52:39'),(132,3,'dashboard_access','User accessed dashboard','2024-11-29 22:01:23'),(133,3,'dashboard_access','User accessed dashboard','2024-11-29 22:01:24'),(134,3,'logout','User logged out','2024-11-29 22:01:30'),(135,3,'login','User logged in successfully','2024-11-29 22:02:47'),(136,3,'dashboard_access','User accessed dashboard','2024-11-29 22:14:25'),(137,3,'dashboard_access','User accessed dashboard','2024-11-29 22:14:27'),(138,3,'dashboard_access','User accessed dashboard','2024-11-29 22:14:27'),(139,3,'logout','User logged out','2024-11-29 22:14:34'),(140,3,'login','Successful login','2024-11-29 22:14:36'),(141,3,'dashboard_access','User accessed dashboard','2024-11-29 22:14:36'),(142,3,'dashboard_access','User accessed dashboard','2024-11-29 22:14:45'),(143,3,'logout','User logged out','2024-11-29 22:14:49'),(144,1,'login','Successful login','2024-11-29 22:14:58'),(145,1,'dashboard_access','User accessed dashboard','2024-11-29 22:14:58'),(146,1,'dashboard_access','User accessed dashboard','2024-11-29 22:20:41'),(147,1,'dashboard_access','User accessed dashboard','2024-11-29 22:20:50'),(148,3,'dashboard_access','User accessed dashboard','2024-11-29 22:20:58'),(149,1,'dashboard_access','User accessed dashboard','2024-11-29 22:22:55'),(150,1,'dashboard_access','User accessed dashboard','2024-11-29 22:23:00'),(151,3,'dashboard_access','User accessed dashboard','2024-11-29 22:23:33'),(152,3,'dashboard_access','User accessed dashboard','2024-11-29 22:25:09'),(153,3,'dashboard_access','User accessed dashboard','2024-11-29 22:25:10'),(154,1,'dashboard_access','User accessed dashboard','2024-11-29 22:25:17'),(155,1,'dashboard_access','User accessed dashboard','2024-11-29 22:29:49'),(156,1,'logout','User logged out','2024-11-29 22:30:19'),(157,3,'dashboard_access','User accessed dashboard','2024-11-29 22:30:29'),(158,3,'logout','User logged out','2024-11-29 22:30:56'),(159,3,'login','Successful login','2024-11-29 22:31:00'),(160,3,'dashboard_access','User accessed dashboard','2024-11-29 22:31:00'),(161,3,'dashboard_access','User accessed dashboard','2024-11-29 22:54:16'),(162,3,'logout','User logged out','2024-11-29 22:54:30'),(163,1,'login','Successful login','2024-11-29 22:54:34'),(164,1,'dashboard_access','User accessed dashboard','2024-11-29 22:54:34'),(165,1,'dashboard_access','User accessed dashboard','2024-11-29 22:55:33'),(166,1,'login','Successful login','2024-12-02 09:34:25'),(167,1,'dashboard_access','User accessed dashboard','2024-12-02 09:34:25'),(168,3,'login','Successful login','2024-12-02 10:05:08'),(169,3,'dashboard_access','User accessed dashboard','2024-12-02 10:05:08'),(170,3,'dashboard_access','User accessed dashboard','2024-12-02 10:05:25'),(171,3,'logout','User logged out','2024-12-02 10:05:40'),(172,1,'login','Successful login','2024-12-02 10:05:48'),(173,1,'dashboard_access','User accessed dashboard','2024-12-02 10:05:48'),(174,1,'logout','User logged out','2024-12-02 10:06:18'),(175,4,'login','Successful login','2024-12-02 10:06:26'),(176,4,'dashboard_access','User accessed dashboard','2024-12-02 10:06:26'),(177,4,'dashboard_access','User accessed dashboard','2024-12-02 10:06:48'),(178,4,'dashboard_access','User accessed dashboard','2024-12-02 10:06:56'),(179,4,'logout','User logged out','2024-12-02 10:07:09'),(180,1,'login','Successful login','2024-12-02 10:07:21'),(181,1,'dashboard_access','User accessed dashboard','2024-12-02 10:07:22'),(182,1,'dashboard_access','User accessed dashboard','2024-12-02 10:23:13'),(183,1,'dashboard_access','User accessed dashboard','2024-12-02 10:24:11'),(184,1,'login','Successful login','2024-12-02 10:37:05'),(185,1,'dashboard_access','User accessed dashboard','2024-12-02 10:37:05'),(186,1,'login','Successful login','2024-12-02 19:28:18'),(187,1,'dashboard_access','User accessed dashboard','2024-12-02 19:28:18'),(188,1,'login','Successful login','2024-12-02 19:47:02'),(189,1,'dashboard_access','User accessed dashboard','2024-12-02 19:47:02'),(190,1,'dashboard_access','User accessed dashboard','2024-12-02 19:50:52'),(191,1,'dashboard_access','User accessed dashboard','2024-12-02 19:51:36'),(192,1,'dashboard_access','User accessed dashboard','2024-12-02 19:52:18'),(193,1,'dashboard_access','User accessed dashboard','2024-12-02 20:15:30'),(194,1,'dashboard_access','User accessed dashboard','2024-12-02 20:16:51'),(195,1,'dashboard_access','User accessed dashboard','2024-12-02 20:17:08'),(196,1,'dashboard_access','User accessed dashboard','2024-12-02 20:17:17'),(197,1,'dashboard_access','User accessed dashboard','2024-12-02 20:18:08'),(198,1,'dashboard_access','User accessed dashboard','2024-12-02 20:26:49'),(199,1,'dashboard_access','User accessed dashboard','2024-12-02 20:30:03'),(200,1,'dashboard_access','User accessed dashboard','2024-12-02 20:45:28'),(201,1,'dashboard_access','User accessed dashboard','2024-12-02 20:46:13'),(202,1,'dashboard_access','User accessed dashboard','2024-12-02 20:49:48'),(203,1,'dashboard_access','User accessed dashboard','2024-12-02 20:50:19'),(204,1,'dashboard_access','User accessed dashboard','2024-12-02 21:53:12'),(205,1,'dashboard_access','User accessed dashboard','2024-12-02 21:53:36'),(206,1,'login','Successful login','2024-12-02 21:59:12'),(207,1,'dashboard_access','User accessed dashboard','2024-12-02 21:59:12'),(208,1,'login','Successful login','2024-12-03 05:07:06'),(209,1,'dashboard_access','User accessed dashboard','2024-12-03 05:07:06'),(210,NULL,'login_failed','Failed login attempt for user: admin','2024-12-03 18:47:02'),(211,1,'login','Successful login','2024-12-03 18:47:25'),(212,1,'dashboard_access','User accessed dashboard','2024-12-03 18:47:25'),(213,3,'login','Successful login','2024-12-04 05:59:51'),(214,3,'dashboard_access','User accessed dashboard','2024-12-04 05:59:51'),(215,1,'login','Successful login','2024-12-04 06:00:31'),(216,1,'dashboard_access','User accessed dashboard','2024-12-04 06:00:31'),(217,3,'logout','User logged out','2024-12-04 06:02:23'),(218,5,'login','Successful login','2024-12-04 06:02:31'),(219,5,'dashboard_access','User accessed dashboard','2024-12-04 06:02:31'),(220,5,'dashboard_access','User accessed dashboard','2024-12-04 06:03:25'),(221,1,'login','Successful login','2024-12-04 19:35:07'),(222,1,'dashboard_access','User accessed dashboard','2024-12-04 19:35:07'),(223,1,'login','Successful login','2024-12-04 19:35:20'),(224,1,'dashboard_access','User accessed dashboard','2024-12-04 19:35:21'),(225,1,'logout','User logged out','2024-12-04 19:35:39'),(226,NULL,'login_failed','Failed login attempt for user: manager','2024-12-04 19:35:47'),(227,5,'login','Successful login','2024-12-04 19:36:01'),(228,5,'dashboard_access','User accessed dashboard','2024-12-04 19:36:01'),(229,5,'login','Successful login','2024-12-05 06:05:18'),(230,5,'dashboard_access','User accessed dashboard','2024-12-05 06:05:18'),(231,5,'dashboard_access','User accessed dashboard','2024-12-05 06:05:22'),(232,1,'login','Successful login','2024-12-05 06:06:54'),(233,1,'dashboard_access','User accessed dashboard','2024-12-05 06:06:54'),(234,5,'login','Successful login','2024-12-05 15:21:56'),(235,5,'dashboard_access','User accessed dashboard','2024-12-05 15:21:56'),(236,1,'login','Successful login','2024-12-05 15:23:28'),(237,1,'dashboard_access','User accessed dashboard','2024-12-05 15:23:28'),(238,1,'dashboard_access','User accessed dashboard','2024-12-05 15:23:51'),(239,1,'dashboard_access','User accessed dashboard','2024-12-05 15:23:53'),(240,5,'login','Successful login','2024-12-06 05:34:02'),(241,5,'dashboard_access','User accessed dashboard','2024-12-06 05:34:02'),(242,5,'login','Successful login','2024-12-06 06:38:46'),(243,5,'dashboard_access','User accessed dashboard','2024-12-06 06:38:46'),(244,5,'dashboard_access','User accessed dashboard','2024-12-06 07:16:18'),(245,5,'logout','User logged out','2024-12-06 07:16:31'),(246,1,'login','Successful login','2024-12-06 07:16:41'),(247,1,'dashboard_access','User accessed dashboard','2024-12-06 07:16:41'),(248,1,'production_added','Added production: 120 small boxes, 0 big boxes for date 2024-12-06','2024-12-06 07:25:55'),(249,1,'dashboard_access','User accessed dashboard','2024-12-06 07:28:06'),(250,1,'dashboard_access','User accessed dashboard','2024-12-06 07:29:03'),(251,5,'dashboard_access','User accessed dashboard','2024-12-06 07:51:09'),(252,1,'dashboard_access','User accessed dashboard','2024-12-06 08:37:23'),(253,5,'login','Successful login','2024-12-06 20:08:52'),(254,5,'dashboard_access','User accessed dashboard','2024-12-06 20:08:52'),(255,5,'dashboard_access','User accessed dashboard','2024-12-06 20:09:00'),(256,1,'dashboard_access','User accessed dashboard','2024-12-06 20:27:03'),(257,1,'dashboard_access','User accessed dashboard','2024-12-06 20:27:59'),(258,1,'dashboard_access','User accessed dashboard','2024-12-06 20:28:02'),(259,5,'dashboard_access','User accessed dashboard','2024-12-06 20:52:00'),(260,5,'dashboard_access','User accessed dashboard','2024-12-06 20:53:51'),(261,5,'dashboard_access','User accessed dashboard','2024-12-06 20:54:35'),(262,5,'dashboard_access','User accessed dashboard','2024-12-06 20:54:42');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `backups`
--

DROP TABLE IF EXISTS `backups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `backups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `backups`
--

LOCK TABLES `backups` WRITE;
/*!40000 ALTER TABLE `backups` DISABLE KEYS */;
INSERT INTO `backups` VALUES (1,'backup_2024-11-24_00-28-04.sql','2024-11-23 21:28:04');
/*!40000 ALTER TABLE `backups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `boxes`
--

DROP TABLE IF EXISTS `boxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `boxes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `box_type` enum('small','big') DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `boxes`
--

LOCK TABLES `boxes` WRITE;
/*!40000 ALTER TABLE `boxes` DISABLE KEYS */;
INSERT INTO `boxes` VALUES (1,'small',300.00),(2,'big',500.00);
/*!40000 ALTER TABLE `boxes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `daily_production`
--

DROP TABLE IF EXISTS `daily_production`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `daily_production` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `small_boxes` int(11) DEFAULT 0,
  `big_boxes` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daily_production`
--

LOCK TABLES `daily_production` WRITE;
/*!40000 ALTER TABLE `daily_production` DISABLE KEYS */;
INSERT INTO `daily_production` VALUES (4,'2024-11-25',98,2,'2024-11-25 08:32:18'),(5,'2024-11-29',7,5,'2024-11-28 21:27:45'),(6,'2024-12-06',120,0,'2024-12-06 07:25:55');
/*!40000 ALTER TABLE `daily_production` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_attempts`
--

LOCK TABLES `login_attempts` WRITE;
/*!40000 ALTER TABLE `login_attempts` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_date` date NOT NULL,
  `manager_id` int(11) NOT NULL,
  `recorded_by` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `boxes_count` int(11) DEFAULT NULL,
  `balance` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `staff_id` (`staff_id`),
  KEY `fk_manager` (`manager_id`),
  KEY `fk_recorded_by` (`recorded_by`),
  CONSTRAINT `fk_manager` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_recorded_by` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,'2024-12-02',3,1,NULL,NULL,10000.00,'',NULL,NULL,'2024-12-02 19:47:59'),(2,'2024-12-02',3,1,NULL,NULL,25000.00,'payment received via mobile money',NULL,NULL,'2024-12-02 19:49:19');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff`
--

DROP TABLE IF EXISTS `staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff`
--

LOCK TABLES `staff` WRITE;
/*!40000 ALTER TABLE `staff` DISABLE KEYS */;
INSERT INTO `staff` VALUES (1,'Hamza','supplier','0700118085'),(2,'wilber ','supplier','0756351211');
/*!40000 ALTER TABLE `staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplies`
--

DROP TABLE IF EXISTS `supplies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supplies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `small_boxes` int(11) DEFAULT 0,
  `big_boxes` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `staff_id` (`staff_id`),
  CONSTRAINT `supplies_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplies`
--

LOCK TABLES `supplies` WRITE;
/*!40000 ALTER TABLE `supplies` DISABLE KEYS */;
INSERT INTO `supplies` VALUES (1,'2024-11-29',1,80,0,'2024-11-28 22:20:31'),(2,'2024-12-02',1,25,0,'2024-12-02 20:46:03'),(3,'2024-12-06',1,100,0,'2024-12-06 07:28:35'),(4,'2024-12-06',1,10,0,'2024-12-06 20:09:37');
/*!40000 ALTER TABLE `supplies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supply_comments`
--

DROP TABLE IF EXISTS `supply_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supply_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supply_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `supply_id` (`supply_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `supply_comments_ibfk_1` FOREIGN KEY (`supply_id`) REFERENCES `supplies` (`id`),
  CONSTRAINT `supply_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supply_comments`
--

LOCK TABLES `supply_comments` WRITE;
/*!40000 ALTER TABLE `supply_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `supply_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','manager','staff') DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','$2y$10$T6ko02McfGWGZxOjUdiYCOjR2hhEF7KuwrUr1Fi9tnywWNyekfQyq','admin','2024-11-22 21:17:49'),(3,'admin22','$2y$10$EELtnpgxurSRbueqNVUEfOmmEaQ8hBwWNZt9rnGnbTmBIla75r9Aa','manager','2024-11-29 21:07:14'),(4,'staff','$2y$10$0.5dWksVHPgWjeUcY6Zv1.LYgskW4U1g77LTRP4brHdi9bEgsAYXO','staff','2024-11-29 21:09:20'),(5,'fadhil','$2y$10$KnNQTLALKkpmQavEEOcUEed/lwOqRxZVmcRV3Jpybm8kUrsJQl/be','manager','2024-12-04 06:01:57');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-12-08 11:53:29
