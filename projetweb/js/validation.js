// ===== VALIDATION ET FONCTIONS UTILES =====

/**
 * Valider la longueur d'une chaîne
 */
function validerLongueur(str, min, max = null) {
    if (!str || str.trim().length < min) return false;
    if (max && str.length > max) return false;
    return true;
}

/**
 * Valider un email
 */
function validerEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

/**
 * Afficher un message d'erreur
 */
function afficherErreur(elementId, message) {
    const element = document.getElementById(elementId);
    if (element) {
        element.textContent = message;
        element.classList.add('show');
    }
}

/**
 * Masquer un message d'erreur
 */
function masquerErreur(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.textContent = '';
        element.classList.remove('show');
    }
}

/**
 * Valider un formulaire de réclamation
 */
function validerReclamation(titre, description) {
    let valide = true;

    // Validation du titre
    if (!validerLongueur(titre, 3, 100)) {
        afficherErreur('error-titre', 'Le titre doit avoir entre 3 et 100 caractères');
        valide = false;
    } else {
        masquerErreur('error-titre');
    }

    // Validation de la description
    if (!validerLongueur(description, 10, 5000)) {
        afficherErreur('error-description', 'La description doit avoir entre 10 et 5000 caractères');
        valide = false;
    } else {
        masquerErreur('error-description');
    }

    return valide;
}

/**
 * Afficher un message de succès ou d'erreur
 */
function afficherMessage(elementId, message, type = 'success') {
    const element = document.getElementById(elementId);
    if (element) {
        element.classList.remove('success', 'error');
        element.classList.add(type);
        element.textContent = message;
    }
}

/**
 * Formater une date
 */
function formaterDate(dateStr) {
    const date = new Date(dateStr);
    const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return date.toLocaleDateString('fr-FR', options);
}

/**
 * Obtenir une couleur de statut
 */
function getCouleurStatut(statut) {
    const couleurs = {
        'En attente': 'pending',
        'En cours': 'inprogress',
        'Résolu': 'resolved',
        'Rejeté': 'rejected'
    };
    return couleurs[statut] || 'pending';
}

/**
 * Nettoyer un formulaire
 */
function nettoyerFormulaire(formId) {
    const form = document.getElementById(formId);
    if (form) {
        form.reset();
        // Masquer tous les messages d'erreur
        const erreurs = form.querySelectorAll('.error-msg');
        erreurs.forEach(err => err.classList.remove('show'));
    }
}

/**
 * Echapper le HTML pour éviter les injections XSS
 */
function echapperHTML(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

/**
 * Faire défiler la page vers un élément
 */
function scrollTo(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
}
