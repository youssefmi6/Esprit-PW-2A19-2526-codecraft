// validation.js - Fonctions de validation

function showError(input, message) {
    let formGroup = input.closest('.form-group');
    if (!formGroup) formGroup = input.parentElement;
    let errorDiv = formGroup.querySelector('.error-message');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'error-message text-danger small mt-1';
        formGroup.appendChild(errorDiv);
    }
    errorDiv.innerHTML = '<i class="bi bi-exclamation-circle-fill me-1"></i>' + message;
    input.classList.add('is-invalid');
    input.classList.remove('is-valid');
}

function removeError(input) {
    let formGroup = input.closest('.form-group');
    if (!formGroup) formGroup = input.parentElement;
    let errorDiv = formGroup.querySelector('.error-message');
    if (errorDiv) errorDiv.remove();
    input.classList.remove('is-invalid');
    input.classList.add('is-valid');
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