<?php
// controllers/subscriptionController.php

function subscriptionPlans() {
    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }

    global $pdo;
    require_once __DIR__ . '/../models/subscriptionModel.php';

    $currentUser = getCurrentUser($pdo);
    $activeSubscription = getActiveSubscriptionByUser($pdo, $currentUser['id']);
    $catalogPlans = getPublishedCatalogPlans($pdo);

    require_once __DIR__ . '/../views/subscription/plans.php';
}

function subscriptionSubscribePost() {
    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }

    global $pdo;
    require_once __DIR__ . '/../models/subscriptionModel.php';

    $currentUser = getCurrentUser($pdo);
    $planId = (int) ($_POST['plan_id'] ?? 0);

    $payCheck = validateSubscriptionPayment($_POST);
    if (!$payCheck['ok']) {
        $_SESSION['subscription_error'] = $payCheck['error'];
        header('Location: index.php?action=subscription');
        exit();
    }

    $paymentMeta = [
        'card_holder' => $payCheck['card_holder'],
        'payment_last4' => $payCheck['payment_last4']
    ];

    if ($planId > 0) {
        $success = upsertUserSubscriptionByCatalogPlan($pdo, $currentUser['id'], $planId, 30, $paymentMeta);
        $p = getPublishedPlanById($pdo, $planId);
        $label = $p ? $p['name'] : (string) $planId;
        if ($success) {
            $_SESSION['subscription_success'] = "Abonnement « " . $label . " » activé avec succès.";
        } else {
            $_SESSION['subscription_error'] = "Ce type d'abonnement n'est pas disponible ou n'est pas publié.";
        }
    } else {
        $_SESSION['subscription_error'] = "Choisissez une offre du catalogue.";
    }

    header('Location: index.php?action=subscription');
    exit();
}

function subscriptionPlaylists() {
    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }

    global $pdo;
    require_once __DIR__ . '/../models/subscriptionModel.php';

    $currentUser = getCurrentUser($pdo);
    $activeSubscription = getActiveSubscriptionByUser($pdo, $currentUser['id']);
    $playlistRows = getAccessiblePlaylistsForUser($pdo, $currentUser['id']);

    $playlists = [];
    foreach ($playlistRows as $row) {
        $key = $row['playlist_nom'] . '|' . $row['id_abonement'];
        if (!isset($playlists[$key])) {
            $playlists[$key] = [
                'name' => $row['playlist_nom'],
                'description' => $row['playlist_description'],
                'required_rank' => intval($row['id_abonement']),
                'resources' => []
            ];
        }

        if (!empty($row['id_res'])) {
            $playlists[$key]['resources'][] = $row;
        }
    }

    require_once __DIR__ . '/../views/subscription/playlists.php';
}

?>
