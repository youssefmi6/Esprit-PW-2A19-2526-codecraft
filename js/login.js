// login.js - Validation du formulaire de connexion

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    if (!loginForm) return;
    
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    
    if (emailInput) {
        emailInput.addEventListener('input', function() {
            if (validateEmail(this.value)) removeError(this);
            else if (this.value.length > 0) showError(this, 'Veuillez entrer un email valide');
            else removeError(this);
        });
    }
    
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            if (validatePassword(this.value)) removeError(this);
            else if (this.value.length > 0) showError(this, 'Le mot de passe doit contenir au moins 6 caractères');
            else removeError(this);
        });
    }
    
    loginForm.addEventListener('submit', function(e) {
        let isValid = true;
        if (!validateEmail(emailInput.value)) { showError(emailInput, 'Veuillez entrer un email valide'); isValid = false; }
        if (!validatePassword(passwordInput.value)) { showError(passwordInput, 'Mot de passe min 6 caractères'); isValid = false; }
        if (!isValid) { e.preventDefault(); document.querySelector('.is-invalid')?.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
    });
});