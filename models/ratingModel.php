<?php
// models/ratingModel.php - Modèle pour les notes / votes sur les ressources

class RatingModel
{
    public static function hasUserRated(PDO $pdo, int $resourceId, int $userId): bool
    {
        $stmt = $pdo->prepare("SELECT COUNT(*) as n FROM ratings WHERE id_res = ? AND id_user = ?");
        $stmt->execute([$resourceId, $userId]);
        $row = $stmt->fetch();
        return (int) ($row['n'] ?? 0) > 0;
    }

    public static function getTotalVotes(PDO $pdo, int $resourceId): int
    {
        $stmt = $pdo->prepare("SELECT COUNT(*) as n FROM ratings WHERE id_res = ?");
        $stmt->execute([$resourceId]);
        $row = $stmt->fetch();
        return (int) ($row['n'] ?? 0);
    }

    public static function addRating(PDO $pdo, int $resourceId, int $userId, int $rating): bool
    {
        $stmt = $pdo->prepare("INSERT INTO ratings (id_res, id_user, rating) VALUES (?, ?, ?)");
        return $stmt->execute([$resourceId, $userId, $rating]);
    }
}

function hasUserRated($pdo, $resourceId, $userId) { return RatingModel::hasUserRated($pdo, (int) $resourceId, (int) $userId); }
function getTotalVotes($pdo, $resourceId) { return RatingModel::getTotalVotes($pdo, (int) $resourceId); }
function addRating($pdo, $resourceId, $userId, $rating) { return RatingModel::addRating($pdo, (int) $resourceId, (int) $userId, (int) $rating); }
