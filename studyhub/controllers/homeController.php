<?php
// controllers/homeController.php
function homeIndex() {
    global $pdo;
    
    require_once __DIR__ . '/../models/resourceModel.php';
    require_once __DIR__ . '/../models/userModel.php';
    
    $resources = getAllResources($pdo);
    $matieres = getResourcesByMatiere($pdo);
    
    // MODIFICATION ICI : Exclure les administrateurs (role = 0)
    $stmt = $pdo->query("SELECT u.*, COUNT(r.id_res) as resource_count, COALESCE(SUM(r.downloads), 0) as total_downloads 
                         FROM users u 
                         LEFT JOIN ressource r ON u.id = r.id 
                         WHERE u.role = 1
                         GROUP BY u.id 
                         ORDER BY total_downloads DESC 
                         LIMIT 4");
    $contributors = $stmt->fetchAll();
    
    $currentUser = getCurrentUser($pdo);
    
    require_once __DIR__ . '/../includes/matiere_photos.php';
    $matiere_icons = get_matiere_default_photos_map();
    
    require_once __DIR__ . '/../views/home/index.php';
}
?>