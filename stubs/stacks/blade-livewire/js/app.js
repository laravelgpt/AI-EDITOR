import './bootstrap';

// Alpine.js components
document.addEventListener('alpine:init', () => {
    Alpine.data('counter', () => ({
        count: 0,
        increment() {
            this.count++;
        },
        decrement() {
            this.count--;
        }
    }));

    Alpine.data('modal', () => ({
        open: false,
        toggle() {
            this.open = !this.open;
        },
        close() {
            this.open = false;
        }
    }));

    Alpine.data('dropdown', () => ({
        open: false,
        toggle() {
            this.open = !this.open;
        },
        close() {
            this.open = false;
        }
    }));
});

// Global utilities
window.utils = {
    // Format date
    formatDate(date) {
        return new Date(date).toLocaleDateString();
    },

    // Format currency
    formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    },

    // Show notification
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            type === 'warning' ? 'bg-yellow-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;

        document.body.appendChild(notification);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    },

    // Copy to clipboard
    async copyToClipboard(text) {
        try {
            await navigator.clipboard.writeText(text);
            this.showNotification('Copied to clipboard!', 'success');
        } catch (err) {
            this.showNotification('Failed to copy to clipboard', 'error');
        }
    }
};

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    // Initialize any tooltips or other UI components
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(tooltip => {
        tooltip.addEventListener('mouseenter', function() {
            // Tooltip logic here
        });
    });
});