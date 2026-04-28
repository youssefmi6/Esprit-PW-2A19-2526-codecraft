<?php
// controllers/resourceController.php
require_once __DIR__ . '/../includes/matiere_photos.php';

function getTrustedGeneratedPhotoUrl(): ?string {
    $url = trim($_POST['generated_photo_url'] ?? '');
    if ($url === '') {
        return null;
    }

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return null;
    }

    $parts = parse_url($url);
    $scheme = strtolower($parts['scheme'] ?? '');
    $host = strtolower($parts['host'] ?? '');
    if ($scheme !== 'https') {
        return null;
    }

    $allowedHosts = ['images.unsplash.com', 'source.unsplash.com', 'image.pollinations.ai'];
    if (!in_array($host, $allowedHosts, true)) {
        return null;
    }

    return $url;
}

function resourceDetail($id) {
    global $pdo;
    
    require_once __DIR__ . '/../models/resourceModel.php';
    require_once __DIR__ . '/../models/commentModel.php';
    require_once __DIR__ . '/../models/ratingModel.php';
    
    $resource = getResourceById($pdo, $id);
    if (!$resource) {
        header('Location: index.php?action=home');
        exit();
    }
    
    $currentUser = getCurrentUser($pdo);
    $hasPurchaseAccess = false;
    if ($resource['acces'] !== 'Premium') {
        $hasPurchaseAccess = true;
    } elseif ($currentUser) {
        require_once __DIR__ . '/../models/resourceModel.php';
        $isOwner = (int)$resource['id'] === (int)$currentUser['id'];
        $isBuyer = hasUserPurchasedResource($pdo, (int)$currentUser['id'], (int)$id);
        $hasPurchaseAccess = $isOwner || $isBuyer;
    }
    $comments = getCommentsByResource($pdo, $id, $currentUser ? (int)$currentUser['id'] : null);
    $hasRated = $currentUser ? hasUserRated($pdo, $id, $currentUser['id']) : false;
    $totalVotes = getTotalVotes($pdo, $id);
    
    $resource_rating = $resource['note_moyenne'] ?? 0;
    $full_stars = floor($resource_rating);
    $half_star = ($resource_rating - $full_stars) >= 0.5;
    
    $matiere_icons = get_matiere_default_photos_map();
    
    require_once __DIR__ . '/../views/resource/detail.php';
}

function resourceUploadGet() {
    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }
    
    global $pdo;
    $user = getCurrentUser($pdo);
    
    require_once __DIR__ . '/../views/resource/upload.php';
}

function resourceUploadPost() {
    global $pdo;
    
    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }
    
    $user = getCurrentUser($pdo);
    
    $uploadDir = __DIR__ . '/../uploads/';
    $imageDir = __DIR__ . '/../uploads/images/';
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
    if (!file_exists($imageDir)) mkdir($imageDir, 0777, true);
    
    $matiere_icons = get_matiere_default_photos_map();
    
    $titre = $_POST['titre'] ?? '';
    $description = $_POST['description'] ?? '';
    $matiere = $_POST['matiere'] ?? 'Autre';
    $type = $_POST['type'] ?? '';
    $niveau = $_POST['niveau'] ?? '';
    $acces = $_POST['acces'] ?? 'Gratuit';
    $prix = ($acces == 'Premium') ? floatval($_POST['prix'] ?? 0) : 0;
    $pages = intval($_POST['pages'] ?? 0);
    
    $photo = $matiere_icons[$matiere] ?? $matiere_icons['Autre'];
    $generatedPhotoUrl = getTrustedGeneratedPhotoUrl();
    if (!empty($generatedPhotoUrl)) {
        $photo = $generatedPhotoUrl;
    }
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
            $new_filename = 'img_' . time() . '_' . uniqid() . '.' . $ext;
            $photo = 'uploads/images/' . $new_filename;
            move_uploaded_file($_FILES['photo']['tmp_name'], $imageDir . $new_filename);
        }
    }
    
    $fichier = '';
    if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['fichier']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['pdf','doc','docx','txt'])) {
            $new_filename = time() . '_' . uniqid() . '.' . $ext;
            $fichier = 'uploads/' . $new_filename;
            move_uploaded_file($_FILES['fichier']['tmp_name'], $uploadDir . $new_filename);
        }
    }
    
    require_once __DIR__ . '/../models/resourceModel.php';
    $data = [
        'titre' => $titre, 'description' => $description, 'matiere' => $matiere,
        'type' => $type, 'niveau' => $niveau, 'acces' => $acces, 'prix' => $prix,
        'fichier' => $fichier, 'photo' => $photo, 'user_id' => $user['id'], 'pages' => $pages
    ];
    createResource($pdo, $data);
    
    header('Location: index.php?action=profile');
    exit();
}

