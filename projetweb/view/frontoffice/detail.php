<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controller/reclamation_controller.php';
require_once __DIR__ . '/../../model/audit.php';

$reclamationController = new ReclamationController();
$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: mes-reclamations.php');
    exit;
}

$detaille = $reclamationController->obtenirDetail($id);
$reclamation = $detaille['reclamation'];
$reponses = $detaille['reponses'];

$audit = new Audit();
$historique = $audit->obtenirHistorique($id);

$message_reponse = '';
$reponse_error = '';
$statusClass = 'badge-attente';
if ($reclamation) {
    switch ($reclamation['status']) {
        case 'En cours':
            $statusClass = 'badge-cours';
            break;
        case 'Résolu':
            $statusClass = 'badge-resolu';
            break;
        case 'Rejeté':
            $statusClass = 'badge-rejete';
            break;
        default:
            $statusClass = 'badge-attente';
    }
}
// Ajouter une réponse
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'repondre') {
    $texte = $_POST['reponse'] ?? '';
    
    if (empty($texte)) {
        $reponse_error = 'La réponse ne peut pas être vide';
    } else {
        $result = $reclamationController->ajouterReponse($id, $texte);
        if ($result['success']) {
            $message_reponse = '✅ Réponse envoyée!';
            header('Refresh: 2');
        }
    }
}

