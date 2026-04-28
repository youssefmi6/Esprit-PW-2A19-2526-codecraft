// admin-forms.js — Validation JS des formulaires admin

document.addEventListener('DOMContentLoaded', function() {
    bindAdminProfileForm();
    bindAdminEditUserForm();
    bindAdminEditResourceForm();
    bindAdminAddResourceForm();
    bindAdminSubscriptionPlanForm();
});

function bindAdminProfileForm() {
    const form = document.getElementById('adminProfileForm');
    if (!form) return;
    const nom = form.querySelector('input[name="nom"]');
    const prenom = form.querySelector('input[name="prenom"]');
    const email = form.querySelector('input[name="email"]');
    const mdp = form.querySelector('input[name="mdp"]');
    form.addEventListener('submit', function(e) {
        let ok = true;
        if (nom && !validateName(nom.value)) { showError(nom, 'Nom invalide'); ok = false; } else if (nom) removeError(nom);
        if (prenom && !validateName(prenom.value)) { showError(prenom, 'Prénom invalide'); ok = false; } else if (prenom) removeError(prenom);
        if (email && !validateEmail(email.value)) { showError(email, 'Email invalide'); ok = false; } else if (email) removeError(email);
        if (mdp && mdp.value && !validatePassword(mdp.value)) { showError(mdp, 'Mot de passe min 6 caractères'); ok = false; } else if (mdp) removeError(mdp);
        if (!ok) e.preventDefault();
    });
}

function bindAdminEditUserForm() {
    const form = document.getElementById('adminEditUserForm');
    if (!form) return;
    const nom = form.querySelector('input[name="nom"]');
    const prenom = form.querySelector('input[name="prenom"]');
    const email = form.querySelector('input[name="email"]');
    const mdp = form.querySelector('input[name="mdp"]');
    form.addEventListener('submit', function(e) {
        let ok = true;
        if (nom && !validateName(nom.value)) { showError(nom, 'Nom invalide'); ok = false; } else if (nom) removeError(nom);
        if (prenom && !validateName(prenom.value)) { showError(prenom, 'Prénom invalide'); ok = false; } else if (prenom) removeError(prenom);
        if (email && !validateEmail(email.value)) { showError(email, 'Email invalide'); ok = false; } else if (email) removeError(email);
        if (mdp && mdp.value && !validatePassword(mdp.value)) { showError(mdp, 'Mot de passe min 6 caractères'); ok = false; } else if (mdp) removeError(mdp);
        if (!ok) e.preventDefault();
    });
}

function bindAdminEditResourceForm() {
    const form = document.getElementById('adminEditResourceForm');
    if (!form) return;
    const titre = form.querySelector('input[name="titre"]');
    const description = form.querySelector('textarea[name="description"]');
    const niveau = form.querySelector('select[name="niveau"]');
    const accesSel = form.querySelector('#accessSelect');
    const prix = form.querySelector('input[name="prix"]');
    form.addEventListener('submit', function(e) {
        let ok = true;
        if (titre && !validateTitle(titre.value)) { showError(titre, 'Titre requis (min 3 caractères)'); ok = false; } else if (titre) removeError(titre);
        if (description && !validateDescription(description.value)) { showError(description, 'Description requise (min 10 caractères)'); ok = false; } else if (description) removeError(description);
        if (niveau && niveau.value === '') { showError(niveau, 'Sélectionnez un niveau'); ok = false; } else if (niveau) removeError(niveau);
        if (accesSel && accesSel.value === 'payant' && prix && !validatePrice(prix.value)) { showError(prix, 'Prix invalide'); ok = false; } else if (prix) removeError(prix);
        if (!ok) e.preventDefault();
    });
}

function bindAdminAddResourceForm() {
    const form = document.getElementById('adminAddResourceForm');
    if (!form) return;
    const titre = form.querySelector('input[name="titre"]');
    const description = form.querySelector('textarea[name="description"]');
    const pages = form.querySelector('input[name="pages"]');
    const acces = form.querySelector('#accessSelect');
    const prix = form.querySelector('input[name="prix"]');
    form.addEventListener('submit', function(e) {
        let ok = true;
        if (titre && !validateTitle(titre.value)) { showError(titre, 'Titre requis'); ok = false; } else if (titre) removeError(titre);
        if (description && !validateDescription(description.value)) { showError(description, 'Description requise (min 10 caractères)'); ok = false; } else if (description) removeError(description);
        if (pages && !validatePagesAllowZero(pages.value)) { showError(pages, 'Nombre de pages invalide (0–1000)'); ok = false; } else if (pages) removeError(pages);
        if (acces && acces.value === 'payant' && prix && !validatePrice(prix.value)) { showError(prix, 'Prix invalide'); ok = false; } else if (prix) removeError(prix);
        if (!ok) e.preventDefault();
    });
}

function bindAdminSubscriptionPlanForm() {
    const form = document.getElementById('adminSubscriptionPlanForm');
    if (!form) return;
    const name = form.querySelector('input[name="name"]');
    const description = form.querySelector('textarea[name="description"]');
    const prix = form.querySelector('input[name="prix"]');

    function runLiveName() {
        if (!name) return;
        const t = name.value.trim();
        if (t === '') removeError(name);
        else if (validateSubscriptionPlanName(name.value)) removeError(name);
    }
    function runLiveDesc() {
        if (!description) return;
        if (validateSubscriptionPlanDescription(description.value)) removeError(description);
    }
    function runLivePrix() {
        if (!prix) return;
        if (validateSubscriptionPlanPrixStrict(prix.value)) removeError(prix);
    }

    if (name) name.addEventListener('input', runLiveName);
    if (description) description.addEventListener('input', runLiveDesc);
    if (prix) prix.addEventListener('input', runLivePrix);

    form.addEventListener('submit', function(e) {
        let ok = true;
        const action = (document.activeElement && document.activeElement.name === 'save_action')
            ? document.activeElement.value
            : 'draft';
        const checkedResources = form.querySelectorAll('input[name="resources[]"]:checked').length;
        if (name) {
            if (!validateSubscriptionPlanName(name.value)) {
                showError(name, 'Lettres uniquement (accents OK), espaces entre mots, 1 à 100 caractères');
                ok = false;
            } else removeError(name);
        }
        if (description) {
            if (!validateSubscriptionPlanDescription(description.value)) {
                showError(description, 'Lettres, chiffres et espaces uniquement (max 500 caractères)');
                ok = false;
            } else removeError(description);
        }
        if (prix) {
            if (!validateSubscriptionPlanPrixStrict(prix.value)) {
                showError(prix, 'Le prix doit être un entier strictement supérieur à 0');
                ok = false;
            } else removeError(prix);
        }
        if (action === 'publish' && checkedResources < 1) {
            ok = false;
            alert('Ajoutez au moins une ressource pour publier.');
        }
        if (!ok) {
            e.preventDefault();
            form.querySelector('.is-invalid')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
}
