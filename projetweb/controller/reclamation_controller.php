<?php
require_once __DIR__ . '/../model/reclamation.php';
require_once __DIR__ . '/../model/reponse.php';

class ReclamationController {
    private $reclamation;
    private $reponse;
    
    public function __construct() {
        $this->reclamation = new Reclamation();
        $this->reponse = new Reponse();
    }
    
    // Créer une nouvelle réclamation
    public function creer($titre, $description, $categorie = 'Général', $priorite = 'Moyenne', $email_client = '', $source = 'Email') {
        $this->reclamation->titre = htmlspecialchars($titre);
        $this->reclamation->description = htmlspecialchars($description);
        $this->reclamation->date = date('Y-m-d H:i:s');
        $this->reclamation->status = 'En attente';
        $this->reclamation->categorie = $categorie ?: 'Général';
        $this->reclamation->priorite = $priorite ?: 'Moyenne';
        $this->reclamation->email_client = filter_var($email_client, FILTER_VALIDATE_EMAIL) ? $email_client : null;
        $this->reclamation->source = $source ?: 'Email';
        
        $erreurs = $this->reclamation->valider();
        
        if (empty($erreurs)) {
            $id = $this->reclamation->ajouter();
            if ($id) {
                $this->reclamation->id = $id;
                return array(
                    'success' => true,
                    'message' => 'Réclamation ajoutée avec succès',
                    'id' => $id
                );
            } else {
                return array('success' => false, 'message' => 'Erreur lors de l\'ajout de la réclamation');
            }
        } else {
            return array('success' => false, 'erreurs' => $erreurs);
        }
    }
    
    // Obtenir toutes les réclamations
    public function obtenirTous() {
        return $this->reclamation->obtenirTous();
    }

    // Retourne l'ID de la dernière réclamation créée
    public function getDerniereReclamationId() {
        return $this->reclamation->id;
    }
    
    // Obtenir une réclamation par ID avec ses réponses
    public function obtenirDetail($id) {
        $reclamation = $this->reclamation->obtenirParId($id);
        $reponses = $this->reponse->obtenirParReclamation($id);
        
        return array(
            'reclamation' => $reclamation,
            'reponses' => $reponses
        );
    }
    
    // Mettre à jour le statut d'une réclamation
    public function mettreAJourStatut($id, $nouveau_statut) {
        $statuts_valides = array('En attente', 'En cours', 'Résolu', 'Rejeté');
        
        if (!in_array($nouveau_statut, $statuts_valides)) {
            return array('success' => false, 'message' => 'Statut invalide');
        }
        
        $this->reclamation->id = $id;
        $this->reclamation->status = $nouveau_statut;
        
        $reclamation = $this->reclamation->obtenirParId($id);
        $this->reclamation->titre = $reclamation['titre'];
        $this->reclamation->description = $reclamation['description'];
        
        if ($this->reclamation->modifier()) {
            return array('success' => true, 'message' => 'Statut mis à jour');
        } else {
            return array('success' => false, 'message' => 'Erreur lors de la mise à jour');
        }
    }

    // Mettre à jour la note de satisfaction d'une réclamation
    public function mettreAJourSatisfaction($id, $satisfaction) {
        if (!in_array(intval($satisfaction), range(1, 5))) {
            return array('success' => false, 'message' => 'Note invalide (1 à 5)');
        }
        
        $this->reclamation->id = intval($id);
        $this->reclamation->satisfaction = intval($satisfaction);
        
        if ($this->reclamation->mettreAJourSatisfaction()) {
            return array('success' => true, 'message' => 'Évaluation enregistrée avec succès');
        }
        return array('success' => false, 'message' => 'Erreur lors de l\'enregistrement de l\'évaluation');
    }
    
    // Ajouter une réponse
    public function ajouterReponse($id_reclamation, $texte_reponse) {
        $this->reponse->reponse = htmlspecialchars($texte_reponse);
        $this->reponse->date = date('Y-m-d H:i:s');
        $this->reponse->id_reclamation = intval($id_reclamation);
        
        $erreurs = $this->reponse->valider();
        
        if (empty($erreurs)) {
            if ($this->reponse->ajouter()) {
                return array('success' => true, 'message' => 'Réponse ajoutée avec succès');
            } else {
                return array('success' => false, 'message' => 'Erreur lors de l\'ajout de la réponse');
            }
        } else {
            return array('success' => false, 'erreurs' => $erreurs);
        }
    }
    
    // Supprimer une réclamation
    public function supprimerReclamation($id) {
        if ($this->reclamation->supprimer($id)) {
            return array('success' => true, 'message' => 'Réclamation supprimée');
        } else {
            return array('success' => false, 'message' => 'Erreur lors de la suppression');
        }
    }
}

// Traiter les requêtes AJAX — uniquement si ce fichier est appelé directement
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
header('Content-Type: application/json');

$controller = new ReclamationController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    switch ($action) {
        case 'creer':
            $result = $controller->creer($_POST['titre'], $_POST['description']);
            echo json_encode($result);
            break;
            
        case 'repondre':
            $result = $controller->ajouterReponse($_POST['id_reclamation'], $_POST['reponse']);
            echo json_encode($result);
            break;
            
        case 'update_statut':
            $result = $controller->mettreAJourStatut($_POST['id_reclamation'], $_POST['statut']);
            echo json_encode($result);
            break;

        case 'evaluer':
            $result = $controller->mettreAJourSatisfaction($_POST['id_reclamation'], $_POST['satisfaction']);
            echo json_encode($result);
            break;
            
        case 'supprimer':
            $result = $controller->supprimerReclamation($_POST['id']);
            echo json_encode($result);
            break;
            
        default:
            echo json_encode(array('success' => false, 'message' => 'Action non reconnue'));
            break;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    
    switch ($action) {
        case 'lister':
            $result = $controller->obtenirTous();
            $reclamations = array();
            while ($row = $result->fetch_assoc()) {
                $reclamations[] = $row;
            }
            echo json_encode($reclamations);
            break;
            
        case 'detail':
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $result = $controller->obtenirDetail($id);
            echo json_encode($result);
            break;
            
        case 'stats':
            $all = $controller->obtenirTous();
            $stats = array(
                'total' => $all->num_rows,
                'pending' => 0,
                'inprogress' => 0,
                'resolved' => 0
            );
            
            while ($row = $all->fetch_assoc()) {
                if ($row['status'] === 'En attente') $stats['pending']++;
                elseif ($row['status'] === 'En cours') $stats['inprogress']++;
                elseif ($row['status'] === 'Résolu') $stats['resolved']++;
            }
            
            echo json_encode($stats);
            break;
            
        default:
            echo json_encode(array('success' => false, 'message' => 'Action non reconnue'));
            break;
    }
} // closes if (GET)
} // fin garde accès direct
?>
