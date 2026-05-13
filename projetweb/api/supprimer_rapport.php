<?php
require_once '../config.php';
require_once '../controller/report_controller.php';

$reportController = new ReportController();
$id_rapport = $_POST['id_rapport'] ?? null;

if (!$id_rapport) {
    die('ID de rapport invalide');
}

$result = $reportController->supprimerRapport($id_rapport);

// Rediriger vers la page des rapports
header('Location: ../view/backoffice/rapports.php');
?>
