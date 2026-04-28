<?php
// views/resource/buy.php - Page d'achat premium
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Achat - StudyHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Jost:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family:'DM Sans',sans-serif; background:linear-gradient(135deg,#dbeafe 0%,#eff6ff 100%); min-height:100vh; padding:40px 0; }
        :root { --primary:#2563eb; }
        .btn-payment { background:#10b981; color:white; padding:12px 32px; border-radius:40px; font-weight:600; border:2px solid #10b981; width:100%; }
        .btn-payment:hover { background:transparent; color:#10b981; }
        .form-card { background:white; border-radius:24px; padding:40px; box-shadow:0 25px 50px rgba(0,0,0,0.1); }
        .form-control { border-radius:12px; padding:12px 16px; }
        .payment-method { border:2px solid #e2e8f0; border-radius:12px; padding:12px; margin-bottom:10px; cursor:pointer; }
        .payment-method:hover,.payment-method.selected { border-color:var(--primary); background:#dbeafe; }
    </style>
</head>
<body>
    <div class="container"><div class="row justify-content-center"><div class="col-md-6"><div class="form-card"><div class="text-center mb-4"><h2>💳 Paiement sécurisé</h2><p class="text-muted">Déverrouillez l'accès premium</p></div>
        <div class="bg-light p-3 rounded-3 mb-4"><div class="d-flex justify-content-between"><div><strong><?= escape($resource['titre']) ?></strong><br><small>Ressource premium</small></div><span class="badge bg-warning">Premium</span></div><div class="mt-2 fw-bold">Prix : <?= $resource['prix'] ?> DT</div></div>
        <form method="POST" id="paymentForm" novalidate><div class="mb-3"><input type="text" id="buy_card_number" name="card_number" class="form-control" placeholder="Numéro de carte" inputmode="numeric" autocomplete="cc-number"></div><div class="row"><div class="col-md-6 mb-3"><input type="text" id="buy_card_expiry" name="card_expiry" class="form-control" placeholder="MM/YY" autocomplete="cc-exp"></div><div class="col-md-6 mb-3"><input type="password" id="buy_card_cvv" name="card_cvv" class="form-control" placeholder="CVV" autocomplete="cc-csc"></div></div>
        <div class="mb-3"><input type="text" id="buy_card_holder" name="card_holder" class="form-control" placeholder="Nom du titulaire" autocomplete="cc-name"></div>
        <div class="payment-method selected" onclick="selectMethod(this)"><i class="fab fa-cc-visa me-2"></i> Carte bancaire</div>
        <div class="payment-method" onclick="selectMethod(this)"><i class="fab fa-cc-mastercard me-2"></i> Mastercard</div>
        <button type="submit" class="btn-payment mt-4">Payer maintenant</button></form>
        <div class="text-center mt-4"><a href="index.php?action=resource&subaction=detail&id=<?= $resource['id_res'] ?>" class="text-muted">← Annuler</a></div>
    </div></div></div></div>
    <script>function selectMethod(el){document.querySelectorAll('.payment-method').forEach(m=>m.classList.remove('selected'));el.classList.add('selected');}</script>
    <script src="js/validation.js"></script>
    <script src="js/buy-payment.js"></script>
</body>
</html>