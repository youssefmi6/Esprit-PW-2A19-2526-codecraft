// ===== BACKOFFICE FUNCTIONS =====

let filtreActuel = 'all';

// Charger les réclamations au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    chargerReclamationsAdmin();
    mettreAJourStatistiques();
});

/**
 * Charger toutes les réclamations pour l'admin
 */
function chargerReclamationsAdmin() {
    fetch('/projetweb/controller/reclamation_controller.php?action=lister')
        .then(response => response.json())
        .then(data => {
            afficherTableauReclamations(data);
        })
        .catch(error => {
            console.error('Erreur:', error);
        });
}

/**
 * Afficher le tableau des réclamations
 */
function afficherTableauReclamations(reclamations) {
    const tbody = document.getElementById('tableBody');
    if (!tbody) return;

    // Filtrer les réclamations si nécessaire
    let reclamationsFiltrees = reclamations;
    if (filtreActuel !== 'all') {
        reclamationsFiltrees = reclamations.filter(r => r.status === filtreActuel);
    }

    if (!reclamationsFiltrees || reclamationsFiltrees.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 2rem;">Aucune réclamation trouée</td></tr>';
        return;
    }

    tbody.innerHTML = reclamationsFiltrees.map(rec => `
        <tr>
            <td>#${rec.id}</td>
            <td><strong>${echapperHTML(rec.titre)}</strong></td>
            <td>${formaterDate(rec.date)}</td>
            <td><span class="status ${getCouleurStatut(rec.status)}">${rec.status}</span></td>
            <td class="table-actions">
                <button class="btn-view btn-small" onclick="afficherDetailsAdmin(${rec.id})">Voir</button>
                <button class="btn-delete btn-small" onclick="supprimerReclamation(${rec.id})">Supprimer</button>
            </td>
        </tr>
    `).join('');
}

/**
 * Filtrer les réclamations par statut
 */
function filtrerStatut(statut) {
    filtreActuel = statut;
    
    // Mettre à jour le bouton actif
    document.querySelectorAll('.menu-item').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');

    // Recharger et filtrer
    chargerReclamationsAdmin();
}

/**
 * Afficher les détails en modal
 */
function afficherDetailsAdmin(id) {
    fetch('/projetweb/controller/reclamation_controller.php?action=detail&id=' + id)
        .then(response => response.json())
        .then(data => {
            const rec = data.reclamation;
            const reponses = data.reponses;

            let html = `
                <div class="admin-detail">
                    <h3>📋 Détails de la Réclamation #${rec.id}</h3>

                    <div class="detail-grid">
                        <div class="detail-group">
                            <label>Titre</label>
                            <p>${echapperHTML(rec.titre)}</p>
                        </div>
                        <div class="detail-group">
                            <label>Date de Création</label>
                            <p>${formaterDate(rec.date)}</p>
                        </div>
                        <div class="detail-group" style="grid-column: 1/-1;">
                            <label>Description</label>
                            <p>${echapperHTML(rec.description)}</p>
                        </div>
                    </div>

                    <!-- Mise à jour du statut -->
                    <div class="status-update">
                        <label for="updateStatus">Changer le Statut</label>
                        <select id="updateStatus" onchange="mettreAJourStatutReclamation(${rec.id}, this.value)">
                            <option value="En attente" ${rec.status === 'En attente' ? 'selected' : ''}>En attente</option>
                            <option value="En cours" ${rec.status === 'En cours' ? 'selected' : ''}>En cours</option>
                            <option value="Résolu" ${rec.status === 'Résolu' ? 'selected' : ''}>Résolu</option>
                            <option value="Rejeté" ${rec.status === 'Rejeté' ? 'selected' : ''}>Rejeté</option>
                        </select>
                    </div>
            `;

            // Afficher les réponses existantes
            if (reponses && reponses.length > 0) {
                html += `
                    <h4>💬 Réponses (${reponses.length})</h4>
                    <div class="reponses-section">
                `;

                reponses.forEach(rep => {
                    html += `
                        <div class="reponse-item">
                            <div class="reponse-date">📅 ${formaterDate(rep.date)}</div>
                            <div class="reponse-text">${echapperHTML(rep.reponse)}</div>
                            <button class="btn-delete btn-small" onclick="supprimerReponse(${rep.id_reponse})" style="margin-top: 0.5rem;">Supprimer</button>
                        </div>
                    `;
                });

                html += `</div>`;
            }

            // Formulaire pour ajouter une réponse
            html += `
                    <div class="form-reponse" style="margin-top: 2rem;">
                        <h4>Ajouter une Réponse</h4>
                        <textarea id="textReponse" placeholder="Écrivez votre réponse..."></textarea>
                        <button class="btn btn-success" onclick="envoyerReponse(${rec.id})">Envoyer la Réponse</button>
                    </div>

                </div>
            `;

            document.getElementById('adminModalDetails').innerHTML = html;
            document.getElementById('detailsAdminModal').style.display = 'block';
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors du chargement des détails');
        });
}

