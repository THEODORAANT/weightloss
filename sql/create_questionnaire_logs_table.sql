-- Stores questionnaire answer history logs in the database instead of filesystem JSON files.
CREATE TABLE IF NOT EXISTS `__PREFIX__questionnaire_logs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` VARCHAR(128) NOT NULL,
  `member_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `questionnaire_type` VARCHAR(32) NOT NULL,
  `metadata_json` LONGTEXT NULL,
  `raw_log_json` LONGTEXT NULL,
  `grouped_log_json` LONGTEXT NULL,
  `has_changes` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `questionnaire_logs_user_type_idx` (`user_id`, `questionnaire_type`),
  KEY `questionnaire_logs_member_idx` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
