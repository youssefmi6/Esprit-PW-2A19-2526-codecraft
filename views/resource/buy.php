<?php
// views/resource/buy.php - Page d'achat premium
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Achat - StudyHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Jost:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://js.stripe.com/v3"></script>
    <style>
        body { font-family:'DM Sans',sans-serif; background:linear-gradient(135deg,#dbeafe 0%,#eff6ff 100%); min-height:100vh; padding:40px 0; }
        :root { --primary:#2563eb; }
        .btn-payment { background:#635bff; color:white; padding:12px 32px; border-radius:40px; font-weight:600; border:2px solid #635bff; width:100%; }
        .btn-payment:hover { background:transparent; color:#635bff; }
        .form-card { background:white; border-radius:24px; padding:40px; box-shadow:0 25px 50px rgba(0,0,0,0.1); }
        .form-control { border-radius:12px; padding:12px 16px; }
        .payment-notice { border:1px solid #e2e8f0; border-radius:12px; padding:12px 14px; background:#f8fafc; color:#334155; font-size:14px; }
        .stripe-card-wrap { border:1px solid #cbd5e1; border-radius:10px; padding:10px 12px; background:#fff; }
        .pay-error { color:#dc2626; font-size:14px; margin-top:8px; min-height:20px; }
        .btn-payment:disabled { opacity:.7; cursor:not-allowed; }
    </style>
</head>
<body>
    <div class="container"><div class="row justify-content-center"><div class="col-md-6"><div class="form-card"><div class="text-center mb-4"><h2>💳 Paiement sécurisé</h2><p class="text-muted">Déverrouillez l'accès premium</p></div>
        <div class="bg-light p-3 rounded-3 mb-4"><div class="d-flex justify-content-between"><div><strong><?= escape($resource['titre']) ?></strong><br><small>Ressource premium</small></div><span class="badge bg-warning">Premium</span></div><div class="mt-2 fw-bold">Prix : <?= $resource['prix'] ?> DT</div></div>
        <?php if ($paymentError !== ''): ?>
            <div class="alert alert-danger">
                <?php if ($paymentError === 'stripe_not_configured'): ?>
                    Stripe n'est pas configure. Ajoutez les variables `STRIPE_SECRET_KEY` et `STRIPE_PUBLIC_KEY`.
                <?php elseif ($paymentError === 'invalid_amount'): ?>
                    Le montant de cette ressource est invalide.
                <?php elseif ($paymentError === 'payment_not_validated'): ?>
                    Paiement non valide ou annule. Veuillez reessayer.
                <?php else: ?>
                    Impossible de lancer le paiement Stripe pour le moment.
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php if ($paymentStatus === 'cancelled'): ?>
            <div class="alert alert-warning">Paiement annule. Aucun montant n'a ete debite.</div>
        <?php endif; ?>

        <div class="payment-notice mb-3">
            Le paiement est traite par Stripe Checkout (page hebergee et securisee).
        </div>

        <?php if ($stripeEnabled): ?>
            <form id="stripePaymentForm" novalidate>
                <div class="mb-3">
                    <label class="form-label">Nom complet</label>
                    <input type="text" id="cardholderName" class="form-control" placeholder="Nom et prenom" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" id="cardholderEmail" class="form-control" placeholder="email@exemple.com" value="<?= escape($currentUser['email'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Carte bancaire</label>
                    <div id="card-element" class="stripe-card-wrap"></div>
                    <div id="payment-error" class="pay-error"></div>
                </div>
                <button type="submit" id="payButton" class="btn-payment mt-2">Payer maintenant</button>
            </form>
        <?php else: ?>
            <button type="button" class="btn-payment mt-2" disabled>Stripe non configure</button>
        <?php endif; ?>
        <div class="text-center mt-4"><a href="index.php?action=home" class="text-muted">← Retour à l'accueil</a></div>
    </div></div></div></div>
    <?php if ($stripeEnabled): ?>
    <script>
    (function() {
        const form = document.getElementById('stripePaymentForm');
        const payButton = document.getElementById('payButton');
        const errorEl = document.getElementById('payment-error');
        const nameEl = document.getElementById('cardholderName');
        const emailEl = document.getElementById('cardholderEmail');
        if (!form || !payButton || !errorEl) return;

        let stripeInstance = null;
        let cardElement = null;

        function showError(message) {
            errorEl.textContent = message || '';
        }

        async function initStripe() {
            try {
                const response = await fetch('index.php?action=resource&subaction=buy_intent&id=<?= (int)$resource['id_res'] ?>', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();
                if (!response.ok || !data.ok) {
                    showError('Impossible de preparer le paiement Stripe.');
                    payButton.disabled = true;
                    return null;
                }

                stripeInstance = Stripe(data.publishableKey);
                const elements = stripeInstance.elements();
                cardElement = elements.create('card', {
                    hidePostalCode: true,
                    style: { base: { fontSize: '16px', color: '#0f172a' } }
                });
                cardElement.mount('#card-element');
                cardElement.on('change', function(event) {
                    showError(event.error ? event.error.message : '');
                });
                return data.clientSecret;
            } catch (e) {
                showError('Erreur reseau lors de la preparation du paiement.');
                payButton.disabled = true;
                return null;
            }
        }

        let clientSecretPromise = initStripe();

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            showError('');
            payButton.disabled = true;
            payButton.textContent = 'Paiement en cours...';

            const clientSecret = await clientSecretPromise;
            if (!clientSecret || !stripeInstance || !cardElement) {
                showError('Stripe est indisponible. Veuillez reessayer.');
                payButton.disabled = false;
                payButton.textContent = 'Payer maintenant';
                return;
            }

            const name = (nameEl.value || '').trim();
            const email = (emailEl.value || '').trim();
            if (!name || !email) {
                showError('Veuillez remplir le nom et l\'email.');
                payButton.disabled = false;
                payButton.textContent = 'Payer maintenant';
                return;
            }

            const result = await stripeInstance.confirmCardPayment(clientSecret, {
                payment_method: {
                    card: cardElement,
                    billing_details: { name: name, email: email }
                }
            });

            if (result.error) {
                showError(result.error.message || 'Le paiement a echoue.');
                payButton.disabled = false;
                payButton.textContent = 'Payer maintenant';
                return;
            }

            const intent = result.paymentIntent;
            if (!intent || intent.status !== 'succeeded') {
                showError('Paiement non valide.');
                payButton.disabled = false;
                payButton.textContent = 'Payer maintenant';
                return;
            }

            window.location.href = 'index.php?action=resource&subaction=buy_success&id=<?= (int)$resource['id_res'] ?>&payment_intent=' + encodeURIComponent(intent.id);
        });
    })();
    </script>
    <?php endif; ?>
</body>
</html>