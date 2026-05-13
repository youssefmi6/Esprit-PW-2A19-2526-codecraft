<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controller/reclamation_controller.php';
require_once __DIR__ . '/../../model/audit.php';
require_once __DIR__ . '/../../model/notification.php';

$reclamationController = new ReclamationController();
$message = '';
$erreurs = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'] ?? '';
    $description = $_POST['description'] ?? '';
    $email = $_POST['email'] ?? '';
    $categorie = $_POST['categorie'] ?? 'Général';
    $priorite = $_POST['priorite'] ?? 'Moyenne';
    $source = $_POST['source'] ?? 'Web';
    
    if (empty($titre)) $erreurs[] = 'Le titre est obligatoire';
    if (empty($description)) $erreurs[] = 'La description est obligatoire';
    if (empty($email)) $erreurs[] = 'L\'email est obligatoire';
    
    if (empty($erreurs)) {
        // Créer la réclamation
        $result = $reclamationController->creer($titre, $description, $categorie, $priorite, $email, $source);
        
        if ($result['success']) {
            $reclamationId = $result['id'] ?? null;

            // Enregistrer dans l'audit
            $audit = new Audit();
            $audit->enregistrerCreation($reclamationId, null, 'Client');

            // Notifier les agents
            $notification = new Notification();
            $notification->notifierNouvelleReclamation($reclamationId, $titre);

            $message = '✅ Réclamation créée avec succès! Numéro: #' . ($reclamationId ?? 'N/A');
            // Redirection après 2 secondes
            header('Refresh: 2; url=mes-reclamations.php');
        } else {
            if (!empty($result['erreurs']) && is_array($result['erreurs'])) {
                $erreurs = array_merge($erreurs, $result['erreurs']);
            } else {
                $erreurs[] = 'Erreur lors de la création';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une Réclamation</title>
    <link rel="stylesheet" href="../../css/frontoffice.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .navbar {
            background: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .navbar-content {
            max-width: 800px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .navbar-logo {
            font-size: 20px;
            font-weight: bold;
            color: #667eea;
        }

        .navbar-menu a {
            text-decoration: none;
            color: #333;
            margin: 0 15px;
            font-weight: 500;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .form-card {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .form-card h1 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .form-card p {
            color: #666;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="email"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        textarea {
            resize: vertical;
            min-height: 150px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            width: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert ul {
            margin-left: 20px;
            margin-top: 10px;
        }

        .alert li {
            margin-bottom: 5px;
        }

        .info-box {
            background-color: #e7f3ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .info-box p {
            color: #004085;
            margin: 0;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .form-card {
                padding: 20px;
            }

            .form-card h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="navbar-content">
            <div class="navbar-logo">📋 E-Business</div>
            <div>
                <a href="index.php" class="navbar-menu">← Retour</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="form-card">
            <h1>📝 Créer une Nouvelle Réclamation</h1>
            <p>Remplissez le formulaire ci-dessous pour signaler votre problème</p>

            <!-- Messages -->
            <?php if ($message): ?>
                <div class="alert alert-success">
                    ✅ <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($erreurs)): ?>
                <div class="alert alert-error">
                    <strong>❌ Erreurs:</strong>
                    <ul>
                        <?php foreach ($erreurs as $erreur): ?>
                            <li><?php echo htmlspecialchars($erreur); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Info -->
            <div class="info-box">
                <p>💡 <strong>Conseil:</strong> Fournissez des détails précis pour une résolution plus rapide</p>
            </div>

            <!-- Formulaire -->
            <form method="POST">
                <div class="form-group">
                    <label for="titre">Titre de la Réclamation *</label>
                    <input type="text" id="titre" name="titre" placeholder="Ex: Problème avec mon compte" required
                           value="<?php echo htmlspecialchars($_POST['titre'] ?? ''); ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Votre Email *</label>
                        <input type="email" id="email" name="email" placeholder="exemple@email.com" required
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="categorie">Catégorie *</label>
                        <select id="categorie" name="categorie" required>
                            <option value="Général">Sélectionner une catégorie</option>
                            <option value="Bug">🐛 Bug/Problème Technique</option>
                            <option value="Facturation">💳 Facturation</option>
                            <option value="Service Client">👥 Service Client</option>
                            <option value="Produit">📦 Produit</option>
                            <option value="Autre">❓ Autre</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="priorite">Priorité *</label>
                        <select id="priorite" name="priorite" required>
                            <option value="Moyenne">Moyenne</option>
                            <option value="Basse">Basse</option>
                            <option value="Haute">Haute</option>
                            <option value="Critique">Critique</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="source">Source *</label>
                        <select id="source" name="source" required>
                            <option value="Web">🌐 Web</option>
                            <option value="Email">📧 Email</option>
                            <option value="Chat">💬 Chat</option>
                            <option value="Téléphone">☎️ Téléphone</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description Détaillée *</label>
                    <textarea id="description" name="description" placeholder="Décrivez votre problème en détail..." required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">📤 Envoyer ma Réclamation</button>
            </form>
        </div>
    </div>
</body>
</html>
