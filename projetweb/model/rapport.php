<?php
require_once __DIR__ . '/../connexion.php';

class Rapport {
    private $conn;
    private $table = 'rapport';
    
    // Propriétés
    public $id_rapport;
    public $titre;
    public $type;
    public $date_debut;
    public $date_fin;
    public $statistiques;
    public $fichier_export;
    public $date_generation;
    public $cree_par;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    // Créer un rapport
    public function creer() {
        $sql = "INSERT INTO " . $this->table . " (titre, type, date_debut, date_fin, statistiques, fichier_export, date_generation, cree_par) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt) {
            $stats_json = json_encode($this->statistiques);
            $stmt->bind_param("sssssssi", 
                $this->titre,
                $this->type,
                $this->date_debut,
                $this->date_fin,
                $stats_json,
                $this->fichier_export,
                $this->date_generation,
                $this->cree_par
            );
            
            if ($stmt->execute()) {
                return $this->conn->insert_id;
            }
            $stmt->close();
        }
        return false;
    }
    
    // Obtenir tous les rapports
    public function obtenirTous() {
        $sql = "SELECT * FROM " . $this->table . " ORDER BY date_generation DESC";
        $result = $this->conn->query($sql);
        return $result;
    }
    
    // Obtenir un rapport par ID
    public function obtenirParId($id_rapport) {
        $sql = "SELECT * FROM " . $this->table . " WHERE id_rapport = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_rapport);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        
        // Décoder les statistiques JSON
        if ($row && $row['statistiques']) {
            $row['statistiques'] = json_decode($row['statistiques'], true);
        }
        
        return $row;
    }
    
    // Obtenir les rapports d'une période
    public function obtenirParPeriode($date_debut, $date_fin) {
        $sql = "SELECT * FROM " . $this->table . " 
                WHERE date_generation >= ? AND date_generation <= ? 
                ORDER BY date_generation DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $date_debut, $date_fin);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Obtenir les rapports par type
    public function obtenirParType($type) {
        $sql = "SELECT * FROM " . $this->table . " WHERE type = ? ORDER BY date_generation DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $type);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Générer un rapport CSV
    public function genererCSV($id_rapport) {
        $rapport = $this->obtenirParId($id_rapport);
        
        if (!$rapport) return false;
        
        $filename = 'rapport_' . $rapport['type'] . '_' . date('Ymd_His') . '.csv';
        $filepath = '../exports/' . $filename;
        
        // Créer le dossier s'il n'existe pas
        if (!is_dir('../exports/')) {
            mkdir('../exports/', 0755, true);
        }
        
        $file = fopen($filepath, 'w');
        
        // En-tête
        fputcsv($file, array('Rapport: ' . $rapport['titre']), ';');
        fputcsv($file, array('Période: ' . $rapport['date_debut'] . ' à ' . $rapport['date_fin']), ';');
        fputcsv($file, array('Généré le: ' . $rapport['date_generation']), ';');
        fputcsv($file, array(''), ';');
        
        // Statistiques
        $stats = $rapport['statistiques'];
        fputcsv($file, array('STATISTIQUES GÉNÉRALES'), ';');
        fputcsv($file, array('Total réclamations', $stats['total'] ?? 0), ';');
        fputcsv($file, array('Résolues', $stats['reglees'] ?? 0), ';');
        fputcsv($file, array('En cours', $stats['en_cours'] ?? 0), ';');
        fputcsv($file, array('En attente', $stats['en_attente'] ?? 0), ';');
        fputcsv($file, array('Rejetées', $stats['rejetees'] ?? 0), ';');
        fputcsv($file, array(''), ';');
        
        fputcsv($file, array('SATISFACTION'), ';');
        fputcsv($file, array('Satisfaction moyenne', $stats['satisfaction_moyenne'] ?? 0), ';');
        fputcsv($file, array(''), ';');
        
        fputcsv($file, array('PERFORMANCE'), ';');
        fputcsv($file, array('Temps moyen résolution (heures)', $stats['temps_moyen'] ?? 0), ';');
        fputcsv($file, array('Coût total estimé', $stats['cout_total'] ?? 0), ';');
        
        fclose($file);
        
        return $filename;
    }
    
    // Générer un rapport PDF (utilise mPDF ou TCPDF)
    public function genererPDF($id_rapport) {
        $rapport = $this->obtenirParId($id_rapport);
        
        if (!$rapport) return false;
        
        require_once __DIR__ . '/../libs/mpdf/vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf();
        
        $html = $this->genererHTMLRapport($rapport);
        $mpdf->WriteHTML($html);
        
        $filename = 'rapport_' . $rapport['type'] . '_' . date('Ymd_His') . '.pdf';
        
        if (!is_dir('../exports/')) {
            mkdir('../exports/', 0755, true);
        }
        
        $filepath = '../exports/' . $filename;
        $mpdf->Output($filepath, 'F');
        
        return $filename;
    }
    
    // Générer le HTML du rapport
    private function genererHTMLRapport($rapport) {
        $stats = $rapport['statistiques'];
        
        $html = '<html><body style="font-family: Arial; margin: 20px;">';
        $html .= '<h1>' . $rapport['titre'] . '</h1>';
        $html .= '<p><strong>Période:</strong> ' . $rapport['date_debut'] . ' à ' . $rapport['date_fin'] . '</p>';
        $html .= '<p><strong>Généré le:</strong> ' . $rapport['date_generation'] . '</p>';
        
        $html .= '<h2>Statistiques Générales</h2>';
        $html .= '<table border="1" cellpadding="10" width="100%">';
        $html .= '<tr><td><strong>Total réclamations</strong></td><td>' . ($stats['total'] ?? 0) . '</td></tr>';
        $html .= '<tr><td><strong>Résolues</strong></td><td>' . ($stats['reglees'] ?? 0) . '</td></tr>';
        $html .= '<tr><td><strong>En cours</strong></td><td>' . ($stats['en_cours'] ?? 0) . '</td></tr>';
        $html .= '<tr><td><strong>En attente</strong></td><td>' . ($stats['en_attente'] ?? 0) . '</td></tr>';
        $html .= '<tr><td><strong>Rejetées</strong></td><td>' . ($stats['rejetees'] ?? 0) . '</td></tr>';
        $html .= '</table>';
        
        $html .= '<h2>Satisfaction Moyenne</h2>';
        $html .= '<p>' . ($stats['satisfaction_moyenne'] ?? 0) . '/5</p>';
        
        $html .= '<h2>Performance</h2>';
        $html .= '<table border="1" cellpadding="10" width="100%">';
        $html .= '<tr><td><strong>Temps moyen résolution</strong></td><td>' . ($stats['temps_moyen'] ?? 0) . ' heures</td></tr>';
        $html .= '<tr><td><strong>Coût total estimé</strong></td><td>€' . ($stats['cout_total'] ?? 0) . '</td></tr>';
        $html .= '</table>';
        
        $html .= '</body></html>';
        
        return $html;
    }
    
    // Télécharger le fichier du rapport
    public function telechargerFichier($id_rapport) {
        $rapport = $this->obtenirParId($id_rapport);
        
        if (!$rapport || !$rapport['fichier_export']) return false;
        
        $filepath = '../exports/' . $rapport['fichier_export'];
        
        if (!file_exists($filepath)) return false;
        
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $rapport['fichier_export'] . '"');
        readfile($filepath);
        
        return true;
    }
    
    // Supprimer un rapport
    public function supprimer($id_rapport) {
        $rapport = $this->obtenirParId($id_rapport);
        
        if ($rapport && $rapport['fichier_export']) {
            $filepath = '../exports/' . $rapport['fichier_export'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }
        
        $sql = "DELETE FROM " . $this->table . " WHERE id_rapport = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_rapport);
        return $stmt->execute();
    }
}
?>
