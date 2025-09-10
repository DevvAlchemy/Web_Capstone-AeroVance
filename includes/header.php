<?php
// includes/header.php

// Get cart count if user is logged in
$cartCount = 0;
if (isLoggedIn()) {
    try {
        require_once dirname(__DIR__) . '/config/database.php';
        require_once dirname(__DIR__) . '/models/Cart.php';
        
        $database = new Database();
        $db = $database->connect();
        $cart = new Cart($db);
        
        $cartCount = $cart->getCartCount($_SESSION['user']['id']);
    } catch (Exception $e) {
        $cartCount = 0;
    }
}

// Get current page for active menu highlighting
$currentPage = basename($_SERVER['REQUEST_URI']);
?>

<!-- Header -->
<header class="header" id="header">
    <div class="nav-container">
        <a href="/" class="logo">
            <i class="fas fa-helicopter"></i>
            <span>AEROVANCE</span>
        </a>
        
        <nav class="nav-menu">
            <a href="/" class="<?= $currentPage == '' || $currentPage == 'index.php' ? 'active' : '' ?>">Home</a>
            <a href="/helicopters" class="<?= $currentPage == 'helicopters' ? 'active' : '' ?>">Helicopters</a>
            <a href="/category/personal" class="<?= strpos($currentPage, 'personal') !== false ? 'active' : '' ?>">Personal</a>
            <a href="/category/business" class="<?= strpos($currentPage, 'business') !== false ? 'active' : '' ?>">Business</a>
            <a href="/category/emergency" class="<?= strpos($currentPage, 'emergency') !== false ? 'active' : '' ?>">Emergency</a>
            <a href="/about" class="<?= $currentPage == 'about' ? 'active' : '' ?>">About</a>
            <a href="/contact" class="<?= $currentPage == 'contact' ? 'active' : '' ?>">Contact</a>
        </nav>
        
        <div class="auth-buttons">
            <?php if (isLoggedIn()): ?>
                <a href="/cart" class="btn btn-outline">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count"><?= $cartCount ?></span>
                </a>
                <div class="user-dropdown">
                    <button class="btn btn-primary" onclick="toggleUserMenu()">
                        <i class="fas fa-user"></i>
                        <?= htmlspecialchars($_SESSION['user']['name'] ?? 'My Account') ?>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu" id="user-menu">
                        <a href="/account"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        <a href="/account/orders"><i class="fas fa-shopping-bag"></i> My Orders</a>
                        <a href="/account/wishlist"><i class="fas fa-heart"></i> Wishlist</a>
                        <a href="/account/listings"><i class="fas fa-helicopter"></i> My Listings</a>
                        <div class="dropdown-divider"></div>
                        <a href="/account/settings"><i class="fas fa-cog"></i> Settings</a>
                        <a href="/logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="/login" class="btn btn-outline">Login</a>
                <a href="/register" class="btn btn-primary">Sign Up</a>
            <?php endif; ?>
        </div>
        
        <!-- Mobile Menu Toggle -->
        <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</header>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobile-menu">
    <div class="mobile-menu-header">
        <a href="/" class="logo">
            <i class="fas fa-helicopter"></i>
            <span>AEROVANCE</span>
        </a>
        <button class="mobile-menu-close" onclick="toggleMobileMenu()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <nav class="mobile-nav">
        <a href="/" class="<?= $currentPage == '' ? 'active' : '' ?>">
            <i class="fas fa-home"></i> Home
        </a>
        <a href="/helicopters" class="<?= $currentPage == 'helicopters' ? 'active' : '' ?>">
            <i class="fas fa-helicopter"></i> Helicopters
        </a>
        <a href="/category/personal">
            <i class="fas fa-user"></i> Personal
        </a>
        <a href="/category/business">
            <i class="fas fa-building"></i> Business
        </a>
        <a href="/category/emergency">
            <i class="fas fa-first-aid"></i> Emergency
        </a>
        <a href="/about">
            <i class="fas fa-info-circle"></i> About
        </a>
        <a href="/contact">
            <i class="fas fa-envelope"></i> Contact
        </a>
        
        <?php if (isLoggedIn()): ?>
            <div class="mobile-menu-divider"></div>
            <a href="/cart">
                <i class="fas fa-shopping-cart"></i> Cart (<?= $cartCount ?>)
            </a>
            <a href="/account">
                <i class="fas fa-user"></i> My Account
            </a>
            <a href="/logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        <?php else: ?>
            <div class="mobile-menu-divider"></div>
            <a href="/login" class="btn btn-outline btn-full">Login</a>
            <a href="/register" class="btn btn-primary btn-full">Sign Up</a>
        <?php endif; ?>
    </nav>
