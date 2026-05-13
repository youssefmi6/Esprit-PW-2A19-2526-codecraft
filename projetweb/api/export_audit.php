<?php
require_once '../config.php';
require_once '../controller/notification_controller.php';

$auditController = new AuditController();
$date_debut = $_POST['date_debut'] ?? date('Y-01-01');
$date_fin = $_POST['date_fin'] ?? date('Y-m-d');

$result = $auditController->exporterAuditCSV($date_debut, $date_fin);

if ($result['success']) {
    $filepath = '../exports/' . $result['filename'];
    
    if (file_exists($filepath)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $result['filename'] . '"');
        readfile($filepath);
    } else {
        die('Fichier non trouvé');
    }
} else {
    die('Erreur lors de la génération');
}
?>
