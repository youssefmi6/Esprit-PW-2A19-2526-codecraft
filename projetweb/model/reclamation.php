<?php
require_once __DIR__ . '/../connexion.php';

class Reclamation {
    private $conn;
    private $table = 'reclamation';
    
    // Propriétés
    public $id;
    public $titre;
    public $description;
    public $date;
    public $status;
    public $categorie;
    public $priorite;
    public $email_client;
    public $id_agent;
    public $date_limite_resolution;
    public $date_resolution;
    public $satisfaction;
    public $source;
    public $estimation_cout;
    public $fichier_joint;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    // Ajouter une reclamation
    public function ajouter() {
        $sql = "INSERT INTO " . $this->table . " (titre, description, date, status, categorie, priorite, email_client, source) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt) {
            $categorie = $this->categorie ?? 'Général';
            $priorite = $this->priorite ?? 'Moyenne';
            $email_client = $this->email_client ?? null;
            $source = $this->source ?? 'Email';
            $stmt->bind_param("ssssssss", $this->titre, $this->description, $this->date, $this->status, $categorie, $priorite, $email_client, $source);
            
            if ($stmt->execute()) {
                return $this->conn->insert_id;
            } else {
                return false;
            }
            $stmt->close();
        }
        return false;
    }
    
    // Récupérer toutes les reclamations
    public function obtenirTous() {
        $sql = "SELECT * FROM " . $this->table . " ORDER BY date DESC";
        $result = $this->conn->query($sql);
        return $result;
    }
    
    // Récupérer une reclamation par ID
    public function obtenirParId($id) {
        $sql = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    // Modifier une reclamation
    public function modifier() {
        $sql = "UPDATE " . $this->table . " SET titre = ?, description = ?, status = ? WHERE id = ?";
        
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("sssi", $this->titre, $this->description, $this->status, $this->id);
            
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
            $stmt->close();
        }
        return false;
    }

    // Mettre à jour la note de satisfaction
    public function mettreAJourSatisfaction() {
        $sql = "UPDATE " . $this->table . " SET satisfaction = ? WHERE id = ?";
        
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ii", $this->satisfaction, $this->id);
            
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
            $stmt->close();
        }
        return false;
    }
    
    // Supprimer une reclamation
    public function supprimer($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE id = ?";
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
        
        if (empty($this->titre) || strlen($this->titre) < 3) {
            $erreurs[] = "Le titre doit contenir au moins 3 caractères";
        }
        
        if (empty($this->description) || strlen($this->description) < 10) {
            $erreurs[] = "La description doit contenir au moins 10 caractères";
        }
        
        if (empty($this->status)) {
            $erreurs[] = "Le statut est requis";
        }
        
        if (empty($this->email_client) || !filter_var($this->email_client, FILTER_VALIDATE_EMAIL)) {
            $erreurs[] = "L'email du client est invalide";
        }
        
        return $erreurs;
    }
}
?>
