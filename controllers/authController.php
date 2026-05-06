<?php
require_once __DIR__ . '/sql_queries.php';

// controllers/authController.php
function authLoginGet() {
    require_once __DIR__ . '/../views/auth/login.php';
}

function authLoginPost() {
    global $pdo;
    
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    require_once __DIR__ . '/../models/userModel.php';
    $user = getUserByEmail($pdo, $email);
    $showActivationLink = false;
    
    if ($user && ($password == $user['mdp'] || password_verify($password, $user['mdp']))) {
        if (isset($user['is_active']) && (int)$user['is_active'] !== 1) {
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
            setActivationToken($pdo, (int)$user['id'], $token, $expiresAt);
            $mailOk = sendActivationEmail($user['email'], trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')), $token);
            $mailErr = $_SESSION['last_activation_mail_error'] ?? '';
            unset($_SESSION['last_activation_mail_error']);
            $error = $mailOk
                ? "Compte inactif. Un lien d'activation vient d'etre envoye a votre email."
                : "Compte inactif. L'email n'a pas pu etre envoye" . ($mailErr !== '' ? " : " . $mailErr : '') . ". Configurez config/email.local.php (mot de passe d'application Gmail) ou utilisez « Activer mon compte » pour renvoyer.";
            $showActivationLink = true;
            require_once __DIR__ . '/../views/auth/login.php';
            return;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nom'];
        $_SESSION['user_prenom'] = $user['prenom'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        
        if ($user['role'] == 0) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_nom'] = $user['nom'];
            $_SESSION['admin_prenom'] = $user['prenom'];
            header('Location: index.php?action=admin&subaction=dashboard');
        } else {
            header('Location: index.php?action=home');
        }
        exit();
    } else {
        $error = "Email ou mot de passe incorrect";
        require_once __DIR__ . '/../views/auth/login.php';
    }
}

function faceDistance(array $a, array $b): float {
    $n = min(count($a), count($b));
    if ($n === 0) return 999.0;
    $sum = 0.0;
    for ($i = 0; $i < $n; $i++) {
        $da = (float)$a[$i];
        $db = (float)$b[$i];
        $d = $da - $db;
        $sum += $d * $d;
    }
    return sqrt($sum);
}

function authLoginFacePost() {
    global $pdo;
    require_once __DIR__ . '/../models/userModel.php';

    $email = trim($_POST['email'] ?? '');
    $descriptorJson = $_POST['face_descriptor'] ?? '';

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Veuillez entrer un email valide.";
        require_once __DIR__ . '/../views/auth/login.php';
        return;
    }

    $user = getUserByEmail($pdo, $email);
    if (!$user) {
        $error = "Email ou Face ID incorrect.";
        require_once __DIR__ . '/../views/auth/login.php';
        return;
    }

    if (isset($user['is_active']) && (int)$user['is_active'] !== 1) {
        $error = "Compte inactif. Activez votre compte avant de vous connecter.";
        require_once __DIR__ . '/../views/auth/login.php';
        return;
    }

    if (empty($user['face_enabled']) || empty($user['face_descriptor'])) {
        $error = "Face ID non configure pour ce compte.";
        require_once __DIR__ . '/../views/auth/login.php';
        return;
    }

    $stored = json_decode((string)$user['face_descriptor'], true);
    $live = json_decode((string)$descriptorJson, true);
    if (!is_array($stored) || !is_array($live)) {
        $error = "Face ID invalide. Reessayez.";
        require_once __DIR__ . '/../views/auth/login.php';
        return;
    }

    $dist = faceDistance($stored, $live);
    $threshold = 0.55;
    if ($dist > $threshold) {
        $error = "Email ou Face ID incorrect.";
        require_once __DIR__ . '/../views/auth/login.php';
        return;
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['nom'];
    $_SESSION['user_prenom'] = $user['prenom'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];

    if ((int)$user['role'] === 0) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_nom'] = $user['nom'];
        $_SESSION['admin_prenom'] = $user['prenom'];
        header('Location: index.php?action=admin&subaction=dashboard');
    } else {
        header('Location: index.php?action=home');
    }
    exit();
}

function authActivateAccount() {
    global $pdo;
    $token = $_GET['token'] ?? '';

    require_once __DIR__ . '/../models/userModel.php';
    $success = false;
    if (!empty($token)) {
        $success = activateUserByToken($pdo, $token);
    }
    $_SESSION['password_reset_success'] = $success
        ? "Votre compte est maintenant actif. Vous pouvez vous connecter."
        : "Lien invalide ou expire. Demandez un nouveau lien depuis la connexion.";
    header('Location: index.php?action=login');
    exit();
}

function authResendActivationGet() {
    require_once __DIR__ . '/../views/auth/resend_activation.php';
}

function authResendActivationPost() {
    global $pdo;
    require_once __DIR__ . '/../models/userModel.php';

    $email = trim($_POST['email'] ?? '');
    $message = '';
    $error = '';
    $canActivate = false;
    $emailForActivation = '';

    if (isset($_POST['send_activation_link'])) {
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Veuillez entrer un email valide.";
            require_once __DIR__ . '/../views/auth/resend_activation.php';
            return;
        }

        $user = getUserByEmail($pdo, $email);
        if (!$user) {
            $message = "Si cet email existe, un lien d'activation a ete envoye.";
            require_once __DIR__ . '/../views/auth/resend_activation.php';
            return;
        }

        if (isset($user['is_active']) && (int)$user['is_active'] === 1) {
            $message = "Votre compte est deja actif. Vous pouvez vous connecter.";
            require_once __DIR__ . '/../views/auth/resend_activation.php';
            return;
        }

        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
        setActivationToken($pdo, (int)$user['id'], $token, $expiresAt);
        $mailOk = sendActivationEmail($user['email'], trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')), $token);
        $mailErr = $_SESSION['last_activation_mail_error'] ?? '';
        unset($_SESSION['last_activation_mail_error']);
        $message = $mailOk
            ? "Lien d'activation envoye. Verifiez votre boite email."
            : "Envoi impossible. " . ($mailErr !== '' ? $mailErr . " " : '') . "Verifiez config/email.local.php (SMTP_PASS = mot de passe d'application Google).";
        require_once __DIR__ . '/../views/auth/resend_activation.php';
        return;
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Veuillez entrer un email valide.";
        require_once __DIR__ . '/../views/auth/resend_activation.php';
        return;
    }

    $user = getUserByEmail($pdo, $email);
    if (!$user) {
        $message = "Aucun compte trouve avec cet email.";
        require_once __DIR__ . '/../views/auth/resend_activation.php';
        return;
    }

    if (isset($user['is_active']) && (int)$user['is_active'] === 1) {
        $message = "Votre compte est deja actif. Vous pouvez vous connecter.";
        require_once __DIR__ . '/../views/auth/resend_activation.php';
        return;
    }

    $canActivate = true;
    $emailForActivation = $email;
    $message = "Email reconnu. Envoyez le lien d'activation a votre boite mail.";
    require_once __DIR__ . '/../views/auth/resend_activation.php';
}

function authRegisterGet() {
    require_once __DIR__ . '/../views/auth/register.php';
}

function normalizePhoneForStorage($phone) {
    $phone = trim((string)$phone);
    if ($phone === '') {
        return '';
    }

    // Keep digits only to avoid MySQL int overflow/coercion side effects.
    $digits = preg_replace('/\D+/', '', $phone);

    // Accept Tunisia format +216XXXXXXXX by storing local 8 digits.
    if (strlen($digits) === 11 && strpos($digits, '216') === 0) {
        $digits = substr($digits, 3);
    }

    if (strlen($digits) !== 8) {
        return false;
    }

    return $digits;
}

function authRegisterPost() {
    global $pdo;
    
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $universite = $_POST['universite'] ?? '';
    $filiere = $_POST['filiere'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $tel = $_POST['tel'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $faceDescriptor = $_POST['face_descriptor'] ?? '';
    $faceEnabled = isset($_POST['face_enabled']) ? (int)$_POST['face_enabled'] : 0;
    
    $error = '';
    
    $normalizedTel = normalizePhoneForStorage($tel);

    if ($normalizedTel === false) {
        $error = "Numéro de téléphone invalide (utilisez 8 chiffres ou +216XXXXXXXX)";
    } elseif ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas";
    } elseif (strlen($password) < 6) {
        $error = "Le mot de passe doit contenir au moins 6 caractères";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $photo = '';
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $uploadDir = __DIR__ . '/../uploads/';
            if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
            $photo = 'uploads/' . time() . '_' . basename($_FILES['photo']['name']);
            move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . basename($photo));
        }
        
        require_once __DIR__ . '/../models/userModel.php';
        $existingUser = getUserByEmail($pdo, $email);
        
        if ($existingUser) {
            $error = "Cet email est déjà utilisé";
        } else {
            $data = [
                'nom' => $nom, 'prenom' => $prenom, 'universite' => $universite,
                'filiere' => $filiere, 'email' => $email, 'mdp' => $hashed_password,
                'tel' => $normalizedTel, 'bio' => $bio, 'photo' => $photo
            ];
            try {
                $userId = createUser($pdo, $data);
                if ($faceEnabled === 1 && is_string($faceDescriptor) && $faceDescriptor !== '') {
                    $decoded = json_decode($faceDescriptor, true);
                    if (is_array($decoded) && count($decoded) >= 64) {
                        updateUserFace($pdo, (int)$userId, 1, $faceDescriptor);
                    }
                }
            } catch (PDOException $e) {
                if ($e->getCode() === '23000') {
                    if (stripos($e->getMessage(), "for key 'tel'") !== false) {
                        $error = "Ce numéro de téléphone est déjà utilisé";
                    } elseif (stripos($e->getMessage(), "for key 'email'") !== false) {
                        $error = "Cet email est déjà utilisé";
                    } else {
                        $error = "Une donnée existe déjà. Vérifiez vos informations.";
                    }
                } else {
                    $error = "Impossible de créer le compte pour le moment.";
                }
            }

            if ($error) {
                require_once __DIR__ . '/../views/auth/register.php';
                return;
            }

            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
            setActivationToken($pdo, (int)$userId, $token, $expiresAt);
            $sent = sendActivationEmail($email, trim($prenom . ' ' . $nom), $token);
            $mailErr = $_SESSION['last_activation_mail_error'] ?? '';
            unset($_SESSION['last_activation_mail_error']);
            $_SESSION['password_reset_success'] = $sent
                ? "Compte cree. Consultez votre email pour activer votre compte, puis connectez-vous."
                : "Compte cree, mais l'email n'est pas parti : " . ($mailErr !== '' ? $mailErr . " — " : '') . "Remplissez SMTP_PASS dans config/email.local.php (mot de passe d'application Gmail). Ensuite : connexion > Activer mon compte.";
            header('Location: index.php?action=login');
            exit();
        }
    }
    
    if ($error) {
        require_once __DIR__ . '/../views/auth/register.php';
    }
}

