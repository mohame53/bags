// Theme switcher (TODO)
function switchTheme(theme) {
    document.body.className = 'theme-' + theme;
    localStorage.setItem('theme', theme);
}

// Load saved theme
document.addEventListener('DOMContentLoaded', () => {
    // Get the current theme from the body class
    const currentTheme = document.body.className.replace('theme-', '');
    localStorage.setItem('theme', currentTheme);
});

// Shopping cart functionality
function updateCart(action, productId, quantity = 1) {
    fetch('update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=${action}&product_id=${productId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount(data.cart_count);
            showNotification('Cart updated successfully!', 'success');
        } else {
            showNotification(data.message || 'Error updating cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error updating cart', 'error');
    });
}

// Add to cart button click handler
document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', (e) => {
        e.preventDefault();
        const productId = button.getAttribute('data-product-id');
        updateCart('add', productId);
    });
});

// Update quantity buttons
document.querySelectorAll('.update-quantity').forEach(button => {
    button.addEventListener('click', (e) => {
        e.preventDefault();
        const productId = button.getAttribute('data-product-id');
        const action = button.getAttribute('data-action');
        const quantity = parseInt(button.getAttribute('data-quantity'));
        updateCart(action, productId, quantity);
    });
});

// Remove item button
document.querySelectorAll('.remove-item').forEach(button => {
    button.addEventListener('click', (e) => {
        e.preventDefault();
        const productId = button.getAttribute('data-product-id');
        updateCart('remove', productId);
    });
});

// Update cart count in navbar
function updateCartCount(count) {
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = count;
    }
}

// Show notification
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
    notification.style.zIndex = '1000';
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Toggle mobile menu
function toggleMobileMenu() {
    const menu = document.querySelector('.mobile-menu');
    if (menu) {
        menu.classList.toggle('show');
    }
}

// Initialize tooltips
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    updateCartCount();
    initTooltips();
});

// Add smooth scrolling to anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
}); 