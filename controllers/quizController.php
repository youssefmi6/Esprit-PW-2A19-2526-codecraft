<?php
require_once __DIR__ . '/sql_queries.php';

// controllers/quizController.php

function quizStart(int $resourceId): void
{
    global $pdo;

    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }

    require_once __DIR__ . '/../models/resourceModel.php';
    require_once __DIR__ . '/../models/quizModel.php';

    $resource = getResourceById($pdo, $resourceId);
    if (!$resource) {
        header('Location: index.php?action=home');
        exit();
    }

    $currentUser = getCurrentUser($pdo);
    if (!$currentUser) {
        header('Location: index.php?action=login');
        exit();
    }

    $existingCertificate = getLatestPassedAttemptForResourceUser($pdo, (int)$resource['id_res'], (int)$currentUser['id']);
    $generationError = null;
    $quizQuestions = buildQuizQuestionsForResource($resource, $generationError);

    require_once __DIR__ . '/../views/quiz/start.php';
}

function quizSubmit(int $resourceId): void
{
    global $pdo;

    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?action=quiz&subaction=start&id=' . $resourceId);
        exit();
    }

    require_once __DIR__ . '/../models/resourceModel.php';
    require_once __DIR__ . '/../models/quizModel.php';

    $resource = getResourceById($pdo, $resourceId);
    if (!$resource) {
        header('Location: index.php?action=home');
        exit();
    }

    $currentUser = getCurrentUser($pdo);
    if (!$currentUser) {
        header('Location: index.php?action=login');
        exit();
    }

    $questionsPayload = $_POST['questions_payload'] ?? '[]';
    $quizQuestions = json_decode($questionsPayload, true);
    if (!is_array($quizQuestions) || count($quizQuestions) !== 10) {
        $generationError = null;
        $quizQuestions = buildQuizQuestionsForResource($resource, $generationError);
    }
    if (!is_array($quizQuestions) || count($quizQuestions) !== 10) {
        header('Location: index.php?action=quiz&subaction=start&id=' . (int)$resource['id_res'] . '&error=no_context');
        exit();
    }

    $userAnswers = [];
    $correctCount = 0;
    foreach ($quizQuestions as $index => $q) {
        $selected = isset($_POST['q_' . $index]) ? (int)$_POST['q_' . $index] : -1;
        $correct = (int)($q['correct_index'] ?? -999);
        $isCorrect = $selected === $correct;
        if ($isCorrect) {
            $correctCount++;
        }
        $userAnswers[] = [
            'question_index' => $index,
            'selected' => $selected,
            'correct' => $correct,
            'is_correct' => $isCorrect
        ];
    }

    $score = round(($correctCount / 10) * 10, 2);
    $passed = $score >= 5.0;
    $certificateCode = $passed
        ? ('SH-' . strtoupper(substr(sha1((string)$resourceId . '|' . (string)$currentUser['id'] . '|' . (string)microtime(true)), 0, 12)))
        : null;

    $attemptId = createQuizAttempt(
        $pdo,
        (int)$resource['id_res'],
        (int)$currentUser['id'],
        $quizQuestions,
        $userAnswers,
        $score,
        $passed,
        $certificateCode
    );

    header('Location: index.php?action=quiz&subaction=result&id=' . (int)$resource['id_res'] . '&attempt=' . $attemptId);
    exit();
}

function quizResult(int $resourceId): void
{
    global $pdo;

    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }

    require_once __DIR__ . '/../models/resourceModel.php';
    require_once __DIR__ . '/../models/quizModel.php';

    $resource = getResourceById($pdo, $resourceId);
    if (!$resource) {
        header('Location: index.php?action=home');
        exit();
    }

    $currentUser = getCurrentUser($pdo);
    $attemptId = (int)($_GET['attempt'] ?? 0);
    $attempt = getQuizAttemptById($pdo, $attemptId);
    if (!$attempt || (int)$attempt['user_id'] !== (int)$currentUser['id'] || (int)$attempt['resource_id'] !== (int)$resource['id_res']) {
        header('Location: index.php?action=quiz&subaction=start&id=' . (int)$resource['id_res']);
        exit();
    }

    $quizQuestions = json_decode((string)$attempt['questions_json'], true) ?: [];
    $userAnswers = json_decode((string)$attempt['answers_json'], true) ?: [];

    require_once __DIR__ . '/../views/quiz/result.php';
}