</div>

<style>
/* Header Styles /remove/ will put in own file after testing  */
.user-dropdown {
    position: relative;
}

.user-dropdown .btn {
    display: flex;
    align-items: center;
    gap: 8px;
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background: #333333;
    border: 1px solid rgba(255, 107, 53, 0.3);
    border-radius: 8px;
    min-width: 200px;
    margin-top: 10px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    z-index: 1000;
}

.dropdown-menu.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-menu a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 20px;
    color: #cccccc;
    text-decoration: none;
    transition: all 0.3s ease;
}

.dropdown-menu a:hover {
    background: rgba(255, 107, 53, 0.1);
    color: #FF6B35;
}

.dropdown-divider {
    height: 1px;
    background: #444444;
    margin: 8px 0;
}

/* Mobile Menu Styles */
.mobile-menu-toggle {
    display: none;
    background: none;
    border: none;
    color: #FF6B35;
    font-size: 1.5rem;
    cursor: pointer;
}

.mobile-menu {
    position: fixed;
    top: 0;
    left: -100%;
    width: 80%;
    max-width: 300px;
    height: 100vh;
    background: #2a2a2a;
    z-index: 10000;
    transition: left 0.3s ease;
    overflow-y: auto;
}

.mobile-menu.active {
    left: 0;
}

.mobile-menu-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #444444;
}

.mobile-menu-close {
    background: none;
    border: none;
    color: #cccccc;
    font-size: 1.5rem;
    cursor: pointer;
}

.mobile-nav {
    padding: 20px;
}

.mobile-nav a {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    color: #cccccc;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.mobile-nav a:hover,
.mobile-nav a.active {
    background: rgba(255, 107, 53, 0.1);
    color: #FF6B35;
}

.mobile-menu-divider {
    height: 1px;
    background: #444444;
    margin: 20px 0;
}

/* Cart count badge */
.cart-count {
    background: #FF6B35;
    color: white;
    border-radius: 10px;
    padding: 2px 6px;
    font-size: 0.8rem;
    margin-left: 5px;
}

/* Responsive */
@media (max-width: 991px) {
    .nav-menu,
    .auth-buttons {
        display: none;
    }
    
    .mobile-menu-toggle {
        display: block;
    }
}
</style>

<script>
// Toggle user dropdown menu
function toggleUserMenu() {
    const menu = document.getElementById('user-menu');
    menu.classList.toggle('show');
    
    // Close menu when clicking outside
    document.addEventListener('click', function closeMenu(e) {
        if (!e.target.closest('.user-dropdown')) {
            menu.classList.remove('show');
            document.removeEventListener('click', closeMenu);
        }
    });
}

// Toggle mobile menu
function toggleMobileMenu() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('active');
    
    // Add overlay
    if (menu.classList.contains('active')) {
        const overlay = document.createElement('div');
        overlay.className = 'mobile-menu-overlay';
        overlay.onclick = toggleMobileMenu;
        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';
    } else {
        const overlay = document.querySelector('.mobile-menu-overlay');
        if (overlay) overlay.remove();
        document.body.style.overflow = '';
    }
}

// Header scroll effect
window.addEventListener('scroll', () => {
    const header = document.getElementById('header');
    if (window.scrollY > 100) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }
});
</script>

<a href="/account/profile">Profile</a>
<a href="/dashboard">Dashboard</a>
<a href="/account/order/123">Order #123</a>
