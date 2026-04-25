<?php
$step = isset($step) ? (int)$step : 1;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié - StudyHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family:'Inter',sans-serif; background:linear-gradient(135deg,#1a8cff 0%,#00b4d8 50%,#90e0ef 100%); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }
        .reset-container { width:460px; background:white; border-radius:30px; padding:35px; box-shadow:0 25px 50px -12px rgba(0,0,0,.25); }
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
    <div class="reset-container">
        <h2 class="title">Réinitialiser le mot de passe</h2>
        <p class="subtitle">
            <?= $step === 1 ? "Entrez votre numéro de téléphone pour recevoir une notification." : "Choisissez votre nouveau mot de passe." ?>
        </p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?></div>
        <?php endif; ?>
        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i><?= $message ?></div>
        <?php endif; ?>

        <?php if ($step === 1): ?>
            <form method="POST">
                <label class="form-label">Numéro de téléphone</label>
                <div class="input-group-custom">
                    <i class="bi bi-telephone-fill"></i>
                    <input type="text" name="tel" class="form-control-custom" placeholder="Ex: 22123456 ou +21622123456" required>
                </div>
                <button type="submit" name="send_notification" class="btn-main">
                    <i class="bi bi-send-fill me-1"></i>Envoyer la notification
                </button>
            </form>
        <?php else: ?>
            <form method="POST">
                <label class="form-label">Nouveau mot de passe</label>
                <div class="input-group-custom">
                    <i class="bi bi-lock-fill"></i>
                    <input type="password" name="new_password" class="form-control-custom" placeholder="Minimum 6 caractères" required>
                </div>
                <label class="form-label">Confirmer le mot de passe</label>
                <div class="input-group-custom">
                    <i class="bi bi-shield-lock-fill"></i>
                    <input type="password" name="confirm_password" class="form-control-custom" placeholder="Retapez le mot de passe" required>
                </div>
                <button type="submit" name="reset_password" class="btn-main">
                    <i class="bi bi-arrow-repeat me-1"></i>Valider le nouveau mot de passe
                </button>
            </form>
        <?php endif; ?>

        <div class="back-link">
            <a href="index.php?action=login"><i class="bi bi-arrow-left me-1"></i>Retour à la connexion</a>
        </div>
    </div>
</body>
</html>
