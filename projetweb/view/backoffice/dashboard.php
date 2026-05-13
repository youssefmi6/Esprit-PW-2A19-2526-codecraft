<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controller/dashboard_controller.php';

$dashboardController = new DashboardController();
$resume = $dashboardController->obtenirResume();
$tableau_bord = $dashboardController->obtenirTableauBord();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Analytique - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../../css/backoffice.css">
    <!-- Chart.js pour les graphiques -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <style>
        .dashboard-container {
            padding: 20px;
            background-color: #f5f6fa;
            min-height: 100vh;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .card-title {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
            text-transform: uppercase;
            font-weight: 600;
        }
        
        .card-value {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .card-subtitle {
            font-size: 12px;
            color: #999;
            margin-top: 8px;
        }
        
        .card-success { border-left: 4px solid #27ae60; }
        .card-warning { border-left: 4px solid #f39c12; }
        .card-danger { border-left: 4px solid #e74c3c; }
        .card-info { border-left: 4px solid #3498db; }
        
        .chart-container {
            position: relative;
            height: 400px;
            margin-bottom: 30px;
        }
        
        .chart-wrapper {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .chart-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        @media (max-width: 768px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-success { background-color: #d4edda; color: #155724; }
        .badge-warning { background-color: #fff3cd; color: #856404; }
        .badge-danger { background-color: #f8d7da; color: #721c24; }
        
        .table-retards {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .table-retards th {
            background-color: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        
        .table-retards td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .table-retards tr:hover {
            background-color: #f8f9fa;
        }
        
        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
        }
        
        .export-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-top: 30px;
        }
        
        .export-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 10px 20px;
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
        
        .btn-success {
            background-color: #27ae60;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #229954;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>📊 Dashboard Analytique</h1>
        <div class="dashboard-actions" style="margin-bottom: 20px; display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
            <form method="POST" action="../../api/export.php" style="display: inline;">
                <input type="hidden" name="action" value="export_json">
                <button type="submit" class="btn btn-primary">Exporter JSON</button>
            </form>
            <form method="POST" action="../../api/export.php" style="display: inline;">
                <input type="hidden" name="action" value="export_csv">
                <button type="submit" class="btn btn-primary">Exporter CSV</button>
            </form>
            <a href="rapports.php" class="btn btn-success">Générer Rapport Complet</a>
        </div>
        
        <!-- Résumé rapide -->
        <div class="dashboard-grid">
            <div class="card card-info">
                <div class="card-title">Total Réclamations</div>
                <div class="card-value"><?php echo $resume['total_reclamations']; ?></div>
                <div class="card-subtitle">Toutes les périodes</div>
            </div>
            
            <div class="card card-warning">
                <div class="card-title">En Attente</div>
                <div class="card-value"><?php echo $resume['en_attente']; ?></div>
                <div class="card-subtitle">À traiter en priorité</div>
            </div>
            
            <div class="card card-danger">
                <div class="card-title">En Retard</div>
                <div class="card-value"><?php echo $resume['reclamations_en_retard']; ?></div>
                <div class="card-subtitle">Dépasse la limite de résolution</div>
            </div>
            
            <div class="card card-success">
                <div class="card-title">Taux Résolution</div>
                <div class="card-value"><?php echo $resume['taux_resolution']; ?>%</div>
                <div class="card-subtitle"><?php echo $resume['reglees']; ?> résolues</div>
            </div>
            
            <div class="card card-info">
                <div class="card-title">Temps Moyen</div>
                <div class="card-value"><?php echo $resume['temps_resolution_moyen_jours']; ?> j</div>
                <div class="card-subtitle">Résolution en jours</div>
            </div>
            
            <div class="card card-success">
                <div class="card-title">Satisfaction</div>
                <div class="card-value"><?php echo $resume['satisfaction_moyenne']; ?>/5</div>
                <div class="card-subtitle">Note moyenne</div>
            </div>
        </div>
        
        <!-- Graphiques -->
        <div class="grid-2">
            <!-- Statuts -->
            <div class="chart-wrapper">
                <div class="chart-title">Distribution par Statut</div>
                <div class="chart-container">
                    <canvas id="chartStatut"></canvas>
                </div>
            </div>
            
            <!-- Priorités -->
            <div class="chart-wrapper">
                <div class="chart-title">Distribution par Priorité</div>
                <div class="chart-container">
                    <canvas id="chartPriorite"></canvas>
                </div>
            </div>
        </div>
        
        <div class="grid-2">
            <!-- Volume mensuel -->
            <div class="chart-wrapper">
                <div class="chart-title">Volume Mensuel (12 derniers mois)</div>
                <div class="chart-container">
                    <canvas id="chartVolumeMensuel"></canvas>
                </div>
            </div>
            
            <!-- Satisfaction par agent -->
            <div class="chart-wrapper">
                <div class="chart-title">Satisfaction par Agent</div>
                <div class="chart-container">
                    <canvas id="chartSatisfactionAgent"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Réclamations en retard -->
        <div class="chart-wrapper">
            <div class="chart-title">⚠️ Réclamations en Retard</div>
            <?php if (count($tableau_bord['reclamations_en_retard']) > 0): ?>
                <table class="table-retards">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Titre</th>
                            <th>Agent</th>
                            <th>Heures en Retard</th>
                            <th>Priorité</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tableau_bord['reclamations_en_retard'] as $retard): ?>
                        <tr>
                            <td><?php echo $retard['id']; ?></td>
                            <td><?php echo htmlspecialchars(substr($retard['titre'], 0, 50)); ?></td>
                            <td><?php echo $retard['nom_agent'] ?? 'Non assigné'; ?></td>
                            <td><?php echo $retard['heures_en_retard']; ?>h</td>
                            <td><span class="status-badge badge-danger"><?php echo $retard['priorite']; ?></span></td>
                            <td><span class="status-badge badge-warning"><?php echo $retard['status']; ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: #27ae60; margin-top: 15px;">✓ Aucune réclamation en retard!</p>
            <?php endif; ?>
        </div>
        
        <!-- Boutons d'export -->
        <div class="export-section">
            <h3>📥 Exporter les Données</h3>
            <div class="export-buttons">
                <form method="POST" action="../../api/export.php" style="display: inline;">
                    <input type="hidden" name="action" value="export_json">
                    <button type="submit" class="btn btn-primary">JSON</button>
                </form>
                <form method="POST" action="../../api/export.php" style="display: inline;">
                    <input type="hidden" name="action" value="export_csv">
                    <button type="submit" class="btn btn-primary">CSV</button>
                </form>
                <a href="rapports.php" class="btn btn-success">Générer Rapport Complet</a>
            </div>
        </div>
    </div>
    
    <script>
        // Données pour les graphiques
        const statuts = <?php echo json_encode($tableau_bord['stats_globales']['par_statut'] ?? []); ?>;
        const priorites = <?php echo json_encode($tableau_bord['stats_globales']['par_priorite'] ?? []); ?>;
        const volumeMensuel = <?php echo json_encode($tableau_bord['volume_mensuel'] ?? []); ?>;
        const classementAgents = <?php echo json_encode($tableau_bord['classement_agents'] ?? []); ?>;
        
        // Graphique des statuts
        new Chart(document.getElementById('chartStatut'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(statuts),
                datasets: [{
                    data: Object.values(statuts),
                    backgroundColor: ['#3498db', '#27ae60', '#e74c3c', '#f39c12'],
                    borderColor: ['#2980b9', '#229954', '#c0392b', '#d68910'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
        
        // Graphique des priorités
        new Chart(document.getElementById('chartPriorite'), {
            type: 'bar',
            data: {
                labels: Object.keys(priorites),
                datasets: [{
                    label: 'Nombre',
                    data: Object.values(priorites),
                    backgroundColor: ['#27ae60', '#f39c12', '#e74c3c', '#c0392b'],
                    borderColor: ['#229954', '#d68910', '#c0392b', '#a93226'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { beginAtZero: true }
                }
            }
        });
        
        // Graphique du volume mensuel
        new Chart(document.getElementById('chartVolumeMensuel'), {
            type: 'line',
            data: {
                labels: Object.keys(volumeMensuel),
                datasets: [{
                    label: 'Total',
                    data: Object.values(volumeMensuel).map(v => v.total),
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    tension: 0.3,
                    fill: true
                }, {
                    label: 'Résolues',
                    data: Object.values(volumeMensuel).map(v => v.reglees),
                    borderColor: '#27ae60',
                    backgroundColor: 'rgba(39, 174, 96, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
        
        // Graphique satisfaction par agent
        new Chart(document.getElementById('chartSatisfactionAgent'), {
            type: 'bar',
            data: {
                labels: classementAgents.map(a => a.nom),
                datasets: [{
                    label: 'Satisfaction (/5)',
                    data: classementAgents.map(a => a.satisfaction_moyenne),
                    backgroundColor: classementAgents.map(a => a.satisfaction_moyenne >= 4 ? '#27ae60' : a.satisfaction_moyenne >= 3 ? '#f39c12' : '#e74c3c'),
                    borderColor: '#2c3e50',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { 
                        beginAtZero: true,
                        max: 5
                    }
                }
            }
        });
    </script>
</body>
</html>
