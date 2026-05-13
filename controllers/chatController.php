<?php
require_once __DIR__ . '/sql_queries.php';

// controllers/chatController.php

function chatIndex(): void
{
    global $pdo;

    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }

    require_once __DIR__ . '/../models/userModel.php';
    require_once __DIR__ . '/../models/chatModel.php';

    $currentUser = getCurrentUser($pdo);
    if (!$currentUser) {
        header('Location: index.php?action=login');
        exit();
    }

    $contacts = getChatContacts($pdo, (int)$currentUser['id']);
    $selectedUserId = isset($_GET['with']) ? (int)$_GET['with'] : 0;

    if ($selectedUserId <= 0 && !empty($contacts)) {
        $selectedUserId = (int)$contacts[0]['id'];
    }

    if ($selectedUserId === (int)$currentUser['id']) {
        $selectedUserId = 0;
    }

    $selectedUser = $selectedUserId > 0 ? getUserById($pdo, $selectedUserId) : null;
    if (!$selectedUser) {
        $selectedUserId = 0;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $receiverId = (int)($_POST['receiver_id'] ?? 0);
        $message = trim($_POST['message'] ?? '');
        if ($receiverId > 0 && $message !== '') {
            createPrivateMessage($pdo, (int)$currentUser['id'], $receiverId, $message);
        }

        header('Location: index.php?action=chat&with=' . $receiverId);
        exit();
    }

    $messages = [];
    if ($selectedUserId > 0) {
        $messages = getConversationMessages($pdo, (int)$currentUser['id'], $selectedUserId);
    }

    require_once __DIR__ . '/../views/chat/index.php';
}

?>
