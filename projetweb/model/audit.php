<?php
require_once __DIR__ . '/../connexion.php';

class Audit {
    private $conn;
    private $table = 'audit';
    
    // Propriétés
    public $id_audit;
    public $id_reclamation;
    public $type_modification;
    public $ancien_statut;
    public $nouveau_statut;
    public $champ_modifie;
    public $ancienne_valeur;
    public $nouvelle_valeur;
    public $id_utilisateur;
    public $nom_utilisateur;
    public $date_modification;
    public $adresse_ip;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    // Enregistrer une création de réclamation
    public function enregistrerCreation($id_reclamation, $id_utilisateur = null, $nom_utilisateur = 'Système') {
        $this->id_reclamation = $id_reclamation;
        $this->type_modification = 'Creation';
        $this->id_utilisateur = $id_utilisateur;
        $this->nom_utilisateur = $nom_utilisateur;
        $this->date_modification = date('Y-m-d H:i:s');
        $this->adresse_ip = $this->obtenirIp();
        
        return $this->ajouter();
    }
    
    // Enregistrer une modification de statut
    public function enregistrerChangementStatut($id_reclamation, $ancien_statut, $nouveau_statut, $id_utilisateur = null, $nom_utilisateur = 'Système') {
        $this->id_reclamation = $id_reclamation;
        $this->type_modification = 'Status_change';
        $this->ancien_statut = $ancien_statut;
        $this->nouveau_statut = $nouveau_statut;
        $this->id_utilisateur = $id_utilisateur;
        $this->nom_utilisateur = $nom_utilisateur;
        $this->date_modification = date('Y-m-d H:i:s');
        $this->adresse_ip = $this->obtenirIp();
        
        return $this->ajouter();
    }
    
    // Enregistrer une modification de champ
    public function enregistrerModification($id_reclamation, $champ_modifie, $ancienne_valeur, $nouvelle_valeur, $id_utilisateur = null, $nom_utilisateur = 'Système') {
        $this->id_reclamation = $id_reclamation;
        $this->type_modification = 'Modification';
        $this->champ_modifie = $champ_modifie;
        $this->ancienne_valeur = $ancienne_valeur;
        $this->nouvelle_valeur = $nouvelle_valeur;
        $this->id_utilisateur = $id_utilisateur;
        $this->nom_utilisateur = $nom_utilisateur;
        $this->date_modification = date('Y-m-d H:i:s');
        $this->adresse_ip = $this->obtenirIp();
        
        return $this->ajouter();
    }
    
    // Enregistrer une suppression
    public function enregistrerSuppression($id_reclamation, $id_utilisateur = null, $nom_utilisateur = 'Système') {
        $this->id_reclamation = $id_reclamation;
        $this->type_modification = 'Suppression';
        $this->id_utilisateur = $id_utilisateur;
        $this->nom_utilisateur = $nom_utilisateur;
        $this->date_modification = date('Y-m-d H:i:s');
        $this->adresse_ip = $this->obtenirIp();
        
        return $this->ajouter();
    }
    
    // Ajouter un enregistrement d'audit
    private function ajouter() {
        $sql = "INSERT INTO " . $this->table . " (id_reclamation, type_modification, ancien_statut, nouveau_statut, 
                champ_modifie, ancienne_valeur, nouvelle_valeur, id_utilisateur, nom_utilisateur, 
                date_modification, adresse_ip) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("issssssisss", 
                $this->id_reclamation,
                $this->type_modification,
                $this->ancien_statut,
                $this->nouveau_statut,
                $this->champ_modifie,
                $this->ancienne_valeur,
                $this->nouvelle_valeur,
                $this->id_utilisateur,
                $this->nom_utilisateur,
                $this->date_modification,
                $this->adresse_ip
            );
            
            if ($stmt->execute()) {
                return true;
            }
            $stmt->close();
        }
        return false;
    }
    
    // Obtenir l'historique complet d'une réclamation
    public function obtenirHistorique($id_reclamation) {
        $sql = "SELECT * FROM " . $this->table . " WHERE id_reclamation = ? ORDER BY date_modification DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_reclamation);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Obtenir les modifications récentes
    public function obtenirModificationsRecentes($limite = 50) {
        $sql = "SELECT * FROM " . $this->table . " ORDER BY date_modification DESC LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limite);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Obtenir l'audit filtré par date
    public function obtenirParPeriode($date_debut, $date_fin) {
        $sql = "SELECT * FROM " . $this->table . " 
                WHERE date_modification >= ? AND date_modification <= ? 
                ORDER BY date_modification DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $date_debut, $date_fin);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Compter les modifications pour une réclamation
    public function compterModifications($id_reclamation) {
        $sql = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE id_reclamation = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_reclamation);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'];
    }
    
    // Obtenir l'adresse IP du client
    private function obtenirIp() {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? 'Inconnu';
        }
    }
}
?>