function resourceEditGet($id) {
    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }
    
    global $pdo;
    $user = getCurrentUser($pdo);
    
    require_once __DIR__ . '/../models/resourceModel.php';
    $resource = getResourceById($pdo, $id);
    
    if (!$resource || $resource['user_id'] != $user['id']) {
        header('Location: index.php?action=profile');
        exit();
    }
    
    require_once __DIR__ . '/../views/resource/edit.php';
}

function resourceEditPost($id) {
    global $pdo;
    
    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }
    
    $user = getCurrentUser($pdo);
    
    require_once __DIR__ . '/../models/resourceModel.php';
    $resource = getResourceById($pdo, $id);
    
    if (!$resource || $resource['user_id'] != $user['id']) {
        header('Location: index.php?action=profile');
        exit();
    }
    
    $uploadDir = __DIR__ . '/../uploads/';
    $imageDir = __DIR__ . '/../uploads/images/';
    
    $titre = $_POST['titre'] ?? '';
    $description = $_POST['description'] ?? '';
    $matiere = $_POST['matiere'] ?? 'Autre';
    $type = $_POST['type'] ?? '';
    $niveau = $_POST['niveau'] ?? '';
    $acces = $_POST['acces'] ?? 'Gratuit';
    $prix = ($acces == 'Premium') ? floatval($_POST['prix'] ?? 0) : 0;
    $pages = intval($_POST['pages'] ?? 0);
    
    $photo = $resource['photo'];
    $generatedPhotoUrl = getTrustedGeneratedPhotoUrl();
    if (!empty($generatedPhotoUrl)) {
        $photo = $generatedPhotoUrl;
    }
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
            $new_filename = 'img_' . time() . '_' . uniqid() . '.' . $ext;
            $photo = 'uploads/images/' . $new_filename;
            move_uploaded_file($_FILES['photo']['tmp_name'], $imageDir . $new_filename);
        }
    }
    
    $fichier = $resource['fichier'];
    if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['fichier']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['pdf','doc','docx','txt'])) {
            $new_filename = time() . '_' . uniqid() . '.' . $ext;
            $fichier = 'uploads/' . $new_filename;
            move_uploaded_file($_FILES['fichier']['tmp_name'], $uploadDir . $new_filename);
        }
    }
    
    $data = [
        'titre' => $titre, 'description' => $description, 'matiere' => $matiere,
        'type' => $type, 'niveau' => $niveau, 'acces' => $acces, 'prix' => $prix,
        'pages' => $pages, 'photo' => $photo, 'fichier' => $fichier
    ];
    updateResource($pdo, $id, $data);
    
    header('Location: index.php?action=profile');
    exit();
}

function resourceDelete($id) {
    global $pdo;
    
    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }
    
    $user = getCurrentUser($pdo);
    
    require_once __DIR__ . '/../models/resourceModel.php';
    deleteResource($pdo, $id, $user['id']);
    
    header('Location: index.php?action=profile');
    exit();
}

function resourceDownload($id) {
    global $pdo;
    
    require_once __DIR__ . '/../models/resourceModel.php';
    $resource = getResourceById($pdo, $id);
    
    if (!$resource) {
        header('Location: index.php?action=home');
        exit();
    }

    if (($resource['acces'] ?? '') === 'Premium') {
        if (!isLoggedIn()) {
            header('Location: index.php?action=login');
            exit();
        }

        $currentUser = getCurrentUser($pdo);
        $isOwner = $currentUser && ((int)$resource['id'] === (int)$currentUser['id']);
        $isBuyer = $currentUser && hasUserPurchasedResource($pdo, (int)$currentUser['id'], (int)$id);
        if (!$isOwner && !$isBuyer) {
            header('Location: index.php?action=resource&subaction=detail&id=' . (int)$id . '&error=access_denied');
            exit();
        }
    }
    
    incrementDownloads($pdo, $id);
    
    $file_path = __DIR__ . '/../' . $resource['fichier'];
    if (file_exists($file_path)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($resource['fichier']) . '"');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit();
    }
    
    header('Location: index.php?action=resource&subaction=detail&id=' . $id);
    exit();
}

