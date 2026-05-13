<?php
// views/admin/edit_resource.php - Modifier une ressource (admin)
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier ressource | StudyHub Admin</title>
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
        .nav-link { display:flex; align-items:center; gap:12px; padding:12px 16px; border-radius:12px; color:rgba(255,255,255,0.85); text-decoration:none; transition:all 0.3s; font-weight:500; }
        .nav-link i { font-size:20px; width:24px; }
        .nav-link:hover { background:rgba(255,255,255,0.2); color:white; transform:translateX(5px); }
        .nav-link.active { background:rgba(255,255,255,0.25); color:white; }
        .logout-link { margin-top:40px; border-top:1px solid rgba(255,255,255,0.2); padding-top:20px; }
        .main-content { margin-left:280px; padding:30px; min-height:100vh; }
        .top-bar { background:rgba(255,255,255,0.95); border-radius:20px; padding:15px 25px; margin-bottom:30px; display:flex; justify-content:space-between; align-items:center; }
        .page-title h1 { font-size:24px; font-weight:700; color:#0a5c8e; margin:0; }
        .page-title p { color:#5a8faa; margin:0; font-size:14px; }
        .content-card { background:white; border-radius:24px; padding:25px; max-width:700px; margin:0 auto; }
        .btn-blue { background:linear-gradient(135deg,#1a8cff 0%,#00b4d8 100%); border:none; padding:12px 28px; border-radius:12px; font-weight:600; color:white; transition:all 0.3s; }
        @media (max-width:768px) { .sidebar { width:80px; } .sidebar .logo-text, .sidebar .nav-link span { display:none; } .main-content { margin-left:80px; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header"><div class="logo"><div class="logo-icon"><i class="bi bi-mortarboard-fill"></i></div><div class="logo-text"><h3>StudyHub</h3><p>Admin Dashboard</p></div></div></div>
        <div class="nav-menu">
            <div class="nav-item"><a href="index.php?action=admin&subaction=dashboard" class="nav-link"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=subscriptions" class="nav-link"><i class="bi bi-star-fill"></i><span>Abonnements</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=users" class="nav-link"><i class="bi bi-people-fill"></i><span>Utilisateurs</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=resources" class="nav-link active"><i class="bi bi-folder-fill"></i><span>Ressources</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=comments" class="nav-link"><i class="bi bi-chat-dots-fill"></i><span>Commentaires</span></a></div>
            <div class="nav-item logout-link"><a href="index.php?action=logout" class="nav-link"><i class="bi bi-box-arrow-right"></i><span>Déconnexion</span></a></div>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar"><div class="page-title"><h1>Modifier la ressource</h1><p>Modifiez les informations de la ressource</p></div><a href="index.php?action=admin&subaction=resources" class="btn btn-secondary">← Retour</a></div>
        <div class="content-card">
            <?php if(isset($success)): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
            <?php if(isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
            <form method="POST" enctype="multipart/form-data"><div class="row"><div class="col-md-8 mb-3"><label class="form-label">Titre *</label><input type="text" name="titre" class="form-control" value="<?= htmlspecialchars($resource['titre']) ?>" required></div><div class="col-md-4 mb-3"><label class="form-label">Type *</label><select name="type" class="form-select" required><option value="Cours" <?= $resource['type'] == 'Cours' ? 'selected' : '' ?>>Cours</option><option value="Exercice" <?= $resource['type'] == 'Exercice' ? 'selected' : '' ?>>Exercice</option><option value="Examen" <?= $resource['type'] == 'Examen' ? 'selected' : '' ?>>Examen</option><option value="TD" <?= $resource['type'] == 'TD' ? 'selected' : '' ?>>TD</option><option value="TP" <?= $resource['type'] == 'TP' ? 'selected' : '' ?>>TP</option></select></div></div>
            <div class="mb-3"><label class="form-label">Description *</label><textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($resource['description']) ?></textarea></div>
            <div class="row"><div class="col-md-6 mb-3"><label class="form-label">Niveau</label><select name="niveau" class="form-select"><option value="">Sélectionner</option><option value="L1" <?= $resource['niveau'] == 'L1' ? 'selected' : '' ?>>L1</option><option value="L2" <?= $resource['niveau'] == 'L2' ? 'selected' : '' ?>>L2</option><option value="L3" <?= $resource['niveau'] == 'L3' ? 'selected' : '' ?>>L3</option><option value="M1" <?= $resource['niveau'] == 'M1' ? 'selected' : '' ?>>M1</option><option value="M2" <?= $resource['niveau'] == 'M2' ? 'selected' : '' ?>>M2</option></select></div>
            <div class="col-md-6 mb-3"><label class="form-label">Accès</label><select name="acces" class="form-select" id="accessSelect"><option value="gratuit" <?= $resource['acces'] == 'gratuit' ? 'selected' : '' ?>>Gratuit</option><option value="payant" <?= $resource['acces'] == 'payant' ? 'selected' : '' ?>>Payant</option></select></div></div>
            <div class="row" id="priceRow" <?= $resource['acces'] == 'payant' ? '' : 'style="display:none;"' ?>><div class="col-md-6 mb-3"><label class="form-label">Prix (€)</label><input type="number" name="prix" class="form-control" value="<?= $resource['prix'] ?>" step="0.01"></div></div>
            <div class="mb-3"><label class="form-label">Fichier actuel</label><?php if(!empty($resource['fichier'])): ?><div class="alert alert-info"><?= htmlspecialchars($resource['fichier']) ?></div><?php else: ?><div class="alert alert-secondary">Aucun fichier</div><?php endif; ?><label class="form-label mt-2">Remplacer le fichier</label><input type="file" name="fichier" class="form-control"></div>
            <div class="d-flex gap-3 mt-4"><button type="submit" class="btn-blue">Enregistrer</button><a href="index.php?action=admin&subaction=resources" class="btn btn-secondary">Annuler</a></div></form>
        </div>
    </div>
    <script>document.getElementById('accessSelect')?.addEventListener('change', function() { document.getElementById('priceRow').style.display = this.value === 'payant' ? 'flex' : 'none'; });</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>