<?php
// views/auth/register.php - Page d'inscription
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - StudyHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:linear-gradient(135deg,#1a8cff 0%,#00b4d8 50%,#90e0ef 100%); min-height:100vh; padding:40px 20px; }
        .register-container { max-width:600px; margin:0 auto; background:white; border-radius:32px; padding:40px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); animation:fadeIn 0.5s ease-out; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        .logo-icon { width:70px; height:70px; background:linear-gradient(135deg,#1a8cff 0%,#00b4d8 100%); border-radius:20px; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; }
        .logo-icon i { font-size:35px; color:white; }
        h2 { text-align:center; font-size:28px; font-weight:700; background:linear-gradient(135deg,#1a8cff 0%,#00b4d8 100%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; margin-bottom:8px; }
        .subtitle { text-align:center; color:#6c757d; font-size:14px; margin-bottom:30px; }
        .form-group { margin-bottom:20px; }
        .form-label { font-weight:600; font-size:14px; color:#1a3a5c; margin-bottom:8px; display:block; }
        .input-group-custom { position:relative; }
        .input-group-custom i { position:absolute; left:16px; top:50%; transform:translateY(-50%); color:#1a8cff; font-size:18px; }
        .form-control-custom { width:100%; padding:12px 16px 12px 48px; border:2px solid #e0e7ff; border-radius:16px; font-size:15px; transition:all 0.3s; }
        .form-control-custom:focus { outline:none; border-color:#1a8cff; box-shadow:0 0 0 4px rgba(26,140,255,0.1); }
        textarea.form-control-custom { padding:12px 16px; }
        .btn-register { width:100%; padding:14px; background:linear-gradient(135deg,#1a8cff 0%,#00b4d8 100%); border:none; border-radius:16px; color:white; font-weight:600; font-size:16px; transition:all 0.3s; margin-top:10px; cursor:pointer; }
        .btn-register:hover { transform:translateY(-2px); box-shadow:0 10px 25px -5px rgba(26,140,255,0.4); }
        .alert { border-radius:16px; padding:12px 16px; margin-bottom:24px; display:none; }
        .alert.show { display:block; }
        .login-link { text-align:center; margin-top:24px; padding-top:20px; border-top:1px solid #e0e7ff; }
        .login-link a { color:#1a8cff; text-decoration:none; font-weight:600; }
        .back-home { text-align:center; margin-top:20px; }
        .back-home a { color:#6c757d; text-decoration:none; font-size:14px; }
        .row { display:flex; gap:20px; margin-bottom:0; }
        .row .form-group { flex:1; margin-bottom:0; }
        @media (max-width:768px) { .row { flex-direction:column; gap:20px; } }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo-icon"><i class="bi bi-person-plus-fill"></i></div>
        <h2>Inscription</h2>
        <p class="subtitle">Créez votre compte StudyHub</p>
        <?php if(isset($error)): ?>
        <div class="alert alert-danger show" id="errorAlert"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" id="registerForm" novalidate>
            <div class="row"><div class="form-group"><label class="form-label">Nom</label><div class="input-group-custom"><i class="bi bi-person-fill"></i><input type="text" name="nom" id="nom" class="form-control-custom" placeholder="Votre nom"></div></div>
            <div class="form-group"><label class="form-label">Prénom</label><div class="input-group-custom"><i class="bi bi-person-fill"></i><input type="text" name="prenom" id="prenom" class="form-control-custom" placeholder="Votre prénom"></div></div></div>
            <div class="form-group"><label class="form-label">Université / École</label><div class="input-group-custom"><i class="bi bi-building-fill"></i><input type="text" name="universite" id="universite" class="form-control-custom" placeholder="Nom de votre université"></div></div>
            <div class="form-group"><label class="form-label">Filière / Spécialité</label><div class="input-group-custom"><i class="bi bi-book-fill"></i><input type="text" name="filiere" id="filiere" class="form-control-custom" placeholder="Votre filière"></div></div>
            <div class="form-group"><label class="form-label">Email</label><div class="input-group-custom"><i class="bi bi-envelope-fill"></i><input type="email" name="email" id="email" class="form-control-custom" placeholder="exemple@email.com"></div></div>
            <div class="row"><div class="form-group"><label class="form-label">Mot de passe</label><div class="input-group-custom"><i class="bi bi-lock-fill"></i><input type="password" name="password" id="password" class="form-control-custom" placeholder="Min. 6 caractères"></div></div>
            <div class="form-group"><label class="form-label">Confirmer</label><div class="input-group-custom"><i class="bi bi-lock-fill"></i><input type="password" name="confirm_password" id="confirm_password" class="form-control-custom" placeholder="Confirmez le mot de passe"></div></div></div>
            <div class="form-group"><label class="form-label">Téléphone (optionnel)</label><div class="input-group-custom"><i class="bi bi-phone-fill"></i><input type="tel" name="tel" id="tel" class="form-control-custom" placeholder="+216 XX XXX XXX"></div></div>
            <div class="form-group"><textarea name="bio" id="bio" class="form-control-custom" rows="3" placeholder="Bio (optionnel)"></textarea></div>
            <div class="form-group"><input type="file" name="photo" id="photo" class="form-control-custom" accept="image/*" style="padding:10px;"></div>
            <input type="hidden" name="face_enabled" id="face_enabled" value="0">
            <input type="hidden" name="face_descriptor" id="face_descriptor" value="">
            <div class="form-group">
                <button type="button" class="btn btn-outline-primary w-100" id="faceEnrollOpen" style="border-radius:16px; font-weight:700;">
                    <i class="bi bi-person-bounding-box me-2"></i>Configurer Face ID (optionnel)
                </button>
                <div id="facePreviewOk" class="text-success small mt-2" style="display:none;">
                    <i class="bi bi-check-circle-fill me-1"></i>Face ID enregistré
                </div>
            </div>
            <button type="submit" class="btn-register"><i class="bi bi-person-plus-fill me-2"></i>S'inscrire</button>
        </form>
        <div class="login-link"><p>Déjà inscrit ? <a href="index.php?action=login">Connectez-vous</a></p></div>
        <div class="back-home"><a href="index.php?action=home"><i class="bi bi-arrow-left me-1"></i>Retour à l'accueil</a></div>
    </div>
    <script src="js/validation.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.12/dist/face-api.min.js"></script>
    <script src="js/faceid.js"></script>
    <script src="js/register.js"></script>
    <script>document.addEventListener('DOMContentLoaded', function(){ window.studyhubInitFaceIdRegister && window.studyhubInitFaceIdRegister(); });</script>

    <div id="faceModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); align-items:center; justify-content:center; z-index:9999;">
        <div style="width:min(520px, 92vw); background:#fff; border-radius:18px; padding:16px;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>Face ID</strong>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="faceModalClose">Fermer</button>
            </div>
            <video id="faceVideo" autoplay playsinline style="width:100%; border-radius:12px; background:#0b1220;"></video>
            <button type="button" class="btn btn-primary w-100 mt-3" id="faceCapture" style="border-radius:14px; font-weight:700;">
                Capturer
            </button>
            <div id="faceStatus" class="small mt-2 text-muted"></div>
        </div>
    </div>
</body>
</html>