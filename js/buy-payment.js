// buy-payment.js — Paiement démo (page achat ressource)

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('paymentForm');
    if (!form || form.dataset.buyPaymentBound || !document.getElementById('buy_card_number')) return;
    form.dataset.buyPaymentBound = '1';

    const cardNumber = document.getElementById('buy_card_number');
    const expiry = document.getElementById('buy_card_expiry');
    const cvv = document.getElementById('buy_card_cvv');
    const holder = document.getElementById('buy_card_holder');

    form.addEventListener('submit', function(e) {
        let ok = true;
        if (cardNumber && !validateCardNumberDigits(cardNumber.value)) {
            showError(cardNumber, 'Numéro de carte invalide');
            ok = false;
        } else if (cardNumber) removeError(cardNumber);
        if (expiry && !validateCardExpiry(expiry.value)) {
            showError(expiry, 'Expiration au format MM/AA');
            ok = false;
        } else if (expiry) removeError(expiry);
        if (cvv && !validateCvv(cvv.value)) {
            showError(cvv, 'CVV invalide (3 ou 4 chiffres)');
            ok = false;
        } else if (cvv) removeError(cvv);
        if (holder && !validateNonEmpty(holder.value)) {
            showError(holder, 'Nom du titulaire requis');
            ok = false;
        } else if (holder) removeError(holder);
        if (!ok) e.preventDefault();
    });
});