function quizCertificate(int $resourceId): void
{
    global $pdo;

    if (!isLoggedIn()) {
        header('Location: index.php?action=login');
        exit();
    }

    require_once __DIR__ . '/../models/resourceModel.php';
    require_once __DIR__ . '/../models/quizModel.php';

    $resource = getResourceById($pdo, $resourceId);
    if (!$resource) {
        header('Location: index.php?action=home');
        exit();
    }

    $currentUser = getCurrentUser($pdo);
    $attemptId = (int)($_GET['attempt'] ?? 0);
    $attempt = getQuizAttemptById($pdo, $attemptId);
    if (!$attempt || (int)$attempt['user_id'] !== (int)$currentUser['id'] || (int)$attempt['resource_id'] !== (int)$resource['id_res']) {
        header('Location: index.php?action=quiz&subaction=start&id=' . (int)$resource['id_res']);
        exit();
    }
    if ((int)$attempt['passed'] !== 1) {
        header('Location: index.php?action=quiz&subaction=result&id=' . (int)$resource['id_res'] . '&attempt=' . (int)$attempt['id']);
        exit();
    }

    require_once __DIR__ . '/../views/quiz/certificate.php';
}

function buildQuizQuestionsForResource(array $resource, ?string &$generationError = null): array
{
    $fileText = extractTextFromUploadedResourceFile($resource);
    if ($fileText === '') {
        $generationError = 'Impossible de lire le fichier uploadé. Le quiz contextuel nécessite un contenu texte exploitable.';
        return [];
    }

    $subcontexts = buildSubcontextsFromText($fileText, 10);
    $context = buildResourceContextForQuiz($resource, $subcontexts);
    $fromAi = generateQuizWithOpenSourceAi($context, $resource, $subcontexts);
    if (is_array($fromAi) && count($fromAi) === 10) {
        return $fromAi;
    }

    $fallback = buildContextFallbackQuiz($resource, $fileText);
    if (count($fallback) !== 10) {
        $generationError = 'Le contenu du fichier est insuffisant pour produire 10 questions contextuelles.';
        return [];
    }
    return $fallback;
}

function buildResourceContextForQuiz(array $resource, array $subcontexts): string
{
    $parts = [];
    $parts[] = 'Titre de la ressource: ' . ($resource['titre'] ?? '');
    $parts[] = 'Consigne: questions basees uniquement sur le texte du fichier.';
    $parts[] = 'Sous-contextes extraits du fichier:';
    foreach ($subcontexts as $i => $chunk) {
        $parts[] = '[' . ($i + 1) . '] ' . $chunk;
    }

    return implode("\n", $parts);
}

function generateQuizWithOpenSourceAi(string $context, array $resource, array $subcontexts = []): ?array
{
    $prompt = "Tu es un générateur de quiz académique. "
        . "A partir de ce contexte, génère exactement 10 questions en français. "
        . "Chaque question doit avoir exactement 4 choix et une seule réponse correcte. "
        . "Chaque question doit etre basee sur un sous-contexte different (1 question par bloc [1]...[10]). "
        . "Interdiction d inventer: base-toi uniquement sur le contexte reçu. "
        . "Si une information n est pas dans le contexte, n'utilise pas cette information. "
        . "Les choix doivent etre différents, crédibles et non redondants. "
        . "Interdiction de copier-coller une phrase brute du contexte comme choix; reformule de façon claire. "
        . "Réponds uniquement en JSON valide sous la forme: "
        . "{\"questions\":[{\"question\":\"...\",\"choices\":[\"A\",\"B\",\"C\",\"D\"],\"correct_index\":0,\"explanation\":\"...\"}]}. "
        . "Aucune phrase hors JSON.\n\nCONTEXTE:\n" . $context;
    $raw = fetchQuizFromAiProviders($prompt);
    if (!is_string($raw) || trim($raw) === '') {
        return null;
    }

    $decoded = decodeQuizJsonFromRaw($raw);
    if (!is_array($decoded) || !isset($decoded['questions']) || !is_array($decoded['questions'])) {
        return null;
    }

    $questions = normalizeAiQuestions($decoded['questions'], $subcontexts);
    return count($questions) === 10 ? $questions : null;
}

