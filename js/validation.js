// validation.js - Fonctions de validation

function clearFieldErrorMessages(input) {
    let n = input.nextElementSibling;
    while (n && n.classList && n.classList.contains('error-message')) {
        const next = n.nextElementSibling;
        n.remove();
        n = next;
    }
}

function showError(input, message) {
    if (!input) return;
    clearFieldErrorMessages(input);
    input.classList.remove('is-valid');
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message text-danger small mt-1';
    errorDiv.innerHTML = '<i class="bi bi-exclamation-circle-fill me-1"></i>' + message;
    input.insertAdjacentElement('afterend', errorDiv);
    input.classList.add('is-invalid');
}

function removeError(input) {
    if (!input) return;
    clearFieldErrorMessages(input);
    input.classList.remove('is-invalid');
    const v = input.type === 'file' ? (input.files && input.files.length) : String(input.value || '').trim();
    if (v) input.classList.add('is-valid');
    else input.classList.remove('is-valid');
}

function validateEmail(email) {
    const emailRegex = /^[^\s@]+@([^\s@.,]+\.)+[^\s@.,]{2,}$/;
    return emailRegex.test(email);
}

function validatePhone(phone) {
    if (!phone) return true;
    const phoneRegex = /^(\+216)?[0-9]{8}$/;
    return phoneRegex.test(phone);
}

function validatePassword(password) {
    return password.length >= 6;
}

function validatePasswordMatch(password, confirm) {
    return password === confirm;
}

function validateName(name) {
    const nameRegex = /^[a-zA-ZÀ-ÿ\s]{2,50}$/;
    return nameRegex.test(name);
}

function validatePages(pages) {
    const pagesNum = parseInt(pages);
    return !isNaN(pagesNum) && pagesNum >= 1 && pagesNum <= 1000;
}

function validatePrice(price) {
    const priceNum = parseFloat(price);
    return !isNaN(priceNum) && priceNum >= 0 && priceNum <= 9999;
}

function validateTitle(title) {
    return title.trim().length >= 3;
}

function validateDescription(description) {
    return description.trim().length >= 10;
}

function validateNonEmpty(value) {
    return String(value || '').trim().length > 0;
}

function validateCommentMessage(message) {
    return String(message || '').trim().length >= 2;
}

function validatePagesAllowZero(pages) {
    const pagesNum = parseInt(pages, 10);
    return !isNaN(pagesNum) && pagesNum >= 0 && pagesNum <= 1000;
}

function validatePaymentLast4(value) {
    const v = String(value || '').trim();
    if (!v) return true;
    return /^\d{4}$/.test(v);
}

function validateCardExpiry(value) {
    const v = String(value || '').trim();
    if (!/^\d{2}\/\d{2}$/.test(v)) return false;
    const parts = v.split('/');
    const mm = parseInt(parts[0], 10);
    const yy = parseInt(parts[1], 10);
    if (mm < 1 || mm > 12) return false;
    if (isNaN(yy)) return false;
    return true;
}

function validateCardNumberDigits(value) {
    const digits = String(value || '').replace(/\s/g, '');
    return digits.length >= 13 && digits.length <= 19 && /^\d+$/.test(digits);
}

function validateCvv(value) {
    const v = String(value || '').trim();
    return /^\d{3,4}$/.test(v);
}

function validateDateOrder(start, end) {
    if (!start || !end) return false;
    return new Date(start) <= new Date(end);
}

/** Format AAAA-MM-JJ (champ type date navigateur) */
function validateIsoDate(value) {
    const s = String(value || '').trim();
    if (!/^\d{4}-\d{2}-\d{2}$/.test(s)) return false;
    const d = new Date(s + 'T12:00:00');
    return !isNaN(d.getTime());
}

/** Longueur texte après trim, max caractères */
function validateMaxTextLength(value, maxLen) {
    return String(value || '').length <= maxLen;
}

/** Nom du type d'abonnement : uniquement lettres Unicode et espaces (1–100 car.) */
function validateSubscriptionPlanName(value) {
    const s = String(value || '').trim();
    if (s.length < 1 || s.length > 100) return false;
    return /^[\p{L}\s]+$/u.test(s) && /\p{L}/u.test(s);
}

/** Description : lettres, chiffres et espaces uniquement ; vide autorisé ; max 500 */
function validateSubscriptionPlanDescription(value) {
    const s = String(value || '');
    if (s.length > 500) return false;
    if (s.trim() === '') return true;
    return /^[\p{L}\p{N}\s]+$/u.test(s);
}

/** Prix type d'abonnement : entier strictement > 0 */
function validateSubscriptionPlanPrixStrict(value) {
    const n = parseInt(String(value ?? '').trim(), 10);
    return !isNaN(n) && n > 0;
}