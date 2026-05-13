<?php
// views/quiz/result.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultat Quiz - <?= escape($resource['titre']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Résultat du quiz</h3>
        <a href="index.php?action=resource&subaction=detail&id=<?= (int)$resource['id_res'] ?>" class="btn btn-outline-secondary">Retour ressource</a>
    </div>

    <?php if ((int)$attempt['passed'] === 1): ?>
        <div class="alert alert-success">
            Bravo ! Note: <strong><?= number_format((float)$attempt['score'], 2) ?>/10</strong>.
            Vous avez obtenu votre certificat.
            <a href="index.php?action=quiz&subaction=certificate&id=<?= (int)$resource['id_res'] ?>&attempt=<?= (int)$attempt['id'] ?>" class="alert-link">Télécharger</a>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            Note: <strong><?= number_format((float)$attempt['score'], 2) ?>/10</strong>.
            Il faut 5/10 minimum pour obtenir le certificat.
        </div>
    <?php endif; ?>

    <h5 class="mb-3">Correction</h5>
    <?php foreach ($quizQuestions as $i => $q): ?>
        <?php
            $answerRow = $userAnswers[$i] ?? null;
            $selected = (int)($answerRow['selected'] ?? -1);
            $correct = (int)($q['correct_index'] ?? -1);
            $isCorrect = $selected === $correct;
        ?>
        <div class="card mb-3 border-<?= $isCorrect ? 'success' : 'danger' ?>">
            <div class="card-body">
                <h6>Q<?= $i + 1 ?>. <?= escape($q['question'] ?? '') ?></h6>
                <p class="mb-1">
                    Votre réponse:
                    <strong><?= ($selected >= 0 && isset($q['choices'][$selected])) ? escape($q['choices'][$selected]) : 'Non répondue' ?></strong>
                </p>
                <p class="mb-1">
                    Bonne réponse:
                    <strong><?= ($correct >= 0 && isset($q['choices'][$correct])) ? escape($q['choices'][$correct]) : '-' ?></strong>
                </p>
                <p class="mb-0 text-muted"><?= escape($q['explanation'] ?? '') ?></p>
            </div>
        </div>
    <?php endforeach; ?>

    <a href="index.php?action=quiz&subaction=start&id=<?= (int)$resource['id_res'] ?>" class="btn btn-primary">Refaire le quiz</a>
</div>
</body>
</html>
