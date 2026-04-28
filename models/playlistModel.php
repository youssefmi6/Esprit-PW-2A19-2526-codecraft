<?php
// models/playlistModel.php

function playlistTableExists($pdo) {
    try {
        $pdo->query('SELECT 1 FROM playlist LIMIT 1');
        return true;
    } catch (Throwable $e) {
        return false;
    }
}

function ensurePlaylistPhotoColumn($pdo) {
    if (!playlistTableExists($pdo)) {
        return false;
    }

    try {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'playlist' AND COLUMN_NAME = 'photo'"
        );
        $stmt->execute();
        $exists = (int) $stmt->fetchColumn() > 0;
        if (!$exists) {
            $pdo->exec("ALTER TABLE playlist ADD COLUMN photo VARCHAR(500) DEFAULT '' AFTER description");
        }
        return true;
    } catch (Throwable $e) {
        return false;
    }
}

function getNextPlaylistGroupId($pdo) {
    $stmt = $pdo->query('SELECT COALESCE(MAX(id_abonement), 0) + 1 FROM playlist');
    return (int) $stmt->fetchColumn();
}

function createPlaylistWithResources($pdo, $nom, $description, $photo, array $resourceIds) {
    if (!ensurePlaylistPhotoColumn($pdo)) {
        return false;
    }
    $resourceIds = array_values(array_unique(array_filter(array_map('intval', $resourceIds), function ($v) {
        return $v > 0;
    })));
    if (empty($resourceIds)) {
        return false;
    }

    $nom = mb_substr(trim($nom), 0, 20);
    $description = mb_substr(trim($description), 0, 50);
    $photo = mb_substr(trim((string) $photo), 0, 500);
    if ($nom === '' || $description === '') {
        return false;
    }

    $groupId = getNextPlaylistGroupId($pdo);
    $stmt = $pdo->prepare(
        'INSERT INTO playlist (nom, description, photo, id_ressource, id_abonement) VALUES (?, ?, ?, ?, ?)'
    );

    try {
        $pdo->beginTransaction();
        foreach ($resourceIds as $rid) {
            $stmt->execute([$nom, $description, $photo, $rid, $groupId]);
        }
        $pdo->commit();
        return true;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return false;
    }
}

function getAdminPlaylists($pdo, $search = '') {
    if (!ensurePlaylistPhotoColumn($pdo)) {
        return [];
    }
    $sql = "SELECT
            id_abonement,
            MIN(nom) AS nom,
            MIN(description) AS description,
            MIN(photo) AS photo,
            COUNT(*) AS resource_count
         FROM playlist
         GROUP BY id_abonement";
    $params = [];
    $search = trim((string) $search);
    if ($search !== '') {
        if (ctype_digit($search)) {
            $sql .= " HAVING id_abonement = ? OR MIN(nom) LIKE ?";
            $params = [(int) $search, '%' . $search . '%'];
        } else {
            $sql .= " HAVING MIN(nom) LIKE ?";
            $params = ['%' . $search . '%'];
        }
    }
    $sql .= " ORDER BY id_abonement DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getPlaylistDashboardStats($pdo) {
    if (!ensurePlaylistPhotoColumn($pdo)) {
        return [
            'total_playlists' => 0,
            'total_resources_linked' => 0,
            'avg_resources_per_playlist' => 0
        ];
    }

    $stmt = $pdo->query(
        "SELECT COUNT(DISTINCT id_abonement) AS total_playlists, COUNT(*) AS total_resources_linked
         FROM playlist"
    );
    $row = $stmt->fetch();
    $totalPlaylists = (int) ($row['total_playlists'] ?? 0);
    $totalResourcesLinked = (int) ($row['total_resources_linked'] ?? 0);

    return [
        'total_playlists' => $totalPlaylists,
        'total_resources_linked' => $totalResourcesLinked,
        'avg_resources_per_playlist' => $totalPlaylists > 0
            ? round($totalResourcesLinked / $totalPlaylists, 1)
            : 0
    ];
}

