<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controller/notification_controller.php';

$auditController = new AuditController();
$modifications = $auditController->obtenirModificationsRecentes(100);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Trail - Historique - <?php echo APP_NAME; ?></title>
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
        
        .filter-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        input[type="date"],
        input[type="text"],
        select {
            padding: 8px 12px;
            border: 1px solid #bdc3c7;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .btn {
            padding: 8px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
        
        .btn-export {
            background-color: #27ae60;
            color: white;
        }
        
        .btn-export:hover {
            background-color: #229954;
        }
        
        .table-audit {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .table-audit th {
            background-color: #34495e;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }
        
        .table-audit td {
            padding: 12px 15px;
            border-bottom: 1px solid #ecf0f1;
            font-size: 13px;
        }
        
        .table-audit tr:hover {
            background-color: #f8f9fa;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .badge-creation { background-color: #d4edda; color: #155724; }
        .badge-modification { background-color: #cce5ff; color: #004085; }
        .badge-status-change { background-color: #fff3cd; color: #856404; }
        .badge-suppression { background-color: #f8d7da; color: #721c24; }
        
        .value-old {
            color: #e74c3c;
            text-decoration: line-through;
            font-size: 12px;
        }
        
        .value-new {
            color: #27ae60;
            font-weight: 600;
            font-size: 12px;
        }
        
        .empty-state {
            background: white;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            color: #7f8c8d;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .export-section {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        
        @media (max-width: 768px) {
            .filter-row {
                grid-template-columns: 1fr;
            }
            .export-section {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Audit Trail - Historique des Modifications</h1>
        
        <!-- Filtres -->
        <div class="filter-section">
            <form method="GET">
                <div class="filter-row">
                    <div class="form-group">
                        <label for="type">Type de Modification</label>
                        <select id="type" name="type">
                            <option value="">Tous</option>
                            <option value="Creation">Création</option>
                            <option value="Modification">Modification</option>
                            <option value="Status_change">Changement de Statut</option>
                            <option value="Suppression">Suppression</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="date_debut">Date de début</label>
                        <input type="date" id="date_debut" name="date_debut">
                    </div>
                    <div class="form-group">
                        <label for="date_fin">Date de fin</label>
                        <input type="date" id="date_fin" name="date_fin">
                    </div>
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary">Filtrer</button>
                        <a href="" class="btn btn-secondary" style="text-decoration: none; text-align: center; display: inline-block;">Réinitialiser</a>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Export -->
        <div class="export-section">
            <form method="POST" action="../../api/export_audit.php">
                <input type="hidden" name="date_debut" value="<?php echo $_GET['date_debut'] ?? ''; ?>">
                <input type="hidden" name="date_fin" value="<?php echo $_GET['date_fin'] ?? ''; ?>">
                <button type="submit" class="btn btn-export">📥 Exporter en CSV</button>
            </form>
        </div>
        
        <!-- Tableau d'audit -->
        <?php if ($modifications && $modifications->num_rows > 0): ?>
            <table class="table-audit">
                <thead>
                    <tr>
                        <th>Date/Heure</th>
                        <th>Réclamation</th>
                        <th>Type</th>
                        <th>Modification</th>
                        <th>Utilisateur</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $modifications->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i:s', strtotime($row['date_modification'])); ?></td>
                        <td>
                            <a href="../../view/backoffice/reclamation.php?id=<?php echo $row['id_reclamation']; ?>" 
                               style="color: #3498db; text-decoration: none;">
                                #<?php echo $row['id_reclamation']; ?>
                            </a>
                        </td>
                        <td>
                            <span class="badge badge-<?php echo strtolower(str_replace('_', '-', $row['type_modification'])); ?>">
                                <?php echo str_replace('_', ' ', $row['type_modification']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($row['type_modification'] === 'Status_change'): ?>
                                <span class="value-old"><?php echo $row['ancien_statut']; ?></span> → <span class="value-new"><?php echo $row['nouveau_statut']; ?></span>
                            <?php elseif ($row['type_modification'] === 'Modification'): ?>
                                <strong><?php echo htmlspecialchars($row['champ_modifie']); ?></strong><br>
                                <span class="value-old"><?php echo substr($row['ancienne_valeur'] ?? '', 0, 50); ?></span> → <span class="value-new"><?php echo substr($row['nouvelle_valeur'] ?? '', 0, 50); ?></span>
                            <?php else: ?>
                                <em style="color: #7f8c8d;">-</em>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['nom_utilisateur'] ?? 'Système'); ?></td>
                        <td><code style="background-color: #f8f9fa; padding: 2px 6px; border-radius: 3px;"><?php echo $row['adresse_ip']; ?></code></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <p>Aucune modification enregistrée pour la période sélectionnée.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