function fetchQuizFromAiProviders(string $prompt): ?string
{
    $openRouterKey = trim((string)getenv('OPENROUTER_API_KEY'));
    if ($openRouterKey !== '') {
        $raw = callOpenRouterForQuiz($prompt, $openRouterKey);
        if (is_string($raw) && trim($raw) !== '') {
            return $raw;
        }
    }

    $hfKey = trim((string)getenv('HF_API_KEY'));
    if ($hfKey !== '') {
        $raw = callHuggingFaceForQuiz($prompt, $hfKey);
        if (is_string($raw) && trim($raw) !== '') {
            return $raw;
        }
    }

    return callPollinationsForQuiz($prompt);
}

function callPollinationsForQuiz(string $prompt): ?string
{
    $url = 'https://text.pollinations.ai/' . rawurlencode($prompt);
    return curlGetText($url);
}

function callOpenRouterForQuiz(string $prompt, string $apiKey): ?string
{
    $model = trim((string)getenv('OPENROUTER_MODEL'));
    if ($model === '') {
        $model = 'mistralai/mistral-small-3.1-24b-instruct:free';
    }

    $payload = [
        'model' => $model,
        'temperature' => 0.2,
        'messages' => [
            ['role' => 'system', 'content' => 'Tu réponds uniquement en JSON valide.'],
            ['role' => 'user', 'content' => $prompt],
        ],
    ];

    $response = curlPostJson(
        'https://openrouter.ai/api/v1/chat/completions',
        $payload,
        [
            'Authorization: Bearer ' . $apiKey,
            'HTTP-Referer: https://studyhub.local',
            'X-Title: StudyHub Quiz Generator',
        ]
    );
    if (!is_array($response) || !is_string($response['body'] ?? null)) {
        return null;
    }

    $decoded = json_decode($response['body'], true);
    $content = $decoded['choices'][0]['message']['content'] ?? null;
    return is_string($content) ? $content : null;
}

function callHuggingFaceForQuiz(string $prompt, string $apiKey): ?string
{
    $model = trim((string)getenv('HF_MODEL'));
    if ($model === '') {
        $model = 'mistralai/Mistral-7B-Instruct-v0.3';
    }

    $url = 'https://api-inference.huggingface.co/models/' . rawurlencode($model);
    $payload = [
        'inputs' => $prompt,
        'parameters' => ['max_new_tokens' => 1800, 'temperature' => 0.2, 'return_full_text' => false],
    ];
    $response = curlPostJson($url, $payload, ['Authorization: Bearer ' . $apiKey]);
    if (!is_array($response) || !is_string($response['body'] ?? null)) {
        return null;
    }

    $decoded = json_decode($response['body'], true);
    if (is_array($decoded) && isset($decoded[0]['generated_text']) && is_string($decoded[0]['generated_text'])) {
        return $decoded[0]['generated_text'];
    }
    if (is_array($decoded) && isset($decoded['generated_text']) && is_string($decoded['generated_text'])) {
        return $decoded['generated_text'];
    }

    return null;
}

