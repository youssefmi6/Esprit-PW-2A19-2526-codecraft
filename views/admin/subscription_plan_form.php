<?php
$isEdit = !empty($plan);
$formAction = $isEdit
    ? 'index.php?action=admin&subaction=subscription_plan_edit&id=' . (int) $plan['id']
    : 'index.php?action=admin&subaction=subscription_plan_add';
$nameVal = $plan['name'] ?? '';
$descVal = $plan['description'] ?? '';
$prixVal = isset($plan['prix']) ? (int) $plan['prix'] : 0;
$pubVal = !empty($plan['published']);
$selectedIds = isset($selectedIds) ? array_map('intval', (array) $selectedIds) : [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $isEdit ? 'Modifier le type' : 'Nouveau type' ?> d'abonnement | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .sidebar { position:fixed; left:0; top:0; width:280px; height:100%; background:linear-gradient(180deg,#0a5c8e 0%,#1a8cff 100%); z-index:1000; }
        .sidebar-header { padding:30px 25px; border-bottom:1px solid rgba(255,255,255,0.2); }
        .logo { display:flex; align-items:center; gap:12px; }
        .logo-icon { width:45px; height:45px; background:rgba(255,255,255,0.2); border-radius:12px; display:flex; align-items:center; justify-content:center; }
        .logo-icon i { font-size:24px; color:white; }
        .logo-text h3 { color:white; font-weight:700; font-size:20px; margin:0; }
        .logo-text p { color:rgba(255,255,255,0.8); font-size:11px; margin:0; }
        .nav-menu { padding:25px; }
        .nav-item { margin-bottom:8px; }
        .nav-link { display:flex; align-items:center; gap:12px; padding:12px 16px; border-radius:12px; color:rgba(255,255,255,0.85); text-decoration:none; font-weight:500; }
        .nav-link i { font-size:20px; width:24px; }
        .nav-link:hover { background:rgba(255,255,255,0.2); color:white; transform:translateX(5px); }
        .nav-link.active { background:rgba(255,255,255,0.25); color:white; }
        .logout-link { margin-top:40px; border-top:1px solid rgba(255,255,255,0.2); padding-top:20px; }
        .main-content { margin-left:280px; padding:30px; min-height:100vh; }
        .content-card { background:white; border-radius:24px; padding:28px; max-width:900px; box-shadow:0 4px 15px rgba(0,0,0,0.05); }
        .btn-blue { background:linear-gradient(135deg,#1a8cff 0%,#00b4d8 100%); border:none; padding:10px 22px; border-radius:12px; font-weight:600; color:white; }
        .res-grid { max-height:320px; overflow-y:auto; border:1px solid #e2e8f0; border-radius:12px; padding:12px; }
        @media (max-width:768px) { .sidebar { width:80px; } .sidebar .logo-text, .sidebar .nav-link span { display:none; } .main-content { margin-left:80px; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header"><div class="logo"><div class="logo-icon"><i class="bi bi-mortarboard-fill"></i></div><div class="logo-text"><h3>StudyHub</h3><p>Admin Dashboard</p></div></div></div>
        <div class="nav-menu">
            <div class="nav-item"><a href="index.php?action=admin&subaction=dashboard" class="nav-link"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=subscriptions" class="nav-link active"><i class="bi bi-star-fill"></i><span>Abonnements</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=users" class="nav-link"><i class="bi bi-people-fill"></i><span>Utilisateurs</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=resources" class="nav-link"><i class="bi bi-folder-fill"></i><span>Ressources</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=comments" class="nav-link"><i class="bi bi-chat-dots-fill"></i><span>Commentaires</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=profile" class="nav-link"><i class="bi bi-person-fill"></i><span>Mon profil</span></a></div>
            <div class="nav-item logout-link"><a href="index.php?action=logout" class="nav-link"><i class="bi bi-box-arrow-right"></i><span>Déconnexion</span></a></div>
        </div>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h4 fw-bold text-primary mb-1"><?= $isEdit ? 'Modifier le type d\'abonnement' : 'Nouveau type d\'abonnement' ?></h1>
                <p class="text-muted small mb-0">1) Informations — 2) Cochez les ressources — 3) Publier pour l'afficher aux étudiants</p>
            </div>
            <a href="index.php?action=admin&subaction=subscriptions" class="btn btn-outline-secondary">← Retour</a>
        </div>

        <div class="content-card">
            <?php if (!empty($_SESSION['admin_sub_error'])): ?>
                <div class="alert alert-danger"><?= escape($_SESSION['admin_sub_error']); unset($_SESSION['admin_sub_error']); ?></div>
            <?php endif; ?>

            <form method="post" action="<?= escape($formAction) ?>">
                <div class="mb-3">
                    <label class="form-label">Nom du type <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required maxlength="100" value="<?= escape($nameVal) ?>" placeholder="Ex: Pack Révision Bac">
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3" maxlength="500" placeholder="Texte visible sur la page abonnements"><?= escape($descVal) ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Prix (DT)</label>
                    <input type="number" name="prix" class="form-control" min="0" value="<?= (int) $prixVal ?>">
                </div>

                <?php if ($isEdit): ?>
                    <p class="small mb-2">Statut actuel : <?= $pubVal ? '<span class="badge bg-success">Publié</span>' : '<span class="badge bg-secondary">Brouillon</span>' ?></p>
                <?php endif; ?>

                <hr>
                <h6 class="mb-2">Ressources incluses dans ce type</h6>
                <p class="text-muted small">Cochez les ressources accessibles aux abonnés de ce type.</p>
                <div class="res-grid mb-3">
                    <?php foreach ($allResources as $res): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="resources[]" value="<?= (int) $res['id_res'] ?>"
                                   id="res<?= (int) $res['id_res'] ?>" <?= in_array((int) $res['id_res'], $selectedIds, true) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="res<?= (int) $res['id_res'] ?>">
                                <?= escape($res['titre']) ?> <small class="text-muted">(<?= escape($res['matiere'] ?? '') ?>)</small>
                            </label>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($allResources)): ?>
                        <p class="text-muted mb-0">Aucune ressource sur la plateforme.</p>
                    <?php endif; ?>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <?php if ($isEdit): ?>
                        <button type="submit" name="save_action" value="keep" class="btn btn-primary">Enregistrer</button>
                        <button type="submit" name="save_action" value="publish" class="btn btn-success"><i class="bi bi-cloud-upload me-1"></i>Publier</button>
                        <button type="submit" name="save_action" value="draft" class="btn btn-outline-secondary">Mettre en brouillon</button>
                        <button type="submit" name="save_action" value="unpublish" class="btn btn-outline-warning">Dépublier</button>
                    <?php else: ?>
                        <button type="submit" name="save_action" value="draft" class="btn btn-outline-secondary">Enregistrer brouillon</button>
                        <button type="submit" name="save_action" value="publish" class="btn btn-success"><i class="bi bi-cloud-upload me-1"></i>Publier</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
