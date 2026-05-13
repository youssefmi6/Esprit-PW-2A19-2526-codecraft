<?php
/**
 * PAGE DE DEBUG - Vérifier l'état de l'activation
 * Accéder via : http://localhost/studyhub/debug_activation.php
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/controllers/sql_queries.php';

$debug = [];

try {
    // Vérifier les colonnes
    $result = $pdo->query("DESC users");
    $columns = $result->fetchAll(PDO::FETCH_COLUMN, 0);
    $debug['colonnes_users'] = $columns;
    
    // Chercher les utilisateurs inactifs
    $stmt = $pdo->query("SELECT id, email, nom, prenom, is_active, activation_token, activation_token_expires_at FROM users WHERE is_active = 0");
    $inactiveUsers = $stmt->fetchAll();
    $debug['utilisateurs_inactifs'] = $inactiveUsers;
    
    // Chercher TOUS les utilisateurs
    $stmt = $pdo->query("SELECT id, email, nom, prenom, is_active, activation_token, activation_token_expires_at FROM users");
    $allUsers = $stmt->fetchAll();
    $debug['tous_utilisateurs'] = $allUsers;
    
} catch (Exception $e) {
    $debug['erreur'] = $e->getMessage();
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($debug, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
