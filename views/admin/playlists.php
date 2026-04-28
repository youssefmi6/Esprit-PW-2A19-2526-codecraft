<?php
// views/admin/playlists.php
$isEdit = !empty($editPlaylist);
$formTitle = $isEdit ? 'Modifier la playlist' : 'Créer une playlist';
$submitLabel = $isEdit ? 'Enregistrer les modifications' : 'Créer playlist';
$nomVal = $isEdit ? ($editPlaylist['nom'] ?? '') : '';
$descVal = $isEdit ? ($editPlaylist['description'] ?? '') : '';
$editGroupId = $isEdit ? (int) ($editPlaylist['id_abonement'] ?? 0) : 0;
$editResourceIds = isset($editResourceIds) ? array_map('intval', (array) $editResourceIds) : [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Playlists | StudyHub Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
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
        .main-content { margin-left:280px; padding:30px; min-height:100vh; background:#f8fafc; }
        .card-box { background:#fff; border-radius:16px; padding:20px; box-shadow:0 4px 14px rgba(0,0,0,0.06); margin-bottom:20px; }
        .playlist-photo { width:56px; height:56px; border-radius:12px; object-fit:cover; background:#eef2ff; }
        .resources-box { max-height:250px; overflow:auto; border:1px solid #e2e8f0; border-radius:10px; padding:10px; }
        .mini-stat { background:#f8fafc; border-radius:12px; padding:12px 16px; text-align:center; border:1px solid #e2e8f0; }
        .mini-stat strong { display:block; font-size:1.25rem; color:#0a5c8e; }
        .search-box { position:relative; }
        .search-box i { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#1a8cff; }
        .search-box input { padding-left:40px; border-radius:12px; }
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
            <div class="nav-item"><a href="index.php?action=admin&subaction=resources" class="nav-link"><i class="bi bi-folder-fill"></i><span>Ressources</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=playlists" class="nav-link active"><i class="bi bi-collection-play-fill"></i><span>Playlists</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=comments" class="nav-link"><i class="bi bi-chat-dots-fill"></i><span>Commentaires</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=profile" class="nav-link"><i class="bi bi-person-fill"></i><span>Mon profil</span></a></div>
            <div class="nav-item logout-link"><a href="index.php?action=logout" class="nav-link"><i class="bi bi-box-arrow-right"></i><span>Déconnexion</span></a></div>
        </div>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h4 mb-1">Playlists admin</h1>
                <p class="text-muted mb-0">Créer une playlist avec photo et ressources.</p>
            </div>
        </div>

        <?php if (!empty($_SESSION['admin_playlist_success'])): ?>
            <div class="alert alert-success"><?php echo escape($_SESSION['admin_playlist_success']); unset($_SESSION['admin_playlist_success']); ?></div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['admin_playlist_error'])): ?>
            <div class="alert alert-danger"><?php echo escape($_SESSION['admin_playlist_error']); unset($_SESSION['admin_playlist_error']); ?></div>
        <?php endif; ?>

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-4"><div class="mini-stat"><small class="text-muted">Total playlists</small><strong><?php echo number_format((int) ($playlistStats['total_playlists'] ?? 0)); ?></strong></div></div>
            <div class="col-6 col-md-4"><div class="mini-stat"><small class="text-muted">Ressources liées</small><strong><?php echo number_format((int) ($playlistStats['total_resources_linked'] ?? 0)); ?></strong></div></div>
            <div class="col-12 col-md-4"><div class="mini-stat"><small class="text-muted">Moyenne ressources / playlist</small><strong><?php echo number_format((float) ($playlistStats['avg_resources_per_playlist'] ?? 0), 1); ?></strong></div></div>
        </div>

        <div class="card-box">
            <h2 class="h5 mb-3"><?php echo escape($formTitle); ?></h2>
            <form method="POST" enctype="multipart/form-data" id="adminPlaylistForm" novalidate>
                <input type="hidden" name="playlist_group_id" value="<?php echo $editGroupId; ?>">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Nom</label>
                        <input type="text" name="nom" class="form-control" value="<?php echo escape($nomVal); ?>">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Description</label>
                        <div class="input-group">
                            <textarea id="playlistDescription" name="description" class="form-control" rows="2"><?php echo escape($descVal); ?></textarea>
                            <button class="btn btn-outline-primary" type="button" id="speechToTextBtn">
                                <i class="bi bi-mic-fill"></i> Speech to text
                            </button>
                        </div>
                        <small class="text-muted">Cliquez une fois pour démarrer, une 2e fois pour arrêter.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Photo playlist</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Choisir les ressources</label>
                        <div class="resources-box">
                            <?php foreach ($allResources as $res): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="resources[]" value="<?php echo (int) $res['id_res']; ?>" id="res_<?php echo (int) $res['id_res']; ?>" <?php echo in_array((int) $res['id_res'], $editResourceIds, true) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="res_<?php echo (int) $res['id_res']; ?>">
                                        <?php echo escape($res['titre']); ?> (<?php echo escape($res['matiere'] ?: 'Autre'); ?>)
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i><?php echo escape($submitLabel); ?></button>
                    <?php if ($isEdit): ?>
                        <a href="index.php?action=admin&subaction=playlists" class="btn btn-outline-secondary">Annuler</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="card-box">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <h2 class="h5 mb-0">Playlists existantes</h2>
                <form method="get" action="index.php" class="search-box">
                    <input type="hidden" name="action" value="admin">
                    <input type="hidden" name="subaction" value="playlists">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" class="form-control" placeholder="Rechercher par ID ou nom..." value="<?php echo escape($search ?? ''); ?>">
                </form>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Photo</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Ressources</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($playlists as $pl): ?>
                            <tr>
                                <td><?php echo (int) $pl['id_abonement']; ?></td>
                                <td>
                                    <?php if (!empty($pl['photo'])): ?>
                                        <img src="<?php echo escape($pl['photo']); ?>" class="playlist-photo" alt="photo playlist">
                                    <?php else: ?>
                                        <div class="playlist-photo d-flex align-items-center justify-content-center"><i class="bi bi-image text-muted"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo escape($pl['nom']); ?></td>
                                <td><?php echo escape($pl['description']); ?></td>
                                <td><?php echo (int) $pl['resource_count']; ?></td>
                                <td>
                                    <a class="btn btn-sm btn-outline-primary" href="index.php?action=admin&subaction=playlists&edit_id=<?php echo (int) $pl['id_abonement']; ?>">
                                        Modifier
                                    </a>
                                    <a class="btn btn-sm btn-outline-danger" href="index.php?action=admin&subaction=playlist_delete&id=<?php echo (int) $pl['id_abonement']; ?>" onclick="return confirm('Supprimer cette playlist ?');">
                                        Supprimer
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($playlists)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-3">Aucune playlist trouvée.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="../js/validation.js"></script>
    <script src="../js/admin-playlist-form.js"></script>
</body>
</html>
