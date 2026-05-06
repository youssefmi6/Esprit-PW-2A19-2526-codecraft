// voice-search.js - Reconnaissance vocale pour la recherche de ressources

class VoiceSearch {
    constructor() {
        // Utiliser l'API Web Speech Recognition (supportée par Chrome, Firefox, Edge)
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        
        if (!SpeechRecognition) {
            console.warn('Speech Recognition API not supported in this browser');
            this.supported = false;
            return;
        }
        
        this.supported = true;
        this.recognition = new SpeechRecognition();
        this.isListening = false;
        this.transcript = '';
        this.finalTranscript = '';
        this.silenceTimeout = null;
        this.silenceDelay = 2000; // 2 secondes de silence pour arrêter
        
        this.setupRecognition();
    }
    
    setupRecognition() {
        // Configuration de la reconnaissance vocale
        this.recognition.continuous = true;  // Continuation jusqu'à silence
        this.recognition.interimResults = true;
        this.recognition.lang = 'fr-FR'; // Français par défaut
        this.recognition.maxAlternatives = 1;
        
        // Quand la reconnaissance démarre
        this.recognition.onstart = () => {
            this.isListening = true;
            this.transcript = '';
            this.finalTranscript = '';
            this.clearSilenceTimeout();
            this.updateUI('listening');
        };
        
        // Pendant que l'utilisateur parle
        this.recognition.onresult = (event) => {
            let interim = '';
            
            // Réinitialiser le timeout de silence
            this.clearSilenceTimeout();
            
            for (let i = event.resultIndex; i < event.results.length; i++) {
                const transcript = event.results[i][0].transcript.trim();
                
                if (event.results[i].isFinal) {
                    // Résultat final - ajouter à la transcription finale
                    this.finalTranscript += transcript + ' ';
                } else {
                    // Résultat temporaire - pour l'affichage en temps réel
                    interim += transcript;
                }
            }
            
            // Afficher le texte en temps réel
            const input = document.getElementById('voiceTranscript');
            if (input) {
                input.style.display = 'block';
                input.value = this.finalTranscript + interim;
            }
            
            // Si des résultats finals, réinitier le timeout de silence
            if (event.results[event.results.length - 1].isFinal) {
                this.setSilenceTimeout();
            }
        };
        
        // Quand la reconnaissance s'arrête
        this.recognition.onend = () => {
            this.isListening = false;
            this.clearSilenceTimeout();
            
            // Si du texte a été reconnu, effectuer la recherche
            const finalText = (this.finalTranscript + this.transcript).trim();
            if (finalText) {
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.value = finalText;
                    // Appeler la fonction de recherche existante
                    if (typeof runDynamicSearch === 'function') {
                        runDynamicSearch();
                    }
                }
                
                // Afficher le statut de succès
                this.updateUI('stopped');
                
                // Masquer la transcription après 3 secondes
                setTimeout(() => {
                    const input = document.getElementById('voiceTranscript');
                    if (input) {
                        input.style.display = 'none';
                    }
                }, 3000);
            } else {
                this.updateUI('stopped');
            }
        };
        
