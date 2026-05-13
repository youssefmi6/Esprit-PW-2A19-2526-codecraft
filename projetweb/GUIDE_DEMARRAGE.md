# 🚀 Guide de Démarrage - E-Business Réclamations

## ✅ Prérequis

- **XAMPP** installé (Apache, MySQL, PHP)
- **Navigateur web** moderne (Chrome, Firefox, Edge)
- **Accès** à la console/terminal (optionnel)

---

## 📋 Étapes d'Installation

### Étape 1: Préparer les fichiers

1. Localiser le dossier XAMPP:
   ```
   C:\xampp\htdocs\
   ```

2. Le dossier `projetweb` doit être placé à:
   ```
   C:\xampp\htdocs\projetweb\
   ```

3. Vérifier la structure:
   ```
   projetweb/
   ├── connexion.php
   ├── config.php
   ├── index.php
   ├── setup.sql
   ├── README.md
   ├── model/
   ├── controller/
   ├── view/
   ├── css/
   └── js/
   ```

### Étape 2: Démarrer XAMPP

1. Ouvrir le **Control Panel de XAMPP**
2. Cliquer sur **"Start"** pour Apache
3. Cliquer sur **"Start"** pour MySQL
4. Attendre que les services passent au vert

### Étape 3: Créer la Base de Données

**Méthode 1: Via phpMyAdmin (Graphique)**

1. Ouvrir: `http://localhost/phpmyadmin`
2. Cliquer sur **"Nouvelle base de données"**
3. Entrer le nom: `projetr`
4. Sélectionner le collation: `utf8mb4_unicode_ci`
5. Cliquer sur **"Créer"**
6. Sélectionner la base `projetr`
7. Cliquer sur l'onglet **"SQL"**
8. Copier-coller le contenu du fichier `setup.sql`
9. Cliquer sur **"Exécuter"**

**Méthode 2: Via le Command Line**

1. Ouvrir le terminal/CMD dans `C:\xampp\mysql\bin\`
2. Exécuter:
   ```bash
   mysql -u root -p < "C:\xampp\htdocs\projetweb\setup.sql"
   ```
   (Sans mot de passe si demandé)

### Étape 4: Configurer la Connexion (Optionnel)

Si votre configuration MySQL est différente:

1. Ouvrir `C:\xampp\htdocs\projetweb\config.php`
2. Modifier les paramètres:
   ```php
   define('DB_HOST', 'localhost');   // Serveur
   define('DB_USER', 'root');        // Utilisateur
   define('DB_PASS', '');            // Mot de passe
   define('DB_NAME', 'projetr');     // Base de données
   ```
3. Sauvegarder le fichier

---

## 🌐 Accès à l'Application

Une fois tout configuré, accéder à:

### Accueil Principal
```
http://localhost/projetweb/
```
📌 La page d'accueil avec liens vers les portals

### Portal Utilisateur (Front Office)
```
http://localhost/projetweb/view/frontoffice/
```
✅ Pour les clients:
- Créer une réclamation
- Consulter ses réclamations
- Voir les réponses reçues

### Dashboard Administration (Back Office)
```
http://localhost/projetweb/view/backoffice/
```
✅ Pour les administrateurs:
- Voir toutes les réclamations
- Filtrer par statut
- Ajouter des réponses
- Modifier les statuts
- Voir les statistiques

---

## 🧪 Test Rapide

### Pour Tester le Frontend

1. Aller à: `http://localhost/projetweb/view/frontoffice/`
2. Remplir le formulaire:
   - **Titre**: "Test de réclamation"
   - **Description**: "Ceci est un test de réclamation pour vérifier le système"
3. Cliquer sur **"Soumettre ma Réclamation"**
4. Vérifier que la réclamation apparaît dans "Mes Réclamations"

### Pour Tester le Backend

1. Aller à: `http://localhost/projetweb/view/backoffice/`
2. Voir la réclamation créée dans le tableau
3. Cliquer sur **"Voir"** pour afficher les détails
4. Changer le statut de la réclamation
5. Ajouter une réponse
6. Vérifier que les modifications sont appliquées

---

## 🔧 Troubleshooting

### ❌ Erreur: "Erreur de connexion à la base de données"

