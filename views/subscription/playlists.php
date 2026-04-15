<?php
function rankName($rank) {
    $r = (int) $rank;
    if ($r === 0) {
        return 'Votre offre';
    }
    return 'Accès ' . $r;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes playlists - StudyHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8fafc; font-family: Arial, sans-serif; }
        .playlist-card { border: 1px solid #e2e8f0; border-radius: 16px; background: #fff; padding: 18px; margin-bottom: 16px; }
        .resource-item { border-top: 1px dashed #e2e8f0; padding-top: 10px; margin-top: 10px; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Mes playlists abonnement</h2>
            <p class="text-muted mb-0">Playlists debloquees selon votre niveau.</p>
        </div>
        <a href="index.php?action=subscription" class="btn btn-outline-primary">Voir les plans</a>
    </div>

    <?php if ($activeSubscription): ?>
        <div class="alert alert-success">
            Niveau actif: <strong><?php echo escape($activeSubscription['nom']); ?></strong>
            (jusqu'au <?php echo escape($activeSubscription['date_fin']); ?>)
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            Aucun abonnement actif. <a href="index.php?action=subscription">Choisir un abonnement</a>
        </div>
    <?php endif; ?>

    <?php if (empty($playlists)): ?>
        <div class="alert alert-info">Aucune playlist accessible pour le moment.</div>
    <?php else: ?>
        <?php foreach ($playlists as $playlist): ?>
            <div class="playlist-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="h5 mb-0"><?php echo escape($playlist['name']); ?></h4>
                    <?php if ((int) $playlist['required_rank'] !== 0): ?>
                    <span class="badge text-bg-dark"><?php echo escape(rankName($playlist['required_rank'])); ?></span>
                    <?php else: ?>
                    <span class="badge text-bg-secondary">Inclus dans votre offre</span>
                    <?php endif; ?>
                </div>
                <p class="text-muted mb-2"><?php echo escape($playlist['description']); ?></p>

                <?php if (empty($playlist['resources'])): ?>
                    <p class="mb-0 text-secondary">Aucune ressource dans cette playlist.</p>
                <?php else: ?>
                    <?php foreach ($playlist['resources'] as $resource): ?>
                        <div class="resource-item">
                            <strong><?php echo escape($resource['titre']); ?></strong>
                            <div class="text-muted">
                                <?php echo escape($resource['matiere']); ?> | <?php echo escape($resource['niveau']); ?> | <?php echo escape($resource['type']); ?>
                            </div>
                            <a href="index.php?action=resource&subaction=detail&id=<?php echo intval($resource['id_res']); ?>" class="btn btn-sm btn-outline-primary mt-2">
                                Voir la ressource
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
