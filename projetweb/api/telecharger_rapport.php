<?php
require_once '../config.php';
require_once '../controller/report_controller.php';

$reportController = new ReportController();
$id_rapport = $_POST['id_rapport'] ?? null;
$format = $_POST['format'] ?? 'csv';

if (!$id_rapport) {
    die('ID de rapport invalide');
}

if ($format === 'csv') {
    $result = $reportController->exporterCSV($id_rapport);
} elseif ($format === 'pdf') {
    $result = $reportController->exporterPDF($id_rapport);
} else {
    die('Format invalide');
}

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
    die($result['message']);
}
?>