function authLogout() {
    session_destroy();
    header('Location: index.php?action=home');
    exit();
}

function studyhubClearPasswordResetSession(): void {
    unset(
        $_SESSION['password_reset_user_id'],
        $_SESSION['password_reset_phone'],
        $_SESSION['password_reset_otp_hash'],
        $_SESSION['password_reset_otp_expires'],
        $_SESSION['password_reset_verified'],
        $_SESSION['password_reset_otp_attempts'],
        $_SESSION['password_reset_last_sms_at']
    );
}

function authForgotPasswordGet() {
    if (isset($_GET['cancel']) && (string)$_GET['cancel'] === '1') {
        studyhubClearPasswordResetSession();
        header('Location: index.php?action=forgot_password');
        exit();
    }

    $step = 1;
    if (!empty($_SESSION['password_reset_verified']) && !empty($_SESSION['password_reset_user_id'])) {
        $step = 3;
    } elseif (!empty($_SESSION['password_reset_user_id']) && !empty($_SESSION['password_reset_otp_hash'])) {
        $step = 2;
    }
    require_once __DIR__ . '/../views/auth/forgot_password.php';
}

function authForgotPasswordPost() {
    global $pdo;
    require_once __DIR__ . '/../models/userModel.php';

    $step = 1;
    $message = '';
    $error = '';

    if (!empty($_SESSION['password_reset_verified']) && !empty($_SESSION['password_reset_user_id'])) {
        $step = 3;
    } elseif (!empty($_SESSION['password_reset_user_id']) && !empty($_SESSION['password_reset_otp_hash'])) {
        $step = 2;
    }

    if (isset($_POST['send_notification'])) {
        studyhubClearPasswordResetSession();

        $tel = $_POST['tel'] ?? '';
        $normalizedTel = normalizePhoneForStorage($tel);

        if ($normalizedTel === false) {
            $error = "Numéro invalide (8 chiffres ou +216XXXXXXXX).";
        } else {
            $user = getUserByPhone($pdo, $normalizedTel);
            if (!$user) {
                $error = "Aucun compte trouvé avec ce numéro.";
            } elseif (!studyhubSmsCanProceed()) {
                $hint = function_exists('studyhubTwilioMissingFromHint') ? studyhubTwilioMissingFromHint() : '';
                $error = $hint !== ''
                    ? $hint
                    : "SMS non configure : creez config/notifications.local.php avec vos cles Twilio (voir notifications.local.example.php), ou pour tester en local uniquement : definissez SMS_DEV_SHOW_CODE dans ce fichier.";
            } elseif (
                studyhubTwilioConfigured()
                && function_exists('studyhubTwilioFromEqualsRecipientTn')
                && studyhubTwilioFromEqualsRecipientTn(studyhubGetOutboundConfig('TWILIO_FROM_NUMBER'), $normalizedTel)
            ) {
                $error = "TWILIO_FROM_NUMBER est identique au numero du compte : Twilio l interdit. Mettez le numero d ENVOI affiche dans Twilio > Phone Numbers > Active numbers (souvent +1...), pas votre portable tunisien.";
            } else {
                $otp = str_pad((string)random_int(0, 9999), 4, '0', STR_PAD_LEFT);
                $_SESSION['password_reset_user_id'] = $user['id'];
                $_SESSION['password_reset_phone'] = $normalizedTel;
                $_SESSION['password_reset_otp_hash'] = password_hash($otp, PASSWORD_DEFAULT);
                $_SESSION['password_reset_otp_expires'] = time() + 900;
                $_SESSION['password_reset_otp_attempts'] = 0;
                $_SESSION['password_reset_last_sms_at'] = time();

                $e164 = studyhubPhoneToE164Tn($normalizedTel);
                $smsBody = 'StudyHub : votre code de reinitialisation est ' . $otp . '. Valide 15 minutes.';

                if (studyhubTwilioConfigured()) {
                    $smsResult = studyhubSendSmsTwilio($e164, $smsBody);
                    if (empty($smsResult['ok'])) {
                        studyhubClearPasswordResetSession();
                        $error = !empty($smsResult['error']) ? $smsResult['error'] : "Impossible d'envoyer le SMS.";
                    } else {
                        $step = 2;
                        $message = "Un SMS avec un code a 4 chiffres a ete envoye au " . htmlspecialchars($e164, ENT_QUOTES, 'UTF-8') . ".";
                    }
                } else {
                    $step = 2;
                    $message = "Mode test local — votre code est <strong>" . htmlspecialchars($otp, ENT_QUOTES, 'UTF-8') . "</strong> (aucun SMS envoye). Configurez Twilio pour un envoi reel.";
                }
            }
        }
    } elseif (isset($_POST['resend_otp'])) {
        if (empty($_SESSION['password_reset_user_id']) || empty($_SESSION['password_reset_phone'])) {
            $step = 1;
            $error = "Session expiree. Entrez a nouveau votre numero.";
        } elseif (!studyhubSmsCanProceed()) {
            $error = "SMS non configure (Twilio ou mode test SMS_DEV_SHOW_CODE).";
            $step = 2;
        } elseif (
            studyhubTwilioConfigured()
            && function_exists('studyhubTwilioFromEqualsRecipientTn')
            && studyhubTwilioFromEqualsRecipientTn(studyhubGetOutboundConfig('TWILIO_FROM_NUMBER'), (string)$_SESSION['password_reset_phone'])
        ) {
            $error = "TWILIO_FROM_NUMBER ne doit pas etre le meme que le destinataire (voir numero Twilio dans Active numbers).";
            $step = 2;
        } else {
            $last = (int)($_SESSION['password_reset_last_sms_at'] ?? 0);
            if ($last > 0 && (time() - $last) < 45) {
                $error = "Patientez quelques secondes avant de renvoyer le code.";
                $step = 2;
            } else {
                $otp = str_pad((string)random_int(0, 9999), 4, '0', STR_PAD_LEFT);
                $_SESSION['password_reset_otp_hash'] = password_hash($otp, PASSWORD_DEFAULT);
                $_SESSION['password_reset_otp_expires'] = time() + 900;
                $_SESSION['password_reset_otp_attempts'] = 0;
                $_SESSION['password_reset_last_sms_at'] = time();
                unset($_SESSION['password_reset_verified']);

                $e164 = studyhubPhoneToE164Tn((string)$_SESSION['password_reset_phone']);
                $smsBody = 'StudyHub : votre code de reinitialisation est ' . $otp . '. Valide 15 minutes.';

                if (studyhubTwilioConfigured()) {
                    $smsResult = studyhubSendSmsTwilio($e164, $smsBody);
                    if (empty($smsResult['ok'])) {
                        $error = !empty($smsResult['error']) ? $smsResult['error'] : "Impossible d'envoyer le SMS.";
                    } else {
                        $message = "Nouveau code envoye par SMS.";
                    }
                } else {
                    $message = "Mode test local — nouveau code : <strong>" . htmlspecialchars($otp, ENT_QUOTES, 'UTF-8') . "</strong>";
                }
                $step = 2;
            }
        }
    } elseif (isset($_POST['verify_otp'])) {
        $code = preg_replace('/\D+/', '', (string)($_POST['otp_code'] ?? ''));

        if (!isset($_SESSION['password_reset_user_id'], $_SESSION['password_reset_otp_hash'], $_SESSION['password_reset_otp_expires'])) {
            $step = 1;
            $error = "Session expiree. Recommencez.";
            studyhubClearPasswordResetSession();
        } elseif (time() > (int)$_SESSION['password_reset_otp_expires']) {
            $error = "Code expire. Demandez un nouveau code.";
            studyhubClearPasswordResetSession();
            $step = 1;
        } elseif (strlen($code) !== 4) {
            $error = "Entrez les 4 chiffres recus par SMS.";
            $step = 2;
        } else {
            $attempts = (int)($_SESSION['password_reset_otp_attempts'] ?? 0);
            if ($attempts >= 5) {
                studyhubClearPasswordResetSession();
                $step = 1;
                $error = "Trop de tentatives. Recommencez depuis le debut.";
            } elseif (!password_verify($code, (string)$_SESSION['password_reset_otp_hash'])) {
                $_SESSION['password_reset_otp_attempts'] = $attempts + 1;
                $error = "Code incorrect.";
                $step = 2;
            } else {
                $_SESSION['password_reset_verified'] = true;
                unset($_SESSION['password_reset_otp_hash'], $_SESSION['password_reset_otp_expires'], $_SESSION['password_reset_otp_attempts']);
                $step = 3;
                $message = "Code valide. Choisissez votre nouveau mot de passe.";
            }
        }
    } elseif (isset($_POST['reset_password'])) {
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $step = 3;

        if (!isset($_SESSION['password_reset_user_id'])) {
            $step = 1;
            $error = "Session expirée. Recommencez la procédure.";
            studyhubClearPasswordResetSession();
        } elseif (empty($_SESSION['password_reset_verified'])) {
            studyhubClearPasswordResetSession();
            $step = 1;
            $error = "Validez le code SMS avant de definir le mot de passe. Recommencez si necessaire.";
        } elseif (strlen($newPassword) < 6) {
            $error = "Le mot de passe doit contenir au moins 6 caractères.";
        } elseif ($newPassword !== $confirmPassword) {
            $error = "Les mots de passe ne correspondent pas.";
        } else {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updated = updateUserPasswordById($pdo, (int)$_SESSION['password_reset_user_id'], $hashedPassword);

            if ($updated) {
                studyhubClearPasswordResetSession();
                $_SESSION['password_reset_success'] = "Mot de passe réinitialisé avec succès. Connectez-vous.";
                header('Location: index.php?action=login');
                exit();
            }
            $error = "Impossible de mettre à jour le mot de passe. Réessayez.";
        }
    }

    require_once __DIR__ . '/../views/auth/forgot_password.php';
}
?>