<?php
// views/admin/view_user.php — Inspection d'un utilisateur (lecture seule)
$avatarName = rawurlencode(trim(($user['prenom'] ?? 'U') . ' ' . ($user['nom'] ?? '')));
$photoUrl = !empty($user['photo']) ? $user['photo'] : ('https://ui-avatars.com/api/?size=128&background=1a8cff&color=fff&name=' . $avatarName);
$roleLabel = ((int)($user['role'] ?? 1) === 0) ? 'Administrateur' : 'Utilisateur';
$totalRes = (int)($stats['total_resources'] ?? 0);
$totalDl = (int)($stats['total_downloads'] ?? 0);
$avgRt = isset($stats['avg_rating']) && $stats['avg_rating'] !== null ? round((float)$stats['avg_rating'], 1) : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil utilisateur | StudyHub Admin</title>
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
        .main-content { margin-left:280px; padding:30px; min-height:100vh; background:#f1f5f9; }
        .top-bar { background:rgba(255,255,255,0.95); border-radius:20px; padding:15px 25px; margin-bottom:24px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; }
        .page-title h1 { font-size:24px; font-weight:700; color:#0a5c8e; margin:0; }
        .page-title p { color:#5a8faa; margin:0; font-size:14px; }
        .content-card { background:white; border-radius:20px; padding:28px; box-shadow:0 4px 15px rgba(0,0,0,0.05); margin-bottom:20px; }
        .profile-avatar { width:120px; height:120px; border-radius:50%; object-fit:cover; border:4px solid #e0e7ff; }
        .kv { display:grid; grid-template-columns:160px 1fr; gap:8px 16px; font-size:14px; }
        .kv dt { color:#64748b; font-weight:500; margin:0; }
        .kv dd { margin:0; color:#0f172a; }
        .stat-pill { background:#f8fafc; border-radius:12px; padding:14px 18px; text-align:center; border:1px solid #e2e8f0; }
        .stat-pill strong { display:block; font-size:1.35rem; color:#0a5c8e; }
        .btn-blue { background:linear-gradient(135deg,#1a8cff 0%,#00b4d8 100%); border:none; padding:10px 20px; border-radius:12px; font-weight:600; color:white; text-decoration:none; display:inline-flex; align-items:center; gap:8px; }
        .btn-blue:hover { color:white; }
        .btn-outline-admin { border:2px solid #e2e8f0; color:#334155; padding:10px 18px; border-radius:12px; font-weight:600; text-decoration:none; }
        .btn-outline-admin:hover { border-color:#1a8cff; color:#1a8cff; }
        @media (max-width:768px) { .sidebar { width:80px; } .sidebar .logo-text, .sidebar .nav-link span { display:none; } .main-content { margin-left:80px; } .kv { grid-template-columns:1fr; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header"><div class="logo"><img src="uploads/logo.png" alt="StudyHub" class="admin-sidebar-logo"><div class="logo-text"><p>Admin Dashboard</p></div></div></div>
        <div class="nav-menu">
            <div class="nav-item"><a href="index.php?action=admin&subaction=dashboard" class="nav-link"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=users" class="nav-link active"><i class="bi bi-people-fill"></i><span>Utilisateurs</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=resources" class="nav-link"><i class="bi bi-folder-fill"></i><span>Ressources</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=comments" class="nav-link"><i class="bi bi-chat-dots-fill"></i><span>Commentaires</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=profile" class="nav-link"><i class="bi bi-person-fill"></i><span>Mon profil</span></a></div>
            <div class="nav-item logout-link"><a href="index.php?action=logout" class="nav-link"><i class="bi bi-box-arrow-right"></i><span>Déconnexion</span></a></div>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <div class="page-title">
                <h1><i class="bi bi-eye-fill me-2"></i>Profil utilisateur</h1>
                <p>ID #<?= (int)$user['id'] ?> — <?= escape(trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? ''))) ?></p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="index.php?action=admin&subaction=users" class="btn-outline-admin">← Liste des utilisateurs</a>
                <a href="index.php?action=admin&subaction=edit_user&id=<?= (int)$user['id'] ?>" class="btn-blue"><i class="bi bi-pencil-fill"></i> Modifier</a>
                <a href="index.php?action=profile&subaction=view&id=<?= (int)$user['id'] ?>" class="btn-blue" style="background:linear-gradient(135deg,#0f766e,#14b8a6);" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> Profil public</a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-5">
                <div class="content-card text-center text-lg-start">
                    <div class="d-flex flex-column align-items-center align-items-lg-start flex-lg-row gap-4">
                        <img src="<?= escape($photoUrl) ?>" alt="" class="profile-avatar">
                        <div>
                            <h2 class="h4 mb-1"><?= escape(trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? ''))) ?></h2>
                            <p class="text-muted mb-2"><i class="bi bi-envelope me-1"></i><?= escape($user['email'] ?? '') ?></p>
                            <span class="badge bg-secondary"><?= escape($roleLabel) ?></span>
                        </div>
                    </div>
                    <?php if (!empty($user['bio'])): ?>
                        <hr>
                        <h3 class="h6 text-muted">Bio</h3>
                        <p class="mb-0" style="white-space:pre-wrap;"><?= escape($user['bio']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="content-card">
                    <h3 class="h6 text-uppercase text-muted mb-3">Informations</h3>
                    <dl class="kv">
                        <dt>Téléphone</dt><dd><?= escape($user['tel'] ?? '—') ?></dd>
                        <dt>Université</dt><dd><?= escape($user['universite'] ?? '') ?: '—' ?></dd>
                        <dt>Filière</dt><dd><?= escape($user['filiere'] ?? '') ?: '—' ?></dd>
                        <dt>Score</dt><dd><?= isset($user['score']) ? (int)$user['score'] : '—' ?></dd>
                    </dl>
                </div>
                <div class="content-card">
                    <h3 class="h6 text-uppercase text-muted mb-3">Activité sur la plateforme</h3>
                    <div class="row g-3">
                        <div class="col-md-4"><div class="stat-pill"><strong><?= $totalRes ?></strong><small class="text-muted">Ressources publiées</small></div></div>
                        <div class="col-md-4"><div class="stat-pill"><strong><?= number_format($totalDl) ?></strong><small class="text-muted">Téléchargements (cumul)</small></div></div>
                        <div class="col-md-4"><div class="stat-pill"><strong><?= $totalRes > 0 ? $avgRt . '/5' : '—' ?></strong><small class="text-muted">Note moy. (ressources)</small></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
