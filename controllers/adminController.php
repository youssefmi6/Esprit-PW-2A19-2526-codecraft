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
    require_once __DIR__ . '/../models/subscriptionModel.php';
    
    $stats = [];
    $stats['total_users'] = count(getAllUsers($pdo));
    $stats['total_resources'] = count(getAllResources($pdo));
    $stats['total_pages'] = 0;
    $stats['total_downloads'] = 0;
    $stats['total_comments'] = count(getAllComments($pdo));
    
    $subStats = getSubscriptionDashboardStats($pdo);
    $stats['subscribers_active'] = $subStats['total_active_subscribers'];
    $stats['subscribers_without'] = max(0, $stats['total_users'] - $subStats['total_active_subscribers']);
    $recentAbonnements = getRecentAbonnementsForAdmin($pdo, 8);
    
    $recentUsers = getRecentUsers($pdo, 5);
    $recentResources = getRecentResources($pdo, 5);
    $topResources = getTopResources($pdo, 5);
    
    require_once __DIR__ . '/../views/admin/dashboard.php';
}

function adminSubscriptions() {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }

    global $pdo;

    require_once __DIR__ . '/../models/subscriptionModel.php';
    $search = $_GET['search'] ?? '';
    $abonnements = getAllAbonnementsForAdmin($pdo, $search);
    $subStats = getSubscriptionDashboardStats($pdo);
    $catalogPlans = getAllSubscriptionPlansCatalog($pdo);

    require_once __DIR__ . '/../views/admin/subscriptions.php';
}

function adminSubscriptionAddGet() {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }

    global $pdo;

    require_once __DIR__ . '/../models/userModel.php';
    require_once __DIR__ . '/../models/subscriptionModel.php';

    $users = getAllUsers($pdo);
    $abonnement = null;
    $catalogPlansForAssign = subscriptionPlansTablesExist($pdo) ? getAllSubscriptionPlansCatalog($pdo) : [];

    require_once __DIR__ . '/../views/admin/subscription_form.php';
}

function adminSubscriptionAddPost() {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }

    global $pdo;

    require_once __DIR__ . '/../models/userModel.php';
    require_once __DIR__ . '/../models/subscriptionModel.php';

    $idUser = (int) ($_POST['id_user'] ?? 0);
    $dateDebut = trim($_POST['date_debut'] ?? '');
    $dateFin = trim($_POST['date_fin'] ?? '');

    if ($idUser < 1 || !getUserById($pdo, $idUser)) {
        $_SESSION['admin_sub_error'] = 'Membre invalide.';
        header('Location: index.php?action=admin&subaction=subscription_add');
        exit();
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateDebut) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFin)) {
        $_SESSION['admin_sub_error'] = 'Dates invalides (format AAAA-MM-JJ).';
        header('Location: index.php?action=admin&subaction=subscription_add');
        exit();
    }

    $catalogPlanId = (int) ($_POST['catalog_plan_id'] ?? 0);
    $planIdForRow = null;
    $fields = null;

    if (subscriptionPlansTablesExist($pdo) && $catalogPlanId > 0) {
        $p = getSubscriptionPlanById($pdo, $catalogPlanId);
        if ($p) {
            $fields = [
                'nom' => $p['name'],
                'prix' => (int) $p['prix'],
                'descreption' => mb_substr($p['description'], 0, 500)
            ];
            $planIdForRow = $catalogPlanId;
        }
    }
    if (!$fields) {
        $fields = adminBuildSubscriptionPlanFields($_POST);
    }
    if (!$fields) {
        $_SESSION['admin_sub_error'] = 'Choisissez un type du catalogue ou renseignez un plan personnalisé (nom, prix).';
        header('Location: index.php?action=admin&subaction=subscription_add');
        exit();
    }

    $ok = createAbonnementAdmin($pdo, [
        'id_user' => $idUser,
        'plan_id' => $planIdForRow,
        'nom' => $fields['nom'],
        'descreption' => $fields['descreption'],
        'prix' => $fields['prix'],
        'date_debut' => $dateDebut,
        'date_fin' => $dateFin,
        'card_holder' => null,
        'payment_last4' => null
    ]);

    if ($ok) {
        $_SESSION['admin_sub_success'] = 'Abonnement créé.';
    } else {
        $_SESSION['admin_sub_error'] = 'Impossible de créer l\'abonnement.';
    }

    header('Location: index.php?action=admin&subaction=subscriptions');
    exit();
}