function curlGetText(string $url): ?string
{
    if (!function_exists('curl_init')) {
        return null;
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 35);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, 'StudyHub-Quiz/1.0');
    $raw = curl_exec($ch);
    $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($raw === false || $http < 200 || $http >= 300) {
        return null;
    }
    return is_string($raw) ? $raw : null;
}

function curlPostJson(string $url, array $payload, array $headers = []): ?array
{
    if (!function_exists('curl_init')) {
        return null;
    }

    $ch = curl_init($url);
    $baseHeaders = ['Content-Type: application/json'];
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($baseHeaders, $headers));
    curl_setopt($ch, CURLOPT_TIMEOUT, 40);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, 'StudyHub-Quiz/1.0');
    $body = curl_exec($ch);
    $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($body === false || $http < 200 || $http >= 300) {
        return null;
    }
    return ['body' => is_string($body) ? $body : '', 'http' => $http];
}

function decodeQuizJsonFromRaw(string $raw): ?array
{
    $decoded = json_decode($raw, true);
    if (is_array($decoded) && isset($decoded['questions']) && is_array($decoded['questions'])) {
        return $decoded;
    }

    if (preg_match('/\{[\s\S]*\}/', $raw, $m)) {
        $decoded = json_decode($m[0], true);
        if (is_array($decoded) && isset($decoded['questions']) && is_array($decoded['questions'])) {
            return $decoded;
        }
    }

    return null;
}

function normalizeAiQuestions(array $rawQuestions, array $subcontexts): array
{
    $questions = [];
    foreach ($rawQuestions as $qIdx => $q) {
        $question = trim((string)($q['question'] ?? ''));
        $choices = $q['choices'] ?? [];
        $correct = (int)($q['correct_index'] ?? -1);
        $explanation = trim((string)($q['explanation'] ?? ''));

        if ($question === '' || !is_array($choices) || $correct < 0 || $correct > 3) {
            continue;
        }

        $normalized = normalizeChoicesSet($choices, $correct, $subcontexts, $qIdx);
        if (!is_array($normalized) || count($normalized['choices']) !== 4 || $normalized['correct_index'] < 0 || $normalized['correct_index'] > 3) {
            continue;
        }

        $questions[] = [
            'question' => $question,
            'choices' => $normalized['choices'],
            'correct_index' => $normalized['correct_index'],
            'explanation' => $explanation !== '' ? $explanation : 'Voir le contenu de la ressource.',
        ];

        if (count($questions) >= 10) {
            break;
        }
    }

    return $questions;
}

function normalizeChoicesSet(array $choices, int $correctIndex, array $subcontexts, int $qIdx): ?array
{
    $cleanChoices = [];
    foreach ($choices as $c) {
        $txt = normalizeChoiceText((string)$c);
        if ($txt !== '') {
            $cleanChoices[] = $txt;
        }
    }
    if (!isset($cleanChoices[$correctIndex])) {
        return null;
    }

    $correctChoice = $cleanChoices[$correctIndex];
    $unique = [];
    foreach ($cleanChoices as $choice) {
        $key = mb_strtolower(trim($choice), 'UTF-8');
        if (!isset($unique[$key])) {
            $unique[$key] = $choice;
        }
    }

    $ordered = array_values($unique);
    if (count($ordered) < 4) {
        foreach (buildAdditionalDistractors($subcontexts, $qIdx) as $extra) {
            $key = mb_strtolower(trim($extra), 'UTF-8');
            if (!isset($unique[$key])) {
                $unique[$key] = $extra;
                $ordered[] = $extra;
            }
            if (count($ordered) >= 4) {
                break;
            }
        }
    }
    if (count($ordered) < 4) {
        return null;
    }

    $ordered = array_slice($ordered, 0, 4);
    $newCorrect = array_search($correctChoice, $ordered, true);
    if (!is_int($newCorrect)) {
        $ordered[0] = $correctChoice;
        $newCorrect = 0;
    }

    return ['choices' => $ordered, 'correct_index' => $newCorrect];
}

