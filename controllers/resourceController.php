<?php
// controllers/resourceController.php
function resourceDetail($id) {
    global $pdo;
    
    require_once __DIR__ . '/../models/resourceModel.php';
    require_once __DIR__ . '/../models/commentModel.php';
    require_once __DIR__ . '/../models/ratingModel.php';
    
    $resource = getResourceById($pdo, $id);
    if (!$resource) {
        header('Location: index.php?action=home');
        exit();
    }
    
    $currentUser = getCurrentUser($pdo);
    $comments = getCommentsByResource($pdo, $id);
    $hasRated = $currentUser ? hasUserRated($pdo, $id, $currentUser['id']) : false;
    $totalVotes = getTotalVotes($pdo, $id);
    
    $resource_rating = $resource['note_moyenne'] ?? 0;
    $full_stars = floor($resource_rating);
    $half_star = ($resource_rating - $full_stars) >= 0.5;
    
    $matiere_icons = [
        'Mathématiques' => 'https://cdn-icons-png.flaticon.com/512/3665/3665924.png',
        'Physique' => 'https://cdn-icons-png.flaticon.com/512/190/190665.png',
        'Chimie' => 'https://cdn-icons-png.flaticon.com/512/2908/2908010.png',
        'Informatique' => 'https://cdn-icons-png.flaticon.com/512/1995/1995572.png',
        'Programmation' => 'https://cdn-icons-png.flaticon.com/512/5968/5968292.png',
        'HTML/CSS' => 'https://cdn-icons-png.flaticon.com/512/732/732212.png',
        'Autre' => 'https://cdn-icons-png.flaticon.com/512/3665/3665924.png'
    ];
    
    require_once __DIR__ . '/../views/resource/detail.php';
}

function resourceUploadGet() {
    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }
    
    global $pdo;
    $user = getCurrentUser($pdo);  // <-- AJOUTER CETTE LIGNE
    
    require_once __DIR__ . '/../views/resource/upload.php';
}

function resourceUploadPost() {
    global $pdo;
    
    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }
    
    $user = getCurrentUser($pdo);
    
    $uploadDir = __DIR__ . '/../uploads/';
    $imageDir = __DIR__ . '/../uploads/images/';
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
    if (!file_exists($imageDir)) mkdir($imageDir, 0777, true);
    
    $matiere_icons = [
        'Mathématiques' => 'https://cdn-icons-png.flaticon.com/512/3665/3665924.png',
        'Physique' => 'https://cdn-icons-png.flaticon.com/512/190/190665.png',
        'Chimie' => 'https://cdn-icons-png.flaticon.com/512/2908/2908010.png',
        'Informatique' => 'https://cdn-icons-png.flaticon.com/512/1995/1995572.png',
        'Programmation' => 'https://cdn-icons-png.flaticon.com/512/5968/5968292.png',
        'HTML/CSS' => 'https://cdn-icons-png.flaticon.com/512/732/732212.png',
        'Autre' => 'https://cdn-icons-png.flaticon.com/512/3665/3665924.png'
    ];
    
    $titre = $_POST['titre'] ?? '';
    $description = $_POST['description'] ?? '';
    $matiere = $_POST['matiere'] ?? 'Autre';
    $type = $_POST['type'] ?? '';
    $niveau = $_POST['niveau'] ?? '';
    $acces = $_POST['acces'] ?? 'Gratuit';
    $prix = ($acces == 'Premium') ? floatval($_POST['prix'] ?? 0) : 0;
    $pages = intval($_POST['pages'] ?? 0);
    
    $photo = $matiere_icons[$matiere] ?? $matiere_icons['Autre'];
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
            $new_filename = 'img_' . time() . '_' . uniqid() . '.' . $ext;
            $photo = 'uploads/images/' . $new_filename;
            move_uploaded_file($_FILES['photo']['tmp_name'], $imageDir . $new_filename);
        }
    }
    
    $fichier = '';
    if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['fichier']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['pdf','doc','docx','txt'])) {
            $new_filename = time() . '_' . uniqid() . '.' . $ext;
            $fichier = 'uploads/' . $new_filename;
            move_uploaded_file($_FILES['fichier']['tmp_name'], $uploadDir . $new_filename);
        }
    }
    
    require_once __DIR__ . '/../models/resourceModel.php';
    $data = [
        'titre' => $titre, 'description' => $description, 'matiere' => $matiere,
        'type' => $type, 'niveau' => $niveau, 'acces' => $acces, 'prix' => $prix,
        'fichier' => $fichier, 'photo' => $photo, 'user_id' => $user['id'], 'pages' => $pages
    ];
    createResource($pdo, $data);
    
    header('Location: index.php?action=profile');
    exit();
}

