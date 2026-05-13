<?php
// models/ratingModel.php - Modèle pour les notes / votes sur les ressources

function hasUserRated($pdo, $resourceId, $userId) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as n FROM ratings WHERE id_res = ? AND id_user = ?");
    $stmt->execute([$resourceId, $userId]);
    $row = $stmt->fetch();
    return (int)($row['n'] ?? 0) > 0;
}

function getTotalVotes($pdo, $resourceId) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as n FROM ratings WHERE id_res = ?");
    $stmt->execute([$resourceId]);
    $row = $stmt->fetch();
    return (int)($row['n'] ?? 0);
}

function addRating($pdo, $resourceId, $userId, $rating) {
    $stmt = $pdo->prepare("INSERT INTO ratings (id_res, id_user, rating) VALUES (?, ?, ?)");
    return $stmt->execute([$resourceId, $userId, $rating]);
}
