<?php
// views/quiz/start.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Quiz - <?= escape($resource['titre']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Quiz de la ressource</h3>
        <a href="index.php?action=resource&subaction=detail&id=<?= (int)$resource['id_res'] ?>" class="btn btn-outline-secondary">Retour</a>
    </div>

    <div class="alert alert-info">
        <strong>Règle :</strong> 10 questions, note sur 10. Si vous obtenez au moins 5/10, vous recevez un certificat.
    </div>
    <?php if (isset($_GET['error']) && $_GET['error'] === 'no_context'): ?>
        <div class="alert alert-danger">
            Impossible de générer le quiz: le contenu du fichier n'est pas assez lisible/exploitable.
            Réessayez avec un fichier texte, DOCX, ou un PDF avec texte sélectionnable.
        </div>
    <?php endif; ?>
    <?php if (!empty($generationError)): ?>
        <div class="alert alert-warning"><?= escape($generationError) ?></div>
    <?php endif; ?>

    <?php if (!empty($existingCertificate)): ?>
        <div class="alert alert-success">
            Vous avez déjà un certificat pour cette ressource.
            <a href="index.php?action=quiz&subaction=certificate&id=<?= (int)$resource['id_res'] ?>&attempt=<?= (int)$existingCertificate['id'] ?>" class="alert-link">Voir mon certificat</a>
        </div>
    <?php endif; ?>

    <?php if (is_array($quizQuestions) && count($quizQuestions) === 10): ?>
        <form method="POST" action="index.php?action=quiz&subaction=submit&id=<?= (int)$resource['id_res'] ?>">
            <input type="hidden" name="questions_payload" value="<?= escape(json_encode($quizQuestions, JSON_UNESCAPED_UNICODE)) ?>">
            <?php foreach ($quizQuestions as $i => $q): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="mb-3">Q<?= $i + 1 ?>. <?= escape($q['question'] ?? '') ?></h6>
                        <?php foreach (($q['choices'] ?? []) as $cIdx => $choice): ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="q_<?= $i ?>" id="q_<?= $i ?>_<?= $cIdx ?>" value="<?= $cIdx ?>" required>
                                <label class="form-check-label" for="q_<?= $i ?>_<?= $cIdx ?>"><?= escape($choice) ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary">Terminer le quiz</button>
        </form>
    <?php else: ?>
        <a href="index.php?action=resource&subaction=detail&id=<?= (int)$resource['id_res'] ?>" class="btn btn-outline-secondary">Retour à la ressource</a>
    <?php endif; ?>
</div>
</body>
</html>
