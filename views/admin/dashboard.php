<?php
// views/admin/dashboard.php - Dashboard admin
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | StudyHub Admin</title>
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
        .top-bar { background:rgba(255,255,255,0.95); border-radius:20px; padding:15px 25px; margin-bottom:30px; display:flex; justify-content:space-between; align-items:center; }
        .page-title h1 { font-size:24px; font-weight:700; color:#0a5c8e; margin:0; }
        .page-title p { color:#5a8faa; margin:0; font-size:14px; }
        .stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:20px; margin-bottom:30px; }
        .stat-card { background:white; border-radius:20px; padding:20px; transition:all 0.3s; box-shadow:0 4px 15px rgba(0,0,0,0.05); cursor:pointer; position:relative; overflow:hidden; }
        .stat-card::before { content:''; position:absolute; top:0; left:0; width:4px; height:100%; background:linear-gradient(180deg,#1a8cff 0%,#00b4d8 100%); }
        .stat-card:hover { transform:translateY(-5px); box-shadow:0 20px 40px rgba(26,140,255,0.15); }
        .stat-value { font-size:28px; font-weight:800; color:#0a5c8e; }
        .stat-label { font-size:13px; color:#6c757d; margin-top:5px; }
        .content-card { background:white; border-radius:20px; padding:20px; box-shadow:0 4px 15px rgba(0,0,0,0.05); margin-bottom:25px; }
        .chart-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; }
        .chart-header h5 { font-weight:700; color:#0a5c8e; margin:0; font-size:16px; }
        .btn-sm-custom { background:linear-gradient(135deg,#1a8cff 0%,#00b4d8 100%); border:none; padding:5px 12px; border-radius:8px; color:white; text-decoration:none; font-size:12px; }
        .stat-card-sub { cursor:default; }
        .stat-card-sub::before { background:linear-gradient(180deg,#94a3b8 0%,#64748b 100%); }
        .stat-card-sub.stat-gold::before { background:linear-gradient(180deg,#f59e0b 0%,#d97706 100%); }
        .stat-card-sub.stat-plat::before { background:linear-gradient(180deg,#a78bfa 0%,#7c3aed 100%); }
        .stat-card-sub.stat-all::before { background:linear-gradient(180deg,#1a8cff 0%,#00b4d8 100%); }
        .stat-card-sub.stat-none::before { background:linear-gradient(180deg,#94a3b8 0%,#cbd5e1 100%); }
        @media (max-width:768px) { .sidebar { width:80px; } .sidebar .logo-text, .sidebar .nav-link span { display:none; } .main-content { margin-left:80px; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header"><div class="logo"><div class="logo-icon"><i class="bi bi-mortarboard-fill"></i></div><div class="logo-text"><h3>StudyHub</h3><p>Admin Dashboard</p></div></div></div>
        <div class="nav-menu">
            <div class="nav-item"><a href="index.php?action=admin&subaction=dashboard" class="nav-link active"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=subscriptions" class="nav-link"><i class="bi bi-star-fill"></i><span>Abonnements</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=users" class="nav-link"><i class="bi bi-people-fill"></i><span>Utilisateurs</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=resources" class="nav-link"><i class="bi bi-folder-fill"></i><span>Ressources</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=comments" class="nav-link"><i class="bi bi-chat-dots-fill"></i><span>Commentaires</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=profile" class="nav-link"><i class="bi bi-person-fill"></i><span>Mon profil</span></a></div>
            <div class="nav-item logout-link"><a href="index.php?action=logout" class="nav-link"><i class="bi bi-box-arrow-right"></i><span>Déconnexion</span></a></div>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar"><div class="page-title"><h1>Dashboard</h1><p>Bienvenue <?= escape($_SESSION['admin_prenom'] ?? 'Admin') . ' ' . escape($_SESSION['admin_nom'] ?? ''); ?></p></div><div class="user-info"><a href="index.php?action=admin&subaction=profile" class="text-decoration-none"><div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width:40px; height:40px;"><i class="bi bi-person-fill text-white"></i></div></a></div></div>

        <div class="stats-grid">
            <div class="stat-card" onclick="window.location.href='index.php?action=admin&subaction=users'"><div class="d-flex justify-content-between"><div><div class="stat-value"><?= number_format($stats['total_users']) ?></div><div class="stat-label">Utilisateurs</div></div><i class="bi bi-people-fill fs-1 text-primary opacity-50"></i></div></div>
            <div class="stat-card" onclick="window.location.href='index.php?action=admin&subaction=users'"><div class="d-flex justify-content-between"><div><div class="stat-value"><?= number_format(getAdminCount($pdo)) ?></div><div class="stat-label">Administrateurs</div></div><i class="bi bi-shield-lock-fill fs-1 text-primary opacity-50"></i></div></div>
            <div class="stat-card" onclick="window.location.href='index.php?action=admin&subaction=resources'"><div class="d-flex justify-content-between"><div><div class="stat-value"><?= number_format($stats['total_resources']) ?></div><div class="stat-label">Ressources</div></div><i class="bi bi-folder-fill fs-1 text-primary opacity-50"></i></div></div>
            <div class="stat-card" onclick="window.location.href='index.php?action=admin&subaction=resources'"><div class="d-flex justify-content-between"><div><div class="stat-value"><?= number_format($stats['total_pages']) ?></div><div class="stat-label">Pages totales</div></div><i class="bi bi-file-text-fill fs-1 text-primary opacity-50"></i></div></div>
            <div class="stat-card"><div class="d-flex justify-content-between"><div><div class="stat-value"><?= number_format($stats['total_downloads']) ?></div><div class="stat-label">Téléchargements</div></div><i class="bi bi-download fs-1 text-primary opacity-50"></i></div></div>
            <div class="stat-card" onclick="window.location.href='index.php?action=admin&subaction=comments'"><div class="d-flex justify-content-between"><div><div class="stat-value"><?= number_format($stats['total_comments']) ?></div><div class="stat-label">Commentaires</div></div><i class="bi bi-chat-dots-fill fs-1 text-primary opacity-50"></i></div></div>
        </div>

        <div class="content-card" id="abonnements">
            <div class="chart-header">
                <h5><i class="bi bi-star-fill me-2 text-warning"></i>Catégories d'abonnement</h5>
                <a href="index.php?action=admin&subaction=subscriptions" class="btn-sm-custom">Gérer les abonnements</a>
            </div>
            <p class="text-muted small mb-3">Répartition des abonnements encore valides par nom de plan.</p>
            <div class="stats-grid" style="margin-bottom:20px;">
                <div class="stat-card stat-card-sub stat-all" onclick="window.location.href='index.php?action=admin&subaction=users'">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-value"><?= number_format($stats['subscribers_active']) ?></div>
                            <div class="stat-label">Abonnés actifs</div>
                        </div>
                        <i class="bi bi-patch-check-fill fs-1 text-primary opacity-50"></i>
                    </div>
                </div>
                <div class="stat-card stat-card-sub stat-none" onclick="window.location.href='index.php?action=admin&subaction=users'">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-value"><?= number_format($stats['subscribers_without']) ?></div>
                            <div class="stat-label">Sans abonnement actif</div>
                        </div>
                        <i class="bi bi-person-x fs-1 text-secondary opacity-50"></i>
                    </div>
                </div>
                <?php foreach ($subStats['by_tier'] as $tname => $tcnt): ?>
                <div class="stat-card stat-card-sub">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-value"><?= number_format((int) $tcnt) ?></div>
                            <div class="stat-label"><?= escape($tname) ?></div>
                        </div>
                        <i class="bi bi-tag-fill fs-1 text-primary opacity-50"></i>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php
            $pct = function ($n) use ($subStats) {
                if ($subStats['total_active_subscribers'] < 1) {
                    return '0';
                }
                return number_format(100 * $n / $subStats['total_active_subscribers'], 1);
            };
            ?>
            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle mb-0">
                    <thead class="table-light"><tr><th>Catégorie</th><th class="text-end">Utilisateurs</th><th class="text-end">% des abonnés</th></tr></thead>
                    <tbody>
                        <?php foreach ($subStats['by_tier'] as $tname => $tcnt): ?>
                        <tr><td><span class="badge bg-primary"><?= escape($tname) ?></span></td><td class="text-end"><?= number_format((int) $tcnt) ?></td><td class="text-end"><?= $pct((int) $tcnt) ?> %</td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <h6 class="mt-4 mb-2 text-secondary"><i class="bi bi-pencil-square me-2"></i>Abonnements récents — actions rapides</h6>
            <p class="text-muted small mb-2">Modifier les dates, le membre ou le plan ; supprimer une ligne d’abonnement.</p>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>ID</th><th>Membre</th><th>Plan</th><th>Fin</th><th class="text-end">Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentAbonnements)): ?>
                            <?php foreach ($recentAbonnements as $ra): ?>
                                <tr>
                                    <td><?= (int) $ra['id'] ?></td>
                                    <td><small><?= escape($ra['prenom'] . ' ' . $ra['user_nom']) ?></small><br><small class="text-muted"><?= escape($ra['user_email']) ?></small></td>
                                    <td><span class="badge bg-primary"><?= escape($ra['nom']) ?></span></td>
                                    <td><small><?= escape($ra['date_fin']) ?></small></td>
                                    <td class="text-end text-nowrap">
                                        <a href="index.php?action=admin&subaction=subscription_edit&id=<?= (int) $ra['id'] ?>" class="btn btn-outline-primary btn-sm">Modifier</a>
                                        <a href="index.php?action=admin&subaction=subscription_delete&id=<?= (int) $ra['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Supprimer cet abonnement ?');">Supprimer</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-muted text-center py-3">Aucun abonnement enregistré. <a href="index.php?action=admin&subaction=subscription_add">Attribuer un abonnement</a></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-2"><a href="index.php?action=admin&subaction=subscriptions" class="btn-sm-custom">Toute la gestion des abonnements</a></div>
        </div>

        <div class="row"><div class="col-md-6"><div class="content-card"><div class="chart-header"><h5><i class="bi bi-people-fill me-2"></i>Derniers utilisateurs</h5><a href="index.php?action=admin&subaction=users" class="btn-sm-custom">Voir tout</a></div><div class="table-responsive"><table class="table table-sm table-hover"><thead><tr><th>Nom</th><th>Email</th><th>Rôle</th></tr></thead><tbody><?php foreach($recentUsers as $user): ?><tr><td><?= escape($user['prenom'] . ' ' . $user['nom']) ?></td><td><?= escape($user['email']) ?></td><td><span class="badge bg-secondary"><?= $user['role'] == 0 ? 'Admin' : 'User' ?></span></td></tr><?php endforeach; ?></tbody></table></div></div></div>
        <div class="col-md-6"><div class="content-card"><div class="chart-header"><h5><i class="bi bi-trophy-fill me-2"></i>Top ressources</h5><a href="index.php?action=admin&subaction=resources" class="btn-sm-custom">Voir tout</a></div><div class="table-responsive"><table class="table table-sm table-hover"><thead><tr><th>Titre</th><th>Downloads</th><th>Note</th></tr></thead><tbody><?php foreach($topResources as $r): ?><tr><td><?= escape(substr($r['titre'], 0, 30)) ?></td><td><i class="bi bi-download me-1"></i> <?= $r['downloads'] ?></td><td><?php if($r['note_moyenne'] > 0): ?><i class="bi bi-star-fill text-warning"></i> <?= $r['note_moyenne'] ?><?php else: ?>-<?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div></div></div></div>

        <div class="content-card"><div class="chart-header"><h5><i class="bi bi-clock-history me-2"></i>Dernières ressources</h5><a href="index.php?action=admin&subaction=resources" class="btn-sm-custom">Voir tout</a></div><div class="table-responsive"><table class="table table-sm table-hover"><thead><tr><th>Titre</th><th>Type</th><th>Auteur</th><th>Downloads</th><th>Note</th></tr></thead><tbody><?php foreach($recentResources as $r): ?><tr><td><?= escape(substr($r['titre'], 0, 35)) ?></td><td><span class="badge bg-info"><?= $r['type'] ?></span></td><td><?= escape($r['prenom'] . ' ' . $r['nom']) ?></td><td><?= $r['downloads'] ?></td><td><?php if($r['note_moyenne'] > 0): ?><i class="bi bi-star-fill text-warning"></i> <?= $r['note_moyenne'] ?><?php else: ?>-<?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div></div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>