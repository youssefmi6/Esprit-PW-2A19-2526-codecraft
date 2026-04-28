<?php
// controllers/homeController.php
function homeIndex() {
    global $pdo;
    
    require_once __DIR__ . '/../models/resourceModel.php';
    require_once __DIR__ . '/../models/userModel.php';
    require_once __DIR__ . '/../models/subscriptionModel.php';

    $homeCatalogPlans = getPublishedCatalogPlans($pdo);

    $resources = [];
    $matieres = [];
    $contributors = [];
    $homeDbWarning = null;

    try {
        $resources = getAllResources($pdo);
        $matieres = getResourcesByMatiere($pdo);

        $stmt = $pdo->query("SELECT u.*, COUNT(r.id_res) as resource_count, COALESCE(SUM(r.downloads), 0) as total_downloads 
                             FROM users u LEFT JOIN ressource r ON u.id = r.id 
                             GROUP BY u.id ORDER BY total_downloads DESC LIMIT 4");
        $contributors = $stmt->fetchAll();
    } catch (PDOException $e) {
        // Evite un fatal error si la table ressource est absente/corrompue.
        if ($e->getCode() === '42S02') {
            $homeDbWarning = "Table 'ressource' indisponible. Importez/reparez la base avec studehub.sql.";
        } else {
            throw $e;
        }
    }
    
    $currentUser = getCurrentUser($pdo);
    
    $matiere_icons = [
        'Mathématiques' => 'https://cdn-icons-png.flaticon.com/512/3665/3665924.png',
        'Physique' => 'https://cdn-icons-png.flaticon.com/512/190/190665.png',
        'Chimie' => 'https://cdn-icons-png.flaticon.com/512/2908/2908010.png',
        'Informatique' => 'https://cdn-icons-png.flaticon.com/512/1995/1995572.png',
        'Programmation' => 'https://cdn-icons-png.flaticon.com/512/5968/5968292.png',
        'HTML/CSS' => 'https://cdn-icons-png.flaticon.com/512/732/732212.png',
        'Autre' => 'https://cdn-icons-png.flaticon.com/512/3665/3665924.png'
    ];
    
    require_once __DIR__ . '/../views/home/index.php';
}
?>