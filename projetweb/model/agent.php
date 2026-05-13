<?php
require_once __DIR__ . '/../connexion.php';

class Agent {
    private $conn;
    private $table = 'agent';
    
    // Propriétés
    public $id_agent;
    public $nom;
    public $email;
    public $departement;
    public $statut;
    public $date_creation;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    // Ajouter un agent
    public function ajouter() {
        $sql = "INSERT INTO " . $this->table . " (nom, email, departement, statut, date_creation) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("sssss", 
                $this->nom,
                $this->email,
                $this->departement,
                $this->statut,
                $this->date_creation
            );
            
            if ($stmt->execute()) {
                return $this->conn->insert_id;
            }
            $stmt->close();
        }
        return false;
    }
    
    // Obtenir tous les agents
    public function obtenirTous() {
        $sql = "SELECT * FROM " . $this->table . " ORDER BY nom ASC";
        $result = $this->conn->query($sql);
        return $result;
    }
    
    // Obtenir les agents actifs
    public function obtenirActifs() {
        $sql = "SELECT * FROM " . $this->table . " WHERE statut = 'Actif' ORDER BY nom ASC";
        $result = $this->conn->query($sql);
        return $result;
    }
    
    // Obtenir un agent par ID
    public function obtenirParId($id_agent) {
        $sql = "SELECT * FROM " . $this->table . " WHERE id_agent = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_agent);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    // Obtenir un agent par email
    public function obtenirParEmail($email) {
        $sql = "SELECT * FROM " . $this->table . " WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    // Modifier un agent
    public function modifier() {
        $sql = "UPDATE " . $this->table . " 
                SET nom = ?, email = ?, departement = ?, statut = ? 
                WHERE id_agent = ?";
        
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ssssi", 
                $this->nom,
                $this->email,
                $this->departement,
                $this->statut,
                $this->id_agent
            );
            
            return $stmt->execute();
        }
        return false;
    }
    
    // Obtenir les statistiques d'un agent
    public function obtenirStatistiques($id_agent) {
        $stats = array();
        
        // Réclamations assignées
        $sql = "SELECT COUNT(*) as total FROM reclamation WHERE id_agent = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_agent);
        $stmt->execute();
        $stats['total_assignees'] = $stmt->get_result()->fetch_assoc()['total'];
        
        // Réclamations résolues
        $sql = "SELECT COUNT(*) as total FROM reclamation WHERE id_agent = ? AND status = 'Résolu'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_agent);
        $stmt->execute();
        $stats['reglees'] = $stmt->get_result()->fetch_assoc()['total'];
        
        // Réclamations en cours
        $sql = "SELECT COUNT(*) as total FROM reclamation WHERE id_agent = ? AND status = 'En cours'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_agent);
        $stmt->execute();
        $stats['en_cours'] = $stmt->get_result()->fetch_assoc()['total'];
        
        // Satisfaction moyenne
        $sql = "SELECT AVG(satisfaction) as moyenne FROM reclamation WHERE id_agent = ? AND satisfaction IS NOT NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_agent);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stats['satisfaction_moyenne'] = round($result['moyenne'] ?? 0, 2);
        
        return $stats;
    }
    
    // Obtenir les agents par département
    public function obtenirParDepartement($departement) {
        $sql = "SELECT * FROM " . $this->table . " WHERE departement = ? AND statut = 'Actif' ORDER BY nom ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $departement);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>
