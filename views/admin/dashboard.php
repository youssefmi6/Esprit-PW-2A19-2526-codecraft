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
    <link rel="stylesheet" href="css/style.css">
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
        .stats-summary-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:20px; margin-bottom:24px; }
        .summary-item { background:#f7fbff; border:1px solid #e1efff; border-radius:16px; padding:18px; }
        .summary-item h6 { color:#0a5c8e; font-weight:700; margin-bottom:12px; }
        .summary-item ul { list-style:none; margin:0; padding:0; }
        .summary-item li { display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px dashed #d8e7fb; font-size:14px; }
        .summary-item li:last-child { border-bottom:none; }
        .charts-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(320px,1fr)); gap:20px; margin-bottom:24px; }
        .chart-card { background:#fff; border-radius:18px; box-shadow:0 6px 18px rgba(0,0,0,0.05); padding:18px; }
        .chart-card h6 { color:#0a5c8e; font-weight:700; margin-bottom:14px; }
        .chart-canvas-wrap { position:relative; height:260px; }
        .chart-canvas-wrap.tall { height:300px; }
        .chart-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; }
        .chart-header h5 { font-weight:700; color:#0a5c8e; margin:0; font-size:16px; }
        .btn-sm-custom { background:linear-gradient(135deg,#1a8cff 0%,#00b4d8 100%); border:none; padding:5px 12px; border-radius:8px; color:white; text-decoration:none; font-size:12px; }
        @media (max-width:768px) { .sidebar { width:80px; } .sidebar .logo-text, .sidebar .nav-link span { display:none; } .main-content { margin-left:80px; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header"><div class="logo"><img src="uploads/logo.png" alt="StudyHub" class="admin-sidebar-logo"><div class="logo-text"><p>Admin Dashboard</p></div></div></div>
        <div class="nav-menu">
            <div class="nav-item"><a href="index.php?action=admin&subaction=dashboard" class="nav-link active"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a></div>
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

        <div class="stats-summary-grid">
            <div class="summary-item">
                <h6><i class="bi bi-people-fill me-2"></i>Statistiques utilisateurs</h6>
                <ul>
                    <li><span>Total comptes</span><strong><?= number_format($stats['total_users']) ?></strong></li>
                    <li><span>Actifs</span><strong><?= number_format($stats['total_active_users']) ?></strong></li>
                    <li><span>Inactifs</span><strong><?= number_format($stats['total_inactive_users']) ?></strong></li>
                    <li><span>Administrateurs</span><strong><?= number_format($stats['total_admins']) ?></strong></li>
                    <li><span>Utilisateurs standard</span><strong><?= number_format($stats['total_regular_users']) ?></strong></li>
                </ul>
            </div>
            <div class="summary-item">
                <h6><i class="bi bi-folder-fill me-2"></i>Statistiques ressources</h6>
                <ul>
                    <li><span>Total ressources</span><strong><?= number_format($stats['total_resources']) ?></strong></li>
                    <li><span>Pages cumulées</span><strong><?= number_format($stats['total_pages']) ?></strong></li>
                    <li><span>Téléchargements</span><strong><?= number_format($stats['total_downloads']) ?></strong></li>
                    <li><span>Matières</span><strong><?= number_format($stats['total_matieres']) ?></strong></li>
                    <li><span>Note moyenne</span><strong><?= number_format((float)$stats['avg_resource_rating'], 2) ?>/5</strong></li>
                </ul>
            </div>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <h6><i class="bi bi-pie-chart-fill me-2"></i>Répartition des utilisateurs</h6>
                <div class="chart-canvas-wrap">
                    <canvas id="usersDonutChart"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <h6><i class="bi bi-pie-chart-fill me-2"></i>Répartition des ressources</h6>
                <div class="chart-canvas-wrap">
                    <canvas id="resourcesDonutChart"></canvas>
                </div>
            </div>
        </div>
        <div class="chart-card mb-4">
            <h6><i class="bi bi-bar-chart-fill me-2"></i>Ressources par matière</h6>
            <div class="chart-canvas-wrap tall">
                <canvas id="resourcesByMatiereBarChart"></canvas>
            </div>
        </div>

        <div class="row"><div class="col-md-6"><div class="content-card"><div class="chart-header"><h5><i class="bi bi-people-fill me-2"></i>Derniers utilisateurs</h5><a href="index.php?action=admin&subaction=users" class="btn-sm-custom">Voir tout</a></div><div class="table-responsive"><table class="table table-sm table-hover"><thead><tr><th>Nom</th><th>Email</th><th>Rôle</th></tr></thead><tbody><?php foreach($recentUsers as $user): ?><tr><td><?= escape($user['prenom'] . ' ' . $user['nom']) ?></td><td><?= escape($user['email']) ?></td><td><span class="badge bg-secondary"><?= $user['role'] == 0 ? 'Admin' : 'User' ?></span></td></tr><?php endforeach; ?></tbody></table></div></div></div>
        <div class="col-md-6"><div class="content-card"><div class="chart-header"><h5><i class="bi bi-trophy-fill me-2"></i>Top ressources</h5><a href="index.php?action=admin&subaction=resources" class="btn-sm-custom">Voir tout</a></div><div class="table-responsive"><table class="table table-sm table-hover"><thead><tr><th>Titre</th><th>Downloads</th><th>Note</th></tr></thead><tbody><?php foreach($topResources as $r): ?><tr><td><?= escape(substr($r['titre'], 0, 30)) ?></td><td><i class="bi bi-download me-1"></i> <?= $r['downloads'] ?></td><td><?php if($r['note_moyenne'] > 0): ?><i class="bi bi-star-fill text-warning"></i> <?= $r['note_moyenne'] ?><?php else: ?>-<?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div></div></div></div>

        <div class="content-card"><div class="chart-header"><h5><i class="bi bi-clock-history me-2"></i>Dernières ressources</h5><a href="index.php?action=admin&subaction=resources" class="btn-sm-custom">Voir tout</a></div><div class="table-responsive"><table class="table table-sm table-hover"><thead><tr><th>Titre</th><th>Type</th><th>Auteur</th><th>Downloads</th><th>Note</th></tr></thead><tbody><?php foreach($recentResources as $r): ?><tr><td><?= escape(substr($r['titre'], 0, 35)) ?></td><td><span class="badge bg-info"><?= $r['type'] ?></span></td><td><?= escape($r['prenom'] . ' ' . $r['nom']) ?></td><td><?= $r['downloads'] ?></td><td><?php if($r['note_moyenne'] > 0): ?><i class="bi bi-star-fill text-warning"></i> <?= $r['note_moyenne'] ?><?php else: ?>-<?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div></div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const usersCtx = document.getElementById('usersDonutChart');
            const resourcesCtx = document.getElementById('resourcesDonutChart');
            const resourcesBarCtx = document.getElementById('resourcesByMatiereBarChart');
            if (!usersCtx || !resourcesCtx || !resourcesBarCtx || typeof Chart === 'undefined') return;

            new Chart(usersCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Actifs', 'Inactifs', 'Admins'],
                    datasets: [{
                        data: [
                            <?= (int)($stats['total_active_users'] ?? 0) ?>,
                            <?= (int)($stats['total_inactive_users'] ?? 0) ?>,
                            <?= (int)($stats['total_admins'] ?? 0) ?>
                        ],
                        backgroundColor: ['#22c55e', '#ef4444', '#1a8cff'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    cutout: '58%'
                }
            });

            new Chart(resourcesCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Ressources', 'Matières', 'Commentaires'],
                    datasets: [{
                        data: [
                            <?= (int)($stats['total_resources'] ?? 0) ?>,
                            <?= (int)($stats['total_matieres'] ?? 0) ?>,
                            <?= (int)($stats['total_comments'] ?? 0) ?>
                        ],
                        backgroundColor: ['#0ea5e9', '#8b5cf6', '#f59e0b'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    cutout: '58%'
                }
            });

            const matiereLabels = <?= json_encode(array_map(static function ($row) { return (string)($row['matiere'] ?? 'Autre'); }, $resourcesByMatiere ?? []), JSON_UNESCAPED_UNICODE) ?>;
            const matiereCounts = <?= json_encode(array_map(static function ($row) { return (int)($row['count'] ?? 0); }, $resourcesByMatiere ?? [])) ?>;
            const barColors = matiereLabels.map((_, idx) => {
                const palette = ['#1a8cff', '#06b6d4', '#8b5cf6', '#f59e0b', '#22c55e', '#ef4444', '#ec4899', '#14b8a6'];
                return palette[idx % palette.length];
            });

            new Chart(resourcesBarCtx, {
                type: 'bar',
                data: {
                    labels: matiereLabels,
                    datasets: [{
                        label: 'Nombre de ressources',
                        data: matiereCounts,
                        backgroundColor: barColors,
                        borderRadius: 8,
                        maxBarThickness: 48
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        })();
    </script>
</body>
</html>