function adminSubscriptionEditGet($id) {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }

    global $pdo;

    require_once __DIR__ . '/../models/userModel.php';
    require_once __DIR__ . '/../models/subscriptionModel.php';

    if ((int) $id < 1) {
        $_SESSION['admin_sub_error'] = 'Identifiant d\'abonnement manquant ou invalide.';
        header('Location: index.php?action=admin&subaction=subscriptions');
        exit();
    }

    $abonnement = getAbonnementByIdForAdmin($pdo, $id);
    if (!$abonnement) {
        header('Location: index.php?action=admin&subaction=subscriptions');
        exit();
    }

    $users = getAllUsers($pdo);
    $catalogPlansForAssign = subscriptionPlansTablesExist($pdo) ? getAllSubscriptionPlansCatalog($pdo) : [];

    require_once __DIR__ . '/../views/admin/subscription_form.php';
}

function adminSubscriptionEditPost($id) {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }

    global $pdo;

    require_once __DIR__ . '/../models/userModel.php';
    require_once __DIR__ . '/../models/subscriptionModel.php';

    if ((int) $id < 1) {
        $_SESSION['admin_sub_error'] = 'Identifiant d\'abonnement manquant ou invalide.';
        header('Location: index.php?action=admin&subaction=subscriptions');
        exit();
    }

    $existing = getAbonnementByIdForAdmin($pdo, $id);
    if (!$existing) {
        $_SESSION['admin_sub_error'] = 'Abonnement introuvable.';
        header('Location: index.php?action=admin&subaction=subscriptions');
        exit();
    }

    $idUser = (int) ($_POST['id_user'] ?? 0);
    $dateDebut = trim($_POST['date_debut'] ?? '');
    $dateFin = trim($_POST['date_fin'] ?? '');

    if ($idUser < 1 || !getUserById($pdo, $idUser)) {
        $_SESSION['admin_sub_error'] = 'Membre invalide.';
        header('Location: index.php?action=admin&subaction=subscription_edit&id=' . $id);
        exit();
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateDebut) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFin)) {
        $_SESSION['admin_sub_error'] = 'Dates invalides.';
        header('Location: index.php?action=admin&subaction=subscription_edit&id=' . $id);
        exit();
    }

    $catalogPlanId = (int) ($_POST['catalog_plan_id'] ?? 0);
    $planIdForRow = null;
    $fields = null;

    if (subscriptionPlansTablesExist($pdo) && $catalogPlanId > 0) {
        $p = getSubscriptionPlanById($pdo, $catalogPlanId);
        if ($p) {
            $fields = [
                'nom' => $p['name'],
                'prix' => (int) $p['prix'],
                'descreption' => mb_substr($p['description'], 0, 500)
            ];
            $planIdForRow = $catalogPlanId;
        }
    }
    if (!$fields) {
        $fields = adminBuildSubscriptionPlanFields($_POST);
    }
    if (!$fields) {
        $_SESSION['admin_sub_error'] = 'Choisissez un type du catalogue ou un plan personnalisé (nom, prix).';
        header('Location: index.php?action=admin&subaction=subscription_edit&id=' . $id);
        exit();
    }

    $holder = trim($_POST['card_holder'] ?? '');
    $last4 = preg_replace('/\D/', '', $_POST['payment_last4'] ?? '');
    if ($last4 !== '' && strlen($last4) > 4) {
        $last4 = substr($last4, -4);
    }

    $ok = updateAbonnementAdmin($pdo, $id, [
        'id_user' => $idUser,
        'plan_id' => $planIdForRow,
        'nom' => $fields['nom'],
        'descreption' => $fields['descreption'],
        'prix' => $fields['prix'],
        'date_debut' => $dateDebut,
        'date_fin' => $dateFin,
        'card_holder' => $holder !== '' ? mb_substr($holder, 0, 120) : ($existing['card_holder'] ?? null),
        'payment_last4' => strlen($last4) === 4 ? $last4 : ($existing['payment_last4'] ?? null)
    ]);

    if ($ok) {
        $_SESSION['admin_sub_success'] = 'Abonnement modifié.';
    } else {
        $_SESSION['admin_sub_error'] = 'Impossible de modifier.';
    }

    header('Location: index.php?action=admin&subaction=subscriptions');
    exit();
}