/**
 * Mettre à jour le statut d'une réclamation
 */
function mettreAJourStatutReclamation(id, nouveauStatut) {
    const formData = new FormData();
    formData.append('action', 'update_statut');
    formData.append('id_reclamation', id);
    formData.append('statut', nouveauStatut);

    fetch('/projetweb/controller/reclamation_controller.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Statut mis à jour avec succès');
            chargerReclamationsAdmin();
            mettreAJourStatistiques();
        } else {
            alert('❌ Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('❌ Erreur lors de la mise à jour');
    });
}

/**
 * Envoyer une réponse
 */
function envoyerReponse(idReclamation) {
    const textReponse = document.getElementById('textReponse');
    if (!textReponse) return;

    const reponse = textReponse.value.trim();

    if (!reponse || reponse.length < 5) {
        alert('La réponse doit contenir au moins 5 caractères');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'repondre');
    formData.append('id_reclamation', idReclamation);
    formData.append('reponse', reponse);

    fetch('/projetweb/controller/reclamation_controller.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Réponse ajoutée avec succès');
            textReponse.value = '';
            afficherDetailsAdmin(idReclamation);
        } else {
            alert('❌ Erreur: ' + (data.message || 'Erreur lors de l\'envoi'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('❌ Erreur de communication');
    });
}

/**
 * Supprimer une réclamation
 */
function supprimerReclamation(id) {
    if (!confirm('⚠️ Êtes-vous sûr de vouloir supprimer cette réclamation ?')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'supprimer');
    formData.append('id', id);

    fetch('/projetweb/controller/reclamation_controller.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Réclamation supprimée');
            chargerReclamationsAdmin();
            fermerModalAdmin();
        } else {
            alert('❌ Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('❌ Erreur lors de la suppression');
    });
}

/**
 * Supprimer une réponse
 */
function supprimerReponse(idReponse) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette réponse ?')) {
        return;
    }

    // Implémentation à ajouter côté backend
    alert('Fonctionnalité à implémenter');
}

/**
 * Mettre à jour les statistiques
 */
function mettreAJourStatistiques() {
    fetch('/projetweb/controller/reclamation_controller.php?action=stats')
        .then(response => response.json())
        .then(data => {
            document.getElementById('stat-total').textContent = data.total || 0;
            document.getElementById('stat-pending').textContent = data.pending || 0;
            document.getElementById('stat-inprogress').textContent = data.inprogress || 0;
            document.getElementById('stat-resolved').textContent = data.resolved || 0;
        })
        .catch(error => console.error('Erreur stats:', error));
}

/**
 * Fermer le modal
 */
function fermerModalAdmin() {
    const modal = document.getElementById('detailsAdminModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

/**
 * Fermer le modal si on clique en dehors
 */
window.onclick = function(event) {
    const modal = document.getElementById('detailsAdminModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}