function normalizeChoiceText(string $choice): string
{
    $choice = preg_replace('/\s+/u', ' ', trim($choice)) ?? trim($choice);
    $choice = preg_replace('/^[A-D]\s*[\)\.\-:]\s*/u', '', $choice) ?? $choice;
    $choice = preg_replace('/^[\-–•]+\s*/u', '', $choice) ?? $choice;
    return trim($choice);
}

function buildAdditionalDistractors(array $subcontexts, int $qIdx): array
{
    $pool = [];
    foreach ($subcontexts as $idx => $chunk) {
        if ($idx === $qIdx) {
            continue;
        }
        $candidate = mb_substr(trim((string)$chunk), 0, 140, 'UTF-8');
        if ($candidate !== '') {
            $pool[] = normalizeChoiceText($candidate);
        }
    }
    shuffle($pool);

    $genericPool = [
        'Cette affirmation n est pas présente dans ce sous-contexte.',
        'Le passage évoque un point différent de cette proposition.',
        'Le sous-contexte ne confirme pas cette réponse.',
    ];
    foreach ($genericPool as $g) {
        $pool[] = $g;
    }

    return array_values(array_filter($pool, static fn($v) => trim((string)$v) !== ''));
}

function buildContextFallbackQuiz(array $resource, string $fileText): array
{
    $chunks = buildSubcontextsFromText($fileText, 10);
    if (count($chunks) < 10) {
        return [];
    }
    $questions = [];
    $title = (string)($resource['titre'] ?? 'Ressource');
    for ($i = 0; $i < 10; $i++) {
        $snippet = mb_substr($chunks[$i], 0, 190, 'UTF-8');
        $distractors = buildFallbackDistractors($chunks, $i, $snippet);
        $choices = array_merge([$snippet], $distractors);
        $correctIndex = shuffleChoicesAndGetCorrectIndex($choices, 0);

        $questions[] = [
            'question' => "Selon le sous-contexte " . ($i + 1) . " du cours \"{$title}\", quelle proposition est correcte ?",
            'choices' => $choices,
            'correct_index' => $correctIndex,
            'explanation' => "La réponse correcte reprend une phrase du contexte extrait du fichier: {$snippet}"
        ];
    }

    return $questions;
}

function extractTextFromUploadedResourceFile(array $resource): string
{
    $relative = trim((string)($resource['fichier'] ?? ''));
    if ($relative === '') {
        return '';
    }

    $path = __DIR__ . '/../' . ltrim($relative, '/');
    if (!file_exists($path) || !is_file($path)) {
        return '';
    }

    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if ($ext === 'txt') {
        $txt = @file_get_contents($path);
        return is_string($txt) ? sanitizeExtractedText($txt) : '';
    }

    if ($ext === 'docx') {
        return extractTextFromDocx($path);
    }

    if ($ext === 'pdf') {
        return extractTextFromPdf($path);
    }

    if ($ext === 'doc') {
        $bin = @file_get_contents($path);
        if (!is_string($bin)) {
            return '';
        }
        return sanitizeExtractedText($bin);
    }

    return '';
}

function extractTextFromDocx(string $path): string
{
    if (!class_exists('ZipArchive')) {
        return '';
    }
    $zip = new ZipArchive();
    if ($zip->open($path) !== true) {
        return '';
    }
    $xml = $zip->getFromName('word/document.xml');
    $zip->close();
    if (!is_string($xml) || $xml === '') {
        return '';
    }

    $text = strip_tags(str_replace(['</w:p>', '</w:tr>'], ["\n", "\n"], $xml));
    return sanitizeExtractedText($text);
}

