<?php
// controllers/homeController.php
function homeIndex() {
    global $pdo;
    
    require_once __DIR__ . '/../models/resourceModel.php';
    require_once __DIR__ . '/../models/userModel.php';
    
    $search = trim($_GET['search'] ?? '');
    $matiere = trim($_GET['matiere'] ?? '');

    $resources = getAllResources($pdo, $search, '', $matiere);
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

    if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'html' => renderHomeResourceCards($resources, $matiere_icons),
            'count' => count($resources),
        ]);
        exit();
    }
    
    require_once __DIR__ . '/../views/home/index.php';
}

function renderHomeResourceCards($resources, $matiere_icons) {
    if (empty($resources)) {
        return '<div class="col-12"><div class="alert alert-info text-center"><i class="ti-info-alt"></i> Aucune ressource trouvée.</div></div>';
    }

    $html = '';
    foreach ($resources as $res) {
        $matiereImage = $matiere_icons[$res['matiere']] ?? $matiere_icons['Autre'];
        $matiereClean = htmlspecialchars($res['matiere'] ?? '', ENT_QUOTES, 'UTF-8');
        $titreClean = htmlspecialchars($res['titre'] ?? '', ENT_QUOTES, 'UTF-8');
        $niveauClean = htmlspecialchars($res['niveau'] ?? '', ENT_QUOTES, 'UTF-8');
        $auteurClean = htmlspecialchars($res['nom'] ?? '', ENT_QUOTES, 'UTF-8');
        $accesClean = htmlspecialchars($res['acces'] ?? '', ENT_QUOTES, 'UTF-8');
        $resourceId = (int)($res['id_res'] ?? 0);
        $userId = (int)($res['user_id'] ?? 0);
        $prix = isset($res['prix']) ? (float)$res['prix'] : 0;

        $priceLabel = $accesClean === 'Premium'
            ? '💰 ' . number_format($prix, 2) . ' DT'
            : '📥 Gratuit';

        $html .= '<div class="col-lg-4 col-md-6" data-matiere="' . $matiereClean . '">'
            . '<div class="resource-card">'
            . '<div class="resource-img">'
            . '<span class="resource-badge ' . ($accesClean === 'Premium' ? 'badge-premium' : 'badge-free') . '">' . $accesClean . '</span>'
            . '<img src="' . (!empty($res['photo']) ? htmlspecialchars($res['photo'], ENT_QUOTES, 'UTF-8') : $matiereImage) . '" alt="' . $titreClean . '">'
            . '</div>'
            . '<div class="resource-content">'
            . '<div class="stars">★★★★★</div>'
            . '<h4 class="resource-title"><a href="index.php?action=resource&subaction=detail&id=' . $resourceId . '">' . $titreClean . '</a></h4>'
            . '<div class="resource-stats">'
            . '<span><i class="ti-book"></i> ' . $niveauClean . '</span>'
            . '<span><i class="ti-folder"></i> ' . $matiereClean . '</span>'
            . '<span><i class="ti-user"></i> <a href="index.php?action=profile&subaction=view&id=' . $userId . '">' . $auteurClean . '</a></span>'
            . '</div>'
            . '<div class="resource-price">' . $priceLabel . '</div>'
            . '<div class="resource-actions">'
            . '<a href="index.php?action=resource&subaction=detail&id=' . $resourceId . '" class="btn-primary-custom" style="padding:8px 20px;">📖 Voir</a>';

        if ($accesClean === 'Premium') {
            $html .= '<a href="index.php?action=resource&subaction=buy&id=' . $resourceId . '" class="btn-outline-custom" style="padding:8px 20px;">🛒 Acheter</a>';
        } else {
            $html .= '<a href="index.php?action=resource&subaction=download&id=' . $resourceId . '" class="btn-outline-custom" style="padding:8px 20px;"><i class="ti-download"></i> Télécharger</a>';
        }

        $html .= '</div></div></div></div>';
    }

    return $html;
}
?>