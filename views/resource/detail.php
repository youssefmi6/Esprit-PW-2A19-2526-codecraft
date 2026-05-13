<?php
// views/resource/detail.php - Détail d'une ressource
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= escape($resource['titre']) ?> - StudyHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Jost:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .resource-photo { max-height:200px; object-fit:contain; margin-bottom:20px; }
        .author-link { color:#1e293b; text-decoration:none; }
        .author-link:hover { color:var(--primary); text-decoration:underline; }
        .rating-star { cursor:pointer; font-size:28px; color:#cbd5e1; display:inline-block; margin-right:5px; }
        .rating-star:hover, .rating-star.active { color:#fbbf24; }
    </style>
</head>
<body>

<nav class="navbar-custom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-6 col-lg-3"><a href="index.php?action=home" class="logo">📚 StudyHub</a></div>
            <div class="col-lg-6 d-none d-lg-block">
                <ul class="nav-links">
                    <li><a href="index.php?action=home">Accueil</a></li>
                    <li><a href="index.php?action=home#resources">Ressources</a></li>
                    <li><a href="index.php?action=resource&subaction=upload">Publier</a></li>
                    <li><a href="index.php?action=profile">Mon Profil</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-3 text-end">
                <?php if ($currentUser): ?>
                    <span><i class="ti-user me-1"></i> <?= escape($currentUser['nom']) ?> <a href="index.php?action=logout" class="ms-2 text-danger"><i class="ti-power-off"></i></a></span>
                <?php else: ?>
                    <a href="index.php?action=login" class="btn-outline-custom me-2" style="padding:6px 20px;">Connexion</a>
                    <a href="index.php?action=register" class="btn-primary-custom" style="padding:6px 20px;">Inscription</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4"><h3>📄 Détail de la ressource</h3><a href="index.php?action=home#resources" class="btn-outline-custom" style="padding:8px 24px;">← Retour</a></div>
    
    <?php if(isset($_GET['rated'])): ?>
        <div class="alert alert-success">✅ Merci pour votre évaluation !</div>
    <?php elseif(isset($_GET['already'])): ?>
        <div class="alert alert-warning">⚠️ Vous avez déjà évalué cette ressource.</div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="resource-detail-card">
                <div class="text-center"><img src="<?= !empty($resource['photo']) ? $resource['photo'] : ($matiere_icons[$resource['matiere']] ?? $matiere_icons['Autre']) ?>" class="resource-photo"></div>
                <div class="d-flex justify-content-between"><div><h2><?= escape($resource['titre']) ?></h2><p class="text-muted">Par <a href="index.php?action=profile&subaction=view&id=<?= $resource['user_id'] ?>" class="author-link"><strong><?= escape($resource['nom']) ?> <?= escape($resource['prenom']) ?></strong></a></p></div><span class="<?= $resource['acces'] == 'Premium' ? 'badge-premium' : 'badge-free' ?>"><?= $resource['acces'] ?></span></div>
                <div class="stars"><?php for($i=1;$i<=5;$i++) echo $i<=$full_stars ? '★' : ($half_star && $i==$full_stars+1 ? '½' : '☆'); ?> (<?= number_format($resource_rating,1) ?>/5 - <?= $totalVotes ?> vote<?= $totalVotes>1?'s':'' ?>)</div>
                <div class="d-flex flex-wrap gap-4 my-3"><span><i class="ti-folder"></i> <?= escape($resource['type']) ?></span><span><i class="ti-layout"></i> <?= escape($resource['niveau']) ?></span><span><i class="ti-book"></i> <?= escape($resource['matiere']) ?></span><span><i class="ti-calendar"></i> Publiée le <?= date('d/m/Y', strtotime($resource['date_creation'])) ?></span></div>
                <div class="mb-4"><h5>Description</h5><p><?= nl2br(escape($resource['description'])) ?></p></div>
                <div class="mt-4"><?php if ($resource['acces'] == 'Premium'): ?><a href="index.php?action=resource&subaction=buy&id=<?= $resource['id_res'] ?>" class="btn-primary-custom" style="background:#f59e0b;">Acheter (<?= $resource['prix'] ?> DT)</a><?php else: ?><a href="index.php?action=resource&subaction=download&id=<?= $resource['id_res'] ?>" class="btn-primary-custom"><i class="ti-download"></i> Télécharger gratuitement</a><?php endif; ?><button class="btn-outline-custom ms-2" onclick="alert('Ressource signalée avec succès')"><i class="ti-flag-alt"></i> Signaler</button></div>
            </div>
            
            <div class="resource-detail-card"><h5>⭐ Évaluez cette ressource</h5>
                <?php if (!$currentUser): ?><div class="alert alert-info"><a href="index.php?action=login">Connectez-vous</a> pour évaluer</div>
                <?php elseif ($hasRated): ?><div class="alert-rating">Vous avez déjà évalué cette ressource. Merci pour votre participation !</div>
                <?php else: ?>
                <form method="POST" action="index.php?action=resource&subaction=add_rating"><input type="hidden" name="id_res" value="<?= $resource['id_res'] ?>"><input type="hidden" name="rating" id="selectedRating" value="0"><div id="ratingStars"><?php for($i=1;$i<=5;$i++) echo '<i class="fa fa-star rating-star" data-rating="'.$i.'"></i>'; ?></div><button type="submit" class="btn-primary-custom mt-2" id="submitRating" disabled>Soumettre ma note</button></form>
                <?php endif; ?>
            </div>
            
            <div class="resource-detail-card"><h5>💬 Commentaires (<?= count($comments) ?>)</h5>
                <?php foreach ($comments as $comment): ?>
                <div class="comment-card"><div class="d-flex gap-3"><img src="<?= $comment['photo'] ?: 'https://randomuser.me/api/portraits/men/32.jpg' ?>" style="width:45px;height:45px;border-radius:50%;"><div><strong><?= escape($comment['nom']) ?> <?= escape($comment['prenom']) ?></strong><p><?= escape($comment['message']) ?></p><small><?= date('d/m/Y H:i', strtotime($comment['date'])) ?></small></div></div></div>
                <?php endforeach; ?>
                <?php if ($currentUser): ?>
                <form method="POST" action="index.php?action=resource&subaction=add_comment" class="mt-4" id="commentForm" novalidate><textarea name="message" class="form-control mb-2" rows="3" placeholder="Votre commentaire..."></textarea><input type="hidden" name="id_res" value="<?= $resource['id_res'] ?>"><button type="submit" class="btn-primary-custom">Publier</button></form>
                <?php else: ?><div class="alert alert-info mt-3"><a href="index.php?action=login">Connectez-vous</a> pour commenter</div><?php endif; ?>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="resource-detail-card"><h5>💰 Informations</h5><hr><div class="text-center"><div class="display-6 fw-bold text-primary"><?= $resource['acces'] == 'Premium' ? $resource['prix'].' DT' : 'GRATUIT' ?></div><?php if ($resource['acces'] == 'Premium'): ?><a href="index.php?action=resource&subaction=buy&id=<?= $resource['id_res'] ?>" class="btn-primary-custom w-100 mt-3" style="background:#f59e0b;">Acheter</a><?php else: ?><a href="index.php?action=resource&subaction=download&id=<?= $resource['id_res'] ?>" class="btn-primary-custom w-100 mt-3">Télécharger</a><?php endif; ?></div></div>
            <div class="resource-detail-card"><h5>📋 Détails techniques</h5><hr><p><i class="ti-file"></i> Format: PDF</p><p><i class="ti-book"></i> Pages: <?= $resource['pages'] ?> pages</p><p><i class="ti-download"></i> Téléchargements: <?= number_format($resource['downloads']) ?></p><p><i class="ti-star"></i> Note: <?= number_format($resource_rating,1) ?>/5</p></div>
        </div>
    </div>
</div>

<script>
let selectedRating = 0;
const stars = document.querySelectorAll('#ratingStars .rating-star');
const submitBtn = document.getElementById('submitRating');
if (stars.length) stars.forEach(star => { star.addEventListener('click', function() { selectedRating = this.getAttribute('data-rating'); document.getElementById('selectedRating').value = selectedRating; stars.forEach(s => s.classList.remove('active')); for(let i=0;i<selectedRating;i++) stars[i].classList.add('active'); submitBtn.disabled = false; }); });
</script>

<footer class="footer"><div class="container"><div class="row"><div class="col-lg-4"><h4>📚 StudyHub</h4><p>Plateforme de partage de ressources académiques entre étudiants.</p></div></div></div></footer>
<div class="copyright"><p>&copy; 2025 StudyHub - Tous droits réservés</p></div>

<script src="js/validation.js"></script>
<script src="js/scripts.js"></script>
</body>
</html>