-- MySQL dump 10.16  Distrib 10.1.25-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: dev_obbi
-- ------------------------------------------------------
-- Server version	10.1.25-MariaDB-1~xenial

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `detal_users`
--

DROP TABLE IF EXISTS `detal_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detal_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `kelurahan_id` char(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zip` char(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valid` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detal_users`
--

LOCK TABLES `detal_users` WRITE;
/*!40000 ALTER TABLE `detal_users` DISABLE KEYS */;
INSERT INTO `detal_users` VALUES (1,9,'256','Ulujami','085891931071','15226',1,'0000-00-00 00:00:00',NULL);
/*!40000 ALTER TABLE `detal_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `donations`
--

DROP TABLE IF EXISTS `donations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `donations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `code` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `detail_id` int(11) NOT NULL,
  `notes` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jumlah` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `donations`
--

LOCK TABLES `donations` WRITE;
/*!40000 ALTER TABLE `donations` DISABLE KEYS */;
INSERT INTO `donations` VALUES (1,1,'',0,NULL,0,'2018-06-20 10:23:01','2018-06-20 10:23:01'),(2,9,'',0,'pembangunan masjid',0,'2018-06-20 14:39:58','2018-06-20 14:39:58'),(3,10,'',0,'pembangunan masjid',0,'2018-06-20 14:39:58','2018-06-20 14:39:58'),(4,9,'',0,'Masjid',-1000,'2018-06-23 11:40:03','2018-06-23 11:40:03'),(5,9,'',0,'Pendidikan',-5000,'2018-06-23 11:40:03','2018-06-23 11:40:03'),(6,9,'',0,'Masjid',-1000,'2018-06-23 11:47:00','2018-06-23 11:47:00'),(7,9,'',0,'Pendidikan',-5000,'2018-06-23 11:47:00','2018-06-23 11:47:00'),(12,9,'DNT-00008',1,'Masjid',-500,'2018-06-24 08:14:57','2018-06-24 08:14:57'),(13,9,'DNT-00008',1,'Pendidikan',0,'2018-06-24 08:14:58','2018-06-24 08:14:58'),(14,9,'DNT-00001',1,'Masjid',-500,'2018-06-24 08:18:15','2018-06-24 08:18:15'),(15,9,'DNT-00001',1,'Pendidikan',0,'2018-06-24 08:18:15','2018-06-24 08:18:15'),(16,9,'DNT-00001',1,'Masjid',-500,'2018-06-24 08:18:31','2018-06-24 08:18:31'),(17,9,'DNT-00001',1,'Pendidikan',0,'2018-06-24 08:18:31','2018-06-24 08:18:31'),(18,9,'DNT-00009',1,'Masjid',-500,'2018-06-24 08:20:01','2018-06-24 08:20:01'),(19,9,'DNT-00009',1,'Pendidikan',0,'2018-06-24 08:20:01','2018-06-24 08:20:01'),(20,9,'DNT-00010',1,'Masjid',-500,'2018-06-24 08:50:05','2018-06-24 08:50:05'),(21,9,'DNT-00010',1,'Pendidikan',0,'2018-06-24 08:50:05','2018-06-24 08:50:05');
/*!40000 ALTER TABLE `donations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `herobis`
--

DROP TABLE IF EXISTS `herobis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `herobis` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `image1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image2` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image3` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valid` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `herobis`
--

LOCK TABLES `herobis` WRITE;
/*!40000 ALTER TABLE `herobis` DISABLE KEYS */;
INSERT INTO `herobis` VALUES (3,10,'http://localhost:8000/api/herobi/dokument/MZBEgHotEUJDvlD6QSaMRJmflX5SKA8oZUodlYRn.jpeg','http://localhost:8000/api/herobi/dokument/4sAaj2iv5VhWrmEmU35EphIP1CqbgooVEBoGal0W.jpeg','http://localhost:8000/api/herobi/dokument/Qj9vQK8rChjFfm8hfORPkiQcHYCYylHDvqpQ2rUL.jpeg',0,'2018-06-18 14:29:03','2018-06-18 14:29:03'),(4,10,'http://localhost:8000/img/herobi/w5BCg7P7tZfUjMAq3r9Y110r4T8KT5eSpQgO0s9R.jpeg','http://localhost:8000/img/herobi/GTUJm4pyRV8DtxFk7CON5u6RpQrX486qq5vDpeQk.jpeg','http://localhost:8000/img/herobi/6AIMfuXkcsrf6b1nL50g4OX1MmefUrWOvDonmkhq.jpeg',0,'2018-06-18 14:34:08','2018-06-18 14:34:08'),(5,10,'http://localhost:8000/img/herobi/mXg4g0viBwmWrNDaliG8JbL9pJR2OQ9lf4rD6eEy.png','http://localhost:8000/img/herobi/yIykp23YckQzftQyP0Hv4ibAMW34eQWgMyipYO2J.png','http://localhost:8000/img/herobi/111HvHPloGcdnYmZpbbNfn6dwANcE0kMeILvqOyD.png',0,'2018-06-21 11:03:52','2018-06-21 11:03:52'),(6,10,'http://localhost:8000/img/herobi/d3aa3585934e05fc3ccf2025387bf378.','http://localhost:8000/img/herobi/d3aa3585934e05fc3ccf2025387bf378.','http://localhost:8000/img/herobi/d3aa3585934e05fc3ccf2025387bf378.',0,'2018-06-21 15:38:05','2018-06-21 15:38:05'),(7,10,'http://localhost:8000/img/herobi/3acc13343809eb4c0f6af0394375851b.','http://localhost:8000/img/herobi/3acc13343809eb4c0f6af0394375851b.','http://localhost:8000/img/herobi/3acc13343809eb4c0f6af0394375851b.',0,'2018-06-21 15:51:02','2018-06-21 15:51:02'),(8,10,'http://localhost:8000/img/herobi/f246334cb4f194154f4e4747562fc4fa.png','http://localhost:8000/img/herobi/f246334cb4f194154f4e4747562fc4fa.png','http://localhost:8000/img/herobi/f246334cb4f194154f4e4747562fc4fa.jpg',0,'2018-06-21 16:32:12','2018-06-21 16:32:12');
/*!40000 ALTER TABLE `herobis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `saldoamals`
--

DROP TABLE IF EXISTS `saldoamals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `saldoamals` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `balance_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `donation_code` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jumlah` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `saldoamals`
--

LOCK TABLES `saldoamals` WRITE;
/*!40000 ALTER TABLE `saldoamals` DISABLE KEYS */;
INSERT INTO `saldoamals` VALUES (1,11,9,'0',500,'2018-06-24 07:08:37','2018-06-24 07:08:37'),(2,0,9,'DNT-00008',-500,'2018-06-24 08:14:58','2018-06-24 08:14:58'),(3,0,9,'DNT-00001',-500,'2018-06-24 08:18:15','2018-06-24 08:18:15'),(4,0,9,'DNT-00001',-500,'2018-06-24 08:18:31','2018-06-24 08:18:31'),(5,0,9,'DNT-00009',-500,'2018-06-24 08:20:01','2018-06-24 08:20:01'),(6,0,9,'DNT-00010',-500,'2018-06-24 08:50:05','2018-06-24 08:50:05');
/*!40000 ALTER TABLE `saldoamals` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-06-27 14:14:48
