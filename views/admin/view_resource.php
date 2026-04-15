<?php
// views/admin/view_resource.php — Inspection d'une ressource (lecture seule)
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inspecter la ressource | StudyHub Admin</title>
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
        .inspect-photo { width:100%; max-height:260px; object-fit:cover; border-radius:16px; margin-bottom:20px; }
        .kv { display:grid; grid-template-columns:180px 1fr; gap:8px 16px; font-size:14px; }
        .kv dt { color:#64748b; font-weight:500; margin:0; }
        .kv dd { margin:0; color:#0f172a; }
        .badge-matiere { background:#e0e7ff; color:#1a8cff; padding:4px 10px; border-radius:20px; font-size:12px; }
        .btn-blue { background:linear-gradient(135deg,#1a8cff 0%,#00b4d8 100%); border:none; padding:10px 20px; border-radius:12px; font-weight:600; color:white; text-decoration:none; display:inline-flex; align-items:center; gap:8px; }
        .btn-blue:hover { color:white; transform:translateY(-1px); }
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
            <div class="nav-item"><a href="index.php?action=admin&subaction=users" class="nav-link"><i class="bi bi-people-fill"></i><span>Utilisateurs</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=resources" class="nav-link active"><i class="bi bi-folder-fill"></i><span>Ressources</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=comments" class="nav-link"><i class="bi bi-chat-dots-fill"></i><span>Commentaires</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=profile" class="nav-link"><i class="bi bi-person-fill"></i><span>Mon profil</span></a></div>
            <div class="nav-item logout-link"><a href="index.php?action=logout" class="nav-link"><i class="bi bi-box-arrow-right"></i><span>Déconnexion</span></a></div>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <div class="page-title">
                <h1><i class="bi bi-eye-fill me-2"></i>Inspection ressource</h1>
                <p>ID #<?= (int)$resource['id_res'] ?> — <?= escape($resource['titre']) ?></p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="index.php?action=admin&subaction=resources" class="btn-outline-admin">← Liste des ressources</a>
                <a href="index.php?action=admin&subaction=edit_resource&id=<?= (int)$resource['id_res'] ?>" class="btn-blue"><i class="bi bi-pencil-fill"></i> Modifier</a>
                <a href="index.php?action=resource&subaction=detail&id=<?= (int)$resource['id_res'] ?>" class="btn-blue" style="background:linear-gradient(135deg,#0f766e,#14b8a6);" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> Fiche publique</a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <div class="content-card">
                    <?php if (!empty($resource['photo'])): ?>
                        <img src="<?= escape($resource['photo']) ?>" alt="" class="inspect-photo">
                    <?php endif; ?>
                    <h2 class="h4 mb-3"><?= escape($resource['titre']) ?></h2>
                    <p class="text-muted" style="white-space:pre-wrap;"><?= escape($resource['description'] ?? '') ?></p>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="content-card">
                    <h3 class="h6 text-uppercase text-muted mb-3">Métadonnées</h3>
                    <dl class="kv">
                        <dt>Matière</dt><dd><span class="badge-matiere"><?= escape($resource['matiere'] ?: '—') ?></span></dd>
                        <dt>Type</dt><dd><?= escape($resource['type'] ?? '—') ?></dd>
                        <dt>Niveau</dt><dd><?= escape($resource['niveau'] ?? '—') ?></dd>
                        <dt>Accès</dt><dd><?= escape($resource['acces'] ?? '—') ?><?php if (!empty($resource['prix']) && ($resource['acces'] ?? '') === 'Premium'): ?> — <?= number_format((float)$resource['prix'], 2) ?> DT<?php endif; ?></dd>
                        <dt>Pages</dt><dd><?= (int)($resource['pages'] ?? 0) ?></dd>
                        <dt>Téléchargements</dt><dd><i class="bi bi-download text-primary"></i> <?= (int)($resource['downloads'] ?? 0) ?></dd>
                        <dt>Note moyenne</dt><dd><?= isset($resource['note_moyenne']) && $resource['note_moyenne'] > 0 ? '<i class="bi bi-star-fill text-warning"></i> ' . escape((string)$resource['note_moyenne']) . ' <span class="text-muted">(' . (int)$totalVotes . ' vote(s))</span>' : '—' ?></dd>
                        <dt>Publication</dt><dd><?= !empty($resource['date_creation']) ? escape(date('d/m/Y H:i', strtotime($resource['date_creation']))) : '—' ?></dd>
                        <dt>Auteur</dt><dd><?= escape(trim(($resource['prenom'] ?? '') . ' ' . ($resource['nom'] ?? ''))) ?> <span class="text-muted">(ID utilisateur <?= (int)($resource['user_id'] ?? $resource['id'] ?? 0) ?>)</span></dd>
                        <dt>Email auteur</dt><dd><?= escape($resource['email'] ?? '—') ?></dd>
                    </dl>
                    <?php if (!empty($resource['fichier'])): ?>
                        <hr>
                        <p class="small text-muted mb-2">Fichier joint</p>
                        <code class="d-block small mb-3 text-break"><?= escape($resource['fichier']) ?></code>
                        <a href="index.php?action=admin&subaction=download_resource&id=<?= (int)$resource['id_res'] ?>" class="btn-blue"><i class="bi bi-download"></i> Télécharger le fichier</a>
                    <?php else: ?>
                        <p class="text-muted small mb-0 mt-3">Aucun fichier joint.</p>
                    <?php endif; ?>
                </div>

                <div class="content-card">
                    <h3 class="h6 text-uppercase text-muted mb-3">Commentaires (<?= count($comments) ?>)</h3>
                    <?php if (empty($comments)): ?>
                        <p class="text-muted small mb-0">Aucun commentaire.</p>
                    <?php else: ?>
                        <ul class="list-unstyled mb-0">
                            <?php foreach (array_slice($comments, 0, 15) as $c): ?>
                                <li class="border-bottom pb-2 mb-2">
                                    <strong><?= escape(trim(($c['prenom'] ?? '') . ' ' . ($c['nom'] ?? ''))) ?></strong>
                                    <span class="text-muted small"><?= !empty($c['date']) ? escape(date('d/m/Y H:i', strtotime($c['date']))) : '' ?></span>
                                    <div class="small mt-1"><?= nl2br(escape($c['message'] ?? '')) ?></div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
