-- MySQL query to store questionnaire answer logs on every answer submission.
-- Replace __PREFIX__ with your Perch DB prefix (for example: perch3_).
CREATE TABLE IF NOT EXISTS `__PREFIX__questionnaire_answer_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` VARCHAR(128) NOT NULL,
  `member_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `questionnaire_type` ENUM('first-order','re-order') NOT NULL,
  `question_key` VARCHAR(128) NOT NULL,
  `answer_text` LONGTEXT NULL,
  `previous_answer_text` LONGTEXT NULL,
  `changed` TINYINT(1) NOT NULL DEFAULT 0,
  `action` VARCHAR(16) NOT NULL DEFAULT 'new',
  `context_step` VARCHAR(128) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `questionnaire_answer_logs_user_type_idx` (`user_id`, `questionnaire_type`),
  KEY `questionnaire_answer_logs_member_idx` (`member_id`),
  KEY `questionnaire_answer_logs_created_idx` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
