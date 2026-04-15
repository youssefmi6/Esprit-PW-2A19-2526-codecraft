<?php
// models/subscriptionModel.php

function getActiveSubscriptionByUser($pdo, $userId) {
    $stmt = $pdo->prepare(
        "SELECT * FROM abonemment
         WHERE id_user = ? AND date_fin >= CURDATE()
         ORDER BY date_fin DESC, id DESC
         LIMIT 1"
    );
    $stmt->execute([$userId]);
    return $stmt->fetch();
}

function getNextPrimaryId($pdo, $table, $column = 'id') {
    $stmt = $pdo->query("SELECT COALESCE(MAX($column), 0) + 1 AS next_id FROM $table");
    $row = $stmt->fetch();
    return intval($row['next_id'] ?? 1);
}

/**
 * Valide les champs de paiement (demo). Ne stocke jamais le numero complet.
 * @return array{ok:bool, error?:string, card_holder?:string, payment_last4?:string}
 */
function validateSubscriptionPayment(array $post) {
    $holder = trim($post['card_holder'] ?? '');
    $num = preg_replace('/\D+/', '', $post['card_number'] ?? '');
    $exp = trim($post['card_expiry'] ?? '');
    $cvv = preg_replace('/\D+/', '', $post['card_cvv'] ?? '');

    if (mb_strlen($holder) < 3) {
        return ['ok' => false, 'error' => 'Indiquez le nom complet du titulaire de la carte.'];
    }
    if (strlen($num) < 13 || strlen($num) > 19) {
        return ['ok' => false, 'error' => 'Numero de carte invalide.'];
    }
    if (!preg_match('/^(0[1-9]|1[0-2])\/(\d{2})$/', $exp, $m)) {
        return ['ok' => false, 'error' => 'Date d expiration invalide (format MM/AA).'];
    }
    $cardM = (int) $m[1];
    $cardY = 2000 + (int) $m[2];
    $nowY = (int) date('Y');
    $nowM = (int) date('n');
    if ($cardY < $nowY || ($cardY === $nowY && $cardM < $nowM)) {
        return ['ok' => false, 'error' => 'La carte est expiree.'];
    }
    if (strlen($cvv) < 3 || strlen($cvv) > 4) {
        return ['ok' => false, 'error' => 'Code CVV invalide.'];
    }

    return [
        'ok' => true,
        'card_holder' => $holder,
        'payment_last4' => substr($num, -4)
    ];
}

/**
 * Souscription a un type du catalogue (plan publie).
 */
