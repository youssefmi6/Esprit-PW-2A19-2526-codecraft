<?php
require_once __DIR__ . '/../model/notification.php';
require_once __DIR__ . '/../model/audit.php';

class NotificationController {
    private $notification;
    private $audit;
    
    public function __construct() {
        $this->notification = new Notification();
        $this->audit = new Audit();
    }
    
    // Obtenir les notifications non lues d'un agent
    public function obtenirNonLuesAgent($id_agent) {
        return $this->notification->obtenirNonLuesAgent($id_agent);
    }
    
    // Compter les notifications non lues
    public function compterNonLues($id_agent) {
        return $this->notification->compterNonLuesAgent($id_agent);
    }
    
    // Marquer une notification comme lue
    public function marquerLue($id_notification) {
        if ($this->notification->marquerCommeLue($id_notification)) {
            return array('success' => true);
        }
        return array('success' => false);
    }
    
    // Marquer tout comme lu
    public function marquerToutLu($id_agent) {
        if ($this->notification->marquerToutCommeLuAgent($id_agent)) {
            return array('success' => true);
        }
        return array('success' => false);
    }
    
    // Obtenir l'historique des notifications
    public function obtenirHistorique($id_agent, $limite = 50) {
        return $this->notification->obtenirHistoriqueAgent($id_agent, $limite);
    }
    
    // Supprimer une notification
    public function supprimerNotification($id_notification) {
        if ($this->notification->supprimer($id_notification)) {
            return array('success' => true);
        }
        return array('success' => false);
    }
}

class AuditController {
    private $audit;
    
    public function __construct() {
        $this->audit = new Audit();
    }
    
    // Obtenir l'historique complet d'une réclamation
    public function obtenirHistoriqueReclamation($id_reclamation) {
        return $this->audit->obtenirHistorique($id_reclamation);
    }
    
    // Obtenir les modifications récentes
    public function obtenirModificationsRecentes($limite = 50) {
        return $this->audit->obtenirModificationsRecentes($limite);
    }
    
    // Obtenir l'audit par période
    public function obtenirAuditPeriode($date_debut, $date_fin) {
        return $this->audit->obtenirParPeriode($date_debut, $date_fin);
    }
    
    // Exporter l'audit en CSV
    public function exporterAuditCSV($date_debut, $date_fin) {
        $result = $this->audit->obtenirParPeriode($date_debut, $date_fin);
        
        $filename = 'audit_' . date('Ymd_His') . '.csv';
        $filepath = '../exports/' . $filename;
        
        if (!is_dir('../exports/')) {
            mkdir('../exports/', 0755, true);
        }
        
        $file = fopen($filepath, 'w');
        
        // En-tête CSV
        fputcsv($file, array(
            'ID Audit',
            'ID Réclamation',
            'Type Modification',
            'Ancien Statut',
            'Nouveau Statut',
            'Champ Modifié',
            'Ancienne Valeur',
            'Nouvelle Valeur',
            'Utilisateur',
            'Date Modification',
            'Adresse IP'
        ), ';');
        
        // Données
        while ($row = $result->fetch_assoc()) {
            fputcsv($file, array(
                $row['id_audit'],
                $row['id_reclamation'],
                $row['type_modification'],
                $row['ancien_statut'] ?? '',
                $row['nouveau_statut'] ?? '',
                $row['champ_modifie'] ?? '',
                substr($row['ancienne_valeur'] ?? '', 0, 50),
                substr($row['nouvelle_valeur'] ?? '', 0, 50),
                $row['nom_utilisateur'],
                $row['date_modification'],
                $row['adresse_ip']
            ), ';');
        }
        
        fclose($file);
        
        return array(
            'success' => true,
            'filename' => $filename,
            'url' => 'exports/' . $filename
        );
    }
}
?>
