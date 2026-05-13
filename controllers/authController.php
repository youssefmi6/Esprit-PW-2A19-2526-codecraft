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
            $userId = createUser($pdo, $data);
            
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