<?php
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
    
    if ($user && ($password == $user['mdp'] || password_verify($password, $user['mdp']))) {
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

    if (isset($_POST['confirm_activate'])) {
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

        // Activation immediate requested from login flow.
        setUserActiveStatus($pdo, (int)$user['id'], 1);
        setActivationToken($pdo, (int)$user['id'], '', null);
        $_SESSION['password_reset_success'] = "Compte active avec succes. Vous pouvez vous connecter.";
        header('Location: index.php?action=login');
        exit();
    }

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
        sendActivationEmail($user['email'], trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')), $token);
        $message = "Lien d'activation envoye. Verifiez votre boite email.";
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
    $message = "Email correct. Cliquez sur Activer pour recevoir le lien d'activation.";
    require_once __DIR__ . '/../views/auth/resend_activation.php';
}

function authRegisterGet() {
    require_once __DIR__ . '/../views/auth/register.php';
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
    
    if ($password !== $confirm_password) {
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
                'tel' => $tel, 'bio' => $bio, 'photo' => $photo
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
            
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $nom;
            $_SESSION['user_prenom'] = $prenom;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = 1;
            
            header('Location: index.php?action=home');
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
?>