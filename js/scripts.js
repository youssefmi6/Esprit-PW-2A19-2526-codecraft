// scripts.js - Scripts principaux

document.addEventListener('DOMContentLoaded', function() {
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

    const commentForm = document.getElementById('commentForm');
    if (commentForm) {
        const messageField = commentForm.querySelector('textarea[name="message"]');
        commentForm.addEventListener('submit', function(e) {
            if (!messageField || !validateCommentMessage(messageField.value)) {
                e.preventDefault();
                if (messageField) showError(messageField, 'Veuillez saisir un commentaire');
            } else if (messageField) removeError(messageField);
        });
    }
});