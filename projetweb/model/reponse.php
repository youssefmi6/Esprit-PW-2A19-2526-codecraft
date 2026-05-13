<?php
require_once __DIR__ . '/../connexion.php';

class Reponse {
    private $conn;
    private $table = 'reponse';
    
    // Propriétés
    public $id_reponse;
    public $reponse;
    public $date;
    public $id_reclamation;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    // Ajouter une reponse
    public function ajouter() {
        $sql = "INSERT INTO " . $this->table . " (reponse, date, id_reclamation) VALUES (?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ssi", $this->reponse, $this->date, $this->id_reclamation);
            
            if ($stmt->execute()) {
                return $this->conn->insert_id;
            } else {
                return false;
            }
            $stmt->close();
        }
        return false;
    }
    
    // Récupérer les reponses d'une reclamation
    public function obtenirParReclamation($id_reclamation) {
        $sql = "SELECT * FROM " . $this->table . " WHERE id_reclamation = ? ORDER BY date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_reclamation);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Récupérer une reponse par ID
    public function obtenirParId($id) {
        $sql = "SELECT * FROM " . $this->table . " WHERE id_reponse = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    // Modifier une reponse
    public function modifier() {
        $sql = "UPDATE " . $this->table . " SET reponse = ? WHERE id_reponse = ?";
        
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("si", $this->reponse, $this->id_reponse);
            
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
            $stmt->close();
        }
        return false;
    }
    
    // Supprimer une reponse
    public function supprimer($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE id_reponse = ?";
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
            $stmt->close();
        }
        return false;
    }
    
    // Valider les données
    public function valider() {
        $erreurs = array();
        
        if (empty($this->reponse) || strlen($this->reponse) < 5) {
            $erreurs[] = "La réponse doit contenir au moins 5 caractères";
        }
        
        if (empty($this->id_reclamation)) {
            $erreurs[] = "L'ID de la réclamation est requis";
        }
        
        return $erreurs;
    }
}
?>
