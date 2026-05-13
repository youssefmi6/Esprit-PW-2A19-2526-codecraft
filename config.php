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

$stripeLocalConfigPath = __DIR__ . '/config/stripe.local.php';
if (file_exists($stripeLocalConfigPath)) {
    require_once $stripeLocalConfigPath;
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

function getStripeConfigValue(string $key, string $default = ''): string {
    $envValue = getenv($key);
    if (is_string($envValue) && trim($envValue) !== '') {
        return trim($envValue);
    }

    if (defined($key)) {
        $constValue = constant($key);
        if (is_string($constValue) && trim($constValue) !== '') {
            return trim($constValue);
        }
    }

    return $default;
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

    $html = '<p>Bonjour ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ',</p>';
    $html .= '<p>Votre compte est inactif. Cliquez sur le bouton ci-dessous pour l\'activer.</p>';
    $html .= '<p><a href="' . htmlspecialchars($activationUrl, ENT_QUOTES, 'UTF-8') . '" style="display:inline-block;padding:12px 20px;background:#1a8cff;color:#fff;text-decoration:none;border-radius:10px;">Activer mon compte</a></p>';
    $html .= '<p style="font-size:12px;color:#666;">Ou copiez ce lien :<br>' . htmlspecialchars($activationUrl, ENT_QUOTES, 'UTF-8') . '</p>';

    $result = studyhubSendEmailTransactional($email, $subject, $message, $html);
    if (empty($result['ok'])) {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['last_activation_mail_error'] = $result['error'] ?? 'Erreur envoi email';
        }
        if (!empty($result['error'])) {
            error_log('StudyHub sendActivationEmail: ' . $result['error']);
        }
        return false;
    }
    if (session_status() === PHP_SESSION_ACTIVE) {
        unset($_SESSION['last_activation_mail_error']);
    }
    return true;
}
?>