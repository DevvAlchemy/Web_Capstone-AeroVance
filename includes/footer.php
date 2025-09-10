<?php
// includes/footer.php
?>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="/helicopters">Browse Helicopters</a></li>
                    <li><a href="/category/personal">Personal Aircraft</a></li>
                    <li><a href="/category/business">Business Solutions</a></li>
                    <li><a href="/category/emergency">Emergency Services</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Support</h4>
                <ul>
                    <li><a href="/contact">Contact Us</a></li>
                    <li><a href="/support">Customer Support</a></li>
                    <li><a href="/financing">Financing Options</a></li>
                    <li><a href="/maintenance">Maintenance Services</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Company</h4>
                <ul>
                    <li><a href="/about">About Us</a></li>
                    <li><a href="/careers">Careers</a></li>
                    <li><a href="/news">News & Updates</a></li>
                    <li><a href="/investors">Investors</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Connect With Us</h4>
                <p>Follow us for the latest updates and aviation news</p>
                <div class="social-links">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                </div>
                
                <div class="newsletter-signup">
                    <h5>Newsletter</h5>
                    <form id="newsletter-form" onsubmit="subscribeNewsletter(event)">
                        <div class="newsletter-input">
                            <input type="email" placeholder="Enter your email" required>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                    <p class="newsletter-note">Get updates on new listings and aviation news</p>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> AEROVANCE. All rights reserved. | 
               <a href="/privacy">Privacy Policy</a> | 
               <a href="/terms">Terms of Service</a> |
               <a href="/sitemap">Sitemap</a>
            </p>
        </div>
    </div>
</footer>

<!-- Scroll to Top Button -->
<button class="scroll-to-top" id="scrollToTop" onclick="scrollToTop()">
    <i class="fas fa-chevron-up"></i>
</button>

<!-- Notification Container -->
<div class="notification-container" id="notificationContainer"></div>

<style>
/* Newsletter Signup/ remove css later */
.newsletter-signup {
    margin-top: 20px;
}

.newsletter-signup h5 {
    color: #FF6B35;
    margin-bottom: 10px;
    font-size: 1rem;
}

.newsletter-input {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
}

.newsletter-input input {
    flex: 1;
    padding: 10px;
    border: 1px solid #555555;
    border-radius: 6px;
    background: #2a2a2a;
    color: #ffffff;
}

.newsletter-input button {
    padding: 10px 15px;
    border-radius: 6px;
}

.newsletter-note {
    font-size: 0.8rem;
    color: #aaaaaa;
    margin: 0;
}

/* Scroll to Top Button */
.scroll-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #FF6B35, #FF8C42);
    border: none;
    color: white;
    font-size: 1.2rem;
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    z-index: 1000;
    box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4);
}

.scroll-to-top.show {
    opacity: 1;
    visibility: visible;
}

.scroll-to-top:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(255, 107, 53, 0.5);
}

/* Notification Container */
.notification-container {
    position: fixed;
    top: 100px;
    right: 20px;
    z-index: 10000;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.notification {
    background: #333333;
    border: 1px solid rgba(255, 107, 53, 0.3);
    border-radius: 8px;
    padding: 15px 20px;
    min-width: 300px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    transform: translateX(400px);
    transition: transform 0.3s ease;
}

.notification.show {
    transform: translateX(0);
}

.notification-content {
    display: flex;
    align-items: center;
    gap: 15px;
    color: #ffffff;
}

.notification button {
    background: none;
    border: none;
    color: #FF6B35;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 0;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Mobile Menu Overlay */
.mobile-menu-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    z-index: 9999;
}
</style>

<script>
// Scroll to top button functionality
window.addEventListener('scroll', () => {
    const scrollToTopBtn = document.getElementById('scrollToTop');
    if (window.scrollY > 300) {
        scrollToTopBtn.classList.add('show');
    } else {
        scrollToTopBtn.classList.remove('show');
    }
});

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Newsletter subscription
function subscribeNewsletter(event) {
    event.preventDefault();
    
    const form = event.target;
    const email = form.querySelector('input[type="email"]').value;
    
    fetch('/api/newsletter/subscribe', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email: email })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Successfully subscribed to newsletter!', 'success');
            form.reset();
        } else {
            showNotification(data.message || 'Error subscribing to newsletter', 'error');
        }
    })
    .catch(error => {
        showNotification('Error subscribing to newsletter', 'error');
    });
}

// Global notification function
function showNotification(message, type = 'info') {
    const container = document.getElementById('notificationContainer');
    const notification = document.createElement('div');
    notification.className = 'notification';
    
    let icon = 'info-circle';
    let color = '#17a2b8';
    
    switch(type) {
        case 'success':
            icon = 'check-circle';
            color = '#28a745';
            break;
        case 'error':
            icon = 'exclamation-circle';
            color = '#dc3545';
            break;
        case 'warning':
            icon = 'exclamation-triangle';
            color = '#ffc107';
            break;
    }
    
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${icon}" style="color: ${color};"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    container.appendChild(notification);
    
    // Trigger animation
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Format price helper (global)
function formatPrice(price) {
    return '$' + parseInt(price).toLocaleString();
}
</script>