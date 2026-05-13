<?php
require_once __DIR__ . '/sql_queries.php';

// controllers/adminController.php
function adminLoginGet() {
    require_once __DIR__ . '/../views/admin/login.php';
}

function adminLoginPost() {
    global $pdo;
    require_once __DIR__ . '/../models/userModel.php';

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = getUserByEmail($pdo, $email);
    if (!$user || !($password == $user['mdp'] || password_verify($password, $user['mdp']))) {
        $error = "Email ou mot de passe incorrect";
        require_once __DIR__ . '/../views/admin/login.php';
        return;
    }

    if ((int)($user['role'] ?? 1) !== 0) {
        $error = "Acces refuse. Ce compte n'est pas administrateur.";
        require_once __DIR__ . '/../views/admin/login.php';
        return;
    }

    if (isset($user['is_active']) && (int)$user['is_active'] !== 1) {
        $error = "Compte administrateur inactif. Activez-le depuis le lien d'activation.";
        require_once __DIR__ . '/../views/admin/login.php';
        return;
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['nom'];
    $_SESSION['user_prenom'] = $user['prenom'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['admin_id'] = $user['id'];
    $_SESSION['admin_nom'] = $user['nom'];
    $_SESSION['admin_prenom'] = $user['prenom'];

    header('Location: index.php?action=admin&subaction=dashboard');
    exit();
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
    
    $userStats = getUserDashboardStats($pdo);
    $resourceStats = getResourceStats($pdo);
    $stats = [];
    $stats['total_users'] = (int)($userStats['total_users'] ?? 0);
    $stats['total_admins'] = (int)($userStats['total_admins'] ?? 0);
    $stats['total_regular_users'] = (int)($userStats['total_regular_users'] ?? 0);
    $stats['total_active_users'] = (int)($userStats['total_active_users'] ?? 0);
    $stats['total_inactive_users'] = (int)($userStats['total_inactive_users'] ?? 0);
    $stats['total_resources'] = (int)($resourceStats['total_resources'] ?? 0);
    $stats['total_pages'] = (int)($resourceStats['total_pages'] ?? 0);
    $stats['total_downloads'] = (int)($resourceStats['total_downloads'] ?? 0);
    $stats['total_matieres'] = (int)($resourceStats['total_matieres'] ?? 0);
    $stats['avg_resource_rating'] = round((float)($resourceStats['avg_rating'] ?? 0), 2);
    $stats['total_comments'] = count(getAllComments($pdo));
    
    $recentUsers = getRecentUsers($pdo, 5);
    $recentResources = getRecentResources($pdo, 5);
    $topResources = getTopResources($pdo, 5);
    $resourcesByMatiere = getResourcesByMatiere($pdo, 8);
    
    require_once __DIR__ . '/../views/admin/dashboard.php';
}

function adminUsers() {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }
    
    global $pdo;
    
    $search = $_GET['search'] ?? '';
    $sort = $_GET['sort'] ?? 'date_desc';
    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = 3;
    $offset = ($page - 1) * $perPage;
    
    require_once __DIR__ . '/../models/userModel.php';
    $total = countUsers($pdo, $search);
    $totalPages = max(1, (int)ceil($total / $perPage));
    if ($page > $totalPages) {
        $page = $totalPages;
        $offset = ($page - 1) * $perPage;
    }
    $users = getAllUsers($pdo, $search, $perPage, $offset, $sort);

    if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'rows' => renderAdminUsersRows($users),
            'count' => $total,
            'page' => $page,
            'total_pages' => $totalPages,
            'sort' => $sort,
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
    $sort = $_GET['sort'] ?? 'date_desc';
    
    require_once __DIR__ . '/../models/resourceModel.php';
    $resources = getAllResources($pdo, $search, $type_filter, $matiere_filter, $sort);
    $types = getAllTypes($pdo);
    $matieres = getAllMatieres($pdo);

    if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'rows' => renderAdminResourcesRows($resources),
            'count' => count($resources),
            'sort' => $sort,
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

function adminToggleUserStatus($id) {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }

    global $pdo;
    require_once __DIR__ . '/../models/userModel.php';

    $currentAdmin = getCurrentUser($pdo);
    $user = getUserById($pdo, $id);
    if (!$user || (int)$id === (int)$currentAdmin['id']) {
        header('Location: index.php?action=admin&subaction=users');
        exit();
    }

    $newStatus = ((int)($user['is_active'] ?? 1) === 1) ? 0 : 1;
    setUserActiveStatus($pdo, $id, $newStatus);

    if ($newStatus === 0) {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
        setActivationToken($pdo, $id, $token, $expiresAt);
        sendActivationEmail($user['email'], trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')), $token);
        $_SESSION['admin_users_message'] = "Utilisateur desactive. Lien d'activation envoye par email.";
    } else {
        setActivationToken($pdo, $id, '', null);
        $_SESSION['admin_users_message'] = "Utilisateur active avec succes.";
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
        return '<tr><td colspan="8" class="text-center text-muted py-4">Aucun utilisateur trouve.</td></tr>';
    }

    $html = '';
    foreach ($users as $user) {
        $fullName = trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? ''));
        $roleLabel = ((int)($user['role'] ?? 1) === 0) ? 'Admin' : 'User';
        $isActive = (int)($user['is_active'] ?? 1) === 1;
        $statusBadge = $isActive ? '<span class="badge bg-success">Actif</span>' : '<span class="badge bg-danger">Inactif</span>';
        $isCurrentAdmin = isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)$user['id'];
        $statusTitle = $isCurrentAdmin ? 'Statut de votre compte' : ($isActive ? 'Desactiver et envoyer lien email' : 'Activer');
        $statusIcon = $isActive ? 'bi-toggle-off' : 'bi-toggle-on';
        $statusClass = $isActive ? 'btn-delete' : 'btn-edit';
        $statusAction = $isCurrentAdmin
            ? '<span class="' . $statusClass . '" title="' . $statusTitle . '"><i class="bi ' . $statusIcon . '"></i></span>'
            : '<a href="index.php?action=admin&subaction=toggle_user_status&id=' . (int)$user['id'] . '" class="' . $statusClass . '" title="' . $statusTitle . '" onclick="return confirm(\'Changer le statut de ce compte ?\')"><i class="bi ' . $statusIcon . '"></i></a>';
        $html .= '<tr>'
            . '<td>' . (int)$user['id'] . '</td>'
            . '<td><i class="bi bi-person-circle me-2" style="color:#1a8cff"></i>' . escape($fullName) . '</td>'
            . '<td>' . escape($user['email'] ?? '') . '</td>'
            . '<td>' . escape(($user['universite'] ?? '') ?: '-') . '</td>'
            . '<td>' . escape(($user['filiere'] ?? '') ?: '-') . '</td>'
            . '<td><span class="badge bg-secondary">' . $roleLabel . '</span></td>'
            . '<td>' . $statusBadge . '</td>'
            . '<td>'
            . '<a href="index.php?action=admin&subaction=view_user&id=' . (int)$user['id'] . '" class="btn-edit" title="Inspecter le profil"><i class="bi bi-eye-fill"></i></a>'
            . '<a href="index.php?action=admin&subaction=edit_user&id=' . (int)$user['id'] . '" class="btn-edit" title="Modifier"><i class="bi bi-pencil-fill"></i></a>'
            . $statusAction
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