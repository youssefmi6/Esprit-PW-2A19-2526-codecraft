<?php
// Inclusion du fichier de configuration
require_once dirname(__FILE__) . '/config.php';

// Création de la connexion
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Vérifier la connexion
    if ($conn->connect_error) {
        die("Erreur de connexion : " . $conn->connect_error);
    }
    
    // Définir le charset
    $conn->set_charset("utf8");
    
    // Mode d'erreur mysqli
    $conn->query("SET SESSION sql_mode='STRICT_TRANS_TABLES'");
    
} catch (Exception $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
