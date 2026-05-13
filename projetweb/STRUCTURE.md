```
projetweb/                          # Racine du projet
│
├── 📄 index.php                    # Accueil principal de l'application
├── 📄 connexion.php                # Connexion à la base de données
├── 📄 config.php                   # Configuration globale (constantes)
├── 📄 setup.sql                    # Script SQL pour créer la BD et données
│
├── 📄 README.md                    # Documentation complète du projet
├── 📄 GUIDE_DEMARRAGE.md          # Guide d'installation et démarrage
├── 📄 STRUCTURE.md                 # Ce fichier
│
├── 📁 model/                       # Modèles de données (Couche Données)
│   ├── reclamation.php             # Classe Reclamation (CRUD + validation)
│   └── reponse.php                 # Classe Reponse (CRUD + validation)
│
├── 📁 controller/                  # Contrôleurs (Logique métier)
│   └── reclamation_controller.php  # Contrôleur principal + endpoints AJAX
│
├── 📁 view/                        # Vues (Interface utilisateur)
│   ├── 📁 frontoffice/             # Interface client (Public)
│   │   └── index.php               # Portal utilisateur
│   │                               # - Créer réclamation
│   │                               # - Voir réclamations
│   │                               # - Voir réponses
│   │
│   └── 📁 backoffice/              # Interface administrateur (Privée)
│       └── index.php               # Dashboard admin
│                                   # - Tableau des réclamations
│                                   # - Filtres par statut
│                                   # - Gestion des réponses
│                                   # - Statistiques
│
├── 📁 css/                         # Feuilles de style
│   ├── style.css                   # Styles généraux + animations
│   │                               # (Tous les éléments, boutons, modals, etc.)
│   ├── frontoffice.css             # Styles spécifiques au frontend
│   │                               # (Cards, section formulaire, etc.)
│   └── backoffice.css              # Styles spécifiques au backend
│                                   # (Sidebar, tableaux, statuts, etc.)
│
└── 📁 js/                          # Scripts JavaScript
    ├── validation.js               # Fonctions de validation et utilitaires
    │                               # - Validation longueur/email
    │                               # - Gestion des erreurs
    │                               # - Formes dates
    ├── frontoffice.js              # Logique partie client
    │                               # - Charger réclamations
    │                               # - Créer réclamation
    │                               # - Afficher détails
    │                               # - Gestion modals
    └── backoffice.js               # Logique partie admin
                                    # - Charger tableau
                                    # - Filtrer statut
                                    # - Mettre à jour statut
                                    # - Ajouter réponse
                                    # - Statistiques
```

## 📊 Flux de Données

```
┌─────────────────────────────────────────────────────────────┐
│               ARCHITECTURE MVC                               │
└─────────────────────────────────────────────────────────────┘

REQUEST CLIENT
    │
    ├─ Frontoffice: /view/frontoffice/index.php
    │   ├─> frontoffice.js (AJAX)
    │   ├─> validation.js (Validation client)
    │   └─> CSS (style.css + frontoffice.css)
    │
    ├─ Backoffice: /view/backoffice/index.php
    │   ├─> backoffice.js (AJAX)
    │   ├─> validation.js (Validation client)
    │   └─> CSS (style.css + backoffice.css)
    │
    └─> AJAX POST/GET
        │
        ├─> /controller/reclamation_controller.php
        │   │
        │   ├─ Action: creer → Reclamation::ajouter()
        │   ├─ Action: repondre → Reponse::ajouter()
        │   ├─ Action: update_statut → Reclamation::modifier()
        │   ├─ Action: supprimer → Reclamation::supprimer()
        │   ├─ Action: lister → Reclamation::obtenirTous()
        │   ├─ Action: detail → Reclamation::obtenirParId()
        │   └─ Action: stats → Statistiques
        │
        ├─> connexion.php (mysqli)
        │
        ├─> model/reclamation.php
        │   ├─ Propriétés: id, titre, description, date, status
        │   └─ Méthodes: ajouter(), modifier(), supprimer(), valider(), etc.
        │
        └─> model/reponse.php
            ├─ Propriétés: id_reponse, reponse, date, id_reclamation
            └─ Méthodes: ajouter(), modifier(), supprimer(), valider(), etc.

        │
        └─> Base de Données (MySQL - projetr)
            ├─ Table: reclamation
            │   ├─ id (PK)
            │   ├─ titre
            │   ├─ description
            │   ├─ date
            │   └─ status
            │
            └─ Table: reponse
                ├─ id_reponse (PK)
                ├─ reponse
                ├─ date
                └─ id_reclamation (FK -> reclamation.id)

RESPONSE (JSON)
    │
    └─> JavaScript affiche/met à jour le DOM
        └─> Utilisateur voit les résultats
```

