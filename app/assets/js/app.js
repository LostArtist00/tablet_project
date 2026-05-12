document.addEventListener('DOMContentLoaded', function() {

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

    // Back to top button
    const backToTop = document.querySelector('.back-to-top');
    if (backToTop) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        });
        backToTop.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // Filter form auto-submit
    const filterForm = document.querySelector('.filter-form');
    if (filterForm) {
        const selects = filterForm.querySelectorAll('select');
        selects.forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
    }

    /* ---------- Scroll effects ---------- */
    window.addEventListener('scroll', () => {
        const nav = document.querySelector('nav');
        if (window.scrollY > 100) {
            nav.style.padding = '0.5rem 2rem';
            nav.style.background = 'rgba(15, 23, 42, 0.95)';
        } else {
            nav.style.padding = '1rem 2rem';
            nav.style.background = 'rgba(15, 23, 42, 0.8)';
        }
    });

    /* ---------- Mobile menu ---------- */
    const mobileToggle = document.getElementById('mobileMenuToggle');
    const mobileNav = document.getElementById('mobileNav');

    if (mobileToggle && mobileNav) {
        mobileToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            mobileNav.classList.toggle('active');
        });

        document.addEventListener('click', function(e) {
            if (!mobileToggle.contains(e.target) && !mobileNav.contains(e.target)) {
                mobileNav.classList.remove('active');
                mobileToggle.classList.remove('active');
            }
        });

        mobileNav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function() {
                mobileNav.classList.remove('active');
                mobileToggle.classList.remove('active');
            });
        });
    }

    /* ---------- Gallery live filtering ---------- */
    const galleryGrid = document.getElementById('galleryGrid');
    const gallerySearch = document.getElementById('gallerySearch');
    const brandCheckboxes = document.querySelectorAll('[data-filter="brand"]');
    const typeRadios = document.querySelectorAll('[data-filter="type"]');

    function filterGallery() {
        if (!galleryGrid) return;
        const q = gallerySearch ? gallerySearch.value.trim().toLowerCase() : '';
        const selectedBrands = [];
        brandCheckboxes.forEach(cb => {
            if (cb.checked) selectedBrands.push(cb.value);
        });
        let selectedType = null;
        typeRadios.forEach(r => {
            if (r.checked) selectedType = r.value;
        });

        const items = galleryGrid.querySelectorAll('.gallery-item');
        let visibleCount = 0;
        items.forEach(item => {
            const brand = item.dataset.brand;
            const type = item.dataset.type;
            const name = (item.dataset.name || '').toLowerCase();

            const matchBrand = selectedBrands.length === 0 || selectedBrands.includes(brand);
            const matchType = !selectedType || type === selectedType;
            const matchSearch = !q || name.includes(q);

            if (matchBrand && matchType && matchSearch) {
                item.classList.add('visible');
                visibleCount++;
            } else {
                item.classList.remove('visible');
            }
        });

        const empty = galleryGrid.querySelector('.gallery-empty');
        if (empty) {
            empty.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    if (galleryGrid) {
        brandCheckboxes.forEach(cb => cb.addEventListener('change', filterGallery));
        typeRadios.forEach(r => r.addEventListener('change', filterGallery));
        if (gallerySearch) gallerySearch.addEventListener('input', filterGallery);
        filterGallery();
    }

    /* ---------- Survey step navigation ---------- */
    const surveyForm = document.getElementById('surveyForm');
    if (surveyForm) {
        const steps = surveyForm.querySelectorAll('.step');
        const nextBtns = surveyForm.querySelectorAll('.next-btn');
        const prevBtns = surveyForm.querySelectorAll('.prev-btn');
        const surveySearch = document.getElementById('surveySearch');

        function showStep(num) {
            steps.forEach(s => s.removeAttribute('aria-current'));
            const target = surveyForm.querySelector('.step[data-step="' + num + '"]');
            if (target) target.setAttribute('aria-current', 'step');

            const dots = surveyForm.querySelectorAll('.step-dot');
            dots.forEach((d, i) => {
                d.classList.toggle('active', i + 1 === num);
            });
        }

        nextBtns.forEach(btn => btn.addEventListener('click', function() {
            const curr = parseInt(surveyForm.querySelector('.step[aria-current]').dataset.step);
            showStep(curr + 1);
        }));

        prevBtns.forEach(btn => btn.addEventListener('click', function() {
            const curr = parseInt(surveyForm.querySelector('.step[aria-current]').dataset.step);
            showStep(curr - 1);
        }));

        showStep(1);

        if (surveySearch) {
            const tabletList = document.getElementById('surveyTabletList');
            surveySearch.addEventListener('input', function() {
                const q = this.value.trim().toLowerCase();
                const labels = tabletList.querySelectorAll('.survey-tablet-item');
                labels.forEach(lab => {
                    const text = lab.textContent.toLowerCase();
                    lab.style.display = text.includes(q) ? 'flex' : 'none';
                });
            });
        }

        surveyForm.addEventListener('submit', function(e) {
            let allFilled = true;
            steps.forEach(s => {
                const reqs = s.querySelectorAll('[required]');
                reqs.forEach(f => {
                    if (!f.value.trim()) allFilled = false;
                });
            });
            if (!allFilled) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return;
            }
        });
    }
});
