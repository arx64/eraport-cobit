/**
 * Main JavaScript
 * Sistem Analisis Risiko TI e-Raport - COBIT 2019
 */

document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle (mobile)
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarClose = document.getElementById('sidebarClose');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            if (sidebarOverlay) {
                sidebarOverlay.classList.toggle('show');
            }
        });
    }
    
    if (sidebarClose && sidebar) {
        sidebarClose.addEventListener('click', function() {
            sidebar.classList.remove('show');
            if (sidebarOverlay) {
                sidebarOverlay.classList.remove('show');
            }
        });
    }
    
    if (sidebarOverlay && sidebar) {
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });
    }
    
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) {
                bsAlert.close();
            }
        }, 5000);
    });
    
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
    });
    
    // Remove invalid class on input
    const inputs = document.querySelectorAll('.form-control, .form-select');
    inputs.forEach(function(input) {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });
    
    // Confirm delete actions
    const deleteLinks = document.querySelectorAll('a[onclick*="confirm"]');
    deleteLinks.forEach(function(link) {
        link.addEventListener('click', function(event) {
            if (!confirm('Apakah Anda yakin?')) {
                event.preventDefault();
            }
        });
    });
    
    // Rating option selection effect
    const ratingOptions = document.querySelectorAll('.rating-option input');
    ratingOptions.forEach(function(radio) {
        radio.addEventListener('change', function() {
            // Remove selected class from siblings
            const name = this.getAttribute('name');
            document.querySelectorAll('input[name="' + name + '"]').forEach(function(r) {
                r.closest('.rating-option').classList.remove('selected');
            });
            // Add selected class to current
            this.closest('.rating-option').classList.add('selected');
        });
    });
    
    // Initialize tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggerList.forEach(function(tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId !== '#') {
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    e.preventDefault();
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
    
    // Table row hover effect enhancement
    const tableRows = document.querySelectorAll('.table tbody tr');
    tableRows.forEach(function(row) {
        row.addEventListener('mouseenter', function() {
            this.style.transition = 'background-color 0.2s';
        });
    });
    
    // Print button handler
    const printButtons = document.querySelectorAll('[data-action="print"]');
    printButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            window.print();
        });
    });
    
    // Progress bar animation
    const progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(function(bar) {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(function() {
            bar.style.transition = 'width 1s ease';
            bar.style.width = width;
        }, 200);
    });
});

/**
 * Format number to Indonesian format
 * @param {number} num Number to format
 * @returns {string} Formatted number
 */
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

/**
 * Confirm action with custom message
 * @param {string} message Confirmation message
 * @returns {boolean} User confirmation
 */
function confirmAction(message) {
    return confirm(message || 'Apakah Anda yakin?');
}

/**
 * Show loading state on button
 * @param {HTMLElement} btn Button element
 * @param {string} text Loading text
 */
function showLoading(btn, text) {
    btn.dataset.originalText = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>' + (text || 'Memuat...');
    btn.disabled = true;
}

/**
 * Hide loading state on button
 * @param {HTMLElement} btn Button element
 */
function hideLoading(btn) {
    btn.innerHTML = btn.dataset.originalText;
    btn.disabled = false;
}
