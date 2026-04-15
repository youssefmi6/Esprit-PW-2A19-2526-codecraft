<?php
// controllers/profileController.php
function profileIndex() {
    global $pdo;
    
    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }
    
    $user = getCurrentUser($pdo);
    
    require_once __DIR__ . '/../models/resourceModel.php';
    require_once __DIR__ . '/../models/userModel.php';
    
    $userResources = getUserResources($pdo, $user['id']);
    
    $totalResources = count($userResources);
    $totalDownloads = 0;
    $totalPremium = 0;
    $totalRating = 0;
    
    foreach ($userResources as $res) {
        $totalDownloads += $res['downloads'] ?? 0;
        if ($res['acces'] == 'Premium') $totalPremium++;
        $totalRating += $res['note_moyenne'] ?? 0;
    }
    
    $avgUserRating = $totalResources > 0 ? round($totalRating / $totalResources, 1) : 0;
    
    require_once __DIR__ . '/../views/profile/view.php';
}

function profileEditGet() {
    global $pdo;
    
    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }
    
    $user = getCurrentUser($pdo);
    require_once __DIR__ . '/../views/profile/edit.php';
}

function profileEditPost() {
    global $pdo;
    
    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }
    
    $user = getCurrentUser($pdo);
    
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $universite = $_POST['universite'] ?? '';
    $filiere = $_POST['filiere'] ?? '';
    $email = $_POST['email'] ?? '';
    $tel = $_POST['tel'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $mdp = $_POST['mdp'] ?? '';
    
    $photo = $user['photo'];
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $uploadDir = __DIR__ . '/../uploads/';
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
        $photo = 'uploads/' . time() . '_' . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . basename($photo));
    }
    
    require_once __DIR__ . '/../models/userModel.php';
    
    $data = [
        'nom' => $nom, 'prenom' => $prenom, 'universite' => $universite,
        'filiere' => $filiere, 'email' => $email, 'tel' => $tel,
        'bio' => $bio, 'photo' => $photo, 'mdp' => !empty($mdp) ? password_hash($mdp, PASSWORD_DEFAULT) : ''
    ];
    updateUser($pdo, $user['id'], $data);
    
    header('Location: index.php?action=profile');
    exit();
}

function profileView($id) {
    global $pdo;
    
    require_once __DIR__ . '/../models/userModel.php';
    require_once __DIR__ . '/../models/resourceModel.php';
    
    $profileUser = getUserById($pdo, $id);
    
    if (!$profileUser) {
        header('Location: index.php?action=home');
        exit();
    }
    
    $userResources = getUserResources($pdo, $id);
    
    $totalResources = count($userResources);
    $totalDownloads = 0;
    $totalRating = 0;
    
    foreach ($userResources as $res) {
        $totalDownloads += $res['downloads'] ?? 0;
        $totalRating += $res['note_moyenne'] ?? 0;
    }
    
    $avgUserRating = $totalResources > 0 ? round($totalRating / $totalResources, 1) : 0;
    $currentUser = getCurrentUser($pdo);
    
    require_once __DIR__ . '/../views/profile/view_other.php';
}
?>