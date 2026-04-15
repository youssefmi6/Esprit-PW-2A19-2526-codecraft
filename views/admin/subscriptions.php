<?php
// views/admin/subscriptions.php — Gestion des abonnements (CRUD)
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Abonnements | StudyHub Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
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
        .nav-link { display:flex; align-items:center; gap:12px; padding:12px 16px; border-radius:12px; color:rgba(255,255,255,0.85); text-decoration:none; transition:all 0.3s; font-weight:500; }
        .nav-link i { font-size:20px; width:24px; }
        .nav-link:hover { background:rgba(255,255,255,0.2); color:white; transform:translateX(5px); }
        .nav-link.active { background:rgba(255,255,255,0.25); color:white; }
        .logout-link { margin-top:40px; border-top:1px solid rgba(255,255,255,0.2); padding-top:20px; }
        .main-content { margin-left:280px; padding:30px; min-height:100vh; }
        .top-bar { background:rgba(255,255,255,0.95); border-radius:20px; padding:15px 25px; margin-bottom:30px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; }
        .page-title h1 { font-size:24px; font-weight:700; color:#0a5c8e; margin:0; }
        .page-title p { color:#5a8faa; margin:0; font-size:14px; }
        .content-card { background:white; border-radius:24px; padding:25px; box-shadow:0 4px 15px rgba(0,0,0,0.05); margin-bottom:20px; }
        .search-box { position:relative; margin-bottom:20px; }
        .search-box i { position:absolute; left:16px; top:50%; transform:translateY(-50%); color:#1a8cff; }
        .search-box input { padding:12px 16px 12px 45px; border:2px solid #e0e7ff; border-radius:16px; width:100%; max-width:400px; }
        .btn-blue { background:linear-gradient(135deg,#1a8cff 0%,#00b4d8 100%); border:none; padding:10px 24px; border-radius:12px; font-weight:600; color:white; text-decoration:none; display:inline-block; }
        .btn-edit { background:none; border:none; color:#1a8cff; font-size:18px; cursor:pointer; padding:5px; text-decoration:none; }
        .btn-delete { background:none; border:none; color:#dc2626; font-size:18px; cursor:pointer; padding:5px; text-decoration:none; }
        .sub-actions { display:flex; flex-wrap:wrap; align-items:center; gap:6px; }
        .sub-actions .btn-action-text { font-size:13px; font-weight:600; text-decoration:none; white-space:nowrap; }
        .sub-actions .btn-action-text.edit { color:#1a8cff; }
        .sub-actions .btn-action-text.del { color:#dc2626; }
        .mini-stat { background:#f8fafc; border-radius:12px; padding:12px 16px; text-align:center; border:1px solid #e2e8f0; }
        .mini-stat strong { display:block; font-size:1.25rem; color:#0a5c8e; }
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
        <div class="top-bar">
            <div class="page-title">
                <h1>Gestion des abonnements</h1>
                <p>Créer, modifier ou supprimer des abonnements et les lier aux membres</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="index.php?action=admin&subaction=dashboard#abonnements" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-bar-chart me-1"></i> Statistiques</a>
                <a href="index.php?action=admin&subaction=subscription_plan_add" class="btn-blue rounded-pill"><i class="bi bi-stars me-1"></i> Nouveau type d'abonnement</a>
                <a href="index.php?action=admin&subaction=subscription_add" class="btn btn-outline-primary rounded-pill"><i class="bi bi-person-plus me-1"></i> Attribuer à un membre</a>
            </div>
        </div>

        <?php if (!empty($_SESSION['admin_sub_success'])): ?>
            <div class="alert alert-success"><?= escape($_SESSION['admin_sub_success']); unset($_SESSION['admin_sub_success']); ?></div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['admin_sub_error'])): ?>
            <div class="alert alert-danger"><?= escape($_SESSION['admin_sub_error']); unset($_SESSION['admin_sub_error']); ?></div>
        <?php endif; ?>

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3"><div class="mini-stat"><small class="text-muted">Abonnés actifs</small><strong><?= number_format($subStats['total_active_subscribers']) ?></strong></div></div>
            <?php foreach ($subStats['by_tier'] as $tierName => $tierCount): ?>
            <div class="col-6 col-md-3"><div class="mini-stat"><small class="text-muted"><?= escape($tierName) ?></small><strong><?= number_format((int) $tierCount) ?></strong></div></div>
            <?php endforeach; ?>
        </div>

        <div class="content-card mb-4">
            <h5 class="mb-3"><i class="bi bi-collection me-2 text-primary"></i>Types d'abonnement (catalogue)</h5>
            <p class="text-muted small">Créez un type, ajoutez des ressources, puis <strong>Publiez</strong> pour qu'il apparaisse sur la page Abonnements des étudiants.</p>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prix</th>
                            <th>Ressources</th>
                            <th>Publication</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($catalogPlans as $cp): ?>
                            <tr>
                                <td><?= (int) $cp['id'] ?></td>
                                <td><strong><?= escape($cp['name']) ?></strong><br><small class="text-muted"><?= escape(mb_substr($cp['description'], 0, 60)) ?><?= mb_strlen($cp['description']) > 60 ? '…' : '' ?></small></td>
                                <td><?= number_format((int) $cp['prix']) ?> DT</td>
                                <td><?= (int) ($cp['resource_count'] ?? 0) ?></td>
                                <td>
                                    <?php if (!empty($cp['published'])): ?>
                                        <span class="badge bg-success">Publié</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Brouillon</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-nowrap">
                                    <div class="sub-actions">
                                        <a href="index.php?action=admin&subaction=subscription_plan_edit&id=<?= (int) $cp['id'] ?>" class="btn-edit" title="Modifier le type et les ressources"><i class="bi bi-pencil-fill"></i></a>
                                        <a href="index.php?action=admin&subaction=subscription_plan_edit&id=<?= (int) $cp['id'] ?>" class="btn-action-text edit">Modifier</a>
                                        <a href="index.php?action=admin&subaction=subscription_plan_delete&id=<?= (int) $cp['id'] ?>" class="btn-delete" title="Supprimer ce type" onclick="return confirm('Supprimer ce type et ses liaisons ressources ?');"><i class="bi bi-trash3-fill"></i></a>
                                        <a href="index.php?action=admin&subaction=subscription_plan_delete&id=<?= (int) $cp['id'] ?>" class="btn-action-text del" onclick="return confirm('Supprimer ce type et ses liaisons ressources ?');">Supprimer</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($catalogPlans)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-3">Aucun type. Cliquez sur « Nouveau type d'abonnement ».</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="content-card">
            <h5 class="mb-3"><i class="bi bi-people me-2 text-primary"></i>Abonnements des membres</h5>
            <div class="search-box">
                <i class="bi bi-search"></i>
                <form method="get" action="index.php" class="d-inline">
                    <input type="hidden" name="action" value="admin">
                    <input type="hidden" name="subaction" value="subscriptions">
                    <input type="text" name="search" class="form-control d-inline-block" placeholder="Rechercher membre, email, plan, id..." value="<?= escape($search) ?>">
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Membre</th>
                            <th>Plan</th>
                            <th>Prix</th>
                            <th>Début</th>
                            <th>Fin</th>
                            <th>Statut</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($abonnements as $a): ?>
                            <?php
                            $actif = ($a['date_fin'] >= date('Y-m-d'));
                            ?>
                            <tr>
                                <td><?= (int) $a['id'] ?></td>
                                <td>
                                    <strong><?= escape($a['user_prenom'] . ' ' . $a['user_nom']) ?></strong><br>
                                    <small class="text-muted"><?= escape($a['user_email']) ?></small>
                                </td>
                                <td><span class="badge bg-primary"><?= escape($a['nom']) ?></span></td>
                                <td><?= number_format((int) $a['prix']) ?> DT</td>
                                <td><?= escape($a['date_debut']) ?></td>
                                <td><?= escape($a['date_fin']) ?></td>
                                <td>
                                    <?php if ($actif): ?>
                                        <span class="badge bg-success">Actif</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Expiré</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-nowrap">
                                    <div class="sub-actions">
                                        <a href="index.php?action=admin&subaction=subscription_edit&id=<?= (int) $a['id'] ?>" class="btn-edit" title="Modifier cet abonnement"><i class="bi bi-pencil-fill"></i></a>
                                        <a href="index.php?action=admin&subaction=subscription_edit&id=<?= (int) $a['id'] ?>" class="btn-action-text edit">Modifier</a>
                                        <a href="index.php?action=admin&subaction=subscription_delete&id=<?= (int) $a['id'] ?>" class="btn-delete" title="Supprimer cet abonnement" onclick="return confirm('Supprimer cet abonnement du membre ?');"><i class="bi bi-trash3-fill"></i></a>
                                        <a href="index.php?action=admin&subaction=subscription_delete&id=<?= (int) $a['id'] ?>" class="btn-action-text del" onclick="return confirm('Supprimer cet abonnement du membre ?');">Supprimer</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($abonnements)): ?>
                            <tr><td colspan="8" class="text-center text-muted py-4">Aucun abonnement. Créez-en un pour un membre.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
