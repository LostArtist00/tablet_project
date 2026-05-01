document.addEventListener('DOMContentLoaded', function() {
    // Lightbox functionality
    const lightbox = document.querySelector('[data-lightbox]');
    const lightboxTrigger = document.querySelector('[data-lightbox-trigger]');
    const lightboxContent = document.querySelector('[data-lightbox-content]');

    if (lightbox && lightboxTrigger && lightboxContent) {
        lightboxTrigger.addEventListener('click', function() {
            lightboxContent.textContent = this.dataset.lightboxTrigger;
            lightbox.classList.add('active');
        });
        lightbox.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    }

    // Reveal animations
    const reveals = document.querySelectorAll('.reveal');
    if (reveals.length) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                }
            });
        }, { threshold: 0.1 });
        reveals.forEach(el => observer.observe(el));
    }

    // Form validation feedback
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const required = this.querySelectorAll('[required]');
            let valid = true;
            required.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.style.borderColor = '#e74c3c';
                } else {
                    field.style.borderColor = '';
                }
            });
            if (!valid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });
});