# 📊 GUIDE - Fonctionnalités Avancées Implémentées

## 🎯 Vue d'ensemble

Vous avez maintenant 4 fonctionnalités avancées prêtes à l'emploi:

1. **Dashboard Analytique** - Statistiques et métriques de performance
2. **Notifications** - Alertes aux agents de nouvelles réclamations
3. **Historique/Audit** - Suivi complet de toutes les modifications
4. **Rapports** - Export et génération de rapports mensuels/trimestriels

---

## 📈 1. DASHBOARD ANALYTIQUE

### Accès
```
http://localhost/projetweb/view/backoffice/dashboard.php
```

### Fonctionnalités
- **Résumé rapide**: Total, En attente, En retard, Taux de résolution, Temps moyen, Satisfaction
- **Graphiques en temps réel**:
  - Distribution par statut (doughnut)
  - Distribution par priorité (barres)
  - Volume mensuel (courbes)
  - Satisfaction par agent (barres)
- **Tableau des réclamations en retard** avec détails
- **Export JSON/CSV** du dashboard

### Métriques clés affichées
```
- Total réclamations
- Statut (En attente, En cours, Résolu, Rejeté)
- Priorité (Basse, Moyenne, Haute, Critique)
- Catégorie (Bug, Facturation, Service Client, Produit)
- Temps moyen de résolution (heures et jours)
- Satisfaction moyenne (1-5 stars)
- Classement des agents
- Source des réclamations
```

---

## 🔔 2. NOTIFICATIONS

### Modèle créé
- **model/notification.php** - Gestion des notifications

### Accès
```
Les notifications s'affichent dans le backoffice pour chaque agent
```

### Types de notifications
- **Nouvelle_reclamation** - Quand une nouvelle réclamation est créée
- **Reponse_client** - Quand le client répond à une réclamation
- **Escalade** - Quand une réclamation est escaladée
- **Depassement_delai** - Quand le délai de résolution est dépassé

### Fonctionnalités
```php
// Créer une notification
$notification = new Notification();
$notification->notifierNouvelleReclamation($id_reclamation, $titre);

// Obtenir les non-lues
$non_lues = $notification->obtenirNonLuesAgent($id_agent);

// Marquer comme lue
$notification->marquerCommeLue($id_notification);

// Compter les non-lues
$count = $notification->compterNonLuesAgent($id_agent);
```

---

## 🔍 3. HISTORIQUE / AUDIT TRAIL

### Accès
```
http://localhost/projetweb/view/backoffice/audit.php
```

### Fonctionnalités
- **Historique complet** de toutes les modifications
- **Filtrage** par type, date
- **Détails tracés**:
  - Type de modification (Création, Modification, Suppression, Changement de statut)
  - Ancien et nouveau statut
  - Champ modifié et ses valeurs
  - Utilisateur qui a effectué l'action
  - Date/heure exacte
  - Adresse IP
- **Export en CSV**

### Tables de base de données
```sql
-- Audit trail complet
CREATE TABLE audit (
  id_audit INT AUTO_INCREMENT,
  id_reclamation INT,
  type_modification VARCHAR(50),
  ancien_statut VARCHAR(20),
  nouveau_statut VARCHAR(20),
  champ_modifie VARCHAR(100),
  ancienne_valeur LONGTEXT,
  nouvelle_valeur LONGTEXT,
  id_utilisateur INT,
  nom_utilisateur VARCHAR(100),
  date_modification DATETIME,
  adresse_ip VARCHAR(45),
  PRIMARY KEY (id_audit)
);
```

### Utilisation
```php
$audit = new Audit();

// Enregistrer une création
$audit->enregistrerCreation($id_reclamation, $id_utilisateur, 'Jean Dupont');

// Enregistrer un changement de statut
$audit->enregistrerChangementStatut($id_reclamation, 'En attente', 'En cours', $id_utilisateur);

// Obtenir l'historique
$historique = $audit->obtenirHistorique($id_reclamation);

// Obtenir modifications par période
$modif = $audit->obtenirParPeriode($date_debut, $date_fin);
```

---

## 📋 4. RAPPORTS / EXPORTS

### Accès
```
http://localhost/projetweb/view/backoffice/rapports.php
```

### Types de rapports
1. **Mensuel** - Rapport du mois courant
2. **Trimestriel** - Rapport du trimestre courant
3. **Annuel** - Rapport de l'année courante
4. **Personnalisé** - Période personnalisée

### Statistiques incluses
```
- Total réclamations de la période
- Répartition par statut
- Satisfaction moyenne
- Temps moyen de résolution par catégorie
- Performance par agent
- Coût total estimé
```

### Formats d'export
- **CSV** - Feuille de calcul (compatible Excel)
- **PDF** - Document complet (nécessite mPDF)
- **JSON** - Format données structurées

### Fichiers créés
```
model/rapport.php                    - Gestion des rapports
controller/report_controller.php      - Logique de génération
view/backoffice/rapports.php          - Interface
api/generer_rapport.php               - API génération
api/telecharger_rapport.php           - API téléchargement
api/supprimer_rapport.php             - API suppression
```