        // Gestion des erreurs
        this.recognition.onerror = (event) => {
            console.error('Speech recognition error:', event.error);
            this.clearSilenceTimeout();
            this.updateUI('error', event.error);
            
            // Réessayer en cas de 'no-speech' ou 'audio-capture'
            if (event.error === 'no-speech' || event.error === 'audio-capture') {
                setTimeout(() => {
                    if (this.isListening) {
                        try {
                            this.recognition.start();
                        } catch (e) {
                            console.error('Error restarting recognition:', e);
                        }
                    }
                }, 500);
            }
        };
    }
    
    setSilenceTimeout() {
        this.clearSilenceTimeout();
        this.silenceTimeout = setTimeout(() => {
            console.log('Silence détecté - arrêt de la reconnaissance');
            this.finalTranscript = (this.finalTranscript + this.transcript).trim();
            this.stop();
        }, this.silenceDelay);
    }
    
    clearSilenceTimeout() {
        if (this.silenceTimeout) {
            clearTimeout(this.silenceTimeout);
            this.silenceTimeout = null;
        }
    }
    
    start() {
        if (!this.supported) {
            alert('La reconnaissance vocale n\'est pas supportée par votre navigateur. Utilisez Chrome, Firefox ou Edge.');
            return;
        }
        
        if (this.isListening) return;
        
        this.transcript = '';
        this.finalTranscript = '';
        try {
            this.recognition.start();
        } catch (e) {
            console.error('Error starting recognition:', e);
            // Essayer à nouveau après un court délai
            setTimeout(() => {
                try {
                    this.recognition.start();
                } catch (err) {
                    console.error('Error restarting recognition:', err);
                }
            }, 500);
        }
    }
    
    stop() {
        if (!this.isListening) return;
        
        try {
            this.clearSilenceTimeout();
            this.recognition.stop();
        } catch (e) {
            console.error('Error stopping recognition:', e);
        }
    }
    
    toggleListening() {
        if (this.isListening) {
            this.stop();
        } else {
            this.start();
        }
    }
    
    updateUI(state, errorMsg = '') {
        const voiceBtn = document.getElementById('voiceSearchBtn');
        const voiceStatus = document.getElementById('voiceStatus');
        
        if (!voiceBtn) return;
        
        // Réinitialiser les classes
        voiceBtn.classList.remove('listening', 'stopped', 'error');
        
        if (voiceStatus) {
            voiceStatus.innerHTML = '';
        }
        
        switch (state) {
            case 'listening':
                voiceBtn.classList.add('listening');
                voiceBtn.innerHTML = '<i class="fa-solid fa-microphone-slash"></i> Écoute...';
                voiceBtn.title = 'Cliquez pour arrêter l\'écoute';
                if (voiceStatus) {
                    voiceStatus.innerHTML = '<span style="color: #16a34a; display: flex; align-items: center; gap: 6px;"><i class="fa-solid fa-circle-dot"></i> En écoute... Parlez maintenant!</span>';
                }
                break;
                
            case 'stopped':
                voiceBtn.classList.add('stopped');
                voiceBtn.innerHTML = '<i class="fa-solid fa-microphone"></i> Chercher par voix';
                voiceBtn.title = 'Cliquez pour commencer la recherche vocale';
                if (voiceStatus) {
                    voiceStatus.innerHTML = '';
                }
                break;
                
            case 'error':
                voiceBtn.classList.add('error');
                voiceBtn.innerHTML = '<i class="fa-solid fa-microphone"></i> Erreur';
                voiceBtn.title = 'Une erreur est survenue';
                if (voiceStatus) {
                    voiceStatus.innerHTML = `<span style="color: #dc2626; display: flex; align-items: center; gap: 6px;"><i class="fa-solid fa-exclamation-circle"></i> ${this.getErrorMessage(errorMsg)}</span>`;
                }
                setTimeout(() => {
                    if (!this.isListening) {
                        voiceBtn.classList.remove('error');
                        voiceBtn.innerHTML = '<i class="fa-solid fa-microphone"></i> Chercher par voix';
                    }
                }, 3000);
                break;
        }
    }
    
    getErrorMessage(error) {
        const errors = {
            'no-speech': 'Aucune parole détectée. Parlez plus fort.',
            'audio-capture': 'Microphone introuvable. Vérifiez vos paramètres audio.',
            'network': 'Erreur réseau. Vérifiez votre connexion Internet.',
            'not-allowed': 'Accès au microphone refusé.',
            'permission-denied': 'Accès au microphone refusé. Vérifiez les paramètres.',
            'service-not-allowed': 'Service non autorisé pour cette page.',
            'bad-grammar': 'Erreur de reconnaissance. Réessayez.',
            'network-timeout': 'Connexion perdue. Réessayez.'
        };
        
        return errors[error] || `Erreur : ${error}. Réessayez.`;
    }
}

// Initialiser la reconnaissance vocale
let voiceSearch = null;

document.addEventListener('DOMContentLoaded', function() {
    voiceSearch = new VoiceSearch();
    
    const voiceBtn = document.getElementById('voiceSearchBtn');
    if (voiceBtn) {
        voiceBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (voiceSearch) {
                voiceSearch.toggleListening();
            }
        });
        
        // Feedback tactile (sur mobile)
        voiceBtn.addEventListener('touchstart', function() {
            this.style.opacity = '0.8';
        });
        
        voiceBtn.addEventListener('touchend', function() {
            this.style.opacity = '1';
        });
    }
    
    // Arrêter la reconnaissance si on quitte la page
    window.addEventListener('beforeunload', function() {
        if (voiceSearch && voiceSearch.isListening) {
            voiceSearch.stop();
        }
    });
});