function adminSubscriptionDelete($id) {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }

    global $pdo;

    require_once __DIR__ . '/../models/subscriptionModel.php';

    if ((int) $id < 1) {
        $_SESSION['admin_sub_error'] = 'Identifiant d\'abonnement manquant ou invalide.';
    } elseif (deleteAbonnementAdmin($pdo, $id)) {
        $_SESSION['admin_sub_success'] = 'Abonnement supprimé.';
    } else {
        $_SESSION['admin_sub_error'] = 'Suppression impossible (abonnement introuvable ou contrainte base de données).';
    }

    header('Location: index.php?action=admin&subaction=subscriptions');
    exit();
}

function adminSubscriptionPlanAddGet() {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }

    global $pdo;

    require_once __DIR__ . '/../models/resourceModel.php';
    require_once __DIR__ . '/../models/subscriptionModel.php';

    if (!subscriptionPlansTablesExist($pdo)) {
        $_SESSION['admin_sub_error'] = 'Importez ou mettez à jour la base avec le fichier studehub.sql (tables catalogue).';
        header('Location: index.php?action=admin&subaction=subscriptions');
        exit();
    }

    $plan = null;
    $selectedIds = [];
    $allResources = getAllResources($pdo);

    require_once __DIR__ . '/../views/admin/subscription_plan_form.php';
}

function adminSubscriptionPlanAddPost() {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }

    global $pdo;

    require_once __DIR__ . '/../models/subscriptionModel.php';

    if (!subscriptionPlansTablesExist($pdo)) {
        $_SESSION['admin_sub_error'] = 'Tables catalogue absentes. Réimportez studehub.sql ou créez subscription_plans.';
        header('Location: index.php?action=admin&subaction=subscriptions');
        exit();
    }

    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $prix = (int) ($_POST['prix'] ?? 0);
    $resources = isset($_POST['resources']) && is_array($_POST['resources'])
        ? array_values(array_filter(array_map('intval', $_POST['resources'])))
        : [];
    $saveAction = $_POST['save_action'] ?? 'draft';

    if ($name === '') {
        $_SESSION['admin_sub_error'] = 'Le nom du type d\'abonnement est obligatoire.';
        header('Location: index.php?action=admin&subaction=subscription_plan_add');
        exit();
    }

    if ($saveAction === 'publish' && count($resources) < 1) {
        $_SESSION['admin_sub_error'] = 'Ajoutez au moins une ressource avant de publier.';
        header('Location: index.php?action=admin&subaction=subscription_plan_add');
        exit();
    }

    $published = ($saveAction === 'publish') ? 1 : 0;
    $newId = createSubscriptionPlan($pdo, $name, $description, $prix, $published);
    saveSubscriptionPlanResources($pdo, $newId, $resources);

    $_SESSION['admin_sub_success'] = $published
        ? 'Type d\'abonnement publié : les étudiants peuvent le choisir sur la page Abonnements.'
        : 'Brouillon enregistré. Vous pouvez cocher des ressources puis cliquer sur Publier.';

    header('Location: index.php?action=admin&subaction=subscription_plan_edit&id=' . $newId);
    exit();
}

