// upload.js - Validation du formulaire d'upload

document.addEventListener('DOMContentLoaded', function() {
    const uploadForm = document.getElementById('uploadForm');
    if (!uploadForm) return;
    
    const titreInput = document.getElementById('titre');
    const descriptionInput = document.getElementById('description');
    const matiereSelect = document.getElementById('matiere');
    const typeSelect = document.getElementById('type');
    const niveauSelect = document.getElementById('niveau');
    const pagesInput = document.getElementById('pages');
    const accesSelect = document.getElementById('accessSelect');
    const prixInput = document.getElementById('prix');
    const fichierInput = document.getElementById('fileInput');
    
    if (titreInput) {
        titreInput.addEventListener('input', function() {
            if (validateTitle(this.value)) removeError(this);
            else if (this.value.length > 0) showError(this, 'Le titre doit contenir au moins 3 caractères');
            else removeError(this);
        });
    }
    
    if (descriptionInput) {
        descriptionInput.addEventListener('input', function() {
            if (validateDescription(this.value)) removeError(this);
            else if (this.value.length > 0) showError(this, 'La description doit contenir au moins 10 caractères');
            else removeError(this);
        });
    }
    
    if (matiereSelect) {
        matiereSelect.addEventListener('change', function() {
            if (this.value !== "") removeError(this);
            else showError(this, 'Veuillez sélectionner une matière');
        });
    }
    
    if (typeSelect) {
        typeSelect.addEventListener('change', function() {
            if (this.value !== "") removeError(this);
            else showError(this, 'Veuillez sélectionner un type');
        });
    }
    
    if (niveauSelect) {
        niveauSelect.addEventListener('change', function() {
            if (this.value !== "") removeError(this);
            else showError(this, 'Veuillez sélectionner un niveau');
        });
    }
    
    if (pagesInput) {
        pagesInput.addEventListener('input', function() {
            if (validatePages(this.value)) removeError(this);
            else if (this.value.length > 0) showError(this, 'Le nombre de pages doit être entre 1 et 1000');
            else removeError(this);
        });
    }
    
    if (accesSelect) {
        accesSelect.addEventListener('change', function() {
            const priceDiv = document.getElementById('priceDiv');
            if (priceDiv) priceDiv.style.display = this.value === 'Premium' ? 'block' : 'none';
            if (prixInput && this.value !== 'Premium') removeError(prixInput);
        });
    }
    
    if (prixInput) {
        prixInput.addEventListener('input', function() {
            if (accesSelect.value === 'Premium') {
                if (validatePrice(this.value)) removeError(this);
                else if (this.value.length > 0) showError(this, 'Le prix doit être un nombre positif');
                else showError(this, 'Veuillez entrer un prix');
            }
        });
    }
    
    if (fichierInput) {
        fichierInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'];
                const maxSize = 10 * 1024 * 1024;
                if (!allowedTypes.includes(file.type)) { showError(this, 'Format non autorisé (PDF, DOC, DOCX, TXT)'); this.value = ''; }
                else if (file.size > maxSize) { showError(this, 'Fichier trop volumineux (max 10MB)'); this.value = ''; }
                else removeError(this);
            } else showError(this, 'Veuillez sélectionner un fichier');
        });
    }
    
    uploadForm.addEventListener('submit', function(e) {
        let isValid = true;
        if (!validateTitle(titreInput.value)) { showError(titreInput, 'Titre requis (min 3 caractères)'); isValid = false; }
        if (!validateDescription(descriptionInput.value)) { showError(descriptionInput, 'Description requise (min 10 caractères)'); isValid = false; }
        if (matiereSelect.value === "") { showError(matiereSelect, 'Sélectionnez une matière'); isValid = false; }
        if (typeSelect.value === "") { showError(typeSelect, 'Sélectionnez un type'); isValid = false; }
        if (niveauSelect.value === "") { showError(niveauSelect, 'Sélectionnez un niveau'); isValid = false; }
        if (!validatePages(pagesInput.value)) { showError(pagesInput, 'Pages invalides (1-1000)'); isValid = false; }
        if (accesSelect.value === 'Premium' && !validatePrice(prixInput.value)) { showError(prixInput, 'Prix invalide'); isValid = false; }
        if (!fichierInput.files || !fichierInput.files[0]) { showError(fichierInput, 'Sélectionnez un fichier'); isValid = false; }
        if (!isValid) { e.preventDefault(); document.querySelector('.is-invalid')?.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
    });
});