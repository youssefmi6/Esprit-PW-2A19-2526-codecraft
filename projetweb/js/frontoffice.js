// ===== FRONTOFFICE FUNCTIONS =====

// Charger les réclamations au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    chargerReclamations();
    initialiserFormulaire();
});

/**
 * Charger toutes les réclamations
 */
function chargerReclamations() {
    fetch('/projetweb/controller/reclamation_controller.php?action=lister')
        .then(response => response.json())
        .then(data => {
            afficherReclamations(data);
            // Mettre à jour les statistiques
            mettreAJourStats(data);
        })
        .catch(error => {
            console.error('Erreur:', error);
            afficherMessageErreur('Erreur lors du chargement des réclamations');
        });
}

/**
 * Afficher les réclamations dans la grille
 */
function afficherReclamations(reclamations) {
    const container = document.getElementById('listReclamations');
    
    if (!container) return;

    if (!reclamations || reclamations.length === 0) {
        container.innerHTML = '<div class="empty-state"><div class="empty-state-icon">📭</div><p>Aucune réclamation pour le moment</p></div>';
        return;
    }

    container.innerHTML = reclamations.map(rec => `
        <div class="reclamation-card" onclick="afficherDetails(${rec.id})">
            <h3>${echapperHTML(rec.titre)}</h3>
            <p class="date">📅 ${formaterDate(rec.date)}</p>
            <span class="status ${getCouleurStatut(rec.status)}">${rec.status}</span>
            <p style="margin-top: 1rem; font-size: 0.9rem; color: #666;">
                ${echapperHTML(rec.description.substring(0, 100))}...
            </p>
        </div>
    `).join('');
}

/**
 * Afficher les détails d'une réclamation
 */
function afficherDetails(id) {
    fetch('/projetweb/controller/reclamation_controller.php?action=detail&id=' + id)
        .then(response => response.json())
        .then(data => {
            const rec = data.reclamation;
            const reponses = data.reponses;

            let html = `
                <div class="detail-reclamation">
                    <h3>${echapperHTML(rec.titre)}</h3>
                    
                    <div class="detail-item">
                        <label>ID Réclamation</label>
                        <p>${rec.id}</p>
                    </div>

                    <div class="detail-item">
                        <label>Description</label>
                        <p>${echapperHTML(rec.description)}</p>
                    </div>

                    <div class="detail-item">
                        <label>Date de Création</label>
                        <p>${formaterDate(rec.date)}</p>
                    </div>

                    <div class="detail-item">
                        <label>Statut</label>
                        <p><span class="status ${getCouleurStatut(rec.status)}">${rec.status}</span></p>
                    </div>
            `;

            // Afficher les réponses
            if (reponses && reponses.length > 0) {
                html += `
                    <div class="reponses-section">
                        <h4>💬 Réponses (${reponses.length})</h4>
                `;

                reponses.forEach(rep => {
                    html += `
                        <div class="reponse-item">
                            <div class="reponse-date">📅 ${formaterDate(rep.date)}</div>
                            <div class="reponse-text">${echapperHTML(rep.reponse)}</div>
                        </div>
                    `;
                });

                html += `</div>`;
            }

            html += `</div>`;

            document.getElementById('modalDetails').innerHTML = html;
            document.getElementById('detailsModal').style.display = 'block';
        })
        .catch(error => {
            console.error('Erreur:', error);
            afficherMessageErreur('Erreur lors du chargement des détails');
        });
}

/**
 * Fermer le modal
 */
function fermerModal() {
    const modal = document.getElementById('detailsModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

/**
 * Fermer le modal si on clique en dehors
 */
window.onclick = function(event) {
    const modal = document.getElementById('detailsModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}

/**
 * Initialiser le formulaire
 */
function initialiserFormulaire() {
    const form = document.getElementById('formReclamation');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const titre = document.getElementById('titre').value;
        const description = document.getElementById('description').value;

        // Valider
        if (!validerReclamation(titre, description)) {
            return;
        }

        // Envoyer le formulaire par AJAX
        const formData = new FormData();
        formData.append('action', 'creer');
        formData.append('titre', titre);
        formData.append('description', description);

        fetch('/projetweb/controller/reclamation_controller.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                afficherMessage('messageForm', '✅ ' + data.message, 'success');
                nettoyerFormulaire('formReclamation');
                
                // Recharger les réclamations après 2 secondes
                setTimeout(() => {
                    chargerReclamations();
                    document.getElementById('messageForm').style.display = 'none';
                }, 2000);
            } else {
                afficherMessage('messageForm', '❌ ' + (data.message || 'Erreur lors de la soumission'), 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            afficherMessage('messageForm', '❌ Erreur de communication avec le serveur', 'error');
        });
    });
}

/**
 * Afficher un message d'erreur
 */
function afficherMessageErreur(message) {
    alert(message);
}

/**
 * Mettre à jour les statistiques (optionnel)
 */
function mettreAJourStats(reclamations) {
    // Peut être utilisé pour mettre à jour les statistiques du frontend
    // si nécessaire
}
