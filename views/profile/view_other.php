<?php
// views/profile/view_other.php - Voir profil d'un autre utilisateur
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil de <?= escape($profileUser['nom']) ?> - StudyHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Jost:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <script>
        (function () {
            var savedTheme = localStorage.getItem('studyhub-theme');
            if (savedTheme === 'light' || savedTheme === 'dark') {
                document.documentElement.setAttribute('data-theme', savedTheme);
            }
        })();
    </script>
</head>
<body>

<nav class="navbar-custom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-6 col-lg-3"><a href="index.php?action=home" class="logo"><img src="uploads/logo.png" alt="StudyHub" class="site-logo"></a></div>
            <div class="col-lg-5 d-none d-lg-block"><ul class="nav-links"><li><a href="index.php?action=home">Accueil</a></li><li><a href="index.php?action=resource&subaction=upload">Publier</a></li><li><a href="index.php?action=profile">Mon Profil</a></li></ul></div>
            <div class="col-6 col-lg-4">
                <div class="nav-right-controls">
                <button type="button" class="theme-toggle" id="themeToggle" title="Changer le mode">
                    <i class="fa-solid fa-sun" id="themeIcon"></i>
                </button>
                <?php if ($currentUser): ?>
                    <span class="user-chip"><img src="<?= escape(!empty($currentUser['photo']) ? $currentUser['photo'] : 'https://randomuser.me/api/portraits/men/32.jpg') ?>" class="user-avatar-small"><span class="user-chip-name"><?= escape($currentUser['nom']) ?></span> <a href="index.php?action=logout" class="text-danger"><i class="ti-power-off"></i></a></span>
                <?php else: ?>
                    <a href="index.php?action=login" class="btn-outline-custom me-2">Connexion</a>
                    <a href="index.php?action=register" class="btn-primary-custom">Inscription</a>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4"><h3>👤 Profil de <?= escape($profileUser['nom']) ?> <?= escape($profileUser['prenom']) ?></h3><a href="index.php?action=home" class="btn-outline-custom">← Retour</a></div>
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="profile-card">
                <img src="<?= !empty($profileUser['photo']) ? $profileUser['photo'] : 'https://randomuser.me/api/portraits/men/32.jpg' ?>" class="profile-avatar">
                <h4><?= escape($profileUser['nom']) ?> <?= escape($profileUser['prenom']) ?></h4>
                <p class="text-muted"><?= escape($profileUser['filiere'] ?: 'Étudiant') ?></p>
                <div class="stars"><?= generateStarRating($avgUserRating) ?></div>
                <p><strong><?= number_format($avgUserRating,1) ?>/5</strong></p>
                <hr><p><i class="ti-location-pin"></i> <?= escape($profileUser['universite'] ?: 'Non renseignée') ?></p>
                <p><i class="ti-email"></i> <?= escape($profileUser['email']) ?></p>
            </div>
        </div>
        <div class="col-md-8">
            <div class="row mb-4">
                <div class="col-6 col-md-4 mb-2"><div class="stat-card"><i class="ti-book"></i><h3><?= $totalResources ?></h3><small>Ressources</small></div></div>
                <div class="col-6 col-md-4 mb-2"><div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#ea580c);"><i class="ti-download"></i><h3><?= number_format($totalDownloads) ?></h3><small>Téléchargements</small></div></div>
                <div class="col-6 col-md-4 mb-2"><div class="stat-card" style="background:linear-gradient(135deg,#10b981,#059669);"><i class="ti-star"></i><h3><?= number_format($avgUserRating,1) ?></h3><small>Note moyenne</small></div></div>
            </div>
            <div class="profile-card text-start"><h5>📚 Ressources publiées</h5>
                <?php foreach ($userResources as $res): ?>
                <div class="resource-item"><strong><a href="index.php?action=resource&subaction=detail&id=<?= $res['id_res'] ?>" style="color:#1e293b;"><?= escape($res['titre']) ?></a></strong><br><small><?= $res['acces'] == 'Premium' ? "💰 {$res['prix']} DT" : '📥 Gratuit' ?> | 📄 <?= $res['pages'] ?> pages | 📥 <?= number_format($res['downloads']) ?> téléch.</small></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<footer class="footer"><div class="container"><div class="row"><div class="col-lg-4"><h4 class="mb-3"><img src="uploads/logo.png" alt="StudyHub" class="site-logo site-logo--footer"></h4><p>Plateforme de partage de ressources académiques entre étudiants.</p></div></div></div></footer>
<div class="copyright"><p>&copy; 2025 StudyHub - Tous droits réservés</p></div>

<script src="js/validation.js"></script>
<script src="js/scripts.js"></script>
</body>
</html>