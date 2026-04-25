<?php
// config.php - Configuration de la base de données
// NE PAS mettre session_start() ici car elle est déjà dans index.php

$host = 'localhost';
$dbname = 'studehub';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Fonctions d'authentification
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] == 0;
}

function getCurrentUser($pdo) {
    if (!isset($_SESSION['user_id'])) return null;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function escape($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function generateStarRating($rating) {
    $full = floor($rating);
    $half = ($rating - $full) >= 0.5;
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $full) {
            $stars .= '★';
        } elseif ($half && $i == $full + 1) {
            $stars .= '½';
        } else {
            $stars .= '☆';
        }
    }
    return $stars;
}

function getAppBaseUrl() {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    if ($scriptDir === '' || $scriptDir === '.') {
        return $scheme . '://' . $host;
    }
    return $scheme . '://' . $host . $scriptDir;
}

function sendActivationEmail($email, $fullName, $token) {
    $activationUrl = getAppBaseUrl() . '/index.php?action=activate_account&token=' . urlencode($token);
    $subject = 'Activation de votre compte StudyHub';
    $name = trim((string)$fullName) !== '' ? $fullName : 'utilisateur';
    $message = "Bonjour " . $name . ",\n\n";
    $message .= "Votre compte est actuellement inactif.\n";
    $message .= "Cliquez sur ce lien pour activer votre compte :\n";
    $message .= $activationUrl . "\n\n";
    $message .= "Si vous n'etes pas a l'origine de cette demande, ignorez ce message.\n";

    $headers = "From: no-reply@studyhub.local\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    return @mail($email, $subject, $message, $headers);
}
?>