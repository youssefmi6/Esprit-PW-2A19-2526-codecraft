// scripts.js - Scripts principaux

document.addEventListener('DOMContentLoaded', function() {
    // Theme toggle (light / dark) shared across front office pages.
    const root = document.documentElement;
    const savedTheme = localStorage.getItem('studyhub-theme');
    if (savedTheme === 'light' || savedTheme === 'dark') {
        root.setAttribute('data-theme', savedTheme);
    } else {
        const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        root.setAttribute('data-theme', prefersDark ? 'dark' : 'light');
    }

    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const syncThemeIcon = () => {
        if (!themeIcon) return;
        const current = root.getAttribute('data-theme') || 'light';
        themeIcon.className = current === 'dark' ? 'fa-solid fa-moon' : 'fa-solid fa-sun';
    };
    syncThemeIcon();
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const current = root.getAttribute('data-theme') || 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', next);
            localStorage.setItem('studyhub-theme', next);
            themeToggle.classList.add('rotate');
            setTimeout(() => themeToggle.classList.remove('rotate'), 260);
            syncThemeIcon();
        });
    }

    // Animation des étoiles de notation
    const ratingStars = document.querySelectorAll('.rating-star');
    if (ratingStars.length) {
        ratingStars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.getAttribute('data-rating');
                ratingStars.forEach(s => s.classList.remove('active'));
                for(let i = 0; i < rating; i++) ratingStars[i].classList.add('active');
                document.getElementById('selectedRating').value = rating;
                const submitBtn = document.getElementById('submitRating');
                if (submitBtn) submitBtn.disabled = false;
            });
        });
    }
    
    // Toggle du prix pour les ressources premium
    const accessSelect = document.getElementById('accessSelect');
    if (accessSelect) {
        accessSelect.addEventListener('change', function() {
            const priceDiv = document.getElementById('priceDiv');
            if (priceDiv) priceDiv.style.display = this.value === 'Premium' ? 'block' : 'none';
        });
    }
    
    // Aperçu de la photo
    const photoInput = document.getElementById('photoInput');
    if (photoInput) {
        photoInput.addEventListener('change', function() {
            const preview = document.getElementById('photoPreview');
            if (this.files && this.files[0] && preview) {
                const reader = new FileReader();
                reader.onload = function(e) { preview.innerHTML = '<img src="' + e.target.result + '" class="image-preview">'; };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    
    // Aperçu du fichier
    const fileInput = document.getElementById('fileInput');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const fileName = document.getElementById('fileName');
            if (this.files && this.files[0] && fileName) fileName.innerHTML = '<i class="ti-check"></i> ' + this.files[0].name;
        });
    }
    
    // Masquer les alertes après 3 secondes
    setTimeout(function() {
        document.querySelectorAll('.alert-success').forEach(alert => alert.style.display = 'none');
    }, 3000);
});