function getAdminPlaylistByGroup($pdo, $groupId) {
    if (!ensurePlaylistPhotoColumn($pdo)) {
        return null;
    }
    $stmt = $pdo->prepare(
        "SELECT id_abonement, MIN(nom) AS nom, MIN(description) AS description, MIN(photo) AS photo
         FROM playlist
         WHERE id_abonement = ?
         GROUP BY id_abonement"
    );
    $stmt->execute([(int) $groupId]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function getResourceIdsForPlaylistGroup($pdo, $groupId) {
    if (!playlistTableExists($pdo)) {
        return [];
    }
    $stmt = $pdo->prepare('SELECT id_ressource FROM playlist WHERE id_abonement = ? ORDER BY id ASC');
    $stmt->execute([(int) $groupId]);
    return array_map('intval', array_column($stmt->fetchAll(), 'id_ressource'));
}

function getResourceIdsForPlaylistGroups($pdo, array $groupIds) {
    if (!playlistTableExists($pdo)) {
        return [];
    }
    $all = [];
    foreach ($groupIds as $gid) {
        $gid = (int) $gid;
        if ($gid < 1) {
            continue;
        }
        $all = array_merge($all, getResourceIdsForPlaylistGroup($pdo, $gid));
    }
    return array_values(array_unique(array_filter(array_map('intval', $all))));
}

function updatePlaylistWithResources($pdo, $groupId, $nom, $description, $photo, array $resourceIds) {
    if (!ensurePlaylistPhotoColumn($pdo)) {
        return false;
    }
    $groupId = (int) $groupId;
    if ($groupId < 1) {
        return false;
    }
    $resourceIds = array_values(array_unique(array_filter(array_map('intval', $resourceIds), function ($v) {
        return $v > 0;
    })));
    if (empty($resourceIds)) {
        return false;
    }

    $nom = mb_substr(trim($nom), 0, 20);
    $description = mb_substr(trim($description), 0, 50);
    $photo = mb_substr(trim((string) $photo), 0, 500);
    if ($nom === '' || $description === '') {
        return false;
    }

    $insert = $pdo->prepare(
        'INSERT INTO playlist (nom, description, photo, id_ressource, id_abonement) VALUES (?, ?, ?, ?, ?)'
    );

    try {
        $pdo->beginTransaction();
        $pdo->prepare('DELETE FROM playlist WHERE id_abonement = ?')->execute([$groupId]);
        foreach ($resourceIds as $rid) {
            $insert->execute([$nom, $description, $photo, $rid, $groupId]);
        }
        $pdo->commit();
        return true;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return false;
    }
}

function getPlaylistGroupsForSelection($pdo) {
    if (!ensurePlaylistPhotoColumn($pdo)) {
        return [];
    }
    $stmt = $pdo->query(
        "SELECT id_abonement, MIN(nom) AS nom, MIN(description) AS description, COUNT(*) AS resource_count
         FROM playlist
         GROUP BY id_abonement
         ORDER BY id_abonement DESC"
    );
    return $stmt->fetchAll();
}

function deletePlaylistByGroup($pdo, $groupId) {
    if (!playlistTableExists($pdo)) {
        return false;
    }
    $stmt = $pdo->prepare('DELETE FROM playlist WHERE id_abonement = ?');
    return $stmt->execute([(int) $groupId]);
}

function getPlaylistRowsByGroup($pdo, $groupId) {
    if (!ensurePlaylistPhotoColumn($pdo)) {
        return [];
    }
    $stmt = $pdo->prepare(
        "SELECT p.*, r.titre, r.matiere, r.type, r.niveau
         FROM playlist p
         INNER JOIN ressource r ON r.id_res = p.id_ressource
         WHERE p.id_abonement = ?
         ORDER BY p.id ASC"
    );
    $stmt->execute([(int) $groupId]);
    return $stmt->fetchAll();
}

function getManualPlaylistsForSubscriptionArea($pdo) {
    if (!ensurePlaylistPhotoColumn($pdo)) {
        return [];
    }
    $stmt = $pdo->query(
        "SELECT p.id AS playlist_row_id, p.id_abonement, p.nom, p.description, p.photo, r.id_res, r.titre, r.type, r.niveau, r.matiere
         FROM playlist p
         INNER JOIN ressource r ON r.id_res = p.id_ressource
         ORDER BY p.id_abonement DESC, p.id ASC"
    );
    $rows = $stmt->fetchAll();

    $out = [];
    foreach ($rows as $row) {
        $out[] = [
            'id' => (int) ($row['playlist_row_id'] ?? 0),
            'playlist_nom' => $row['nom'],
            'playlist_description' => $row['description'],
            'playlist_photo' => $row['photo'] ?? '',
            'id_abonement' => 0,
            'id_res' => (int) $row['id_res'],
            'titre' => $row['titre'],
            'type' => $row['type'],
            'niveau' => $row['niveau'],
            'matiere' => $row['matiere']
        ];
    }
    return $out;
}
?>
