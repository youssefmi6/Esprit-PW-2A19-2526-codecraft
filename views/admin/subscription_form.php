<?php
// views/admin/subscription_form.php — Créer / modifier un abonnement (catalogue ou personnalisé)
$isEdit = !empty($abonnement);
$custom_nom = '';
$custom_prix = '';
$custom_desc = '';
$id_user_sel = $abonnement['id_user'] ?? 0;
$date_debut = $abonnement['date_debut'] ?? date('Y-m-d');
$date_fin = $abonnement['date_fin'] ?? date('Y-m-d', strtotime('+30 days'));
$card_holder_val = $abonnement['card_holder'] ?? '';
$payment_last4_val = $abonnement['payment_last4'] ?? '';
$catalogPlansForAssign = $catalogPlansForAssign ?? [];
$catalog_sel = ($isEdit && !empty($abonnement['plan_id'])) ? (int) $abonnement['plan_id'] : 0;

if ($isEdit && $catalog_sel === 0) {
    $custom_nom = $abonnement['nom'] ?? '';
    $custom_prix = (string) (int) ($abonnement['prix'] ?? 0);
    $custom_desc = $abonnement['descreption'] ?? '';
}

$formAction = $isEdit
    ? 'index.php?action=admin&subaction=subscription_edit&id=' . (int) $abonnement['id']
    : 'index.php?action=admin&subaction=subscription_add';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $isEdit ? 'Modifier' : 'Nouvel' ?> abonnement | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .sidebar { position:fixed; left:0; top:0; width:280px; height:100%; background:linear-gradient(180deg,#0a5c8e 0%,#1a8cff 100%); z-index:1000; }
        .sidebar-header { padding:30px 25px; border-bottom:1px solid rgba(255,255,255,0.2); }
        .logo { display:flex; align-items:center; gap:12px; }
        .logo-icon { width:45px; height:45px; background:rgba(255,255,255,0.2); border-radius:12px; display:flex; align-items:center; justify-content:center; }
        .logo-icon i { font-size:24px; color:white; }
        .logo-text h3 { color:white; font-weight:700; font-size:20px; margin:0; }
        .logo-text p { color:rgba(255,255,255,0.8); font-size:11px; margin:0; }
        .nav-menu { padding:25px; }
        .nav-item { margin-bottom:8px; }
        .nav-link { display:flex; align-items:center; gap:12px; padding:12px 16px; border-radius:12px; color:rgba(255,255,255,0.85); text-decoration:none; transition:all 0.3s; font-weight:500; }
        .nav-link i { font-size:20px; width:24px; }
        .nav-link:hover { background:rgba(255,255,255,0.2); color:white; transform:translateX(5px); }
        .nav-link.active { background:rgba(255,255,255,0.25); color:white; }
        .logout-link { margin-top:40px; border-top:1px solid rgba(255,255,255,0.2); padding-top:20px; }
        .main-content { margin-left:280px; padding:30px; min-height:100vh; }
        .top-bar { background:rgba(255,255,255,0.95); border-radius:20px; padding:15px 25px; margin-bottom:30px; display:flex; justify-content:space-between; align-items:center; }
        .content-card { background:white; border-radius:24px; padding:28px; max-width:640px; box-shadow:0 4px 15px rgba(0,0,0,0.05); }
        .btn-blue { background:linear-gradient(135deg,#1a8cff 0%,#00b4d8 100%); border:none; padding:12px 28px; border-radius:12px; font-weight:600; color:white; }
        @media (max-width:768px) { .sidebar { width:80px; } .sidebar .logo-text, .sidebar .nav-link span { display:none; } .main-content { margin-left:80px; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header"><div class="logo"><div class="logo-icon"><i class="bi bi-mortarboard-fill"></i></div><div class="logo-text"><h3>StudyHub</h3><p>Admin Dashboard</p></div></div></div>
        <div class="nav-menu">
            <div class="nav-item"><a href="index.php?action=admin&subaction=dashboard" class="nav-link"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=subscriptions" class="nav-link active"><i class="bi bi-star-fill"></i><span>Abonnements</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=users" class="nav-link"><i class="bi bi-people-fill"></i><span>Utilisateurs</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=resources" class="nav-link"><i class="bi bi-folder-fill"></i><span>Ressources</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=comments" class="nav-link"><i class="bi bi-chat-dots-fill"></i><span>Commentaires</span></a></div>
            <div class="nav-item"><a href="index.php?action=admin&subaction=profile" class="nav-link"><i class="bi bi-person-fill"></i><span>Mon profil</span></a></div>
            <div class="nav-item logout-link"><a href="index.php?action=logout" class="nav-link"><i class="bi bi-box-arrow-right"></i><span>Déconnexion</span></a></div>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <div>
                <h1 class="h4 mb-0 fw-bold text-primary"><?= $isEdit ? 'Modifier l\'abonnement' : 'Nouvel abonnement' ?></h1>
                <p class="text-muted small mb-0">Plan catalogue publié ou libellé personnalisé</p>
            </div>
            <a href="index.php?action=admin&subaction=subscriptions" class="btn btn-secondary">← Liste</a>
        </div>

        <div class="content-card">
            <?php if (!empty($_SESSION['admin_sub_error'])): ?>
                <div class="alert alert-danger"><?= escape($_SESSION['admin_sub_error']); unset($_SESSION['admin_sub_error']); ?></div>
            <?php endif; ?>

            <form method="post" action="<?= escape($formAction) ?>" id="adminSubscriptionForm" novalidate>
                <div class="mb-3">
                    <label class="form-label">Membre <span class="text-danger">*</span></label>
                    <select name="id_user" class="form-select">
                        <option value="">— Choisir un utilisateur —</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= (int) $u['id'] ?>" <?= ((int) $u['id'] === (int) $id_user_sel) ? 'selected' : '' ?>>
                                <?= escape($u['prenom'] . ' ' . $u['nom']) ?> (<?= escape($u['email']) ?>)<?= ((int) $u['role'] === 0) ? ' — Admin' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if (!empty($catalogPlansForAssign)): ?>
                <div class="mb-3">
                    <label class="form-label">Type du catalogue (prioritaire si sélectionné)</label>
                    <select name="catalog_plan_id" id="catalog_plan_id" class="form-select">
                        <option value="0">— Aucun : utiliser le bloc « Plan personnalisé » ci-dessous —</option>
                        <?php foreach ($catalogPlansForAssign as $cp): ?>
                            <option value="<?= (int) $cp['id'] ?>" <?= $catalog_sel === (int) $cp['id'] ? 'selected' : '' ?>>
                                <?= escape($cp['name']) ?> — <?= (int) $cp['prix'] ?> DT
                                (<?= (int) ($cp['resource_count'] ?? 0) ?> res.)<?= empty($cp['published']) ? ' [brouillon]' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="border rounded p-3 mb-3 bg-light">
                    <p class="small fw-semibold mb-2">Plan personnalisé</p>
                    <p class="text-muted small">Obligatoire si aucun type catalogue n’est choisi (valeur 0).</p>
                    <div class="mb-2">
                        <label class="form-label">Nom du plan (max 100 car.)</label>
                        <input type="text" name="custom_nom" class="form-control" maxlength="100" value="<?= escape($custom_nom) ?>" placeholder="Ex: Partenaire école">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Prix (DT)</label>
                        <input type="number" name="custom_prix" class="form-control" value="<?= escape($custom_prix) ?>">
                    </div>
                    <div>
                        <label class="form-label">Description (max 500 car.)</label>
                        <textarea name="custom_desc" class="form-control" rows="2" maxlength="500"><?= escape($custom_desc) ?></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date début</label>
                        <input type="date" name="date_debut" class="form-control" value="<?= escape($date_debut) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date fin</label>
                        <input type="date" name="date_fin" class="form-control" value="<?= escape($date_fin) ?>">
                    </div>
                </div>

                <?php if ($isEdit): ?>
                    <p class="small text-muted">Paiement (optionnel, mise à jour)</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Titulaire carte</label>
                            <input type="text" name="card_holder" class="form-control" value="<?= escape($card_holder_val) ?>" maxlength="120">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">4 derniers chiffres</label>
                            <input type="text" name="payment_last4" class="form-control" maxlength="4" value="<?= escape($payment_last4_val) ?>">
                        </div>
                    </div>
                <?php endif; ?>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn-blue"><?= $isEdit ? 'Enregistrer' : 'Créer l\'abonnement' ?></button>
                    <a href="index.php?action=admin&subaction=subscriptions" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/validation.js"></script>
    <script src="../js/subscription-admin-form.js"></script>
</body>
</html>
