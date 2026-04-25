<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Activer mon compte - StudyHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family:'Inter',sans-serif; background:linear-gradient(135deg,#1a8cff 0%,#00b4d8 50%,#90e0ef 100%); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }
        .card-box { width:460px; background:white; border-radius:30px; padding:35px; box-shadow:0 25px 50px -12px rgba(0,0,0,.25); }
        .title { text-align:center; font-weight:700; color:#1a3a5c; margin-bottom:8px; }
        .subtitle { text-align:center; color:#6c757d; font-size:14px; margin-bottom:24px; }
        .form-label { font-weight:600; font-size:14px; color:#1a3a5c; margin-bottom:8px; }
        .input-group-custom { position:relative; margin-bottom:16px; }
        .input-group-custom i { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#1a8cff; }
        .form-control-custom { width:100%; padding:12px 14px 12px 44px; border:2px solid #e0e7ff; border-radius:14px; }
        .form-control-custom:focus { outline:none; border-color:#1a8cff; box-shadow:0 0 0 4px rgba(26,140,255,0.1); }
        .btn-main { width:100%; padding:12px; border:none; border-radius:14px; background:linear-gradient(135deg,#1a8cff 0%,#00b4d8 100%); color:white; font-weight:600; }
        .back-link { text-align:center; margin-top:16px; }
        .back-link a { color:#1a8cff; text-decoration:none; font-size:14px; font-weight:600; }
    </style>
</head>
<body>
    <div class="card-box">
        <h2 class="title">Activer mon compte</h2>
        <p class="subtitle">Entrez votre email pour recevoir un lien d'activation.</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= escape($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i><?= escape($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <label class="form-label">Email</label>
            <div class="input-group-custom">
                <i class="bi bi-envelope-fill"></i>
                <input type="email" name="email" class="form-control-custom" placeholder="exemple@email.com" value="<?= !empty($emailForActivation) ? escape($emailForActivation) : '' ?>" <?= !empty($canActivate) ? 'readonly' : '' ?> required>
            </div>
            <?php if (!empty($canActivate) && !empty($emailForActivation)): ?>
                <button type="submit" name="confirm_activate" value="1" class="btn-main">
                    <i class="bi bi-check-circle-fill me-1"></i>Activer maintenant
                </button>
                <button type="submit" name="send_activation_link" value="1" class="btn btn-outline-primary w-100 mt-2" style="border-radius:14px; font-weight:600;">
                    <i class="bi bi-send-fill me-1"></i>Envoyer aussi un lien par email
                </button>
            <?php else: ?>
                <button type="submit" class="btn-main">
                    <i class="bi bi-search me-1"></i>Verifier l'email
                </button>
            <?php endif; ?>
        </form>

        <div class="back-link">
            <a href="index.php?action=login"><i class="bi bi-arrow-left me-1"></i>Retour a la connexion</a>
        </div>
    </div>
</body>
</html>
