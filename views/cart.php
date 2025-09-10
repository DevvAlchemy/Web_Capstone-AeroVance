<?php
// views/cart.php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Cart.php';
require_once '../models/Helicopter.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: /login?redirect=/cart');
    exit;
}

try {
    $database = new Database();
    $db = $database->connect();
    
    $cart = new Cart($db);
    $helicopter = new Helicopter($db);
    
    // Get cart items for the user
    $userId = $_SESSION['user']['id'];
    $cartItems = $cart->getCartItems($userId);
    
    // Calculate totals
    $subtotal = 0;
    $documentationFee = 2500;
    $inspectionFee = 5000;
    
    foreach ($cartItems as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    
    $total = $subtotal + $documentationFee + $inspectionFee;
    
} catch (Exception $e) {
    error_log('Cart error: ' . $e->getMessage());
    $cartItems = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - AeroVance</title>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="/helicopter-marketplace/assets/css/app.css">
    
    <style>
        /* Shopping Cart Specific Styles */
        .cart-page {
            margin-top: 100px;
            padding: 40px 0;
            min-height: 80vh;
        }
        
        .cart-header {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .cart-header h1 {
            font-size: 2.5rem;
            color: #FF6B35;
            margin-bottom: 10px;
        }
        
        .cart-header p {
            color: #cccccc;
            font-size: 1.1rem;
        }
        
        /* Cart Layout */
        .cart-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
            align-items: start;
        }
        
        /* Cart Items Section */
        .cart-items {
            background: #333333;
            border-radius: 15px;
            padding: 30px;
            border: 1px solid rgba(255, 107, 53, 0.2);
        }
        
        .cart-items-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #444444;
        }
        
        .cart-items-header h2 {
            color: #ffffff;
            font-size: 1.5rem;
            margin: 0;
        }
        
        .items-count {
            color: #FF6B35;
            font-weight: 600;
        }
        
        /* Cart Item */
        .cart-item {
            display: grid;
            grid-template-columns: 150px 1fr auto;
            gap: 20px;
            padding: 20px 0;
            border-bottom: 1px solid #444444;
            align-items: center;
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .item-image {
            height: 120px;
            background: #444444;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }
        
        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        /* Empty Cart State */
        .empty-cart {
            text-align: center;
            padding: 80px 20px;
            background: #333333;
            border-radius: 15px;
            border: 1px solid rgba(255, 107, 53, 0.2);
        }
        
        .empty-cart i {
            font-size: 5rem;
            color: #FF6B35;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .empty-cart h2 {
            color: #ffffff;
            margin-bottom: 15px;
        }
        
        .empty-cart p {
            color: #cccccc;
            margin-bottom: 30px;
        }
        
        /* Responsive */
        @media (max-width: 991px) {
            .cart-container {
                grid-template-columns: 1fr;
            }
            
            .cart-summary {
                position: static;
            }
        }
    </style>
</head>
<body>
    <!-- Include Header -->
    <?php include '../includes/header.php'; ?>

    <!-- Cart Page -->
    <section class="cart-page">
        <div class="container">
            <div class="cart-header">
                <h1><i class="fas fa-shopping-cart"></i> Shopping Cart</h1>
                <p>Review your selected aircraft before proceeding to checkout</p>
            </div>

            <?php if (!empty($cartItems)): ?>
            <!-- Cart with items -->
            <div class="cart-container" id="cart-with-items">
                <!-- Cart Items -->
                <div class="cart-items">
                    <div class="cart-items-header">
                        <h2>Your Aircraft</h2>
                        <span class="items-count"><?= count($cartItems) ?> item<?= count($cartItems) > 1 ? 's' : '' ?></span>
                    </div>

                    <?php foreach ($cartItems as $item): ?>
                    <!-- Cart Item -->
                    <div class="cart-item" data-item-id="<?= $item['cart_id'] ?>">
                        <div class="item-image">
                            <?php 
                            $images = json_decode($item['images'], true);
                            $mainImage = $images[0] ?? '/assets/images/helicopter-placeholder.jpg';
                            ?>
                            <img src="<?= $mainImage ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                        </div>
                        
                        <div class="item-details">
                            <h3><a href="/helicopter/<?= $item['helicopter_id'] ?>"><?= htmlspecialchars($item['name']) ?></a></h3>
                            <p class="item-specs">
                                <?= $item['year'] ?> Model • 
                                <?= ucfirst($item['category']) ?> Aviation • 
                                <?= $item['total_time'] ?? 'N/A' ?> Hours
                            </p>
                            <p class="item-price"><?= formatPrice($item['price']) ?></p>
                        </div>
                        
                        <div class="item-actions">
                            <div class="quantity-control">
                                <button class="quantity-btn" onclick="updateQuantity(<?= $item['cart_id'] ?>, -1)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="quantity-input" value="<?= $item['quantity'] ?>" min="1" readonly>
                                <button class="quantity-btn" onclick="updateQuantity(<?= $item['cart_id'] ?>, 1)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <button class="remove-btn" onclick="removeItem(<?= $item['cart_id'] ?>)">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <!-- Trust Badges -->
                    <div class="trust-badges">
                        <div class="trust-badge">
                            <i class="fas fa-shield-alt"></i>
                            <h4>Secure Purchase</h4>
                            <p>100% Protected</p>
                        </div>
                        <div class="trust-badge">
                            <i class="fas fa-certificate"></i>
                            <h4>Certified Aircraft</h4>
                            <p>All Documentation Verified</p>
                        </div>
                        <div class="trust-badge">
                            <i class="fas fa-handshake"></i>
                            <h4>Expert Support</h4>
                            <p>24/7 Assistance</p>
                        </div>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="cart-summary">
                    <div class="summary-card">
                        <h2>Order Summary</h2>
                        
                        <div class="summary-row">
                            <span>Subtotal (<?= count($cartItems) ?> item<?= count($cartItems) > 1 ? 's' : '' ?>)</span>
                            <span id="subtotal"><?= formatPrice($subtotal) ?></span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Documentation Fee</span>
                            <span><?= formatPrice($documentationFee) ?></span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Inspection Service</span>
                            <span><?= formatPrice($inspectionFee) ?></span>
                        </div>
                        
                        <div class="promo-section">
                            <label style="color: #cccccc; font-size: 0.9rem;">Promo Code</label>
                            <div class="promo-input">
                                <input type="text" id="promo-code" placeholder="Enter code">
                                <button onclick="applyPromo()">Apply</button>
                            </div>
                            <div id="promo-message" style="margin-top: 10px; font-size: 0.9rem;"></div>
                        </div>
                        
                        <div class="summary-row total">
                            <span>Total</span>
                            <span class="amount" id="total"><?= formatPrice($total) ?></span>
                        </div>
                        
                        <a href="/checkout" class="btn btn-primary btn-large checkout-btn">
                            <i class="fas fa-lock"></i> Proceed to Checkout
                        </a>
                        
                        <div class="continue-shopping">
                            <a href="/helicopters">
                                <i class="fas fa-arrow-left"></i> Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <!-- Empty Cart State -->
            <div class="empty-cart" id="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h2>Your cart is empty</h2>
                <p>Looks like you haven't added any aircraft to your cart yet.</p>
                <a href="/helicopters" class="btn btn-primary btn-large">
                    <i class="fas fa-helicopter"></i> Browse Aircraft
                </a>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Include Footer -->
    <?php include '../includes/footer.php'; ?>

    <script>
        // Update quantity
        function updateQuantity(cartId, change) {
            const input = event.target.closest('.quantity-control').querySelector('.quantity-input');
            let currentValue = parseInt(input.value);
            let newValue = currentValue + change;
            
            if (newValue >= 1) {
                // Update in database
                fetch('/api/cart/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        cart_id: cartId,
                        quantity: newValue
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        input.value = newValue;
                        updateTotals(data.totals);
                    } else {
                        showNotification(data.message || 'Error updating quantity', 'error');
                    }
                })
                .catch(error => {
                    showNotification('Error updating quantity', 'error');
                });
            }
        }

        // Remove item from cart
        function removeItem(cartId) {
            if (confirm('Remove this aircraft from your cart?')) {
                fetch('/api/cart/remove', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        cart_id: cartId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove item from DOM
                        const itemElement = document.querySelector(`[data-item-id="${cartId}"]`);
                        if (itemElement) {
                            itemElement.remove();
                        }
                        
                        // Update counts and totals
                        updateCartCount(data.cart_count);
                        
                        // Check if cart is empty
                        if (data.cart_count === 0) {
                            location.reload(); // Reload to show empty cart state
                        } else {
                            updateTotals(data.totals);
                            document.querySelector('.items-count').textContent = 
                                data.cart_count + ' item' + (data.cart_count > 1 ? 's' : '');
                        }
                        
                        showNotification('Item removed from cart', 'success');
                    } else {
                        showNotification(data.message || 'Error removing item', 'error');
                    }
                })
                .catch(error => {
                    showNotification('Error removing item', 'error');
                });
            }
        }

        // Apply promo code
        function applyPromo() {
            const promoCode = document.getElementById('promo-code').value.trim();
            const messageDiv = document.getElementById('promo-message');
            
            if (!promoCode) {
                messageDiv.innerHTML = '<span style="color: #dc3545;">Please enter a promo code</span>';
                return;
            }
            
            fetch('/api/cart/apply-promo', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    promo_code: promoCode
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageDiv.innerHTML = `<span style="color: #28a745;">${data.message}</span>`;
                    updateTotals(data.totals);
                } else {
                    messageDiv.innerHTML = `<span style="color: #dc3545;">${data.message || 'Invalid promo code'}</span>`;
                }
            })
            .catch(error => {
                messageDiv.innerHTML = '<span style="color: #dc3545;">Error applying promo code</span>';
            });
        }

        // Update totals in UI
        function updateTotals(totals) {
            if (totals.subtotal) {
                document.getElementById('subtotal').textContent = formatPrice(totals.subtotal);
            }
            if (totals.total) {
                document.getElementById('total').textContent = formatPrice(totals.total);
            }
        }

        // Format price helper
        function formatPrice(price) {
            return '$' + parseInt(price).toLocaleString();
        }

        // Update cart count in header
        function updateCartCount(count) {
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                cartCount.textContent = count;
            }
        }

        // Notification helper
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = 'notification show';
            notification.innerHTML = `
                <div class="notification-content">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}" 
                       style="color: ${type === 'success' ? '#28a745' : '#dc3545'};"></i>
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    </script>
</body>
</html>