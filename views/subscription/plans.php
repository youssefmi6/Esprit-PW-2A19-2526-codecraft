<?php
$catalogAccent = ['#0d9488', '#7c3aed', '#db2777', '#ea580c', '#2563eb'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abonnements - StudyHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8fafc; font-family: Arial, sans-serif; }
        .plan-card { border-radius: 16px; border: 1px solid #e2e8f0; background: #fff; padding: 24px; height: 100%; }
        .badge-level { color: #fff; border-radius: 20px; padding: 6px 14px; font-size: 13px; display: inline-block; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Abonnements</h2>
            <p class="text-muted mb-0">Choisissez une offre publiée par l’équipe, puis complétez le paiement (démo).</p>
        </div>
        <div class="d-flex gap-2">
            <a href="index.php?action=home" class="btn btn-outline-secondary">Accueil</a>
            <a href="index.php?action=subscription&subaction=playlists" class="btn btn-primary">Mes contenus abonnement</a>
        </div>
    </div>

    <?php if (!empty($_SESSION['subscription_success'])): ?>
        <div class="alert alert-success"><?php echo escape($_SESSION['subscription_success']); unset($_SESSION['subscription_success']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['subscription_error'])): ?>
        <div class="alert alert-danger"><?php echo escape($_SESSION['subscription_error']); unset($_SESSION['subscription_error']); ?></div>
    <?php endif; ?>

    <?php if ($activeSubscription): ?>
        <div class="alert alert-info">
            Abonnement actuel: <strong><?php echo escape($activeSubscription['nom']); ?></strong>
            (valide jusqu'au <?php echo escape($activeSubscription['date_fin']); ?>)
            <?php if (!empty($activeSubscription['payment_last4'])): ?>
                <span class="ms-2">— Carte **** <?php echo escape($activeSubscription['payment_last4']); ?></span>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">Vous n'avez pas encore d'abonnement actif.</div>
    <?php endif; ?>

    <?php if (!empty($catalogPlans)): ?>
        <h4 class="mb-3">Offres disponibles</h4>
        <div class="row g-4">
            <?php foreach ($catalogPlans as $i => $cp): ?>
                <?php $ac = $catalogAccent[$i % count($catalogAccent)]; ?>
                <div class="col-md-4">
                    <div class="plan-card">
                        <span class="badge-level mb-3" style="background: <?php echo $ac; ?>">
                            Offre
                        </span>
                        <h3 class="h4"><?php echo escape($cp['name']); ?></h3>
                        <?php
                        $desc = (string) ($cp['description'] ?? '');
                        $descShort = $desc;
                        if (function_exists('mb_strlen') && mb_strlen($desc, 'UTF-8') > 200) {
                            $descShort = mb_substr($desc, 0, 200, 'UTF-8') . '…';
                        } elseif (strlen($desc) > 200) {
                            $descShort = substr($desc, 0, 197) . '…';
                        }
                        ?>
                        <p class="text-muted small"><?php echo escape($descShort); ?></p>
                        <div class="display-6 mb-3"><?php echo number_format((int) $cp['prix'], 0); ?> DT</div>
                        <button type="button" class="btn btn-dark w-100"
                                data-bs-toggle="modal" data-bs-target="#paymentModal"
                                data-plan-id="<?php echo (int) $cp['id']; ?>"
                                data-price="<?php echo (int) $cp['prix']; ?>"
                                data-label="<?php echo escape($cp['name']); ?>">
                            Activer <?php echo escape($cp['name']); ?>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-secondary">
            Aucune offre n’est publiée pour le moment. Revenez plus tard ou contactez l’administration.
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Paiement sécurisé</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form method="POST" action="index.php?action=subscription&subaction=subscribe" id="paymentForm">
                <div class="modal-body">
                    <input type="hidden" name="plan_id" id="payPlanId" value="">
                    <p class="text-muted small mb-3">Plan sélectionné : <strong id="paySummary"></strong></p>
                    <p class="small text-secondary">Demo : aucun paiement réel. Ne saisissez pas une vraie carte en production.</p>

                    <div class="mb-3">
                        <label class="form-label" for="card_holder">Titulaire de la carte</label>
                        <input type="text" class="form-control" id="card_holder" name="card_holder" required autocomplete="cc-name" placeholder="Nom comme sur la carte">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="card_number">Numéro de carte</label>
                        <input type="text" class="form-control" id="card_number" name="card_number" required inputmode="numeric" autocomplete="cc-number" placeholder="1234 5678 9012 3456" maxlength="23">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label" for="card_expiry">Expiration (MM/AA)</label>
                            <input type="text" class="form-control" id="card_expiry" name="card_expiry" required autocomplete="cc-exp" placeholder="12/28" maxlength="5">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label" for="card_cvv">CVV</label>
                            <input type="password" class="form-control" id="card_cvv" name="card_cvv" required autocomplete="cc-csc" placeholder="123" maxlength="4">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Payer et activer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function() {
    var modal = document.getElementById('paymentModal');
    if (!modal) return;
    modal.addEventListener('show.bs.modal', function(event) {
        var btn = event.relatedTarget;
        if (!btn) return;
        var form = document.getElementById('paymentForm');
        if (form) form.reset();
        var planId = btn.getAttribute('data-plan-id') || '';
        var price = btn.getAttribute('data-price') || '';
        var label = btn.getAttribute('data-label') || '';
        document.getElementById('payPlanId').value = planId;
        document.getElementById('paySummary').textContent = label + ' — ' + price + ' DT';
    });
})();
</script>
</body>
</html>
