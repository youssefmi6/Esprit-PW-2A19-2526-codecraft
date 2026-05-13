<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Administration Réclamations</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/backoffice.css">
</head>
<body>
    <!-- Header Admin -->
    <header class="admin-header">
        <nav class="navbar-admin">
            <div class="logo-admin">
                <h1>⚙️ Dashboard Admin</h1>
            </div>
            <ul class="nav-admin">
                <li><a href="index.php" class="active">Réclamations</a></li>
                <li><a href="dashboard.php">Statistiques</a></li>
                <li><a href="parametres.php">Paramètres</a></li>
                <li><a href="#" class="btn-logout">Déconnexion</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Admin Content -->
    <main class="admin-container">
        <div class="admin-actions" style="padding: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="dashboard.php" class="btn btn-success" style="text-decoration: none; padding: 12px 20px;">Voir Statistiques</a>
            <a href="rapports.php" class="btn btn-primary" style="text-decoration: none; padding: 12px 20px;">Exporter / Rapports</a>
        </div>
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-menu">
                <button class="menu-item active" onclick="filtrerStatut('all')">
                    📋 Toutes les Réclamations
                </button>
                <button class="menu-item" onclick="filtrerStatut('En attente')">
                    ⏳ En Attente
                </button>
                <button class="menu-item" onclick="filtrerStatut('En cours')">
                    🔄 En Cours
                </button>
                <button class="menu-item" onclick="filtrerStatut('Résolu')">
                    ✅ Résolu
                </button>
                <button class="menu-item" onclick="filtrerStatut('Rejeté')">
                    ❌ Rejeté
                </button>
            </div>
        </aside>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Statistiques -->
            <section class="stats-cards">
                <div class="stat-card total">
                    <h3>Total Réclamations</h3>
                    <p id="stat-total">0</p>
                </div>
                <div class="stat-card pending">
                    <h3>En Attente</h3>
                    <p id="stat-pending">0</p>
                </div>
                <div class="stat-card inprogress">
                    <h3>En Cours</h3>
                    <p id="stat-inprogress">0</p>
                </div>
                <div class="stat-card resolved">
                    <h3>Résolus</h3>
                    <p id="stat-resolved">0</p>
                </div>
            </section>

            <!-- Tableau des Réclamations -->
            <section class="table-section">
                <h2>Gestion des Réclamations</h2>
                <table class="reclamations-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <!-- Les données seront chargées dynamiquement -->
                    </tbody>
                </table>
            </section>
        </div>
    </main>

    <!-- Modal de Détails et Réponse -->
    <div id="detailsAdminModal" class="modal-admin">
        <div class="modal-admin-content">
            <span class="close-admin" onclick="fermerModalAdmin()">&times;</span>
            <div id="adminModalDetails"></div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="admin-footer">
        <p>&copy; 2026 E-Business Admin Panel | Gestion Centralisée</p>
    </footer>

    <script src="../../js/validation.js"></script>
    <script src="../../js/backoffice.js"></script>
</body>
</html>
