<?php
// views/resource/upload.php - Formulaire d'upload de ressource
// La variable $user est passée depuis le contrôleur
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Publier une ressource - StudyHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Jost:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .upload-area { border:2px dashed #cbd5e1; border-radius:16px; padding:40px; text-align:center; cursor:pointer; transition:.3s; }
        .upload-area:hover { border-color:var(--primary); background:#f0f9ff; }
        .image-preview { width:120px; height:120px; border-radius:12px; object-fit:cover; margin-top:10px; border:2px solid var(--primary); }
    </style>
</head>
<body>

<nav class="navbar-custom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-6 col-lg-3"><a href="index.php?action=home" class="logo"><img src="uploads/logo.png" alt="StudyHub" class="site-logo"></a></div>
            <div class="col-6 col-lg-9 text-end">
                <?php if (isset($user) && $user): ?>
                    <span><i class="ti-user me-1"></i> <?= escape($user['nom']) ?> <a href="index.php?action=logout" class="ms-2 text-danger"><i class="ti-power-off"></i></a></span>
                <?php else: ?>
                    <a href="index.php?action=login" class="btn-outline-custom me-2" style="padding:6px 20px;">Connexion</a>
                    <a href="index.php?action=register" class="btn-primary-custom" style="padding:6px 20px;">Inscription</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4"><h3>📤 Publier une ressource</h3><a href="index.php?action=home" class="btn-outline-custom" style="padding:8px 24px;">← Retour</a></div>
    <div class="row justify-content-center"><div class="col-md-8"><div class="form-card">
        <form method="POST" enctype="multipart/form-data" id="uploadForm">
            <div class="mb-3"><label>Titre</label><input type="text" name="titre" id="titre" class="form-control" required></div>
            <div class="mb-3"><label>Description</label><textarea name="description" id="description" class="form-control" rows="3" required></textarea></div>
            <div class="row"><div class="col-md-6 mb-3"><label>Matière</label><select name="matiere" id="matiere" class="form-select" required><option value="">Sélectionner</option><option>Mathématiques</option><option>Physique</option><option>Chimie</option><option>Informatique</option><option>Programmation</option><option>HTML/CSS</option><option>JavaScript</option><option>Python</option><option>Java</option><option>Base de données</option><option>Réseaux</option><option>Économie</option><option>Gestion</option><option>Droit</option><option>Langues</option><option>Anglais</option><option>Français</option><option>Autre</option></select></div>
            <div class="col-md-6 mb-3"><label>Type</label><select name="type" id="type" class="form-select"><option>Résumé</option><option>Examen</option><option>Exercice</option><option>Cours complet</option></select></div></div>
            <div class="row"><div class="col-md-6 mb-3"><label>Niveau</label><select name="niveau" id="niveau" class="form-select"><option>Licence 1</option><option>Licence 2</option><option>Licence 3</option><option>Master 1</option><option>Master 2</option></select></div>
            <div class="col-md-6 mb-3"><label>Pages</label><input type="number" name="pages" id="pages" class="form-control" required></div></div>
            <div class="row"><div class="col-md-6 mb-3"><label>Accès</label><select name="acces" id="accessSelect" class="form-select"><option value="Gratuit">Gratuit</option><option value="Premium">Premium</option></select></div>
            <div class="col-md-6 mb-3" id="priceDiv" style="display:none;"><label>Prix (DT)</label><input type="number" name="prix" id="prix" class="form-control" step="0.01"></div></div>
            <div class="mb-3"><div class="upload-area" onclick="document.getElementById('photoInput').click()"><i class="ti-image" style="font-size:32px;"></i><p>Photo (optionnel)</p><input type="file" id="photoInput" name="photo" accept="image/*" style="display:none;" onchange="previewPhoto(this)"></div><div id="photoPreview" class="text-center mt-2"></div></div>
            <div class="mb-4"><div class="upload-area" onclick="document.getElementById('fileInput').click()"><i class="ti-upload" style="font-size:48px;"></i><p>Fichier PDF</p><input type="file" id="fileInput" name="fichier" style="display:none;" required onchange="previewFile(this)"></div><div id="fileName" class="mt-2 text-center text-success"></div></div>
            <button type="submit" class="btn-primary-custom w-100">🚀 Publier</button>
        </form>
    </div></div></div>
</div>

<script>
document.getElementById('accessSelect').addEventListener('change', function() { document.getElementById('priceDiv').style.display = this.value === 'Premium' ? 'block' : 'none'; });
function previewFile(input) { if(input.files && input.files[0]) document.getElementById('fileName').innerHTML = '<i class="ti-check"></i> ' + input.files[0].name; }
function previewPhoto(input) { if(input.files && input.files[0]) { const reader = new FileReader(); reader.onload = function(e) { document.getElementById('photoPreview').innerHTML = '<img src="' + e.target.result + '" class="image-preview">'; }; reader.readAsDataURL(input.files[0]); } }
</script>

<footer class="footer"><div class="container"><div class="row"><div class="col-lg-4"><h4 class="mb-3"><img src="uploads/logo.png" alt="StudyHub" class="site-logo site-logo--footer"></h4><p>Plateforme de partage de ressources académiques entre étudiants.</p></div></div></div></footer>
<div class="copyright"><p>&copy; 2025 StudyHub - Tous droits réservés</p></div>

<script src="js/validation.js"></script>
<script src="js/upload.js"></script>
</body>
</html>