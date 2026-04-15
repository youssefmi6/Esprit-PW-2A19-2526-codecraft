<?php
// controllers/adminController.php
function adminLoginGet() {
    require_once __DIR__ . '/../views/admin/login.php';
}

function adminDashboard() {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }
    
    global $pdo;
    
    require_once __DIR__ . '/../models/userModel.php';
    require_once __DIR__ . '/../models/resourceModel.php';
    require_once __DIR__ . '/../models/commentModel.php';
    
    $stats = [];
    $stats['total_users'] = count(getAllUsers($pdo));
    $stats['total_resources'] = count(getAllResources($pdo));
    $stats['total_pages'] = 0;
    $stats['total_downloads'] = 0;
    $stats['total_comments'] = count(getAllComments($pdo));
    
    $recentUsers = getRecentUsers($pdo, 5);
    $recentResources = getRecentResources($pdo, 5);
    $topResources = getTopResources($pdo, 5);
    
    require_once __DIR__ . '/../views/admin/dashboard.php';
}

function adminUsers() {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }
    
    global $pdo;
    
    $search = $_GET['search'] ?? '';
    
    require_once __DIR__ . '/../models/userModel.php';
    $users = getAllUsers($pdo, $search);
    
    require_once __DIR__ . '/../views/admin/users.php';
}

function adminResources() {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }
    
    global $pdo;
    
    $search = $_GET['search'] ?? '';
    $type_filter = $_GET['type'] ?? '';
    $matiere_filter = $_GET['matiere'] ?? '';
    
    require_once __DIR__ . '/../models/resourceModel.php';
    $resources = getAllResources($pdo, $search, $type_filter, $matiere_filter);
    $types = getAllTypes($pdo);
    $matieres = getAllMatieres($pdo);
    
    require_once __DIR__ . '/../views/admin/resources.php';
}

function adminComments() {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }
    
    global $pdo;
    
    require_once __DIR__ . '/../models/commentModel.php';
    $comments = getAllComments($pdo);
    
    require_once __DIR__ . '/../views/admin/comments.php';
}

function adminProfileGet() {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }
    
    global $pdo;
    
    $admin = getCurrentUser($pdo);
    require_once __DIR__ . '/../views/admin/profile.php';
}

function adminProfilePost() {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }
    
    global $pdo;
    
    $admin = getCurrentUser($pdo);
    
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $email = $_POST['email'] ?? '';
    $universite = $_POST['universite'] ?? '';
    $filiere = $_POST['filiere'] ?? '';
    $mdp = $_POST['mdp'] ?? '';
    
    require_once __DIR__ . '/../models/userModel.php';
    
    $data = [
        'nom' => $nom, 'prenom' => $prenom, 'universite' => $universite,
        'filiere' => $filiere, 'email' => $email, 'tel' => $admin['tel'],
        'bio' => $admin['bio'], 'photo' => $admin['photo'],
        'mdp' => !empty($mdp) ? password_hash($mdp, PASSWORD_DEFAULT) : ''
    ];
    updateUser($pdo, $admin['id'], $data);
    
    $_SESSION['admin_nom'] = $nom;
    $_SESSION['admin_prenom'] = $prenom;
    
    header('Location: index.php?action=admin&subaction=profile');
    exit();
}

function adminViewUser($id) {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }
    
    global $pdo;
    
    if ($id <= 0) {
        header('Location: index.php?action=admin&subaction=users');
        exit();
    }
    
    require_once __DIR__ . '/../models/userModel.php';
    
    $user = getUserById($pdo, $id);
    if (!$user) {
        header('Location: index.php?action=admin&subaction=users');
        exit();
    }
    
    $stats = getUserStats($pdo, $id);
    
    require_once __DIR__ . '/../views/admin/view_user.php';
}

function adminEditUserGet($id) {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }
    
    global $pdo;
    
    require_once __DIR__ . '/../models/userModel.php';
    $user = getUserById($pdo, $id);
    
    // Vérifier que le fichier existe
    $file = __DIR__ . '/../views/admin/edit_user.php';
    if (!file_exists($file)) {
        die("Fichier introuvable : " . $file);
    }
    
    require_once $file;
}

