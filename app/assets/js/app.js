document.addEventListener('DOMContentLoaded', function() {
    const body = document.body;
    const navToggle = document.querySelector('[data-nav-toggle]');
    const nav = document.querySelector('[data-nav]');
    const themeToggle = document.querySelector('[data-theme-toggle]');
    const backToTop = document.querySelector('[data-back-to-top]');
    const hero = document.querySelector('[data-parallax]');
    const revealItems = document.querySelectorAll('.reveal');
    const lightbox = document.querySelector('[data-lightbox]');
    const lightboxContent = document.querySelector('[data-lightbox-content]');

    // Theme
    const savedTheme = localStorage.getItem('tablet-survey-theme');
    if (savedTheme) {
        body.dataset.theme = savedTheme;
    }

    navToggle?.addEventListener('click', function() {
        nav?.classList.toggle('is-open');
    });

    themeToggle?.addEventListener('click', function() {
        const nextTheme = body.dataset.theme === 'light' ? 'dark' : 'light';
        body.dataset.theme = nextTheme;
        localStorage.setItem('tablet-survey-theme', nextTheme);
    });

    document.addEventListener('click', function(e) {
        if (nav && navToggle && !navToggle.contains(e.target) && !nav.contains(e.target)) {
            nav.classList.remove('is-open');
        }
    });

    nav?.querySelectorAll('a').forEach(function(link) {
        link.addEventListener('click', function() {
            nav.classList.remove('is-open');
        });
    });

    // Back to top
    window.addEventListener('scroll', function() {
        if (backToTop) {
            backToTop.classList.toggle('visible', window.scrollY > 320);
        }
        if (hero) {
            hero.style.transform = 'translateY(' + window.scrollY * 0.08 + 'px)';
        }
    });

    backToTop?.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Reveal animations
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
            }
        });
    }, { threshold: 0.15 });

    revealItems.forEach(function(item) {
        observer.observe(item);
    });

    // Lightbox
    document.querySelectorAll('[data-lightbox-trigger]').forEach(function(trigger) {
        trigger.addEventListener('click', function() {
            if (!lightbox || !lightboxContent) return;
            lightboxContent.textContent = trigger.getAttribute('data-lightbox-trigger') || '';
            lightbox.classList.add('open');
        });
    });

    lightbox?.addEventListener('click', function(event) {
        if (event.target === lightbox) {
            lightbox.classList.remove('open');
        }
    });

    // Form validation feedback
    const forms = document.querySelectorAll('form');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const required = this.querySelectorAll('[required]');
            let valid = true;
            required.forEach(function(field) {
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

    // Filter form auto-submit
    const filterForm = document.querySelector('.filter-form');
    if (filterForm) {
        const selects = filterForm.querySelectorAll('select');
        selects.forEach(function(select) {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
    }

    // Gallery live filtering
    const galleryGrid = document.getElementById('galleryGrid');
    const gallerySearch = document.getElementById('gallerySearch');
    const brandCheckboxes = document.querySelectorAll('[data-filter="brand"]');
    const typeRadios = document.querySelectorAll('[data-filter="type"]');

    function filterGallery() {
        if (!galleryGrid) return;
        const q = gallerySearch ? gallerySearch.value.trim().toLowerCase() : '';
        const selectedBrands = [];
        brandCheckboxes.forEach(function(cb) {
            if (cb.checked) selectedBrands.push(cb.value);
        });
        let selectedType = null;
        typeRadios.forEach(function(r) {
            if (r.checked) selectedType = r.value;
        });

        const items = galleryGrid.querySelectorAll('.card');
        let visibleCount = 0;
        items.forEach(function(item) {
            const brand = item.dataset.brand;
            const type = item.dataset.type;
            const name = (item.dataset.name || '').toLowerCase();

            const matchBrand = selectedBrands.length === 0 || selectedBrands.includes(brand);
            const matchType = !selectedType || type === selectedType;
            const matchSearch = !q || name.includes(q);

            if (matchBrand && matchType && matchSearch) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        const empty = galleryGrid.querySelector('.empty-state');
        if (empty) {
            empty.style.display = visibleCount === 0 ? '' : 'none';
        }
    }

    if (galleryGrid) {
        brandCheckboxes.forEach(function(cb) { cb.addEventListener('change', filterGallery); });
        typeRadios.forEach(function(r) { r.addEventListener('change', filterGallery); });
        if (gallerySearch) gallerySearch.addEventListener('input', filterGallery);
        filterGallery();
    }

    // Report tablet brand filtering
    const reportBrandFilter = document.getElementById('reportBrandFilter');
    const reportTabletSelect = document.getElementById('reportTabletSelect');

    function filterReportTablets() {
        if (!reportBrandFilter || !reportTabletSelect) return;

        const brandId = reportBrandFilter.value;

        Array.from(reportTabletSelect.options).forEach(function(option) {
            if (!option.value) {
                option.hidden = false;
                return;
            }

            option.hidden = !!brandId && option.dataset.brand !== brandId;
        });

        const selectedOption = reportTabletSelect.options[reportTabletSelect.selectedIndex];
        if (selectedOption && selectedOption.hidden) {
            reportTabletSelect.value = '';
        }
    }

    if (reportBrandFilter && reportTabletSelect) {
        reportBrandFilter.addEventListener('change', filterReportTablets);
        filterReportTablets();
    }

    // Survey step navigation
    const surveyForm = document.getElementById('surveyForm');
    if (surveyForm) {
        const steps = surveyForm.querySelectorAll('.step');
        const nextBtns = surveyForm.querySelectorAll('.next-btn');
        const prevBtns = surveyForm.querySelectorAll('.prev-btn');
        const surveySearch = document.getElementById('surveySearch');

        function filterSurveyTablets() {
            const brandRadio = surveyForm.querySelector('input[name="brand_id"]:checked');
            const typeRadio = surveyForm.querySelector('input[name="has_display"]:checked');
            const brandId = brandRadio ? brandRadio.value : '';
            const typeVal = typeRadio ? typeRadio.value : '';

            const tabletList = document.getElementById('surveyTabletList');
            if (!tabletList) return;

            const items = tabletList.querySelectorAll('.survey-tablet-item');
            items.forEach(function(item) {
                const matchesBrand = !brandId || item.dataset.brand === brandId;
                const matchesType = !typeVal || item.dataset.type === (typeVal === '1' ? 'display' : 'graphics');
                item.style.display = matchesBrand && matchesType ? 'flex' : 'none';
            });

            const searchInput = document.getElementById('surveySearch');
            if (searchInput) {
                const q = searchInput.value.trim().toLowerCase();
                if (q) {
                    items.forEach(function(item) {
                        if (item.style.display !== 'none') {
                            const text = item.textContent.toLowerCase();
                            item.style.display = text.includes(q) ? 'flex' : 'none';
                        }
                    });
                }
            }
        }

        function showStep(num) {
            steps.forEach(function(s) { s.removeAttribute('aria-current'); });
            const target = surveyForm.querySelector('.step[data-step="' + num + '"]');
            if (target) target.setAttribute('aria-current', 'step');

            if (num === 3) filterSurveyTablets();

            const dots = surveyForm.querySelectorAll('.step-dot');
            dots.forEach(function(d, i) {
                d.classList.toggle('active', i + 1 === num);
            });
        }

        nextBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const curr = parseInt(surveyForm.querySelector('.step[aria-current]').dataset.step);
                showStep(curr + 1);
            });
        });

        const brandRadios = surveyForm.querySelectorAll('input[name="brand_id"]');
        brandRadios.forEach(function(r) { r.addEventListener('change', filterSurveyTablets); });
        const typeRadios = surveyForm.querySelectorAll('input[name="has_display"]');
        typeRadios.forEach(function(r) { r.addEventListener('change', filterSurveyTablets); });

        prevBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const curr = parseInt(surveyForm.querySelector('.step[aria-current]').dataset.step);
                showStep(curr - 1);
            });
        });

        showStep(1);

        if (surveySearch) {
            const tabletList = document.getElementById('surveyTabletList');
            surveySearch.addEventListener('input', function() {
                const q = this.value.trim().toLowerCase();
                const labels = tabletList.querySelectorAll('.survey-tablet-item');
                labels.forEach(function(lab) {
                    const text = lab.textContent.toLowerCase();
                    lab.style.display = text.includes(q) ? 'flex' : 'none';
                });
            });
        }

        surveyForm.addEventListener('submit', function(e) {
            let allFilled = true;
            steps.forEach(function(s) {
                const reqs = s.querySelectorAll('[required]');
                reqs.forEach(function(f) {
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
