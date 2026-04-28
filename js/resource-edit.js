// resource-edit.js — Formulaire modification ressource (utilisateur)

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('resourceEditForm');
    if (!form) return;

    const titre = form.querySelector('input[name="titre"]');
    const description = form.querySelector('textarea[name="description"]');
    const pages = form.querySelector('input[name="pages"]');
    const acces = form.querySelector('#accessSelect');
    const prix = form.querySelector('input[name="prix"]');
    const priceDiv = document.getElementById('priceDiv');

    form.addEventListener('submit', function(e) {
        let ok = true;
        if (titre && !validateTitle(titre.value)) {
            showError(titre, 'Titre requis (min 3 caractères)');
            ok = false;
        } else if (titre) removeError(titre);
        if (description && !validateDescription(description.value)) {
            showError(description, 'Description requise (min 10 caractères)');
            ok = false;
        } else if (description) removeError(description);
        if (pages && !validatePages(pages.value)) {
            showError(pages, 'Nombre de pages entre 1 et 1000');
            ok = false;
        } else if (pages) removeError(pages);
        if (acces && acces.value === 'Premium' && prix && !validatePrice(prix.value)) {
            showError(prix, 'Prix invalide pour une ressource premium');
            ok = false;
        } else if (prix) removeError(prix);
        if (!ok) {
            e.preventDefault();
            document.querySelector('#resourceEditForm .is-invalid')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
});
