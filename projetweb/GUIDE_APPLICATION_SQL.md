# 📋 GUIDE COMPLET - REQUÊTES SQL À APPLIQUER

## 📂 Fichiers disponibles

1. **REQUETES_SQL_MODIFICATION.sql** - Version commentée + complète (pour lecture)
2. **REQUETES_SQL_COURTES.sql** - Version pour copier-coller direct (recommandée)

---

## 🚀 COMMENT APPLIQUER (3 MÉTHODES)

### ✅ MÉTHODE 1: phpMyAdmin (PLUS FACILE)

1. Ouvrez phpMyAdmin: `http://localhost/phpmyadmin`
2. Sélectionnez la base `projetr`
3. Allez dans l'onglet **SQL**
4. Copiez-collez le contenu de **REQUETES_SQL_COURTES.sql**
5. Cliquez sur **Exécuter** (Ctrl+Entrée)

### ✅ MÉTHODE 2: MySQL Workbench

1. Connectez-vous à MySQL
2. Sélectionnez la base `projetr`
3. Ouvrez un nouvel onglet SQL
4. Copiez-collez le contenu de **REQUETES_SQL_COURTES.sql**
5. Exécutez (Ctrl+Entrée)

### ✅ MÉTHODE 3: Ligne de commande

```bash
mysql -u root -p projetr < REQUETES_SQL_COURTES.sql
```

(Sans mot de passe si vide)

---

## 📝 RÉSUMÉ DES MODIFICATIONS

### 1️⃣ MODIFICATIONS TABLE `reclamation` (+10 colonnes)

```sql
ALTER TABLE `reclamation` 
ADD COLUMN `categorie` VARCHAR(50) DEFAULT 'Général',
ADD COLUMN `priorite` VARCHAR(20) DEFAULT 'Moyenne',
ADD COLUMN `email_client` VARCHAR(100),
ADD COLUMN `id_agent` INT,
ADD COLUMN `date_limite_resolution` DATETIME,
ADD COLUMN `date_resolution` DATETIME,
ADD COLUMN `satisfaction` INT,
ADD COLUMN `source` VARCHAR(50) DEFAULT 'Email',
ADD COLUMN `estimation_cout` DECIMAL(10, 2) DEFAULT 0.00,
ADD COLUMN `fichier_joint` VARCHAR(255),
ADD KEY `idx_priorite` (`priorite`),
ADD KEY `idx_categorie` (`categorie`),
ADD KEY `idx_agent` (`id_agent`);
```

**Nouvelles colonnes:**
| Colonne | Type | Défaut | Description |
|---------|------|--------|------------|
| `categorie` | VARCHAR(50) | 'Général' | Bug, Facturation, Service Client, Produit |
| `priorite` | VARCHAR(20) | 'Moyenne' | Basse, Moyenne, Haute, Critique |
| `email_client` | VARCHAR(100) | NULL | Email du client |
| `id_agent` | INT | NULL | ID de l'agent assigné |
| `date_limite_resolution` | DATETIME | NULL | Délai limite de résolution |
| `date_resolution` | DATETIME | NULL | Date réelle de résolution |
| `satisfaction` | INT | NULL | Note 1-5 |
| `source` | VARCHAR(50) | 'Email' | Email, Chat, Téléphone |
| `estimation_cout` | DECIMAL(10,2) | 0.00 | Coût estimé |
| `fichier_joint` | VARCHAR(255) | NULL | Nom fichier joint |

---

### 2️⃣ MODIFICATIONS TABLE `reponse` (+6 colonnes)

```sql
ALTER TABLE `reponse` 
ADD COLUMN `id_agent` INT,
ADD COLUMN `type_reponse` VARCHAR(50) DEFAULT 'Réponse',
ADD COLUMN `lu` TINYINT DEFAULT 0,
ADD COLUMN `date_lecture` DATETIME,
ADD COLUMN `feedback_client` INT,
ADD COLUMN `fichier_joint` VARCHAR(255);
```

**Nouvelles colonnes:**
| Colonne | Type | Défaut | Description |
|---------|------|--------|------------|
| `id_agent` | INT | NULL | Agent qui a répondu |
| `type_reponse` | VARCHAR(50) | 'Réponse' | Réponse, Escalade, Solution |
| `lu` | TINYINT | 0 | Client a-t-il lu? (0/1) |
| `date_lecture` | DATETIME | NULL | Quand le client a lu |
| `feedback_client` | INT | NULL | Évaluation 1-5 |
| `fichier_joint` | VARCHAR(255) | NULL | Nom fichier joint |

