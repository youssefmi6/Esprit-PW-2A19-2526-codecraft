<?php
// models/ratingModel.php
function hasUserRated($pdo, $resourceId, $userId) {
    $stmt = $pdo->prepare("SELECT * FROM ratings WHERE id_res = ? AND id_user = ?");
    $stmt->execute([$resourceId, $userId]);
    return $stmt->rowCount() > 0;
}

function addRating($pdo, $resourceId, $userId, $rating) {
    $stmt = $pdo->prepare("INSERT INTO ratings (id_res, id_user, rating) VALUES (?, ?, ?)");
    return $stmt->execute([$resourceId, $userId, $rating]);
}

function getResourceAverageRating($pdo, $resourceId) {
    $stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating FROM ratings WHERE id_res = ?");
    $stmt->execute([$resourceId]);
    $result = $stmt->fetch();
    return round($result['avg_rating'] ?? 0, 2);
}

function updateResourceRating($pdo, $resourceId) {
    $avg = getResourceAverageRating($pdo, $resourceId);
    $stmt = $pdo->prepare("UPDATE ressource SET note_moyenne = ? WHERE id_res = ?");
    return $stmt->execute([$avg, $resourceId]);
}

function getTotalVotes($pdo, $resourceId) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM ratings WHERE id_res = ?");
    $stmt->execute([$resourceId]);
    return $stmt->fetch()['total'];
}

function getUserAverageRating($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT AVG(note_moyenne) as avg_rating FROM ressource WHERE id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();
    return round($result['avg_rating'] ?? 0, 2);
}
?>