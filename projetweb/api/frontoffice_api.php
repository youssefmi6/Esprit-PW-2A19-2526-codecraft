<?php
require_once '../config.php';
require_once '../controller/frontoffice_controller.php';

header('Content-Type: application/json');

$frontofficeController = new FrontofficeController();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        // Créer une réclamation
        case 'creer_reclamation':
            $titre = $_POST['titre'] ?? '';
            $description = $_POST['description'] ?? '';
            $email = $_POST['email'] ?? '';
            $categorie = $_POST['categorie'] ?? 'Général';
            $priorite = $_POST['priorite'] ?? 'Moyenne';
            $source = $_POST['source'] ?? 'Web';
            
            $response = $frontofficeController->creerReclamation(
                $titre, 
                $description, 
                $email, 
                $categorie, 
                $priorite, 
                $source
            );
            break;
        
        // Ajouter une réponse
        case 'ajouter_reponse':
            $id_reclamation = $_POST['id_reclamation'] ?? '';
            $texte = $_POST['reponse'] ?? '';
            
            $response = $frontofficeController->ajouterReponseClient($id_reclamation, $texte);
            break;
        
        // Évaluer une réclamation
        case 'evaluer':
            $id_reclamation = $_POST['id_reclamation'] ?? '';
            $note = $_POST['satisfaction'] ?? 0;
            
            $response = $frontofficeController->evaluerReclamation($id_reclamation, $note, '');
            break;
        
        default:
            $response = array('success' => false, 'message' => 'Action non reconnue');
    }
} catch (Exception $e) {
    $response = array('success' => false, 'message' => $e->getMessage());
}

echo json_encode($response);
?>
