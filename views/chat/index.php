<?php
// views/chat/index.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Conversations - StudyHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .chat-wrapper { min-height: calc(100vh - 180px); }
        .chat-panel { background: #fff; border-radius: 16px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06); }
        .chat-contacts { max-height: 70vh; overflow-y: auto; border-right: 1px solid #ebf0f8; }
        .chat-messages { height: 58vh; overflow-y: auto; background: #f8fbff; border-radius: 12px; padding: 14px; }
        .contact-link { display: block; padding: 12px 14px; border-bottom: 1px solid #f0f4fa; text-decoration: none; color: #1f2937; }
        .contact-link.active { background: #eaf4ff; }
        .msg { max-width: 75%; padding: 10px 12px; border-radius: 12px; margin-bottom: 10px; }
        .msg-me { margin-left: auto; background: #1a8cff; color: white; }
        .msg-other { background: white; border: 1px solid #dce8f7; color: #1f2937; }
        .msg-meta { font-size: 12px; opacity: 0.8; margin-top: 4px; }
    </style>
</head>
<body>
<nav class="navbar-custom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-6 col-lg-3"><a href="index.php?action=home" class="logo"><img src="uploads/logo.png" alt="StudyHub" class="site-logo"></a></div>
            <div class="col-lg-5 d-none d-lg-block"><ul class="nav-links"><li><a href="index.php?action=home">Accueil</a></li><li><a href="index.php?action=resource&subaction=upload">Publier</a></li><li><a href="index.php?action=profile">Mon Profil</a></li></ul></div>
            <div class="col-6 col-lg-4 text-end">
                <a href="index.php?action=profile" class="btn btn-outline-primary">Mon profil</a>
            </div>
        </div>
    </div>
</nav>

<div class="container my-4 chat-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="fa-regular fa-comments me-2"></i>Espace de conversation</h4>
        <a href="index.php?action=profile" class="btn btn-outline-secondary">Retour profil</a>
    </div>

    <div class="row g-3 chat-panel p-3">
        <div class="col-md-4">
            <div class="chat-contacts rounded">
                <?php if (empty($contacts)): ?>
                    <p class="text-muted p-3 mb-0">Aucun utilisateur disponible.</p>
                <?php else: ?>
                    <?php foreach ($contacts as $contact): ?>
                        <a href="index.php?action=chat&with=<?= (int)$contact['id'] ?>" class="contact-link <?= ((int)$selectedUserId === (int)$contact['id']) ? 'active' : '' ?>">
                            <div class="fw-semibold"><?= escape(($contact['prenom'] ?? '') . ' ' . ($contact['nom'] ?? '')) ?></div>
                            <small class="text-muted">
                                <?= !empty($contact['last_message_at']) ? 'Dernier message: ' . date('d/m/Y H:i', strtotime($contact['last_message_at'])) : 'Pas encore de messages' ?>
                            </small>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-8">
            <?php if ($selectedUserId <= 0 || empty($selectedUser)): ?>
                <div class="alert alert-info mb-0">Choisissez un utilisateur pour commencer une conversation.</div>
            <?php else: ?>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Conversation avec <?= escape(($selectedUser['prenom'] ?? '') . ' ' . ($selectedUser['nom'] ?? '')) ?></h6>
                </div>
                <div class="chat-messages mb-3">
                    <?php if (empty($messages)): ?>
                        <p class="text-muted mb-0">Aucun message pour le moment.</p>
                    <?php else: ?>
                        <?php foreach ($messages as $message): ?>
                            <?php $isMe = (int)$message['sender_id'] === (int)$currentUser['id']; ?>
                            <div class="msg <?= $isMe ? 'msg-me' : 'msg-other' ?>">
                                <div><?= nl2br(escape($message['message_text'] ?? '')) ?></div>
                                <div class="msg-meta"><?= $isMe ? 'Vous' : escape(($selectedUser['prenom'] ?? '') . ' ' . ($selectedUser['nom'] ?? '')) ?> - <?= date('d/m/Y H:i', strtotime($message['created_at'])) ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <form method="POST" action="index.php?action=chat&with=<?= (int)$selectedUserId ?>" class="d-flex gap-2">
                    <input type="hidden" name="receiver_id" value="<?= (int)$selectedUserId ?>">
                    <input type="text" name="message" class="form-control" placeholder="Ecrire un message..." required>
                    <button type="submit" class="btn btn-primary">Envoyer</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
