<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Business | Plateforme de Réclamations</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .center-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 60vh;
            text-align: center;
        }
        
        .center-content h2 {
            font-size: 3rem;
            color: var(--secondary);
            margin-bottom: 1rem;
        }
        
        .center-content p {
            font-size: 1.3rem;
            color: var(--text);
            margin-bottom: 2rem;
        }
        
        .button-group {
            display: flex;
            gap: 2rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .button-group .btn {
            padding: 1rem 2rem;
            font-size: 1.1rem;
        }
        
        .features {
            background: white;
            padding: 3rem 2rem;
            margin: 2rem auto;
            max-width: 1200px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .features h3 {
            text-align: center;
            color: var(--secondary);
            margin-bottom: 2rem;
            font-size: 2rem;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }
        
        .feature-card {
            padding: 1.5rem;
            border-radius: 10px;
            background: var(--light);
            text-align: center;
        }
        
        .feature-card h4 {
            color: var(--primary);
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
        }
        
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
    </style>
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
                    <li><a href="#caractéristiques">Caractéristiques</a></li>
                    <li><a href="view/frontoffice/index.php" class="btn btn-primary">Portal Utilisateur</a></li>
                    <li><a href="view/backoffice/index.php" class="btn btn-secondary">Dashboard Admin</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <!-- Section Accueil -->
        <section id="accueil" class="center-content">
            <h2>Bienvenue sur E-Business</h2>
            <p>Plateforme complète pour la gestion des réclamations</p>
            <p style="font-size: 1rem; color: #666; max-width: 600px;">
                Une solution moderne et intuitive pour gérer les réclamations des clients. 
                Économie digitale, entrepreneuriat et avenir du travail.
            </p>
            
            <div class="button-group">
                <a href="view/frontoffice/index.php" class="btn btn-primary" style="font-size: 1.1rem; padding: 1rem 2rem;">
                    💼 Déposer une Réclamation
                </a>
                <a href="view/backoffice/index.php" class="btn btn-secondary" style="font-size: 1.1rem; padding: 1rem 2rem;">
                    ⚙️ Gérer les Réclamations
                </a>
            </div>
        </section>

        <!-- Caractéristiques -->
        <section id="caractéristiques" class="features">
            <h3>✨ Caractéristiques Principales</h3>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">📝</div>
                    <h4>Créer une Réclamation</h4>
                    <p>Interface simple et intuitive pour déposer vos réclamations avec tous les détails nécessaires.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h4>Suivi en Temps Réel</h4>
                    <p>Suivez le statut de votre réclamation en temps réel avec les mises à jour instantanées.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">💬</div>
                    <h4>Communication Directe</h4>
                    <p>Recevez des réponses directes et échangez avec l'équipe support.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">⚙️</div>
                    <h4>Gestion Centralisée</h4>
                    <p>Tableau de bord admin pour une gestion efficace de toutes les réclamations.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">📈</div>
                    <h4>Statistiques</h4>
                    <p>Visualisez les statistiques et les KPI de vos réclamations.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <h4>Sécurité</h4>
                    <p>Vos données sont protégées avec validation et échappement XSS.</p>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2026 E-Business Platform | Économie Digitale & Entrepreneuriat</p>
        <p>Contribuons ensemble à l'avenir du travail</p>
    </footer>
</body>
</html>
