<?php
/**
 * Photographies par défaut par matière (Unsplash — scènes réelles, pas des pictogrammes).
 * https://unsplash.com/license
 */
function get_matiere_default_photos_map(): array {
    $q = 'auto=format&fit=crop&w=1200&q=80';
    return [
        'Mathématiques' => "https://images.unsplash.com/photo-1509228468518-180dd4864904?$q",
        'Physique' => "https://images.unsplash.com/photo-1532094340224-2e0c147c6c8c?$q",
        'Chimie' => "https://images.unsplash.com/photo-1532187861976-d7169f429c5a?$q",
        'Informatique' => "https://images.unsplash.com/photo-1498050108023-c5249f4df085?$q",
        'Programmation' => "https://images.unsplash.com/photo-1461740565680-d09c1d69d0c6?$q",
        'HTML/CSS' => "https://images.unsplash.com/photo-1547658719-da2b51169166?$q",
        'JavaScript' => "https://images.unsplash.com/photo-1555066931-4365d14bab8c?$q",
        'Python' => "https://images.unsplash.com/photo-1526379095098-d400fd0bf935?$q",
        'Java' => "https://images.unsplash.com/photo-1517694712202-14dd9538aa97?$q",
        'Base de données' => "https://images.unsplash.com/photo-1558494949-ef010cbdcc31?$q",
        'Réseaux' => "https://images.unsplash.com/photo-1544197158-b7d2d7aefca6?$q",
        'Sciences de l\'ingénieur' => "https://images.unsplash.com/photo-1581092160562-40aa08e78837?$q",
        'Économie' => "https://images.unsplash.com/photo-1611974789855-9c698a0b2ad4?$q",
        'Gestion' => "https://images.unsplash.com/photo-1552664730-d307ca884978?$q",
        'Droit' => "https://images.unsplash.com/photo-1589829545856-d10d557cf95f?$q",
        'Langues' => "https://images.unsplash.com/photo-1543002588-bfa64002ed9e?$q",
        'Anglais' => "https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?$q",
        'Français' => "https://images.unsplash.com/photo-1497633762265-9d179a990aa6?$q",
        'Marketing' => "https://images.unsplash.com/photo-1533750349088-cd871a92f312?$q",
        'Design' => "https://images.unsplash.com/photo-1561070791-2526d309894b?$q",
        'Science' => "https://images.unsplash.com/photo-1576086213369-97a306d36558?$q",
        'Histoire' => "https://images.unsplash.com/photo-1461360228754-6e81c478b882?$q",
        'Géographie' => "https://images.unsplash.com/photo-1526779259212-939e64788e3c?$q",
        'Philosophie' => "https://images.unsplash.com/photo-1521587760474-fcfb5b35721a?$q",
        'Art' => "https://images.unsplash.com/photo-1541961017774-22349e4a1262?$q",
        'Musique' => "https://images.unsplash.com/photo-1514320291840-2e0a9dee2a41?$q",
        'Sport' => "https://images.unsplash.com/photo-1461896836934-ffe607ba8211?$q",
        'Autre' => "https://images.unsplash.com/photo-1434030216411-0b793f4b4173?$q",
    ];
}
