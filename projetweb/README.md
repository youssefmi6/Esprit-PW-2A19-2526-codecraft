# 🚀 E-Business - Plateforme de Gestion des Réclamations

## 📋 Description du Projet

Une application web complète pour la gestion des réclamations clients avec une interface frontend pour les utilisateurs et un dashboard backend pour l'administration.

**Thématique**: Économie Digitale, Entrepreneuriat et Avenir du Travail

## 🏗️ Structure du Projet

```
projetweb/
├── connexion.php              # Connexion à la base de données
├── index.php                  # Accueil du site
├── model/
│   ├── reclamation.php        # Modèle Reclamation
│   └── reponse.php            # Modèle Reponse
├── controller/
│   └── reclamation_controller.php    # Contrôleur Reclamation
├── view/
│   ├── frontoffice/           # Interface utilisateur (client)
│   │   └── index.php
│   └── backoffice/            # Interface administration
│       └── index.php
├── css/
│   ├── style.css              # Styles généraux
│   ├── frontoffice.css        # Styles frontend
│   └── backoffice.css         # Styles backend
└── js/
    ├── validation.js          # Validation et utilitaires
    ├── frontoffice.js         # Fonctions frontend
    └── backoffice.js          # Fonctions backend
```

## 🗄️ Base de Données

### Configuration

1. **Dans phpMyAdmin /XAMPP**:
   - Créer une base de données nommée `projetr`
   - Exécuter le script SQL fourni ci-dessous

### Script SQL

```sql
-- Créer la table reclamation
CREATE TABLE `reclamation` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `titre` VARCHAR(100) NOT NULL,
  `description` TEXT NOT NULL,
  `date` DATETIME NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'En attente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Créer la table reponse
CREATE TABLE `reponse` (
  `id_reponse` INT AUTO_INCREMENT PRIMARY KEY,
  `reponse` TEXT NOT NULL,
  `date` DATETIME NOT NULL,
  `id_reclamation` INT NOT NULL,
  FOREIGN KEY (`id_reclamation`) REFERENCES `reclamation`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Index pour optimiser les requêtes
CREATE INDEX `idx_status` ON `reclamation`(`status`);
CREATE INDEX `idx_reclamation` ON `reponse`(`id_reclamation`);
```

## 🔧 Configuration

### Fichier `connexion.php`

```php
define('DB_HOST', 'localhost');      // Serveur MySQL
define('DB_USER', 'root');           // Utilisateur MySQL
define('DB_PASS', '');               // Mot de passe (vide par défaut)
define('DB_NAME', 'projetr');        // Nom de la base de données
```

**Modifiez ces variables selon votre configuration XAMPP**.

## 📱 Fonctionnalités

### Frontend (Portal Utilisateur)
- ✅ Consulter les réclamations
- ✅ Créer une nouvelle réclamation avec validation
- ✅ Voir les détails et réponses
- ✅ Interface moderne et responsive

### Backend (Dashboard Admin)
- ✅ Voir toutes les réclamations
- ✅ Filtrer par statut
- ✅ Mettre à jour le statut d'une réclamation
- ✅ Ajouter une réponse à une réclamation
- ✅ Supprimer une réclamation
- ✅ Voir les statistiques

## 🎨 Design

- **Theme**: Économie Digitale & Entrepreneuriat
- **Couleurs**:
  - Vert (#00a86b) - Succès, croissance
  - Bleu (#003d7d) - Professionnalisme, confiance
  - Orange (#ff6b35) - Énergie, innovation
- **Responsive**: Mobile, Tablette, Desktop
- **Animations**: Transitions fluides et effets hover

## 🔐 Sécurité

- ✅ Validation côté client (JavaScript)
- ✅ Validation côté serveur (PHP)
- ✅ Échappement HTML (protection XSS)
- ✅ Prepared Statements (protection SQL Injection)
- ✅ Nettoyage des données avec `htmlspecialchars()`

## 🚀 Installation & Utilisation

### 1. Préparation
- Placer le dossier `projetweb` dans `C:\xampp\htdocs\`
- Démarrer XAMPP (Apache et MySQL)

### 2. Base de Données
- Ouvrir phpMyAdmin (`http://localhost/phpmyadmin`)
- Créer la base de données `projetr`
- Exécuter le script SQL fourni

### 3. Accès à l'Application
- **Accueil**: `http://localhost/projetweb/`
- **Portal Utilisateur**: `http://localhost/projetweb/view/frontoffice/`
- **Dashboard Admin**: `http://localhost/projetweb/view/backoffice/`

## 📊 Guide Utilisateur

### Pour les Clients
1. Accéder au portal utilisateur
2. Remplir et valider le formulaire de réclamation
3. Consulter l'état de la réclamation dans "Mes Réclamations"
4. Voir les réponses reçues

### Pour les Administrateurs
1. Accéder au dashboard admin
2. Utiliser le sidebar pour filtrer les réclamations
3. Cliquer sur "Voir" pour afficher les détails
4. Changer le statut de la réclamation
5. Ajouter une réponse aux clients
6. Voir les statistiques en temps réel

## 🛠️ Technologies Utilisées

- **Backend**: PHP 7+
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Base de Données**: MySQL
- **Architecture**: MVC (Model-View-Controller)

## 📝 Statuts de Réclamation

- **En attente** ⏳: Nouvelle réclamation en attente de traitement
- **En cours** 🔄: Réclamation en cours de traitement
- **Résolu** ✅: Réclamation résolue
- **Rejeté** ❌: Réclamation rejetée

## 📞 Support

Pour toute question ou problème:
1. Vérifiez la connexion MySQL
2. Assurez-vous que la base de données existe
3. Vérifiez les droits d'accès aux fichiers

## 📄 Licence

Projet éducatif - Libre d'utilisation

---

**Version**: 1.0  
**Date**: 2026-05-06  
**Développé pour**: E-Business Platform
