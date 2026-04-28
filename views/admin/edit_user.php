<?php
// views/admin/edit_user.php - Modifier un utilisateur
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier utilisateur</title>
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
        .content-card { background:white; border-radius:24px; padding:25px; max-width:600px; margin:0 auto; }
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
            <div class="nav-item"><a href="index.php?action=admin&subaction=users" class="nav-link active"><i class="bi bi-people-fill"></i><span>Utilisateurs</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=resources" class="nav-link"><i class="bi bi-folder-fill"></i><span>Ressources</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=comments" class="nav-link"><i class="bi bi-chat-dots-fill"></i><span>Commentaires</span></a></div>
            <div class="nav-item logout-link"><a href="index.php?action=logout" class="nav-link"><i class="bi bi-box-arrow-right"></i><span>Déconnexion</span></a></div>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar"><div class="page-title"><h1>Modifier l'utilisateur</h1><p>Modifiez les informations</p></div><a href="index.php?action=admin&subaction=users" class="btn btn-secondary">← Retour</a></div>
        <div class="content-card">
            <?php if(isset($success)): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
            <?php if(isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
            <form method="POST" id="adminEditUserForm" novalidate><div class="row"><div class="col-md-6 mb-3"><label class="form-label">Nom</label><input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($user['nom']) ?>"></div><div class="col-md-6 mb-3"><label class="form-label">Prénom</label><input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($user['prenom']) ?>"></div></div>
            <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>"></div>
            <div class="row"><div class="col-md-6 mb-3"><label class="form-label">Université</label><input type="text" name="universite" class="form-control" value="<?= htmlspecialchars($user['universite']) ?>"></div><div class="col-md-6 mb-3"><label class="form-label">Filière</label><input type="text" name="filiere" class="form-control" value="<?= htmlspecialchars($user['filiere']) ?>"></div></div>
            <div class="mb-3"><label class="form-label">Rôle</label><select name="role" class="form-select"><option value="1" <?= $user['role'] == 1 ? 'selected' : '' ?>>Utilisateur</option><option value="0" <?= $user['role'] == 0 ? 'selected' : '' ?>>Administrateur</option></select></div>
            <div class="mb-3"><label class="form-label">Nouveau mot de passe</label><input type="password" name="mdp" class="form-control" placeholder="Laisser vide pour ne pas changer"></div>
            <div class="d-flex gap-3"><button type="submit" class="btn-blue">Enregistrer</button><a href="index.php?action=admin&subaction=users" class="btn btn-secondary">Annuler</a></div></form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/validation.js"></script>
    <script src="../js/admin-forms.js"></script>
</body>
</html>