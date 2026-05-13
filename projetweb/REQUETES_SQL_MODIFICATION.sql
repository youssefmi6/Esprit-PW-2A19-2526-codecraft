-- ========================================================
-- REQUÊTES SQL - MODIFICATION COMPLÈTE DE LA BASE DE DONNÉES
-- Projet: E-Business - Gestion des Réclamations - Fonctionnalités Avancées
-- Date: May 12, 2026
-- ========================================================

-- ⚠️ IMPORTANT: Exécutez ces requêtes manuellement dans phpMyAdmin ou MySQL Workbench
-- ========================================================

-- ========================================================
-- 1️⃣ MODIFICATION TABLE RECLAMATION - Ajouter nouvelles colonnes
-- ========================================================

ALTER TABLE `reclamation` ADD COLUMN `categorie` VARCHAR(50) DEFAULT 'Général' COMMENT 'Bug, Facturation, Service Client, Produit, Autre' AFTER `status`;

ALTER TABLE `reclamation` ADD COLUMN `priorite` VARCHAR(20) DEFAULT 'Moyenne' COMMENT 'Basse, Moyenne, Haute, Critique' AFTER `categorie`;

ALTER TABLE `reclamation` ADD COLUMN `email_client` VARCHAR(100) COMMENT 'Email du client' AFTER `priorite`;

ALTER TABLE `reclamation` ADD COLUMN `id_agent` INT COMMENT 'Agent assigné' AFTER `email_client`;

ALTER TABLE `reclamation` ADD COLUMN `date_limite_resolution` DATETIME COMMENT 'Date limite pour résoudre' AFTER `id_agent`;

ALTER TABLE `reclamation` ADD COLUMN `date_resolution` DATETIME COMMENT 'Date réelle de résolution' AFTER `date_limite_resolution`;

ALTER TABLE `reclamation` ADD COLUMN `satisfaction` INT COMMENT 'Note de satisfaction (1-5)' AFTER `date_resolution`;

ALTER TABLE `reclamation` ADD COLUMN `source` VARCHAR(50) DEFAULT 'Email' COMMENT 'Email, Chat, Téléphone, Autre' AFTER `satisfaction`;

ALTER TABLE `reclamation` ADD COLUMN `estimation_cout` DECIMAL(10, 2) DEFAULT 0.00 COMMENT 'Impact financier estimé' AFTER `source`;

ALTER TABLE `reclamation` ADD COLUMN `fichier_joint` VARCHAR(255) COMMENT 'Nom du fichier joint' AFTER `estimation_cout`;

-- Ajouter les indices pour performance
ALTER TABLE `reclamation` ADD KEY `idx_priorite` (`priorite`);
ALTER TABLE `reclamation` ADD KEY `idx_categorie` (`categorie`);
ALTER TABLE `reclamation` ADD KEY `idx_agent` (`id_agent`);

-- ========================================================
-- 2️⃣ MODIFICATION TABLE REPONSE - Ajouter nouvelles colonnes
-- ========================================================

ALTER TABLE `reponse` ADD COLUMN `id_agent` INT COMMENT 'Agent qui a répondu' AFTER `id_reclamation`;

ALTER TABLE `reponse` ADD COLUMN `type_reponse` VARCHAR(50) DEFAULT 'Réponse' COMMENT 'Réponse, Escalade, Solution finale' AFTER `id_agent`;

ALTER TABLE `reponse` ADD COLUMN `lu` TINYINT DEFAULT 0 COMMENT 'Client a-t-il lu la réponse?' AFTER `type_reponse`;

ALTER TABLE `reponse` ADD COLUMN `date_lecture` DATETIME COMMENT 'Date de lecture par le client' AFTER `lu`;

ALTER TABLE `reponse` ADD COLUMN `feedback_client` INT COMMENT 'Évaluation de la réponse (1-5)' AFTER `date_lecture`;

ALTER TABLE `reponse` ADD COLUMN `fichier_joint` VARCHAR(255) COMMENT 'Nom du fichier joint' AFTER `feedback_client`;

-- ========================================================
-- 3️⃣ CRÉER NOUVELLE TABLE: AUDIT
-- ========================================================

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

-- ========================================================
-- 4️⃣ CRÉER NOUVELLE TABLE: NOTIFICATION
-- ========================================================

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

-- ========================================================
-- 5️⃣ CRÉER NOUVELLE TABLE: AGENT
-- ========================================================

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

-- ========================================================
-- 6️⃣ CRÉER NOUVELLE TABLE: RAPPORT
-- ========================================================

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

-- ========================================================
-- 7️⃣ INSÉRER DONNÉES D'EXEMPLE (optionnel)
-- ========================================================

-- Insérer des agents (exemples)
INSERT INTO `agent` (`nom`, `email`, `departement`, `statut`, `date_creation`) VALUES
('Jean Dupont', 'jean.dupont@company.com', 'Support', 'Actif', NOW()),
('Marie Martin', 'marie.martin@company.com', 'Facturation', 'Actif', NOW()),
('Pierre Bernard', 'pierre.bernard@company.com', 'Support', 'Actif', NOW());

-- ========================================================
-- ✅ RÉSUMÉ DES MODIFICATIONS
-- ========================================================
/*
✅ TABLE RECLAMATION:
   - 10 nouvelles colonnes ajoutées
   - 3 nouveaux indices créés
   
✅ TABLE REPONSE:
   - 6 nouvelles colonnes ajoutées
   
✅ 4 NOUVELLES TABLES:
   - AUDIT (historique des modifications)
   - NOTIFICATION (alertes agents)
   - AGENT (gestion des agents)
   - RAPPORT (rapports générés)

✅ 3 AGENTS D'EXEMPLE insérés

Total: +10 colonnes reclamation, +6 colonnes reponse, 4 tables, 3 agents

Les modifications sont rétro-compatibles avec les données existantes!
*/

-- ========================================================
-- FIN DES REQUÊTES SQL
-- ========================================================
