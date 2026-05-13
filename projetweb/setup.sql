-- ==========================================
-- SCRIPT DE CRÉATION DE LA BASE DE DONNÉES
-- Projet: E-Business - Gestion des Réclamations
-- Date: 2026-05-06
-- ==========================================

-- Créer la base de données
CREATE DATABASE IF NOT EXISTS `projetr` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `projetr`;

-- ==========================================
-- TABLE: reclamation
-- ==========================================
CREATE TABLE IF NOT EXISTS `reclamation` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `titre` VARCHAR(100) NOT NULL COMMENT 'Titre de la réclamation',
  `description` LONGTEXT NOT NULL COMMENT 'Description détaillée',
  `date` DATETIME NOT NULL COMMENT 'Date de création',
  `status` VARCHAR(20) NOT NULL DEFAULT 'En attente' COMMENT 'Statut: En attente, En cours, Résolu, Rejeté',
  `categorie` VARCHAR(50) DEFAULT 'Général' COMMENT 'Bug, Facturation, Service Client, Produit, Autre',
  `priorite` VARCHAR(20) DEFAULT 'Moyenne' COMMENT 'Basse, Moyenne, Haute, Critique',
  `email_client` VARCHAR(100) COMMENT 'Email du client',
  `id_agent` INT COMMENT 'Agent assigné',
  `date_limite_resolution` DATETIME COMMENT 'Date limite pour résoudre',
  `date_resolution` DATETIME COMMENT 'Date réelle de résolution',
  `satisfaction` INT COMMENT 'Note de satisfaction (1-5)',
  `source` VARCHAR(50) DEFAULT 'Email' COMMENT 'Email, Chat, Téléphone, Autre',
  `estimation_cout` DECIMAL(10, 2) DEFAULT 0.00 COMMENT 'Impact financier estimé',
  `fichier_joint` VARCHAR(255) COMMENT 'Nom du fichier joint',
  KEY `idx_status` (`status`),
  KEY `idx_date` (`date`),
  KEY `idx_priorite` (`priorite`),
  KEY `idx_categorie` (`categorie`),
  KEY `idx_agent` (`id_agent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Table des réclamations';

-- ==========================================
-- TABLE: reponse
-- ==========================================
CREATE TABLE IF NOT EXISTS `reponse` (
  `id_reponse` INT AUTO_INCREMENT PRIMARY KEY,
  `reponse` LONGTEXT NOT NULL COMMENT 'Contenu de la réponse',
  `date` DATETIME NOT NULL COMMENT 'Date de la réponse',
  `id_reclamation` INT NOT NULL COMMENT 'Référence à la réclamation',
  `id_agent` INT COMMENT 'Agent qui a répondu',
  `type_reponse` VARCHAR(50) DEFAULT 'Réponse' COMMENT 'Réponse, Escalade, Solution finale',
  `lu` TINYINT DEFAULT 0 COMMENT 'Client a-t-il lu la réponse?',
  `date_lecture` DATETIME COMMENT 'Date de lecture par le client',
  `feedback_client` INT COMMENT 'Évaluation de la réponse (1-5)',
  `fichier_joint` VARCHAR(255) COMMENT 'Nom du fichier joint',
  CONSTRAINT `fk_reclamation` FOREIGN KEY (`id_reclamation`) 
    REFERENCES `reclamation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `idx_reclamation` (`id_reclamation`),
  KEY `idx_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Table des réponses aux réclamations';

-- ==========================================
-- TABLE: audit - Historique des modifications
-- ==========================================
CREATE TABLE IF NOT EXISTS `audit` (
  `id_audit` INT AUTO_INCREMENT PRIMARY KEY,
  `id_reclamation` INT NOT NULL COMMENT 'ID de la réclamation modifiée',
  `type_modification` VARCHAR(50) NOT NULL COMMENT 'Creation, Modification, Suppression, Status_change',
  `ancien_statut` VARCHAR(20) COMMENT 'Ancien statut (si modification)',
  `nouveau_statut` VARCHAR(20) COMMENT 'Nouveau statut (si modification)',
  `champ_modifie` VARCHAR(100) COMMENT 'Quel champ a été modifié',
  `ancienne_valeur` LONGTEXT COMMENT 'Ancienne valeur',
  `nouvelle_valeur` LONGTEXT COMMENT 'Nouvelle valeur',
  `id_utilisateur` INT COMMENT 'ID de l\'utilisateur qui a fait la modification',
  `nom_utilisateur` VARCHAR(100) COMMENT 'Nom de l\'utilisateur',
  `date_modification` DATETIME NOT NULL COMMENT 'Quand la modification a eu lieu',
  `adresse_ip` VARCHAR(45) COMMENT 'Adresse IP de l\'utilisateur',
  CONSTRAINT `fk_audit_reclamation` FOREIGN KEY (`id_reclamation`) 
    REFERENCES `reclamation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `idx_reclamation_audit` (`id_reclamation`),
  KEY `idx_date_audit` (`date_modification`),
  KEY `idx_type_modification` (`type_modification`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Audit trail - Historique complet des modifications';

-- ==========================================
-- TABLE: notification - Alertes aux agents
-- ==========================================
CREATE TABLE IF NOT EXISTS `notification` (
  `id_notification` INT AUTO_INCREMENT PRIMARY KEY,
  `id_reclamation` INT NOT NULL COMMENT 'Réclamation concernée',
  `id_agent` INT NOT NULL COMMENT 'Agent à notifier',
  `type_notification` VARCHAR(50) NOT NULL COMMENT 'Nouvelle_reclamation, Reponse_a_ajouter, Escalade, etc',
  `message` VARCHAR(255) NOT NULL COMMENT 'Message de la notification',
  `lu` TINYINT DEFAULT 0 COMMENT 'Notification lue ou non',
  `date_creation` DATETIME NOT NULL COMMENT 'Date de création',
  `date_lecture` DATETIME COMMENT 'Date de lecture',
  CONSTRAINT `fk_notification_reclamation` FOREIGN KEY (`id_reclamation`) 
    REFERENCES `reclamation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `idx_agent_notification` (`id_agent`),
  KEY `idx_lu` (`lu`),
  KEY `idx_date_creation` (`date_creation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Notifications pour les agents';

-- ==========================================
-- TABLE: agent - Gestion des agents
-- ==========================================
CREATE TABLE IF NOT EXISTS `agent` (
  `id_agent` INT AUTO_INCREMENT PRIMARY KEY,
  `nom` VARCHAR(100) NOT NULL COMMENT 'Nom de l\'agent',
  `email` VARCHAR(100) NOT NULL UNIQUE COMMENT 'Email de l\'agent',
  `departement` VARCHAR(100) COMMENT 'Département (Support, Facturation, etc)',
  `statut` VARCHAR(20) DEFAULT 'Actif' COMMENT 'Actif ou Inactif',
  `date_creation` DATETIME NOT NULL COMMENT 'Date d\'embauche',
  KEY `idx_email` (`email`),
  KEY `idx_statut` (`statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Gestion des agents support';

-- ==========================================
-- TABLE: rapport - Rapports générés
-- ==========================================
CREATE TABLE IF NOT EXISTS `rapport` (
  `id_rapport` INT AUTO_INCREMENT PRIMARY KEY,
  `titre` VARCHAR(255) NOT NULL COMMENT 'Titre du rapport',
  `type` VARCHAR(50) NOT NULL COMMENT 'Mensuel, Trimestriel, Annuel, Custom',
  `date_debut` DATE NOT NULL COMMENT 'Période de début',
  `date_fin` DATE NOT NULL COMMENT 'Période de fin',
  `statistiques` LONGTEXT NOT NULL COMMENT 'Données du rapport en JSON',
  `fichier_export` VARCHAR(255) COMMENT 'Chemin du fichier généré (PDF, CSV, Excel)',
  `date_generation` DATETIME NOT NULL COMMENT 'Quand le rapport a été généré',
  `cree_par` INT COMMENT 'ID de l\'utilisateur qui a créé le rapport',
  KEY `idx_type` (`type`),
  KEY `idx_date_generation` (`date_generation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Rapports analytiques générés';

-- Insérer des exemples de réclamations
INSERT INTO `reclamation` (`titre`, `description`, `date`, `status`) VALUES
('Problème avec mon compte', 'Je n\'arrive pas à me connecter à mon compte depuis hier. J\'ai essayé réinitialiser mon mot de passe, mais aucun email n\'a été reçu.', NOW(), 'En attente'),
('Service client non réactif', 'J\'ai envoyé trois emails au support et je n\'ai reçu aucune réponse depuis une semaine. C\'est très frustrant.', NOW(), 'En cours'),
('Product défectueux', 'Le produit reçu ne correspond pas à la description. Il est endommagé et non conforme.', DATE_SUB(NOW(), INTERVAL 2 DAY), 'Résolu'),
('Facturation incorrecte', 'J\'ai été facturisé deux fois pour la même commande. Veuillez corriger celà.', DATE_SUB(NOW(), INTERVAL 3 DAY), 'En attente');

-- Insérer des exemples de réponses
INSERT INTO `reponse` (`reponse`, `date`, `id_reclamation`) VALUES
('Merci pour votre signalement. Nous avons réinitialisé votre compte et vous devriez recevoir un email de réinitialisation dans les 5 minutes.', NOW(), 1),
('Nous nous excusons pour ce délai. Merci de votre patience. Un agent va vous contacter dans les 24 heures.', NOW(), 2),
('Merci pour votre réclamation. Votre remboursement a été traité et réparation envoyée.', DATE_SUB(NOW(), INTERVAL 1 DAY), 3);

-- ==========================================
-- FIN DU SCRIPT
-- ==========================================
