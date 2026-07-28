-- MySQL dump 10.13  Distrib 9.7.0, for macos15 (arm64)
--
-- Host: localhost    Database: kalender_app
-- ------------------------------------------------------
-- Server version	9.7.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ '109cbcb8-5743-11f1-8dc4-0535fa497c95:1-14406';

--
-- Table structure for table `batch_schedules`
--

DROP TABLE IF EXISTS `batch_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `batch_schedules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `training_batch_id` bigint unsigned NOT NULL,
  `template_phase_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `sequence` int NOT NULL DEFAULT '0',
  `activity_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `conflict_group` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#0369A1',
  `is_locked` tinyint(1) NOT NULL DEFAULT '0',
  `is_manual` tinyint(1) NOT NULL DEFAULT '0',
  `is_anchor` tinyint(1) NOT NULL DEFAULT '0',
  `recalculation_source` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'generated',
  `recalculated_at` timestamp NULL DEFAULT NULL,
  `is_alert` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `generated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `batch_schedules_template_phase_id_foreign` (`template_phase_id`),
  KEY `batch_schedules_training_batch_id_start_date_end_date_index` (`training_batch_id`,`start_date`,`end_date`),
  KEY `batch_schedules_conflict_group_index` (`conflict_group`),
  KEY `batch_schedules_generated_by_foreign` (`generated_by`),
  CONSTRAINT `batch_schedules_generated_by_foreign` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `batch_schedules_template_phase_id_foreign` FOREIGN KEY (`template_phase_id`) REFERENCES `template_phases` (`id`) ON DELETE SET NULL,
  CONSTRAINT `batch_schedules_training_batch_id_foreign` FOREIGN KEY (`training_batch_id`) REFERENCES `training_batches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `batch_schedules`
--