### Utilisation
```php
$reportController = new ReportController();

// Générer rapport mensuel
$rapport = $reportController->genererRapportMensuel(5, 2026);

// Générer rapport personnalisé
$rapport = $reportController->genererRapportPersonnalise('2026-05-01', '2026-05-31', 'Mon Rapport');

// Exporter en CSV
$csv = $reportController->exporterCSV($id_rapport);

// Exporter en PDF
$pdf = $reportController->exporterPDF($id_rapport);

// Obtenir tous les rapports
$rapports = $reportController->obtenirRapports();
```

---

## 🗄️ MODIFICATION BASE DE DONNÉES

### Nouvelles colonnes pour RECLAMATION
```sql
ALTER TABLE reclamation ADD COLUMN:
- categorie VARCHAR(50)              -- Bug, Facturation, Service Client, Produit, Autre
- priorite VARCHAR(20)               -- Basse, Moyenne, Haute, Critique
- email_client VARCHAR(100)          -- Email du client
- id_agent INT                       -- Agent assigné
- date_limite_resolution DATETIME    -- Délai de résolution
- date_resolution DATETIME           -- Date réelle de résolution
- satisfaction INT                   -- Note 1-5
- source VARCHAR(50)                 -- Email, Chat, Téléphone, Autre
- estimation_cout DECIMAL(10, 2)    -- Impact financier
- fichier_joint VARCHAR(255)        -- Pièce jointe
```

### Nouvelles colonnes pour REPONSE
```sql
ALTER TABLE reponse ADD COLUMN:
- id_agent INT                      -- Qui a répondu
- type_reponse VARCHAR(50)          -- Réponse, Escalade, Solution finale
- lu TINYINT                        -- Client a-t-il lu?
- date_lecture DATETIME             -- Quand lu?
- feedback_client INT               -- Évaluation 1-5
- fichier_joint VARCHAR(255)        -- Pièce jointe
```

### Nouvelles tables
```sql
1. AUDIT - Historique complet
2. NOTIFICATION - Alertes agents
3. AGENT - Gestion des agents
4. RAPPORT - Rapports générés
```

---

## 🔗 INTÉGRATION AVEC CODE EXISTANT

### Mise à jour du contrôleur Reclamation
Ajouter à `reclamation_controller.php`:

```php
require_once '../model/audit.php';
require_once '../model/notification.php';

// Dans la méthode creer()
$audit = new Audit();
$audit->enregistrerCreation($reclamation_id, $id_utilisateur);

$notification = new Notification();
$notification->notifierNouvelleReclamation($reclamation_id, $titre);

// Dans mettreAJourStatut()
$audit->enregistrerChangementStatut($id, $ancien_statut, $nouveau_statut, $id_utilisateur);
```

---

## 📁 STRUCTURE DE FICHIERS CRÉÉS

```
model/
├── audit.php                        - Audit trail
├── notification.php                 - Notifications
├── agent.php                        - Gestion agents
├── statistiques.php                 - Statistiques
└── rapport.php                      - Rapports

controller/
├── dashboard_controller.php          - Dashboard
├── report_controller.php             - Rapports
└── notification_controller.php       - Notifications & Audit

view/backoffice/
├── dashboard.php                    - Dashboard analytique
├── rapports.php                     - Interface rapports
└── audit.php                        - Audit trail

api/
├── generer_rapport.php              - Génération rapports
├── telecharger_rapport.php          - Téléchargement
├── supprimer_rapport.php            - Suppression
├── export.php                       - Export dashboard
└── export_audit.php                 - Export audit

exports/                             - Dossier de sauvegarde des fichiers
```

---

## 🚀 PROCHAINES ÉTAPES

1. **Installer mPDF** pour export PDF (optionnel)
   ```bash
   composer require mpdf/mpdf
   ```

2. **Créer page agent** pour afficher les notifications

3. **Ajouter intégration email** pour alertes temps réel

4. **Implémenter dashboard agent** personnalisé

5. **Ajouter graphiques interactifs** (Chart.js)

6. **Configurer les SLA** (délais de résolution)

---

## 💡 EXEMPLES D'UTILISATION

### Créer une réclamation ET enregistrer l'audit
```php
$reclamation = new Reclamation();
$reclamation->titre = "Bug critique";
$reclamation->description = "...";
$reclamation->date = date('Y-m-d H:i:s');
$reclamation->status = 'En attente';
$reclamation->priorite = 'Critique';
$reclamation->categorie = 'Bug';
$reclamation->email_client = 'client@example.com';

$id = $reclamation->ajouter();

// Enregistrer dans l'audit
$audit = new Audit();
$audit->enregistrerCreation($id, 1, 'Admin');

// Notifier les agents
$notification = new Notification();
$notification->notifierNouvelleReclamation($id, $reclamation->titre);
```

### Obtenir le dashboard complet
```php
$dashboard = new DashboardController();
$data = $dashboard->obtenirTableauBord();

echo "Total: " . $data['stats_globales']['total_reclamations'];
echo "Satisfaction: " . $data['satisfaction']['moyenne'];
echo "En retard: " . count($data['reclamations_en_retard']);
```

### Générer un rapport mensuel
```php
$report = new ReportController();
$result = $report->genererRapportMensuel(5, 2026);

if ($result['success']) {
    $csv = $report->exporterCSV($result['id_rapport']);
    // Télécharger le fichier
}
```

---

## 📞 SUPPORT & QUESTIONS

Pour toute question ou amélioration, consultez la documentation des modèles individuels.

**Dernière mise à jour:** May 12, 2026
