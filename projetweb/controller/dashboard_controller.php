<?php
require_once __DIR__ . '/../model/statistiques.php';
require_once __DIR__ . '/../model/audit.php';
require_once __DIR__ . '/../model/notification.php';

class DashboardController {
    private $statistiques;
    private $audit;
    private $notification;
    
    public function __construct() {
        $this->statistiques = new Statistiques();
        $this->audit = new Audit();
        $this->notification = new Notification();
    }
    
    // Obtenir le tableau de bord complet
    public function obtenirTableauBord() {
        return array(
            'stats_globales' => $this->statistiques->obtenirStatsGlobales(),
            'temps_resolution' => $this->statistiques->obtenirTempsResolutionMoyen(),
            'satisfaction' => $this->statistiques->obtenirSatisfactionMoyenne(),
            'volume_mensuel' => $this->statistiques->obtenirVolumeMensuel(12),
            'volume_quotidien' => $this->statistiques->obtenirVolumeQuotidien(),
            'classement_agents' => $this->statistiques->obtenirClassementAgents(),
            'reclamations_en_retard' => $this->statistiques->obtenirReclamationsEnRetard(),
            'sources' => $this->statistiques->obtenirParSource()
        );
    }
    
    // Obtenir le widget résumé pour le backoffice
    public function obtenirResume() {
        $stats = $this->statistiques->obtenirStatsGlobales();
        $temps_resolution = $this->statistiques->obtenirTempsResolutionMoyen();
        $satisfaction = $this->statistiques->obtenirSatisfactionMoyenne();
        
        $en_attente = $stats['par_statut']['En attente'] ?? 0;
        $en_cours = $stats['par_statut']['En cours'] ?? 0;
        $reglees = $stats['par_statut']['Résolu'] ?? 0;
        
        return array(
            'total_reclamations' => $stats['total_reclamations'],
            'en_attente' => $en_attente,
            'en_cours' => $en_cours,
            'reglees' => $reglees,
            'taux_resolution' => round(($reglees / max($stats['total_reclamations'], 1)) * 100, 2),
            'temps_resolution_moyen_jours' => $temps_resolution['jours'],
            'satisfaction_moyenne' => $satisfaction['moyenne'],
            'reclamations_en_retard' => count($this->statistiques->obtenirReclamationsEnRetard())
        );
    }
    
    // Obtenir le dashboard agent
    public function obtenirDashboardAgent($id_agent) {
        return array(
            'notifications' => $this->obtenirNotificationsAgent($id_agent),
            'performance' => $this->statistiques->obtenirClassementAgents(), // Tous les agents pour comparaison
            'reclamations_en_retard' => $this->statistiques->obtenirReclamationsEnRetard(),
            'satisfaction_distribution' => $this->statistiques->obtenirDistributionSatisfaction()
        );
    }
    
    // Obtenir les notifications d'un agent
    public function obtenirNotificationsAgent($id_agent) {
        return $this->notification->obtenirNonLuesAgent($id_agent);
    }
    
    // Marquer une notification comme lue
    public function marquerNotificationLue($id_notification) {
        return $this->notification->marquerCommeLue($id_notification);
    }
    
    // Obtenir les statistiques détaillées par période
    public function obtenirStatsPeriode($date_debut, $date_fin) {
        return array(
            'global' => $this->statistiques->obtenirStatsPeriode($date_debut, $date_fin),
            'temps_resolution_categorie' => $this->statistiques->obtenirTempsResolutionParCategorie(),
            'satisfaction_agent' => $this->statistiques->obtenirSatisfactionParAgent(),
            'volume_mensuel' => $this->statistiques->obtenirVolumeMensuel(),
            'reclamations_en_retard' => $this->statistiques->obtenirReclamationsEnRetard()
        );
    }
    
    // Générer un export JSON du dashboard
    public function exporterJSON() {
        $data = $this->obtenirTableauBord();
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
?>
