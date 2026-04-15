<?php
// models/resourceModel.php
function getAllResources($pdo, $search = '', $type = '', $matiere = '') {
    $sql = "SELECT r.*, u.nom, u.prenom FROM ressource r JOIN users u ON r.id = u.id WHERE 1=1";
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

function getResourceById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT r.*, u.nom, u.prenom, u.email, u.id as user_id 
                           FROM ressource r JOIN users u ON r.id = u.id WHERE r.id_res = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getUserResources($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT * FROM ressource WHERE id = ? ORDER BY id_res DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function createResource($pdo, $data) {
    $stmt = $pdo->prepare("INSERT INTO ressource (titre, description, matiere, type, niveau, acces, prix, fichier, photo, id, pages, downloads) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
    $stmt->execute([
        $data['titre'], $data['description'], $data['matiere'], $data['type'],
        $data['niveau'], $data['acces'], $data['prix'], $data['fichier'],
        $data['photo'], $data['user_id'], $data['pages']
    ]);
    return $pdo->lastInsertId();
}

function updateResource($pdo, $id, $data) {
    $stmt = $pdo->prepare("UPDATE ressource SET titre=?, description=?, matiere=?, type=?, niveau=?, acces=?, prix=?, pages=?, photo=?, fichier=? 
                           WHERE id_res=?");
    return $stmt->execute([
        $data['titre'], $data['description'], $data['matiere'], $data['type'],
        $data['niveau'], $data['acces'], $data['prix'], $data['pages'],
        $data['photo'], $data['fichier'], $id
    ]);
}

function deleteResource($pdo, $id, $userId = null) {
    if ($userId) {
        $stmt = $pdo->prepare("DELETE FROM ressource WHERE id_res = ? AND id = ?");
        return $stmt->execute([$id, $userId]);
    } else {
        $stmt = $pdo->prepare("DELETE FROM ressource WHERE id_res = ?");
        return $stmt->execute([$id]);
    }
}

function incrementDownloads($pdo, $id) {
    $stmt = $pdo->prepare("UPDATE ressource SET downloads = downloads + 1 WHERE id_res = ?");
    return $stmt->execute([$id]);
}

function getRecentResources($pdo, $limit = 5) {
    $limit = intval($limit);
    $stmt = $pdo->query("SELECT r.*, u.nom, u.prenom FROM ressource r JOIN users u ON r.id = u.id ORDER BY r.id_res DESC LIMIT $limit");
    return $stmt->fetchAll();
}

function getTopResources($pdo, $limit = 5) {
    $limit = intval($limit);
    $stmt = $pdo->query("SELECT r.*, u.nom, u.prenom FROM ressource r JOIN users u ON r.id = u.id ORDER BY r.downloads DESC LIMIT $limit");
    return $stmt->fetchAll();
}

function getResourcesByMatiere($pdo, $limit = 4) {
    $limit = intval($limit);
    $stmt = $pdo->query("SELECT matiere, COUNT(*) as count FROM ressource GROUP BY matiere ORDER BY count DESC LIMIT $limit");
    return $stmt->fetchAll();
}

function getAllTypes($pdo) {
    $stmt = $pdo->query("SELECT DISTINCT type FROM ressource WHERE type IS NOT NULL AND type != ''");
    return $stmt->fetchAll();
}

function getAllMatieres($pdo) {
    $stmt = $pdo->query("SELECT DISTINCT matiere FROM ressource WHERE matiere IS NOT NULL AND matiere != ''");
    return $stmt->fetchAll();
}
?>