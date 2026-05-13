<?php
require_once __DIR__ . '/../connexion.php';

class Statistiques {
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    // ========== STATISTIQUES GÉNÉRALES ==========
    
    // Obtenir les statistiques globales
    public function obtenirStatsGlobales() {
        $stats = array();
        
        // Total de réclamations
        $sql = "SELECT COUNT(*) as total FROM reclamation";
        $stats['total_reclamations'] = $this->conn->query($sql)->fetch_assoc()['total'];
        
        // Réclamations par statut
        $sql = "SELECT status, COUNT(*) as nombre FROM reclamation GROUP BY status";
        $result = $this->conn->query($sql);
        $stats['par_statut'] = array();
        while ($row = $result->fetch_assoc()) {
            $stats['par_statut'][$row['status']] = $row['nombre'];
        }
        
        // Réclamations par priorité
        $sql = "SELECT priorite, COUNT(*) as nombre FROM reclamation GROUP BY priorite";
        $result = $this->conn->query($sql);
        $stats['par_priorite'] = array();
        while ($row = $result->fetch_assoc()) {
            $stats['par_priorite'][$row['priorite']] = $row['nombre'];
        }
        
        // Réclamations par catégorie
        $sql = "SELECT categorie, COUNT(*) as nombre FROM reclamation GROUP BY categorie";
        $result = $this->conn->query($sql);
        $stats['par_categorie'] = array();
        while ($row = $result->fetch_assoc()) {
            $stats['par_categorie'][$row['categorie']] = $row['nombre'];
        }
        
        return $stats;
    }
    
    // ========== TEMPS DE RÉSOLUTION ==========
    
    // Obtenir le temps moyen de résolution
    public function obtenirTempsResolutionMoyen() {
        $sql = "SELECT 
                    AVG(TIMESTAMPDIFF(HOUR, date, date_resolution)) as heures,
                    AVG(TIMESTAMPDIFF(DAY, date, date_resolution)) as jours
                FROM reclamation 
                WHERE status = 'Résolu' AND date_resolution IS NOT NULL";
        
        $result = $this->conn->query($sql)->fetch_assoc();
        
        return array(
            'heures' => round($result['heures'] ?? 0, 2),
            'jours' => round($result['jours'] ?? 0, 2)
        );
    }
    
    // Obtenir le temps de résolution par catégorie
    public function obtenirTempsResolutionParCategorie() {
        $sql = "SELECT 
                    categorie,
                    COUNT(*) as total,
                    AVG(TIMESTAMPDIFF(HOUR, date, date_resolution)) as heures_moyen,
                    MIN(TIMESTAMPDIFF(HOUR, date, date_resolution)) as heures_min,
                    MAX(TIMESTAMPDIFF(HOUR, date, date_resolution)) as heures_max
                FROM reclamation 
                WHERE status = 'Résolu' AND date_resolution IS NOT NULL
                GROUP BY categorie";
        
        $result = $this->conn->query($sql);
        $data = array();
        
        while ($row = $result->fetch_assoc()) {
            $data[$row['categorie']] = array(
                'total' => $row['total'],
                'moyen' => round($row['heures_moyen'] ?? 0, 2),
                'min' => round($row['heures_min'] ?? 0, 2),
                'max' => round($row['heures_max'] ?? 0, 2)
            );
        }
        
        return $data;
    }
    
    // ========== SATISFACTION ==========
    
    // Obtenir le taux de satisfaction moyen
    public function obtenirSatisfactionMoyenne() {
        $sql = "SELECT 
                    AVG(satisfaction) as moyenne,
                    COUNT(*) as total_evaluations,
                    MIN(satisfaction) as min,
                    MAX(satisfaction) as max
                FROM reclamation 
                WHERE satisfaction IS NOT NULL";
        
        $result = $this->conn->query($sql)->fetch_assoc();
        
        return array(
            'moyenne' => round($result['moyenne'] ?? 0, 2),
            'total_evaluations' => $result['total_evaluations'],
            'min' => $result['min'],
            'max' => $result['max']
        );
    }
    
    // Obtenir la satisfaction par agent
    public function obtenirSatisfactionParAgent() {
        $sql = "SELECT 
                    r.id_agent,
                    a.nom,
                    a.email,
                    AVG(r.satisfaction) as moyenne,
                    COUNT(r.satisfaction) as total_evaluations
                FROM reclamation r
                LEFT JOIN agent a ON r.id_agent = a.id_agent
                WHERE r.satisfaction IS NOT NULL AND r.id_agent IS NOT NULL
                GROUP BY r.id_agent
                ORDER BY moyenne DESC";
        
        $result = $this->conn->query($sql);
        $data = array();
        
        while ($row = $result->fetch_assoc()) {
            $data[] = array(
                'id_agent' => $row['id_agent'],
                'nom' => $row['nom'] ?? 'Non assigné',
                'email' => $row['email'],
                'moyenne' => round($row['moyenne'] ?? 0, 2),
                'total_evaluations' => $row['total_evaluations']
            );
        }
        
        return $data;
    }
    
    // Obtenir la distribution des satisfactions
    public function obtenirDistributionSatisfaction() {
        $sql = "SELECT satisfaction, COUNT(*) as nombre FROM reclamation 
                WHERE satisfaction IS NOT NULL
                GROUP BY satisfaction
                ORDER BY satisfaction DESC";
        
        $result = $this->conn->query($sql);
        $distribution = array();
        
        while ($row = $result->fetch_assoc()) {
            $distribution[$row['satisfaction']] = $row['nombre'];
        }
        
        return $distribution;
    }
    
