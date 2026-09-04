-- public_page_visibility
-- Safe import for tcsonrw-gomel.by

CREATE TABLE IF NOT EXISTS `public_page_visibility` (
  `page_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT '1',
  `published_by_login` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by_login` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`page_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `public_page_visibility` (
  `page_key`,
  `is_published`,
  `published_by_login`,
  `updated_by_login`,
  `updated_at`
) VALUES
  ('/contacts.php', 1, 'admin_maks', 'admin_maks', '2026-06-10 13:23:38'),
  ('/news/ffff', 1, 'admin_maks', 'admin_maks', '2026-06-10 13:23:38')
ON DUPLICATE KEY UPDATE
  `is_published` = VALUES(`is_published`),
  `published_by_login` = VALUES(`published_by_login`),
  `updated_by_login` = VALUES(`updated_by_login`),
  `updated_at` = VALUES(`updated_at`);
