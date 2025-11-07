-- Chat tables for member support conversations
-- Replace __PREFIX__ with your Perch table prefix before running.

CREATE TABLE IF NOT EXISTS `__PREFIX__chat_threads` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `memberID` int unsigned NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `status` enum('open','closed') NOT NULL DEFAULT 'open',
  `last_message_at` datetime DEFAULT NULL,
  `last_message_from` enum('member','staff') DEFAULT NULL,
  `last_member_activity` datetime DEFAULT NULL,
  `last_staff_activity` datetime DEFAULT NULL,
  `last_member_read_at` datetime DEFAULT NULL,
  `last_staff_read_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `member_unique` (`memberID`),
  KEY `status_last_message` (`status`,`last_message_at`),
  KEY `last_staff_activity` (`last_staff_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `__PREFIX__chat_messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `threadID` int unsigned NOT NULL,
  `sender_type` enum('member','staff') NOT NULL,
  `sender_memberID` int unsigned DEFAULT NULL,
  `sender_staffID` int unsigned DEFAULT NULL,
  `body` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `thread_created` (`threadID`,`created_at`),
  CONSTRAINT `__PREFIX__chat_messages_thread_fk` FOREIGN KEY (`threadID`) REFERENCES `__PREFIX__chat_threads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `__PREFIX__chat_thread_closures` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `threadID` int unsigned NOT NULL,
  `last_message_id` int unsigned DEFAULT NULL,
  `closed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `thread_closed_at` (`threadID`,`closed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
