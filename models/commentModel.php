<?php
// models/commentModel.php - Modèle pour les commentaires

class CommentModel
{
    public static function ensureCommentReactionTable(PDO $pdo): void
    {
        static $initialized = false;
        if ($initialized) {
            return;
        }

        $pdo->exec("CREATE TABLE IF NOT EXISTS comment_reaction (
                        id_reaction INT AUTO_INCREMENT PRIMARY KEY,
                        id_comment INT NOT NULL,
                        id_user INT NOT NULL,
                        reaction TINYINT NOT NULL,
                        date_reaction DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        UNIQUE KEY unique_comment_user_reaction (id_comment, id_user)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $initialized = true;
    }

    public static function getCommentsByResource(PDO $pdo, int $resourceId, ?int $currentUserId = null): array
    {
        self::ensureCommentReactionTable($pdo);

        $query = "SELECT c.*, u.nom, u.prenom, u.photo,
                         COALESCE(cr_count.likes_count, 0) AS likes_count,
                         COALESCE(cr_count.dislikes_count, 0) AS dislikes_count";
        if ($currentUserId !== null) {
            $query .= ", COALESCE(cr_user.reaction, 0) AS user_reaction";
        } else {
            $query .= ", 0 AS user_reaction";
        }

        $query .= " FROM comment c
                    JOIN users u ON c.id = u.id
                    LEFT JOIN (
                        SELECT id_comment,
                               SUM(CASE WHEN reaction = 1 THEN 1 ELSE 0 END) AS likes_count,
                               SUM(CASE WHEN reaction = -1 THEN 1 ELSE 0 END) AS dislikes_count
                        FROM comment_reaction
                        GROUP BY id_comment
                    ) cr_count ON c.id_comment = cr_count.id_comment";
        if ($currentUserId !== null) {
            $query .= " LEFT JOIN comment_reaction cr_user
                        ON c.id_comment = cr_user.id_comment AND cr_user.id_user = ?";
        }
        $query .= " WHERE c.id_res = ?
                    ORDER BY c.date DESC";
        $stmt = $pdo->prepare($query);
        if ($currentUserId !== null) {
            $stmt->execute([$currentUserId, $resourceId]);
        } else {
            $stmt->execute([$resourceId]);
        }
        return $stmt->fetchAll();
    }

    public static function addComment(PDO $pdo, int $resourceId, int $userId, string $message): bool
    {
        $stmt = $pdo->prepare("INSERT INTO comment (message, date, id, id_res) VALUES (?, NOW(), ?, ?)");
        return $stmt->execute([$message, $userId, $resourceId]);
    }

    public static function deleteComment(PDO $pdo, int $commentId): bool
    {
        $stmt = $pdo->prepare("DELETE FROM comment WHERE id_comment = ?");
        return $stmt->execute([$commentId]);
    }

    public static function getAllComments(PDO $pdo): array
    {
        $stmt = $pdo->query("SELECT c.*, u.nom, u.prenom, r.titre as ressource_titre
                             FROM comment c
                             JOIN users u ON c.id = u.id
                             JOIN ressource r ON c.id_res = r.id_res
                             ORDER BY c.date DESC");
        return $stmt->fetchAll();
    }

    public static function updateCommentByOwner(PDO $pdo, int $commentId, int $userId, string $message): bool
    {
        $stmt = $pdo->prepare("UPDATE comment SET message = ? WHERE id_comment = ? AND id = ?");
        $stmt->execute([$message, $commentId, $userId]);
        return $stmt->rowCount() > 0;
    }

    public static function deleteCommentByOwner(PDO $pdo, int $commentId, int $userId): bool
    {
        $stmt = $pdo->prepare("DELETE FROM comment WHERE id_comment = ? AND id = ?");
        $stmt->execute([$commentId, $userId]);
        return $stmt->rowCount() > 0;
    }

    public static function reactToComment(PDO $pdo, int $commentId, int $resourceId, int $userId, int $reaction): bool
    {
        self::ensureCommentReactionTable($pdo);
        if (!in_array($reaction, [1, -1], true)) {
            return false;
        }

        $checkStmt = $pdo->prepare("SELECT id_comment FROM comment WHERE id_comment = ? AND id_res = ?");
        $checkStmt->execute([$commentId, $resourceId]);
        if (!$checkStmt->fetch()) {
            return false;
        }

        $stmt = $pdo->prepare("INSERT INTO comment_reaction (id_comment, id_user, reaction, date_reaction)
                               VALUES (?, ?, ?, NOW())
                               ON DUPLICATE KEY UPDATE reaction = VALUES(reaction), date_reaction = NOW()");
        return $stmt->execute([$commentId, $userId, $reaction]);
    }
}

function getCommentsByResource($pdo, $resourceId, $currentUserId = null) { return CommentModel::getCommentsByResource($pdo, (int) $resourceId, $currentUserId !== null ? (int) $currentUserId : null); }
function addComment($pdo, $resourceId, $userId, $message) { return CommentModel::addComment($pdo, (int) $resourceId, (int) $userId, (string) $message); }
function deleteComment($pdo, $commentId) { return CommentModel::deleteComment($pdo, (int) $commentId); }
function getAllComments($pdo) { return CommentModel::getAllComments($pdo); }
function updateCommentByOwner($pdo, $commentId, $userId, $message) { return CommentModel::updateCommentByOwner($pdo, (int) $commentId, (int) $userId, (string) $message); }
function deleteCommentByOwner($pdo, $commentId, $userId) { return CommentModel::deleteCommentByOwner($pdo, (int) $commentId, (int) $userId); }
function reactToComment($pdo, $commentId, $resourceId, $userId, $reaction) { return CommentModel::reactToComment($pdo, (int) $commentId, (int) $resourceId, (int) $userId, (int) $reaction); }
?>