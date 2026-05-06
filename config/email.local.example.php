<?php
/**
 * Copiez ce fichier vers email.local.php (a la racine du projet: config/email.local.php)
 * Sans ce fichier, les emails d activation ne partent pas vers Gmail sous XAMPP.
 *
 * Choisissez UNE des options ci-dessous (decommentez et remplissez).
 */

// --- Option A : SMTP (recommande en local : Gmail, Brevo, Outlook...) ---
// Gmail : activez la validation en 2 etapes, puis creez un "Mot de passe d application".
// https://myaccount.google.com/apppasswords

define('MAIL_FROM_EMAIL', 'votre.email@gmail.com');
define('MAIL_FROM_NAME', 'StudyHub');

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', '587');
define('SMTP_ENCRYPTION', 'tls');   // 'tls' pour le port 587, 'ssl' pour le port 465
define('SMTP_USER', 'votre.email@gmail.com');
define('SMTP_PASS', 'xxxx xxxx xxxx xxxx');  // mot de passe d application Gmail

// Si erreur TLS sur Windows, essayez temporairement (moins securise) :
// define('SMTP_VERIFY_PEER', '0');

// --- Option B : SendGrid (API HTTP, pas SMTP) ---
// define('SENDGRID_API_KEY', 'SG.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
// define('MAIL_FROM_EMAIL', 'activation@votredomaine.com');
// define('MAIL_FROM_NAME', 'StudyHub');
// Verifiez l expediteur dans le tableau SendGrid (Sender Authentication).
