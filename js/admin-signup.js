// admin-signup.js - Validation du formulaire admin

document.addEventListener('DOMContentLoaded', function() {
    const adminForm = document.getElementById('loginForm');
    if (!adminForm) return;

    const emailInput = adminForm.querySelector('input[name="email"]');
    const passwordInput = adminForm.querySelector('input[name="password"]');
    
    if (emailInput) {
        emailInput.addEventListener('input', function() {
            if (validateEmail(this.value)) removeError(this);
            else if (this.value.length > 0) showError(this, 'Email invalide');
            else removeError(this);
        });
    }
    
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            if (this.value.length >= 1) removeError(this);
            else showError(this, 'Veuillez entrer votre mot de passe');
        });
    }
    
    adminForm.addEventListener('submit', function(e) {
        let isValid = true;
        if (!validateEmail(emailInput.value)) { showError(emailInput, 'Email invalide'); isValid = false; }
        if (passwordInput.value.trim() === '') { showError(passwordInput, 'Mot de passe requis'); isValid = false; }
        if (!isValid) e.preventDefault();
    });
});