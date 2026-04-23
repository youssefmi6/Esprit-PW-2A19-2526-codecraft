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
        .generated-photo-preview { width:120px; height:120px; border-radius:12px; object-fit:cover; margin-top:10px; border:2px solid #0ea5e9; }
        .description-toolbar { display:flex; justify-content:space-between; align-items:center; gap:10px; margin-bottom:8px; flex-wrap:wrap; }
        .description-help { font-size:13px; color:#64748b; margin:0; }
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
            <div class="mb-3">
                <label>Description</label>
                <div class="description-toolbar">
                    <p class="description-help">Tu peux écrire manuellement ou générer à partir du fichier uploadé.</p>
                    <button type="button" class="btn-outline-custom" style="padding:6px 14px;" onclick="generateDescription()">✨ Générer description</button>
                </div>
                <textarea name="description" id="description" class="form-control" rows="4" required></textarea>
            </div>
            <div class="row"><div class="col-md-6 mb-3"><label>Matière</label><select name="matiere" id="matiere" class="form-select" required><option value="">Sélectionner</option><option>Mathématiques</option><option>Physique</option><option>Chimie</option><option>Informatique</option><option>Programmation</option><option>HTML/CSS</option><option>JavaScript</option><option>Python</option><option>Java</option><option>Base de données</option><option>Réseaux</option><option>Économie</option><option>Gestion</option><option>Droit</option><option>Langues</option><option>Anglais</option><option>Français</option><option>Autre</option></select></div>
            <div class="col-md-6 mb-3"><label>Type</label><select name="type" id="type" class="form-select"><option>Résumé</option><option>Examen</option><option>Exercice</option><option>Cours complet</option></select></div></div>
            <div class="row"><div class="col-md-6 mb-3"><label>Niveau</label><select name="niveau" id="niveau" class="form-select"><option>Licence 1</option><option>Licence 2</option><option>Licence 3</option><option>Master 1</option><option>Master 2</option></select></div>
            <div class="col-md-6 mb-3"><label>Pages</label><input type="number" name="pages" id="pages" class="form-control" required></div></div>
            <div class="row"><div class="col-md-6 mb-3"><label>Accès</label><select name="acces" id="accessSelect" class="form-select"><option value="Gratuit">Gratuit</option><option value="Premium">Premium</option></select></div>
            <div class="col-md-6 mb-3" id="priceDiv" style="display:none;"><label>Prix (DT)</label><input type="number" name="prix" id="prix" class="form-control" step="0.01"></div></div>
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                    <label class="mb-0">Photo (optionnel)</label>
                    <button type="button" class="btn-outline-custom" style="padding:6px 14px;" onclick="generatePhoto()">🖼️ Générer photo</button>
                </div>
                <input type="hidden" name="generated_photo_url" id="generatedPhotoUrl" value="">
                <div class="upload-area" onclick="document.getElementById('photoInput').click()"><i class="ti-image" style="font-size:32px;"></i><p>Photo (optionnel)</p><input type="file" id="photoInput" name="photo" accept="image/*" style="display:none;" onchange="previewPhoto(this)"></div><div id="photoPreview" class="text-center mt-2"></div>
            </div>
            <div class="mb-4"><div class="upload-area" onclick="document.getElementById('fileInput').click()"><i class="ti-upload" style="font-size:48px;"></i><p>Fichier PDF</p><input type="file" id="fileInput" name="fichier" style="display:none;" required onchange="previewFile(this)"></div><div id="fileName" class="mt-2 text-center text-success"></div></div>
            <button type="submit" class="btn-primary-custom w-100">🚀 Publier</button>
        </form>
    </div></div></div>
</div>

<script>
document.getElementById('accessSelect').addEventListener('change', function() { document.getElementById('priceDiv').style.display = this.value === 'Premium' ? 'block' : 'none'; });
function previewFile(input) { if(input.files && input.files[0]) document.getElementById('fileName').innerHTML = '<i class="ti-check"></i> ' + input.files[0].name; }
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

function formatBytes(bytes) {
    if (!bytes || bytes <= 0) return '0 Ko';
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

function generateDescription() {
    var titre = (document.getElementById('titre').value || '').trim();
    var matiere = (document.getElementById('matiere').value || '').trim();
    var type = (document.getElementById('type').value || '').trim();
    var niveau = (document.getElementById('niveau').value || '').trim();
    var pages = (document.getElementById('pages').value || '').trim();
    var fileInput = document.getElementById('fileInput');
    var description = document.getElementById('description');

    if (!fileInput.files || !fileInput.files[0]) {
        alert('Ajoute d\'abord un fichier pour générer une description.');
        return;
    }

    var file = fileInput.files[0];
    var filename = file.name || 'fichier';
    var extension = '';
    if (filename.indexOf('.') !== -1) {
        extension = filename.split('.').pop().toLowerCase();
    }

    var fileKind = getFileKind(extension);
    var sizeText = formatBytes(file.size || 0);
    var titleText = titre || ('Ressource ' + (type || 'académique'));
    var pagesText = pages ? (pages + ' pages') : 'nombre de pages non précisé';

    var generated = titleText + " est un " + fileKind + " en " + (matiere || 'matière générale') + ".\n\n";
    generated += "Cette ressource est destinée au niveau " + (niveau || 'universitaire') + " et au format " + (type || 'support pédagogique') + ". ";
    generated += "Le fichier source (" + filename + ") contient " + pagesText + " et a une taille de " + sizeText + ".\n\n";
    generated += "Objectif: fournir un support clair pour la révision, l'entraînement et la préparation des examens.";

    description.value = generated;
}
</script>

<footer class="footer"><div class="container"><div class="row"><div class="col-lg-4"><h4 class="mb-3"><img src="uploads/logo.png" alt="StudyHub" class="site-logo site-logo--footer"></h4><p>Plateforme de partage de ressources académiques entre étudiants.</p></div></div></div></footer>
<div class="copyright"><p>&copy; 2025 StudyHub - Tous droits réservés</p></div>

<script src="js/validation.js"></script>
<script src="js/upload.js"></script>
</body>
</html>