function resourceBuyGet($id) {
    header('Location: index.php?action=resource&subaction=buy_checkout&id=' . (int)$id);
    exit();
}

function resourceBuyPost($id) {
    header('Location: index.php?action=resource&subaction=buy_checkout&id=' . (int)$id);
    exit();
}

function stripeApiRequest(string $method, string $endpoint, string $secretKey, array $payload = []): array {
    $ch = curl_init('https://api.stripe.com' . $endpoint);
    if ($ch === false) {
        return ['ok' => false, 'error' => 'Impossible d\'initialiser la connexion Stripe.'];
    }

    $headers = [
        'Authorization: Bearer ' . $secretKey
    ];

    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => strtoupper($method),
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 30
    ];

    if (!empty($payload)) {
        $options[CURLOPT_POSTFIELDS] = http_build_query($payload);
    }

    curl_setopt_array($ch, $options);
    $rawResponse = curl_exec($ch);

    if ($rawResponse === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['ok' => false, 'error' => 'Erreur réseau Stripe: ' . $error];
    }

    $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $response = json_decode($rawResponse, true);
    if (!is_array($response)) {
        $response = [];
    }

    if ($httpCode < 200 || $httpCode >= 300) {
        $stripeMessage = $response['error']['message'] ?? 'Erreur Stripe inconnue.';
        return ['ok' => false, 'error' => $stripeMessage, 'status' => $httpCode, 'response' => $response];
    }

    return ['ok' => true, 'response' => $response];
}

function resourceBuyCreateCheckout($id) {
    global $pdo;

    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }

    require_once __DIR__ . '/../models/resourceModel.php';
    $resource = getResourceById($pdo, $id);
    if (!$resource || $resource['acces'] !== 'Premium') {
        header('Location: index.php?action=home');
        exit();
    }

    $currentUser = getCurrentUser($pdo);
    if (!$currentUser) {
        header('Location: index.php?action=login');
        exit();
    }

    $secretKey = getStripeConfigValue('STRIPE_SECRET_KEY');
    $currency = strtolower(getStripeConfigValue('STRIPE_CURRENCY'));
    if ($currency === '') {
        $currency = 'eur';
    }

    if ($secretKey === '') {
        header('Location: index.php?action=resource&subaction=detail&id=' . (int)$id . '&error=stripe_not_configured');
        exit();
    }

    $price = (float)($resource['prix'] ?? 0);
    $unitAmount = (int)round($price * 100);
    if ($unitAmount <= 0) {
        header('Location: index.php?action=resource&subaction=detail&id=' . (int)$id . '&error=invalid_amount');
        exit();
    }

    $baseUrl = getAppBaseUrl();
    $sessionResult = stripeApiRequest('POST', '/v1/checkout/sessions', $secretKey, [
        'mode' => 'payment',
        'success_url' => $baseUrl . '/index.php?action=resource&subaction=buy_success&id=' . (int)$id . '&session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => $baseUrl . '/index.php?action=resource&subaction=buy_cancel&id=' . (int)$id,
        'client_reference_id' => (string)((int)$currentUser['id']),
        'metadata[resource_id]' => (string)((int)$resource['id_res']),
        'metadata[user_id]' => (string)((int)$currentUser['id']),
        'line_items[0][quantity]' => '1',
        'line_items[0][price_data][currency]' => $currency,
        'line_items[0][price_data][unit_amount]' => (string)$unitAmount,
        'line_items[0][price_data][product_data][name]' => (string)$resource['titre'],
        'line_items[0][price_data][product_data][description]' => 'Acces premium StudyHub'
    ]);

    if (!$sessionResult['ok']) {
        header('Location: index.php?action=resource&subaction=detail&id=' . (int)$id . '&error=checkout_failed');
        exit();
    }

    $checkoutUrl = $sessionResult['response']['url'] ?? '';
    if ($checkoutUrl === '') {
        header('Location: index.php?action=resource&subaction=detail&id=' . (int)$id . '&error=checkout_url_missing');
        exit();
    }

    header('Location: ' . $checkoutUrl);
    exit();
}

