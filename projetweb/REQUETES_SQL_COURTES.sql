-- ========================================================
-- REQUÊTES SQL - VERSION COURTE (Seulement les modifications)
-- À copier-coller directement dans phpMyAdmin/MySQL
-- ========================================================

USE `projetr`;

-- ✅ MODIFICATION TABLE RECLAMATION
ALTER TABLE `reclamation` 
ADD COLUMN `categorie` VARCHAR(50) DEFAULT 'Général' AFTER `status`,
ADD COLUMN `priorite` VARCHAR(20) DEFAULT 'Moyenne' AFTER `categorie`,
ADD COLUMN `email_client` VARCHAR(100) AFTER `priorite`,
ADD COLUMN `id_agent` INT AFTER `email_client`,
ADD COLUMN `date_limite_resolution` DATETIME AFTER `id_agent`,
ADD COLUMN `date_resolution` DATETIME AFTER `date_limite_resolution`,
ADD COLUMN `satisfaction` INT AFTER `date_resolution`,
ADD COLUMN `source` VARCHAR(50) DEFAULT 'Email' AFTER `satisfaction`,
ADD COLUMN `estimation_cout` DECIMAL(10, 2) DEFAULT 0.00 AFTER `source`,
ADD COLUMN `fichier_joint` VARCHAR(255) AFTER `estimation_cout`,
ADD KEY `idx_priorite` (`priorite`),
ADD KEY `idx_categorie` (`categorie`),
ADD KEY `idx_agent` (`id_agent`);

-- ✅ MODIFICATION TABLE REPONSE
ALTER TABLE `reponse` 
ADD COLUMN `id_agent` INT AFTER `id_reclamation`,
ADD COLUMN `type_reponse` VARCHAR(50) DEFAULT 'Réponse' AFTER `id_agent`,
ADD COLUMN `lu` TINYINT DEFAULT 0 AFTER `type_reponse`,
ADD COLUMN `date_lecture` DATETIME AFTER `lu`,
ADD COLUMN `feedback_client` INT AFTER `date_lecture`,
ADD COLUMN `fichier_joint` VARCHAR(255) AFTER `feedback_client`;

-- ✅ CRÉATION TABLE AUDIT
CREATE TABLE IF NOT EXISTS `audit` (
  `id_audit` INT AUTO_INCREMENT PRIMARY KEY,
  `id_reclamation` INT NOT NULL,
  `type_modification` VARCHAR(50) NOT NULL,
  `ancien_statut` VARCHAR(20),
  `nouveau_statut` VARCHAR(20),
  `champ_modifie` VARCHAR(100),
  `ancienne_valeur` LONGTEXT,
  `nouvelle_valeur` LONGTEXT,
  `id_utilisateur` INT,
  `nom_utilisateur` VARCHAR(100),
  `date_modification` DATETIME NOT NULL,
  `adresse_ip` VARCHAR(45),
  CONSTRAINT `fk_audit_reclamation` FOREIGN KEY (`id_reclamation`) 
    REFERENCES `reclamation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `idx_reclamation_audit` (`id_reclamation`),
  KEY `idx_date_audit` (`date_modification`),
  KEY `idx_type_modification` (`type_modification`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ✅ CRÉATION TABLE NOTIFICATION
CREATE TABLE IF NOT EXISTS `notification` (
  `id_notification` INT AUTO_INCREMENT PRIMARY KEY,
  `id_reclamation` INT NOT NULL,
  `id_agent` INT NOT NULL,
  `type_notification` VARCHAR(50) NOT NULL,
  `message` VARCHAR(255) NOT NULL,
  `lu` TINYINT DEFAULT 0,
  `date_creation` DATETIME NOT NULL,
  `date_lecture` DATETIME,
  CONSTRAINT `fk_notification_reclamation` FOREIGN KEY (`id_reclamation`) 
    REFERENCES `reclamation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `idx_agent_notification` (`id_agent`),
  KEY `idx_lu` (`lu`),
  KEY `idx_date_creation` (`date_creation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ✅ CRÉATION TABLE AGENT
CREATE TABLE IF NOT EXISTS `agent` (
  `id_agent` INT AUTO_INCREMENT PRIMARY KEY,
  `nom` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `departement` VARCHAR(100),
  `statut` VARCHAR(20) DEFAULT 'Actif',
  `date_creation` DATETIME NOT NULL,
  KEY `idx_email` (`email`),
  KEY `idx_statut` (`statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ✅ CRÉATION TABLE RAPPORT
CREATE TABLE IF NOT EXISTS `rapport` (
  `id_rapport` INT AUTO_INCREMENT PRIMARY KEY,
  `titre` VARCHAR(255) NOT NULL,
  `type` VARCHAR(50) NOT NULL,
  `date_debut` DATE NOT NULL,
  `date_fin` DATE NOT NULL,
  `statistiques` LONGTEXT NOT NULL,
  `fichier_export` VARCHAR(255),
  `date_generation` DATETIME NOT NULL,
  `cree_par` INT,
  KEY `idx_type` (`type`),
  KEY `idx_date_generation` (`date_generation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ✅ INSÉRER AGENTS D'EXEMPLE
INSERT INTO `agent` (`nom`, `email`, `departement`, `statut`, `date_creation`) VALUES
('Jean Dupont', 'jean.dupont@company.com', 'Support', 'Actif', NOW()),
('Marie Martin', 'marie.martin@company.com', 'Facturation', 'Actif', NOW()),
('Pierre Bernard', 'pierre.bernard@company.com', 'Support', 'Actif', NOW());