## 🔐 Sécurité

- ✅ **Validation côté client** (JavaScript)
- ✅ **Validation côté serveur** (PHP - Classes Model)
- ✅ **Échappement HTML** (`htmlspecialchars()`)
- ✅ **Protection SQL Injection** (Prepared Statements)
- ✅ **Protection XSS** (DOMContentLoaded, test input)

## 📋 Entités Principales

### Reclamation
```php
Propriétés:
- id (INT, AUTO_INCREMENT, PRIMARY KEY)
- titre (VARCHAR 100)
- description (TEXT)
- date (DATETIME)
- status (VARCHAR 20: En attente|En cours|Résolu|Rejeté)

Méthodes:
- ajouter(): Créer une réclamation
- obtenirTous(): Récupérer toutes les réclamations
- obtenirParId($id): Récupérer une réclamation
- modifier(): Mettre à jour une réclamation
- supprimer($id): Supprimer une réclamation
- valider(): Valider les données
```

### Reponse
```php
Propriétés:
- id_reponse (INT, AUTO_INCREMENT, PRIMARY KEY)
- reponse (TEXT)
- date (DATETIME)
- id_reclamation (INT, FOREIGN KEY)

Méthodes:
- ajouter(): Créer une réponse
- obtenirParReclamation($id): Récupérer les réponses
- obtenirParId($id): Récupérer une réponse
- modifier(): Mettre à jour une réponse
- supprimer($id): Supprimer une réponse
- valider(): Valider les données
```

## 🎨 Thématique Design

**Économie Digitale, Entrepreneuriat, Avenir du Travail**

### Couleurs Principales
- **Vert (#00a86b)** - Croissance, succès, confiance
- **Bleu (#003d7d)** - Professionnalisme, stabilité
- **Orange (#ff6b35)** - Énergie, innovation, créativité

### Éléments Design
- ✨ Animations fluides et transitions
- 📱 Design responsive (Mobile-first)
- 🎯 Icons emoji pour meilleure UX
- 📊 Cards modernes avec shadows
- 🔘 Boutons interactifs avec hover effects

## 📝 Fonctionnalités Principales

### Frontend (Clients)
✅ Nouvelle réclamation
✅ Validation formulaire
✅ Voir ses réclamations
✅ Afficher détails + réponses
✅ Interface responsive

### Backend (Admin)
✅ Tableau de toutes les réclamations
✅ Filtrer par statut
✅ Voir détails complètes
✅ Changer statut
✅ Ajouter réponse
✅ Supprimer réclamation
✅ Statistiques en temps réel

## 🚀 Points Clés

1. **MVC Pattern** - Séparation claire des responsabilités
2. **AJAX** - Communication asynchrone sans rechargement
3. **Sécurité** - Validation + Échappement + Prepared Statements
4. **UX** - Animations, messages feedback, etc.
5. **Responsive** - Fonctionne sur tous les appareils
6. **Extensible** - Facile d'ajouter de nouvelles fonctionnalités

## 📞 Support Technique

Pour des problèmes, consultez:
1. **GUIDE_DEMARRAGE.md** - Installation et Configuration
2. **README.md** - Documentation complète
3. **Logs PHP** - Erreurs dans XAMPP
4. **phpMyAdmin** - État de la base de données

---

**Version**: 1.0.0
**Date**: 2026-05-06
**Développé pour**: E-Business Platform
**Thème**: Économie Digitale & Entrepreneuriat