function adminSubscriptionPlanEditGet($id) {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }

    global $pdo;

    require_once __DIR__ . '/../models/resourceModel.php';
    require_once __DIR__ . '/../models/subscriptionModel.php';

    $id = (int) $id;
    if ($id < 1 || !subscriptionPlansTablesExist($pdo)) {
        header('Location: index.php?action=admin&subaction=subscriptions');
        exit();
    }

    $plan = getSubscriptionPlanById($pdo, $id);
    if (!$plan) {
        header('Location: index.php?action=admin&subaction=subscriptions');
        exit();
    }

    $selectedIds = getResourceIdsForPlan($pdo, $id);
    $allResources = getAllResources($pdo);

    require_once __DIR__ . '/../views/admin/subscription_plan_form.php';
}

function adminSubscriptionPlanEditPost($id) {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }

    global $pdo;

    require_once __DIR__ . '/../models/subscriptionModel.php';

    $id = (int) $id;
    if ($id < 1 || !subscriptionPlansTablesExist($pdo)) {
        header('Location: index.php?action=admin&subaction=subscriptions');
        exit();
    }

    $plan = getSubscriptionPlanById($pdo, $id);
    if (!$plan) {
        $_SESSION['admin_sub_error'] = 'Type introuvable.';
        header('Location: index.php?action=admin&subaction=subscriptions');
        exit();
    }

    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $prix = (int) ($_POST['prix'] ?? 0);
    $resources = isset($_POST['resources']) && is_array($_POST['resources'])
        ? array_values(array_filter(array_map('intval', $_POST['resources'])))
        : [];
    $saveAction = $_POST['save_action'] ?? 'draft';

    if ($name === '') {
        $_SESSION['admin_sub_error'] = 'Le nom est obligatoire.';
        header('Location: index.php?action=admin&subaction=subscription_plan_edit&id=' . $id);
        exit();
    }

    if ($saveAction === 'publish' && count($resources) < 1) {
        $_SESSION['admin_sub_error'] = 'Ajoutez au moins une ressource pour publier.';
        header('Location: index.php?action=admin&subaction=subscription_plan_edit&id=' . $id);
        exit();
    }

    if ($saveAction === 'publish') {
        $published = 1;
    } elseif ($saveAction === 'unpublish') {
        $published = 0;
    } elseif ($saveAction === 'draft') {
        $published = 0;
    } else {
        $published = (int) ($plan['published'] ?? 0);
    }

    updateSubscriptionPlan($pdo, $id, $name, $description, $prix, $published);
    saveSubscriptionPlanResources($pdo, $id, $resources);

    if ($published) {
        $_SESSION['admin_sub_success'] = 'Type mis à jour et publié.';
    } elseif ($saveAction === 'unpublish') {
        $_SESSION['admin_sub_success'] = 'Type dépublié (plus visible pour les étudiants).';
    } elseif ($saveAction === 'draft') {
        $_SESSION['admin_sub_success'] = 'Type enregistré en brouillon.';
    } else {
        $_SESSION['admin_sub_success'] = 'Modifications enregistrées.';
    }

    header('Location: index.php?action=admin&subaction=subscription_plan_edit&id=' . $id);
    exit();
}

function adminSubscriptionPlanDelete($id) {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }

    global $pdo;

    require_once __DIR__ . '/../models/subscriptionModel.php';

    $id = (int) $id;
    if ($id > 0 && deleteSubscriptionPlan($pdo, $id)) {
        $_SESSION['admin_sub_success'] = 'Type d\'abonnement supprimé.';
    } else {
        $_SESSION['admin_sub_error'] = 'Suppression impossible.';
    }

    header('Location: index.php?action=admin&subaction=subscriptions');
    exit();
}

function adminUsers() {
    if (!isAdmin()) {
        adminLoginGet();
        return;
    }
    
    global $pdo;
    
    $search = $_GET['search'] ?? '';
    
    require_once __DIR__ . '/../models/userModel.php';
    require_once __DIR__ . '/../models/subscriptionModel.php';
    $users = getAllUsers($pdo, $search);

    foreach ($users as &$u) {
        $sub = getActiveSubscriptionByUser($pdo, (int) $u['id']);
        if ($sub) {
            $u['subscription_nom'] = $sub['nom'];
            $u['subscription_fin'] = $sub['date_fin'];
        } else {
            $u['subscription_nom'] = '';
            $u['subscription_fin'] = '';
        }
    }
    unset($u);
    
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