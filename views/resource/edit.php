<?php
// views/resource/edit.php - Modifier une ressource
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier la ressource - StudyHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Jost:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar-custom">
    <div class="container"><div class="row align-items-center"><div class="col-6 col-lg-3"><a href="index.php?action=home" class="logo">📚 StudyHub</a></div><div class="col-6 col-lg-9 text-end"><span><i class="ti-user me-1"></i> <?= escape($user['nom']) ?> <a href="index.php?action=logout" class="ms-2 text-danger"><i class="ti-power-off"></i></a></span></div></div></div>
</nav>

<div class="container"><div class="d-flex justify-content-between align-items-center mb-4"><h3>✏️ Modifier la ressource</h3><a href="index.php?action=profile" class="btn-outline-custom" style="padding:8px 24px;">← Retour</a></div>
<div class="row justify-content-center"><div class="col-md-8"><div class="form-card">
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3"><label>Titre</label><input type="text" name="titre" class="form-control" value="<?= escape($resource['titre']) ?>" required></div>
        <div class="mb-3"><label>Description</label><textarea name="description" class="form-control" rows="3" required><?= escape($resource['description']) ?></textarea></div>
        <div class="row"><div class="col-md-6 mb-3"><label>Matière</label><select name="matiere" class="form-select"><option <?= $resource['matiere']=='Mathématiques'?'selected':'' ?>>Mathématiques</option><option <?= $resource['matiere']=='Physique'?'selected':'' ?>>Physique</option><option <?= $resource['matiere']=='Informatique'?'selected':'' ?>>Informatique</option><option <?= $resource['matiere']=='Programmation'?'selected':'' ?>>Programmation</option></select></div>
        <div class="col-md-6 mb-3"><label>Type</label><select name="type" class="form-select"><option <?= $resource['type']=='Résumé'?'selected':'' ?>>Résumé</option><option <?= $resource['type']=='Examen'?'selected':'' ?>>Examen</option><option <?= $resource['type']=='Exercice'?'selected':'' ?>>Exercice</option><option <?= $resource['type']=='Cours complet'?'selected':'' ?>>Cours complet</option></select></div></div>
        <div class="row"><div class="col-md-6 mb-3"><label>Niveau</label><select name="niveau" class="form-select"><option <?= $resource['niveau']=='Licence 1'?'selected':'' ?>>Licence 1</option><option <?= $resource['niveau']=='Licence 2'?'selected':'' ?>>Licence 2</option><option <?= $resource['niveau']=='Licence 3'?'selected':'' ?>>Licence 3</option><option <?= $resource['niveau']=='Master 1'?'selected':'' ?>>Master 1</option></select></div>
        <div class="col-md-6 mb-3"><label>Pages</label><input type="number" name="pages" class="form-control" value="<?= $resource['pages'] ?>" required></div></div>
        <div class="row"><div class="col-md-6 mb-3"><label>Accès</label><select name="acces" class="form-select" id="accessSelect"><option value="Gratuit" <?= $resource['acces']=='Gratuit'?'selected':'' ?>>Gratuit</option><option value="Premium" <?= $resource['acces']=='Premium'?'selected':'' ?>>Premium</option></select></div>
        <div class="col-md-6 mb-3" id="priceDiv" style="display:<?= $resource['acces']=='Premium'?'block':'none' ?>"><label>Prix</label><input type="number" name="prix" class="form-control" step="0.01" value="<?= $resource['prix'] ?>"></div></div>
        <div class="mb-3"><label>Nouvelle photo</label><input type="file" name="photo" class="form-control" accept="image/*"></div>
        <div class="mb-3"><label>Nouveau fichier</label><input type="file" name="fichier" class="form-control" accept=".pdf,.doc,.docx"></div>
        <button type="submit" class="btn-primary-custom w-100">💾 Enregistrer</button>
    </form>
</div></div></div></div>

<script>document.getElementById('accessSelect').addEventListener('change',function(){document.getElementById('priceDiv').style.display=this.value=='Premium'?'block':'none';});</script>

<footer class="footer"><div class="container"><div class="row"><div class="col-lg-4"><h4>📚 StudyHub</h4><p>Plateforme de partage de ressources académiques entre étudiants.</p></div></div></div></footer>
<div class="copyright"><p>&copy; 2025 StudyHub - Tous droits réservés</p></div>

<script src="js/validation.js"></script>
<script src="js/upload.js"></script>
</body>
</html>