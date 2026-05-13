<?php
// controllers/profileController.php
function profileIndex() {
    global $pdo;
    
    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }
    
    $user = getCurrentUser($pdo);
    
    require_once __DIR__ . '/../models/resourceModel.php';
    require_once __DIR__ . '/../models/userModel.php';
    require_once __DIR__ . '/../models/subscriptionModel.php';

    $activeSubscription = getActiveSubscriptionByUser($pdo, $user['id']);
    
    $userResources = getUserResources($pdo, $user['id']);
    
    $totalResources = count($userResources);
    $totalDownloads = 0;
    $totalPremium = 0;
    $totalRating = 0;
    
    foreach ($userResources as $res) {
        $totalDownloads += $res['downloads'] ?? 0;
        if ($res['acces'] == 'Premium') $totalPremium++;
        $totalRating += $res['note_moyenne'] ?? 0;
    }
    
    $avgUserRating = $totalResources > 0 ? round($totalRating / $totalResources, 1) : 0;
    
    require_once __DIR__ . '/../views/profile/view.php';
}

function profileEditGet() {
    global $pdo;
    
    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }
    
    $user = getCurrentUser($pdo);
    require_once __DIR__ . '/../views/profile/edit.php';
}

function profileEditPost() {
    global $pdo;
    
    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }
    
    $user = getCurrentUser($pdo);
    
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $universite = $_POST['universite'] ?? '';
    $filiere = $_POST['filiere'] ?? '';
    $email = $_POST['email'] ?? '';
    $tel = $_POST['tel'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $mdp = $_POST['mdp'] ?? '';
    $generatedPhotoUrl = trim($_POST['generated_photo_url'] ?? '');
    
    $photo = $user['photo'];
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $uploadDir = __DIR__ . '/../uploads/';
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
        $photo = 'uploads/' . time() . '_' . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . basename($photo));
    } elseif ($generatedPhotoUrl !== '' && preg_match('#^https?://#i', $generatedPhotoUrl)) {
        // Store generated image URL directly
        $photo = $generatedPhotoUrl;
    }
    
    require_once __DIR__ . '/../models/userModel.php';
    
    $data = [
        'nom' => $nom, 'prenom' => $prenom, 'universite' => $universite,
        'filiere' => $filiere, 'email' => $email, 'tel' => $tel,
        'bio' => $bio, 'photo' => $photo, 'mdp' => !empty($mdp) ? password_hash($mdp, PASSWORD_DEFAULT) : ''
    ];
    updateUser($pdo, $user['id'], $data);
    
    header('Location: index.php?action=profile');
    exit();
}

function profileGeneratePhoto() {
    global $pdo;

    // Ensure clean JSON output (avoid PHP notices breaking JSON).
    while (ob_get_level() > 0) {
        @ob_end_clean();
    }
    @ini_set('display_errors', '0');

    if (!isLoggedIn()) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['ok' => false, 'error' => 'not_logged_in']);
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['ok' => false, 'error' => 'method_not_allowed']);
        exit();
    }

    $prompt = trim($_POST['prompt'] ?? '');
    if ($prompt === '') {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['ok' => false, 'error' => 'empty_prompt']);
        exit();
    }

    $user = getCurrentUser($pdo);
    $seed = (int)($_POST['seed'] ?? time());
    $url = 'https://image.pollinations.ai/prompt/' . rawurlencode($prompt)
        . '?width=512&height=512&model=flux&enhance=true&nologo=true&seed=' . $seed;

    $binary = null;
    // Prefer curl when available.
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 25);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 'StudyHub/1.0');
        $binary = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if (!$binary || $httpCode < 200 || $httpCode >= 300) {
            $binary = null;
        }
    }

    if ($binary === null) {
        $ctx = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 25,
                'header' => "User-Agent: StudyHub/1.0\r\n",
            ],
        ]);
        $binary = @file_get_contents($url, false, $ctx);
        if ($binary === false || $binary === null) {
            $binary = null;
        }
    }

    if ($binary === null) {
        // Fallback: generate a local SVG avatar (works offline).
        $binary = null;
    }

    $uploadDir = __DIR__ . '/../uploads/';
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

    $safeUserId = (int)($user['id'] ?? 0);
    $ext = $binary !== null ? 'jpg' : 'svg';
    $filename = 'avatar_' . $safeUserId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $relativePath = 'uploads/' . $filename;
    $fullPath = $uploadDir . $filename;

    if ($binary === null) {
        $name = trim((string)($user['prenom'] ?? '') . ' ' . (string)($user['nom'] ?? ''));
        $letters = preg_replace('/[^A-Za-zÀ-ÿ0-9 ]/u', '', $name);
        $parts = preg_split('/\s+/', trim($letters));
        $initials = '';
        if (!empty($parts[0])) $initials .= mb_substr($parts[0], 0, 1, 'UTF-8');
        if (!empty($parts[1])) $initials .= mb_substr($parts[1], 0, 1, 'UTF-8');
        if ($initials === '') $initials = 'U';
        $initials = mb_strtoupper($initials, 'UTF-8');

        $hash = substr(sha1($prompt . '|' . $safeUserId . '|' . $seed), 0, 6);
        $bg = '#' . $hash;
        $fg = '#ffffff';

        $svg = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 512 512">'
            . '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
            . '<stop offset="0" stop-color="' . htmlspecialchars($bg, ENT_QUOTES, 'UTF-8') . '"/>'
            . '<stop offset="1" stop-color="#0ea5e9"/></linearGradient></defs>'
            . '<rect width="512" height="512" rx="256" fill="url(#g)"/>'
            . '<text x="256" y="290" text-anchor="middle" font-family="Inter, Arial, sans-serif" font-size="160" font-weight="800" fill="' . $fg . '">'
            . htmlspecialchars($initials, ENT_QUOTES, 'UTF-8')
            . '</text>'
            . '</svg>';

        $binary = $svg;
    }

    $written = @file_put_contents($fullPath, $binary);
    if (!$written) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['ok' => false, 'error' => 'write_failed']);
        exit();
    }

    // Apply immediately as profile photo
    require_once __DIR__ . '/../models/userModel.php';
    updateUserPhotoById($pdo, (int)$user['id'], $relativePath);

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['ok' => true, 'url' => $relativePath]);
    exit();
}

function profileView($id) {
    global $pdo;
    
    require_once __DIR__ . '/../models/userModel.php';
    require_once __DIR__ . '/../models/resourceModel.php';
    
    $profileUser = getUserById($pdo, $id);
    
    if (!$profileUser) {
        header('Location: index.php?action=home');
        exit();
    }
    
    $userResources = getUserResources($pdo, $id);
    
    $totalResources = count($userResources);
    $totalDownloads = 0;
    $totalRating = 0;
    
    foreach ($userResources as $res) {
        $totalDownloads += $res['downloads'] ?? 0;
        $totalRating += $res['note_moyenne'] ?? 0;
    }
    
    $avgUserRating = $totalResources > 0 ? round($totalRating / $totalResources, 1) : 0;
    $currentUser = getCurrentUser($pdo);

    require_once __DIR__ . '/../models/subscriptionModel.php';
    $profileSubscription = getActiveSubscriptionByUser($pdo, $id);
    
    require_once __DIR__ . '/../views/profile/view_other.php';
}
?>