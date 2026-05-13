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
    const genBtn = document.getElementById('generateProfilePhotoBtn');
    const promptInput = document.getElementById('profilePhotoPrompt');
    const genStatus = document.getElementById('profilePhotoGenStatus');
    const avatarPreview = document.getElementById('avatarPreview');
    const generatedPhotoUrl = document.getElementById('generatedPhotoUrl');

    // --- Photo generation (should work even if validations fail) ---
    function setGenStatus(text, type) {
        if (!genStatus) return;
        genStatus.textContent = text || '';
        genStatus.className = 'small mt-2 ' + (type === 'error' ? 'text-danger' : type === 'ok' ? 'text-success' : 'text-muted');
    }

    function buildDefaultPrompt() {
        const nom = (nomInput && nomInput.value ? nomInput.value : '').trim();
        const prenom = (prenomInput && prenomInput.value ? prenomInput.value : '').trim();
        const filiere = (profileForm.querySelector('input[name="filiere"]')?.value || '').trim();
        const base = (prenom + ' ' + nom).trim() || 'student';
        const detail = filiere ? (', university student, field: ' + filiere) : ', university student';
        return 'Professional portrait photo, realistic, friendly smile, clean background, soft studio lighting, high quality' + detail + ', subject: ' + base + ', no text, no watermark';
    }

    function loadFirstWorkingImageInto(imgEl, urls, onSuccess, onFailure) {
        let idx = 0;
        let settled = false;

        function tryNext() {
            if (settled) return;
            if (idx >= urls.length) {
                settled = true;
                onFailure && onFailure();
                return;
            }

            const url = urls[idx++];
            imgEl.referrerPolicy = 'no-referrer';
            imgEl.onload = function () {
                if (settled) return;
                settled = true;
                onSuccess(url);
            };
            imgEl.onerror = function () {
                tryNext();
            };
            imgEl.src = url;
        }

        const timeout = setTimeout(() => {
            if (!settled) tryNext();
        }, 6000);

        const originalOnSuccess = onSuccess;
        onSuccess = (url) => {
            clearTimeout(timeout);
            originalOnSuccess(url);
        };

        tryNext();
    }

    if (genBtn && promptInput && avatarPreview && generatedPhotoUrl) {
        genBtn.addEventListener('click', function () {
            const custom = (promptInput.value || '').trim();
            const prompt = custom || buildDefaultPrompt();
            const seed = Date.now();
            setGenStatus('Génération en cours…', 'muted');
            if (photoInput) photoInput.value = '';
            generatedPhotoUrl.value = '';

            const body = new URLSearchParams();
            body.set('prompt', prompt);
            body.set('seed', String(seed));

            fetch('index.php?action=profile_generate_photo', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body: body.toString()
            })
            .then(async (r) => {
                const text = await r.text();
                let data = null;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    setGenStatus('Erreur serveur (réponse invalide). Ouvrez la console.', 'error');
                    console.error('profile_generate_photo non-JSON response:', text);
                    return null;
                }
                return data;
            })
            .then((data) => {
                if (!data) return;
                if (data.ok !== true || !data.url) {
                    const err = data.error ? String(data.error) : 'unknown';
                    setGenStatus('Échec génération: ' + err, 'error');
                    return;
                }
                const url = String(data.url);
                generatedPhotoUrl.value = url;
                const finalUrl = url + '?cb=' + seed;
                avatarPreview.src = finalUrl;
                // Update navbar / any other avatar on the page
                document.querySelectorAll('.user-avatar-small, .user-avatar, .profile-avatar, .profile-avatar-preview')
                    .forEach((img) => {
                        try { img.src = finalUrl; } catch (e) {}
                    });
                setGenStatus('Photo générée et appliquée automatiquement.', 'ok');
            })
            .catch((e) => {
                console.error('profile_generate_photo fetch error:', e);
                setGenStatus('Erreur réseau. Réessayez.', 'error');
            });
        });
    }
    
    if (nomInput && typeof validateName === 'function') {
        nomInput.addEventListener('input', function() {
            if (validateName(this.value)) removeError(this);
            else if (this.value.length > 0) showError(this, '2-50 caractères (lettres uniquement)');
            else removeError(this);
        });
    }
    
    if (prenomInput && typeof validateName === 'function') {
        prenomInput.addEventListener('input', function() {
            if (validateName(this.value)) removeError(this);
            else if (this.value.length > 0) showError(this, '2-50 caractères (lettres uniquement)');
            else removeError(this);
        });
    }
    
    if (emailInput && typeof validateEmail === 'function') {
        emailInput.addEventListener('input', function() {
            if (validateEmail(this.value)) removeError(this);
            else if (this.value.length > 0) showError(this, 'Email invalide');
            else removeError(this);
        });
    }
    
    if (telInput && typeof validatePhone === 'function') {
        telInput.addEventListener('input', function() {
            if (this.value === '' || validatePhone(this.value)) removeError(this);
            else showError(this, 'Format invalide. Utilisez 8 chiffres ou +216XXXXXXXX');
        });
    }
    
    if (passwordInput && typeof validatePassword === 'function') {
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

    // (photo generation moved above)
    
    profileForm.addEventListener('submit', function(e) {
        let isValid = true;
        if (nomInput && typeof validateName === 'function' && !validateName(nomInput.value)) { showError(nomInput, 'Nom invalide'); isValid = false; }
        if (prenomInput && typeof validateName === 'function' && !validateName(prenomInput.value)) { showError(prenomInput, 'Prénom invalide'); isValid = false; }
        if (emailInput && typeof validateEmail === 'function' && !validateEmail(emailInput.value)) { showError(emailInput, 'Email invalide'); isValid = false; }
        if (telInput && telInput.value && typeof validatePhone === 'function' && !validatePhone(telInput.value)) { showError(telInput, 'Format téléphone invalide'); isValid = false; }
        if (passwordInput && passwordInput.value && typeof validatePassword === 'function' && !validatePassword(passwordInput.value)) { showError(passwordInput, 'Mot de passe min 6 caractères'); isValid = false; }
        if (!isValid) { e.preventDefault(); document.querySelector('.is-invalid')?.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
    });
});