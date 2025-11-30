-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table requisition_slip_test.account_groups
CREATE TABLE IF NOT EXISTS `account_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name_account_group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_garansi` tinyint(1) NOT NULL DEFAULT '0',
  `ccar` enum('smd_idr','smd_usd') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'smd_idr',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.account_groups: ~2 rows (approximately)
INSERT INTO `account_groups` (`id`, `name_account_group`, `bank_garansi`, `ccar`, `created_at`, `updated_at`) VALUES
	(1, 'COMMERCIAL', 1, 'smd_idr', '2025-11-24 09:58:16', '2025-11-24 09:58:16'),
	(2, 'KEY ACOOUNT', 1, 'smd_idr', '2025-11-24 10:00:58', '2025-11-24 10:00:58'),
	(3, 'REGION 1A', 0, 'smd_idr', '2025-11-24 10:01:13', '2025-11-24 10:01:13');

-- Dumping structure for table requisition_slip_test.activity_log
CREATE TABLE IF NOT EXISTS `activity_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `log_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `causer_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` bigint unsigned DEFAULT NULL,
  `properties` json DEFAULT NULL,
  `batch_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject` (`subject_type`,`subject_id`),
  KEY `causer` (`causer_type`,`causer_id`),
  KEY `activity_log_log_name_index` (`log_name`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.activity_log: ~11 rows (approximately)
INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES
	(1, 'default', 'Deleted a role', NULL, 'roles', NULL, 'App\\Models\\User', 18, '{"deleted_data": {"id": 19, "name": "sales", "created_at": "2025-11-24T09:49:13.000000Z", "guard_name": "web", "updated_at": "2025-11-24T09:49:13.000000Z"}}', NULL, '2025-11-24 09:49:41', '2025-11-24 09:49:41'),
	(2, 'default', 'Created a new role', 'Spatie\\Permission\\Models\\Role', 'roles', 20, 'App\\Models\\User', 18, '[]', NULL, '2025-11-24 09:49:49', '2025-11-24 09:49:49'),
	(3, 'default', 'Updated user data', 'App\\Models\\User', 'users', 25, 'App\\Models\\User', 18, '{"new": {"roles": ["sales"]}, "old": {"id": 25, "nik": "U0950", "name": "Rofika", "email": "rofika.lay@smii.co.id", "roles": ["user-requisition"], "avatar": null, "status": "active", "password": "$2y$12$YHibt4GsyVLajnkr9mhKOeAEbkD2xXUa7pLQCidx/qo9SaCw6hu8.", "username": "rofika", "atasan_nik": "ATASAN01", "created_at": "2025-11-24T09:44:53.000000Z", "updated_at": "2025-11-24T09:44:53.000000Z", "position_id": 1, "department_id": 7, "remember_token": null, "email_verified_at": null}}', NULL, '2025-11-24 09:50:11', '2025-11-24 09:50:11'),
	(4, 'default', 'Updated user data', 'App\\Models\\User', 'users', 18, 'App\\Models\\User', 18, '{"new": {"roles": ["super-admin", "sales"], "updated_at": "2025-11-25 10:24:05", "position_id": "1"}, "old": {"id": 18, "nik": "AG1111", "name": "Super Admin", "email": "superadmin@example.com", "roles": ["super-admin"], "avatar": null, "status": "active", "password": "$2y$12$abqy8HxkqZ2nJGcszsFInev3f7DTrMyztJ17OlDyykiackekxAMEi", "username": "superadmin", "atasan_nik": "AG2222", "created_at": "2025-11-10T08:34:32.000000Z", "updated_at": "2025-11-10T08:34:32.000000Z", "position_id": null, "department_id": 1, "remember_token": "IU26TUF3fLwjnB2dvf0vxyvejA2VEKY92XKk54CluEUNccUpIwRD4dJGhvuA", "email_verified_at": "2025-11-10T08:34:32.000000Z"}}', NULL, '2025-11-25 03:24:05', '2025-11-25 03:24:05'),
	(5, 'default', 'Created a new role', 'Spatie\\Permission\\Models\\Role', 'roles', 21, 'App\\Models\\User', 18, '[]', NULL, '2025-11-28 07:13:57', '2025-11-28 07:13:57'),
	(6, 'default', 'Created a new role', 'Spatie\\Permission\\Models\\Role', 'roles', 22, 'App\\Models\\User', 18, '[]', NULL, '2025-11-28 07:14:12', '2025-11-28 07:14:12'),
	(7, 'default', 'Created a new role', 'Spatie\\Permission\\Models\\Role', 'roles', 23, 'App\\Models\\User', 18, '[]', NULL, '2025-11-28 07:14:20', '2025-11-28 07:14:20'),
	(8, 'path - customer', 'Membuat alur persetujuan baru untuk Customer - CBD.', 'App\\Models\\Requisition\\ApprovalPath', 'create', 1, 'App\\Models\\User', 18, '{"category": "Customer", "approvers": ["atasan", "head-SNM", "finance-manager", "IT"], "sub_category": "CBD"}', NULL, '2025-11-28 07:19:28', '2025-11-28 07:19:28'),
	(9, 'path - customer', 'Membuat alur persetujuan baru untuk Customer.', 'App\\Models\\Requisition\\ApprovalPath', 'create', 2, 'App\\Models\\User', 18, '{"category": "Customer", "approvers": ["atasan", "head-SNM", "finance-manager", "head-FA", "IT"], "sub_category": null}', NULL, '2025-11-28 07:21:34', '2025-11-28 07:21:34'),
	(10, 'default', 'Created a new user', 'App\\Models\\User', 'users', 26, 'App\\Models\\User', 18, '[]', NULL, '2025-11-28 07:27:05', '2025-11-28 07:27:05'),
	(11, 'default', 'Created a new user', 'App\\Models\\User', 'users', 27, 'App\\Models\\User', 18, '[]', NULL, '2025-11-28 07:28:16', '2025-11-28 07:28:16'),
	(12, 'default', 'Created a new user', 'App\\Models\\User', 'users', 28, 'App\\Models\\User', 18, '[]', NULL, '2025-11-28 07:28:58', '2025-11-28 07:28:58'),
	(13, 'default', 'Created a new user', 'App\\Models\\User', 'users', 29, 'App\\Models\\User', 18, '[]', NULL, '2025-11-28 07:29:55', '2025-11-28 07:29:55');

-- Dumping structure for table requisition_slip_test.approval_logs
CREATE TABLE IF NOT EXISTS `approval_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `related_id` bigint unsigned NOT NULL,
  `approver_nik` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` int DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `token` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_logs_type_related_id_index` (`category`,`related_id`) USING BTREE,
  KEY `approval_logs_user_id_foreign` (`approver_nik`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.approval_logs: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.approval_paths
CREATE TABLE IF NOT EXISTS `approval_paths` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sub_category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sequence_approvers` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.approval_paths: ~2 rows (approximately)
INSERT INTO `approval_paths` (`id`, `category`, `sub_category`, `sequence_approvers`, `created_at`, `updated_at`) VALUES
	(1, 'Customer', 'CBD', '["atasan", "head-SNM", "finance-manager", "IT"]', '2025-11-28 07:19:28', '2025-11-28 07:19:28'),
	(2, 'Customer', NULL, '["atasan", "head-SNM", "finance-manager", "head-FA", "IT"]', '2025-11-28 07:21:34', '2025-11-28 07:21:34');

-- Dumping structure for table requisition_slip_test.approvers
CREATE TABLE IF NOT EXISTS `approvers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sub_category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sequence_approvers` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approvers_type_level_index` (`category`,`sub_category`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.approvers: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.bank_garansi
CREATE TABLE IF NOT EXISTS `bank_garansi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `bg_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bg_type` enum('existing','extension','new') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `base_bg_id` bigint unsigned DEFAULT NULL,
  `bg_nominal` decimal(18,2) NOT NULL DEFAULT '0.00',
  `issued_date` date DEFAULT NULL,
  `exp_date` date DEFAULT NULL,
  `status` enum('draft','sent_to_customer','submitted','reviewed','approved','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bank_garansi_customer_id_foreign` (`customer_id`),
  KEY `bank_garansi_base_bg_id_foreign` (`base_bg_id`),
  KEY `bank_garansi_created_by_foreign` (`created_by`),
  CONSTRAINT `bank_garansi_base_bg_id_foreign` FOREIGN KEY (`base_bg_id`) REFERENCES `bank_garansi` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bank_garansi_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bank_garansi_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.bank_garansi: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.bg_details
CREATE TABLE IF NOT EXISTS `bg_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `bank_garansi_id` bigint unsigned NOT NULL,
  `bank_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_address` text COLLATE utf8mb4_unicode_ci,
  `contact_person` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nominal` decimal(18,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bg_details_bank_garansi_id_foreign` (`bank_garansi_id`),
  CONSTRAINT `bg_details_bank_garansi_id_foreign` FOREIGN KEY (`bank_garansi_id`) REFERENCES `bank_garansi` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.bg_details: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.bg_histories
CREATE TABLE IF NOT EXISTS `bg_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `bank_garansi_id` bigint unsigned NOT NULL,
  `previous_nominal` decimal(18,2) DEFAULT NULL,
  `new_nominal` decimal(18,2) DEFAULT NULL,
  `previous_exp_date` date DEFAULT NULL,
  `new_exp_date` date DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bg_histories_bank_garansi_id_foreign` (`bank_garansi_id`),
  KEY `bg_histories_created_by_foreign` (`created_by`),
  CONSTRAINT `bg_histories_bank_garansi_id_foreign` FOREIGN KEY (`bank_garansi_id`) REFERENCES `bank_garansi` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bg_histories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.bg_histories: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.bg_limit_rules
CREATE TABLE IF NOT EXISTS `bg_limit_rules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `min_year` int DEFAULT NULL,
  `max_year` int DEFAULT NULL,
  `percentage` decimal(5,2) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.bg_limit_rules: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.bg_recommendations
CREATE TABLE IF NOT EXISTS `bg_recommendations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `average` decimal(18,2) DEFAULT NULL,
  `average_increase_percent` decimal(5,2) DEFAULT NULL,
  `top` int DEFAULT NULL,
  `lead_time` int DEFAULT NULL,
  `inflation` decimal(5,2) DEFAULT NULL,
  `increase_percent` decimal(5,2) DEFAULT NULL,
  `recommended_credit_limit` decimal(18,2) DEFAULT NULL,
  `rounded_credit_limit` decimal(18,2) DEFAULT NULL,
  `fk_with_limit` decimal(18,2) DEFAULT NULL,
  `current_bg` decimal(18,2) DEFAULT NULL,
  `set_bg` decimal(18,2) DEFAULT NULL,
  `credit_limit_updated` decimal(18,2) DEFAULT NULL,
  `status` enum('draft','pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bg_recommendations_customer_id_foreign` (`customer_id`),
  CONSTRAINT `bg_recommendations_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.bg_recommendations: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.bg_submissions
CREATE TABLE IF NOT EXISTS `bg_submissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `bg_recommendation_id` bigint unsigned NOT NULL,
  `form_code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_nominal` decimal(18,2) NOT NULL DEFAULT '0.00',
  `signed_document_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `upload_completed_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending_print','awaiting_upload','uploaded','reviewed','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending_print',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bg_submissions_form_code_unique` (`form_code`),
  KEY `bg_submissions_bg_recommendation_id_foreign` (`bg_recommendation_id`),
  CONSTRAINT `bg_submissions_bg_recommendation_id_foreign` FOREIGN KEY (`bg_recommendation_id`) REFERENCES `bg_recommendations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.bg_submissions: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.branches
CREATE TABLE IF NOT EXISTS `branches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.branches: ~0 rows (approximately)
INSERT INTO `branches` (`id`, `branch_name`, `created_at`, `updated_at`) VALUES
	(1, 'Pusat', '2025-11-24 09:17:55', '2025-11-24 09:17:55');

-- Dumping structure for table requisition_slip_test.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.cache: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.cache_locks: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.complain_images
CREATE TABLE IF NOT EXISTS `complain_images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `requisition_id` bigint unsigned NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `complain_images_requisition_id_foreign` (`requisition_id`),
  CONSTRAINT `complain_images_requisition_id_foreign` FOREIGN KEY (`requisition_id`) REFERENCES `requisitions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.complain_images: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.credit_limits
CREATE TABLE IF NOT EXISTS `credit_limits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `bank_garansi_id` bigint unsigned DEFAULT NULL,
  `recommendation_id` bigint unsigned DEFAULT NULL,
  `requested_limit` decimal(18,2) DEFAULT NULL,
  `approved_limit` decimal(18,2) DEFAULT NULL,
  `lampiran_d_version_id` bigint unsigned DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `credit_limits_customer_id_foreign` (`customer_id`),
  KEY `credit_limits_bank_garansi_id_foreign` (`bank_garansi_id`),
  KEY `credit_limits_recommendation_id_foreign` (`recommendation_id`),
  KEY `credit_limits_lampiran_d_version_id_foreign` (`lampiran_d_version_id`),
  KEY `credit_limits_approved_by_foreign` (`approved_by`),
  CONSTRAINT `credit_limits_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `credit_limits_bank_garansi_id_foreign` FOREIGN KEY (`bank_garansi_id`) REFERENCES `bank_garansi` (`id`) ON DELETE SET NULL,
  CONSTRAINT `credit_limits_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `credit_limits_lampiran_d_version_id_foreign` FOREIGN KEY (`lampiran_d_version_id`) REFERENCES `lampiran_d_versions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `credit_limits_recommendation_id_foreign` FOREIGN KEY (`recommendation_id`) REFERENCES `bg_recommendations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.credit_limits: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.customers
CREATE TABLE IF NOT EXISTS `customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_class` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_group` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_to_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_to_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchasing_manager_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchasing_manager_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `finance_manager_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `finance_manager_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penagihan_nama_kontak` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penagihan_telepon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penagihan_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `surat_menyurat_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_contact_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_contact_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_contact_phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `npwp` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_npwp` date DEFAULT NULL,
  `nppkp` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_nppkp` date DEFAULT NULL,
  `no_pengukuhan_kaber` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `output_tax` enum('PPN','NON-PPN','Terhutang PPN') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `term_of_payment` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lead_time` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credit_limit` decimal(18,2) DEFAULT NULL,
  `ccar` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_garansi` enum('YA','TIDAK') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `area` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `route_to` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_approval` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_code_unique` (`code`),
  KEY `customers_user_id_foreign` (`user_id`),
  CONSTRAINT `customers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.customers: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.customer_classes
CREATE TABLE IF NOT EXISTS `customer_classes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name_class` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.customer_classes: ~0 rows (approximately)
INSERT INTO `customer_classes` (`id`, `name_class`, `created_at`, `updated_at`) VALUES
	(1, 'Bakery', '2025-11-25 02:12:23', '2025-11-25 02:12:23');

-- Dumping structure for table requisition_slip_test.customer_files
CREATE TABLE IF NOT EXISTS `customer_files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `npwp_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nib_siup_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ktp_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_files_customer_id_foreign` (`customer_id`),
  CONSTRAINT `customer_files_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.customer_files: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.departments
CREATE TABLE IF NOT EXISTS `departments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departments_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.departments: ~9 rows (approximately)
INSERT INTO `departments` (`id`, `name`, `code`, `slug`, `created_at`, `updated_at`) VALUES
	(1, 'Engineering & Maintenance', '-', 'engineering-maintenance', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(2, 'Finance Admin', '-', 'finance-admin', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(3, 'HCD', '-', 'hcd', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(4, 'Manufacturing', '-', 'manufacturing', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(5, 'QM & HSE', '5302', 'qm-hse', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(6, 'R&D', '5302', 'rd', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(7, 'Sales & Marketing', '5300', 'sales-marketing', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(8, 'Supply Chain', '-', 'supply-chain', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(9, 'Supply & Maintenance', '-', 'supply-maintenance', '2025-11-10 08:34:27', '2025-11-10 08:34:27');

-- Dumping structure for table requisition_slip_test.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.failed_jobs: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.item_details
CREATE TABLE IF NOT EXISTS `item_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_master_id` bigint unsigned NOT NULL,
  `material_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_detail_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_detail_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `net_weight` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `item_details_item_detail_code_unique` (`item_detail_code`),
  KEY `item_details_item_master_id_foreign` (`item_master_id`),
  CONSTRAINT `item_details_item_master_id_foreign` FOREIGN KEY (`item_master_id`) REFERENCES `item_masters` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=251 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.item_details: ~250 rows (approximately)
INSERT INTO `item_details` (`id`, `item_master_id`, `material_type`, `item_detail_code`, `item_detail_name`, `unit`, `net_weight`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Finished', 'ID-1370', 'iure repellendus', 'L', 10.96, '2025-11-10 08:34:33', '2025-11-14 03:03:48'),
	(2, 1, 'Semi-Finished', 'ID-7217', 'sint tempora', 'PCS', 93.56, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(3, 1, 'Semi-Finished', 'ID-7245', 'mollitia dolor', 'L', 70.53, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(4, 1, 'Finished', 'ID-9116', 'laborum omnis', 'KG', 96.21, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(5, 1, 'Raw', 'ID-1657', 'architecto itaque', 'PCS', 82.25, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(6, 2, 'Semi-Finished', 'ID-7600', 'odio distinctio', 'KG', 68.38, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(7, 2, 'Finished', 'ID-2051', 'rem numquam', 'PCS', 17.01, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(8, 2, 'Semi-Finished', 'ID-5766', 'aut sed', 'BOX', 40.29, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(9, 2, 'Semi-Finished', 'ID-5928', 'quas nihil', 'L', 72.96, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(10, 2, 'Finished', 'ID-5862', 'est consequuntur', 'KG', 65.66, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(11, 3, 'Raw', 'ID-6995', 'dicta nostrum', 'KG', 9.21, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(12, 3, 'Finished', 'ID-2283', 'debitis consequuntur', 'PCS', 60.91, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(13, 3, 'Raw', 'ID-9986', 'illo totam', 'PCS', 36.39, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(14, 3, 'Raw', 'ID-2958', 'optio enim', 'KG', 49.40, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(15, 3, 'Semi-Finished', 'ID-6379', 'sit laboriosam', 'PCS', 43.84, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(16, 4, 'Finished', 'ID-8387', 'vitae omnis', 'BOX', 11.58, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(17, 4, 'Semi-Finished', 'ID-2803', 'voluptatem unde', 'PCS', 26.38, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(18, 4, 'Finished', 'ID-8890', 'nihil ex', 'PCS', 95.35, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(19, 4, 'Raw', 'ID-5102', 'non et', 'KG', 48.56, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(20, 4, 'Semi-Finished', 'ID-5688', 'incidunt in', 'BOX', 64.73, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(21, 5, 'Raw', 'ID-3914', 'ipsa culpa', 'L', 23.31, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(22, 5, 'Semi-Finished', 'ID-5475', 'ut repellendus', 'BOX', 39.83, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(23, 5, 'Semi-Finished', 'ID-0679', 'in ullam', 'L', 71.36, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(24, 5, 'Semi-Finished', 'ID-9485', 'cupiditate eligendi', 'L', 7.62, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(25, 5, 'Semi-Finished', 'ID-6619', 'in repudiandae', 'BOX', 41.52, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(26, 6, 'Raw', 'ID-1065', 'aspernatur debitis', 'BOX', 11.87, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(27, 6, 'Finished', 'ID-5149', 'qui nisi', 'BOX', 12.59, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(28, 6, 'Semi-Finished', 'ID-4924', 'et similique', 'PCS', 2.27, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(29, 6, 'Semi-Finished', 'ID-3009', 'consequatur repellendus', 'PCS', 99.24, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(30, 6, 'Semi-Finished', 'ID-7306', 'deleniti dolor', 'L', 69.85, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(31, 7, 'Raw', 'ID-9348', 'quae ipsam', 'KG', 89.01, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(32, 7, 'Semi-Finished', 'ID-7193', 'sed dolore', 'PCS', 72.66, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(33, 7, 'Raw', 'ID-2987', 'sed ut', 'BOX', 90.70, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(34, 7, 'Raw', 'ID-7997', 'minima placeat', 'BOX', 50.65, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(35, 7, 'Semi-Finished', 'ID-5767', 'cumque ut', 'BOX', 24.29, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(36, 8, 'Finished', 'ID-5630', 'beatae quos', 'KG', 59.45, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(37, 8, 'Semi-Finished', 'ID-0949', 'modi sequi', 'PCS', 48.17, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(38, 8, 'Finished', 'ID-4562', 'molestias voluptatem', 'PCS', 53.30, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(39, 8, 'Semi-Finished', 'ID-4724', 'aut natus', 'L', 23.43, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(40, 8, 'Finished', 'ID-8573', 'molestiae enim', 'PCS', 47.48, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(41, 9, 'Semi-Finished', 'ID-1034', 'dolorum laborum', 'PCS', 65.42, '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(42, 9, 'Finished', 'ID-6979', 'qui mollitia', 'PCS', 41.47, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(43, 9, 'Raw', 'ID-3397', 'quia similique', 'BOX', 13.25, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(44, 9, 'Raw', 'ID-9026', 'laudantium aspernatur', 'L', 39.35, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(45, 9, 'Raw', 'ID-5191', 'molestias consequatur', 'BOX', 85.58, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(51, 11, 'Semi-Finished', 'ID-1840', 'aut et', 'L', 27.44, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(52, 11, 'Semi-Finished', 'ID-4349', 'aliquam ut', 'L', 5.37, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(53, 11, 'Semi-Finished', 'ID-7194', 'dolorem eaque', 'KG', 28.75, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(54, 11, 'Finished', 'ID-9279', 'aut unde', 'KG', 42.92, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(55, 11, 'Semi-Finished', 'ID-4351', 'hic harum', 'BOX', 71.25, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(56, 12, 'Raw', 'ID-6813', 'consequuntur omnis', 'BOX', 52.90, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(57, 12, 'Finished', 'ID-3051', 'officiis maxime', 'KG', 45.59, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(58, 12, 'Raw', 'ID-7274', 'quas itaque', 'PCS', 23.54, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(59, 12, 'Finished', 'ID-2630', 'quam velit', 'KG', 50.85, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(60, 12, 'Raw', 'ID-5328', 'sit provident', 'KG', 85.39, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(61, 13, 'Raw', 'ID-9093', 'corrupti repellendus', 'L', 6.31, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(62, 13, 'Raw', 'ID-8082', 'aut eum', 'PCS', 24.22, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(63, 13, 'Finished', 'ID-3725', 'fugit at', 'BOX', 85.99, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(64, 13, 'Finished', 'ID-8922', 'nisi expedita', 'PCS', 10.16, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(65, 13, 'Raw', 'ID-1659', 'in minima', 'L', 62.63, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(66, 14, 'Finished', 'ID-4909', 'error aut', 'PCS', 78.58, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(67, 14, 'Semi-Finished', 'ID-4975', 'assumenda et', 'L', 69.65, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(68, 14, 'Semi-Finished', 'ID-3368', 'itaque officiis', 'KG', 19.90, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(69, 14, 'Finished', 'ID-9763', 'illum voluptatum', 'PCS', 2.94, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(70, 14, 'Raw', 'ID-3606', 'ut sequi', 'KG', 28.25, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(71, 15, 'Semi-Finished', 'ID-2663', 'esse eius', 'KG', 21.01, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(72, 15, 'Semi-Finished', 'ID-6293', 'voluptas repudiandae', 'KG', 0.17, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(73, 15, 'Raw', 'ID-5341', 'optio in', 'KG', 1.73, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(74, 15, 'Raw', 'ID-9161', 'expedita vero', 'BOX', 39.50, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(75, 15, 'Finished', 'ID-7417', 'voluptates est', 'PCS', 86.06, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(76, 16, 'Semi-Finished', 'ID-1675', 'architecto est', 'PCS', 18.39, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(77, 16, 'Finished', 'ID-0098', 'quia in', 'L', 85.63, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(78, 16, 'Finished', 'ID-2150', 'voluptatem repellat', 'L', 20.15, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(79, 16, 'Finished', 'ID-5050', 'mollitia excepturi', 'BOX', 48.97, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(80, 16, 'Finished', 'ID-2524', 'rerum sequi', 'BOX', 54.16, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(81, 17, 'Raw', 'ID-6304', 'omnis repellendus', 'KG', 8.39, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(82, 17, 'Semi-Finished', 'ID-7047', 'in non', 'KG', 6.50, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(83, 17, 'Semi-Finished', 'ID-4363', 'quod beatae', 'BOX', 46.63, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(84, 17, 'Finished', 'ID-4020', 'est et', 'BOX', 25.29, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(85, 17, 'Finished', 'ID-9273', 'perferendis consequuntur', 'L', 52.74, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(86, 18, 'Semi-Finished', 'ID-6252', 'quia molestiae', 'BOX', 2.90, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(87, 18, 'Finished', 'ID-7843', 'repellat et', 'KG', 87.70, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(88, 18, 'Raw', 'ID-5427', 'sunt non', 'PCS', 1.29, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(89, 18, 'Finished', 'ID-7166', 'recusandae sapiente', 'PCS', 68.89, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(90, 18, 'Finished', 'ID-2109', 'saepe reprehenderit', 'PCS', 2.15, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(91, 19, 'Semi-Finished', 'ID-6901', 'debitis magnam', 'KG', 55.15, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(92, 19, 'Finished', 'ID-6565', 'dolorum expedita', 'KG', 99.37, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(93, 19, 'Finished', 'ID-8936', 'sit aut', 'PCS', 69.19, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(94, 19, 'Raw', 'ID-5666', 'similique quia', 'BOX', 98.71, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(95, 19, 'Finished', 'ID-2188', 'placeat et', 'BOX', 95.13, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(96, 20, 'Raw', 'ID-5172', 'cupiditate omnis', 'L', 22.41, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(97, 20, 'Raw', 'ID-8142', 'doloremque deserunt', 'L', 42.72, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(98, 20, 'Finished', 'ID-5499', 'quo recusandae', 'PCS', 67.27, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(99, 20, 'Finished', 'ID-7443', 'qui eos', 'KG', 9.90, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(100, 20, 'Finished', 'ID-2460', 'laudantium porro', 'BOX', 44.26, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(101, 21, 'Semi-Finished', 'ID-5123', 'aut quam', 'PCS', 81.48, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(102, 21, 'Semi-Finished', 'ID-4735', 'repellendus veritatis', 'KG', 3.84, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(103, 21, 'Semi-Finished', 'ID-2665', 'recusandae earum', 'PCS', 47.22, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(104, 21, 'Semi-Finished', 'ID-2204', 'et voluptas', 'KG', 12.05, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(105, 21, 'Semi-Finished', 'ID-8145', 'dolor aspernatur', 'PCS', 40.72, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(106, 22, 'Semi-Finished', 'ID-4049', 'odio voluptatem', 'KG', 48.78, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(107, 22, 'Semi-Finished', 'ID-2133', 'eum dicta', 'PCS', 68.69, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(108, 22, 'Semi-Finished', 'ID-0444', 'dolorem deleniti', 'BOX', 86.83, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(109, 22, 'Semi-Finished', 'ID-0726', 'voluptas hic', 'L', 96.08, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(110, 22, 'Raw', 'ID-1306', 'nam minima', 'L', 95.69, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(111, 23, 'Semi-Finished', 'ID-6635', 'beatae nam', 'KG', 64.57, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(112, 23, 'Finished', 'ID-2963', 'laborum aut', 'KG', 85.51, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(113, 23, 'Semi-Finished', 'ID-2438', 'et cupiditate', 'KG', 95.91, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(114, 23, 'Finished', 'ID-8588', 'autem dolores', 'KG', 77.93, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(115, 23, 'Finished', 'ID-7842', 'ut aut', 'KG', 37.29, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(116, 24, 'Raw', 'ID-1284', 'illo rerum', 'BOX', 69.91, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(117, 24, 'Finished', 'ID-1086', 'molestiae dolorem', 'KG', 23.74, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(118, 24, 'Raw', 'ID-5613', 'quas earum', 'PCS', 46.58, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(119, 24, 'Finished', 'ID-7767', 'veritatis et', 'L', 3.85, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(120, 24, 'Semi-Finished', 'ID-4007', 'ab vel', 'BOX', 99.70, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(121, 25, 'Semi-Finished', 'ID-8071', 'placeat est', 'BOX', 69.38, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(122, 25, 'Finished', 'ID-3135', 'cum quis', 'L', 50.42, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(123, 25, 'Finished', 'ID-0087', 'sint ea', 'BOX', 34.89, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(124, 25, 'Finished', 'ID-4330', 'labore nesciunt', 'L', 16.34, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(125, 25, 'Finished', 'ID-0288', 'sunt voluptatem', 'PCS', 73.97, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(126, 26, 'Raw', 'ID-0260', 'nemo suscipit', 'BOX', 82.42, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(127, 26, 'Raw', 'ID-3615', 'quis aperiam', 'KG', 6.62, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(128, 26, 'Raw', 'ID-7986', 'sunt quia', 'KG', 74.02, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(129, 26, 'Finished', 'ID-5719', 'voluptatem assumenda', 'L', 16.39, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(130, 26, 'Semi-Finished', 'ID-6894', 'fuga voluptatum', 'BOX', 66.10, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(131, 27, 'Finished', 'ID-0033', 'impedit ut', 'L', 91.37, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(132, 27, 'Raw', 'ID-7344', 'iste sunt', 'PCS', 78.52, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(133, 27, 'Semi-Finished', 'ID-1980', 'aspernatur nesciunt', 'L', 22.16, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(134, 27, 'Raw', 'ID-9333', 'aut aperiam', 'L', 93.26, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(135, 27, 'Semi-Finished', 'ID-8623', 'fugit ea', 'KG', 29.15, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(136, 28, 'Finished', 'ID-8149', 'itaque iste', 'KG', 43.18, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(137, 28, 'Raw', 'ID-4226', 'quia voluptatem', 'BOX', 17.48, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(138, 28, 'Finished', 'ID-9049', 'ratione cum', 'KG', 78.80, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(139, 28, 'Finished', 'ID-1519', 'ullam illo', 'L', 43.59, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(140, 28, 'Raw', 'ID-7711', 'pariatur consectetur', 'PCS', 31.07, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(141, 29, 'Finished', 'ID-3567', 'aspernatur enim', 'KG', 7.61, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(142, 29, 'Raw', 'ID-4520', 'dolore voluptas', 'PCS', 36.87, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(143, 29, 'Finished', 'ID-1201', 'animi impedit', 'BOX', 70.82, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(144, 29, 'Finished', 'ID-6587', 'magnam in', 'L', 37.89, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(145, 29, 'Finished', 'ID-4302', 'minima et', 'KG', 19.06, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(146, 30, 'Semi-Finished', 'ID-3181', 'quia et', 'KG', 19.08, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(147, 30, 'Semi-Finished', 'ID-2435', 'possimus adipisci', 'BOX', 62.05, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(148, 30, 'Semi-Finished', 'ID-2727', 'hic qui', 'L', 76.76, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(149, 30, 'Finished', 'ID-8543', 'vero qui', 'L', 30.69, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(150, 30, 'Semi-Finished', 'ID-1699', 'nihil rerum', 'PCS', 71.37, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(151, 31, 'Finished', 'ID-0992', 'fugiat at', 'L', 15.86, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(152, 31, 'Semi-Finished', 'ID-5969', 'accusantium impedit', 'KG', 73.50, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(153, 31, 'Semi-Finished', 'ID-4743', 'ipsa quia', 'PCS', 26.99, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(154, 31, 'Raw', 'ID-9526', 'deleniti veritatis', 'KG', 1.05, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(155, 31, 'Raw', 'ID-7830', 'distinctio et', 'PCS', 94.70, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(156, 32, 'Semi-Finished', 'ID-9513', 'inventore nesciunt', 'L', 91.10, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(157, 32, 'Finished', 'ID-8384', 'assumenda officia', 'L', 62.52, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(158, 32, 'Finished', 'ID-2482', 'quia corrupti', 'L', 2.76, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(159, 32, 'Semi-Finished', 'ID-0213', 'corrupti animi', 'KG', 41.36, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(160, 32, 'Raw', 'ID-1876', 'veniam ut', 'KG', 88.12, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(161, 33, 'Semi-Finished', 'ID-4501', 'et aut', 'BOX', 70.76, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(162, 33, 'Finished', 'ID-0618', 'fugiat necessitatibus', 'KG', 97.92, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(163, 33, 'Finished', 'ID-5101', 'veritatis deleniti', 'BOX', 25.56, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(164, 33, 'Finished', 'ID-7444', 'dignissimos sit', 'BOX', 84.71, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(165, 33, 'Raw', 'ID-8360', 'et hic', 'KG', 97.95, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(166, 34, 'Finished', 'ID-5714', 'excepturi reprehenderit', 'L', 1.16, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(167, 34, 'Raw', 'ID-1783', 'pariatur explicabo', 'L', 96.13, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(168, 34, 'Semi-Finished', 'ID-3934', 'quo in', 'PCS', 69.54, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(169, 34, 'Finished', 'ID-9754', 'eum qui', 'KG', 89.70, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(170, 34, 'Finished', 'ID-5020', 'doloremque delectus', 'KG', 41.97, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(171, 35, 'Raw', 'ID-7282', 'voluptatibus commodi', 'L', 21.77, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(172, 35, 'Raw', 'ID-4468', 'maxime est', 'L', 99.79, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(173, 35, 'Raw', 'ID-9137', 'porro molestiae', 'BOX', 15.17, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(174, 35, 'Semi-Finished', 'ID-4556', 'voluptatem sed', 'BOX', 80.77, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(175, 35, 'Semi-Finished', 'ID-0064', 'neque perferendis', 'L', 26.39, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(176, 36, 'Raw', 'ID-8603', 'ipsum rerum', 'KG', 23.83, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(177, 36, 'Semi-Finished', 'ID-7633', 'molestias ab', 'BOX', 80.36, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(178, 36, 'Finished', 'ID-4018', 'exercitationem libero', 'L', 34.71, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(179, 36, 'Raw', 'ID-5187', 'minima harum', 'PCS', 5.55, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(180, 36, 'Finished', 'ID-6489', 'iure exercitationem', 'BOX', 21.23, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(181, 37, 'Finished', 'ID-9443', 'quaerat repellendus', 'L', 92.94, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(182, 37, 'Raw', 'ID-9653', 'dolorum ut', 'PCS', 56.30, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(183, 37, 'Raw', 'ID-5171', 'culpa magni', 'BOX', 7.92, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(184, 37, 'Semi-Finished', 'ID-2333', 'dignissimos neque', 'BOX', 20.27, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(185, 37, 'Finished', 'ID-8636', 'necessitatibus quia', 'L', 1.28, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(186, 38, 'Semi-Finished', 'ID-9608', 'rerum velit', 'KG', 97.87, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(187, 38, 'Raw', 'ID-7946', 'eum culpa', 'KG', 67.26, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(188, 38, 'Finished', 'ID-0658', 'expedita aut', 'KG', 65.20, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(189, 38, 'Finished', 'ID-1998', 'exercitationem a', 'BOX', 23.14, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(190, 38, 'Semi-Finished', 'ID-1842', 'numquam eligendi', 'BOX', 31.56, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(191, 39, 'Raw', 'ID-6927', 'dicta vel', 'L', 95.56, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(192, 39, 'Raw', 'ID-2299', 'quis alias', 'L', 66.44, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(193, 39, 'Semi-Finished', 'ID-4885', 'dignissimos sed', 'PCS', 24.43, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(194, 39, 'Raw', 'ID-6593', 'dolorum nostrum', 'BOX', 74.09, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(195, 39, 'Finished', 'ID-3689', 'ducimus maiores', 'KG', 83.96, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(196, 40, 'Raw', 'ID-6661', 'non fuga', 'PCS', 81.38, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(197, 40, 'Raw', 'ID-5919', 'voluptate non', 'KG', 27.82, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(198, 40, 'Raw', 'ID-1097', 'dolore necessitatibus', 'KG', 59.15, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(199, 40, 'Raw', 'ID-4263', 'et ex', 'PCS', 74.85, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(200, 40, 'Semi-Finished', 'ID-3169', 'cum suscipit', 'BOX', 14.25, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(201, 41, 'Raw', 'ID-6763', 'iusto eum', 'PCS', 51.84, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(202, 41, 'Finished', 'ID-6713', 'quia incidunt', 'BOX', 35.79, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(203, 41, 'Semi-Finished', 'ID-8842', 'dolores commodi', 'BOX', 52.35, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(204, 41, 'Raw', 'ID-5110', 'et sit', 'BOX', 63.85, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(205, 41, 'Semi-Finished', 'ID-5907', 'itaque vel', 'KG', 78.54, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(206, 42, 'Raw', 'ID-1237', 'provident nulla', 'L', 88.39, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(207, 42, 'Semi-Finished', 'ID-7624', 'similique officiis', 'BOX', 4.66, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(208, 42, 'Raw', 'ID-9950', 'sed neque', 'L', 44.61, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(209, 42, 'Raw', 'ID-0014', 'libero quis', 'L', 80.52, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(210, 42, 'Semi-Finished', 'ID-5859', 'ipsam magni', 'KG', 76.99, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(211, 43, 'Finished', 'ID-3151', 'doloribus cumque', 'PCS', 13.46, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(212, 43, 'Finished', 'ID-4334', 'officia consequatur', 'L', 4.17, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(213, 43, 'Raw', 'ID-6436', 'non vero', 'L', 1.38, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(214, 43, 'Semi-Finished', 'ID-7629', 'error sapiente', 'KG', 5.47, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(215, 43, 'Finished', 'ID-9107', 'sed culpa', 'L', 95.58, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(216, 44, 'Raw', 'ID-0022', 'at voluptatem', 'BOX', 20.18, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(217, 44, 'Finished', 'ID-0091', 'nobis consequatur', 'KG', 40.45, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(218, 44, 'Semi-Finished', 'ID-7618', 'aut quaerat', 'L', 21.36, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(219, 44, 'Finished', 'ID-4095', 'eveniet in', 'L', 61.80, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(220, 44, 'Finished', 'ID-4072', 'quia corrupti', 'L', 8.78, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(221, 45, 'Semi-Finished', 'ID-7828', 'ea quia', 'L', 31.16, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(222, 45, 'Semi-Finished', 'ID-2911', 'et repellendus', 'PCS', 6.40, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(223, 45, 'Semi-Finished', 'ID-5234', 'et perspiciatis', 'PCS', 0.66, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(224, 45, 'Semi-Finished', 'ID-0190', 'sit quas', 'L', 30.74, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(225, 45, 'Semi-Finished', 'ID-0713', 'beatae quidem', 'L', 8.80, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(226, 46, 'Finished', 'ID-0161', 'rem quis', 'KG', 35.24, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(227, 46, 'Raw', 'ID-6538', 'vero omnis', 'KG', 45.64, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(228, 46, 'Semi-Finished', 'ID-0937', 'numquam voluptatem', 'KG', 96.67, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(229, 46, 'Finished', 'ID-8528', 'numquam quaerat', 'KG', 71.36, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(230, 46, 'Raw', 'ID-4726', 'id ea', 'L', 41.62, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(231, 47, 'Finished', 'ID-4482', 'voluptatibus eius', 'L', 82.52, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(232, 47, 'Raw', 'ID-1816', 'at aut', 'KG', 36.61, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(233, 47, 'Semi-Finished', 'ID-4613', 'autem magni', 'BOX', 66.33, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(234, 47, 'Semi-Finished', 'ID-6841', 'necessitatibus qui', 'BOX', 20.24, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(235, 47, 'Semi-Finished', 'ID-1546', 'occaecati eum', 'BOX', 10.35, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(236, 48, 'Raw', 'ID-3376', 'est nisi', 'BOX', 96.12, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(237, 48, 'Raw', 'ID-7805', 'molestias voluptatum', 'PCS', 73.69, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(238, 48, 'Finished', 'ID-6626', 'ea debitis', 'KG', 2.27, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(239, 48, 'Finished', 'ID-2469', 'consequatur aliquam', 'PCS', 5.24, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(240, 48, 'Semi-Finished', 'ID-1975', 'omnis sit', 'BOX', 85.20, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(241, 49, 'Finished', 'ID-3040', 'iusto aliquid', 'PCS', 11.11, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(242, 49, 'Finished', 'ID-6929', 'quam illum', 'L', 6.18, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(243, 49, 'Semi-Finished', 'ID-5457', 'provident quibusdam', 'KG', 72.15, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(244, 49, 'Raw', 'ID-8995', 'ducimus rem', 'L', 47.22, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(245, 49, 'Semi-Finished', 'ID-9027', 'quidem est', 'KG', 23.67, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(246, 50, 'Semi-Finished', 'ID-5483', 'alias sit', 'PCS', 59.91, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(247, 50, 'Semi-Finished', 'ID-0380', 'culpa velit', 'KG', 33.40, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(248, 50, 'Semi-Finished', 'ID-7995', 'provident officiis', 'BOX', 80.58, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(249, 50, 'Semi-Finished', 'ID-5826', 'et reiciendis', 'L', 18.38, '2025-11-10 08:34:34', '2025-11-10 08:34:34'),
	(250, 50, 'Finished', 'ID-9194', 'quia voluptas', 'L', 31.65, '2025-11-10 08:34:34', '2025-11-10 08:34:34');

-- Dumping structure for table requisition_slip_test.item_masters
CREATE TABLE IF NOT EXISTS `item_masters` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_master_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_master_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `item_masters_item_master_code_unique` (`item_master_code`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.item_masters: ~50 rows (approximately)
INSERT INTO `item_masters` (`id`, `item_master_code`, `item_master_name`, `unit`, `created_at`, `updated_at`) VALUES
	(1, 'IM-62744', 'animi nulla sit', 'BOX', '2025-11-10 08:34:33', '2025-11-13 09:16:03'),
	(2, 'IM-058', 'ut beatae voluptates', 'PCS', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(3, 'IM-256', 'accusamus delectus modi', 'PCS', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(4, 'IM-701', 'qui laborum est', 'KG', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(5, 'IM-831', 'eum et animi', 'PCS', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(6, 'IM-773', 'veritatis exercitationem ipsam', 'L', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(7, 'IM-275', 'iste ea ab', 'PCS', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(8, 'IM-499', 'voluptas omnis quam', 'L', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(9, 'IM-853', 'qui corporis similique', 'L', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(11, 'IM-536', 'officiis quibusdam architecto', 'KG', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(12, 'IM-119', 'aliquid molestiae inventore', 'L', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(13, 'IM-693', 'cum sequi asperiores', 'L', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(14, 'IM-502', 'molestiae est sunt', 'BOX', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(15, 'IM-899', 'enim accusamus aspernatur', 'BOX', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(16, 'IM-336', 'sit dolorem pariatur', 'KG', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(17, 'IM-387', 'nobis nobis ut', 'L', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(18, 'IM-308', 'quidem consequuntur et', 'PCS', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(19, 'IM-287', 'nihil aut ab', 'PCS', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(20, 'IM-474', 'quasi aut a', 'PCS', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(21, 'IM-184', 'cum ut et', 'L', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(22, 'IM-197', 'sint officiis perferendis', 'L', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(23, 'IM-598', 'non numquam repellendus', 'PCS', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(24, 'IM-303', 'eligendi illo incidunt', 'BOX', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(25, 'IM-549', 'eum doloribus voluptate', 'KG', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(26, 'IM-459', 'voluptates quidem suscipit', 'KG', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(27, 'IM-445', 'voluptatem esse aperiam', 'KG', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(28, 'IM-700', 'ducimus explicabo assumenda', 'BOX', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(29, 'IM-870', 'magni corporis ut', 'BOX', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(30, 'IM-508', 'a officia ipsa', 'PCS', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(31, 'IM-079', 'et et et', 'BOX', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(32, 'IM-571', 'at qui voluptatem', 'KG', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(33, 'IM-843', 'est vero ut', 'L', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(34, 'IM-672', 'consequatur et consectetur', 'KG', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(35, 'IM-350', 'quae ad nostrum', 'KG', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(36, 'IM-174', 'et porro et', 'PCS', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(37, 'IM-232', 'quo minus repellat', 'PCS', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(38, 'IM-757', 'eos soluta corrupti', 'KG', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(39, 'IM-967', 'hic enim doloremque', 'PCS', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(40, 'IM-577', 'saepe deleniti debitis', 'BOX', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(41, 'IM-722', 'vel et commodi', 'PCS', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(42, 'IM-692', 'explicabo enim maxime', 'KG', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(43, 'IM-145', 'est numquam neque', 'PCS', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(44, 'IM-108', 'et doloribus vero', 'PCS', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(45, 'IM-223', 'velit commodi velit', 'KG', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(46, 'IM-636', 'nemo qui est', 'BOX', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(47, 'IM-818', 'unde quia impedit', 'PCS', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(48, 'IM-604', 'quasi et beatae', 'PCS', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(49, 'IM-187', 'voluptatum quibusdam mollitia', 'PCS', '2025-11-10 08:34:33', '2025-11-10 08:34:33'),
	(50, 'IM-409', 'minus illo sunt', 'KG', '2025-11-10 08:34:33', '2025-11-10 08:34:33');

-- Dumping structure for table requisition_slip_test.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.jobs: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
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

-- Dumping data for table requisition_slip_test.job_batches: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.lampiran_d
CREATE TABLE IF NOT EXISTS `lampiran_d` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `bg_submission_id` bigint unsigned NOT NULL,
  `version_latest` int NOT NULL DEFAULT '0',
  `active_version_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lampiran_d_bg_submission_id_foreign` (`bg_submission_id`),
  KEY `lampiran_d_created_by_foreign` (`created_by`),
  KEY `lampiran_d_active_version_id_foreign` (`active_version_id`),
  CONSTRAINT `lampiran_d_active_version_id_foreign` FOREIGN KEY (`active_version_id`) REFERENCES `lampiran_d_versions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lampiran_d_bg_submission_id_foreign` FOREIGN KEY (`bg_submission_id`) REFERENCES `bg_submissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lampiran_d_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.lampiran_d: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.lampiran_d_versions
CREATE TABLE IF NOT EXISTS `lampiran_d_versions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lampiran_d_id` bigint unsigned NOT NULL,
  `version_no` int NOT NULL DEFAULT '1',
  `data_snapshot` json DEFAULT NULL,
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `generated_by` bigint unsigned DEFAULT NULL,
  `generated_at` timestamp NULL DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lampiran_d_versions_lampiran_d_id_foreign` (`lampiran_d_id`),
  KEY `lampiran_d_versions_generated_by_foreign` (`generated_by`),
  CONSTRAINT `lampiran_d_versions_generated_by_foreign` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lampiran_d_versions_lampiran_d_id_foreign` FOREIGN KEY (`lampiran_d_id`) REFERENCES `lampiran_d` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.lampiran_d_versions: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.migrations: ~53 rows (approximately)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_department', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '0001_01_01_000003_create_users_table', 1),
	(5, '2025_09_03_044709_add_atasan_nik_to_table_user', 1),
	(6, '2025_09_03_045822_create_permission_tables', 1),
	(9, '2025_09_03_063947_create_requisitions_table', 1),
	(10, '2025_09_03_070300_create_item_masters_table', 1),
	(11, '2025_09_03_070413_create_item_details_table', 1),
	(12, '2025_09_03_070834_create_requisition_items_table', 1),
	(13, '2025_09_03_071018_create_approval_logs_table', 1),
	(14, '2025_09_03_071206_create_payments_table', 1),
	(15, '2025_09_03_071317_create_trackings_table', 1),
	(16, '2025_09_03_071357_create_approval_paths_table', 1),
	(17, '2025_09_03_075433_create_requisition_specials_table', 1),
	(21, '2025_09_08_074738_add_status_to_users', 1),
	(22, '2025_09_22_034309_add_approved_at_to_approval_logs_table', 1),
	(23, '2025_09_29_091923_add_printbatch_into_requisition', 1),
	(24, '2025_09_29_101023_create_complain_images_table', 1),
	(25, '2025_10_03_141756_add_token_to_trackings_table', 1),
	(26, '2025_10_09_000000_add_status_to_trackings_table', 1),
	(27, '2025_10_09_000001_drop_status_from_trackings_table', 1),
	(28, '2025_10_10_103149_change_batch_number_type_in_requisition_items_table', 1),
	(29, '2025_10_13_104325_change_sample_count_to_string_in_requisition_specials_table', 1),
	(30, '2025_10_16_155504_create_notifications_table', 1),
	(50, '2025_09_03_063505_create_customers_table', 2),
	(59, '2025_09_03_063626_create_revisions_table', 3),
	(60, '2025_11_20_120000_create_customer_files_table', 3),
	(61, '2025_11_20_120100_create_bank_garansi_table', 3),
	(62, '2025_11_20_120110_create_bg_details_table', 3),
	(63, '2025_11_20_120120_create_bg_recommendations_table', 3),
	(64, '2025_11_20_120130_create_bg_submissions_table', 3),
	(65, '2025_11_20_120140_create_lampiran_d_table', 3),
	(66, '2025_11_20_120141_create_lampiran_d_versions_table', 3),
	(67, '2025_11_20_120143_create_bg_histories_table', 3),
	(68, '2025_11_20_120144_create_credit_limits_table', 3),
	(69, '2025_11_20_120145_create_bg_limit_rules_table', 3),
	(70, '2025_11_20_120150_create_approvers_table', 3),
	(71, '2025_11_20_120151_create_approval_logs_table', 3),
	(77, '2025_11_24_112659_create_regions_table', 4),
	(78, '2025_11_24_112709_create_branches_table', 4),
	(79, '2025_11_24_112737_create_regions_table', 5),
	(80, '2025_11_24_112757_create_account_groups_table', 5),
	(81, '2025_11_24_112823_create_customer_classes_table', 5),
	(82, '2025_11_24_112833_create_sales_table', 5),
	(83, '2025_11_24_113110_create_t_o_p_s_table', 5),
	(84, '2025_11_24_124500_add_contact_fields_to_customers_table', 6),
	(86, '2025_11_24_135301_add_user_id_to_customers_table', 7),
	(87, '2025_11_24_162217_create_positions_table', 8),
	(88, '2025_11_24_162910_add_position_id_to_table_users', 9),
	(89, '2025_09_07_051646_create_activity_log_table', 10),
	(90, '2025_09_07_051647_add_event_column_to_activity_log_table', 11),
	(91, '2025_09_07_051648_add_batch_uuid_column_to_activity_log_table', 12),
	(92, '2025_11_25_091701_add_no_pengukuhan_kaber_to_customer_table', 13),
	(93, '2025_11_27_142805_add_address_penagihan_to_table_customer', 14),
	(94, '2025_11_28_104613_add_route_to_to_table_customer', 15);

-- Dumping structure for table requisition_slip_test.model_has_permissions
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.model_has_permissions: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.model_has_roles
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.model_has_roles: ~30 rows (approximately)
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
	(4, 'App\\Models\\User', 1),
	(5, 'App\\Models\\User', 2),
	(6, 'App\\Models\\User', 3),
	(7, 'App\\Models\\User', 4),
	(8, 'App\\Models\\User', 5),
	(9, 'App\\Models\\User', 6),
	(9, 'App\\Models\\User', 7),
	(10, 'App\\Models\\User', 8),
	(11, 'App\\Models\\User', 9),
	(11, 'App\\Models\\User', 10),
	(12, 'App\\Models\\User', 11),
	(13, 'App\\Models\\User', 12),
	(13, 'App\\Models\\User', 13),
	(14, 'App\\Models\\User', 14),
	(15, 'App\\Models\\User', 15),
	(15, 'App\\Models\\User', 16),
	(16, 'App\\Models\\User', 17),
	(1, 'App\\Models\\User', 18),
	(20, 'App\\Models\\User', 18),
	(2, 'App\\Models\\User', 19),
	(3, 'App\\Models\\User', 20),
	(2, 'App\\Models\\User', 21),
	(2, 'App\\Models\\User', 22),
	(2, 'App\\Models\\User', 23),
	(2, 'App\\Models\\User', 24),
	(20, 'App\\Models\\User', 25),
	(22, 'App\\Models\\User', 26),
	(21, 'App\\Models\\User', 27),
	(23, 'App\\Models\\User', 28),
	(8, 'App\\Models\\User', 29);

-- Dumping structure for table requisition_slip_test.notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` int NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.notifications: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.password_reset_tokens: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.payments
CREATE TABLE IF NOT EXISTS `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `requisition_id` bigint unsigned NOT NULL,
  `payment_date` date NOT NULL,
  `document_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_requisition_id_foreign` (`requisition_id`),
  CONSTRAINT `payments_requisition_id_foreign` FOREIGN KEY (`requisition_id`) REFERENCES `requisitions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.payments: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.permissions: ~38 rows (approximately)
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'view role', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(2, 'create role', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(3, 'update role', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(4, 'delete role', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(5, 'view permission', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(6, 'create permission', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(7, 'update permission', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(8, 'delete permission', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(9, 'view user', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(10, 'create user', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(11, 'update user', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(12, 'delete user', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(13, 'view department', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(14, 'create department', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(15, 'update department', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(16, 'delete department', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(17, 'view requisition', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(18, 'create requisition', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(19, 'update requisition', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(20, 'delete requisition', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(21, 'view approval', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(22, 'view item', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(23, 'create item', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(24, 'update item', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(25, 'delete item', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(26, 'view customer', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(27, 'create customer', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(28, 'update customer', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(29, 'delete customer', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(30, 'view log', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(31, 'view requisition-form', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(32, 'view report', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(33, 'view requisition-approval', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(34, 'view approval-path', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(35, 'view revision', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(36, 'approve requisition', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(37, 'reject requisition', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(38, 'view dashboard', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27');

-- Dumping structure for table requisition_slip_test.positions
CREATE TABLE IF NOT EXISTS `positions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `position_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.positions: ~0 rows (approximately)
INSERT INTO `positions` (`id`, `position_name`, `created_at`, `updated_at`) VALUES
	(1, 'Ass Manager Key Account', '2025-11-24 09:41:58', '2025-11-24 09:41:58');

-- Dumping structure for table requisition_slip_test.regions
CREATE TABLE IF NOT EXISTS `regions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `region_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.regions: ~5 rows (approximately)
INSERT INTO `regions` (`id`, `region_name`, `created_at`, `updated_at`) VALUES
	(1, 'Key Account', '2025-11-24 09:18:14', '2025-11-24 09:18:14'),
	(2, 'Commercial', '2025-11-24 09:18:21', '2025-11-24 09:18:21'),
	(3, 'West', '2025-11-24 09:18:29', '2025-11-24 09:18:29'),
	(4, 'East', '2025-11-24 09:18:35', '2025-11-24 09:18:35'),
	(5, 'Export', '2025-11-24 09:19:37', '2025-11-24 09:19:37');

-- Dumping structure for table requisition_slip_test.requisitions
CREATE TABLE IF NOT EXISTS `requisitions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `requester_nik` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `no_srs` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cost_center` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_date` date NOT NULL,
  `revision_id` bigint unsigned DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sub_category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `route_to` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason_for_replacement` text COLLATE utf8mb4_unicode_ci,
  `objectives` text COLLATE utf8mb4_unicode_ci,
  `estimated_potential` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `print_batch` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `requisitions_revision_id_unique` (`revision_id`),
  KEY `requisitions_customer_id_foreign` (`customer_id`),
  CONSTRAINT `requisitions_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `requisitions_revision_id_foreign` FOREIGN KEY (`revision_id`) REFERENCES `revisions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.requisitions: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.requisition_items
CREATE TABLE IF NOT EXISTS `requisition_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `requisition_id` bigint unsigned NOT NULL,
  `item_master_id` bigint unsigned NOT NULL,
  `item_detail_id` bigint unsigned DEFAULT NULL,
  `material_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity_required` int DEFAULT NULL,
  `quantity_issued` int DEFAULT NULL,
  `batch_number` date DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `requisition_items_requisition_id_foreign` (`requisition_id`),
  KEY `requisition_items_item_master_id_foreign` (`item_master_id`),
  KEY `requisition_items_item_detail_id_foreign` (`item_detail_id`),
  CONSTRAINT `requisition_items_item_detail_id_foreign` FOREIGN KEY (`item_detail_id`) REFERENCES `item_details` (`id`) ON DELETE CASCADE,
  CONSTRAINT `requisition_items_item_master_id_foreign` FOREIGN KEY (`item_master_id`) REFERENCES `item_masters` (`id`) ON DELETE CASCADE,
  CONSTRAINT `requisition_items_requisition_id_foreign` FOREIGN KEY (`requisition_id`) REFERENCES `requisitions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.requisition_items: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.requisition_specials
CREATE TABLE IF NOT EXISTS `requisition_specials` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `requisition_id` bigint unsigned NOT NULL,
  `requested_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `products` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight_selection` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `packaging_selection` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sample_count` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purpose` text COLLATE utf8mb4_unicode_ci,
  `coa_required` tinyint(1) NOT NULL DEFAULT '0',
  `shipment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sample_notes` text COLLATE utf8mb4_unicode_ci,
  `production_date` date DEFAULT NULL,
  `preparation_method` text COLLATE utf8mb4_unicode_ci,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `requisition_specials_requisition_id_foreign` (`requisition_id`),
  CONSTRAINT `requisition_specials_requisition_id_foreign` FOREIGN KEY (`requisition_id`) REFERENCES `requisitions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.requisition_specials: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.revisions
CREATE TABLE IF NOT EXISTS `revisions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `revision_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revision_count` int NOT NULL,
  `revision_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.revisions: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.roles: ~18 rows (approximately)
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'super-admin', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(2, 'user-requisition', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(3, 'user-approval', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(4, 'wh-supervisor', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(5, 'wh-staff', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(6, 'material-supervisor', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(7, 'material-staff', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(8, 'head-SNM', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(9, 'staff-SNM', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(10, 'head-R&D', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(11, 'staff-R&D', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(12, 'head-QA', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(13, 'staff-QA', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(14, 'head-HCD', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(15, 'staff-HCD', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(16, 'atasan', 'web', '2025-11-10 08:34:27', '2025-11-10 08:34:27'),
	(17, 'business-controller', 'web', '2025-11-14 06:48:32', '2025-11-14 06:48:32'),
	(20, 'sales', 'web', '2025-11-24 09:49:49', '2025-11-24 09:49:49'),
	(21, 'finance-manager', 'web', '2025-11-28 07:13:57', '2025-11-28 07:13:57'),
	(22, 'head-FA', 'web', '2025-11-28 07:14:12', '2025-11-28 07:14:12'),
	(23, 'IT', 'web', '2025-11-28 07:14:20', '2025-11-28 07:14:20');

-- Dumping structure for table requisition_slip_test.role_has_permissions
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.role_has_permissions: ~136 rows (approximately)
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
	(1, 1),
	(2, 1),
	(3, 1),
	(4, 1),
	(5, 1),
	(6, 1),
	(7, 1),
	(8, 1),
	(9, 1),
	(10, 1),
	(11, 1),
	(12, 1),
	(13, 1),
	(14, 1),
	(15, 1),
	(16, 1),
	(17, 1),
	(18, 1),
	(19, 1),
	(20, 1),
	(21, 1),
	(22, 1),
	(23, 1),
	(24, 1),
	(25, 1),
	(26, 1),
	(27, 1),
	(28, 1),
	(29, 1),
	(30, 1),
	(31, 1),
	(32, 1),
	(33, 1),
	(34, 1),
	(35, 1),
	(36, 1),
	(37, 1),
	(38, 1),
	(17, 2),
	(18, 2),
	(19, 2),
	(20, 2),
	(30, 2),
	(31, 2),
	(32, 2),
	(21, 3),
	(30, 3),
	(31, 3),
	(32, 3),
	(33, 3),
	(36, 3),
	(37, 3),
	(30, 4),
	(31, 4),
	(32, 4),
	(33, 4),
	(36, 4),
	(37, 4),
	(17, 5),
	(18, 5),
	(19, 5),
	(20, 5),
	(30, 5),
	(31, 5),
	(32, 5),
	(30, 6),
	(31, 6),
	(32, 6),
	(33, 6),
	(36, 6),
	(37, 6),
	(17, 7),
	(18, 7),
	(19, 7),
	(20, 7),
	(30, 7),
	(31, 7),
	(32, 7),
	(30, 8),
	(31, 8),
	(32, 8),
	(33, 8),
	(36, 8),
	(37, 8),
	(17, 9),
	(18, 9),
	(19, 9),
	(20, 9),
	(30, 9),
	(31, 9),
	(32, 9),
	(30, 10),
	(31, 10),
	(32, 10),
	(33, 10),
	(36, 10),
	(37, 10),
	(17, 11),
	(18, 11),
	(19, 11),
	(20, 11),
	(30, 11),
	(31, 11),
	(32, 11),
	(30, 12),
	(31, 12),
	(32, 12),
	(33, 12),
	(36, 12),
	(37, 12),
	(17, 13),
	(18, 13),
	(19, 13),
	(20, 13),
	(30, 13),
	(31, 13),
	(32, 13),
	(30, 14),
	(31, 14),
	(32, 14),
	(33, 14),
	(36, 14),
	(37, 14),
	(17, 15),
	(18, 15),
	(19, 15),
	(20, 15),
	(30, 15),
	(31, 15),
	(32, 15),
	(30, 16),
	(31, 16),
	(32, 16),
	(33, 16),
	(36, 16),
	(37, 16);

-- Dumping structure for table requisition_slip_test.sales
CREATE TABLE IF NOT EXISTS `sales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `account_group_id` bigint unsigned DEFAULT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `region_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_user_id_foreign` (`user_id`),
  KEY `sales_account_group_id_foreign` (`account_group_id`),
  KEY `sales_branch_id_foreign` (`branch_id`),
  KEY `sales_region_id_foreign` (`region_id`),
  CONSTRAINT `sales_account_group_id_foreign` FOREIGN KEY (`account_group_id`) REFERENCES `account_groups` (`id`),
  CONSTRAINT `sales_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `sales_region_id_foreign` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`),
  CONSTRAINT `sales_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.sales: ~0 rows (approximately)
INSERT INTO `sales` (`id`, `user_id`, `account_group_id`, `branch_id`, `region_id`, `created_at`, `updated_at`) VALUES
	(2, 25, 2, 1, 1, '2025-11-24 10:14:44', '2025-11-24 10:14:44'),
	(3, 18, 1, 1, 3, '2025-11-25 03:24:44', '2025-11-25 03:24:44');

-- Dumping structure for table requisition_slip_test.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
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

-- Dumping data for table requisition_slip_test.sessions: ~2 rows (approximately)
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('79BPvJrWQUmGOSbSgrTZLlm0ziqI19jN6Qj4CvgT', 18, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiOER5UkhpRHhKSms1V2p4WDdKOU1yOFJpVlE2cElHM3Myb01ISU82cCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC91c2VycyI7czo1OiJyb3V0ZSI7czoxMToidXNlcnMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxODt9', 1764314996),
	('aKkvbNxKFYk48xp27B6odFCbxOmFJ6qEpCAVGYjQ', 18, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiMVJ4cGdkUXlESTQ2eXFvS3VuZ0FEMGgwZDcyVHpTV0hzaERHbTFLQSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9yZXF1aXNpdGlvbi9wYXRoIjtzOjU6InJvdXRlIjtzOjE2OiJyZXF1aXNpdGlvbi5wYXRoIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE4O30=', 1764322518);

-- Dumping structure for table requisition_slip_test.tops
CREATE TABLE IF NOT EXISTS `tops` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name_top` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `desc_top` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.tops: ~6 rows (approximately)
INSERT INTO `tops` (`id`, `name_top`, `desc_top`, `created_at`, `updated_at`) VALUES
	(1, '21', 'Within 21 Days Due Date', '2025-11-24 09:52:50', '2025-11-24 09:52:50'),
	(2, '60', 'Within 60 Days Due Date', '2025-11-24 09:53:02', '2025-11-24 09:53:02'),
	(3, '45', 'Within 45 Days Due Date', '2025-11-24 09:53:12', '2025-11-24 09:53:12'),
	(4, '15', 'Within 15 Days Due Date', '2025-11-24 09:53:21', '2025-11-24 09:53:21'),
	(5, '7', 'Within 7 Days Due Date', '2025-11-24 09:53:31', '2025-11-24 09:53:31'),
	(6, '10', 'Within 10 Days Due Date', '2025-11-24 09:53:41', '2025-11-24 09:53:41');

-- Dumping structure for table requisition_slip_test.trackings
CREATE TABLE IF NOT EXISTS `trackings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `requisition_id` bigint unsigned NOT NULL,
  `current_position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_updated` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `trackings_token_unique` (`token`),
  KEY `trackings_requisition_id_foreign` (`requisition_id`),
  CONSTRAINT `trackings_requisition_id_foreign` FOREIGN KEY (`requisition_id`) REFERENCES `requisitions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.trackings: ~0 rows (approximately)

-- Dumping structure for table requisition_slip_test.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nik` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department_id` bigint unsigned NOT NULL,
  `position_id` bigint unsigned DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `atasan_nik` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_nik_unique` (`nik`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_department_id_foreign` (`department_id`),
  KEY `users_position_id_foreign` (`position_id`),
  CONSTRAINT `users_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `users_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table requisition_slip_test.users: ~29 rows (approximately)
INSERT INTO `users` (`id`, `nik`, `username`, `email`, `name`, `avatar`, `department_id`, `position_id`, `email_verified_at`, `password`, `status`, `remember_token`, `created_at`, `updated_at`, `atasan_nik`) VALUES
	(1, 'HDWH01', 'head.wh', 'head.wh@example.com', 'Head WH', NULL, 9, NULL, '2025-11-10 08:34:28', '$2y$12$gA.xNK5X3q2dcGwA5DN/1ueNPR01ohWa29sz0yeuidXjR6SA400n.', 'active', NULL, '2025-11-10 08:34:28', '2025-11-10 08:34:28', 'AG1111'),
	(2, 'STWH01', 'staff.wh1', 'staff.wh1@example.com', 'Staff WH 1', NULL, 9, NULL, '2025-11-10 08:34:28', '$2y$12$nEcNd/fwtq9XKl90edREZe1/FZJXcIUBIDK.Uym6zfEorNkq5otnO', 'active', NULL, '2025-11-10 08:34:28', '2025-11-10 08:34:28', 'HDWH01'),
	(3, 'HDMT01', 'head.material', 'head.material@example.com', 'Head Material', NULL, 8, NULL, '2025-11-10 08:34:28', '$2y$12$nJJwvJGTdXGkZw2LsKLNM.UnsymobHlvqGtbdFvxsKp2nOX24S82m', 'active', NULL, '2025-11-10 08:34:28', '2025-11-10 08:34:28', 'AG1111'),
	(4, 'STMT01', 'staff.material1', 'staff.material1@example.com', 'Staff Material 1', NULL, 8, NULL, '2025-11-10 08:34:28', '$2y$12$O.n4u1iU.Cb2fKVBJszHaO9OO0lFZVgYKHWxgQR/DtAPVABddPGV6', 'active', NULL, '2025-11-10 08:34:28', '2025-11-10 08:34:28', 'HDMT01'),
	(5, 'HDSM01', 'head.sales', 'head.sales@example.com', 'Head SNM', NULL, 7, NULL, '2025-11-10 08:34:28', '$2y$12$Qarpdcn4hQz.OLhI6w3QUuo5COcEkB6KkTE.ZKXZMjfIZKxwUDNr2', 'active', NULL, '2025-11-10 08:34:28', '2025-11-10 08:34:28', 'AG1111'),
	(6, 'STSM01', 'staff.sales1', 'staff.sales1@example.com', 'Staff SNM 1', NULL, 7, NULL, '2025-11-10 08:34:29', '$2y$12$OlS7lfd5w3dQ1A1WqtA3pOo18/7XYkXfiNumZBNAtA5LLO5gn6Pxu', 'active', NULL, '2025-11-10 08:34:29', '2025-11-10 08:34:29', 'HDSM01'),
	(7, 'STSM02', 'staff.sales2', 'staff.sales2@example.com', 'Staff SNM 2', NULL, 7, NULL, '2025-11-10 08:34:29', '$2y$12$bDmV43Nmp1NQngxy8yNwcO.o6S/cWcto5T6Y5oFo/aeVKueDAJMK2', 'active', NULL, '2025-11-10 08:34:29', '2025-11-10 08:34:29', 'HDSM01'),
	(8, 'HDRD01', 'head.rnd', 'head.rnd@example.com', 'Head R&D', NULL, 6, NULL, '2025-11-10 08:34:29', '$2y$12$3MctXXYZGXN7Lyua5n65Vu.b5eKsbtlEWnGCHpiNxY5YL4lFyvdou', 'active', NULL, '2025-11-10 08:34:29', '2025-11-10 08:34:29', 'AG1111'),
	(9, 'STRD01', 'staff.rnd1', 'staff.rnd1@example.com', 'Staff R&D 1', NULL, 6, NULL, '2025-11-10 08:34:29', '$2y$12$2fbflqFCDAJ0blI5UxKgqupx6MsmOG8d4dUBt4JEXVMxN4cXhLWqe', 'active', NULL, '2025-11-10 08:34:29', '2025-11-10 08:34:29', 'HDRD01'),
	(10, 'STRD02', 'staff.rnd2', 'staff.rnd2@example.com', 'Staff R&D 2', NULL, 6, NULL, '2025-11-10 08:34:30', '$2y$12$1DG/QvQHNqjEJb0vYdE6EuPF5DAyucwfe7IebvmooaSzxV68OVIDK', 'active', NULL, '2025-11-10 08:34:30', '2025-11-10 08:34:30', 'HDRD01'),
	(11, 'HDQA01', 'head.qa', 'head.qa@example.com', 'Head QA', NULL, 5, NULL, '2025-11-10 08:34:30', '$2y$12$iwU.CbNNu86crg27IN4G.eB.nZhsra5YN8cF0kbh.KWIwxcuAGdkG', 'active', NULL, '2025-11-10 08:34:30', '2025-11-10 08:34:30', 'AG1111'),
	(12, 'STQA01', 'staff.qa1', 'staff.qa1@example.com', 'Staff QA 1', NULL, 5, NULL, '2025-11-10 08:34:30', '$2y$12$jAPLlAwCZjZDhwD0l8jrJOm4UYSQMaGh9hm7FghL.pD37CMluAZpy', 'active', NULL, '2025-11-10 08:34:30', '2025-11-10 08:34:30', 'HDQA01'),
	(13, 'STQA02', 'staff.qa2', 'staff.qa2@example.com', 'Staff QA 2', NULL, 5, NULL, '2025-11-10 08:34:30', '$2y$12$awt9T3SzcokSv/pZyz0mW.4kYcYzOJ8A6o53QQzCo4cv83C9ETkPy', 'active', NULL, '2025-11-10 08:34:30', '2025-11-10 08:34:30', 'HDQA01'),
	(14, 'HDHCD01', 'head.hcd', 'head.hcd@example.com', 'Head HCD', NULL, 3, NULL, '2025-11-10 08:34:31', '$2y$12$R3LFfTw2wvLKgR4xL4yurutwXR2LIs2K2i3e57CoFotoJpOaarQRK', 'active', NULL, '2025-11-10 08:34:31', '2025-11-10 08:34:31', 'AG1111'),
	(15, 'STHCD01', 'staff.hcd1', 'staff.hcd1@example.com', 'Staff HCD 1', NULL, 3, NULL, '2025-11-10 08:34:31', '$2y$12$J18shTP0m7tYNmIjlxm1/Os3Syu3Pt11AWOzTfhULkJCejsxSlnIu', 'active', NULL, '2025-11-10 08:34:31', '2025-11-10 08:34:31', 'HDHCD01'),
	(16, 'STHCD02', 'staff.hcd2', 'staff.hcd2@example.com', 'Staff HCD 2', NULL, 3, NULL, '2025-11-10 08:34:31', '$2y$12$TvARbYXfvLayvrtPxUI1JOJUGwN15AmfoA.ZuLgfQR15gRZTmHbgi', 'active', NULL, '2025-11-10 08:34:31', '2025-11-10 08:34:31', 'HDHCD01'),
	(17, 'ATASAN01', 'atasan1', 'atasan1@example.com', 'Atasan 1', NULL, 1, NULL, '2025-11-10 08:34:31', '$2y$12$gXUKrbOCgVvPgl.yGVoZfO3PeA.b7ZzlD8/ad4ItmLPH870Y5UQNy', 'active', NULL, '2025-11-10 08:34:31', '2025-11-10 08:34:31', 'AG1111'),
	(18, 'AG1111', 'superadmin', 'superadmin@example.com', 'Super Admin', NULL, 1, 1, '2025-11-10 08:34:32', '$2y$12$abqy8HxkqZ2nJGcszsFInev3f7DTrMyztJ17OlDyykiackekxAMEi', 'active', 'IU26TUF3fLwjnB2dvf0vxyvejA2VEKY92XKk54CluEUNccUpIwRD4dJGhvuA', '2025-11-10 08:34:32', '2025-11-25 03:24:05', 'AG2222'),
	(19, 'AG2222', 'user-requisition', 'user-requisition@example.com', 'User Requisition', NULL, 2, NULL, '2025-11-10 08:34:32', '$2y$12$sBxJeSe10JANV72cBTBVI.T7yg/HYxB15FC.yZ4AerONQr/MVb56S', 'active', NULL, '2025-11-10 08:34:32', '2025-11-10 08:34:32', 'AG1111'),
	(20, 'AG3333', 'user-approval', 'no-reply@example.com', 'User Approval', NULL, 3, NULL, '2025-11-10 08:34:32', '$2y$12$3lIzTIl8R5yjE5rXpTeYBe/D6yOOqQnchyxFVxFaqAIWqttJfnG5y', 'active', NULL, '2025-11-10 08:34:32', '2025-11-10 08:34:32', 'AG1111'),
	(21, 'ST0001', 'staff.eng', 'staff.eng@example.com', 'Staff Engineering', NULL, 7, NULL, '2025-11-10 08:34:32', '$2y$12$BhSz4WSrphQoBEcBMvcqv.Rds6hpF90RWlN.WjqzAyqh8MqRUYVGq', 'active', NULL, '2025-11-10 08:34:32', '2025-11-10 08:34:32', 'HD0001'),
	(22, 'WH0001', 'inward.wh', 'inward.wh@example.com', 'Inward WH Supervisor', NULL, 8, NULL, '2025-11-10 08:34:32', '$2y$12$lc5mmK89JJsr6uZZqMQz3uQBp0aqJp39PKT2YwxZnmKVhMsBoQNze', 'active', NULL, '2025-11-10 08:34:32', '2025-11-10 08:34:32', 'AG1111'),
	(23, 'MS0001', 'material.support', 'material.support@example.com', 'Material Support Supervisor', NULL, 8, NULL, '2025-11-10 08:34:33', '$2y$12$p6MoKp4Pbnn2xu2gIqXfYu/yeJq2E8eJ1FEldapQZ2RftKNcp/sE2', 'active', NULL, '2025-11-10 08:34:33', '2025-11-10 08:34:33', 'AG1111'),
	(24, 'WH0002', 'outward.wh', 'outward.wh@example.com', 'Outward WH Supervisor', NULL, 8, NULL, '2025-11-10 08:34:33', '$2y$12$9NPLE3ug.p3J1R3hRsJA/OkmqrMQSkJC.yiKFCQQdrYw6P.jwavKa', 'active', NULL, '2025-11-10 08:34:33', '2025-11-10 08:34:33', 'AG1111'),
	(25, 'U0950', 'rofika', 'rofika.lay@smii.co.id', 'Rofika', NULL, 7, 1, NULL, '$2y$12$YHibt4GsyVLajnkr9mhKOeAEbkD2xXUa7pLQCidx/qo9SaCw6hu8.', 'active', NULL, '2025-11-24 09:44:53', '2025-11-24 09:44:53', 'ATASAN01'),
	(26, 'X1031', 'edie', 'edie@smii.co.id', 'Edie Hirman', NULL, 2, 1, NULL, '$2y$12$pKPkHVt/.LjTKanwqSV/q.cNCU1PPR4/UyhD8kuxF.ymZrLTdQg/y', 'active', NULL, '2025-11-28 07:27:05', '2025-11-28 07:27:05', NULL),
	(27, 'AD2124', 'rainita', 'rainita.darmadi@smii.co.id', 'Rainita Darmadi', NULL, 2, 1, NULL, '$2y$12$/PG6jrCtbtRzmJe9W83uWOBgK2ZdBpcTQFveJhfaeymLdJUmi35ui', 'active', NULL, '2025-11-28 07:28:16', '2025-11-28 07:28:16', NULL),
	(28, 'AD1227', 'andika', 'andika.suhendar@smii.co.id', 'Andika Suhendar', NULL, 2, 1, NULL, '$2y$12$/egR94u7pKYfwNNBeo3ndO2C1wrLNpt./.AfTkbd6rIRXFI0pxE2q', 'active', NULL, '2025-11-28 07:28:58', '2025-11-28 07:28:58', NULL),
	(29, 'Z1058', 'ronal', 'ronal.katili@smii.co.id', 'Ronal Katili', NULL, 7, 1, NULL, '$2y$12$.4L8Si6wWFNvptZnEc5smev/GkJX64QH33Mvlu/tSJ2UjAW74McHS', 'active', NULL, '2025-11-28 07:29:55', '2025-11-28 07:29:55', NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
