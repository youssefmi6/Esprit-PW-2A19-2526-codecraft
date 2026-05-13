<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controller/reclamation_controller.php';

$reclamationController = new ReclamationController();
$reclamations = $reclamationController->obtenirTous();

$filter_status = $_GET['status'] ?? '';
$filter_priorite = $_GET['priorite'] ?? '';

function statutBadgeClass($status) {
    switch ($status) {
        case 'En cours':
            return 'badge-cours';
        case 'Résolu':
            return 'badge-resolu';
        case 'Rejeté':
            return 'badge-rejete';
        default:
            return 'badge-attente';
    }
}

// Filtrer les résultats
$reclamations_filtrees = array();
if ($reclamations) {
    while ($rec = $reclamations->fetch_assoc()) {
        $match = true;
        if ($filter_status && $rec['status'] !== $filter_status) $match = false;
        if ($filter_priorite && $rec['priorite'] !== $filter_priorite) $match = false;
        if ($match) $reclamations_filtrees[] = $rec;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Réclamations</title>
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
            max-width: 1200px;
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

        .navbar-menu {
            display: flex;
            gap: 20px;
        }

        .navbar-menu a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }

        .btn-new {
            background: #667eea;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 30px;
        }

        .header-card h1 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 28px;
        }

        .filters {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        select {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .btn-filter {
            background: #667eea;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-filter:hover {
            background: #764ba2;
        }

        .table-reclamations {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .table-reclamations th {
            background: #34495e;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }

        .table-reclamations td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .table-reclamations tr:hover {
            background: #f8f9fa;
        }

        .table-reclamations tr:hover td a {
            color: #667eea;
        }

        .table-reclamations a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-attente { background: #fff3cd; color: #856404; }
        .badge-cours { background: #cce5ff; color: #004085; }
        .badge-resolu { background: #d4edda; color: #155724; }
        .badge-rejete { background: #f8d7da; color: #721c24; }

        .badge-basse { background: #e2e3e5; color: #383d41; }
        .badge-moyenne { background: #fff3cd; color: #856404; }
        .badge-haute { background: #f8d7da; color: #721c24; }
        .badge-critique { background: #d32f2f; color: white; }

        .empty-state {
            background: white;
            border-radius: 10px;
            padding: 50px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .empty-state p {
            color: #666;
            font-size: 18px;
            margin-bottom: 20px;
        }

        .btn-primary {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary:hover {
            background: #764ba2;
        }

        @media (max-width: 768px) {
            .filters {
                flex-direction: column;
            }

            .table-reclamations {
                font-size: 14px;
            }

            .table-reclamations th,
            .table-reclamations td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="navbar-content">
            <div class="navbar-logo">📋 E-Business</div>
            <div class="navbar-menu">
                <a href="index.php">Accueil</a>
                <a href="creer.php" class="btn-new">+ Nouvelle</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- En-tête -->
        <div class="header-card">
            <h1>📊 Mes Réclamations</h1>

            <!-- Filtres -->
            <div class="filters">
                <form method="GET" style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <div class="filter-group">
                        <label for="status">Statut:</label>
                        <select name="status" id="status">
                            <option value="">Tous</option>
                            <option value="En attente" <?php echo $filter_status === 'En attente' ? 'selected' : ''; ?>>En attente</option>
                            <option value="En cours" <?php echo $filter_status === 'En cours' ? 'selected' : ''; ?>>En cours</option>
                            <option value="Résolu" <?php echo $filter_status === 'Résolu' ? 'selected' : ''; ?>>Résolu</option>
                            <option value="Rejeté" <?php echo $filter_status === 'Rejeté' ? 'selected' : ''; ?>>Rejeté</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="priorite">Priorité:</label>
                        <select name="priorite" id="priorite">
                            <option value="">Tous</option>
                            <option value="Basse" <?php echo $filter_priorite === 'Basse' ? 'selected' : ''; ?>>Basse</option>
                            <option value="Moyenne" <?php echo $filter_priorite === 'Moyenne' ? 'selected' : ''; ?>>Moyenne</option>
                            <option value="Haute" <?php echo $filter_priorite === 'Haute' ? 'selected' : ''; ?>>Haute</option>
                            <option value="Critique" <?php echo $filter_priorite === 'Critique' ? 'selected' : ''; ?>>Critique</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-filter">🔍 Filtrer</button>
                </form>
            </div>
        </div>

        <!-- Tableau -->
        <?php if (!empty($reclamations_filtrees)): ?>
            <table class="table-reclamations">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Titre</th>
                        <th>Catégorie</th>
                        <th>Priorité</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reclamations_filtrees as $rec): ?>
                    <tr>
                        <td>#<?php echo $rec['id']; ?></td>
                        <td><?php echo htmlspecialchars(substr($rec['titre'], 0, 50)); ?></td>
                        <td><?php echo $rec['categorie'] ?? 'Général'; ?></td>
                        <td>
                            <span class="badge badge-<?php echo strtolower($rec['priorite'] ?? 'Moyenne'); ?>">
                                <?php echo $rec['priorite'] ?? 'Moyenne'; ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge <?php echo statutBadgeClass($rec['status']); ?>">
                                <?php echo $rec['status']; ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($rec['date'])); ?></td>
                        <td>
                            <a href="detail.php?id=<?php echo $rec['id']; ?>">👁️ Voir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <p>❌ Aucune réclamation trouvée</p>
                <p style="font-size: 14px; color: #999; margin-bottom: 20px;">Créez votre première réclamation dès maintenant</p>
                <a href="creer.php" class="btn-primary">📝 Créer une Réclamation</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
