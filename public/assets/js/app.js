// Logistic Management System - Main JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Alpine.js components
    initAlpineComponents();
    
    // Initialize form validation
    initFormValidation();
    
    // Initialize interactive elements
    initInteractiveElements();
    
    // Initialize real-time updates (if needed)
    initRealTimeUpdates();
});

function initAlpineComponents() {
    // Global Alpine store
    window.LogisticStore = {
        // State
        notifications: [],
        isLoading: false,
        user: null,
        
        // Actions
        addNotification(type, message) {
            this.notifications.push({
                id: Date.now(),
                type,
                message
            });
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                this.notifications.shift();
            }, 5000);
        },
        
        clearNotifications() {
            this.notifications = [];
        }
    };
}

function initFormValidation() {
    // Form validation with real-time feedback
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
        
        // Real-time validation
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                clearFieldError(this);
            });
        });
    });
}

function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
        if (!validateField(field)) {
            isValid = false;
        }
    });
    
    // Validate email format
    const emailFields = form.querySelectorAll('input[type="email"]');
    emailFields.forEach(field => {
        if (field.value && !isValidEmail(field.value)) {
            showFieldError(field, 'Please enter a valid email address');
            isValid = false;
        }
    });
    
    // Validate phone format
    const phoneFields = form.querySelectorAll('input[name*="phone"]');
    phoneFields.forEach(field => {
        if (field.value && !isValidPhone(field.value)) {
            showFieldError(field, 'Please enter a valid phone number');
            isValid = false;
        }
    });
    
    return isValid;
}

function validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    
    if (field.hasAttribute('required') && !value) {
        showFieldError(field, 'This field is required');
        isValid = false;
    }
    
    if (field.type === 'email' && value && !isValidEmail(value)) {
        showFieldError(field, 'Please enter a valid email address');
        isValid = false;
    }
    
    if (field.name === 'customer_phone' && value && !isValidPhone(value)) {
        showFieldError(field, 'Please enter a valid phone number');
        isValid = false;
    }
    
    if (field.type === 'number' && field.hasAttribute('min')) {
        const min = parseFloat(field.getAttribute('min'));
        if (parseFloat(value) < min) {
            showFieldError(field, `Value must be at least ${min}`);
            isValid = false;
        }
    }
    
    if (field.type === 'date') {
        const selectedDate = new Date(value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            showFieldError(field, 'Date cannot be in the past');
            isValid = false;
        }
    }
    
    return isValid;
}

function showFieldError(field, message) {
    clearFieldError(field);
    
    field.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
    
    const errorDiv = document.createElement('p');
    errorDiv.className = 'mt-1 text-sm text-red-600';
    errorDiv.textContent = message;
    errorDiv.id = `${field.id || field.name}-error`;
    
    field.parentNode.appendChild(errorDiv);
}

function clearFieldError(field) {
    field.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
    
    const errorDiv = field.parentNode.querySelector(`#${field.id || field.name}-error`);
    if (errorDiv) {
        errorDiv.remove();
    }
}

function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function isValidPhone(phone) {
    // Indonesian phone number validation
    const re = /^(\+62|62|0)8[1-9][0-9]{6,9}$/;
    return re.test(phone.replace(/\s+/g, ''));
}

function initInteractiveElements() {
    // Tab functionality
    const tabButtons = document.querySelectorAll('[data-tab]');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            switchTab(tabId);
        });
    });
    
    // Modal functionality
    const modalTriggers = document.querySelectorAll('[data-modal]');
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal');
            openModal(modalId);
        });
    });
    
    // Close modal buttons
    const closeButtons = document.querySelectorAll('[data-close-modal]');
    closeButtons.forEach(button => {
        button.addEventListener('click', closeAllModals);
    });
    
    // Close modal on outside click
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-overlay')) {
            closeAllModals();
        }
    });
    
    // Toggle password visibility
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const input = document.querySelector(this.getAttribute('data-target'));
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            
            // Toggle icon
            const icon = this.querySelector('i');
            if (type === 'text') {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
    
    // Copy to clipboard
    const copyButtons = document.querySelectorAll('[data-copy]');
    copyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const text = this.getAttribute('data-copy');
            copyToClipboard(text);
            
            // Show feedback
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-check mr-2"></i>Copied!';
            this.classList.add('bg-green-500');
            
            setTimeout(() => {
                this.innerHTML = originalText;
                this.classList.remove('bg-green-500');
            }, 2000);
        });
    });
    
    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert-auto-hide');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
}