// Évaluer la réclamation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'evaluer') {
    $satisfaction = intval($_POST['satisfaction'] ?? 0);
    
    if ($satisfaction < 1 || $satisfaction > 5) {
        $reponse_error = 'Note invalide';
    } else {
        $evaluationResult = $reclamationController->mettreAJourSatisfaction($id, $satisfaction);
        if ($evaluationResult['success']) {
            $message_reponse = '✅ Évaluation enregistrée!';
            header('Refresh: 2');
            exit;
        }
        $reponse_error = $evaluationResult['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail Réclamation #<?php echo $id; ?></title>
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
            max-width: 1000px;
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
            max-width: 1000px;
            margin: 0 auto;
        }

        .detail-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 30px;
        }

        .detail-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 20px;
        }

        .detail-title h1 {
            color: #667eea;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .detail-title p {
            color: #666;
            font-size: 14px;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
        }

        .badge-attente { background: #fff3cd; color: #856404; }
        .badge-cours { background: #cce5ff; color: #004085; }
        .badge-resolu { background: #d4edda; color: #155724; }
        .badge-rejete { background: #f8d7da; color: #721c24; }

        .detail-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .meta-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .meta-label {
            font-size: 12px;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
        }

        .meta-value {
            font-size: 16px;
            color: #333;
            margin-top: 5px;
            font-weight: 600;
        }

        .section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .description {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            color: #333;
            line-height: 1.6;
            white-space: pre-wrap;
        }

        .reponses {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .reponse-item {
            background: #f0f7ff;
            padding: 15px;
            border-left: 4px solid #667eea;
            border-radius: 5px;
        }

        .reponse-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .reponse-agent {
            font-weight: 600;
            color: #667eea;
        }

        .reponse-date {
            font-size: 12px;
            color: #999;
        }

        .reponse-text {
            color: #333;
            line-height: 1.6;
            white-space: pre-wrap;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
            min-height: 100px;
        }

        textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #764ba2;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .stars {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .star {
            cursor: pointer;
            font-size: 24px;
            color: #ddd;
            transition: color 0.2s;
        }

        .star:hover,
        .star.selected {
            color: #ffc107;
        }

        @media (max-width: 768px) {
            .detail-header {
                flex-direction: column;
            }

            .detail-meta {
                grid-template-columns: 1fr 1fr;
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
                <a href="mes-reclamations.php" class="navbar-menu">← Retour</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if ($reclamation): ?>
            <!-- Détails -->
            <div class="detail-card">
                <div class="detail-header">
                    <div class="detail-title">
                        <h1><?php echo htmlspecialchars($reclamation['titre']); ?></h1>
                        <p>Réclamation #<?php echo $reclamation['id']; ?></p>
                    </div>
                    <span class="status-badge <?php echo $statusClass; ?>">
                        <?php echo $reclamation['status']; ?>
                    </span>
                </div>

                <!-- Infos -->
                <div class="detail-meta">
                    <div class="meta-item">
                        <div class="meta-label">📅 Date Création</div>
                        <div class="meta-value"><?php echo date('d/m/Y H:i', strtotime($reclamation['date'])); ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">📌 Catégorie</div>
                        <div class="meta-value"><?php echo $reclamation['categorie'] ?? 'Général'; ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">⚡ Priorité</div>
                        <div class="meta-value"><?php echo $reclamation['priorite'] ?? 'Moyenne'; ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">👤 Agent</div>
                        <div class="meta-value"><?php echo $reclamation['id_agent'] ? 'Assigné' : 'Non assigné'; ?></div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="detail-card">
                <div class="section">
                    <div class="section-title">📝 Description</div>
                    <div class="description"><?php echo htmlspecialchars($reclamation['description']); ?></div>
                </div>
            </div>

            <!-- Réponses -->
            <?php if ($reponses && $reponses->num_rows > 0): ?>
            <div class="detail-card">
                <div class="section">
                    <div class="section-title">💬 Réponses (<?php echo $reponses->num_rows; ?>)</div>
                    <div class="reponses">
                        <?php while ($rep = $reponses->fetch_assoc()): ?>
                            <div class="reponse-item">
                                <div class="reponse-header">
                                    <span class="reponse-agent">Agent Support</span>
                                    <span class="reponse-date"><?php echo date('d/m/Y H:i', strtotime($rep['date'])); ?></span>
                                </div>
                                <div class="reponse-text"><?php echo htmlspecialchars($rep['reponse']); ?></div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Ajouter une réponse -->
            <?php if ($reclamation['status'] !== 'Résolu'): ?>
            <div class="detail-card">
                <div class="section">
                    <div class="section-title">✍️ Ajouter une Réponse</div>
                    
                    <?php if ($message_reponse): ?>
                        <div class="alert alert-success"><?php echo $message_reponse; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($reponse_error): ?>
                        <div class="alert alert-error"><?php echo $reponse_error; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="action" value="repondre">
                        <div class="form-group">
                            <label for="reponse">Votre Réponse</label>
                            <textarea id="reponse" name="reponse" placeholder="Écrivez votre réponse..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">📤 Envoyer la Réponse</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Évaluation -->
            <?php if ($reclamation['status'] === 'Résolu' && !isset($reclamation['satisfaction'])): ?>
            <div class="detail-card">
                <div class="section">
                    <div class="section-title">⭐ Évaluer cette Réclamation</div>
                    
                    <form method="POST">
                        <input type="hidden" name="action" value="evaluer">
                        <div class="form-group">
                            <label>Votre Évaluation</label>
                            <div class="stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="star" onclick="document.querySelector('input[name=satisfaction]').value=<?php echo $i; ?>;document.querySelectorAll('.star').forEach((s,idx)=>s.classList.toggle('selected', idx < <?php echo $i; ?>))">⭐</span>
                                <?php endfor; ?>
                            </div>
                            <input type="hidden" name="satisfaction" value="0">
                        </div>
                        <button type="submit" class="btn btn-primary">📊 Envoyer l'Évaluation</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Historique -->
            <?php if ($historique && $historique->num_rows > 0): ?>
            <div class="detail-card">
                <div class="section">
                    <div class="section-title">📋 Historique des Modifications</div>
                    <div class="reponses">
                        <?php while ($hist = $historique->fetch_assoc()): ?>
                            <div class="reponse-item" style="background: #f9f9f9; border-left-color: #999;">
                                <div class="reponse-header">
                                    <span class="reponse-agent"><?php echo $hist['type_modification']; ?></span>
                                    <span class="reponse-date"><?php echo date('d/m/Y H:i', strtotime($hist['date_modification'])); ?></span>
                                </div>
                                <div class="reponse-text" style="font-size: 12px;">
                                    Par: <?php echo $hist['nom_utilisateur']; ?> | IP: <?php echo $hist['adresse_ip']; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="detail-card" style="text-align: center; padding: 50px;">
                <p style="color: #666; font-size: 18px;">❌ Réclamation non trouvée</p>
                <a href="mes-reclamations.php" class="btn btn-primary" style="margin-top: 20px;">Retour aux Réclamations</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