function resourceEditGet($id) {
    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }
    
    global $pdo;
    $user = getCurrentUser($pdo);
    
    require_once __DIR__ . '/../models/resourceModel.php';
    $resource = getResourceById($pdo, $id);
    
    if (!$resource || $resource['user_id'] != $user['id']) {
        header('Location: index.php?action=profile');
        exit();
    }
    
    require_once __DIR__ . '/../views/resource/edit.php';
}

function resourceEditPost($id) {
    global $pdo;
    
    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }
    
    $user = getCurrentUser($pdo);
    
    require_once __DIR__ . '/../models/resourceModel.php';
    $resource = getResourceById($pdo, $id);
    
    if (!$resource || $resource['user_id'] != $user['id']) {
        header('Location: index.php?action=profile');
        exit();
    }
    
    $uploadDir = __DIR__ . '/../uploads/';
    $imageDir = __DIR__ . '/../uploads/images/';
    
    $titre = $_POST['titre'] ?? '';
    $description = $_POST['description'] ?? '';
    $matiere = $_POST['matiere'] ?? 'Autre';
    $type = $_POST['type'] ?? '';
    $niveau = $_POST['niveau'] ?? '';
    $acces = $_POST['acces'] ?? 'Gratuit';
    $prix = ($acces == 'Premium') ? floatval($_POST['prix'] ?? 0) : 0;
    $pages = intval($_POST['pages'] ?? 0);
    
    $photo = $resource['photo'];
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
            $new_filename = 'img_' . time() . '_' . uniqid() . '.' . $ext;
            $photo = 'uploads/images/' . $new_filename;
            move_uploaded_file($_FILES['photo']['tmp_name'], $imageDir . $new_filename);
        }
    }
    
    $fichier = $resource['fichier'];
    if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['fichier']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['pdf','doc','docx','txt'])) {
            $new_filename = time() . '_' . uniqid() . '.' . $ext;
            $fichier = 'uploads/' . $new_filename;
            move_uploaded_file($_FILES['fichier']['tmp_name'], $uploadDir . $new_filename);
        }
    }
    
    $data = [
        'titre' => $titre, 'description' => $description, 'matiere' => $matiere,
        'type' => $type, 'niveau' => $niveau, 'acces' => $acces, 'prix' => $prix,
        'pages' => $pages, 'photo' => $photo, 'fichier' => $fichier
    ];
    updateResource($pdo, $id, $data);
    
    header('Location: index.php?action=profile');
    exit();
}

function resourceDelete($id) {
    global $pdo;
    
    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }
    
    $user = getCurrentUser($pdo);
    
    require_once __DIR__ . '/../models/resourceModel.php';
    deleteResource($pdo, $id, $user['id']);
    
    header('Location: index.php?action=profile');
    exit();
}

function resourceDownload($id) {
    global $pdo;
    
    require_once __DIR__ . '/../models/resourceModel.php';
    $resource = getResourceById($pdo, $id);
    
    if (!$resource) {
        header('Location: index.php?action=home');
        exit();
    }
    
    incrementDownloads($pdo, $id);
    
    $file_path = __DIR__ . '/../' . $resource['fichier'];
    if (file_exists($file_path)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($resource['fichier']) . '"');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit();
    }
    
    header('Location: index.php?action=resource&subaction=detail&id=' . $id);
    exit();
}

function resourceBuyGet($id) {
    global $pdo;
    
    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }
    
    $currentUser = getCurrentUser($pdo);
    
    require_once __DIR__ . '/../models/resourceModel.php';
    $resource = getResourceById($pdo, $id);
    
    if (!$resource || $resource['acces'] != 'Premium') {
        header('Location: index.php?action=home');
        exit();
    }
    
    require_once __DIR__ . '/../views/resource/buy.php';
}

function resourceBuyPost($id) {
    header("Location: index.php?action=resource&subaction=download&id=$id");
    exit();
}

function resourceAddRating() {
    global $pdo;
    
    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }
    
    $user = getCurrentUser($pdo);
    $id_res = intval($_POST['id_res'] ?? 0);
    $rating = intval($_POST['rating'] ?? 0);
    
    require_once __DIR__ . '/../models/ratingModel.php';
    
    if ($id_res && $rating >= 1 && $rating <= 5 && !hasUserRated($pdo, $id_res, $user['id'])) {
        addRating($pdo, $id_res, $user['id'], $rating);
        updateResourceRating($pdo, $id_res);
    }
    
    header("Location: index.php?action=resource&subaction=detail&id=$id_res");
    exit();
}

function resourceAddComment() {
    global $pdo;
    
    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }
    
    $user = getCurrentUser($pdo);
    $id_res = intval($_POST['id_res'] ?? 0);
    $message = $_POST['message'] ?? '';
    
    if (!empty($message) && $id_res) {
        require_once __DIR__ . '/../models/commentModel.php';
        addComment($pdo, $id_res, $user['id'], $message);
    }
    
    header("Location: index.php?action=resource&subaction=detail&id=$id_res");
    exit();
}
?>