function upsertUserSubscriptionByCatalogPlan($pdo, $userId, $planId, $durationDays = 30, $paymentMeta = null) {
    if (!subscriptionPlansTablesExist($pdo)) {
        return false;
    }
    $plan = getPublishedPlanById($pdo, $planId);
    if (!$plan) {
        return false;
    }

    $holder = $paymentMeta['card_holder'] ?? null;
    $last4 = $paymentMeta['payment_last4'] ?? null;
    $startDate = date('Y-m-d');
    $endDate = date('Y-m-d', strtotime("+$durationDays days"));
    $nom = mb_substr($plan['name'], 0, 100);
    $desc = mb_substr($plan['description'], 0, 500);
    $prix = (int) $plan['prix'];

    $existing = getActiveSubscriptionByUser($pdo, $userId);
    if ($existing) {
        $stmt = $pdo->prepare(
            "UPDATE abonemment SET nom = ?, descreption = ?, prix = ?, date_debut = ?, date_fin = ?,
             card_holder = ?, payment_last4 = ?, plan_id = ? WHERE id = ?"
        );
        return $stmt->execute([$nom, $desc, $prix, $startDate, $endDate, $holder, $last4, $planId, $existing['id']]);
    }

    $newId = getNextPrimaryId($pdo, 'abonemment', 'id');
    $stmt = $pdo->prepare(
        "INSERT INTO abonemment (id, id_user, plan_id, nom, descreption, prix, date_debut, date_fin, card_holder, payment_last4)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    return $stmt->execute([$newId, $userId, $planId, $nom, $desc, $prix, $startDate, $endDate, $holder, $last4]);
}

function getAccessiblePlaylistsForUser($pdo, $userId) {
    $active = getActiveSubscriptionByUser($pdo, $userId);
    if ($active && !empty($active['plan_id']) && subscriptionPlansTablesExist($pdo)) {
        return getCatalogPlanResourcesAsPlaylistRows($pdo, (int) $active['plan_id']);
    }

    return [];
}

function subscriptionPlansTablesExist($pdo) {
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    try {
        $pdo->query('SELECT 1 FROM subscription_plans LIMIT 1');
        $cache = true;
    } catch (Throwable $e) {
        $cache = false;
    }
    return $cache;
}

function getSubscriptionPlanById($pdo, $id) {
    if (!subscriptionPlansTablesExist($pdo)) {
        return null;
    }
    $stmt = $pdo->prepare('SELECT * FROM subscription_plans WHERE id = ?');
    $stmt->execute([(int) $id]);
    $r = $stmt->fetch();
    return $r ?: null;
}

function getPublishedPlanById($pdo, $id) {
    if (!subscriptionPlansTablesExist($pdo)) {
        return null;
    }
    $stmt = $pdo->prepare('SELECT * FROM subscription_plans WHERE id = ? AND published = 1');
    $stmt->execute([(int) $id]);
    $r = $stmt->fetch();
    return $r ?: null;
}

function getAllSubscriptionPlansCatalog($pdo) {
    if (!subscriptionPlansTablesExist($pdo)) {
        return [];
    }
    $stmt = $pdo->query(
        "SELECT p.*, (SELECT COUNT(*) FROM subscription_plan_resources r WHERE r.plan_id = p.id) AS resource_count
         FROM subscription_plans p
         ORDER BY p.sort_order ASC, p.id DESC"
    );
    return $stmt->fetchAll();
}

function getPublishedCatalogPlans($pdo) {
    if (!subscriptionPlansTablesExist($pdo)) {
        return [];
    }
    $stmt = $pdo->query('SELECT * FROM subscription_plans WHERE published = 1 ORDER BY sort_order ASC, id ASC');
    return $stmt->fetchAll();
}

function getResourceIdsForPlan($pdo, $planId) {
    if (!subscriptionPlansTablesExist($pdo)) {
        return [];
    }
    $stmt = $pdo->prepare(
        'SELECT id_ressource FROM subscription_plan_resources WHERE plan_id = ? ORDER BY sort_order ASC, id ASC'
    );
    $stmt->execute([(int) $planId]);
    return array_map('intval', array_column($stmt->fetchAll(), 'id_ressource'));
}

function saveSubscriptionPlanResources($pdo, $planId, array $resourceIds) {
    if (!subscriptionPlansTablesExist($pdo)) {
        return false;
    }
    $pdo->prepare('DELETE FROM subscription_plan_resources WHERE plan_id = ?')->execute([(int) $planId]);
    $order = 0;
    foreach ($resourceIds as $rid) {
        $rid = (int) $rid;
        if ($rid < 1) {
            continue;
        }
        $pdo->prepare(
            'INSERT INTO subscription_plan_resources (plan_id, id_ressource, sort_order) VALUES (?,?,?)'
        )->execute([(int) $planId, $rid, $order++]);
    }
    return true;
}

function createSubscriptionPlan($pdo, $name, $description, $prix, $published = 0) {
    $stmt = $pdo->prepare(
        'INSERT INTO subscription_plans (name, description, prix, published, sort_order) VALUES (?,?,?,?,0)'
    );
    $stmt->execute([
        mb_substr(trim($name), 0, 100),
        mb_substr(trim($description), 0, 500),
        (int) $prix,
        $published ? 1 : 0
    ]);
    return (int) $pdo->lastInsertId();
}

function updateSubscriptionPlan($pdo, $id, $name, $description, $prix, $published) {
    $stmt = $pdo->prepare(
        'UPDATE subscription_plans SET name = ?, description = ?, prix = ?, published = ? WHERE id = ?'
    );
    return $stmt->execute([
        mb_substr(trim($name), 0, 100),
        mb_substr(trim($description), 0, 500),
        (int) $prix,
        $published ? 1 : 0,
        (int) $id
    ]);
}

function deleteSubscriptionPlan($pdo, $id) {
    if (!subscriptionPlansTablesExist($pdo)) {
        return false;
    }
    $stmt = $pdo->prepare('DELETE FROM subscription_plans WHERE id = ?');
    return $stmt->execute([(int) $id]);
}

function getCatalogPlanResourcesAsPlaylistRows($pdo, $planId) {
    $plan = getSubscriptionPlanById($pdo, $planId);
    if (!$plan) {
        return [];
    }
    $stmt = $pdo->prepare(
        "SELECT r.id_res, r.titre, r.type, r.niveau, r.matiere, r.photo
         FROM subscription_plan_resources pr
         INNER JOIN ressource r ON r.id_res = pr.id_ressource
         WHERE pr.plan_id = ?
         ORDER BY pr.sort_order ASC, pr.id ASC"
    );
    $stmt->execute([(int) $planId]);
    $rows = $stmt->fetchAll();
    $out = [];
    foreach ($rows as $r) {
        $out[] = [
            'id' => 0,
            'playlist_nom' => $plan['name'],
            'playlist_description' => mb_substr($plan['description'], 0, 50),
            'id_abonement' => 0,
            'id_res' => $r['id_res'],
            'titre' => $r['titre'],
            'type' => $r['type'],
            'niveau' => $r['niveau'],
            'matiere' => $r['matiere'],
            'photo' => $r['photo']
        ];
    }
    return $out;
}

function getAllAbonnementsForAdmin($pdo, $search = '') {
    $sql = "SELECT a.*, u.nom AS user_nom, u.prenom AS user_prenom, u.email AS user_email
            FROM abonemment a
            INNER JOIN users u ON u.id = a.id_user
            WHERE 1=1";
    $params = [];
    if ($search !== '') {
        $sql .= " AND (u.nom LIKE ? OR u.prenom LIKE ? OR u.email LIKE ? OR a.nom LIKE ? OR CAST(a.id AS CHAR) = ?)";
        $q = '%' . $search . '%';
        $params = [$q, $q, $q, $q, $search];
    }
    $sql .= ' ORDER BY a.id DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Derniers abonnements membres (aperçu dashboard).
 */
function getRecentAbonnementsForAdmin($pdo, int $limit = 10): array {
    $limit = max(1, min(50, $limit));
    $stmt = $pdo->query(
        "SELECT a.id, a.nom, a.prix, a.date_debut, a.date_fin, u.prenom, u.nom AS user_nom, u.email AS user_email
         FROM abonemment a
         INNER JOIN users u ON u.id = a.id_user
         ORDER BY a.id DESC
         LIMIT " . (int) $limit
    );
    return $stmt->fetchAll();
}

function getAbonnementByIdForAdmin($pdo, $id) {
    $stmt = $pdo->prepare(
        "SELECT a.*, u.nom AS user_nom, u.prenom AS user_prenom, u.email AS user_email
         FROM abonemment a
         INNER JOIN users u ON u.id = a.id_user
         WHERE a.id = ?"
    );
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Plan personnalisé (sans type catalogue) : nom, prix, description depuis le formulaire admin.
 * @return array{nom:string, prix:int, descreption:string}|null
 */
function adminBuildSubscriptionPlanFields($post) {
    $nom = mb_substr(trim($post['custom_nom'] ?? ''), 0, 100);
    if ($nom === '') {
        return null;
    }
    $prix = (int) ($post['custom_prix'] ?? 0);
    $descreption = mb_substr(trim($post['custom_desc'] ?? ''), 0, 500);
    if ($descreption === '') {
        $descreption = mb_substr('Abonnement ' . $nom, 0, 500);
    }
    return ['nom' => $nom, 'prix' => $prix, 'descreption' => $descreption];
}

function createAbonnementAdmin($pdo, array $data) {
    $planId = isset($data['plan_id']) && $data['plan_id'] !== '' && $data['plan_id'] !== null
        ? (int) $data['plan_id'] : null;
    $stmt = $pdo->prepare(
        "INSERT INTO abonemment (id_user, plan_id, nom, descreption, prix, date_debut, date_fin, card_holder, payment_last4)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    return $stmt->execute([
        $data['id_user'],
        $planId,
        $data['nom'],
        $data['descreption'],
        $data['prix'],
        $data['date_debut'],
        $data['date_fin'],
        $data['card_holder'] ?? null,
        $data['payment_last4'] ?? null
    ]);
}

function updateAbonnementAdmin($pdo, $id, array $data) {
    $planId = isset($data['plan_id']) && $data['plan_id'] !== '' && $data['plan_id'] !== null
        ? (int) $data['plan_id'] : null;
    $stmt = $pdo->prepare(
        "UPDATE abonemment SET id_user = ?, plan_id = ?, nom = ?, descreption = ?, prix = ?, date_debut = ?, date_fin = ?,
 card_holder = ?, payment_last4 = ? WHERE id = ?"
    );
    return $stmt->execute([
        $data['id_user'],
        $planId,
        $data['nom'],
        $data['descreption'],
        $data['prix'],
        $data['date_debut'],
        $data['date_fin'],
        $data['card_holder'] ?? null,
        $data['payment_last4'] ?? null,
        $id
    ]);
}

function deleteAbonnementAdmin($pdo, $id) {
    if ((int) $id < 1) {
        return false;
    }
    try {
        $stmt = $pdo->prepare('DELETE FROM abonemment WHERE id = ?');
        $stmt->execute([(int) $id]);
        return $stmt->rowCount() > 0;
    } catch (Throwable $e) {
        return false;
    }
}

function getSubscriptionDashboardStats($pdo) {
    $byTier = [];
    $stmt = $pdo->query(
        "SELECT nom, COUNT(DISTINCT id_user) AS cnt
         FROM abonemment
         WHERE date_fin >= CURDATE()
         GROUP BY nom
         ORDER BY nom"
    );
    while ($row = $stmt->fetch()) {
        $byTier[$row['nom']] = (int) $row['cnt'];
    }

    $stmt = $pdo->query(
        "SELECT COUNT(DISTINCT id_user) FROM abonemment WHERE date_fin >= CURDATE()"
    );
    $total = (int) $stmt->fetchColumn();

    return [
        'total_active_subscribers' => $total,
        'by_tier' => $byTier,
        'other_tier' => 0
    ];
}

?>