function switchTab(tabId) {
    // Hide all tab content
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Show selected tab content
    const selectedContent = document.getElementById(`tab-${tabId}`);
    if (selectedContent) {
        selectedContent.classList.remove('hidden');
    }
    
    // Update active tab button
    document.querySelectorAll('[data-tab]').forEach(button => {
        if (button.getAttribute('data-tab') === tabId) {
            button.classList.add('active');
        } else {
            button.classList.remove('active');
        }
    });
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
}

function closeAllModals() {
    document.querySelectorAll('.modal').forEach(modal => {
        modal.classList.add('hidden');
    });
    document.body.classList.remove('overflow-hidden');
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).catch(err => {
        console.error('Failed to copy text: ', err);
    });
}

function initRealTimeUpdates() {
    // Check for new notifications
    if (window.LogisticStore.user) {
        setInterval(() => {
            fetch('/api/notifications/unread', {
                headers: {
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.notifications.length > 0) {
                    data.notifications.forEach(notification => {
                        window.LogisticStore.addNotification(notification.type, notification.message);
                    });
                }
            });
        }, 30000); // Check every 30 seconds
    }
    
    // Auto-refresh order status
    const orderTrackingElements = document.querySelectorAll('[data-order-tracking]');
    if (orderTrackingElements.length > 0) {
        setInterval(() => {
            orderTrackingElements.forEach(element => {
                const orderId = element.getAttribute('data-order-tracking');
                fetch(`/api/orders/${orderId}/status`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            element.textContent = data.status;
                            
                            // Update status badge class
                            const badge = element.closest('.status-badge');
                            if (badge) {
                                badge.className = `status-badge ${data.status}`;
                            }
                        }
                    });
            });
        }, 60000); // Update every minute
    }
}

// API helper functions
class API {
    static async request(endpoint, options = {}) {
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        };
        
        const response = await fetch(endpoint, { ...defaultOptions, ...options });
        return response.json();
    }
    
    static async getOrders(filters = {}) {
        const queryString = new URLSearchParams(filters).toString();
        return this.request(`/api/orders?${queryString}`);
    }
    
    static async createOrder(orderData) {
        return this.request('/api/orders', {
            method: 'POST',
            body: JSON.stringify(orderData)
        });
    }
    
    static async updateOrder(id, orderData) {
        return this.request(`/api/orders/${id}`, {
            method: 'PUT',
            body: JSON.stringify(orderData)
        });
    }
    
    static async deleteOrder(id) {
        return this.request(`/api/orders/${id}`, {
            method: 'DELETE'
        });
    }
}

// Export for global use
window.API = API;

// Currency formatting
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(amount);
}

// Date formatting
function formatDate(dateString, format = 'short') {
    const date = new Date(dateString);
    
    if (format === 'short') {
        return date.toLocaleDateString('id-ID');
    } else if (format === 'long') {
        return date.toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    } else if (format === 'datetime') {
        return date.toLocaleString('id-ID');
    }
    
    return date.toISOString().split('T')[0];
}

// Debounce function for search inputs
function debounce(func, wait) {
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

// Search functionality
const searchInput = document.querySelector('[data-search]');
if (searchInput) {
    const searchResults = document.querySelector('[data-search-results]');
    const debouncedSearch = debounce(async (query) => {
        const response = await API.getOrders({ search: query });
        if (searchResults && response.success) {
            searchResults.innerHTML = '';
            response.data.forEach(order => {
                const item = document.createElement('div');
                item.className = 'search-result-item';
                item.innerHTML = `
                    <div>${order.tracking_number} - ${order.customer_name}</div>
                    <div class="text-sm text-gray-500">${order.status}</div>
                `;
                item.addEventListener('click', () => {
                    window.location.href = `/orders/edit/${order.id}`;
                });
                searchResults.appendChild(item);
            });
        }
    }, 300);
    
    searchInput.addEventListener('input', (e) => {
        debouncedSearch(e.target.value);
    });
}