<?php
// views/quiz/certificate.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Certificat - <?= escape($resource['titre']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .cert-wrap { max-width: 920px; margin: 30px auto; border: 8px solid #1a8cff; border-radius: 16px; padding: 42px; background: #fff; }
        .cert-title { font-size: 42px; font-weight: 800; color: #0a5c8e; margin-bottom: 8px; }
        .cert-sub { font-size: 18px; color: #475569; margin-bottom: 22px; }
        .cert-name { font-size: 34px; font-weight: 700; color: #0f172a; margin: 20px 0; }
        .cert-meta { font-size: 15px; color: #334155; }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="cert-wrap text-center">
            <div class="cert-title">Certificat de Réussite</div>
            <div class="cert-sub">StudyHub certifie que</div>
            <div class="cert-name"><?= escape(($currentUser['prenom'] ?? '') . ' ' . ($currentUser['nom'] ?? '')) ?></div>
            <p class="mb-2">a validé le quiz de la ressource :</p>
            <h4 class="mb-4"><?= escape($resource['titre'] ?? '') ?></h4>
            <p class="cert-meta mb-1">Note obtenue : <strong><?= number_format((float)$attempt['score'], 2) ?>/10</strong></p>
            <p class="cert-meta mb-1">Code certificat : <strong><?= escape((string)$attempt['certificate_code']) ?></strong></p>
            <p class="cert-meta mb-4">Date : <strong><?= date('d/m/Y', strtotime($attempt['created_at'])) ?></strong></p>
            <div>
                <button class="btn btn-primary" onclick="window.print()">Imprimer / Enregistrer PDF</button>
                <a href="index.php?action=resource&subaction=detail&id=<?= (int)$resource['id_res'] ?>" class="btn btn-outline-secondary">Retour</a>
            </div>
        </div>
    </div>
</body>
</html>