function adminEditUserPost($id) {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }
    
    global $pdo;
    
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $email = $_POST['email'] ?? '';
    $universite = $_POST['universite'] ?? '';
    $filiere = $_POST['filiere'] ?? '';
    $role = intval($_POST['role'] ?? 1);
    $mdp = $_POST['mdp'] ?? '';
    
    require_once __DIR__ . '/../models/userModel.php';
    
    $user = getUserById($pdo, $id);
    $data = [
        'nom' => $nom, 'prenom' => $prenom, 'universite' => $universite,
        'filiere' => $filiere, 'email' => $email, 'tel' => $user['tel'],
        'bio' => $user['bio'], 'photo' => $user['photo'],
        'mdp' => !empty($mdp) ? password_hash($mdp, PASSWORD_DEFAULT) : ''
    ];
    updateUser($pdo, $id, $data);
    updateUserRole($pdo, $id, $role);
    
    header('Location: index.php?action=admin&subaction=users');
    exit();
}

function adminDeleteUser($id) {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }
    
    global $pdo;
    
    $currentAdmin = getCurrentUser($pdo);
    
    if ($id > 0 && $id != $currentAdmin['id']) {
        require_once __DIR__ . '/../models/userModel.php';
        deleteUser($pdo, $id);
    }
    
    header('Location: index.php?action=admin&subaction=users');
    exit();
}

function adminViewResource($id) {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }
    
    global $pdo;
    
    if ($id <= 0) {
        header('Location: index.php?action=admin&subaction=resources');
        exit();
    }
    
    require_once __DIR__ . '/../models/resourceModel.php';
    require_once __DIR__ . '/../models/commentModel.php';
    require_once __DIR__ . '/../models/ratingModel.php';
    
    $resource = getResourceById($pdo, $id);
    if (!$resource) {
        header('Location: index.php?action=admin&subaction=resources');
        exit();
    }
    
    $comments = getCommentsByResource($pdo, $id);
    $totalVotes = getTotalVotes($pdo, $id);
    
    require_once __DIR__ . '/../views/admin/view_resource.php';
}

function adminEditResourceGet($id) {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }
    
    global $pdo;
    
    require_once __DIR__ . '/../models/resourceModel.php';
    $resource = getResourceById($pdo, $id);
    
    // Vérifier que le fichier existe
    $file = __DIR__ . '/../views/admin/edit_resource.php';
    if (!file_exists($file)) {
        die("Fichier introuvable : " . $file);
    }
    
    require_once $file;
}

function adminEditResourcePost($id) {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }
    
    global $pdo;
    
    $titre = $_POST['titre'] ?? '';
    $description = $_POST['description'] ?? '';
    $type = $_POST['type'] ?? '';
    $niveau = $_POST['niveau'] ?? '';
    $acces = $_POST['acces'] ?? '';
    $prix = floatval($_POST['prix'] ?? 0);
    
    $uploadDir = __DIR__ . '/../uploads/';
    
    $fichier_nom = '';
    if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] === UPLOAD_ERR_OK) {
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $fichier_nom = time() . '_' . basename($_FILES['fichier']['name']);
        move_uploaded_file($_FILES['fichier']['tmp_name'], $uploadDir . $fichier_nom);
    }
    
    require_once __DIR__ . '/../models/resourceModel.php';
    $resource = getResourceById($pdo, $id);
    $fichier = $fichier_nom ?: $resource['fichier'];
    
    $data = [
        'titre' => $titre, 'description' => $description, 'matiere' => $resource['matiere'],
        'type' => $type, 'niveau' => $niveau, 'acces' => $acces, 'prix' => $prix,
        'pages' => $resource['pages'], 'photo' => $resource['photo'], 'fichier' => $fichier
    ];
    updateResource($pdo, $id, $data);
    
    header('Location: index.php?action=admin&subaction=resources');
    exit();
}

function adminDeleteResource($id) {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }
    
    global $pdo;
    
    require_once __DIR__ . '/../models/resourceModel.php';
    deleteResource($pdo, $id);
    
    header('Location: index.php?action=admin&subaction=resources');
    exit();
}

function adminDeleteComment($id) {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }
    
    global $pdo;
    
    require_once __DIR__ . '/../models/commentModel.php';
    deleteComment($pdo, $id);
    
    header('Location: index.php?action=admin&subaction=comments');
    exit();
}

function adminDownloadResource($id) {
    global $pdo;
    
    require_once __DIR__ . '/../models/resourceModel.php';
    $resource = getResourceById($pdo, $id);
    
    if ($resource && !empty($resource['fichier'])) {
        $file_path = __DIR__ . '/../uploads/' . $resource['fichier'];
        if (file_exists($file_path)) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);
            exit();
        }
    }
    
    header('Location: index.php?action=admin&subaction=resources');
    exit();
}
?>