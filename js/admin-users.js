// admin-users.js - Validation du formulaire admin utilisateurs

document.addEventListener('DOMContentLoaded', function() {
    const addUserForm = document.querySelector('#addUserModal form');
    if (!addUserForm) return;
    
    const nomInput = addUserForm.querySelector('input[name="nom"]');
    const prenomInput = addUserForm.querySelector('input[name="prenom"]');
    const emailInput = addUserForm.querySelector('input[name="email"]');
    const mdpInput = addUserForm.querySelector('input[name="mdp"]');
    
    if (nomInput) {
        nomInput.addEventListener('input', function() {
            if (validateName(this.value)) removeError(this);
            else if (this.value.length > 0) showError(this, '2-50 caractères');
            else removeError(this);
        });
    }
    
    if (prenomInput) {
        prenomInput.addEventListener('input', function() {
            if (validateName(this.value)) removeError(this);
            else if (this.value.length > 0) showError(this, '2-50 caractères');
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
    
    if (mdpInput) {
        mdpInput.addEventListener('input', function() {
            if (validatePassword(this.value)) removeError(this);
            else if (this.value.length > 0) showError(this, 'Mot de passe min 6 caractères');
            else removeError(this);
        });
    }
    
    addUserForm.addEventListener('submit', function(e) {
        let isValid = true;
        if (!validateName(nomInput.value)) { showError(nomInput, 'Nom invalide'); isValid = false; }
        if (!validateName(prenomInput.value)) { showError(prenomInput, 'Prénom invalide'); isValid = false; }
        if (!validateEmail(emailInput.value)) { showError(emailInput, 'Email invalide'); isValid = false; }
        if (!validatePassword(mdpInput.value)) { showError(mdpInput, 'Mot de passe min 6 caractères'); isValid = false; }
        if (!isValid) e.preventDefault();
    });
});