**Solution**:
1. Vérifier que MySQL est démarré dans XAMPP
2. Vérifier le nom de la base de données dans `config.php`
3. Vérifier les identifiants: `root` / vide
4. Tester via phpMyAdmin

### ❌ Page blanche ou erreur 500

**Solution**:
1. Vérifier que tous les fichiers PHP sont présents
2. Vérifier les droits d'accès aux dossiers
3. Vérifier les logs PHP dans XAMPP

### ❌ CSS/JavaScript ne se charge pas

**Solution**:
1. Vérifier les chemins des fichiers CSS/JS
2. Vérifier les droits d'accès aux fichiers
3. Vider le cache du navigateur (Ctrl+Shift+Delete)

### ❌ La base de données n'existe pas

**Solution**:
1. Re-exécuter le script `setup.sql`
2. Vérifier que `projetr` apparaît dans phpMyAdmin
3. Exécuter manuellement le SQL fourni

---

## 📞 Commandes Utiles

### Redémarrer MySQL
```bash
C:\xampp\mysql\bin\mysql -u root
> FLUSH PRIVILEGES;
> EXIT;
```

### Exporter une Sauvegarde
```bash
mysqldump -u root projetr > sauvegarde.sql
```

### Importer une Sauvegarde
```bash
mysql -u root projetr < sauvegarde.sql
```

---

## 🏗️ Architecture du Projet

### Modèle MVC

```
MODEL (model/)
├── reclamation.php    → Gère les données Reclamation
└── reponse.php        → Gère les données Reponse
                        (Accès à la BD, CRUD)

CONTROLLER (controller/)
└── reclamation_controller.php  → Logique métier
                                 (Validation, traitements)

VIEW (view/)
├── frontoffice/       → Interface utilisateur
│   └── index.php      (Clients - Créer & consulter)
└── backoffice/        → Interface administration
    └── index.php      (Admin - Gestion complète)

ASSETS
├── css/               → Styles (style.css, frontoffice.css, backoffice.css)
├── js/                → Scripts (validation.js, frontoffice.js, backoffice.js)
└── images/            (À créer selon besoin)
```

---

## 📊 Diagramme de Flux

```
CLIENT FLUX:
┌─────────────────┐
│   Accueil       │
└────────┬────────┘
         │
┌────────▼───────────────┐
│  Portal Utilisateur    │
│  - Créer réclamation  │
│  - Voir réclamations  │
└────────┬───────────────┘
         │
    (AJAX via JS)
         │
┌────────▼────────────────────┐
│   reclamation_controller    │
│   - Créer                   │
│   - Lister                  │
│   - Afficher détails        │
└────────┬────────────────────┘
         │
┌────────▼──────────┐
│  Model (PHP)      │
│  - reclamation.php│
│  - reponse.php    │
└────────┬──────────┘
         │
┌────────▼──────────┐
│  Base de données  │
│  - Table reclamation
│  - Table reponse
└─────────────────┘
```

---

## 🎨 Personnalisation

### Modifier les Couleurs

Fichier: `css/style.css`

```css
:root {
    --primary: #00a86b;      /* Couleur principale (vert) */
    --secondary: #003d7d;    /* Couleur secondaire (bleu) */
    --accent: #ff6b35;       /* Couleur accent (orange) */
    --success: #28a745;      /* Succès (vert) */
    --danger: #dc3545;       /* Danger (rouge) */
}
```

### Modifier les Textes/Messages

Fichiers:
- `view/frontoffice/index.php` → Textes interface client
- `view/backoffice/index.php` → Textes interface admin
- `js/frontoffice.js` → Messages JS frontend
- `js/backoffice.js` → Messages JS backend

---

## 📚 Documentation Supplémentaire

- [README.md](README.md) - Documentation complète du projet
- [setup.sql](setup.sql) - Script de création de BD
- [config.php](config.php) - Configuration globale

---

## ✨ Prochaines Étapes

Après l'installation:

1. ✅ Tester les fonctionnalités
2. 🔐 Ajouter l'authentification (login)
3. 📧 Implémenter les notifications par email
4. 📱 Optimiser pour mobile
5. 🔄 Ajouter pagination pour les listes
6. 📊 Améliorer les statistiques

---

**Version du Guide**: 1.0  
**Date**: 2026-05-06  
**Dernière mise à jour**: 2026-05-06

Pour toute question, consultez le README.md ou la documentation inline dans les fichiers.
