<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Réclamation - Économie Digitale</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/frontoffice.css">
</head>
<body>
    <!-- Header -->
    <header>
        <nav class="navbar">
            <div class="container-nav">
                <div class="logo">
                    <h1>🚀 E-Business</h1>
                    <p>Plateforme de Réclamations</p>
                </div>
                <ul class="nav-links">
                    <li><a href="#accueil">Accueil</a></li>
                    <li><a href="#reclamations">Mes Réclamations</a></li>
                    <li><a href="#nouvelle">Nouvelle Réclamation</a></li>
                    <li><a href="mes-reclamations.php">Liste Complète</a></li>
                    <li><a href="creer.php">Page Création</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <!-- Section Héro -->
        <section id="accueil" class="hero">
            <div class="hero-content">
                <h2>Bienvenue sur notre Plateforme de Réclamations</h2>
                <p>Nous luttons pour améliorer votre expérience digitale</p>
                <p>Entrepreneuriat • Innovation • Transparence</p>
                <button class="btn btn-primary" onclick="scrollTo('nouvelle')">Déposer une Réclamation</button>
            </div>
            <div class="hero-icons">
                <span class="icon">💼</span>
                <span class="icon">🌐</span>
                <span class="icon">📱</span>
            </div>
        </section>

        <!-- Section Nouvelle Réclamation -->
        <section id="nouvelle" class="section-form">
            <div class="form-container">
                <h2>Déposer une Nouvelle Réclamation</h2>
                <form id="formReclamation" class="form-reclamation">
                    <div class="form-group">
                        <label for="titre">Titre de la Réclamation *</label>
                        <input type="text" id="titre" name="titre" placeholder="Décrivez brièvement votre problème" required>
                        <span class="error-msg" id="error-titre"></span>
                    </div>

                    <div class="form-group">
                        <label for="description">Description Détaillée *</label>
                        <textarea id="description" name="description" placeholder="Expliquez votre réclamation en détail" rows="6" required></textarea>
                        <span class="error-msg" id="error-description"></span>
                    </div>

                    <button type="submit" class="btn btn-success">Soumettre ma Réclamation</button>
                </form>
                <div id="messageForm" class="message-box"></div>
            </div>
        </section>

        <!-- Section Mes Réclamations -->
        <section id="reclamations" class="section-reclamations">
            <div class="container">
                <h2>Mes Réclamations</h2>
                <div id="listReclamations" class="reclamations-grid">
                    <!-- Les réclamations seront chargées par JavaScript -->
                </div>
            </div>
        </section>
    </main>

    <!-- Modal pour détails -->
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="fermerModal()">&times;</span>
            <div id="modalDetails"></div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2026 E-Business Platform | Économie Digitale & Entrepreneuriat</p>
        <p>Contribuons ensemble à l'avenir du travail</p>
    </footer>

    <script src="../../js/validation.js"></script>
    <script src="../../js/frontoffice.js"></script>
</body>
</html>
