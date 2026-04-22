<?php
// models/userModel.php

class UserModel
{
    public static function getUserById(PDO $pdo, int $id)
    {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function getUserByEmail(PDO $pdo, string $email)
    {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public static function getAllUsers(PDO $pdo, string $search = ''): array
    {
        if ($search !== '') {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE nom LIKE ? OR prenom LIKE ? OR email LIKE ? ORDER BY id DESC");
            $like = '%' . $search . '%';
            $stmt->execute([$like, $like, $like]);
            return $stmt->fetchAll();
        }

        return $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
    }

    public static function createUser(PDO $pdo, array $data): string
    {
        $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, universite, filiere, email, mdp, tel, bio, photo, role, score)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 0)");
        $stmt->execute([
            $data['nom'], $data['prenom'], $data['universite'], $data['filiere'],
            $data['email'], $data['mdp'], $data['tel'], $data['bio'], $data['photo']
        ]);
        return $pdo->lastInsertId();
    }

    public static function updateUser(PDO $pdo, int $id, array $data): bool
    {
        $sql = "UPDATE users SET nom=?, prenom=?, universite=?, filiere=?, email=?, tel=?, bio=?, photo=?";
        $params = [$data['nom'], $data['prenom'], $data['universite'], $data['filiere'], $data['email'], $data['tel'], $data['bio'], $data['photo']];

        if (!empty($data['mdp'])) {
            $sql .= ", mdp=?";
            $params[] = $data['mdp'];
        }

        $sql .= " WHERE id=?";
        $params[] = $id;

        return $pdo->prepare($sql)->execute($params);
    }

    public static function deleteUser(PDO $pdo, int $id): bool
    {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function updateUserRole(PDO $pdo, int $id, int $role): bool
    {
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        return $stmt->execute([$role, $id]);
    }

    public static function getRecentUsers(PDO $pdo, int $limit = 5): array
    {
        $limit = max(1, (int) $limit);
        $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC LIMIT {$limit}");
        return $stmt->fetchAll();
    }

    public static function getAdminCount(PDO $pdo): int
    {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 0");
        return (int) ($stmt->fetch()['count'] ?? 0);
    }

    public static function getUserStats(PDO $pdo, int $userId)
    {
        $stmt = $pdo->prepare("SELECT COUNT(*) as total_resources, SUM(downloads) as total_downloads, AVG(note_moyenne) as avg_rating
                               FROM ressource WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
}

function getUserById($pdo, $id) { return UserModel::getUserById($pdo, (int) $id); }
function getUserByEmail($pdo, $email) { return UserModel::getUserByEmail($pdo, (string) $email); }
function getAllUsers($pdo, $search = '') { return UserModel::getAllUsers($pdo, (string) $search); }
function createUser($pdo, $data) { return UserModel::createUser($pdo, (array) $data); }
function updateUser($pdo, $id, $data) { return UserModel::updateUser($pdo, (int) $id, (array) $data); }
function deleteUser($pdo, $id) { return UserModel::deleteUser($pdo, (int) $id); }
function updateUserRole($pdo, $id, $role) { return UserModel::updateUserRole($pdo, (int) $id, (int) $role); }
function getRecentUsers($pdo, $limit = 5) { return UserModel::getRecentUsers($pdo, (int) $limit); }
function getAdminCount($pdo) { return UserModel::getAdminCount($pdo); }
function getUserStats($pdo, $userId) { return UserModel::getUserStats($pdo, (int) $userId); }

?>