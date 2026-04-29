<?php
// views/auth/login.php - Page de connexion
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - StudyHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:linear-gradient(135deg,#1a8cff 0%,#00b4d8 50%,#90e0ef 100%); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }
        .login-container { width:450px; background:white; border-radius:32px; padding:40px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); animation:fadeIn 0.5s ease-out; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        .login-brand { text-align:center; margin-bottom:12px; }
        .login-brand-img { max-height:64px; width:auto; max-width:240px; object-fit:contain; }
        .subtitle { text-align:center; color:#6c757d; font-size:14px; margin-bottom:30px; }
        .form-group { margin-bottom:24px; }
        .form-label { font-weight:600; font-size:14px; color:#1a3a5c; margin-bottom:8px; display:block; }
        .input-group-custom { position:relative; }
        .input-group-custom i { position:absolute; left:16px; top:50%; transform:translateY(-50%); color:#1a8cff; font-size:18px; }
        .form-control-custom { width:100%; padding:14px 16px 14px 48px; border:2px solid #e0e7ff; border-radius:16px; font-size:15px; transition:all 0.3s; }
        .form-control-custom:focus { outline:none; border-color:#1a8cff; box-shadow:0 0 0 4px rgba(26,140,255,0.1); }
        .btn-login { width:100%; padding:14px; background:linear-gradient(135deg,#1a8cff 0%,#00b4d8 100%); border:none; border-radius:16px; color:white; font-weight:600; font-size:16px; transition:all 0.3s; cursor:pointer; }
        .btn-login:hover { transform:translateY(-2px); box-shadow:0 10px 25px -5px rgba(26,140,255,0.4); }
        .alert { border-radius:16px; padding:12px 16px; margin-bottom:24px; display:none; }
        .alert.show { display:block; }
        .register-link { text-align:center; margin-top:24px; padding-top:20px; border-top:1px solid #e0e7ff; }
        .register-link a { color:#1a8cff; text-decoration:none; font-weight:600; }
        .back-home { text-align:center; margin-top:20px; }
        .back-home a { color:#6c757d; text-decoration:none; font-size:14px; }
        .back-home a:hover { color:#1a8cff; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-brand"><img src="uploads/logo.png" alt="StudyHub" class="login-brand-img"></div>
        <p class="subtitle">Connectez-vous à votre compte</p>
        <?php if(isset($_SESSION['password_reset_success'])): ?>
        <div class="alert alert-success show"><i class="bi bi-check-circle-fill me-2"></i><?= $_SESSION['password_reset_success'] ?></div>
        <?php unset($_SESSION['password_reset_success']); endif; ?>
        <?php if(isset($error)): ?>
        <div class="alert alert-danger show" id="errorAlert"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?></div>
        <?php endif; ?>
        <form method="POST" id="loginForm">
            <div class="form-group"><label class="form-label">Email</label><div class="input-group-custom"><i class="bi bi-envelope-fill"></i><input type="email" name="email" id="email" class="form-control-custom" placeholder="exemple@email.com" required></div></div>
            <div class="form-group"><label class="form-label">Mot de passe</label><div class="input-group-custom"><i class="bi bi-lock-fill"></i><input type="password" name="password" id="password" class="form-control-custom" placeholder="••••••••" required></div></div>
            <div style="text-align:right; margin-top:-10px; margin-bottom:18px;">
                <a href="index.php?action=forgot_password" style="font-size:14px; color:#1a8cff; text-decoration:none; font-weight:600;">Mot de passe oublié ?</a>
            </div>
            <?php if (!empty($showActivationLink)): ?>
                <div style="text-align:right; margin-top:-12px; margin-bottom:18px;">
                    <a href="index.php?action=resend_activation" style="font-size:14px; color:#1a8cff; text-decoration:none; font-weight:600;">Activer mon compte</a>
                </div>
            <?php endif; ?>
            <button type="submit" class="btn-login"><i class="bi bi-box-arrow-in-right me-2"></i>Se connecter</button>
        </form>
        <button type="button" class="btn-login mt-2" id="faceLoginOpen" style="background:linear-gradient(135deg,#0f766e 0%,#14b8a6 100%);">
            <i class="bi bi-person-bounding-box me-2"></i>Se connecter avec Face ID
        </button>

        <form method="POST" action="index.php?action=login_face" id="faceLoginForm" style="display:none;">
            <input type="hidden" name="email" id="faceLoginEmail" value="">
            <input type="hidden" name="face_descriptor" id="faceLoginDescriptor" value="">
        </form>
        <div class="register-link"><p>Pas encore de compte ? <a href="index.php?action=register">Inscrivez-vous</a></p></div>
        <div class="back-home"><a href="index.php?action=home"><i class="bi bi-arrow-left me-1"></i>Retour à l'accueil</a></div>
    </div>
    <script src="js/validation.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.12/dist/face-api.min.js"></script>
    <script src="js/faceid.js"></script>
    <script src="js/login.js"></script>
    <script>document.addEventListener('DOMContentLoaded', function(){ window.studyhubInitFaceIdLogin && window.studyhubInitFaceIdLogin(); });</script>

    <div id="faceLoginModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); align-items:center; justify-content:center; z-index:9999;">
        <div style="width:min(520px, 92vw); background:#fff; border-radius:18px; padding:16px;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>Connexion Face ID</strong>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="faceLoginClose">Fermer</button>
            </div>
            <video id="faceLoginVideo" autoplay playsinline style="width:100%; border-radius:12px; background:#0b1220;"></video>
            <button type="button" class="btn btn-success w-100 mt-3" id="faceLoginCapture" style="border-radius:14px; font-weight:700;">
                Se connecter
            </button>
            <div id="faceLoginStatus" class="small mt-2 text-muted"></div>
        </div>
    </div>
</body>
</html>