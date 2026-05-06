<?php
/**
 * Copiez ce fichier vers config/notifications.local.php
 *
 * --- SMS reels : Twilio (compte gratuit pour tester) ---
 * 1. https://www.twilio.com/try-twilio
 * 2. Console : Account SID + Auth Token (page d accueil du compte)
 * 3. TWILIO_FROM_NUMBER = uniquement le numero ATTRIBUE par Twilio (Active numbers), souvent +1...
 *    Ne mettez pas votre portable ni celui des utilisateurs : erreur « From and To cannot be the same ».
 * 4. Compte d essai : pour envoyer vers un portable reel, ajoutez-le dans
 *    Verified Caller IDs ; pour tout le monde sans verification, il faut un compte payant Twilio.
 *
 * Doc : https://www.twilio.com/docs/sms
 */

define('TWILIO_ACCOUNT_SID', 'ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('TWILIO_AUTH_TOKEN', 'your_auth_token');
define('TWILIO_FROM_NUMBER', '+1xxxxxxxxxx');

/**
 * --- Tests sur PC sans Twilio (XAMPP) ---
 * Decommentez la ligne ci-dessous : le code a 4 chiffres s affiche a l ecran au lieu du SMS.
 * A RETIRER en production (risque de fuite du code).
 */
// define('SMS_DEV_SHOW_CODE', true);
