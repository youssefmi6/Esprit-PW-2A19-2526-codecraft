// subscription-admin-form.js — Contrôle de saisie (création / édition d'abonnement admin)

(function() {
    function needsCustomPlan(catalog) {
        return !catalog || catalog.value === '0' || catalog.value === '';
    }

    function parseCustomPrix(input) {
        if (!input) return { ok: true, value: 0 };
        const raw = String(input.value || '').trim().replace(',', '.');
        if (raw === '') return { ok: true, value: 0 };
        const n = parseFloat(raw);
        if (isNaN(n) || n < 0 || n > 99999999) return { ok: false, value: NaN };
        return { ok: true, value: n };
    }

    function validateCustomBlock(catalog, customNom, customPrix, customDesc) {
        let ok = true;
        if (!needsCustomPlan(catalog)) {
            if (customNom) removeError(customNom);
            if (customPrix) removeError(customPrix);
            if (customDesc) removeError(customDesc);
            return true;
        }
        const nom = customNom ? customNom.value.trim() : '';
        if (!customNom || nom.length < 1 || nom.length > 100) {
            if (customNom) showError(customNom, 'Nom du plan obligatoire (1 à 100 caractères)');
            ok = false;
        } else if (customNom) removeError(customNom);

        const pr = parseCustomPrix(customPrix);
        if (customPrix && !pr.ok) {
            showError(customPrix, 'Prix invalide (nombre ≥ 0)');
            ok = false;
        } else if (customPrix) removeError(customPrix);

        if (customDesc && !validateMaxTextLength(customDesc.value, 500)) {
            showError(customDesc, 'Description limitée à 500 caractères');
            ok = false;
        } else if (customDesc) removeError(customDesc);
        return ok;
    }

    function validateAll(form) {
        const idUser = form.querySelector('select[name="id_user"]');
        const catalog = form.querySelector('#catalog_plan_id');
        const customNom = form.querySelector('input[name="custom_nom"]');
        const customPrix = form.querySelector('input[name="custom_prix"]');
        const customDesc = form.querySelector('textarea[name="custom_desc"]');
        const dateDebut = form.querySelector('input[name="date_debut"]');
        const dateFin = form.querySelector('input[name="date_fin"]');
        const cardHolder = form.querySelector('input[name="card_holder"]');
        const last4 = form.querySelector('input[name="payment_last4"]');

        let ok = true;

        const uid = idUser ? parseInt(idUser.value, 10) : 0;
        if (!idUser || !uid || uid < 1) {
            if (idUser) showError(idUser, 'Veuillez choisir un membre');
            ok = false;
        } else if (idUser) removeError(idUser);

        if (!validateCustomBlock(catalog, customNom, customPrix, customDesc)) ok = false;

        if (dateDebut && (!validateNonEmpty(dateDebut.value) || !validateIsoDate(dateDebut.value))) {
            showError(dateDebut, 'Date de début invalide (format AAAA-MM-JJ)');
            ok = false;
        } else if (dateDebut) removeError(dateDebut);

        if (dateFin && (!validateNonEmpty(dateFin.value) || !validateIsoDate(dateFin.value))) {
            showError(dateFin, 'Date de fin invalide (format AAAA-MM-JJ)');
            ok = false;
        } else if (dateFin) removeError(dateFin);

        if (dateDebut && dateFin && validateIsoDate(dateDebut.value) && validateIsoDate(dateFin.value) && !validateDateOrder(dateDebut.value, dateFin.value)) {
            showError(dateFin, 'La date de fin doit être le même jour ou après la date de début');
            ok = false;
        }

        if (cardHolder) {
            const h = cardHolder.value.trim();
            if (h.length > 120) {
                showError(cardHolder, 'Titulaire : maximum 120 caractères');
                ok = false;
            } else removeError(cardHolder);
        }

        if (last4 && !validatePaymentLast4(last4.value)) {
            showError(last4, 'Saisissez exactement 4 chiffres ou laissez vide');
            ok = false;
        } else if (last4) removeError(last4);

        return ok;
    }

    function bindLive(form) {
        const catalog = form.querySelector('#catalog_plan_id');
        const customNom = form.querySelector('input[name="custom_nom"]');
        const customPrix = form.querySelector('input[name="custom_prix"]');
        const customDesc = form.querySelector('textarea[name="custom_desc"]');
        const dateDebut = form.querySelector('input[name="date_debut"]');
        const dateFin = form.querySelector('input[name="date_fin"]');
        const idUser = form.querySelector('select[name="id_user"]');

        if (catalog) {
            catalog.addEventListener('change', function() {
                if (needsCustomPlan(catalog)) return;
                if (customNom) removeError(customNom);
                if (customPrix) removeError(customPrix);
                if (customDesc) removeError(customDesc);
            });
        }
        if (idUser) {
            idUser.addEventListener('change', function() {
                const uid = parseInt(idUser.value, 10);
                if (uid >= 1) removeError(idUser);
            });
        }
        if (customNom) {
            customNom.addEventListener('input', function() {
                if (!needsCustomPlan(catalog)) return;
                const nom = this.value.trim();
                if (nom.length >= 1 && nom.length <= 100) removeError(this);
            });
        }
        if (customPrix) {
            customPrix.addEventListener('input', function() {
                if (!needsCustomPlan(catalog)) return;
                if (parseCustomPrix(this).ok) removeError(this);
            });
        }
        if (customDesc) {
            customDesc.addEventListener('input', function() {
                if (validateMaxTextLength(this.value, 500)) removeError(this);
            });
        }
        if (dateDebut) {
            dateDebut.addEventListener('change', function() {
                if (validateIsoDate(this.value)) removeError(this);
                if (dateFin && validateIsoDate(dateDebut.value) && validateIsoDate(dateFin.value) && validateDateOrder(dateDebut.value, dateFin.value)) removeError(dateFin);
            });
        }
        if (dateFin) {
            dateFin.addEventListener('change', function() {
                if (validateIsoDate(this.value)) removeError(this);
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('adminSubscriptionForm');
        if (!form) return;

        bindLive(form);

        form.addEventListener('submit', function(e) {
            if (!validateAll(form)) {
                e.preventDefault();
                const first = form.querySelector('.is-invalid');
                if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    });
})();
