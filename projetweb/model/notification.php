<?php
require_once __DIR__ . '/../connexion.php';

class Notification {
    private $conn;
    private $table = 'notification';
    
    // Propriétés
    public $id_notification;
    public $id_reclamation;
    public $id_agent;
    public $type_notification;
    public $message;
    public $lu;
    public $date_creation;
    public $date_lecture;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    // Créer une notification pour nouvelle réclamation
    public function notifierNouvelleReclamation($id_reclamation, $titre) {
        $this->id_reclamation = $id_reclamation;
        $this->id_agent = null; // À tous les agents
        $this->type_notification = 'Nouvelle_reclamation';
        $this->message = "Nouvelle réclamation: " . substr($titre, 0, 50) . "...";
        $this->lu = 0;
        $this->date_creation = date('Y-m-d H:i:s');
        
        return $this->ajouter();
    }
    
    // Créer une notification pour nouvelle réponse
    public function notifierNouvelleReponse($id_reclamation, $id_agent) {
        $this->id_reclamation = $id_reclamation;
        $this->id_agent = $id_agent;
        $this->type_notification = 'Reponse_client';
        $this->message = "Le client a répondu à la réclamation #" . $id_reclamation;
        $this->lu = 0;
        $this->date_creation = date('Y-m-d H:i:s');
        
        return $this->ajouter();
    }
    
    // Créer une notification d'escalade
    public function notifierEscalade($id_reclamation, $id_agent) {
        $this->id_reclamation = $id_reclamation;
        $this->id_agent = $id_agent;
        $this->type_notification = 'Escalade';
        $this->message = "La réclamation #" . $id_reclamation . " a été escaladée";
        $this->lu = 0;
        $this->date_creation = date('Y-m-d H:i:s');
        
        return $this->ajouter();
    }
    
    // Créer une notification de dépassement de délai
    public function notifierDepassementDelai($id_reclamation, $id_agent) {
        $this->id_reclamation = $id_reclamation;
        $this->id_agent = $id_agent;
        $this->type_notification = 'Depassement_delai';
        $this->message = "⚠️ La réclamation #" . $id_reclamation . " dépasse le délai de résolution";
        $this->lu = 0;
        $this->date_creation = date('Y-m-d H:i:s');
        
        return $this->ajouter();
    }
    
    // Ajouter une notification
    private function ajouter() {
        $sql = "INSERT INTO " . $this->table . " (id_reclamation, id_agent, type_notification, message, lu, date_creation) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("iissis", 
                $this->id_reclamation,
                $this->id_agent,
                $this->type_notification,
                $this->message,
                $this->lu,
                $this->date_creation
            );
            
            if ($stmt->execute()) {
                return $this->conn->insert_id;
            }
            $stmt->close();
        }
        return false;
    }
    
    // Marquer une notification comme lue
    public function marquerCommeLue($id_notification) {
        $sql = "UPDATE " . $this->table . " SET lu = 1, date_lecture = ? WHERE id_notification = ?";
        $stmt = $this->conn->prepare($sql);
        $date_lecture = date('Y-m-d H:i:s');
        $stmt->bind_param("si", $date_lecture, $id_notification);
        return $stmt->execute();
    }
    
    // Obtenir toutes les notifications non lues d'un agent
    public function obtenirNonLuesAgent($id_agent) {
        $sql = "SELECT n.*, r.titre FROM " . $this->table . " n
                LEFT JOIN reclamation r ON n.id_reclamation = r.id
                WHERE (n.id_agent = ? OR n.id_agent IS NULL) AND n.lu = 0
                ORDER BY n.date_creation DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_agent);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Compter les notifications non lues
    public function compterNonLuesAgent($id_agent) {
        $sql = "SELECT COUNT(*) as total FROM " . $this->table . " 
                WHERE (id_agent = ? OR id_agent IS NULL) AND lu = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_agent);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'];
    }
    
    // Obtenir l'historique des notifications
    public function obtenirHistoriqueAgent($id_agent, $limite = 50) {
        $sql = "SELECT n.*, r.titre FROM " . $this->table . " n
                LEFT JOIN reclamation r ON n.id_reclamation = r.id
                WHERE (n.id_agent = ? OR n.id_agent IS NULL)
                ORDER BY n.date_creation DESC LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $id_agent, $limite);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Supprimer une notification
    public function supprimer($id_notification) {
        $sql = "DELETE FROM " . $this->table . " WHERE id_notification = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_notification);
        return $stmt->execute();
    }
    
    // Marquer toutes les notifications comme lues
    public function marquerToutCommeLuAgent($id_agent) {
        $sql = "UPDATE " . $this->table . " SET lu = 1, date_lecture = ? 
                WHERE (id_agent = ? OR id_agent IS NULL) AND lu = 0";
        $stmt = $this->conn->prepare($sql);
        $date_lecture = date('Y-m-d H:i:s');
        $stmt->bind_param("si", $date_lecture, $id_agent);
        return $stmt->execute();
    }
}
?>
