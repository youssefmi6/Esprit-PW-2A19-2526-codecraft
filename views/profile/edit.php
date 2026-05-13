<?php
// views/profile/edit.php - Modifier mon profil
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier mon profil - StudyHub</title>
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
            <div class="col-6 col-lg-9"><div class="nav-right-controls"><button type="button" class="theme-toggle" id="themeToggle" title="Changer le mode"><i class="fa-solid fa-sun" id="themeIcon"></i></button><span class="user-chip"><img src="<?= escape(!empty($user['photo']) ? $user['photo'] : 'https://randomuser.me/api/portraits/men/32.jpg') ?>" class="user-avatar-small"><span class="user-chip-name"><?= escape($user['nom']) ?></span> <a href="index.php?action=logout" class="text-danger"><i class="ti-power-off"></i></a></span></div></div>
        </div>
    </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4"><h3>✏️ Modifier mon profil</h3><a href="index.php?action=profile" class="btn-outline-custom" style="padding:8px 24px;">← Retour</a></div>
    <div class="row justify-content-center"><div class="col-md-8"><div class="form-card">
        <form method="POST" enctype="multipart/form-data" id="profileForm">
            <div class="text-center"><img src="<?= !empty($user['photo']) ? $user['photo'] : 'https://randomuser.me/api/portraits/men/32.jpg' ?>" class="profile-avatar-preview" id="avatarPreview" onclick="document.getElementById('photoInput').click()"><div><label class="btn-outline-custom" style="cursor:pointer; padding:8px 20px;"><i class="ti-camera"></i> Changer la photo<input type="file" id="photoInput" name="photo" accept="image/*" style="display:none;" onchange="previewImage(this)"></label></div></div>
            <input type="hidden" name="generated_photo_url" id="generatedPhotoUrl" value="">
            <div class="mt-3">
                <label class="form-label fw-bold">Prompt (pour générer une photo)</label>
                <div class="d-flex gap-2 flex-wrap">
                    <input type="text" class="form-control" id="profilePhotoPrompt" placeholder="Ex: Portrait photo réaliste, fond bleu, style professionnel">
                    <button type="button" class="btn-outline-custom" id="generateProfilePhotoBtn" style="padding:10px 18px; border-radius:12px;">
                        🖼️ Générer photo
                    </button>
                </div>
                <div class="small text-muted mt-2" id="profilePhotoGenStatus"></div>
            </div>
            <div class="row"><div class="col-md-6 mb-3"><label>Nom</label><input type="text" name="nom" class="form-control" value="<?= escape($user['nom']) ?>" required></div><div class="col-md-6 mb-3"><label>Prénom</label><input type="text" name="prenom" class="form-control" value="<?= escape($user['prenom']) ?>" required></div></div>
            <div class="mb-3"><label>Université</label><input type="text" name="universite" class="form-control" value="<?= escape($user['universite']) ?>"></div>
            <div class="mb-3"><label>Filière</label><input type="text" name="filiere" class="form-control" value="<?= escape($user['filiere']) ?>"></div>
            <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" value="<?= escape($user['email']) ?>" required></div>
            <div class="mb-3"><label>Téléphone</label><input type="tel" name="tel" class="form-control" value="<?= escape($user['tel']) ?>"></div>
            <div class="mb-3"><label>Bio</label><textarea name="bio" class="form-control" rows="3"><?= escape($user['bio']) ?></textarea></div>
            <div class="mb-3"><label>Nouveau mot de passe</label><input type="password" name="mdp" class="form-control" placeholder="Laisser vide pour ne pas changer"></div>
            <div class="d-flex gap-3 mt-4"><button type="submit" class="btn-primary-custom">💾 Enregistrer</button><a href="index.php?action=profile" class="btn-outline-custom">Annuler</a></div>
        </form>
    </div></div></div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var gen = document.getElementById('generatedPhotoUrl');
        if (gen) gen.value = '';
        const reader = new FileReader();
        reader.onload = function(e) { document.getElementById('avatarPreview').src = e.target.result; };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<footer class="footer"><div class="container"><div class="row"><div class="col-lg-4"><h4 class="mb-3"><img src="uploads/logo.png" alt="StudyHub" class="site-logo site-logo--footer"></h4><p>Plateforme de partage de ressources académiques entre étudiants.</p></div></div></div></footer>
<div class="copyright"><p>&copy; 2025 StudyHub - Tous droits réservés</p></div>

<script src="js/validation.js"></script>
<script src="js/scripts.js"></script>
<script src="js/profile.js"></script>
</body>
</html>