<?php
require_once '../config.php';
require_once '../controller/report_controller.php';

header('Content-Type: application/json');

$reportController = new ReportController();

$type = $_POST['type'] ?? '';
$response = array();

switch ($type) {
    case 'mensuel':
        $mois = $_POST['mois'] ?? date('m');
        $annee = $_POST['annee'] ?? date('Y');
        $response = $reportController->genererRapportMensuel($mois, $annee);
        break;
        
    case 'trimestriel':
        $trimestre = $_POST['trimestre'] ?? ceil(date('m') / 3);
        $annee = $_POST['annee'] ?? date('Y');
        $response = $reportController->genererRapportTrimestriel($trimestre, $annee);
        break;
        
    case 'annuel':
        $annee = $_POST['annee'] ?? date('Y');
        $response = $reportController->genererRapportAnnuel($annee);
        break;
        
    case 'custom':
        $date_debut = $_POST['date_debut'] ?? date('Y-m-01');
        $date_fin = $_POST['date_fin'] ?? date('Y-m-d');
        $titre = $_POST['titre'] ?? 'Rapport personnalisé';
        $response = $reportController->genererRapportPersonnalise($date_debut, $date_fin, $titre);
        break;
        
    default:
        $response = array('success' => false, 'message' => 'Type de rapport invalide');
}

echo json_encode($response);
?>
