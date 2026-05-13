<?php
// Contrôleur frontoffice pour les clients
// Gère la création, consultation et gestion des réclamations côté client

require_once __DIR__ . '/../model/reclamation.php';
require_once __DIR__ . '/../model/reponse.php';
require_once __DIR__ . '/../model/audit.php';
require_once __DIR__ . '/../model/notification.php';

class FrontofficeController {
    private $reclamation;
    private $reponse;
    private $audit;
    private $notification;
    
    public function __construct() {
        $this->reclamation = new Reclamation();
        $this->reponse = new Reponse();
        $this->audit = new Audit();
        $this->notification = new Notification();
    }
    
    // Créer une nouvelle réclamation
    public function creerReclamation($titre, $description, $email, $categorie = 'Général', $priorite = 'Moyenne', $source = 'Web') {
        $this->reclamation->titre = htmlspecialchars($titre);
        $this->reclamation->description = htmlspecialchars($description);
        $this->reclamation->date = date('Y-m-d H:i:s');
        $this->reclamation->status = 'En attente';
        $this->reclamation->categorie = $categorie;
        $this->reclamation->priorite = $priorite;
        $this->reclamation->email_client = $email;
        $this->reclamation->source = $source;
        
        $erreurs = $this->reclamation->valider();
        
        if (empty($erreurs)) {
            $id = $this->reclamation->ajouter();
            
            if ($id) {
                // Enregistrer dans l'audit
                $this->audit->enregistrerCreation($id, null, 'Client Web');
                
                // Notifier les agents
                $this->notification->notifierNouvelleReclamation($id, $titre);
                
                return array('success' => true, 'id' => $id, 'message' => 'Réclamation créée avec succès');
            } else {
                return array('success' => false, 'message' => 'Erreur lors de la création');
            }
        } else {
            return array('success' => false, 'erreurs' => $erreurs);
        }
    }
    
    // Obtenir une réclamation complète
    public function obtenirReclamationComplete($id) {
        $rec = $this->reclamation->obtenirParId($id);
        
        if (!$rec) {
            return null;
        }
        
        $reponses = $this->reponse->obtenirParReclamation($id);
        
        return array(
            'reclamation' => $rec,
            'reponses' => $reponses
        );
    }
    
    // Ajouter une réponse du client
    public function ajouterReponseClient($id_reclamation, $texte) {
        $this->reponse->reponse = htmlspecialchars($texte);
        $this->reponse->date = date('Y-m-d H:i:s');
        $this->reponse->id_reclamation = intval($id_reclamation);
        $this->reponse->type_reponse = 'Réponse Client';
        
        $erreurs = $this->reponse->valider();
        
        if (empty($erreurs)) {
            if ($this->reponse->ajouter()) {
                // Notifier les agents
                $this->notification->notifierNouvelleReponse($id_reclamation, null);
                
                return array('success' => true, 'message' => 'Réponse ajoutée');
            }
        }
        
        return array('success' => false, 'message' => 'Erreur lors de l\'ajout');
    }
    
    // Évaluer une réclamation
    public function evaluerReclamation($id_reclamation, $note, $feedback) {
        if ($note < 1 || $note > 5) {
            return array('success' => false, 'message' => 'Note invalide (1-5)');
        }
        
        // Mettre à jour la satisfaction
        $sql = "UPDATE reclamation SET satisfaction = ?, feedback = ? WHERE id = ?";
        // À implémenter dans le modèle Reclamation
        
        return array('success' => true, 'message' => 'Évaluation enregistrée');
    }
    
    // Obtenir les statistiques client
    public function obtenirStatistiquesClient($email_client = null) {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'En attente' THEN 1 ELSE 0 END) as en_attente,
                    SUM(CASE WHEN status = 'En cours' THEN 1 ELSE 0 END) as en_cours,
                    SUM(CASE WHEN status = 'Résolu' THEN 1 ELSE 0 END) as reglees,
                    AVG(satisfaction) as satisfaction_moyenne
                FROM reclamation";
        
        if ($email_client) {
            $sql .= " WHERE email_client = ?";
        }
        
        return $sql; // À exécuter avec la base de données
    }
    
    // Télécharger l'historique client
    public function telechargerHistoriqueClient($email_client) {
        $filename = 'historique_' . str_replace('@', '_', $email_client) . '_' . date('Ymd_His') . '.csv';
        $filepath = '../exports/' . $filename;
        
        if (!is_dir('../exports/')) {
            mkdir('../exports/', 0755, true);
        }
        
        // Générer le CSV
        $file = fopen($filepath, 'w');
        
        fputcsv($file, array('ID', 'Titre', 'Catégorie', 'Priorité', 'Statut', 'Date', 'Satisfaction'), ';');
        
        // Ajouter les données...
        
        fclose($file);
        
        return array('success' => true, 'filename' => $filename);
    }
}
?>
