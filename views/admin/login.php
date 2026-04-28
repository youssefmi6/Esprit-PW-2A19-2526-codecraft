<?php
// views/admin/login.php - Connexion admin
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>StudyHub Admin - Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:linear-gradient(135deg,#1a8cff 0%,#00b4d8 50%,#90e0ef 100%); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }
        .login-container { width:450px; background:white; border-radius:32px; padding:40px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); animation:fadeIn 0.5s ease-out; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        .logo-icon { width:70px; height:70px; background:linear-gradient(135deg,#1a8cff 0%,#00b4d8 100%); border-radius:20px; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; }
        .logo-icon i { font-size:35px; color:white; }
        h2 { text-align:center; font-size:28px; font-weight:700; background:linear-gradient(135deg,#1a8cff 0%,#00b4d8 100%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; margin-bottom:8px; }
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
        .back-home { text-align:center; margin-top:20px; }
        .back-home a { color:#6c757d; text-decoration:none; font-size:14px; }
        .back-home a:hover { color:#1a8cff; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-icon"><i class="bi bi-shield-lock-fill"></i></div>
        <h2>StudyHub Admin</h2>
        <p class="subtitle">Espace d'administration</p>
        <?php if(isset($error)): ?>
        <div class="alert alert-danger show" id="errorAlert"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?></div>
        <?php endif; ?>
        <form method="POST" id="loginForm" novalidate>
            <div class="form-group"><label class="form-label">Email</label><div class="input-group-custom"><i class="bi bi-envelope-fill"></i><input type="email" name="email" id="email" class="form-control-custom" placeholder="admin@studyhub.com"></div></div>
            <div class="form-group"><label class="form-label">Mot de passe</label><div class="input-group-custom"><i class="bi bi-lock-fill"></i><input type="password" name="password" id="password" class="form-control-custom" placeholder="••••••••"></div></div>
            <button type="submit" class="btn-login"><i class="bi bi-box-arrow-in-right me-2"></i>Se connecter</button>
        </form>
        <div class="back-home"><a href="index.php?action=home"><i class="bi bi-arrow-left me-1"></i>Retour à l'accueil</a></div>
    </div>
    <script src="../js/validation.js"></script>
    <script src="../js/admin-signup.js"></script>
</body>
</html>