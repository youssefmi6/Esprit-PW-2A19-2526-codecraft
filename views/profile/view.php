<?php
// views/profile/view.php - Mon profil
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil - StudyHub</title>
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
            <div class="col-lg-5 d-none d-lg-block"><ul class="nav-links"><li><a href="index.php?action=home">Accueil</a></li><li><a href="index.php?action=resource&subaction=upload">Publier</a></li><li><a href="index.php?action=profile" class="active">Mon Profil</a></li></ul></div>
            <div class="col-6 col-lg-4"><div class="nav-right-controls"><button type="button" class="theme-toggle" id="themeToggle" title="Changer le mode"><i class="fa-solid fa-sun" id="themeIcon"></i></button><span class="user-chip"><img src="<?= escape(!empty($user['photo']) ? $user['photo'] : 'https://randomuser.me/api/portraits/men/32.jpg') ?>" class="user-avatar-small"><span class="user-chip-name"><?= escape($user['nom']) ?></span> <a href="index.php?action=logout" class="text-danger"><i class="ti-power-off"></i></a></span></div></div>
        </div>
    </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4"><h3>👤 Mon Profil</h3><a href="index.php?action=home" class="btn-outline-custom" style="padding:8px 24px;">← Retour</a></div>
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="profile-card">
                <img src="<?= !empty($user['photo']) ? $user['photo'] : 'https://randomuser.me/api/portraits/men/32.jpg' ?>" class="profile-avatar mb-3">
                <h4><?= escape($user['nom']) ?> <?= escape($user['prenom']) ?></h4>
                <p class="text-muted"><?= escape($user['filiere'] ?: 'Non renseigné') ?></p>
                <hr>
                <div class="stars"><?= generateStarRating($avgUserRating) ?></div>
                <p><strong><?= number_format($avgUserRating, 1) ?>/5</strong> (note moyenne)</p>
                <hr><p><i class="ti-location-pin"></i> <?= escape($user['universite'] ?: 'Non renseignée') ?></p>
                <p><i class="ti-email"></i> <?= escape($user['email']) ?></p>
                <a href="index.php?action=profile&subaction=edit" class="btn-primary-custom w-100 mt-3">✏️ Modifier le profil</a>
            </div>
        </div>
        <div class="col-md-8">
            <div class="row mb-4">
                <div class="col-6 col-md-3 mb-2"><div class="stat-card"><i class="ti-book" style="font-size:28px;"></i><h3><?= $totalResources ?></h3><small>Ressources</small></div></div>
                <div class="col-6 col-md-3 mb-2"><div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#ea580c);"><i class="ti-download" style="font-size:28px;"></i><h3><?= number_format($totalDownloads) ?></h3><small>Téléchargements</small></div></div>
                <div class="col-6 col-md-3 mb-2"><div class="stat-card" style="background:linear-gradient(135deg,#10b981,#059669);"><i class="ti-star" style="font-size:28px;"></i><h3><?= number_format($avgUserRating, 1) ?></h3><small>Note moyenne</small></div></div>
                <div class="col-6 col-md-3 mb-2"><div class="stat-card" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);"><i class="ti-wallet" style="font-size:28px;"></i><h3><?= $totalPremium ?></h3><small>Premium</small></div></div>
            </div>
            <div class="profile-card text-start">
                <div class="d-flex justify-content-between mb-3"><h5>📚 Mes ressources (<?= $totalResources ?>)</h5><a href="index.php?action=resource&subaction=upload" class="btn-primary-custom" style="padding:6px 18px;">+ Nouvelle</a></div>
                <?php foreach ($userResources as $res): ?>
                <div class="resource-item"><div class="d-flex justify-content-between flex-wrap"><div><strong><?= escape($res['titre']) ?></strong><br><small><?= $res['acces'] == 'Premium' ? "💰 {$res['prix']} DT" : '📥 Gratuit' ?> | 📄 <?= $res['pages'] ?> pages | 📥 <?= number_format($res['downloads']) ?> téléch.</small></div><div class="d-flex gap-2 mt-2"><a href="index.php?action=resource&subaction=edit&id=<?= $res['id_res'] ?>" class="btn-primary-custom" style="padding:6px 18px; background:#f59e0b;">Modifier</a><a href="index.php?action=resource&subaction=delete&id=<?= $res['id_res'] ?>" class="btn-primary-custom btn-danger-custom" style="padding:6px 18px;" onclick="return confirm('Supprimer ?')">Supprimer</a></div></div></div>
                <?php endforeach; ?>
            </div>
            <div class="profile-card text-start mt-4">
                <h5>🛍️ Mes achats (<?= $totalPurchased ?>)</h5>
                <?php if (empty($purchasedResources)): ?>
                    <p class="text-muted mb-0">Vous n'avez pas encore acheté de ressource premium.</p>
                <?php else: ?>
                    <?php foreach ($purchasedResources as $purchase): ?>
                        <div class="resource-item">
                            <div class="d-flex justify-content-between flex-wrap">
                                <div>
                                    <strong><?= escape($purchase['titre']) ?></strong><br>
                                    <small>💰 <?= number_format((float)$purchase['prix'], 2) ?> DT | Achat: <?= date('d/m/Y H:i', strtotime($purchase['purchased_at'])) ?></small>
                                </div>
                                <div class="mt-2">
                                    <a href="index.php?action=resource&subaction=download&id=<?= (int)$purchase['id_res'] ?>" class="btn-primary-custom" style="padding:6px 18px;">
                                        <i class="ti-download"></i> Télécharger
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<footer class="footer"><div class="container"><div class="row"><div class="col-lg-4"><h4 class="mb-3"><img src="uploads/logo.png" alt="StudyHub" class="site-logo site-logo--footer"></h4><p>Plateforme de partage de ressources académiques entre étudiants.</p></div></div></div></footer>
<div class="copyright"><p>&copy; 2025 StudyHub - Tous droits réservés</p></div>

<script src="js/validation.js"></script>
<script src="js/scripts.js"></script>
<script src="js/profile.js"></script>
</body>
</html>