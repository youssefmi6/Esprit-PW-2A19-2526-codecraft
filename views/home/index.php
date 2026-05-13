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
        
        .hero { background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); padding: 60px 0; margin-bottom: 50px; position: relative; overflow: hidden; }
        .hero::before,
        .hero::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            filter: blur(1px);
            opacity: 0.55;
            pointer-events: none;
        }
        .hero::before {
            width: 280px;
            height: 280px;
            background: radial-gradient(circle, rgba(37,99,235,0.35) 0%, rgba(37,99,235,0) 70%);
            top: -80px;
            right: -40px;
            animation: floatBlobOne 9s ease-in-out infinite;
        }
        .hero::after {
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(14,165,233,0.35) 0%, rgba(14,165,233,0) 70%);
            bottom: -70px;
            left: -50px;
            animation: floatBlobTwo 11s ease-in-out infinite;
        }
        .hero h1 { font-size: 48px; margin-bottom: 20px; }
        .hero h1 span { color: var(--primary); }
        .hero-illustration { max-width: 100%; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); animation: floatImage 7s ease-in-out infinite; }
        .hero-content-animated { animation: fadeInUp 0.9s ease both; }
        .hero-visual-animated { animation: fadeInUp 1.15s ease both; }
        .studyhub-book-scene {
            width: min(100%, 430px);
            margin: 0 auto;
            perspective: 1400px;
            perspective-origin: 50% 45%;
            min-height: 360px;
            display: grid;
            place-items: center;
            position: relative;
        }
        .studyhub-book-orbit {
            position: absolute;
            width: 330px;
            height: 330px;
            border-radius: 50%;
            border: 1px solid rgba(96, 165, 250, 0.26);
            background: radial-gradient(circle, rgba(59, 130, 246, 0.13), rgba(59, 130, 246, 0));
            box-shadow: inset 0 0 45px rgba(96, 165, 250, 0.14);
            transform: translateY(-4px);
            animation: orbitPulse 4.8s ease-in-out infinite;
            pointer-events: none;
        }
        .studyhub-book {
            width: 280px;
            height: 200px;
            position: relative;
            transform-style: preserve-3d;
            transform: rotateX(18deg) rotateZ(-15deg);
            filter: drop-shadow(0 30px 26px rgba(15, 23, 42, 0.33));
            animation: bookFloat 6.8s ease-in-out infinite;
        }
        .studyhub-book-bottom,
        .studyhub-book-pages,
        .studyhub-book-cover {
            position: absolute;
            inset: 0;
            transform-style: preserve-3d;
            border-radius: 8px;
        }
        .studyhub-book-bottom {
            background: linear-gradient(165deg, #0f2f9b 0%, #1f4dd4 65%, #0a216f 100%);
            transform: translateZ(-18px);
            box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.08);
        }
        .studyhub-book-spine {
            position: absolute;
            left: -16px;
            top: 0;
            width: 16px;
            height: 100%;
            transform-origin: right center;
            transform: rotateY(-90deg);
            border-radius: 6px 0 0 6px;
            background: linear-gradient(180deg, #1e3a8a, #0b2a88);
        }
        .studyhub-book-pages {
            background: linear-gradient(145deg, #fcfdff 0%, #eef2ff 100%);
            transform: translateZ(-2px);
            box-shadow: inset 0 0 0 1px rgba(100, 116, 139, 0.24);
            overflow: hidden;
        }
        .studyhub-page-content {
            position: absolute;
            inset: 18px 22px;
            text-align: left;
            color: #1e293b;
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        .studyhub-book.play .studyhub-page-content {
            opacity: 1;
            transform: translateY(0);
            transition-delay: 3.5s;
        }
        .studyhub-page-content h4 {
            margin: 0 0 8px;
            color: #1d4ed8;
            font-size: 23px;
            font-weight: 800;
        }
        .studyhub-page-content p {
            margin: 0;
            color: #475569;
            font-size: 14px;
            line-height: 1.45;
        }
        .studyhub-page-line {
            display: block;
            width: 100%;
            height: 7px;
            margin-top: 9px;
            border-radius: 999px;
            background: linear-gradient(90deg, rgba(37, 99, 235, 0.28), rgba(56, 189, 248, 0.2));
        }
        .studyhub-page {
            position: absolute;
            top: 8px;
            right: 6px;
            width: calc(100% - 16px);
            height: calc(100% - 16px);
            border-radius: 6px;
            background: linear-gradient(145deg, #ffffff 0%, #f1f5f9 100%);
            transform-origin: left center;
            transform-style: preserve-3d;
            box-shadow: inset 0 0 0 1px rgba(148, 163, 184, 0.35);
            backface-visibility: hidden;
        }
        .studyhub-page::after {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(90deg, rgba(15, 23, 42, 0.07), rgba(15, 23, 42, 0));
            pointer-events: none;
        }
        .studyhub-page.page-1 { z-index: 6; }
        .studyhub-page.page-2 { z-index: 5; }
        .studyhub-page.page-3 { z-index: 4; }
        .studyhub-page.page-4 { z-index: 3; }
        .studyhub-book-cover {
            z-index: 10;
            transform-origin: left center;
            will-change: transform;
        }
        .studyhub-book-cover-face {
            position: absolute;
            inset: 0;
            border-radius: 8px;
            backface-visibility: hidden;
            border: 1px solid rgba(255,255,255,0.18);
            box-shadow: 0 8px 26px rgba(30, 64, 175, 0.35), inset 0 0 0 1px rgba(255,255,255,0.12);
            overflow: hidden;
        }
        .studyhub-book-cover-face.front {
            background: linear-gradient(160deg, #3b82f6 0%, #1d4ed8 55%, #1e40af 100%);
        }
        .studyhub-book-cover-face.back {
            transform: rotateY(180deg);
            background: linear-gradient(165deg, #1e40af 0%, #1e3a8a 100%);
        }
        .studyhub-book-cover-badge {
            position: absolute;
            left: 20px;
            top: 18px;
            width: 96px;
            height: 96px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.28);
            background: radial-gradient(circle at 40% 35%, rgba(255,255,255,0.35), rgba(255,255,255,0.05));
            pointer-events: none;
        }
        .studyhub-book-cover-title {
            position: absolute;
            left: 22px;
            top: 24px;
            font-family: 'Jost', sans-serif;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 0.8px;
            color: rgba(255,255,255,0.95);
        }
        .studyhub-book-cover-subtitle {
            position: absolute;
            left: 24px;
            top: 62px;
            color: rgba(219, 234, 254, 0.88);
            font-size: 13px;
            letter-spacing: 0.7px;
        }
        .studyhub-book-left-content {
            position: absolute;
            inset: 18px 20px;
            transform: rotateY(180deg);
            color: #dbeafe;
            text-align: left;
            backface-visibility: hidden;
        }
        .studyhub-book-left-content h5 {
            margin: 0 0 8px;
            color: #ffffff;
            font-size: 19px;
            font-weight: 700;
            letter-spacing: 0.3px;
        }
        .studyhub-book-left-content p {
            margin: 0;
            font-size: 13px;
            line-height: 1.45;
            color: rgba(219, 234, 254, 0.95);
        }
        .studyhub-book-left-line {
            display: block;
            width: 100%;
            height: 6px;
            margin-top: 8px;
            border-radius: 999px;
            background: linear-gradient(90deg, rgba(191, 219, 254, 0.85), rgba(147, 197, 253, 0.25));
        }
        .studyhub-book-glow {
            position: absolute;
            width: 210px;
            height: 80px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.35), rgba(59,130,246,0));
            transform: translateY(120px) rotateX(80deg);
            filter: blur(3px);
            pointer-events: none;
        }
        .studyhub-book.play .studyhub-book-cover {
            animation: openCover 2.9s cubic-bezier(0.23, 0.74, 0.22, 0.99) forwards;
        }
        .studyhub-book.play .studyhub-page.page-1 {
            animation: flipPage 1.55s cubic-bezier(0.24, 0.76, 0.22, 1) forwards;
            animation-delay: 1.55s;
        }
        .studyhub-book.play .studyhub-page.page-2 {
            animation: flipPage 1.45s cubic-bezier(0.24, 0.76, 0.22, 1) forwards;
            animation-delay: 2.2s;
        }
        .studyhub-book.play .studyhub-page.page-3 {
            animation: flipPage 1.35s cubic-bezier(0.24, 0.76, 0.22, 1) forwards;
            animation-delay: 2.82s;
        }
        .studyhub-book.play .studyhub-page.page-4 {
            animation: flipPage 1.25s cubic-bezier(0.24, 0.76, 0.22, 1) forwards;
            animation-delay: 3.4s;
        }
        @keyframes openCover {
            0% { transform: rotateY(0deg) translateZ(2px); }
            45% { transform: rotateY(-92deg) translateZ(2px); }
            100% { transform: rotateY(-165deg) translateZ(2px); }
        }
        @keyframes flipPage {
            0% { transform: rotateY(0deg); }
            55% { transform: rotateY(-115deg); }
            100% { transform: rotateY(-170deg); }
        }
        @keyframes bookFloat {
            0%, 100% { transform: rotateX(18deg) rotateZ(-15deg) translateY(0); }
            50% { transform: rotateX(18deg) rotateZ(-15deg) translateY(-8px); }
        }
        @keyframes orbitPulse {
            0%, 100% { transform: translateY(-4px) scale(1); opacity: 0.85; }
            50% { transform: translateY(-4px) scale(1.04); opacity: 1; }
        }
        
        .search-wrapper { background: white; border-radius: 60px; padding: 5px; display: flex; max-width: 420px; gap: 8px; align-items: center; }
        .search-wrapper input { flex: 1; border: none; padding: 12px 18px; border-radius: 60px; outline: none; font-size: 14px; }
        .search-wrapper button { background: var(--primary); border: none; padding: 10px 22px; border-radius: 60px; color: white; font-weight: 600; transition: all 0.3s ease; cursor: pointer; font-size: 13px; }
        .search-wrapper button:hover { opacity: 0.9; }
        
        /* Styles pour la recherche vocale */
        .voice-search-container { margin-top: 14px; display: flex; flex-direction: column; align-items: center; gap: 10px; }
        #voiceSearchBtn { background: linear-gradient(135deg, #8b5cf6, #7c3aed); border: none; padding: 10px 28px; border-radius: 60px; color: white; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center; justify-content: center; gap: 8px; font-size: 13px; white-space: nowrap; }
        #voiceSearchBtn:hover { opacity: 0.9; transform: scale(1.02); }
        #voiceSearchBtn:active { transform: scale(0.98); }
        #voiceSearchBtn.listening { background: linear-gradient(135deg, #ef4444, #dc2626); animation: pulse 1.2s ease-in-out infinite; }
        #voiceSearchBtn.stopped { background: linear-gradient(135deg, #10b981, #059669); }
        #voiceSearchBtn.error { background: linear-gradient(135deg, #ef4444, #dc2626); }
        
        #voiceStatus { font-size: 11px; min-height: 20px; display: flex; align-items: center; justify-content: center; gap: 8px; color: #16a34a; font-weight: 500; }
        #voiceTranscript { width: 100%; max-width: 350px; padding: 10px 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 12px; font-style: italic; color: #1e293b; background-color: #f8fafc; display: none; }
        #voiceTranscript:focus { outline: none; border-color: #2563eb; background-color: #eff6ff; }
        
        :root[data-theme="dark"] #voiceTranscript { background-color: #1f2937; color: #f1f5f9; border-color: #374151; }
        :root[data-theme="dark"] #voiceTranscript:focus { background-color: #111827; border-color: #60a5fa; }
        
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            50% { box-shadow: 0 0 0 12px rgba(239, 68, 68, 0); }
        }
        
        .resources-toolbar { display:flex; justify-content:flex-end; gap:10px; margin-bottom:18px; }
        .resources-toolbar .form-select { max-width: 260px; border-radius: 12px; border: 2px solid #dbeafe; }
        
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
        .reveal-on-scroll {
            opacity: 0;
            transform: translateY(22px);
            transition: opacity 0.65s ease, transform 0.65s ease;
        }
        .reveal-on-scroll.revealed {
            opacity: 1;
            transform: translateY(0);
        }
        @keyframes floatBlobOne {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-18px, 12px) scale(1.06); }
        }
        @keyframes floatBlobTwo {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(16px, -10px) scale(1.08); }
        }
        @keyframes floatImage {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (prefers-reduced-motion: reduce) {
            .studyhub-book,
            .studyhub-book.play .studyhub-book-cover,
            .studyhub-book.play .studyhub-page {
                animation: none !important;
            }
            .studyhub-book-cover {
                transform: rotateY(-150deg) translateZ(2px);
            }
            .studyhub-page {
                transform: rotateY(-165deg);
            }
            .studyhub-page-content {
                opacity: 1;
                transform: none;
            }
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
            <div class="col-lg-6 hero-content-animated">
                <h1>Partagez, <span>apprenez</span> et réussissez ensemble</h1>
                <p class="lead mt-3">Rejoignez une communauté d'étudiants qui partagent leurs connaissances.</p>
                <div class="search-wrapper mt-4">
                    <input type="text" id="searchInput" placeholder="Rechercher une ressource...">
                    <button onclick="searchResources()"><i class="ti-search"></i> Rechercher</button>
                </div>
                <div class="voice-search-container">
                    <button type="button" id="voiceSearchBtn" title="Cliquez pour chercher par voix"><i class="fa-solid fa-microphone"></i> Chercher par voix</button>
                    <div id="voiceStatus"></div>
                    <input type="text" id="voiceTranscript" placeholder="Le texte reconnu apparaîtra ici..." readonly>
                </div>
            </div>
            <div class="col-lg-6 text-center mt-4 mt-lg-0 hero-visual-animated">
                <div class="studyhub-book-scene" aria-label="Livre 3D StudyHub animé">
                    <span class="studyhub-book-orbit" aria-hidden="true"></span>
                    <div class="studyhub-book" id="studyhubBook">
                        <span class="studyhub-book-glow" aria-hidden="true"></span>
                        <div class="studyhub-book-bottom"></div>
                        <div class="studyhub-book-spine"></div>
                        <div class="studyhub-book-pages">
                            <div class="studyhub-page-content">
                                <h4>Explore StudyHub</h4>
                                <p>Des cours, fiches et examens qui s'ouvrent devant vous en un instant.</p>
                                <span class="studyhub-page-line"></span>
                                <span class="studyhub-page-line"></span>
                                <span class="studyhub-page-line" style="width:72%;"></span>
                            </div>
                            <span class="studyhub-page page-4"></span>
                            <span class="studyhub-page page-3"></span>
                            <span class="studyhub-page page-2"></span>
                            <span class="studyhub-page page-1"></span>
                        </div>
                        <div class="studyhub-book-cover">
                            <span class="studyhub-book-cover-face front">
                                <span class="studyhub-book-cover-badge"></span>
                                <span class="studyhub-book-cover-title">StudyHub</span>
                                <span class="studyhub-book-cover-subtitle">Learn • Share • Grow</span>
                            </span>
                            <span class="studyhub-book-cover-face back">
                                <span class="studyhub-book-left-content">
                                    <h5>Bienvenu sur StudyHub</h5>
                                    <p>Page gauche lisible, ouverture fluide et expérience moderne.</p>
                                    <span class="studyhub-book-left-line"></span>
                                    <span class="studyhub-book-left-line" style="width:82%;"></span>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container" id="resources">
    <div class="category-section reveal-on-scroll">
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

    <div class="text-center mb-5 reveal-on-scroll">
        <h2>📚 Ressources éducatives</h2>
        <p class="text-muted">Des milliers de ressources pour réussir vos études</p>
    </div>
    <div class="resources-toolbar reveal-on-scroll">
        <select id="frontResourcesSort" class="form-select">
            <option value="date_desc" <?= (($_GET['sort'] ?? 'date_desc') === 'date_desc') ? 'selected' : '' ?>>Trier: Plus récents</option>
            <option value="date_asc" <?= (($_GET['sort'] ?? 'date_desc') === 'date_asc') ? 'selected' : '' ?>>Trier: Plus anciens</option>
            <option value="alpha_asc" <?= (($_GET['sort'] ?? 'date_desc') === 'alpha_asc') ? 'selected' : '' ?>>Titre A-Z</option>
            <option value="alpha_desc" <?= (($_GET['sort'] ?? 'date_desc') === 'alpha_desc') ? 'selected' : '' ?>>Titre Z-A</option>
            <option value="downloads_desc" <?= (($_GET['sort'] ?? 'date_desc') === 'downloads_desc') ? 'selected' : '' ?>>Téléchargements</option>
            <option value="rating_desc" <?= (($_GET['sort'] ?? 'date_desc') === 'rating_desc') ? 'selected' : '' ?>>Meilleure note</option>
        </select>
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

    <div class="contributors-section reveal-on-scroll" id="contributors">
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
var sortInput = document.getElementById('frontResourcesSort');
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
        matiere: currentMatiereFilter,
        sort: sortInput ? sortInput.value : 'date_desc'
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
<script src="js/voice-search.js"></script>
</body>
</html>