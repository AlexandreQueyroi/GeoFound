DROP DATABASE geofound;
CREATE DATABASE geofound;
USE geofound;

DROP TABLE IF EXISTS `avatar`;
CREATE TABLE IF NOT EXISTS `avatar` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hair` varchar(50) DEFAULT NULL,
  `hair_color` varchar(50) DEFAULT NULL,
  `eyes` varchar(50) DEFAULT NULL,
  `skin` varchar(50) DEFAULT NULL,
  `mouth` varchar(50) DEFAULT NULL,
  `nose` varchar(50) DEFAULT NULL,
  `head` varchar(50) DEFAULT NULL,
  `accessory` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `bookmarks`;
CREATE TABLE IF NOT EXISTS `bookmarks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `post_id` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_bookmark` (`user_id`,`post_id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `captcha`;
CREATE TABLE IF NOT EXISTS `captcha` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(256) DEFAULT NULL,
  `response` text,
  `enabled` tinyint(1) DEFAULT '0',
  `requested` tinyint(1) DEFAULT '0',
  `success_requested` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `comment`;
CREATE TABLE IF NOT EXISTS `comment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  `content` varchar(512) NOT NULL,
  `comment_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `follow`;
CREATE TABLE IF NOT EXISTS `follow` (
  `id` int NOT NULL AUTO_INCREMENT,
  `follow_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `state` varchar(50) DEFAULT 'pending',
  `user1_id` int NOT NULL,
  `user2_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user1_id` (`user1_id`,`user2_id`),
  KEY `user2_id` (`user2_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
  `id` int NOT NULL AUTO_INCREMENT,
  `posted_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `content` varchar(512) DEFAULT NULL,
  `state` varchar(16) DEFAULT 'sent',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `order_table`;
CREATE TABLE IF NOT EXISTS `order_table` (
  `id` int NOT NULL AUTO_INCREMENT,
  `made_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `deliver_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `page_maintenance`;
CREATE TABLE IF NOT EXISTS `page_maintenance` (
  `id` int NOT NULL AUTO_INCREMENT,
  `page_path` varchar(255) NOT NULL,
  `page_name` varchar(100) NOT NULL,
  `is_maintenance` tinyint(1) DEFAULT '0',
  `maintenance_message` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `page_permissions`;
CREATE TABLE IF NOT EXISTS `page_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `page_path` varchar(191) NOT NULL,
  `permission_id` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_page_permission` (`page_path`,`permission_id`),
  KEY `permission_id` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES
(1, '*', 'Permission administrateur - Accès à tout', '2025-06-27 11:28:08'),
(2, 'admin.access', "Accès au panel d'administration", '2025-06-27 11:28:08'),
(3, 'admin.users', 'Gestion des utilisateurs', '2025-06-27 11:28:08'),
(4, 'admin.ranks', 'Gestion des rangs', '2025-06-27 11:28:08'),
(5, 'admin.permissions', 'Gestion des permissions', '2025-06-27 11:28:08'),
(6, 'admin.maintenance', 'Gestion de la maintenance', '2025-06-27 11:28:08'),
(7, 'page.access.*', 'Accès à toutes les pages', '2025-06-27 11:28:08'),
(8, 'page.maintenance.bypass', 'Bypass de la maintenance globale', '2025-06-27 11:28:08'),
(9, 'page.maintenance.manage', 'Gestion de la maintenance des pages', '2025-06-27 11:28:08'),
(10, 'page.permission.manage', 'Gestion des permissions de pages', '2025-06-27 11:28:08'),
(11, 'user.ban', 'Bannir des utilisateurs', '2025-06-27 11:28:08'),
(12, 'user.unban', 'Débannir des utilisateurs', '2025-06-27 11:28:08'),
(13, 'user.edit', 'Modifier les utilisateurs', '2025-06-27 11:28:08'),
(14, 'post.moderate', 'Modérer les posts', '2025-06-27 11:28:08'),
(15, 'comment.moderate', 'Modérer les commentaires', '2025-06-27 11:28:08'),
(16, 'message.global', 'Messagerie globale', '2025-06-27 11:28:08'),
(17, 'log.view', 'Voir les logs', '2025-06-27 11:28:08'),
(18, 'captcha.manage', 'Gestion des captchas', '2025-06-27 11:28:08');

DROP TABLE IF EXISTS `post`;
CREATE TABLE IF NOT EXISTS `post` (
  `id` int NOT NULL AUTO_INCREMENT,
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `content_id` int NOT NULL,
  `user` int NOT NULL,
  `name` varchar(256) DEFAULT NULL,
  `description` varchar(512) DEFAULT NULL,
  `date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `content_id` (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `post_content`;
CREATE TABLE IF NOT EXISTS `post_content` (
  `id` int NOT NULL AUTO_INCREMENT,
  `content` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `ranks`;
CREATE TABLE IF NOT EXISTS `ranks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `color` varchar(7) DEFAULT '#3B82F6',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `ranks` (`id`, `name`, `color`, `created_at`) VALUES
(1, 'admin', '#EF4444', '2025-06-27 11:28:08'),
(2, 'moderator', '#F59E0B', '2025-06-27 11:28:08'),
(3, 'user', '#3B82F6', '2025-06-27 11:28:08');

DROP TABLE IF EXISTS `rank_permissions`;
CREATE TABLE IF NOT EXISTS `rank_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rank_id` int NOT NULL,
  `permission_id` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_rank_permission` (`rank_id`,`permission_id`),
  KEY `permission_id` (`permission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `rank_permissions` (`id`, `rank_id`, `permission_id`, `created_at`) VALUES
(1, 1, 1, '2025-06-27 11:28:08'),
(2, 2, 2, '2025-06-27 11:28:08'),
(3, 2, 3, '2025-06-27 11:28:08'),
(4, 2, 15, '2025-06-27 11:28:08'),
(5, 2, 17, '2025-06-27 11:28:08'),
(6, 2, 16, '2025-06-27 11:28:08'),
(7, 2, 14, '2025-06-27 11:28:08'),
(8, 2, 11, '2025-06-27 11:28:08'),
(9, 2, 13, '2025-06-27 11:28:08'),
(10, 2, 12, '2025-06-27 11:28:08');

DROP TABLE IF EXISTS `reaction`;
CREATE TABLE IF NOT EXISTS `reaction` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  `state` enum('like','love','wow') DEFAULT 'like',
  `react_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_reaction` (`post_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `report`;
CREATE TABLE IF NOT EXISTS `report` (
  `id` int NOT NULL AUTO_INCREMENT,
  `report_to` int DEFAULT NULL,
  `report_reason` varchar(512) DEFAULT NULL,
  `report_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `assigned_to` int DEFAULT NULL,
  `response` varchar(512) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'not treated',
  `commentary` text,
  PRIMARY KEY (`id`),
  KEY `report_to` (`report_to`),
  KEY `assigned_to` (`assigned_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `reward`;
CREATE TABLE IF NOT EXISTS `reward` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(256) DEFAULT NULL,
  `description` varchar(512) DEFAULT NULL,
  `stock` int DEFAULT '0',
  `point` int DEFAULT '0',
  `image` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `sanction`;
CREATE TABLE IF NOT EXISTS `sanction` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` varchar(50) DEFAULT NULL,
  `reason` varchar(512) DEFAULT NULL,
  `begin_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `end_at` datetime DEFAULT NULL,
  `cancel` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `support`;
CREATE TABLE IF NOT EXISTS `support` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date` datetime DEFAULT CURRENT_TIMESTAMP,
  `content` varchar(1024) DEFAULT NULL,
  `state` varchar(50) DEFAULT 'open',
  `respond_at` datetime DEFAULT NULL,
  `assigned_to` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assigned_to` (`assigned_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pseudo` varchar(100) DEFAULT NULL,
  `user_rank` varchar(32) DEFAULT NULL,
  `password` text,
  `email` varchar(100) DEFAULT NULL,
  `description` text,
  `desactivated` tinyint(1) DEFAULT '0',
  `token` varchar(128) DEFAULT NULL,
  `connected` datetime DEFAULT NULL,
  `verified` tinyint(1) DEFAULT '0',
  `verified_at` datetime DEFAULT NULL,
  `avatar_id` int DEFAULT NULL,
  `point` int DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `pseudo` (`pseudo`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `token` (`token`),
  KEY `avatar_id` (`avatar_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `pseudo`, `user_rank`, `password`, `email`, `description`, `desactivated`, `token`, `connected`, `verified`, `verified_at`, `avatar_id`, `point`) VALUES
(1, 'admin', 'admin', '$2y$10$mjIYy.RcnzPIGytlmqifBudv8b5mqW.0KE/JpIFXmkRiv0WrxpfB2', 'admin@geofound.com', 'Default admin account', 0, NULL, NULL, 1, NULL, NULL, 0);

DROP TABLE IF EXISTS `user_message`;
CREATE TABLE IF NOT EXISTS `user_message` (
  `id` int NOT NULL AUTO_INCREMENT,
  `message_id` int NOT NULL,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `message_id` (`message_id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `user_permissions`;
CREATE TABLE IF NOT EXISTS `user_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `permission_id` int NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_permission` (`user_id`,`permission_id`),
  KEY `permission_id` (`permission_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `page_permissions`
  ADD CONSTRAINT `page_permissions_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`avatar_id`) REFERENCES `avatar` (`id`) ON DELETE SET NULL;
COMMIT;

-- Table des signalements
CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('post', 'comment', 'message') NOT NULL,
    target_id INT NOT NULL,
    reporter_id INT NOT NULL,
    reason VARCHAR(255) NOT NULL,
    details TEXT,
    status ENUM('pending', 'reviewed', 'rejected', 'sanctioned') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    reviewed_by INT DEFAULT NULL,
    reviewed_at DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table des sanctions
CREATE TABLE IF NOT EXISTS sanctions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    report_id INT DEFAULT NULL,
    type ENUM('ban', 'mute', 'delete_content', 'warning') NOT NULL,
    reason VARCHAR(255) NOT NULL,
    details TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME DEFAULT NULL,
    admin_id INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
