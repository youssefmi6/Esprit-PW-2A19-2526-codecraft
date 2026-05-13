<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controller/report_controller.php';

$reportController = new ReportController();
$rapports = $reportController->obtenirRapports();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapports Analytiques - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../../css/backoffice.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f6fa;
            min-height: 100vh;
        }
        
        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
        }
        
        .generation-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        
        .button-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            text-align: center;
        }
        
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-success {
            background-color: #27ae60;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #229954;
        }
        
        .btn-small {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .table-rapports {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .table-rapports th {
            background-color: #34495e;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        .table-rapports td {
            padding: 12px 15px;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .table-rapports tr:hover {
            background-color: #f8f9fa;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-mensuel { background-color: #d4edda; color: #155724; }
        .badge-trimestriel { background-color: #cce5ff; color: #004085; }
        .badge-annuel { background-color: #fff3cd; color: #856404; }
        .badge-custom { background-color: #f8d7da; color: #721c24; }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        form {
            display: inline;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        input[type="date"],
        input[type="text"] {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #bdc3c7;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .custom-date-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #3498db;
        }
        
        .custom-date-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        @media (max-width: 768px) {
            .button-group {
                grid-template-columns: 1fr;
            }
            .custom-date-inputs {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Rapports Analytiques</h1>
        
        <!-- Section Génération de Rapports -->
        <div class="generation-section">
            <div class="section-title">Générer un Rapport</div>
            
            <div class="button-group">
                <form method="POST" action="../../api/generer_rapport.php" style="width: 100%;">
                    <input type="hidden" name="type" value="mensuel">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        📅 Rapport Mensuel (Mois courant)
                    </button>
                </form>
                
                <form method="POST" action="../../api/generer_rapport.php" style="width: 100%;">
                    <input type="hidden" name="type" value="trimestriel">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        📊 Rapport Trimestriel (Trimestre courant)
                    </button>
                </form>
                
                <form method="POST" action="../../api/generer_rapport.php" style="width: 100%;">
                    <input type="hidden" name="type" value="annuel">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        📈 Rapport Annuel (Année courante)
                    </button>
                </form>
            </div>
            
            <div class="custom-date-section">
                <div class="section-title" style="margin-bottom: 15px;">Rapport Personnalisé</div>
                <form method="POST" action="../../api/generer_rapport.php">
                    <input type="hidden" name="type" value="custom">
                    <div class="custom-date-inputs">
                        <div class="form-group">
                            <label for="date_debut">Date de début</label>
                            <input type="date" id="date_debut" name="date_debut" required>
                        </div>
                        <div class="form-group">
                            <label for="date_fin">Date de fin</label>
                            <input type="date" id="date_fin" name="date_fin" required>
                        </div>
                        <div class="form-group">
                            <label for="titre">Titre (optionnel)</label>
                            <input type="text" id="titre" name="titre" placeholder="Ex: Rapport spécial">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success" style="width: 100%;">Générer</button>
                </form>
            </div>
        </div>
        
        <!-- Liste des Rapports -->
        <div class="generation-section">
            <div class="section-title">Rapports Générés</div>
            
            <?php if ($rapports && $rapports->num_rows > 0): ?>
                <table class="table-rapports">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Type</th>
                            <th>Période</th>
                            <th>Date Génération</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($rapport = $rapports->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($rapport['titre']); ?></td>
                            <td><span class="badge badge-<?php echo strtolower($rapport['type']); ?>"><?php echo $rapport['type']; ?></span></td>
                            <td><?php echo date('d/m/Y', strtotime($rapport['date_debut'])); ?> → <?php echo date('d/m/Y', strtotime($rapport['date_fin'])); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($rapport['date_generation'])); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <form method="POST" action="../../api/telecharger_rapport.php" style="display: inline;">
                                        <input type="hidden" name="id_rapport" value="<?php echo $rapport['id_rapport']; ?>">
                                        <input type="hidden" name="format" value="csv">
                                        <button type="submit" class="btn btn-small btn-primary">CSV</button>
                                    </form>
                                    <form method="POST" action="../../api/telecharger_rapport.php" style="display: inline;">
                                        <input type="hidden" name="id_rapport" value="<?php echo $rapport['id_rapport']; ?>">
                                        <input type="hidden" name="format" value="pdf">
                                        <button type="submit" class="btn btn-small btn-primary">PDF</button>
                                    </form>
                                    <form method="POST" action="../../api/supprimer_rapport.php" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr?');">
                                        <input type="hidden" name="id_rapport" value="<?php echo $rapport['id_rapport']; ?>">
                                        <button type="submit" class="btn btn-small" style="background-color: #e74c3c; color: white;">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <p>Aucun rapport généré pour le moment.</p>
                    <p style="margin-top: 10px; font-size: 12px;">Générez votre premier rapport en utilisant les boutons ci-dessus.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
