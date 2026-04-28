<?php
// views/home/index.php - Page d'accueil
// NE PAS inclure de modèles ici
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyHub - Plateforme de Ressources Étudiantes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Jost:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/themify-icons@0.1.2/css/themify-icons.css">
    <script>
        (function () {
            var savedTheme = localStorage.getItem('studyhub-theme');
            if (savedTheme === 'light' || savedTheme === 'dark') {
                document.documentElement.setAttribute('data-theme', savedTheme);
                return;
            }
            var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.setAttribute('data-theme', prefersDark ? 'dark' : 'light');
        })();
    </script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: #f0f4f8; }
        :root { --primary: #2563eb; --primary-light: #dbeafe; }
        :root[data-theme="dark"] { --primary: #60a5fa; --primary-light: #1e293b; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Jost', sans-serif; font-weight: 700; }
        
        .btn-primary-custom { background: var(--primary); color: white; padding: 10px 24px; border-radius: 40px; font-weight: 600; border: 2px solid var(--primary); transition: 0.3s; text-decoration: none; display: inline-block; }
        .btn-primary-custom:hover { background: transparent; color: var(--primary); }
        .btn-outline-custom { background: transparent; border: 2px solid var(--primary); color: var(--primary); padding: 10px 24px; border-radius: 40px; font-weight: 600; transition: 0.3s; text-decoration: none; }
        .btn-outline-custom:hover { background: var(--primary); color: white; }
        
        .navbar-custom { background: white; box-shadow: 0 2px 20px rgba(0,0,0,0.05); padding: 15px 0; position: sticky; top: 0; z-index: 1000; }
        .logo { font-size: 24px; font-weight: 800; color: var(--primary); text-decoration: none; display: inline-flex; align-items: center; line-height: 1; }
        .logo .site-logo { height: 40px; width: auto; max-width: 220px; object-fit: contain; display: block; }
        .footer h4 .site-logo--footer { height: 36px; max-width: 200px; object-fit: contain; display: block; }
        .nav-links { display: flex; gap: 35px; list-style: none; margin: 0; padding: 0; }
        .nav-links a { color: #334155; text-decoration: none; font-weight: 500; transition: 0.3s; }
        .nav-links a:hover, .nav-links a.active { color: var(--primary); }
        
        .user-info { display: flex; align-items: center; gap: 15px; cursor: pointer; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary); }
        .user-name { font-weight: 500; color: #1e293b; }
        .nav-right-controls { display:flex; align-items:center; justify-content:flex-end; gap:10px; }
        .dropdown-menu-custom { position: absolute; right: 0; top: 50px; background: white; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); padding: 10px 0; min-width: 180px; display: none; z-index: 1000; }
        .dropdown-menu-custom a { display: block; padding: 10px 20px; color: #1e293b; text-decoration: none; transition: 0.3s; }
        .dropdown-menu-custom a:hover { background: #f1f5f9; color: var(--primary); }
        .user-dropdown { position: relative; }
        
        .hero { background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); padding: 60px 0; margin-bottom: 50px; }
        .hero h1 { font-size: 48px; margin-bottom: 20px; }
        .hero h1 span { color: var(--primary); }
        .hero-illustration { max-width: 100%; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        
        .search-wrapper { background: white; border-radius: 60px; padding: 5px; display: flex; max-width: 500px; }
        .search-wrapper input { flex: 1; border: none; padding: 15px 25px; border-radius: 60px; outline: none; }
        .search-wrapper button { background: var(--primary); border: none; padding: 12px 30px; border-radius: 60px; color: white; font-weight: 600; }
        
        .resource-card { background: white; border-radius: 20px; overflow: hidden; transition: all 0.3s ease; box-shadow: 0 5px 20px rgba(0,0,0,0.05); margin-bottom: 30px; border: 1px solid #e2e8f0; }
        .resource-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(37,99,235,0.15); }
        .resource-img { background: #e2e8f0; padding: 0; text-align: center; position: relative; height: 200px; overflow: hidden; display: flex; align-items: center; justify-content: center; }
        .resource-img img { width: 100%; height: 100%; object-fit: cover; object-position: center; border-radius: 0; }
        .resource-badge { position: absolute; top: 15px; left: 15px; padding: 5px 15px; border-radius: 30px; font-size: 12px; font-weight: 600; color: white; }
        .badge-premium { background: linear-gradient(135deg, #f59e0b, #ef4444); }
        .badge-free { background: #10b981; }
        .resource-content { padding: 20px; }
        .resource-title { font-size: 18px; font-weight: 700; margin: 10px 0; }
        .resource-title a { color: #1e293b; text-decoration: none; }
        .resource-title a:hover { color: var(--primary); }
        .resource-stats { display: flex; flex-wrap: wrap; gap: 15px; margin: 15px 0; color: #64748b; font-size: 13px; }
        .resource-stats span { display: flex; align-items: center; gap: 5px; }
        .resource-price { font-size: 16px; font-weight: 700; color: var(--primary); margin-top: 15px; padding-top: 15px; border-top: 1px solid #e2e8f0; }
        .resource-actions { display: flex; gap: 12px; margin-top: 15px; flex-wrap: wrap; }
        .stars { color: #fbbf24; margin-bottom: 10px; }
        
        .category-section { background: white; padding: 50px 0; margin: 40px 0; border-radius: 30px; }
        .category-item { text-align: center; padding: 20px; transition: 0.3s; border-radius: 16px; cursor: pointer; }
        .category-item:hover { background: var(--primary-light); transform: translateY(-5px); }
        .category-item > .category-img-wrapper { margin-bottom: 12px; }
        .categories-carousel {
            position: relative;
            padding: 0 52px;
        }
        .categories-track {
            display: flex;
            gap: 0;
            overflow-x: auto;
            scroll-behavior: smooth;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .categories-track::-webkit-scrollbar {
            display: none;
        }
        .category-slide {
            flex: 0 0 33.3333%;
            min-width: 33.3333%;
            padding: 0 12px;
        }
        .categories-nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 50%;
            background: #ffffff;
            color: #1e293b;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.18);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            cursor: pointer;
            pointer-events: auto;
            transition: all 0.2s ease;
        }
        .categories-nav-btn:hover {
            background: var(--primary);
            color: #ffffff;
        }
        .categories-nav-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }
        .categories-nav-btn.prev { left: 4px; }
        .categories-nav-btn.next { right: 4px; }
        
        .contributors-section { background: white; padding: 60px 0; margin: 40px 0; border-radius: 30px; }
        .contributor-card { text-align: center; padding: 30px 20px; transition: 0.3s; border-radius: 20px; background: #fff; margin-bottom: 20px; border: 1px solid #eef2ff; }
        .contributor-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(37,99,235,0.1); }
        .contributor-avatar { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; margin-bottom: 20px; border: 4px solid var(--primary); }
        .contributor-name { font-size: 20px; font-weight: 700; margin-bottom: 5px; color: #1e293b; }
        .contributor-card a { text-decoration: none; color: inherit; }
        .contributor-card a:hover .contributor-name { color: var(--primary); }
        .contributor-title { color: var(--primary); font-size: 14px; font-weight: 500; margin-bottom: 15px; }
        .contributor-stats { display: flex; justify-content: center; gap: 25px; margin-top: 15px; padding-top: 15px; border-top: 1px solid #e2e8f0; }
        .contributor-stats span { display: flex; align-items: center; gap: 6px; font-size: 14px; color: #64748b; }
        .contributor-badge { display: inline-block; background: linear-gradient(135deg, #f59e0b, #ef4444); color: white; font-size: 11px; padding: 3px 10px; border-radius: 30px; margin-top: 8px; }
        
        .footer { background: #0f172a; color: #94a3b8; padding: 60px 0 30px; margin-top: 60px; }
        .footer h4 { color: white; margin-bottom: 25px; }
        .footer-links { list-style: none; padding: 0; }
        .footer-links li { margin-bottom: 12px; }
        .footer-links a { color: #94a3b8; text-decoration: none; }
        .footer-links a:hover { color: var(--primary); }
        .social-links a { display: inline-flex; width: 38px; height: 38px; background: rgba(255,255,255,0.1); border-radius: 50%; align-items: center; justify-content: center; margin-right: 10px; color: white; transition: 0.3s; }
        .social-links a:hover { background: var(--primary); transform: translateY(-3px); }
        .copyright { background: #0a0f1c; text-align: center; padding: 20px; font-size: 14px; color: #64748b; }
        .theme-toggle { width: 40px; height: 40px; border-radius: 50%; border: 2px solid #dbeafe; background: #fff; color: #2563eb; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.25s ease; margin-right: 8px; }
        .theme-toggle i { transition: transform 0.35s ease; }
        .theme-toggle:active i { transform: rotate(180deg); }
        .theme-toggle:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25); }

        :root[data-theme="dark"] body { background: #0f172a; color: #e2e8f0; }
        :root[data-theme="dark"] .navbar-custom { background: #111827; box-shadow: 0 2px 20px rgba(0,0,0,0.35); }
        :root[data-theme="dark"] .nav-links a { color: #cbd5e1; }
        :root[data-theme="dark"] .user-name { color: #e2e8f0; }
        :root[data-theme="dark"] .dropdown-menu-custom { background: #1f2937; }
        :root[data-theme="dark"] .dropdown-menu-custom a { color: #e5e7eb; }
        :root[data-theme="dark"] .dropdown-menu-custom a:hover { background: #374151; }
        :root[data-theme="dark"] .hero { background: linear-gradient(135deg, #111827 0%, #1f2937 100%); }
        :root[data-theme="dark"] .search-wrapper,
        :root[data-theme="dark"] .resource-card,
        :root[data-theme="dark"] .category-section,
        :root[data-theme="dark"] .contributors-section,
        :root[data-theme="dark"] .contributor-card { background: #111827; border-color: #334155; }
        :root[data-theme="dark"] .resource-title a,
        :root[data-theme="dark"] .contributor-name,
        :root[data-theme="dark"] h2,
        :root[data-theme="dark"] h1 { color: #f8fafc; }
        :root[data-theme="dark"] .text-muted,
        :root[data-theme="dark"] .resource-stats,
        :root[data-theme="dark"] .contributor-stats { color: #94a3b8 !important; }
        :root[data-theme="dark"] .resource-price,
        :root[data-theme="dark"] .contributor-stats { border-color: #334155; }
        :root[data-theme="dark"] .theme-toggle { background: #1f2937; border-color: #334155; color: #fbbf24; }
        :root[data-theme="dark"] .categories-nav-btn {
            background: #1f2937;
            color: #e2e8f0;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.35);
        }
        :root[data-theme="dark"] .categories-nav-btn:hover {
            background: var(--primary);
            color: #0f172a;
        }
        
        @media (max-width: 768px) { .hero h1 { font-size: 32px; } .nav-links { display: none; } }
        @media (max-width: 991.98px) {
            .category-slide {
                flex: 0 0 50%;
                min-width: 50%;
            }
            .categories-carousel {
                padding: 0 42px;
            }
        }
        @media (max-width: 575.98px) {
            .category-slide {
                flex: 0 0 100%;
                min-width: 100%;
            }
            .categories-carousel {
                padding: 0 36px;
            }
        }
        
        .category-img-wrapper {
            width: 96px;
            height: 96px;
            margin: 0 auto;
            border-radius: 50%;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(15, 23, 42, 0.12);
            border: 3px solid #eef2ff;
        }
        .category-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }
    </style>
</head>
<body>

<nav class="navbar-custom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-6 col-lg-3"><a href="index.php?action=home" class="logo"><img src="uploads/logo.png" alt="StudyHub" class="site-logo"></a></div>
            <div class="col-lg-6 d-none d-lg-block">
                <ul class="nav-links">
                    <li><a href="index.php?action=home" class="active">Accueil</a></li>
                    <li><a href="#resources">Ressources</a></li>
                    <li><a href="#contributors">Top Contributeurs</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-3">
                <div class="nav-right-controls">
                <button type="button" class="theme-toggle" id="themeToggle" title="Changer le mode">
                    <i class="fa-solid fa-sun" id="themeIcon"></i>
                </button>
                <?php if ($currentUser): ?>
                    <div class="user-dropdown" id="userDropdown">
                        <div class="user-info">
                            <img src="<?php echo htmlspecialchars(!empty($currentUser['photo']) ? $currentUser['photo'] : 'https://randomuser.me/api/portraits/men/32.jpg'); ?>" class="user-avatar">
                            <span class="user-name"><?php echo htmlspecialchars($currentUser['nom']); ?></span>
                            <i class="ti-angle-down"></i>
                        </div>
                        <div class="dropdown-menu-custom" id="dropdownMenu">
                            <a href="index.php?action=profile"><i class="ti-user"></i> Mon profil</a>
                            <a href="index.php?action=resource&subaction=upload"><i class="ti-upload"></i> Publier une ressource</a>
                            <hr>
                            <a href="index.php?action=logout" style="color:#ef4444;"><i class="ti-power-off"></i> Déconnexion</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="index.php?action=login" class="btn-outline-custom me-2" style="padding:6px 20px;">Connexion</a>
                    <a href="index.php?action=register" class="btn-primary-custom" style="padding:6px 20px;">Inscription</a>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    var userDropdown = document.getElementById('userDropdown');
    if(userDropdown) {
        userDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
            var menu = document.getElementById('dropdownMenu');
            if(menu) {
                menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
            }
        });
    }
    document.addEventListener('click', function() {
        var menu = document.getElementById('dropdownMenu');
        if(menu) menu.style.display = 'none';
    });
</script>

<section class="hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1>Partagez, <span>apprenez</span> et réussissez ensemble</h1>
                <p class="lead mt-3">Rejoignez une communauté d'étudiants qui partagent leurs connaissances.</p>
                <div class="search-wrapper mt-4">
                    <input type="text" id="searchInput" placeholder="Rechercher une ressource...">
                    <button onclick="searchResources()"><i class="ti-search"></i> Rechercher</button>
                </div>
            </div>
            <div class="col-lg-6 text-center mt-4 mt-lg-0">
                <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=600&h=400&fit=crop" class="hero-illustration" style="height:350px; object-fit:cover;">
            </div>
        </div>
    </div>
</section>

<div class="container" id="resources">
    <div class="category-section">
        <div class="text-center mb-5">
            <h2>Matières populaires</h2>
            <p class="text-muted">Découvrez les ressources par matière</p>
        </div>
        <div class="categories-carousel">
            <button type="button" class="categories-nav-btn prev" id="categoriesPrev" aria-label="Matières précédentes">
                <i class="ti-angle-left"></i>
            </button>
            <button type="button" class="categories-nav-btn next" id="categoriesNext" aria-label="Matières suivantes">
                <i class="ti-angle-right"></i>
            </button>
            <div class="categories-track text-center" id="categoriesTrack">
            <?php if(!empty($matieres)): ?>
                <?php
                    $matieresSorted = $matieres;
                    usort($matieresSorted, function ($a, $b) {
                        return (int)($b['count'] ?? 0) <=> (int)($a['count'] ?? 0);
                    });
                ?>
                <?php foreach ($matieresSorted as $matiere): ?>
                <?php 
                    $matiereKey = $matiere['matiere'];
                    $matiereNom = htmlspecialchars($matiereKey, ENT_QUOTES, 'UTF-8');
                    $matiereImage = $matiere_icons[$matiereKey] ?? $matiere_icons['Autre'];
                ?>
                <div class="category-slide">
                    <div class="category-item" onclick="filterByMatiere(<?php echo htmlspecialchars(json_encode($matiereKey), ENT_QUOTES, 'UTF-8'); ?>)">
                        <div class="category-img-wrapper">
                            <img src="<?php echo $matiereImage; ?>" alt="<?php echo $matiereNom; ?>">
                        </div>
                        <h5><?php echo $matiereNom; ?></h5>
                        <small><?php echo $matiere['count']; ?> Ressources</small>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="text-center mb-5">
        <h2>📚 Ressources éducatives</h2>
        <p class="text-muted">Des milliers de ressources pour réussir vos études</p>
    </div>
    
    <?php if(empty($resources)): ?>
        <div class="alert alert-info text-center">
            <i class="ti-info-alt"></i> Aucune ressource disponible pour le moment. 
            <a href="index.php?action=resource&subaction=upload" class="alert-link">Soyez le premier à publier une ressource !</a>
        </div>
    <?php else: ?>
    <div class="row" id="resourcesGrid">
        <?php foreach ($resources as $res): ?>
        <?php 
            $matiereImage = $matiere_icons[$res['matiere']] ?? $matiere_icons['Autre'];
            $matiereClean = htmlspecialchars($res['matiere'], ENT_QUOTES, 'UTF-8');
            $titreClean = htmlspecialchars($res['titre']);
            $niveauClean = htmlspecialchars($res['niveau']);
            $auteurClean = htmlspecialchars($res['nom']);
            $accesClean = htmlspecialchars($res['acces']);
            $resourceId = (int)$res['id_res'];
            $isOwner = $currentUser && ((int)($res['id'] ?? 0) === (int)$currentUser['id']);
            $isBought = !empty($purchasedResourceIds) && in_array($resourceId, $purchasedResourceIds, true);
            $canDownloadPremium = ($accesClean !== 'Premium') || $isOwner || $isBought;
        ?>
        <div class="col-lg-4 col-md-6" data-matiere="<?php echo $matiereClean; ?>">
            <div class="resource-card">
                <div class="resource-img">
                    <span class="resource-badge <?php echo $accesClean == 'Premium' ? 'badge-premium' : 'badge-free'; ?>"><?php echo $accesClean; ?></span>
                    <img src="<?php echo !empty($res['photo']) ? htmlspecialchars($res['photo']) : $matiereImage; ?>" alt="<?php echo $titreClean; ?>">
                </div>
                <div class="resource-content">
                    <div class="stars">★★★★★</div>
                    <h4 class="resource-title"><a href="index.php?action=resource&subaction=detail&id=<?php echo $resourceId; ?>"><?php echo $titreClean; ?></a></h4>
                    <div class="resource-stats">
                        <span><i class="ti-book"></i> <?php echo $niveauClean; ?></span>
                        <span><i class="ti-folder"></i> <?php echo $matiereClean; ?></span>
                        <span><i class="ti-user"></i> <a href="index.php?action=profile&subaction=view&id=<?php echo $res['user_id']; ?>"><?php echo $auteurClean; ?></a></span>
                    </div>
                    <div class="resource-price"><?php echo $accesClean == 'Premium' ? "💰 " . number_format($res['prix'], 2) . " DT" : '📥 Gratuit'; ?></div>
                    <div class="resource-actions">
                        <a href="index.php?action=resource&subaction=detail&id=<?php echo $resourceId; ?>" class="btn-primary-custom" style="padding:8px 20px;">📖 Voir</a>
                        <?php if ($accesClean == 'Premium' && !$canDownloadPremium): ?>
                            <a href="index.php?action=resource&subaction=buy_checkout&id=<?php echo $resourceId; ?>" class="btn-outline-custom" style="padding:8px 20px;">🛒 Acheter</a>
                        <?php else: ?>
                            <a href="index.php?action=resource&subaction=download&id=<?php echo $resourceId; ?>" class="btn-outline-custom" style="padding:8px 20px;"><i class="ti-download"></i> Télécharger</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="contributors-section" id="contributors">
        <div class="text-center mb-5">
            <h2>🏆 Nos meilleurs contributeurs</h2>
            <p class="text-muted">Ces étudiants partagent leurs connaissances</p>
        </div>
        <?php if(empty($contributors)): ?>
            <div class="text-center text-muted">
                <p>Aucun contributeur pour le moment.</p>
            </div>
        <?php else: ?>
        <div class="row">
            <?php foreach ($contributors as $i => $c): ?>
            <div class="col-lg-3 col-md-6">
                <div class="contributor-card">
                    <a href="index.php?action=profile&subaction=view&id=<?php echo $c['id']; ?>">
                        <img src="<?php echo !empty($c['photo']) ? htmlspecialchars($c['photo']) : 'https://randomuser.me/api/portraits/men/32.jpg'; ?>" class="contributor-avatar" alt="<?php echo htmlspecialchars($c['nom']); ?>">
                        <h4 class="contributor-name"><?php echo htmlspecialchars($c['nom']) . ' ' . htmlspecialchars($c['prenom']); ?></h4>
                    </a>
                    <div class="contributor-title"><?php echo htmlspecialchars($c['filiere'] ?: 'Étudiant'); ?></div>
                    <div class="contributor-stats">
                        <span><i class="ti-book"></i> <?php echo $c['resource_count']; ?> Ressources</span>
                        <span><i class="ti-download"></i> <?php echo number_format($c['total_downloads']); ?> Téléch.</span>
                    </div>
                    <span class="contributor-badge">
                        <?php
                        if($i == 0) echo '🏆 Meilleur contributeur';
                        elseif($i == 1) echo '⭐ Top contributeur';
                        elseif($i == 2) echo '📚 Expert';
                        else echo '🌟 Révélation';
                        ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function searchResources() {
    runDynamicSearch();
}

function filterByMatiere(matiere) {
    runDynamicSearch(matiere);
}

var searchInput = document.getElementById('searchInput');
var searchTimer = null;
var currentMatiereFilter = '';

async function runDynamicSearch(matiere) {
    if (typeof matiere !== 'undefined') {
        currentMatiereFilter = matiere || '';
    }

    var resourcesGrid = document.getElementById('resourcesGrid');
    if (!resourcesGrid) return;

    var term = searchInput ? searchInput.value.trim() : '';
    var params = new URLSearchParams({
        action: 'home',
        ajax: '1',
        search: term,
        matiere: currentMatiereFilter
    });

    try {
        var response = await fetch('index.php?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!response.ok) return;
        var data = await response.json();
        if (typeof data.html === 'string') {
            resourcesGrid.innerHTML = data.html;
        }
    } catch (e) {
        console.error('Dynamic home search failed:', e);
    }
}

if(searchInput) {
    searchInput.addEventListener('keypress', function(e) {
        if(e.key === 'Enter') {
            e.preventDefault();
            searchResources();
        }
    });
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            runDynamicSearch();
        }, 250);
    });
}

var categoriesTrack = document.getElementById('categoriesTrack');
var categoriesPrev = document.getElementById('categoriesPrev');
var categoriesNext = document.getElementById('categoriesNext');

function getCategoryStep() {
    if (!categoriesTrack) return 0;
    var firstSlide = categoriesTrack.querySelector('.category-slide');
    if (firstSlide) {
        var slideWidth = firstSlide.getBoundingClientRect().width;
        if (slideWidth > 0) return slideWidth;
    }
    return Math.max(220, Math.floor(categoriesTrack.clientWidth * 0.8));
}

function updateCategoryNavState() {
    if (!categoriesTrack || !categoriesPrev || !categoriesNext) return;
    var maxScrollLeft = categoriesTrack.scrollWidth - categoriesTrack.clientWidth;
    categoriesPrev.disabled = categoriesTrack.scrollLeft <= 0;
    categoriesNext.disabled = categoriesTrack.scrollLeft >= maxScrollLeft - 1;
}

if (categoriesTrack && categoriesPrev && categoriesNext) {
    function scrollCategories(direction) {
        var step = getCategoryStep();
        var delta = direction === 'next' ? step : -step;
        try {
            categoriesTrack.scrollBy({ left: delta, behavior: 'smooth' });
        } catch (e) {
            categoriesTrack.scrollLeft += delta;
        }
    }

    categoriesPrev.addEventListener('click', function () {
        scrollCategories('prev');
    });

    categoriesNext.addEventListener('click', function () {
        scrollCategories('next');
    });

    categoriesTrack.addEventListener('scroll', updateCategoryNavState);
    window.addEventListener('resize', function () {
        updateCategoryNavState();
    });
    window.addEventListener('load', function () {
        updateCategoryNavState();
    });
    setTimeout(updateCategoryNavState, 150);
    updateCategoryNavState();
}

// Theme toggle handled globally in js/scripts.js
</script>

<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <h4 class="mb-3"><img src="uploads/logo.png" alt="StudyHub" class="site-logo site-logo--footer"></h4>
                <p>Plateforme de partage de ressources académiques entre étudiants.</p>
            </div>
            <div class="col-lg-2">
                <h4>Liens</h4>
                <ul class="footer-links">
                    <li><a href="index.php?action=home">Accueil</a></li>
                    <li><a href="index.php?action=resource&subaction=upload">Publier</a></li>
                    <li><a href="index.php?action=profile">Mon profil</a></li>
                </ul>
            </div>
            <div class="col-lg-3">
                <h4>Contact</h4>
                <ul class="footer-links">
                    <li><i class="ti-location-pin"></i> Tunis, Tunisie</li>
                    <li><i class="ti-mobile"></i> +216 99 999 999</li>
                    <li><i class="ti-email"></i> contact@studyhub.tn</li>
                </ul>
            </div>
        </div>
    </div>
</footer>
<div class="copyright">
    <p>&copy; 2025 StudyHub - Tous droits réservés</p>
</div>

<script src="js/validation.js"></script>
<script src="js/scripts.js"></script>
</body>
</html>