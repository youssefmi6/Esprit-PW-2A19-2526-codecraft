<?php
// models/userModel.php

class UserModel
{
    public static function ensureUserActivationColumns(PDO $pdo): void
    {
        static $initialized = false;
        if ($initialized) {
            return;
        }
        $initialized = true;

        $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS is_active TINYINT(1) NOT NULL DEFAULT 1");
        $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS activation_token VARCHAR(64) DEFAULT NULL");
        $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS activation_token_expires_at DATETIME DEFAULT NULL");

        // Face ID (optional)
        $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS face_enabled TINYINT(1) NOT NULL DEFAULT 0");
        $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS face_descriptor LONGTEXT DEFAULT NULL");
    }

    public static function getUserById(PDO $pdo, int $id)
    {
        self::ensureUserActivationColumns($pdo);
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function getUserByEmail(PDO $pdo, string $email)
    {
        self::ensureUserActivationColumns($pdo);
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public static function getUserByPhone(PDO $pdo, string $phone)
    {
        self::ensureUserActivationColumns($pdo);
        $stmt = $pdo->prepare("SELECT * FROM users WHERE tel = ?");
        $stmt->execute([$phone]);
        return $stmt->fetch();
    }

    public static function getAllUsers(PDO $pdo, string $search = '', int $limit = 0, int $offset = 0): array
    {
        self::ensureUserActivationColumns($pdo);
        if ($search !== '') {
            $sql = "SELECT * FROM users WHERE nom LIKE ? OR prenom LIKE ? OR email LIKE ? ORDER BY id DESC";
            if ($limit > 0) {
                $sql .= " LIMIT " . (int)$limit . " OFFSET " . max(0, (int)$offset);
            }
            $stmt = $pdo->prepare($sql);
            $like = '%' . $search . '%';
            $stmt->execute([$like, $like, $like]);
            return $stmt->fetchAll();
        }

        $sql = "SELECT * FROM users ORDER BY id DESC";
        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . max(0, (int)$offset);
        }
        return $pdo->query($sql)->fetchAll();
    }

    public static function countUsers(PDO $pdo, string $search = ''): int
    {
        self::ensureUserActivationColumns($pdo);
        if ($search !== '') {
            $stmt = $pdo->prepare("SELECT COUNT(*) as c FROM users WHERE nom LIKE ? OR prenom LIKE ? OR email LIKE ?");
            $like = '%' . $search . '%';
            $stmt->execute([$like, $like, $like]);
            return (int)($stmt->fetch()['c'] ?? 0);
        }
        return (int)($pdo->query("SELECT COUNT(*) as c FROM users")->fetch()['c'] ?? 0);
    }

    public static function createUser(PDO $pdo, array $data): string
    {
        self::ensureUserActivationColumns($pdo);
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
        self::ensureUserActivationColumns($pdo);
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
        self::ensureUserActivationColumns($pdo);
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function updateUserRole(PDO $pdo, int $id, int $role): bool
    {
        self::ensureUserActivationColumns($pdo);
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        return $stmt->execute([$role, $id]);
    }

    public static function updateUserPassword(PDO $pdo, int $id, string $hashedPassword): bool
    {
        self::ensureUserActivationColumns($pdo);
        $stmt = $pdo->prepare("UPDATE users SET mdp = ? WHERE id = ?");
        return $stmt->execute([$hashedPassword, $id]);
    }

    public static function updateUserPhoto(PDO $pdo, int $id, string $photo): bool
    {
        self::ensureUserActivationColumns($pdo);
        $stmt = $pdo->prepare("UPDATE users SET photo = ? WHERE id = ?");
        return $stmt->execute([$photo, $id]);
    }

    public static function updateUserFace(PDO $pdo, int $id, int $enabled, ?string $descriptorJson): bool
    {
        self::ensureUserActivationColumns($pdo);
        $stmt = $pdo->prepare("UPDATE users SET face_enabled = ?, face_descriptor = ? WHERE id = ?");
        return $stmt->execute([$enabled, $descriptorJson, $id]);
    }

    public static function setUserActiveStatus(PDO $pdo, int $id, int $isActive): bool
    {
        self::ensureUserActivationColumns($pdo);
        $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?");
        return $stmt->execute([$isActive, $id]);
    }

    public static function setActivationToken(PDO $pdo, int $id, string $token, ?string $expiresAt): bool
    {
        self::ensureUserActivationColumns($pdo);
        $stmt = $pdo->prepare("UPDATE users SET activation_token = ?, activation_token_expires_at = ? WHERE id = ?");
        return $stmt->execute([$token, $expiresAt, $id]);
    }

    public static function getUserByActivationToken(PDO $pdo, string $token)
    {
        self::ensureUserActivationColumns($pdo);
        $stmt = $pdo->prepare("SELECT * FROM users WHERE activation_token = ? LIMIT 1");
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    public static function activateUserByToken(PDO $pdo, string $token): bool
    {
        self::ensureUserActivationColumns($pdo);
        $stmt = $pdo->prepare("UPDATE users
            SET is_active = 1, activation_token = NULL, activation_token_expires_at = NULL
            WHERE activation_token = ? AND activation_token_expires_at IS NOT NULL AND activation_token_expires_at >= NOW()");
        return $stmt->execute([$token]) && $stmt->rowCount() > 0;
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
function getUserByPhone($pdo, $phone) { return UserModel::getUserByPhone($pdo, (string) $phone); }
function getAllUsers($pdo, $search = '', $limit = 0, $offset = 0) { return UserModel::getAllUsers($pdo, (string) $search, (int)$limit, (int)$offset); }
function countUsers($pdo, $search = '') { return UserModel::countUsers($pdo, (string)$search); }
function createUser($pdo, $data) { return UserModel::createUser($pdo, (array) $data); }
function updateUser($pdo, $id, $data) { return UserModel::updateUser($pdo, (int) $id, (array) $data); }
function deleteUser($pdo, $id) { return UserModel::deleteUser($pdo, (int) $id); }
function updateUserRole($pdo, $id, $role) { return UserModel::updateUserRole($pdo, (int) $id, (int) $role); }
function updateUserPasswordById($pdo, $id, $hashedPassword) { return UserModel::updateUserPassword($pdo, (int) $id, (string) $hashedPassword); }
function updateUserPhotoById($pdo, $id, $photo) { return UserModel::updateUserPhoto($pdo, (int)$id, (string)$photo); }
function updateUserFace($pdo, $id, $enabled, $descriptorJson = null) { return UserModel::updateUserFace($pdo, (int)$id, (int)$enabled, $descriptorJson !== null ? (string)$descriptorJson : null); }
function setUserActiveStatus($pdo, $id, $isActive) { return UserModel::setUserActiveStatus($pdo, (int)$id, (int)$isActive); }
function setActivationToken($pdo, $id, $token, $expiresAt = null) { return UserModel::setActivationToken($pdo, (int)$id, (string)$token, $expiresAt !== null ? (string)$expiresAt : null); }
function getUserByActivationToken($pdo, $token) { return UserModel::getUserByActivationToken($pdo, (string)$token); }
function activateUserByToken($pdo, $token) { return UserModel::activateUserByToken($pdo, (string)$token); }
function getRecentUsers($pdo, $limit = 5) { return UserModel::getRecentUsers($pdo, (int) $limit); }
function getAdminCount($pdo) { return UserModel::getAdminCount($pdo); }
function getUserStats($pdo, $userId) { return UserModel::getUserStats($pdo, (int) $userId); }

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