---

### 3️⃣ CRÉER TABLE `audit` (Historique)

```sql
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
```

**Colonnes:**
- Trace chaque modification (création, changement, suppression)
- Enregistre l'utilisateur, l'IP, la date
- Garde l'ancien et nouvel statut
- Lié à `reclamation` par clé étrangère

---

### 4️⃣ CRÉER TABLE `notification` (Alertes)

```sql
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
```

**Types de notifications:**
- Nouvelle_reclamation
- Reponse_client
- Escalade
- Depassement_delai

---

### 5️⃣ CRÉER TABLE `agent` (Gestion agents)

```sql
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
```

**Colonnes:**
- Nom complet de l'agent
- Email unique
- Département (Support, Facturation, etc)
- Statut (Actif/Inactif)
- Date d'embauche

---

### 6️⃣ CRÉER TABLE `rapport` (Rapports générés)

```sql
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
```

**Types de rapports:**
- Mensuel
- Trimestriel
- Annuel
- Custom (personnalisé)

**Statistiques:** Stockées en JSON

---

### 7️⃣ INSÉRER DONNÉES D'EXEMPLE (Agents)

```sql
INSERT INTO `agent` (`nom`, `email`, `departement`, `statut`, `date_creation`) VALUES
('Jean Dupont', 'jean.dupont@company.com', 'Support', 'Actif', NOW()),
('Marie Martin', 'marie.martin@company.com', 'Facturation', 'Actif', NOW()),
('Pierre Bernard', 'pierre.bernard@company.com', 'Support', 'Actif', NOW());
```

---

## ✅ VÉRIFIER L'APPLICATION

Après avoir exécuté les requêtes, vérifiez:

```sql
-- Voir les nouvelles colonnes de reclamation
DESC reclamation;

-- Voir les nouvelles colonnes de reponse
DESC reponse;

-- Voir les nouvelles tables
SHOW TABLES;

-- Voir les agents insérés
SELECT * FROM agent;

-- Voir les structures des nouvelles tables
DESC audit;
DESC notification;
DESC rapport;
```

---

## 📊 STATUT DE L'APPLICATION

Avant modification:
- ❌ 2 tables (reclamation, reponse)
- ❌ 5 colonnes reclamation
- ❌ 3 colonnes reponse
- ❌ Pas de tracking
- ❌ Pas d'alertes

Après modification:
- ✅ 6 tables
- ✅ 15 colonnes reclamation (+10)
- ✅ 9 colonnes reponse (+6)
- ✅ Audit complet
- ✅ Système notifications
- ✅ Gestion agents
- ✅ Rapports automatisés

---

## 🔄 RÉTRO-COMPATIBILITÉ

✅ **Toutes les modifications sont compatibles avec les données existantes!**

- Les nouvelles colonnes ont des valeurs par défaut
- Les données existantes ne sont pas modifiées
- Les clés étrangères relient correctement les tables
- Aucune suppression de données

---

## ⚠️ IMPORTANT

1. **Faire une sauvegarde avant** (pour sécurité)
2. **Exécuter les requêtes dans l'ordre** (audit, notification, agent d'abord)
3. **Vérifier qu'aucune erreur** ne s'affiche
4. **Les indices améliorent les performances** - Les garder!
5. **Les colonnes NULL acceptent les valeurs manquantes** - Normal!

---

## 🎯 PROCHAINES ÉTAPES

1. ✅ Appliquer les requêtes SQL
2. ✅ Vérifier les tables créées
3. ✅ Copier les fichiers PHP (model, controller, view, api)
4. ✅ Accéder au dashboard: `http://localhost/projetweb/view/backoffice/dashboard.php`

---

## 📞 SUPPORT

En cas de problème:
- Vérifier que la base `projetr` existe
- Vérifier les droits utilisateur MySQL
- Vérifier que les tables `reclamation` et `reponse` existent
- Vérifier le charset `utf8mb4`

**Fichiers à copier:**
- `model/` → Tous les fichiers .php
- `controller/` → Tous les fichiers .php
- `view/backoffice/` → dashboard.php, rapports.php, audit.php
- `api/` → Tous les fichiers .php