function resourceBuyCreateIntent($id) {
    global $pdo;

    header('Content-Type: application/json; charset=utf-8');

    if (!isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['ok' => false, 'error' => 'auth_required']);
        exit();
    }

    require_once __DIR__ . '/../models/resourceModel.php';
    $resource = getResourceById($pdo, $id);
    if (!$resource || $resource['acces'] !== 'Premium') {
        http_response_code(404);
        echo json_encode(['ok' => false, 'error' => 'resource_not_found']);
        exit();
    }

    $currentUser = getCurrentUser($pdo);
    if (!$currentUser) {
        http_response_code(401);
        echo json_encode(['ok' => false, 'error' => 'auth_required']);
        exit();
    }

    $secretKey = getStripeConfigValue('STRIPE_SECRET_KEY');
    $publicKey = getStripeConfigValue('STRIPE_PUBLIC_KEY');
    $currency = strtolower(getStripeConfigValue('STRIPE_CURRENCY'));
    if ($currency === '') {
        $currency = 'eur';
    }

    if ($secretKey === '' || $publicKey === '') {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'stripe_not_configured']);
        exit();
    }

    $price = (float)($resource['prix'] ?? 0);
    $amount = (int)round($price * 100);
    if ($amount <= 0) {
        http_response_code(422);
        echo json_encode(['ok' => false, 'error' => 'invalid_amount']);
        exit();
    }

    $intentResult = stripeApiRequest('POST', '/v1/payment_intents', $secretKey, [
        'amount' => (string)$amount,
        'currency' => $currency,
        'automatic_payment_methods[enabled]' => 'true',
        'metadata[resource_id]' => (string)((int)$resource['id_res']),
        'metadata[user_id]' => (string)((int)$currentUser['id']),
        'description' => 'Achat ressource premium StudyHub'
    ]);

    if (!$intentResult['ok']) {
        http_response_code(502);
        echo json_encode(['ok' => false, 'error' => 'intent_creation_failed']);
        exit();
    }

    $intent = $intentResult['response'];
    $clientSecret = (string)($intent['client_secret'] ?? '');
    if ($clientSecret === '') {
        http_response_code(502);
        echo json_encode(['ok' => false, 'error' => 'intent_missing_client_secret']);
        exit();
    }

    echo json_encode([
        'ok' => true,
        'clientSecret' => $clientSecret,
        'publishableKey' => $publicKey
    ]);
    exit();
}

function resourceBuySuccess($id) {
    global $pdo;

    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }

    $sessionId = trim((string)($_GET['session_id'] ?? ''));
    $paymentIntentId = trim((string)($_GET['payment_intent'] ?? ''));

    if ($sessionId === '' && $paymentIntentId === '') {
        header('Location: index.php?action=resource&subaction=buy&id=' . (int)$id . '&error=missing_session');
        exit();
    }

    $secretKey = getStripeConfigValue('STRIPE_SECRET_KEY');
    if ($secretKey === '') {
        header('Location: index.php?action=resource&subaction=buy&id=' . (int)$id . '&error=stripe_not_configured');
        exit();
    }

    $currentUser = getCurrentUser($pdo);
    if (!$currentUser) {
        header('Location: index.php?action=login');
        exit();
    }

    $paymentReference = '';
    if ($paymentIntentId !== '') {
        $verifyResult = stripeApiRequest('GET', '/v1/payment_intents/' . rawurlencode($paymentIntentId), $secretKey);
        if (!$verifyResult['ok']) {
            header('Location: index.php?action=resource&subaction=buy&id=' . (int)$id . '&error=verification_failed');
            exit();
        }

        $intent = $verifyResult['response'];
        $paymentStatus = (string)($intent['status'] ?? '');
        $resourceIdMeta = (int)($intent['metadata']['resource_id'] ?? 0);
        $userIdMeta = (int)($intent['metadata']['user_id'] ?? 0);

        $isPaid = $paymentStatus === 'succeeded';
        $matchesResource = $resourceIdMeta === (int)$id;
        $matchesUser = $userIdMeta === (int)$currentUser['id'];
        if (!$isPaid || !$matchesResource || !$matchesUser) {
            header('Location: index.php?action=resource&subaction=buy&id=' . (int)$id . '&error=payment_not_validated');
            exit();
        }
        $paymentReference = (string)($intent['id'] ?? $paymentIntentId);
    } else {
        $verifyResult = stripeApiRequest('GET', '/v1/checkout/sessions/' . rawurlencode($sessionId), $secretKey);
        if (!$verifyResult['ok']) {
            header('Location: index.php?action=resource&subaction=buy&id=' . (int)$id . '&error=verification_failed');
            exit();
        }

        $session = $verifyResult['response'];
        $paymentStatus = (string)($session['payment_status'] ?? '');
        $resourceIdMeta = (int)($session['metadata']['resource_id'] ?? 0);
        $userIdMeta = (int)($session['metadata']['user_id'] ?? 0);

        $isPaid = $paymentStatus === 'paid';
        $matchesResource = $resourceIdMeta === (int)$id;
        $matchesUser = $userIdMeta === (int)$currentUser['id'];
        if (!$isPaid || !$matchesResource || !$matchesUser) {
            header('Location: index.php?action=resource&subaction=buy&id=' . (int)$id . '&error=payment_not_validated');
            exit();
        }
        $paymentReference = (string)($session['id'] ?? $sessionId);
    }

    require_once __DIR__ . '/../models/resourceModel.php';
    registerResourcePurchase($pdo, (int)$currentUser['id'], (int)$id, $paymentReference);

    header('Location: index.php?action=resource&subaction=detail&id=' . (int)$id . '&payment=success');
    exit();
}

