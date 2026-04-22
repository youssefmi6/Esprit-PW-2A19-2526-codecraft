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

    if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'rows' => renderAdminUsersRows($users),
            'count' => count($users),
        ]);
        exit();
    }
    
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

    if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'rows' => renderAdminResourcesRows($resources),
            'count' => count($resources),
        ]);
        exit();
    }
    
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

function renderAdminUsersRows($users) {
    if (empty($users)) {
        return '<tr><td colspan="7" class="text-center text-muted py-4">Aucun utilisateur trouvé.</td></tr>';
    }

    $html = '';
    foreach ($users as $user) {
        $fullName = trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? ''));
        $roleLabel = ((int)($user['role'] ?? 1) === 0) ? 'Admin' : 'User';
        $html .= '<tr>'
            . '<td>' . (int)$user['id'] . '</td>'
            . '<td><i class="bi bi-person-circle me-2" style="color:#1a8cff"></i>' . escape($fullName) . '</td>'
            . '<td>' . escape($user['email'] ?? '') . '</td>'
            . '<td>' . escape(($user['universite'] ?? '') ?: '-') . '</td>'
            . '<td>' . escape(($user['filiere'] ?? '') ?: '-') . '</td>'
            . '<td><span class="badge bg-secondary">' . $roleLabel . '</span></td>'
            . '<td>'
            . '<a href="index.php?action=admin&subaction=view_user&id=' . (int)$user['id'] . '" class="btn-edit" title="Inspecter le profil"><i class="bi bi-eye-fill"></i></a>'
            . '<a href="index.php?action=admin&subaction=edit_user&id=' . (int)$user['id'] . '" class="btn-edit" title="Modifier"><i class="bi bi-pencil-fill"></i></a>'
            . '<a href="index.php?action=admin&subaction=delete_user&id=' . (int)$user['id'] . '" class="btn-delete" title="Supprimer" onclick="return confirm(\'Supprimer ?\')"><i class="bi bi-trash3-fill"></i></a>'
            . '</td>'
            . '</tr>';
    }

    return $html;
}

function renderAdminResourcesRows($resources) {
    if (empty($resources)) {
        return '<tr><td colspan="10" class="text-center text-muted py-4">Aucune ressource trouvée.</td></tr>';
    }

    $html = '';
    foreach ($resources as $r) {
        $title = substr($r['titre'] ?? '', 0, 40);
        $rating = (float)($r['note_moyenne'] ?? 0);
        $ratingCell = $rating > 0
            ? '<i class="bi bi-star-fill text-warning"></i> ' . escape((string)$rating)
            : '-';

        $html .= '<tr>'
            . '<td>' . (int)$r['id_res'] . '</td>'
            . '<td>' . escape($title) . '</td>'
            . '<td><span class="badge bg-info">' . escape($r['type'] ?? '') . '</span></td>'
            . '<td><span class="badge-matiere">' . escape(($r['matiere'] ?? '') ?: 'Autre') . '</span></td>'
            . '<td>' . escape(($r['niveau'] ?? '') ?: '-') . '</td>'
            . '<td>' . escape(trim(($r['prenom'] ?? '') . ' ' . ($r['nom'] ?? ''))) . '</td>'
            . '<td>' . escape((string)(($r['pages'] ?? '') ?: '-')) . '</td>'
            . '<td><i class="bi bi-download"></i> ' . (int)($r['downloads'] ?? 0) . '</td>'
            . '<td>' . $ratingCell . '</td>'
            . '<td>'
            . '<a href="index.php?action=admin&subaction=view_resource&id=' . (int)$r['id_res'] . '" class="btn-edit"><i class="bi bi-eye-fill"></i></a>'
            . '<a href="index.php?action=admin&subaction=edit_resource&id=' . (int)$r['id_res'] . '" class="btn-edit"><i class="bi bi-pencil-fill"></i></a>'
            . '<a href="index.php?action=admin&subaction=delete_resource&id=' . (int)$r['id_res'] . '" class="btn-delete" onclick="return confirm(\'Supprimer ?\')"><i class="bi bi-trash3-fill"></i></a>'
            . '</td>'
            . '</tr>';
    }

    return $html;
}
?>