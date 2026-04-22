<?php
// views/admin/users.php - Gestion des utilisateurs
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Utilisateurs | StudyHub Admin</title>
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
        .content-card { background:white; border-radius:24px; padding:25px; box-shadow:0 4px 15px rgba(0,0,0,0.05); }
        .search-box { position:relative; margin-bottom:25px; }
        .search-box i { position:absolute; left:16px; top:50%; transform:translateY(-50%); color:#1a8cff; }
        .search-box input { padding:12px 16px 12px 45px; border:2px solid #e0e7ff; border-radius:16px; width:100%; }
        .btn-blue { background:linear-gradient(135deg,#1a8cff 0%,#00b4d8 100%); border:none; padding:10px 24px; border-radius:12px; font-weight:600; color:white; transition:all 0.3s; }
        .btn-blue:hover { transform:translateY(-2px); box-shadow:0 5px 15px rgba(26,140,255,0.3); }
        .btn-edit { background:none; border:none; color:#1a8cff; font-size:18px; cursor:pointer; padding:5px; }
        .btn-delete { background:none; border:none; color:#dc2626; font-size:18px; cursor:pointer; padding:5px; }
        @media (max-width:768px) { .sidebar { width:80px; } .sidebar .logo-text, .sidebar .nav-link span { display:none; } .main-content { margin-left:80px; } }
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
        <div class="top-bar"><div class="page-title"><h1>Gestion des utilisateurs</h1><p>Gérez tous les utilisateurs de la plateforme</p></div><button class="btn-blue" data-bs-toggle="modal" data-bs-target="#addUserModal"><i class="bi bi-person-plus-fill me-2"></i>Ajouter</button></div>
        <div class="content-card">
            <div class="search-box"><i class="bi bi-search"></i><form method="GET" action="index.php?action=admin&subaction=users"><input type="hidden" name="action" value="admin"><input type="hidden" name="subaction" value="users"><input type="text" name="search" id="usersSearchInput" class="form-control" placeholder="Rechercher..." value="<?= escape($search) ?>"></form></div>
            <?php if(isset($success)): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
            <?php if(isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
            <div class="table-responsive"><table class="table table-hover"><thead class="table-light"><tr><th>ID</th><th>Nom complet</th><th>Email</th><th>Université</th><th>Filière</th><th>Rôle</th><th>Actions</th></tr></thead><tbody id="usersTableBody">
                <?php foreach($users as $user): ?>
                <tr><td><?= $user['id'] ?></td><td><i class="bi bi-person-circle me-2" style="color:#1a8cff"></i><?= escape($user['prenom'] . ' ' . $user['nom']) ?></td><td><?= escape($user['email']) ?></td><td><?= escape($user['universite'] ?: '-') ?></td><td><?= escape($user['filiere'] ?: '-') ?></td><td><span class="badge bg-secondary"><?= $user['role'] == 0 ? 'Admin' : 'User' ?></span></td>
                <td><a href="index.php?action=admin&subaction=view_user&id=<?= $user['id'] ?>" class="btn-edit" title="Inspecter le profil"><i class="bi bi-eye-fill"></i></a><a href="index.php?action=admin&subaction=edit_user&id=<?= $user['id'] ?>" class="btn-edit" title="Modifier"><i class="bi bi-pencil-fill"></i></a><a href="index.php?action=admin&subaction=delete_user&id=<?= $user['id'] ?>" class="btn-delete" title="Supprimer" onclick="return confirm('Supprimer ?')"><i class="bi bi-trash3-fill"></i></a></td>
                <?php endforeach; ?>
            </tbody></table></div>
        </div>
    </div>

    <div class="modal fade" id="addUserModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header" style="background:linear-gradient(135deg,#1a8cff 0%,#00b4d8 100%); color:white;"><h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Ajouter</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="index.php?action=admin&subaction=users"><input type="hidden" name="action" value="add"><div class="modal-body"><input type="text" name="nom" class="form-control mb-3" placeholder="Nom" required><input type="text" name="prenom" class="form-control mb-3" placeholder="Prénom" required><input type="email" name="email" class="form-control mb-3" placeholder="Email" required><input type="password" name="mdp" class="form-control mb-3" placeholder="Mot de passe" required><select name="role" class="form-select"><option value="1">Utilisateur</option><option value="0">Administrateur</option></select></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn-blue">Ajouter</button></div></form>
    </div></div></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            const input = document.getElementById('usersSearchInput');
            const body = document.getElementById('usersTableBody');
            if (!input || !body) return;

            let timer = null;
            input.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(async () => {
                    const search = encodeURIComponent(input.value.trim());
                    const url = `index.php?action=admin&subaction=users&ajax=1&search=${search}`;
                    try {
                        const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                        if (!response.ok) return;
                        const data = await response.json();
                        if (typeof data.rows === 'string') {
                            body.innerHTML = data.rows;
                        }
                    } catch (e) {
                        console.error('Recherche dynamique users error:', e);
                    }
                }, 250);
            });
        })();
    </script>
    <script src="../js/validation.js"></script>
    <script src="../js/admin-users.js"></script>
</body>
</html>