function extractTextFromPdf(string $path): string
{
    $content = @file_get_contents($path);
    if (!is_string($content) || $content === '') {
        return '';
    }

    // 1) Tentative extraction depuis flux texte "simples"
    $chunks = extractTextChunksFromPdfContent($content);

    // 2) Tentative extraction depuis streams FlateDecode compressés
    if (empty($chunks) && preg_match_all('/stream[\r\n]+(.*?)endstream/s', $content, $streams)) {
        foreach (($streams[1] ?? []) as $rawStream) {
            $decoded = tryDecodePdfStream((string)$rawStream);
            if ($decoded === '') {
                continue;
            }
            $decodedChunks = extractTextChunksFromPdfContent($decoded);
            if (!empty($decodedChunks)) {
                $chunks = array_merge($chunks, $decodedChunks);
            }
        }
    }

    // 3) Fallback CLI si disponible (pdftotext)
    if (empty($chunks)) {
        $cliText = tryExtractPdfTextWithCli($path);
        if ($cliText !== '') {
            return sanitizeExtractedText($cliText);
        }
    }

    if (empty($chunks)) {
        return '';
    }

    $text = implode(' ', array_map(static function ($c) {
        $c = str_replace(['\\n', '\\r', '\\t', '\\(', '\\)', '\\\\'], ["\n", ' ', ' ', '(', ')', '\\'], (string)$c);
        return $c;
    }, $chunks));

    return sanitizeExtractedText($text);
}

function sanitizeExtractedText(string $text): string
{
    $text = str_replace("\0", ' ', $text);
    $text = preg_replace('/[^\P{C}\n\t]/u', ' ', $text) ?? $text;
    $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
    return trim($text);
}

function buildSubcontextsFromText(string $text, int $targetCount = 10): array
{
    $clean = sanitizeExtractedText($text);
    if ($clean === '') {
        return [];
    }

    $sentences = preg_split('/(?<=[\.\!\?])\s+/u', $clean, -1, PREG_SPLIT_NO_EMPTY);
    $sentences = array_values(array_filter(array_map(static function ($s) {
        $s = trim((string)$s);
        return mb_strlen($s, 'UTF-8') >= 20 ? $s : '';
    }, is_array($sentences) ? $sentences : [])));

    if (empty($sentences)) {
        $words = preg_split('/\s+/u', $clean, -1, PREG_SPLIT_NO_EMPTY);
        if (!is_array($words) || empty($words)) {
            return [];
        }
        $wordChunks = [];
        $chunkSize = max(25, (int)floor(count($words) / max(1, $targetCount)));
        for ($i = 0; $i < count($words); $i += $chunkSize) {
            $segment = trim(implode(' ', array_slice($words, $i, $chunkSize)));
            if ($segment !== '') {
                $wordChunks[] = mb_substr($segment, 0, 420, 'UTF-8');
            }
        }
        $sentences = $wordChunks;
    }

    $chunks = [];
    $perChunk = max(1, (int)ceil(count($sentences) / $targetCount));
    for ($i = 0; $i < count($sentences); $i += $perChunk) {
        $segment = implode(' ', array_slice($sentences, $i, $perChunk));
        $segment = trim($segment);
        if ($segment !== '') {
            $chunks[] = mb_substr($segment, 0, 420, 'UTF-8');
        }
    }

    // Normaliser a exactement $targetCount avec repartition circulaire si besoin
    if (count($chunks) > $targetCount) {
        $chunks = array_slice($chunks, 0, $targetCount);
    }
    while (!empty($chunks) && count($chunks) < $targetCount) {
        $chunks[] = $chunks[count($chunks) % max(1, count($chunks))];
    }

    return $chunks;
}

function extractTextChunksFromPdfContent(string $content): array
{
    $matches = [];
    preg_match_all('/\(([^()]*)\)\s*Tj/s', $content, $matches);
    $chunks = $matches[1] ?? [];
    if (empty($chunks)) {
        preg_match_all('/\[(.*?)\]\s*TJ/s', $content, $arrMatches);
        if (!empty($arrMatches[1])) {
            foreach ($arrMatches[1] as $arrChunk) {
                if (preg_match_all('/\(([^()]*)\)/s', $arrChunk, $inner)) {
                    $chunks = array_merge($chunks, $inner[1]);
                }
            }
        }
    }
    if (empty($chunks)) {
        return [];
    }

    return array_values(array_filter(array_map('decodePdfTextChunk', $chunks), static function ($chunk) {
        return trim((string)$chunk) !== '';
    }));
}

