-- =============================================================================
-- StudyHub — schéma et données (fichier unique)
-- Base vide recommandée (sinon supprimer les tables existantes avant import).
-- phpMyAdmin : Importer ce fichier, ou : mysql -u root -e "CREATE DATABASE IF NOT EXISTS studehub" && mysql -u root studehub < studehub.sql
-- =============================================================================

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `studehub` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `studehub`;

START TRANSACTION;
SET time_zone = "+00:00";

-- -----------------------------------------------------------------------------
-- Table users
-- -----------------------------------------------------------------------------
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `universite` varchar(255) NOT NULL,
  `filiere` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `tel` int(255) NOT NULL,
  `bio` varchar(500) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `role` int(11) NOT NULL DEFAULT 1,
  `score` decimal(10,0) NOT NULL,
  `note_moyenne` decimal(3,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `tel` (`tel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `nom`, `prenom`, `universite`, `filiere`, `email`, `mdp`, `tel`, `bio`, `photo`, `role`, `score`, `note_moyenne`) VALUES
(1, 'youssef', 'miledi', '', '', 'youssef@gmail.com', 'admin123', 0, '', '', 0, 0, 0.00),
(2, 'anas', 'b', 'esprit', '2 eme', 'anasbouremma@gmail.com', '$2y$10$RrwEScYmaya/XYj0hKS0meTDg6m7Bn4MjeBF8LVmatAJMTg4cX0Dm', 12345678, 'gg wp', '', 1, 0, 1.50),
(6, 'wassim', 'm', 'acac', 'acacas', 'wassim@gmail.com', '$2y$10$yW5tCCTCwArT3DdhhEvYZOOK23HoBzLWA78eMhQUikQtQC/3ZfVwO', 12345679, 'adawdawdawd', 'uploads/1775947488_Gemini_Generated_Image_cis6rhcis6rhcis6.png', 1, 0, 0.00),
(7, 'rafaa', 'b', 'esprit', 'fxht', 'rafaa@gmail.com', '$2y$10$SgVfxin4GxbyDTBku0WsJe7cEkHy6itlOcZEvudwJfBrIDnj.N1mu', 1234577, 'kjkl', 'uploads/1776019032_View-of-Toledo-907x1024.jpg', 1, 0, 0.00);

-- -----------------------------------------------------------------------------
-- Table ressource
-- -----------------------------------------------------------------------------
CREATE TABLE `ressource` (
  `id_res` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `description` varchar(500) NOT NULL,
  `type` varchar(255) NOT NULL,
  `niveau` varchar(255) NOT NULL,
  `acces` varchar(255) NOT NULL,
  `prix` double NOT NULL,
  `fichier` varchar(255) NOT NULL,
  `id` int(11) NOT NULL,
  `pages` int(11) DEFAULT 0,
  `downloads` int(11) DEFAULT 0,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `note_moyenne` decimal(3,2) DEFAULT 0.00,
  `matiere` varchar(255) DEFAULT 'Autre',
  `photo` varchar(500) DEFAULT '',
  PRIMARY KEY (`id_res`),
  KEY `id` (`id`),
  CONSTRAINT `ressource_ibfk_1` FOREIGN KEY (`id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `ressource` (`id_res`, `titre`, `description`, `type`, `niveau`, `acces`, `prix`, `fichier`, `id`, `pages`, `downloads`, `date_creation`, `note_moyenne`, `matiere`, `photo`) VALUES
(2, 'awdwa', 'dawdawd', 'Examen', 'Licence 3', 'Gratuit', 0, 'uploads/1775947514_TP1-FDR-1.pdf', 6, 0, 0, '2026-04-12 16:23:38', 0.00, 'Autre', ''),
(3, 'aaaaa', 'aaaaaa', 'Résumé', 'Licence 1', 'Gratuit', 0, 'uploads/1776011396_69dbc8841b907.pdf', 2, 10, 3, '2026-04-12 16:29:56', 3.00, 'Autre', ''),
(4, 'matimathique de base 3', 'adadawdwad', 'Résumé', 'Licence 1', 'Gratuit', 0, 'uploads/1776015954_69dbda52c92a8.pdf', 2, 10, 0, '2026-04-12 17:45:54', 0.00, 'Mathématiques', '');

-- -----------------------------------------------------------------------------
-- Catalogue d abonnements (plans personnalisés + ressources)
-- -----------------------------------------------------------------------------
CREATE TABLE `subscription_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(500) NOT NULL,
  `prix` int(11) NOT NULL DEFAULT 0,
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_published` (`published`),
  KEY `idx_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `subscription_plan_resources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) NOT NULL,
  `id_ressource` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_plan_res` (`plan_id`,`id_ressource`),
  KEY `id_ressource` (`id_ressource`),
  CONSTRAINT `spr_plan_fk` FOREIGN KEY (`plan_id`) REFERENCES `subscription_plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `spr_res_fk` FOREIGN KEY (`id_ressource`) REFERENCES `ressource` (`id_res`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `subscription_plan_playlists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) NOT NULL,
  `playlist_group_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_plan_playlist` (`plan_id`,`playlist_group_id`),
  KEY `idx_plan_playlist_plan` (`plan_id`),
  CONSTRAINT `spp_plan_fk` FOREIGN KEY (`plan_id`) REFERENCES `subscription_plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------------------------------
-- Abonnements membres (plan catalogue ou libelle personnalise)
-- -----------------------------------------------------------------------------
CREATE TABLE `abonemment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `nom` varchar(100) NOT NULL,
  `descreption` varchar(500) NOT NULL,
  `prix` int(11) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `card_holder` varchar(120) DEFAULT NULL,
  `payment_last4` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `abonemment_ibfk_1` (`id_user`),
  KEY `abonemment_plan` (`plan_id`),
  CONSTRAINT `abonemment_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `abonemment_plan_fk` FOREIGN KEY (`plan_id`) REFERENCES `subscription_plans` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------------------------------
-- Playlists (optionnel ; les abonnements catalogue utilisent subscription_plan_resources)
-- -----------------------------------------------------------------------------
CREATE TABLE `playlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(20) NOT NULL,
  `description` varchar(50) NOT NULL,
  `photo` varchar(500) DEFAULT '',
  `id_ressource` int(11) NOT NULL,
  `id_abonement` int(11) NOT NULL DEFAULT 0 COMMENT 'Cle de regroupement interne',
  PRIMARY KEY (`id`),
  KEY `id_ressource` (`id_ressource`),
  KEY `id_abonement` (`id_abonement`),
  CONSTRAINT `playlist_ibfk_1` FOREIGN KEY (`id_ressource`) REFERENCES `ressource` (`id_res`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------------------------------
-- Commentaires et notes
-- -----------------------------------------------------------------------------
CREATE TABLE `comment` (
  `id_comment` int(11) NOT NULL AUTO_INCREMENT,
  `message` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `id` int(11) NOT NULL,
  `id_res` int(11) NOT NULL,
  PRIMARY KEY (`id_comment`),
  KEY `id` (`id`),
  KEY `id_res` (`id_res`),
  CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`id`) REFERENCES `users` (`id`),
  CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`id_res`) REFERENCES `ressource` (`id_res`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `comment` (`id_comment`, `message`, `date`, `id`, `id_res`) VALUES
(3, 'hiii', '2026-04-12', 2, 3);

CREATE TABLE `ratings` (
  `id_rating` int(11) NOT NULL AUTO_INCREMENT,
  `id_res` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `date_rating` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_rating`),
  UNIQUE KEY `unique_rating` (`id_res`,`id_user`),
  KEY `id_res` (`id_res`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`id_res`) REFERENCES `ressource` (`id_res`),
  CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `ratings` (`id_rating`, `id_res`, `id_user`, `rating`, `date_rating`) VALUES
(1, 3, 2, 3, '2026-04-12 17:07:24');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
