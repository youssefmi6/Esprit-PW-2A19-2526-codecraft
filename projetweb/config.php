<?php
/**
 * Configuration Globale de l'Application
 * Fichier: config.php
 * 
 * Ce fichier contient toutes les constantes et configurations
 * de l'application E-Business
 */

// ===== CONFIGURATION BASE DE DONNÉES =====
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'projetr');

// ===== CONFIGURATION APPLICATION =====
define('APP_NAME', 'E-Business');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/projetweb/');

// ===== STATUTS VALIDES =====
define('STATUTS_VALIDES', array(
    'En attente',
    'En cours',
    'Résolu',
    'Rejeté'
));

// ===== LIMITES DE VALIDATION =====
define('MIN_TITRE_LENGTH', 3);
define('MAX_TITRE_LENGTH', 100);
define('MIN_DESC_LENGTH', 10);
define('MAX_DESC_LENGTH', 5000);
define('MIN_REPONSE_LENGTH', 5);
define('MAX_REPONSE_LENGTH', 5000);

// ===== AUTRES CONFIGURATIONS =====
define('TIMEZONE', 'Europe/Paris');
define('DATE_FORMAT', 'd/m/Y H:i:s');

// Définir le timezone
date_default_timezone_set(TIMEZONE);

// ===== AFFICHAGE D'ERREURS (À désactiver en production) =====
if (defined('DEVELOPMENT')) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}
?>
