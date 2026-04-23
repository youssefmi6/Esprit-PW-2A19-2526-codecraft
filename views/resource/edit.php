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
    <style>
        .description-toolbar { display:flex; justify-content:space-between; align-items:center; gap:10px; margin-bottom:8px; flex-wrap:wrap; }
        .description-help { font-size:13px; color:#64748b; margin:0; }
        .generated-photo-preview { width:120px; height:120px; border-radius:12px; object-fit:cover; margin-top:10px; border:2px solid #0ea5e9; }
        .image-preview { width:120px; height:120px; border-radius:12px; object-fit:cover; margin-top:10px; border:2px solid var(--primary); }
    </style>
</head>
<body>

<nav class="navbar-custom">
    <div class="container"><div class="row align-items-center"><div class="col-6 col-lg-3"><a href="index.php?action=home" class="logo"><img src="uploads/logo.png" alt="StudyHub" class="site-logo"></a></div><div class="col-6 col-lg-9 text-end"><span><i class="ti-user me-1"></i> <?= escape($user['nom']) ?> <a href="index.php?action=logout" class="ms-2 text-danger"><i class="ti-power-off"></i></a></span></div></div></div>
</nav>

<div class="container"><div class="d-flex justify-content-between align-items-center mb-4"><h3>✏️ Modifier la ressource</h3><a href="index.php?action=profile" class="btn-outline-custom" style="padding:8px 24px;">← Retour</a></div>
<div class="row justify-content-center"><div class="col-md-8"><div class="form-card">
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3"><label>Titre</label><input type="text" name="titre" id="titre" class="form-control" value="<?= escape($resource['titre']) ?>" required></div>
        <div class="mb-3">
            <label>Description</label>
            <div class="description-toolbar">
                <p class="description-help">Tu peux modifier manuellement ou générer une nouvelle description.</p>
                <button type="button" class="btn-outline-custom" style="padding:6px 14px;" onclick="generateDescription()">✨ Générer description</button>
            </div>
            <textarea name="description" id="description" class="form-control" rows="3" required><?= escape($resource['description']) ?></textarea>
        </div>
        <div class="row"><div class="col-md-6 mb-3"><label>Matière</label><select name="matiere" id="matiere" class="form-select"><option <?= $resource['matiere']=='Mathématiques'?'selected':'' ?>>Mathématiques</option><option <?= $resource['matiere']=='Physique'?'selected':'' ?>>Physique</option><option <?= $resource['matiere']=='Informatique'?'selected':'' ?>>Informatique</option><option <?= $resource['matiere']=='Programmation'?'selected':'' ?>>Programmation</option></select></div>
        <div class="col-md-6 mb-3"><label>Type</label><select name="type" id="type" class="form-select"><option <?= $resource['type']=='Résumé'?'selected':'' ?>>Résumé</option><option <?= $resource['type']=='Examen'?'selected':'' ?>>Examen</option><option <?= $resource['type']=='Exercice'?'selected':'' ?>>Exercice</option><option <?= $resource['type']=='Cours complet'?'selected':'' ?>>Cours complet</option></select></div></div>
        <div class="row"><div class="col-md-6 mb-3"><label>Niveau</label><select name="niveau" id="niveau" class="form-select"><option <?= $resource['niveau']=='Licence 1'?'selected':'' ?>>Licence 1</option><option <?= $resource['niveau']=='Licence 2'?'selected':'' ?>>Licence 2</option><option <?= $resource['niveau']=='Licence 3'?'selected':'' ?>>Licence 3</option><option <?= $resource['niveau']=='Master 1'?'selected':'' ?>>Master 1</option></select></div>
        <div class="col-md-6 mb-3"><label>Pages</label><input type="number" name="pages" id="pages" class="form-control" value="<?= $resource['pages'] ?>" required></div></div>
        <div class="row"><div class="col-md-6 mb-3"><label>Accès</label><select name="acces" class="form-select" id="accessSelect"><option value="Gratuit" <?= $resource['acces']=='Gratuit'?'selected':'' ?>>Gratuit</option><option value="Premium" <?= $resource['acces']=='Premium'?'selected':'' ?>>Premium</option></select></div>
        <div class="col-md-6 mb-3" id="priceDiv" style="display:<?= $resource['acces']=='Premium'?'block':'none' ?>"><label>Prix</label><input type="number" name="prix" class="form-control" step="0.01" value="<?= $resource['prix'] ?>"></div></div>
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                <label class="mb-0">Nouvelle photo</label>
                <button type="button" class="btn-outline-custom" style="padding:6px 14px;" onclick="generatePhoto()">🖼️ Générer photo</button>
            </div>
            <input type="hidden" name="generated_photo_url" id="generatedPhotoUrl" value="">
            <input type="file" name="photo" id="photoInput" class="form-control" accept="image/*" onchange="previewPhoto(this)">
            <div id="photoPreview" class="text-center mt-2"></div>
        </div>
        <div class="mb-3"><label>Nouveau fichier</label><input type="file" name="fichier" id="fileInput" class="form-control" accept=".pdf,.doc,.docx,.txt"></div>
        <button type="submit" class="btn-primary-custom w-100">💾 Enregistrer</button>
    </form>
</div></div></div></div>

<script>
document.getElementById('accessSelect').addEventListener('change', function() {
    document.getElementById('priceDiv').style.display = this.value == 'Premium' ? 'block' : 'none';
});

function formatBytes(bytes) {
    if (!bytes || bytes <= 0) return 'taille non précisée';
    var units = ['octets', 'Ko', 'Mo', 'Go'];
    var idx = Math.floor(Math.log(bytes) / Math.log(1024));
    idx = Math.min(idx, units.length - 1);
    var value = bytes / Math.pow(1024, idx);
    return value.toFixed(idx === 0 ? 0 : 2) + ' ' + units[idx];
}

function getFileKind(ext) {
    var map = {
        pdf: 'document PDF',
        doc: 'document Word',
        docx: 'document Word',
        txt: 'document texte'
    };
    return map[ext] || 'document';
}

function getCurrentFilename() {
    var currentPath = <?= json_encode((string)($resource['fichier'] ?? ''), JSON_UNESCAPED_UNICODE) ?>;
    if (!currentPath) return 'fichier';
    return currentPath.split('/').pop();
}

function generateDescription() {
    var titre = (document.getElementById('titre').value || '').trim();
    var matiere = (document.getElementById('matiere').value || '').trim();
    var type = (document.getElementById('type').value || '').trim();
    var niveau = (document.getElementById('niveau').value || '').trim();
    var pages = (document.getElementById('pages').value || '').trim();
    var fileInput = document.getElementById('fileInput');
    var description = document.getElementById('description');

    var filename = getCurrentFilename();
    var extension = '';
    var sizeText = 'taille non précisée';

    if (fileInput.files && fileInput.files[0]) {
        filename = fileInput.files[0].name || filename;
        sizeText = formatBytes(fileInput.files[0].size || 0);
    }

    if (filename.indexOf('.') !== -1) {
        extension = filename.split('.').pop().toLowerCase();
    }

    var fileKind = getFileKind(extension);
    var titleText = titre || ('Ressource ' + (type || 'académique'));
    var pagesText = pages ? (pages + ' pages') : 'nombre de pages non précisé';

    var generated = titleText + " est un " + fileKind + " en " + (matiere || 'matière générale') + ".\n\n";
    generated += "Cette ressource est destinée au niveau " + (niveau || 'universitaire') + " et au format " + (type || 'support pédagogique') + ". ";
    generated += "Le fichier source (" + filename + ") contient " + pagesText + " et a une taille de " + sizeText + ".\n\n";
    generated += "Objectif: fournir un support clair pour la révision, l'entraînement et la préparation des examens.";

    description.value = generated;
}

function previewPhoto(input) {
    if (input.files && input.files[0]) {
        document.getElementById('generatedPhotoUrl').value = '';
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photoPreview').innerHTML = '<img src="' + e.target.result + '" class="image-preview"><p class="mt-2 mb-0 text-muted">Photo locale sélectionnée.</p>';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function buildPhotoPrompt() {
    var matiere = (document.getElementById('matiere').value || 'education').trim();
    var type = (document.getElementById('type').value || 'study').trim();
    var niveau = (document.getElementById('niveau').value || 'universite').trim();
    var pages = (document.getElementById('pages').value || '').trim();
    var titre = (document.getElementById('titre').value || 'resource').trim();
    var pagesText = pages ? (pages + ' pages') : 'pages non precisees';
    return "Academic cover art, modern educational illustration, subject: " + matiere +
        ", type: " + type + ", level: " + niveau + ", title: " + titre +
        ", " + pagesText + ", clean style, no text, no watermark, high quality";
}

function generatePhoto() {
    var prompt = buildPhotoPrompt();
    var photoPreview = document.getElementById('photoPreview');
    var photoInput = document.getElementById('photoInput');
    var seed = Date.now();
    var base = 'https://image.pollinations.ai/prompt/' + encodeURIComponent(prompt);
    var candidates = [
        base + '?width=800&height=600&model=flux&enhance=true&nologo=true&seed=' + seed,
        base + '?width=800&height=600&model=turbo&enhance=true&nologo=true&seed=' + (seed + 1)
    ];

    photoPreview.innerHTML = '<p class="mt-2 mb-0 text-muted">Generation IA en cours...</p>';
    if (photoInput) photoInput.value = '';
    document.getElementById('generatedPhotoUrl').value = '';

    loadFirstWorkingImage(candidates, function(url) {
        document.getElementById('generatedPhotoUrl').value = url;
        photoPreview.innerHTML = '<img src="' + url + '" class="generated-photo-preview" alt="Photo générée" referrerpolicy="no-referrer"><p class="mt-2 mb-0 text-muted">Photo générée avec IA selon les données.</p>';
    }, showPhotoGenerationError);
}

function loadFirstWorkingImage(urls, onSuccess, onFailure) {
    var idx = 0;
    function tryNext() {
        if (idx >= urls.length) {
            onFailure();
            return;
        }
        var url = urls[idx++];
        var testImg = new Image();
        testImg.referrerPolicy = 'no-referrer';
        testImg.onload = function() { onSuccess(url); };
        testImg.onerror = tryNext;
        testImg.src = url;
    }
    tryNext();
}

function showPhotoGenerationError() {
    document.getElementById('generatedPhotoUrl').value = '';
    document.getElementById('photoPreview').innerHTML = '<p class="mt-2 mb-0 text-danger">Echec de generation IA. Reessaie ou ajoute une photo manuellement.</p>';
}
</script>

<footer class="footer"><div class="container"><div class="row"><div class="col-lg-4"><h4 class="mb-3"><img src="uploads/logo.png" alt="StudyHub" class="site-logo site-logo--footer"></h4><p>Plateforme de partage de ressources académiques entre étudiants.</p></div></div></div></footer>
<div class="copyright"><p>&copy; 2025 StudyHub - Tous droits réservés</p></div>

<script src="js/validation.js"></script>
<script src="js/upload.js"></script>
</body>
</html>