function tryDecodePdfStream(string $rawStream): string
{
    $stream = trim($rawStream);
    if ($stream === '') {
        return '';
    }
    $candidates = [
        @gzuncompress($stream),
        @gzinflate($stream),
        @gzdecode($stream),
    ];
    foreach ($candidates as $decoded) {
        if (is_string($decoded) && $decoded !== '') {
            return $decoded;
        }
    }
    return '';
}

function tryExtractPdfTextWithCli(string $path): string
{
    if (!function_exists('shell_exec')) {
        return '';
    }

    $escaped = escapeshellarg($path);
    $cmds = [
        "pdftotext {$escaped} - 2>NUL",
        "pdftotext {$escaped} - 2>/dev/null",
    ];
    foreach ($cmds as $cmd) {
        $out = @shell_exec($cmd);
        if (is_string($out) && trim($out) !== '') {
            return $out;
        }
    }
    return '';
}

function buildFallbackDistractors(array $chunks, int $currentIndex, string $snippet): array
{
    $pool = [];
    foreach ($chunks as $idx => $chunk) {
        if ($idx === $currentIndex) {
            continue;
        }
        $candidate = trim((string)$chunk);
        if ($candidate === '') {
            continue;
        }
        $candidate = mb_substr($candidate, 0, 190, 'UTF-8');
        if ($candidate !== $snippet) {
            $pool[] = $candidate;
        }
    }

    if (!empty($pool)) {
        shuffle($pool);
    }

    $selected = array_slice(array_values(array_unique($pool)), 0, 3);
    $genericPool = [
        'Aucune de ces propositions n est explicitement citée dans ce sous-contexte.',
        'Cette idée est évoquée dans une autre section, pas dans ce passage.',
        'Le sous-contexte présente une information différente de cette proposition.',
    ];
    foreach ($genericPool as $generic) {
        if (count($selected) >= 3) {
            break;
        }
        if (!in_array($generic, $selected, true)) {
            $selected[] = $generic;
        }
    }

    while (count($selected) < 3) {
        $selected[] = 'Ce sous-contexte ne contient pas cette formulation.';
    }

    return $selected;
}

function shuffleChoicesAndGetCorrectIndex(array &$choices, int $correctIndex): int
{
    $indexed = [];
    foreach ($choices as $idx => $choice) {
        $indexed[] = ['text' => (string)$choice, 'is_correct' => $idx === $correctIndex];
    }
    shuffle($indexed);

    $newChoices = [];
    $newCorrect = 0;
    foreach ($indexed as $idx => $row) {
        $newChoices[] = $row['text'];
        if ($row['is_correct']) {
            $newCorrect = $idx;
        }
    }

    $choices = $newChoices;
    return $newCorrect;
}

function decodePdfTextChunk(string $chunk): string
{
    $chunk = preg_replace_callback('/\\\\([0-7]{1,3})/', static function ($m) {
        return chr(octdec($m[1]));
    }, $chunk) ?? $chunk;

    // Préserver les séquences d'échappement PDF usuelles avant normalisation.
    $chunk = str_replace(['\\(', '\\)', '\\\\', '\\n', '\\r', '\\t'], ['(', ')', '\\', ' ', ' ', ' '], $chunk);

    if (function_exists('mb_convert_encoding')) {
        $tryUtf8 = @mb_convert_encoding($chunk, 'UTF-8', 'UTF-8,ISO-8859-1,Windows-1252');
        if (is_string($tryUtf8) && $tryUtf8 !== '') {
            $chunk = $tryUtf8;
        }
    }

    return trim(sanitizeExtractedText($chunk));
}

?>
