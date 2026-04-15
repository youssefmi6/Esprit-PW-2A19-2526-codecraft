<?php
// models/commentModel.php
function getCommentsByResource($pdo, $resourceId) {
    $stmt = $pdo->prepare("SELECT c.*, u.nom, u.prenom, u.photo 
                           FROM comment c JOIN users u ON c.id = u.id 
                           WHERE c.id_res = ? ORDER BY c.date DESC");
    $stmt->execute([$resourceId]);
    return $stmt->fetchAll();
}

function addComment($pdo, $resourceId, $userId, $message) {
    $stmt = $pdo->prepare("INSERT INTO comment (message, date, id, id_res) VALUES (?, NOW(), ?, ?)");
    return $stmt->execute([$message, $userId, $resourceId]);
}

function deleteComment($pdo, $commentId) {
    $stmt = $pdo->prepare("DELETE FROM comment WHERE id_comment = ?");
    return $stmt->execute([$commentId]);
}

function getAllComments($pdo) {
    $stmt = $pdo->query("SELECT c.*, u.nom, u.prenom, r.titre as ressource_titre 
                         FROM comment c 
                         JOIN users u ON c.id = u.id 
                         JOIN ressource r ON c.id_res = r.id_res 
                         ORDER BY c.date DESC");
    return $stmt->fetchAll();
}
?>