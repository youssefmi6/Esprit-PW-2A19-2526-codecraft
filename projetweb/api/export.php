<?php
require_once '../config.php';
require_once '../controller/dashboard_controller.php';

$dashboardController = new DashboardController();
$action = $_POST['action'] ?? 'export_json';

if ($action === 'export_json') {
    $data = $dashboardController->exporterJSON();
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="dashboard_' . date('Ymd_His') . '.json"');
    echo $data;
} elseif ($action === 'export_csv') {
    $tableau_bord = $dashboardController->obtenirTableauBord();
    
    $filename = 'dashboard_' . date('Ymd_His') . '.csv';
    $filepath = '../exports/' . $filename;
    
    if (!is_dir('../exports/')) {
        mkdir('../exports/', 0755, true);
    }
    
    $file = fopen($filepath, 'w');
    
    // En-tête
    fputcsv($file, array('DASHBOARD ANALYTIQUE'), ';');
    fputcsv($file, array('Généré le: ' . date('d/m/Y H:i:s')), ';');
    fputcsv($file, array(''), ';');
    
    // Statistiques globales
    $stats = $tableau_bord['stats_globales'];
    fputcsv($file, array('STATISTIQUES GÉNÉRALES'), ';');
    fputcsv($file, array('Total Réclamations', $stats['total_reclamations'] ?? 0), ';');
    
    foreach ($stats['par_statut'] ?? array() as $statut => $nombre) {
        fputcsv($file, array($statut, $nombre), ';');
    }
    fputcsv($file, array(''), ';');
    
    // Temps de résolution
    fputcsv($file, array('TEMPS DE RÉSOLUTION'), ';');
    fputcsv($file, array('Heures moyennes', $tableau_bord['temps_resolution']['heures'] ?? 0), ';');
    fputcsv($file, array('Jours moyens', $tableau_bord['temps_resolution']['jours'] ?? 0), ';');
    fputcsv($file, array(''), ';');
    
    // Satisfaction
    fputcsv($file, array('SATISFACTION'), ';');
    fputcsv($file, array('Moyenne', $tableau_bord['satisfaction']['moyenne'] ?? 0), ';');
    fputcsv($file, array('Total Évaluations', $tableau_bord['satisfaction']['total_evaluations'] ?? 0), ';');
    
    fclose($file);
    
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    readfile($filepath);
} else {
    die('Action invalide');
}
?>
