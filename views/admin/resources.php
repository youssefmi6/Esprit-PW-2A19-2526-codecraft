<?php
// views/admin/resources.php - Gestion des ressources
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ressources | StudyHub Admin</title>
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
        .content-card { background:white; border-radius:20px; padding:20px; box-shadow:0 4px 15px rgba(0,0,0,0.05); }
        .search-box { position:relative; display:inline-block; width:100%; margin-bottom:10px; }
        .search-box i { position:absolute; left:15px; top:50%; transform:translateY(-50%); color:#1a8cff; }
        .search-box input { padding:10px 15px 10px 40px; border:2px solid #e0e7ff; border-radius:12px; width:100%; }
        .btn-blue { background:linear-gradient(135deg,#1a8cff 0%,#00b4d8 100%); border:none; padding:8px 20px; border-radius:10px; font-weight:600; color:white; transition:all 0.3s; }
        .btn-blue:hover { transform:translateY(-2px); box-shadow:0 5px 15px rgba(26,140,255,0.3); }
        .btn-edit { background:none; border:none; color:#1a8cff; font-size:18px; cursor:pointer; padding:5px; }
        .btn-delete { background:none; border:none; color:#dc2626; font-size:18px; cursor:pointer; padding:5px; }
        .badge-matiere { background:#e0e7ff; color:#1a8cff; padding:4px 10px; border-radius:20px; font-size:11px; }
        @media (max-width:768px) { .sidebar { width:80px; } .sidebar .logo-text, .sidebar .nav-link span { display:none; } .main-content { margin-left:80px; } }
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
        <div class="top-bar"><div class="page-title"><h1>Gestion des ressources</h1><p>Gérez toutes les ressources pédagogiques</p></div><button class="btn-blue" data-bs-toggle="modal" data-bs-target="#addResourceModal"><i class="bi bi-plus-circle-fill me-2"></i>Ajouter</button></div>
        <div class="content-card">
            <form method="GET" action="index.php?action=admin&subaction=resources"><input type="hidden" name="action" value="admin"><input type="hidden" name="subaction" value="resources"><div class="row g-2 mb-3"><div class="col-md-3"><div class="search-box"><i class="bi bi-search"></i><input type="text" id="resourcesSearchInput" name="search" class="form-control" placeholder="Rechercher..." value="<?= escape($search) ?>"></div></div>
            <div class="col-md-3"><select id="resourcesTypeFilter" name="type" class="form-select" onchange="this.form.submit()"><option value="">Tous les types</option><?php foreach($types as $t): ?><option value="<?= escape($t['type']) ?>" <?= $type_filter == $t['type'] ? 'selected' : '' ?>><?= escape($t['type']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-3"><select id="resourcesMatiereFilter" name="matiere" class="form-select" onchange="this.form.submit()"><option value="">Toutes les matières</option><?php foreach($matieres as $m): ?><option value="<?= escape($m['matiere']) ?>" <?= $matiere_filter == $m['matiere'] ? 'selected' : '' ?>><?= escape($m['matiere']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2">
                <select id="resourcesSortFilter" name="sort" class="form-select" onchange="this.form.submit()">
                    <option value="date_desc" <?= (($sort ?? 'date_desc') === 'date_desc') ? 'selected' : '' ?>>Plus récents</option>
                    <option value="date_asc" <?= (($sort ?? 'date_desc') === 'date_asc') ? 'selected' : '' ?>>Plus anciens</option>
                    <option value="alpha_asc" <?= (($sort ?? 'date_desc') === 'alpha_asc') ? 'selected' : '' ?>>Titre A-Z</option>
                    <option value="alpha_desc" <?= (($sort ?? 'date_desc') === 'alpha_desc') ? 'selected' : '' ?>>Titre Z-A</option>
                    <option value="downloads_desc" <?= (($sort ?? 'date_desc') === 'downloads_desc') ? 'selected' : '' ?>>Téléchargements</option>
                    <option value="rating_desc" <?= (($sort ?? 'date_desc') === 'rating_desc') ? 'selected' : '' ?>>Note</option>
                </select>
            </div>
            <div class="col-md-1"><a href="index.php?action=admin&subaction=resources" class="btn btn-secondary w-100">RAZ</a></div></div></form>
            
            <?php if(isset($success)): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
            <?php if(isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
            
            <div class="table-responsive"><table class="table table-hover"><thead class="table-light"><tr><th>ID</th><th>Titre</th><th>Type</th><th>Matière</th><th>Niveau</th><th>Auteur</th><th>Pages</th><th>Downloads</th><th>Note</th><th>Actions</th></tr></thead><tbody id="resourcesTableBody">
                <?php foreach($resources as $r): ?>
                <tr><td><?= $r['id_res'] ?></td><td><?= escape(substr($r['titre'], 0, 40)) ?></td><td><span class="badge bg-info"><?= $r['type'] ?></span></td><td><span class="badge-matiere"><?= escape($r['matiere'] ?: 'Autre') ?></span></td><td><?= $r['niveau'] ?: '-' ?></td><td><?= escape($r['prenom'] . ' ' . $r['nom']) ?></td><td><?= $r['pages'] ?: '-' ?></td><td><i class="bi bi-download"></i> <?= $r['downloads'] ?></td><td><?php if($r['note_moyenne'] > 0): ?><i class="bi bi-star-fill text-warning"></i> <?= $r['note_moyenne'] ?><?php else: ?>-<?php endif; ?></td>
                <td><a href="index.php?action=admin&subaction=view_resource&id=<?= $r['id_res'] ?>" class="btn-edit"><i class="bi bi-eye-fill"></i></a><a href="index.php?action=admin&subaction=edit_resource&id=<?= $r['id_res'] ?>" class="btn-edit"><i class="bi bi-pencil-fill"></i></a><a href="index.php?action=admin&subaction=delete_resource&id=<?= $r['id_res'] ?>" class="btn-delete" onclick="return confirm('Supprimer ?')"><i class="bi bi-trash3-fill"></i></a></td>
                <?php endforeach; ?>
            </tbody><tr></div>
        </div>
    </div>

    <div class="modal fade" id="addResourceModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content"><div class="modal-header" style="background:linear-gradient(135deg,#1a8cff 0%,#00b4d8 100%); color:white;"><h5 class="modal-title"><i class="bi bi-plus-circle-fill me-2"></i>Ajouter une ressource</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
        <form method="POST" enctype="multipart/form-data" action="index.php?action=admin&subaction=resources"><input type="hidden" name="action" value="add"><div class="modal-body"><div class="row"><div class="col-md-8 mb-3"><label class="form-label">Titre *</label><input type="text" name="titre" class="form-control" required></div><div class="col-md-4 mb-3"><label class="form-label">Type *</label><select name="type" class="form-select" required><option value="Cours">Cours</option><option value="Exercice">Exercice</option><option value="Examen">Examen</option><option value="TD">TD</option><option value="TP">TP</option><option value="Résumé">Résumé</option></select></div></div>
        <div class="mb-3"><label class="form-label">Description *</label><textarea name="description" class="form-control" rows="3" required></textarea></div>
        <div class="row"><div class="col-md-6 mb-3"><label class="form-label">Matière</label><input type="text" name="matiere" class="form-control" placeholder="Mathématiques, Physique..."></div><div class="col-md-6 mb-3"><label class="form-label">Niveau</label><select name="niveau" class="form-select"><option value="">Sélectionner</option><option value="Licence 1">Licence 1</option><option value="Licence 2">Licence 2</option><option value="Licence 3">Licence 3</option><option value="Master 1">Master 1</option><option value="Master 2">Master 2</option></select></div></div>
        <div class="row"><div class="col-md-4 mb-3"><label class="form-label">Pages</label><input type="number" name="pages" class="form-control" value="0"></div><div class="col-md-4 mb-3"><label class="form-label">Accès</label><select name="acces" class="form-select" id="accessSelect"><option value="gratuit">Gratuit</option><option value="payant">Payant</option></select></div><div class="col-md-4 mb-3" id="priceRow" style="display:none;"><label class="form-label">Prix (€)</label><input type="number" name="prix" class="form-control" value="0" step="0.01"></div></div>
        <div class="mb-3"><label class="form-label">Fichier (PDF)</label><input type="file" name="fichier" class="form-control" accept=".pdf"></div>
        <div class="mb-3"><label class="form-label">Image de couverture</label><input type="file" name="photo" class="form-control" accept="image/*"></div></div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn-blue">Ajouter</button></div></form>
    </div></div></div>

    <script>document.getElementById('accessSelect')?.addEventListener('change', function() { document.getElementById('priceRow').style.display = this.value === 'payant' ? 'flex' : 'none'; });</script>
    <script>
        (function () {
            const searchInput = document.getElementById('resourcesSearchInput');
            const typeFilter = document.getElementById('resourcesTypeFilter');
            const matiereFilter = document.getElementById('resourcesMatiereFilter');
            const sortFilter = document.getElementById('resourcesSortFilter');
            const tableBody = document.getElementById('resourcesTableBody');
            if (!searchInput || !typeFilter || !matiereFilter || !sortFilter || !tableBody) return;

            let timer = null;
            const runSearch = async () => {
                const params = new URLSearchParams({
                    action: 'admin',
                    subaction: 'resources',
                    ajax: '1',
                    search: searchInput.value.trim(),
                    type: typeFilter.value,
                    matiere: matiereFilter.value,
                    sort: sortFilter.value
                });

                try {
                    const response = await fetch(`index.php?${params.toString()}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (!response.ok) return;
                    const data = await response.json();
                    if (typeof data.rows === 'string') {
                        tableBody.innerHTML = data.rows;
                    }
                } catch (e) {
                    console.error('Recherche dynamique resources error:', e);
                }
            };

            searchInput.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(runSearch, 250);
            });
            typeFilter.addEventListener('change', function (e) {
                e.preventDefault();
                runSearch();
            });
            matiereFilter.addEventListener('change', function (e) {
                e.preventDefault();
                runSearch();
            });
            sortFilter.addEventListener('change', function (e) {
                e.preventDefault();
                runSearch();
            });
        })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>