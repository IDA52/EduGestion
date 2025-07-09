/**
 * JavaScript principal pour EduGestion
 * Version: 1.0
 */

// Classe principale de l'application
class EduGestion {
    constructor() {
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.initializeComponents();
        this.setupAjaxDefaults();
    }
    
    setupEventListeners() {
        // Gestion des formulaires
        document.addEventListener('submit', this.handleFormSubmit.bind(this));
        
        // Gestion des confirmations
        document.addEventListener('click', this.handleConfirmations.bind(this));
        
        // Gestion des tooltips
        this.initializeTooltips();
        
        // Gestion des modals
        this.initializeModals();
    }
    
    initializeComponents() {
        // Initialisation des composants Bootstrap
        this.initializeBootstrapComponents();
        
        // Initialisation des tableaux de données
        this.initializeDataTables();
        
        // Initialisation des graphiques
        this.initializeCharts();
    }
    
    setupAjaxDefaults() {
        // Configuration par défaut pour les requêtes AJAX
        if (typeof fetch !== 'undefined') {
            this.setupFetchDefaults();
        }
    }
    
    setupFetchDefaults() {
        // Intercepteur pour ajouter automatiquement le token CSRF
        const originalFetch = window.fetch;
        window.fetch = (url, options = {}) => {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            if (token && options.method && options.method !== 'GET') {
                options.headers = {
                    ...options.headers,
                    'X-CSRF-TOKEN': token
                };
            }
            
            return originalFetch(url, options);
        };
    }
    
    handleFormSubmit(event) {
        const form = event.target;
        const formId = form.id;
        
        // Validation automatique des formulaires
        if (!this.validateForm(form)) {
            event.preventDefault();
            return false;
        }
        
        // Gestion des formulaires AJAX
        if (form.hasAttribute('data-ajax')) {
            event.preventDefault();
            this.handleAjaxForm(form);
        }
    }
    
    validateForm(form) {
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                this.showFieldError(input, 'Ce champ est requis');
                isValid = false;
            } else {
                this.clearFieldError(input);
            }
        });
        
        // Validation des emails
        const emailInputs = form.querySelectorAll('input[type="email"]');
        emailInputs.forEach(input => {
            if (input.value && !this.isValidEmail(input.value)) {
                this.showFieldError(input, 'Format d\'email invalide');
                isValid = false;
            }
        });
        
        return isValid;
    }
    
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    showFieldError(input, message) {
        input.classList.add('is-invalid');
        
        let errorDiv = input.parentNode.querySelector('.invalid-feedback');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            input.parentNode.appendChild(errorDiv);
        }
        errorDiv.textContent = message;
    }
    
    clearFieldError(input) {
        input.classList.remove('is-invalid');
        const errorDiv = input.parentNode.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.remove();
        }
    }
    
    handleAjaxForm(form) {
        const formData = new FormData(form);
        const url = form.action || window.location.href;
        const method = form.method || 'POST';
        
        // Afficher le spinner de chargement
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Envoi...';
        submitBtn.disabled = true;
        
        fetch(url, {
            method: method,
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification(data.message || 'Opération réussie', 'success');
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                }
            } else {
                this.showNotification(data.message || 'Erreur lors de l\'opération', 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur AJAX:', error);
            this.showNotification('Erreur de connexion', 'danger');
        })
        .finally(() => {
            // Restaurer le bouton
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    }
    
    handleConfirmations(event) {
        const target = event.target;
        
        if (target.hasAttribute('data-confirm')) {
            const message = target.getAttribute('data-confirm');
            if (!confirm(message)) {
                event.preventDefault();
                return false;
            }
        }
    }
    
    initializeTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    initializeModals() {
        const modalTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="modal"]'));
        modalTriggerList.map(function (modalTriggerEl) {
            return new bootstrap.Modal(modalTriggerEl);
        });
    }
    
    initializeBootstrapComponents() {
        // Initialisation des dropdowns
        const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
        dropdownElementList.map(function (dropdownToggleEl) {
            return new bootstrap.Dropdown(dropdownToggleEl);
        });
        
        // Initialisation des popovers
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    }
    
    initializeDataTables() {
        // Initialisation des tableaux avec tri et recherche
        const tables = document.querySelectorAll('.table-sortable');
        tables.forEach(table => {
            this.makeTableSortable(table);
        });
    }
    
    makeTableSortable(table) {
        const headers = table.querySelectorAll('th[data-sort]');
        headers.forEach(header => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', () => {
                this.sortTable(table, header);
            });
        });
    }
    
    sortTable(table, header) {
        const column = header.getAttribute('data-sort');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const isAscending = header.classList.contains('sort-asc');
        
        // Trier les lignes
        rows.sort((a, b) => {
            const aValue = a.querySelector(`td[data-${column}]`).getAttribute(`data-${column}`);
            const bValue = b.querySelector(`td[data-${column}]`).getAttribute(`data-${column}`);
            
            if (isAscending) {
                return aValue > bValue ? -1 : 1;
            } else {
                return aValue < bValue ? -1 : 1;
            }
        });
        
        // Réorganiser les lignes
        rows.forEach(row => tbody.appendChild(row));
        
        // Mettre à jour les indicateurs de tri
        table.querySelectorAll('th').forEach(th => {
            th.classList.remove('sort-asc', 'sort-desc');
        });
        header.classList.add(isAscending ? 'sort-desc' : 'sort-asc');
    }
    
    initializeCharts() {
        // Initialisation des graphiques Chart.js
        const chartElements = document.querySelectorAll('[data-chart]');
        chartElements.forEach(element => {
            const chartType = element.getAttribute('data-chart');
            const chartData = JSON.parse(element.getAttribute('data-chart-data') || '{}');
            
            if (typeof Chart !== 'undefined') {
                new Chart(element, {
                    type: chartType,
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }
        });
    }
    
    showNotification(message, type = 'info', duration = 5000) {
        // Créer l'élément de notification
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Ajouter au DOM
        document.body.appendChild(notification);
        
        // Auto-suppression après la durée spécifiée
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, duration);
    }
    
    // Méthodes utilitaires
    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('fr-FR', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }
    
    formatNumber(number, decimals = 2) {
        return parseFloat(number).toFixed(decimals);
    }
    
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}

// Initialisation de l'application quand le DOM est chargé
document.addEventListener('DOMContentLoaded', () => {
    window.eduGestion = new EduGestion();
});

// Export pour utilisation dans d'autres modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = EduGestion;
} 