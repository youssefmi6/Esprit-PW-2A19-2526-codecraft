<?php
// models/userModel.php
function getUserById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getUserByEmail($pdo, $email) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}

function getAllUsers($pdo, $search = '') {
    if (!empty($search)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE nom LIKE ? OR prenom LIKE ? OR email LIKE ? ORDER BY id DESC");
        $stmt->execute(["%$search%", "%$search%", "%$search%"]);
    } else {
        $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
    }
    return $stmt->fetchAll();
}

function createUser($pdo, $data) {
    $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, universite, filiere, email, mdp, tel, bio, photo, role, score) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 0)");
    $stmt->execute([
        $data['nom'], $data['prenom'], $data['universite'], $data['filiere'],
        $data['email'], $data['mdp'], $data['tel'], $data['bio'], $data['photo']
    ]);
    return $pdo->lastInsertId();
}

function updateUser($pdo, $id, $data) {
    $sql = "UPDATE users SET nom=?, prenom=?, universite=?, filiere=?, email=?, tel=?, bio=?, photo=?";
    $params = [$data['nom'], $data['prenom'], $data['universite'], $data['filiere'], $data['email'], $data['tel'], $data['bio'], $data['photo']];
    
    if (!empty($data['mdp'])) {
        $sql .= ", mdp=?";
        $params[] = $data['mdp'];
    }
    
    $sql .= " WHERE id=?";
    $params[] = $id;
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

function deleteUser($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    return $stmt->execute([$id]);
}

function updateUserRole($pdo, $id, $role) {
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    return $stmt->execute([$role, $id]);
}

function getRecentUsers($pdo, $limit = 5) {
    $limit = intval($limit);
    $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC LIMIT $limit");
    return $stmt->fetchAll();
}

function getAdminCount($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 0");
    return $stmt->fetch()['count'];
}

function getUserStats($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_resources, SUM(downloads) as total_downloads, AVG(note_moyenne) as avg_rating 
                           FROM ressource WHERE id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch();
}
?>