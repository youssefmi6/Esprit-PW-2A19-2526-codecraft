// profile.js - Validation du formulaire de profil

document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.getElementById('profileForm');
    if (!profileForm) return;
    
    const nomInput = profileForm.querySelector('input[name="nom"]');
    const prenomInput = profileForm.querySelector('input[name="prenom"]');
    const emailInput = profileForm.querySelector('input[name="email"]');
    const telInput = profileForm.querySelector('input[name="tel"]');
    const passwordInput = profileForm.querySelector('input[name="mdp"]');
    const photoInput = profileForm.querySelector('input[name="photo"]');
    
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
    
    if (emailInput) {
        emailInput.addEventListener('input', function() {
            if (validateEmail(this.value)) removeError(this);
            else if (this.value.length > 0) showError(this, 'Email invalide');
            else removeError(this);
        });
    }
    
    if (telInput) {
        telInput.addEventListener('input', function() {
            if (this.value === '' || validatePhone(this.value)) removeError(this);
            else showError(this, 'Format invalide. Utilisez 8 chiffres ou +216XXXXXXXX');
        });
    }
    
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            if (this.value === '' || validatePassword(this.value)) removeError(this);
            else showError(this, 'Mot de passe min 6 caractères');
        });
    }
    
    if (photoInput) {
        photoInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                const maxSize = 2 * 1024 * 1024;
                if (!allowedTypes.includes(file.type)) { showError(this, 'Format non autorisé (JPG, PNG, GIF, WEBP)'); this.value = ''; }
                else if (file.size > maxSize) { showError(this, 'Image trop volumineuse (max 2MB)'); this.value = ''; }
                else removeError(this);
            }
        });
    }
    
    profileForm.addEventListener('submit', function(e) {
        let isValid = true;
        if (nomInput && !validateName(nomInput.value)) { showError(nomInput, 'Nom invalide'); isValid = false; }
        if (prenomInput && !validateName(prenomInput.value)) { showError(prenomInput, 'Prénom invalide'); isValid = false; }
        if (emailInput && !validateEmail(emailInput.value)) { showError(emailInput, 'Email invalide'); isValid = false; }
        if (telInput && telInput.value && !validatePhone(telInput.value)) { showError(telInput, 'Format téléphone invalide'); isValid = false; }
        if (passwordInput && passwordInput.value && !validatePassword(passwordInput.value)) { showError(passwordInput, 'Mot de passe min 6 caractères'); isValid = false; }
        if (!isValid) { e.preventDefault(); document.querySelector('.is-invalid')?.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
    });
});