function resourceBuyCancel($id) {
    header('Location: index.php?action=home');
    exit();
}

function resourceAddRating() {
    global $pdo;
    
    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }
    
    $user = getCurrentUser($pdo);
    $id_res = intval($_POST['id_res'] ?? 0);
    $rating = intval($_POST['rating'] ?? 0);
    
    require_once __DIR__ . '/../models/ratingModel.php';
    require_once __DIR__ . '/../models/resourceModel.php';
    
    if ($id_res && $rating >= 1 && $rating <= 5 && !hasUserRated($pdo, $id_res, $user['id'])) {
        addRating($pdo, $id_res, $user['id'], $rating);
        updateResourceRating($pdo, $id_res);
    }
    
    header("Location: index.php?action=resource&subaction=detail&id=$id_res");
    exit();
}

function resourceAddComment() {
    global $pdo;
    
    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }
    
    $user = getCurrentUser($pdo);
    $id_res = intval($_POST['id_res'] ?? 0);
    $message = $_POST['message'] ?? '';
    
    if (!empty($message) && $id_res) {
        require_once __DIR__ . '/../models/commentModel.php';
        addComment($pdo, $id_res, $user['id'], $message);
    }
    
    header("Location: index.php?action=resource&subaction=detail&id=$id_res");
    exit();
}

function resourceUpdateComment() {
    global $pdo;

    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }

    $user = getCurrentUser($pdo);
    $id_res = intval($_POST['id_res'] ?? 0);
    $commentId = intval($_POST['id_comment'] ?? 0);
    $message = trim($_POST['message'] ?? '');

    if ($id_res > 0 && $commentId > 0 && $message !== '') {
        require_once __DIR__ . '/../models/commentModel.php';
        updateCommentByOwner($pdo, $commentId, (int)$user['id'], $message);
    }

    header("Location: index.php?action=resource&subaction=detail&id=$id_res");
    exit();
}

function resourceDeleteComment() {
    global $pdo;

    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }

    $user = getCurrentUser($pdo);
    $id_res = intval($_POST['id_res'] ?? 0);
    $commentId = intval($_POST['id_comment'] ?? 0);

    if ($id_res > 0 && $commentId > 0) {
        require_once __DIR__ . '/../models/commentModel.php';
        deleteCommentByOwner($pdo, $commentId, (int)$user['id']);
    }

    header("Location: index.php?action=resource&subaction=detail&id=$id_res");
    exit();
}

function resourceReactComment() {
    global $pdo;

    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }

    $user = getCurrentUser($pdo);
    $id_res = intval($_POST['id_res'] ?? 0);
    $commentId = intval($_POST['id_comment'] ?? 0);
    $reaction = intval($_POST['reaction'] ?? 0);

    if ($id_res > 0 && $commentId > 0 && in_array($reaction, [1, -1], true)) {
        require_once __DIR__ . '/../models/commentModel.php';
        reactToComment($pdo, $commentId, $id_res, (int)$user['id'], $reaction);
    }

    header("Location: index.php?action=resource&subaction=detail&id=$id_res");
    exit();
}
?>