LOCK TABLES `batch_schedules` WRITE;
/*!40000 ALTER TABLE `batch_schedules` DISABLE KEYS */;
INSERT INTO `batch_schedules` VALUES (15,3,1,'CekIn1','2026-06-30','2026-06-30',1,'checkin','','#0369A1',0,0,0,'generated',NULL,0,NULL,1,'2026-07-15 20:31:05','2026-07-15 20:31:05'),(16,3,2,'On-Campus1','2026-07-01','2026-07-03',2,'classroom','','#0891B2',0,0,0,'generated',NULL,0,NULL,1,'2026-07-15 20:31:05','2026-07-15 20:31:05'),(17,3,3,'Klasikal1','2026-07-04','2026-07-09',3,'classroom','','#059669',0,0,0,'generated',NULL,0,NULL,1,'2026-07-15 20:31:05','2026-07-15 20:31:05'),(18,3,4,'Seminar','2026-07-10','2026-07-10',4,'seminar','seminar','#EF4444',0,0,0,'generated',NULL,1,NULL,1,'2026-07-15 20:31:05','2026-07-15 20:31:05'),(19,3,5,'OJT1','2026-07-11','2026-07-22',5,'ojt','','#7C3AED',0,0,0,'generated',NULL,0,NULL,1,'2026-07-15 20:31:05','2026-07-15 20:31:05'),(20,3,6,'Post-Test','2026-07-23','2026-07-23',6,'test','','#D97706',0,0,0,'generated',NULL,0,NULL,1,'2026-07-15 20:31:05','2026-07-15 20:31:05'),(21,3,7,'CekOut','2026-07-24','2026-07-24',7,'checkin','','#64748B',0,0,0,'generated',NULL,0,NULL,1,'2026-07-15 20:31:05','2026-07-15 20:31:05');
/*!40000 ALTER TABLE `batch_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('laravel-cache-spatie.permission.cache','a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:9:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:12:\"manage_users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:12:\"manage_units\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:16:\"manage_templates\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:14:\"manage_batches\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:17:\"generate_schedule\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:9:\"assign_wi\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:15:\"manage_holidays\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:10:\"export_pdf\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:17:\"monitor_conflicts\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}}s:5:\"roles\";a:3:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:11:\"super_admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:12:\"unit_manager\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:5:\"admin\";s:1:\"c\";s:3:\"web\";}}}',1784251015);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `holidays`
--

DROP TABLE IF EXISTS `holidays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `holidays` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_national` tinyint(1) NOT NULL DEFAULT '1',
  `organizational_unit_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `holidays_date_organizational_unit_id_unique` (`date`,`organizational_unit_id`),
  KEY `holidays_date_index` (`date`),
  KEY `holidays_organizational_unit_id_foreign` (`organizational_unit_id`),
  CONSTRAINT `holidays_organizational_unit_id_foreign` FOREIGN KEY (`organizational_unit_id`) REFERENCES `organizational_units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `holidays`
--

LOCK TABLES `holidays` WRITE;
/*!40000 ALTER TABLE `holidays` DISABLE KEYS */;
/*!40000 ALTER TABLE `holidays` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_06_09_000001_add_org_fields_to_users_table',1),(5,'2026_06_09_000002_create_organizational_units_table',1),(6,'2026_06_09_000003_create_training_templates_table',1),(7,'2026_06_09_000004_create_template_phases_table',1),(8,'2026_06_09_000005_create_training_batches_table',1),(9,'2026_06_09_000006_create_batch_schedules_table',1),(10,'2026_06_09_000007_create_widyaiswaras_table',1),(11,'2026_06_09_000008_create_wi_assignments_table',1),(12,'2026_06_09_000009_create_holidays_table',1),(13,'2026_06_09_034735_create_permission_tables',1),(14,'2026_06_09_034800_add_ownership_columns',1),(15,'2026_06_09_075602_change_allow_weekend_to_work_days_in_template_phases',1),(16,'2026_06_09_153856_add_recalculation_fields_to_batch_schedules',1),(17,'2026_06_09_154553_add_unit_scope_to_holidays',1),(18,'2026_06_09_165607_add_has_conflict_alert_to_training_batches',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organizational_units`
--

DROP TABLE IF EXISTS `organizational_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `organizational_units` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `organizational_units_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organizational_units`
--

LOCK TABLES `organizational_units` WRITE;
/*!40000 ALTER TABLE `organizational_units` DISABLE KEYS */;
INSERT INTO `organizational_units` VALUES (1,'TEKPIM','Tekpim','Unit Teknologi dan Pimpinan',1,'2026-06-09 20:10:19','2026-06-09 20:10:19'),(2,'FUNGSIONAL','Fungsional','Unit Fungsional',1,'2026-06-09 20:10:19','2026-06-09 20:10:19'),(3,'SEKRETARIAT','Sekretariat','Unit Sekretariat',1,'2026-06-09 20:10:19','2026-06-09 20:10:19'),(4,'PENKOM','Penkom','Unit Pengembangan Kompetensi',1,'2026-06-09 20:10:19','2026-06-09 20:10:19');
/*!40000 ALTER TABLE `organizational_units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'manage_users','web','2026-06-09 20:10:19','2026-06-09 20:10:19'),(2,'manage_units','web','2026-06-09 20:10:19','2026-06-09 20:10:19'),(3,'manage_templates','web','2026-06-09 20:10:19','2026-06-09 20:10:19'),(4,'manage_batches','web','2026-06-09 20:10:19','2026-06-09 20:10:19'),(5,'generate_schedule','web','2026-06-09 20:10:19','2026-06-09 20:10:19'),(6,'assign_wi','web','2026-06-09 20:10:19','2026-06-09 20:10:19'),(7,'manage_holidays','web','2026-06-09 20:10:19','2026-06-09 20:10:19'),(8,'export_pdf','web','2026-06-09 20:10:19','2026-06-09 20:10:19'),(9,'monitor_conflicts','web','2026-06-09 20:10:19','2026-06-09 20:10:19');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(3,2),(4,2),(5,2),(6,2),(7,2),(8,2),(9,2),(3,3),(4,3),(5,3),(6,3),(8,3);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'super_admin','web','2026-06-09 20:10:19','2026-06-09 20:10:19'),(2,'unit_manager','web','2026-06-09 20:10:19','2026-06-09 20:10:19'),(3,'admin','web','2026-06-09 20:10:19','2026-06-09 20:10:19');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('AfDxonSkfrah29rywr7C3pcQzWu7sNUx1CthxIds',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJWU3pXZTZ3akZLWXM2UnhNQm43VzFkYWg2bDlJNVFrRG9QNW9lejIyIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hZG1pblwvZGFzaGJvYXJkIiwicm91dGUiOiJhZG1pbi5kYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MSwiaW1wb3J0X3RlbXBsYXRlX2lkIjoxfQ==',1784175099);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `template_phases`
--

DROP TABLE IF EXISTS `template_phases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `template_phases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `training_template_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sequence` int NOT NULL DEFAULT '0',
  `duration` int NOT NULL DEFAULT '1',
  `duration_unit` enum('day','hour') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'day',
  `offset_days` int NOT NULL DEFAULT '0',
  `day_type` enum('working_day','calendar_day') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'working_day',
  `work_days` tinyint NOT NULL DEFAULT '5',
  `activity_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `conflict_group` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_alert` tinyint(1) NOT NULL DEFAULT '0',
  `alert_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `can_manual_edit` tinyint(1) NOT NULL DEFAULT '0',
  `color` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#0369A1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `template_phases_training_template_id_foreign` (`training_template_id`),
  CONSTRAINT `template_phases_training_template_id_foreign` FOREIGN KEY (`training_template_id`) REFERENCES `training_templates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `template_phases`
--

LOCK TABLES `template_phases` WRITE;
/*!40000 ALTER TABLE `template_phases` DISABLE KEYS */;
INSERT INTO `template_phases` VALUES (1,1,'CekIn1',1,1,'day',-1,'working_day',5,'checkin','',0,NULL,0,'#0369A1','2026-07-15 19:49:39','2026-07-15 19:49:39'),(2,1,'On-Campus1',2,3,'day',0,'working_day',5,'classroom','',0,NULL,0,'#0891B2','2026-07-15 19:49:39','2026-07-15 19:49:39'),(3,1,'Klasikal1',3,5,'day',0,'working_day',6,'classroom','',0,NULL,0,'#059669','2026-07-15 19:49:39','2026-07-15 19:49:39'),(4,1,'Seminar',4,1,'day',0,'working_day',5,'seminar','seminar',1,NULL,0,'#EF4444','2026-07-15 19:49:39','2026-07-15 19:49:39'),(5,1,'OJT1',5,10,'day',0,'working_day',6,'ojt','',0,NULL,1,'#7C3AED','2026-07-15 19:49:39','2026-07-15 19:49:39'),(6,1,'Post-Test',6,1,'day',0,'working_day',5,'test','',0,NULL,0,'#D97706','2026-07-15 19:49:39','2026-07-15 19:49:39'),(7,1,'CekOut',7,1,'day',0,'working_day',5,'checkin','',0,NULL,0,'#64748B','2026-07-15 19:49:39','2026-07-15 19:49:39');
/*!40000 ALTER TABLE `template_phases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_batches`
--

DROP TABLE IF EXISTS `training_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `training_batches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `training_template_id` bigint unsigned NOT NULL,
  `organizational_unit_id` bigint unsigned DEFAULT NULL,
  `batch_number` int NOT NULL,
  `year` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `participant_count` int NOT NULL DEFAULT '0',
  `status` enum('draft','generated','finished') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `has_conflict_alert` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `training_batches_training_template_id_foreign` (`training_template_id`),
  KEY `training_batches_organizational_unit_id_foreign` (`organizational_unit_id`),
  KEY `training_batches_created_by_foreign` (`created_by`),
  KEY `training_batches_updated_by_foreign` (`updated_by`),
  CONSTRAINT `training_batches_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `training_batches_organizational_unit_id_foreign` FOREIGN KEY (`organizational_unit_id`) REFERENCES `organizational_units` (`id`) ON DELETE SET NULL,
  CONSTRAINT `training_batches_training_template_id_foreign` FOREIGN KEY (`training_template_id`) REFERENCES `training_templates` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `training_batches_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `training_batches`
--

LOCK TABLES `training_batches` WRITE;
/*!40000 ALTER TABLE `training_batches` DISABLE KEYS */;
INSERT INTO `training_batches` VALUES (3,1,1,1,2026,'PKA','2026-07-01','2026-07-24',30,'generated',0,1,1,'2026-07-15 20:30:58','2026-07-15 20:31:05');
/*!40000 ALTER TABLE `training_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_templates`
--

DROP TABLE IF EXISTS `training_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `training_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organizational_unit_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `training_templates_code_unique` (`code`),
  KEY `training_templates_organizational_unit_id_foreign` (`organizational_unit_id`),
  KEY `training_templates_created_by_foreign` (`created_by`),
  KEY `training_templates_updated_by_foreign` (`updated_by`),
  CONSTRAINT `training_templates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `training_templates_organizational_unit_id_foreign` FOREIGN KEY (`organizational_unit_id`) REFERENCES `organizational_units` (`id`) ON DELETE SET NULL,
  CONSTRAINT `training_templates_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `training_templates`
--

LOCK TABLES `training_templates` WRITE;
/*!40000 ALTER TABLE `training_templates` DISABLE KEYS */;
INSERT INTO `training_templates` VALUES (1,1,1,1,'PKA-2026','PKA',NULL,1,'2026-07-15 19:49:39','2026-07-15 19:49:39');
/*!40000 ALTER TABLE `training_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organizational_unit_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,NULL,'Super Admin','admin@bpsdm.go.id','2026-06-09 20:10:19','$2y$12$d96D.1dfTG4SwetYtIFOfe39A/AOYZJHyAcI6PAB4Z14lkbT7bXa2',1,'7vXhYpk2WO','2026-06-09 20:10:20','2026-06-09 20:10:20');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wi_assignments`
--

DROP TABLE IF EXISTS `wi_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wi_assignments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `batch_schedule_id` bigint unsigned NOT NULL,
  `widyaiswara_id` bigint unsigned NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `assigned_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wi_assignments_batch_schedule_id_foreign` (`batch_schedule_id`),
  KEY `wi_assignments_widyaiswara_id_start_date_end_date_index` (`widyaiswara_id`,`start_date`,`end_date`),
  KEY `wi_assignments_assigned_by_foreign` (`assigned_by`),
  CONSTRAINT `wi_assignments_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `wi_assignments_batch_schedule_id_foreign` FOREIGN KEY (`batch_schedule_id`) REFERENCES `batch_schedules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `wi_assignments_widyaiswara_id_foreign` FOREIGN KEY (`widyaiswara_id`) REFERENCES `widyaiswaras` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wi_assignments`
--

LOCK TABLES `wi_assignments` WRITE;
/*!40000 ALTER TABLE `wi_assignments` DISABLE KEYS */;
/*!40000 ALTER TABLE `wi_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `widyaiswaras`
--

DROP TABLE IF EXISTS `widyaiswaras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `widyaiswaras` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nip` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expertise` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `widyaiswaras_nip_unique` (`nip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `widyaiswaras`
--

LOCK TABLES `widyaiswaras` WRITE;
/*!40000 ALTER TABLE `widyaiswaras` DISABLE KEYS */;
/*!40000 ALTER TABLE `widyaiswaras` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-28 10:29:49
