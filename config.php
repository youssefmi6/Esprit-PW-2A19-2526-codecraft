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
?>