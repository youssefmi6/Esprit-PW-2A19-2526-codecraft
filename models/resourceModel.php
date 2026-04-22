<?php
// models/resourceModel.php - Modèle pour la gestion des ressources

class ResourceModel
{
    public static function getAllResources(PDO $pdo, string $search = '', string $type = '', string $matiere = ''): array
    {
        return getAllResources($pdo, $search, $type, $matiere);
    }

    public static function getResourceById(PDO $pdo, int $id)
    {
        return getResourceById($pdo, $id);
    }

    public static function getUserResources(PDO $pdo, int $userId): array
    {
        return getUserResources($pdo, $userId);
    }

    public static function createResource(PDO $pdo, array $data)
    {
        return createResource($pdo, $data);
    }

    public static function updateResource(PDO $pdo, int $id, array $data): bool
    {
        return updateResource($pdo, $id, $data);
    }

    public static function deleteResource(PDO $pdo, int $id, ?int $userId = null): bool
    {
        return deleteResource($pdo, $id, $userId);
    }

    public static function incrementDownloads(PDO $pdo, int $id): bool
    {
        return incrementDownloads($pdo, $id);
    }
}

// ========== FONCTIONS DE BASE ==========

/**
 * Récupère toutes les ressources avec filtres optionnels
 */
function getAllResources($pdo, $search = '', $type = '', $matiere = '') {
    $sql = "SELECT r.*, u.nom, u.prenom, u.id as user_id 
            FROM ressource r 
            JOIN users u ON r.id = u.id 
            WHERE 1=1";
    $params = [];
    
    if (!empty($search)) {
        $sql .= " AND (r.titre LIKE ? OR r.description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    if (!empty($type)) {
        $sql .= " AND r.type = ?";
        $params[] = $type;
    }
    if (!empty($matiere)) {
        $sql .= " AND r.matiere = ?";
        $params[] = $matiere;
    }
    $sql .= " ORDER BY r.id_res DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Récupère une ressource par son ID
 */
function getResourceById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT r.*, u.nom, u.prenom, u.email, u.id as user_id 
                           FROM ressource r 
                           JOIN users u ON r.id = u.id 
                           WHERE r.id_res = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Récupère toutes les ressources d'un utilisateur
 */
function getUserResources($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT * FROM ressource WHERE id = ? ORDER BY id_res DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

/**
 * Crée une nouvelle ressource
 */
function createResource($pdo, $data) {
    $stmt = $pdo->prepare("INSERT INTO ressource (titre, description, matiere, type, niveau, acces, prix, fichier, photo, id, pages, downloads, date_creation) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW())");
    $stmt->execute([
        $data['titre'], 
        $data['description'], 
        $data['matiere'], 
        $data['type'],
        $data['niveau'], 
        $data['acces'], 
        $data['prix'], 
        $data['fichier'],
        $data['photo'], 
        $data['user_id'], 
        $data['pages']
    ]);
    return $pdo->lastInsertId();
}

/**
 * Met à jour une ressource existante
 */
function updateResource($pdo, $id, $data) {
    $stmt = $pdo->prepare("UPDATE ressource 
                           SET titre=?, description=?, matiere=?, type=?, niveau=?, 
                               acces=?, prix=?, pages=?, photo=?, fichier=? 
                           WHERE id_res=?");
    return $stmt->execute([
        $data['titre'], 
        $data['description'], 
        $data['matiere'], 
        $data['type'],
        $data['niveau'], 
        $data['acces'], 
        $data['prix'], 
        $data['pages'],
        $data['photo'], 
        $data['fichier'], 
        $id
    ]);
}

/**
 * Supprime une ressource (commentaires et notes d'abord à cause des clés étrangères).
 */
function deleteResource($pdo, $id, $userId = null) {
    $pdo->beginTransaction();
    try {
        if ($userId) {
            $check = $pdo->prepare("SELECT 1 FROM ressource WHERE id_res = ? AND id = ? LIMIT 1");
            $check->execute([$id, $userId]);
            if (!$check->fetchColumn()) {
                $pdo->rollBack();
                return false;
            }
        } else {
            $check = $pdo->prepare("SELECT 1 FROM ressource WHERE id_res = ? LIMIT 1");
            $check->execute([$id]);
            if (!$check->fetchColumn()) {
                $pdo->rollBack();
                return false;
            }
        }

        $pdo->prepare("DELETE FROM comment WHERE id_res = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM ratings WHERE id_res = ?")->execute([$id]);

        if ($userId) {
            $stmt = $pdo->prepare("DELETE FROM ressource WHERE id_res = ? AND id = ?");
            $stmt->execute([$id, $userId]);
        } else {
            $stmt = $pdo->prepare("DELETE FROM ressource WHERE id_res = ?");
            $stmt->execute([$id]);
        }

        $pdo->commit();
        return $stmt->rowCount() > 0;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

/**
 * Incrémente le compteur de téléchargements
 */
function incrementDownloads($pdo, $id) {
    $stmt = $pdo->prepare("UPDATE ressource SET downloads = downloads + 1 WHERE id_res = ?");
    return $stmt->execute([$id]);
}

// ========== FONCTIONS DE RÉCUPÉRATION AVEC LIMITES ==========

/**
 * Récupère les dernières ressources
 */
function getRecentResources($pdo, $limit = 5) {
    $limit = intval($limit);
    $stmt = $pdo->query("SELECT r.*, u.nom, u.prenom 
                         FROM ressource r 
                         JOIN users u ON r.id = u.id 
                         ORDER BY r.id_res DESC 
                         LIMIT $limit");
    return $stmt->fetchAll();
}

/**
 * Récupère les ressources les plus téléchargées
 */
function getTopResources($pdo, $limit = 5) {
    $limit = intval($limit);
    $stmt = $pdo->query("SELECT r.*, u.nom, u.prenom 
                         FROM ressource r 
                         JOIN users u ON r.id = u.id 
                         ORDER BY r.downloads DESC 
                         LIMIT $limit");
    return $stmt->fetchAll();
}

/**
 * Récupère les statistiques des ressources par matière
 */
function getResourcesByMatiere($pdo, $limit = 4) {
    $limit = intval($limit);
    $stmt = $pdo->query("SELECT matiere, COUNT(*) as count 
                         FROM ressource 
                         GROUP BY matiere 
                         ORDER BY count DESC 
                         LIMIT $limit");
    return $stmt->fetchAll();
}

// ========== FONCTIONS POUR LES LISTES DÉROULANTES ==========

/**
 * Récupère tous les types de ressources distincts
 */
function getAllTypes($pdo) {
    $stmt = $pdo->query("SELECT DISTINCT type 
                         FROM ressource 
                         WHERE type IS NOT NULL AND type != '' 
                         ORDER BY type");
    return $stmt->fetchAll();
}

/**
 * Récupère toutes les matières distinctes
 */
function getAllMatieres($pdo) {
    $stmt = $pdo->query("SELECT DISTINCT matiere 
                         FROM ressource 
                         WHERE matiere IS NOT NULL AND matiere != '' 
                         ORDER BY matiere");
    return $stmt->fetchAll();
}

// ========== FONCTIONS POUR LES STATISTIQUES ==========

/**
 * Récupère les statistiques globales des ressources
 */
function getResourceStats($pdo) {
    $stmt = $pdo->query("SELECT 
                         COUNT(*) as total_resources,
                         SUM(downloads) as total_downloads,
                         SUM(pages) as total_pages,
                         COUNT(DISTINCT matiere) as total_matieres,
                         AVG(note_moyenne) as avg_rating
                         FROM ressource");
    return $stmt->fetch();
}

/**
 * Récupère le nombre de ressources par matière
 */
function getResourceCountByMatiere($pdo, $matiere) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM ressource WHERE matiere = ?");
    $stmt->execute([$matiere]);
    return $stmt->fetch()['count'];
}

/**
 * Récupère le nombre total de téléchargements pour un utilisateur
 */
function getUserTotalDownloads($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT SUM(downloads) as total FROM ressource WHERE id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch()['total'] ?? 0;
}

// ========== FONCTIONS POUR LES IMAGES PAR MATIÈRE ==========

/**
 * Retourne l'image par défaut pour une matière donnée (photo illustrative, pas un logo).
 */
function getMatiereImage($matiere) {
    require_once __DIR__ . '/../includes/matiere_photos.php';
    $images = get_matiere_default_photos_map();
    return isset($images[$matiere]) ? $images[$matiere] : $images['Autre'];
}

/**
 * Retourne la couleur de fond pour une matière donnée
 */
function getMatiereColor($matiere) {
    $colors = [
        'Mathématiques' => '#e8f4fd',
        'Physique' => '#f0f4ff',
        'Chimie' => '#e8f8f5',
        'Informatique' => '#e8eef4',
        'Programmation' => '#f3e8ff',
        'HTML/CSS' => '#ffe8e8',
        'JavaScript' => '#fff3e8',
        'Python' => '#e8fce8',
        'Java' => '#fce8e8',
        'Base de données' => '#e8f0ff',
        'Réseaux' => '#e8f8ff',
        'Économie' => '#e8fce8',
        'Gestion' => '#fce8f0',
        'Droit' => '#f0e8fc',
        'Langues' => '#fce8e8',
        'Anglais' => '#fce8e8',
        'Français' => '#fce8e8',
        'Marketing' => '#e8fce8',
        'Design' => '#fce8f0',
        'Science' => '#f0f4ff',
        'Autre' => '#f0f0f0'
    ];
    
    return isset($colors[$matiere]) ? $colors[$matiere] : $colors['Autre'];
}

// ========== FONCTIONS POUR LA RECHERCHE ==========

/**
 * Recherche des ressources par mot-clé
 */
function searchResources($pdo, $keyword) {
    $keyword = "%$keyword%";
    $stmt = $pdo->prepare("SELECT r.*, u.nom, u.prenom 
                           FROM ressource r 
                           JOIN users u ON r.id = u.id 
                           WHERE r.titre LIKE ? 
                              OR r.description LIKE ? 
                              OR r.matiere LIKE ?
                           ORDER BY r.downloads DESC");
    $stmt->execute([$keyword, $keyword, $keyword]);
    return $stmt->fetchAll();
}

/**
 * Filtre les ressources par matière
 */
function filterResourcesByMatiere($pdo, $matiere) {
    $stmt = $pdo->prepare("SELECT r.*, u.nom, u.prenom 
                           FROM ressource r 
                           JOIN users u ON r.id = u.id 
                           WHERE r.matiere = ? 
                           ORDER BY r.id_res DESC");
    $stmt->execute([$matiere]);
    return $stmt->fetchAll();
}

/**
 * Filtre les ressources par niveau
 */
function filterResourcesByNiveau($pdo, $niveau) {
    $stmt = $pdo->prepare("SELECT r.*, u.nom, u.prenom 
                           FROM ressource r 
                           JOIN users u ON r.id = u.id 
                           WHERE r.niveau = ? 
                           ORDER BY r.id_res DESC");
    $stmt->execute([$niveau]);
    return $stmt->fetchAll();
}

/**
 * Filtre les ressources par accès (gratuit/premium)
 */
function filterResourcesByAccess($pdo, $acces) {
    $stmt = $pdo->prepare("SELECT r.*, u.nom, u.prenom 
                           FROM ressource r 
                           JOIN users u ON r.id = u.id 
                           WHERE r.acces = ? 
                           ORDER BY r.id_res DESC");
    $stmt->execute([$acces]);
    return $stmt->fetchAll();
}

// ========== FONCTIONS POUR LES NOTES ==========

/**
 * Met à jour la note moyenne d'une ressource
 */
function updateResourceRating($pdo, $resourceId) {
    $stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating FROM ratings WHERE id_res = ?");
    $stmt->execute([$resourceId]);
    $result = $stmt->fetch();
    $avg = round($result['avg_rating'] ?? 0, 2);
    
    $stmt = $pdo->prepare("UPDATE ressource SET note_moyenne = ? WHERE id_res = ?");
    return $stmt->execute([$avg, $resourceId]);
}

/**
 * Récupère la note moyenne d'une ressource
 */
function getResourceAverageRating($pdo, $resourceId) {
    $stmt = $pdo->prepare("SELECT note_moyenne FROM ressource WHERE id_res = ?");
    $stmt->execute([$resourceId]);
    $result = $stmt->fetch();
    return $result['note_moyenne'] ?? 0;
}

/**
 * Récupère les ressources les mieux notées
 */
function getTopRatedResources($pdo, $limit = 5) {
    $limit = intval($limit);
    $stmt = $pdo->query("SELECT r.*, u.nom, u.prenom 
                         FROM ressource r 
                         JOIN users u ON r.id = u.id 
                         WHERE r.note_moyenne > 0 
                         ORDER BY r.note_moyenne DESC 
                         LIMIT $limit");
    return $stmt->fetchAll();
}

// ========== FONCTIONS POUR LES TÉLÉCHARGEMENTS ==========

/**
 * Récupère les ressources les plus téléchargées par matière
 */
function getTopResourcesByMatiere($pdo, $matiere, $limit = 5) {
    $limit = intval($limit);
    $stmt = $pdo->prepare("SELECT r.*, u.nom, u.prenom 
                           FROM ressource r 
                           JOIN users u ON r.id = u.id 
                           WHERE r.matiere = ? 
                           ORDER BY r.downloads DESC 
                           LIMIT $limit");
    $stmt->execute([$matiere]);
    return $stmt->fetchAll();
}

/**
 * Récupère le nombre total de téléchargements global
 */
function getTotalDownloads($pdo) {
    $stmt = $pdo->query("SELECT SUM(downloads) as total FROM ressource");
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}

// ========== FONCTIONS POUR LES STATISTIQUES AVANCÉES ==========

/**
 * Récupère les statistiques mensuelles des téléchargements
 */
function getDownloadsByMonth($pdo, $months = 6) {
    $stmt = $pdo->query("SELECT 
                         DATE_FORMAT(date_creation, '%Y-%m') as month,
                         SUM(downloads) as total_downloads,
                         COUNT(*) as new_resources
                         FROM ressource 
                         GROUP BY month 
                         ORDER BY month DESC 
                         LIMIT $months");
    return $stmt->fetchAll();
}

/**
 * Récupère la répartition des ressources par type
 */
function getResourcesByTypeStats($pdo) {
    $stmt = $pdo->query("SELECT type, COUNT(*) as count 
                         FROM ressource 
                         GROUP BY type 
                         ORDER BY count DESC");
    return $stmt->fetchAll();
}

/**
 * Récupère la répartition des ressources par niveau
 */
function getResourcesByNiveauStats($pdo) {
    $stmt = $pdo->query("SELECT niveau, COUNT(*) as count 
                         FROM ressource 
                         WHERE niveau IS NOT NULL AND niveau != ''
                         GROUP BY niveau 
                         ORDER BY count DESC");
    return $stmt->fetchAll();
}

// ========== FONCTION DE NETTOYAGE ==========

/**
 * Nettoie les données d'une ressource pour l'affichage
 */
function cleanResourceData($resource) {
    if (empty($resource)) return $resource;
    
    $clean = [];
    foreach ($resource as $key => $value) {
        if (is_string($value)) {
            $clean[$key] = htmlspecialchars(trim(strip_tags($value)), ENT_QUOTES, 'UTF-8');
        } else {
            $clean[$key] = $value;
        }
    }
    return $clean;
}

/**
 * Nettoie un tableau de ressources
 */
function cleanResourcesData($resources) {
    if (empty($resources)) return $resources;
    
    $cleaned = [];
    foreach ($resources as $resource) {
        $cleaned[] = cleanResourceData($resource);
    }
    return $cleaned;
}
?>