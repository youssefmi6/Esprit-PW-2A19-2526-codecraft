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