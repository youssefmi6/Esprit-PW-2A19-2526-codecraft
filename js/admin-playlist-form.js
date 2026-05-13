// admin-playlist-form.js — Validation JS du formulaire playlists admin

(function() {
    function bindSpeechToText(descriptionInput, btn) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!btn || !descriptionInput || !SpeechRecognition) {
            if (btn) btn.disabled = true;
            return;
        }

        const recognition = new SpeechRecognition();
        recognition.lang = 'fr-FR';
        recognition.interimResults = false;
        recognition.continuous = true;

        let listening = false;

        recognition.onresult = function(event) {
            let text = '';
            for (let i = event.resultIndex; i < event.results.length; i++) {
                text += event.results[i][0].transcript + ' ';
            }
            descriptionInput.value = (descriptionInput.value + ' ' + text).trim().slice(0, 50);
        };

        recognition.onend = function() {
            if (listening) recognition.start();
        };

        btn.addEventListener('click', function() {
            if (!listening) {
                listening = true;
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-danger');
                btn.innerHTML = '<i class="bi bi-stop-circle-fill"></i> Stop';
                recognition.start();
                return;
            }

            listening = false;
            btn.classList.remove('btn-danger');
            btn.classList.add('btn-outline-primary');
            btn.innerHTML = '<i class="bi bi-mic-fill"></i> Speech to text';
            recognition.stop();
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('adminPlaylistForm');
        const descriptionInput = document.getElementById('playlistDescription');
        const speechBtn = document.getElementById('speechToTextBtn');
        const nameInput = form ? form.querySelector('input[name="nom"]') : null;

        bindSpeechToText(descriptionInput, speechBtn);

        if (!form || !nameInput || !descriptionInput) return;

        form.addEventListener('submit', function(e) {
            let ok = true;
            const checkedResources = form.querySelectorAll('input[name="resources[]"]:checked').length;
            const nom = nameInput.value.trim();
            const description = descriptionInput.value.trim();

            if (nom.length < 1 || nom.length > 20) {
                showError(nameInput, 'Le nom est obligatoire (1 à 20 caractères).');
                ok = false;
            } else removeError(nameInput);

            if (description.length < 1 || description.length > 50) {
                showError(descriptionInput, 'La description est obligatoire (1 à 50 caractères).');
                ok = false;
            } else removeError(descriptionInput);

            if (checkedResources < 1) {
                ok = false;
                alert('Sélectionnez au moins une ressource.');
            }

            if (!ok) e.preventDefault();
        });
    });
})();
