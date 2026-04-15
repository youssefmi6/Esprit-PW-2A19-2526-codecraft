// register.js - Validation du formulaire d'inscription

document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    if (!registerForm) return;
    
    const nomInput = document.getElementById('nom');
    const prenomInput = document.getElementById('prenom');
    const universiteInput = document.getElementById('universite');
    const filiereInput = document.getElementById('filiere');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const telInput = document.getElementById('tel');
    
    if (nomInput) {
        nomInput.addEventListener('input', function() {
            if (validateName(this.value)) removeError(this);
            else if (this.value.length > 0) showError(this, '2-50 caractères (lettres uniquement)');
            else removeError(this);
        });
    }
    
    if (prenomInput) {
        prenomInput.addEventListener('input', function() {
            if (validateName(this.value)) removeError(this);
            else if (this.value.length > 0) showError(this, '2-50 caractères (lettres uniquement)');
            else removeError(this);
        });
    }
    
    if (universiteInput) {
        universiteInput.addEventListener('input', function() {
            if (this.value.trim().length >= 2) removeError(this);
            else if (this.value.length > 0) showError(this, 'Veuillez entrer le nom de votre université');
            else removeError(this);
        });
    }
    
    if (filiereInput) {
        filiereInput.addEventListener('input', function() {
            if (this.value.trim().length >= 2) removeError(this);
            else if (this.value.length > 0) showError(this, 'Veuillez entrer votre filière');
            else removeError(this);
        });
    }
    
    if (emailInput) {
        emailInput.addEventListener('input', function() {
            if (validateEmail(this.value)) removeError(this);
            else if (this.value.length > 0) showError(this, 'Email invalide');
            else removeError(this);
        });
    }
    
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            if (validatePassword(this.value)) { removeError(this);
                if (confirmPasswordInput && confirmPasswordInput.value) {
                    if (validatePasswordMatch(this.value, confirmPasswordInput.value)) removeError(confirmPasswordInput);
                    else showError(confirmPasswordInput, 'Les mots de passe ne correspondent pas');
                }
            } else if (this.value.length > 0) showError(this, 'Mot de passe min 6 caractères');
            else removeError(this);
        });
    }
    
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            if (validatePasswordMatch(passwordInput.value, this.value)) removeError(this);
            else if (this.value.length > 0) showError(this, 'Les mots de passe ne correspondent pas');
            else removeError(this);
        });
    }
    
    if (telInput) {
        telInput.addEventListener('input', function() {
            if (this.value === '' || validatePhone(this.value)) removeError(this);
            else showError(this, 'Format invalide. Utilisez 8 chiffres ou +216XXXXXXXX');
        });
    }
    
    registerForm.addEventListener('submit', function(e) {
        let isValid = true;
        if (!validateName(nomInput.value)) { showError(nomInput, 'Nom invalide'); isValid = false; }
        if (!validateName(prenomInput.value)) { showError(prenomInput, 'Prénom invalide'); isValid = false; }
        if (universiteInput.value.trim().length < 2) { showError(universiteInput, 'Université requise'); isValid = false; }
        if (filiereInput.value.trim().length < 2) { showError(filiereInput, 'Filière requise'); isValid = false; }
        if (!validateEmail(emailInput.value)) { showError(emailInput, 'Email invalide'); isValid = false; }
        if (!validatePassword(passwordInput.value)) { showError(passwordInput, 'Mot de passe min 6 caractères'); isValid = false; }
        if (!validatePasswordMatch(passwordInput.value, confirmPasswordInput.value)) { showError(confirmPasswordInput, 'Les mots de passe ne correspondent pas'); isValid = false; }
        if (telInput.value && !validatePhone(telInput.value)) { showError(telInput, 'Format téléphone invalide'); isValid = false; }
        if (!isValid) { e.preventDefault(); document.querySelector('.is-invalid')?.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
    });
});