    // ========== VOLUME ET TENDANCES ==========
    
    // Obtenir le volume de réclamations par mois
    public function obtenirVolumeMensuel($mois_retroactifs = 12) {
        $sql = "SELECT 
                    DATE_FORMAT(date, '%Y-%m') as mois,
                    COUNT(*) as nombre,
                    SUM(CASE WHEN status = 'Résolu' THEN 1 ELSE 0 END) as reglees
                FROM reclamation
                WHERE date >= DATE_SUB(NOW(), INTERVAL ? MONTH)
                GROUP BY DATE_FORMAT(date, '%Y-%m')
                ORDER BY mois ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $mois_retroactifs);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[$row['mois']] = array(
                'total' => $row['nombre'],
                'reglees' => $row['reglees']
            );
        }
        
        return $data;
    }
    
    // Obtenir le volume de réclamations par jour (derniers 30 jours)
    public function obtenirVolumeQuotidien() {
        $sql = "SELECT 
                    DATE(date) as jour,
                    COUNT(*) as nombre,
                    SUM(CASE WHEN status = 'Résolu' THEN 1 ELSE 0 END) as reglees
                FROM reclamation
                WHERE date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY DATE(date)
                ORDER BY jour DESC";
        
        $result = $this->conn->query($sql);
        $data = array();
        
        while ($row = $result->fetch_assoc()) {
            $data[$row['jour']] = array(
                'total' => $row['nombre'],
                'reglees' => $row['reglees']
            );
        }
        
        return $data;
    }
    
    // ========== PERFORMANCE DES AGENTS ==========
    
    // Obtenir le classement des agents
    public function obtenirClassementAgents() {
        $sql = "SELECT 
                    a.id_agent,
                    a.nom,
                    a.email,
                    a.departement,
                    COUNT(r.id) as total_assignees,
                    SUM(CASE WHEN r.status = 'Résolu' THEN 1 ELSE 0 END) as reglees,
                    SUM(CASE WHEN r.status = 'En cours' THEN 1 ELSE 0 END) as en_cours,
                    AVG(r.satisfaction) as satisfaction_moyenne,
                    AVG(TIMESTAMPDIFF(HOUR, r.date, r.date_resolution)) as temps_moyen_heures
                FROM agent a
                LEFT JOIN reclamation r ON a.id_agent = r.id_agent
                WHERE a.statut = 'Actif'
                GROUP BY a.id_agent
                ORDER BY satisfaction_moyenne DESC, reglees DESC";
        
        $result = $this->conn->query($sql);
        $agents = array();
        
        while ($row = $result->fetch_assoc()) {
            $taux_resolution = 0;
            if ($row['total_assignees'] > 0) {
                $taux_resolution = round(($row['reglees'] / $row['total_assignees']) * 100, 2);
            }
            
            $agents[] = array(
                'id' => $row['id_agent'],
                'nom' => $row['nom'],
                'email' => $row['email'],
                'departement' => $row['departement'],
                'total_assignees' => $row['total_assignees'] ?? 0,
                'reglees' => $row['reglees'] ?? 0,
                'en_cours' => $row['en_cours'] ?? 0,
                'taux_resolution' => $taux_resolution . '%',
                'satisfaction_moyenne' => round($row['satisfaction_moyenne'] ?? 0, 2),
                'temps_moyen_heures' => round($row['temps_moyen_heures'] ?? 0, 2)
            );
        }
        
        return $agents;
    }
    
    // ========== RÉCLAMATIONS DÉPASSANT LES DÉLAIS ==========
    
    // Obtenir les réclamations en retard
    public function obtenirReclamationsEnRetard() {
        $sql = "SELECT 
                    r.*,
                    a.nom as nom_agent,
                    TIMESTAMPDIFF(HOUR, r.date_limite_resolution, NOW()) as heures_en_retard
                FROM reclamation r
                LEFT JOIN agent a ON r.id_agent = a.id_agent
                WHERE r.date_limite_resolution IS NOT NULL 
                AND r.date_limite_resolution < NOW()
                AND r.status != 'Résolu'
                ORDER BY heures_en_retard DESC";
        
        $result = $this->conn->query($sql);
        $retards = array();
        
        while ($row = $result->fetch_assoc()) {
            $retards[] = $row;
        }
        
        return $retards;
    }
    
    // ========== SOURCES DE RÉCLAMATIONS ==========
    
    // Obtenir les réclamations par source
    public function obtenirParSource() {
        $sql = "SELECT source, COUNT(*) as nombre FROM reclamation GROUP BY source ORDER BY nombre DESC";
        
        $result = $this->conn->query($sql);
        $sources = array();
        
        while ($row = $result->fetch_assoc()) {
            $sources[$row['source']] = $row['nombre'];
        }
        
        return $sources;
    }
    
    // ========== DONNÉES POUR PÉRIODE SPÉCIFIQUE ==========
    
    // Obtenir les statistiques pour une période
    public function obtenirStatsPeriode($date_debut, $date_fin) {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'Résolu' THEN 1 ELSE 0 END) as reglees,
                    SUM(CASE WHEN status = 'En cours' THEN 1 ELSE 0 END) as en_cours,
                    SUM(CASE WHEN status = 'En attente' THEN 1 ELSE 0 END) as en_attente,
                    SUM(CASE WHEN status = 'Rejeté' THEN 1 ELSE 0 END) as rejetees,
                    AVG(satisfaction) as satisfaction_moyenne,
                    SUM(estimation_cout) as cout_total
                FROM reclamation
                WHERE date >= ? AND date <= ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $date_debut, $date_fin);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
