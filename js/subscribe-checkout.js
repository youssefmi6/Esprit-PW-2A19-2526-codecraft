// subscribe-checkout.js — Modal paiement abonnements (plans)

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('paymentForm');
    if (!form || !form.querySelector('#card_holder') || form.dataset.subscribeCheckoutBound) return;
    form.dataset.subscribeCheckoutBound = '1';

    const holder = document.getElementById('card_holder');
    const number = document.getElementById('card_number');
    const expiry = document.getElementById('card_expiry');
    const cvv = document.getElementById('card_cvv');

    form.addEventListener('submit', function(e) {
        let ok = true;
        if (holder && !validateNonEmpty(holder.value)) {
            showError(holder, 'Titulaire requis');
            ok = false;
        } else if (holder) removeError(holder);
        if (number && !validateCardNumberDigits(number.value)) {
            showError(number, 'Numéro de carte invalide');
            ok = false;
        } else if (number) removeError(number);
        if (expiry && !validateCardExpiry(expiry.value)) {
            showError(expiry, 'Expiration au format MM/AA');
            ok = false;
        } else if (expiry) removeError(expiry);
        if (cvv && !validateCvv(cvv.value)) {
            showError(cvv, 'CVV invalide');
            ok = false;
        } else if (cvv) removeError(cvv);
        if (!ok) e.preventDefault();
    });
});
