<?php
/**
 * WISHLIST PAGE - Following Your Successful Pattern
 * 
 * STUDENT NOTES:
 * - Uses your exact same structure from dashboard.php
 * - Same authentication pattern
 * - Same database connection approach
 * - Ready for React migration with data-component attributes
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/helicopter-marketplace/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/helicopter-marketplace/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/helicopter-marketplace/models/User.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/helicopter-marketplace/models/Helicopter.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/helicopter-marketplace/models/Wishlist.php';

// Check if user is logged in (same as your dashboard)
if (!isLoggedIn()) {
    header('Location: /login');
    exit;
}

$userId = $_SESSION['user']['id'];

try {
    $database = new Database();
    $db = $database->connect();
    
    // Initialize models (same pattern as your dashboard)
    $userModel = new User($db);
    $helicopterModel = new Helicopter($db);
    $wishlistModel = new Wishlist($db);
    
    // Get user data
    $userData = $userModel->getUserById($userId);
    
    // Get wishlist items with full helicopter details
    $wishlistItems = $wishlistModel->getUserWishlist($userId);
    
    // Get wishlist statistics
    $wishlistStats = [
        'total_items' => count($wishlistItems),
        'total_value' => array_sum(array_column($wishlistItems, 'price')),
        'categories' => array_unique(array_column($wishlistItems, 'category')),
        'avg_price' => count($wishlistItems) > 0 ? array_sum(array_column($wishlistItems, 'price')) / count($wishlistItems) : 0
    ];
    
} catch (Exception $e) {
    error_log('Wishlist error: ' . $e->getMessage());
    // Fallback data (same pattern as your dashboard)
    $userData = $_SESSION['user'];
    $wishlistItems = [];
    $wishlistStats = ['total_items' => 0, 'total_value' => 0, 'categories' => [], 'avg_price' => 0];
}

// Handle AJAX actions (remove from wishlist, etc.)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'remove') {
        $helicopterId = (int)$_POST['helicopter_id'];
        try {
            $result = $wishlistModel->removeFromWishlist($userId, $helicopterId);
            echo json_encode(['success' => $result]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error removing item']);
        }
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - AeroVance</title>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="/helicopter-marketplace/assets/css/app.css">
    
    <style>
        /* Component-based styling (same approach as your dashboard) */
        .wishlist-container {
            margin-top: 100px;
            padding: 40px 0;
            min-height: 80vh;
            background: #1a1a1a;
        }
        
        /* WishlistHeader Component */
        .wishlist-header {
            background: linear-gradient(135deg, #2a2a2a, #1a1a1a);
            padding: 40px 0;
            margin-bottom: 40px;
            border-bottom: 1px solid rgba(255, 107, 53, 0.3);
        }
        
        .wishlist-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 30px;
        }
        
        .header-info h1 {
            color: #FF6B35;
            font-size: 2.5rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .header-info p {
            color: #cccccc;
            font-size: 1.1rem;
        }
        
        .wishlist-actions {
            display: flex;
            gap: 15px;
        }
        
        /* WishlistStats Component */
        .wishlist-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: #333333;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            border: 1px solid rgba(255, 107, 53, 0.2);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            border-color: #FF6B35;
            box-shadow: 0 10px 30px rgba(255, 107, 53, 0.2);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            color: #FF6B35;
            margin-bottom: 15px;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #aaaaaa;
            font-size: 0.9rem;
            text-transform: uppercase;
        }
        
        /* WishlistGrid Component */
        .wishlist-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .wishlist-card {
            background: #333333;
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid rgba(255, 107, 53, 0.2);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .wishlist-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
            border-color: #FF6B35;
        }
        
        .wishlist-image {
            height: 250px;
            background: #444444;
            position: relative;
            overflow: hidden;
        }
        
        .wishlist-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .remove-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(220, 53, 69, 0.9);
            border: none;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .remove-btn:hover {
            background: #dc3545;
            transform: scale(1.1);
        }
        
        .price-tag {
            position: absolute;
            bottom: 15px;
            right: 15px;
            background: linear-gradient(135deg, #FF6B35, #FF8C42);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        .wishlist-info {
            padding: 25px;
        }
        
        .wishlist-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: #ffffff;
        }
        
        .wishlist-subtitle {
            color: #FF6B35;
            font-size: 0.9rem;
            margin-bottom: 15px;
            font-weight: 500;
        }
        
        .wishlist-description {
            color: #cccccc;
            margin-bottom: 20px;
            font-size: 0.9rem;
            line-height: 1.5;
            display: -webkit-box;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .wishlist-specs {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        
        .spec-item {
            text-align: center;
            padding: 10px 8px;
            background: rgba(255, 107, 53, 0.1);
            border-radius: 8px;
        }
        
        .spec-value {
            color: #FF6B35;
            font-weight: bold;
            display: block;
            font-size: 1rem;
        }
        
        .spec-label {
            color: #aaaaaa;
            font-size: 0.75rem;
            text-transform: uppercase;
            margin-top: 2px;
        }
        
        .wishlist-actions {
            display: flex;
            gap: 10px;
        }
        
        /* EmptyWishlist Component */
        .empty-wishlist {
            text-align: center;
            padding: 80px 20px;
            background: #333333;
            border-radius: 15px;
            border: 1px solid rgba(255, 107, 53, 0.2);
        }
        
        .empty-wishlist i {
            font-size: 5rem;
            color: #FF6B35;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .empty-wishlist h2 {
            color: #ffffff;
            margin-bottom: 15px;
        }
        
        .empty-wishlist p {
            color: #cccccc;
            margin-bottom: 30px;
        }
        
        /* Responsive Design */
        @media (max-width: 991px) {
            .wishlist-header-content {
                flex-direction: column;
                text-align: center;
            }
            
            .wishlist-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            }
        }
        
        @media (max-width: 768px) {
            .wishlist-stats {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .wishlist-grid {
                grid-template-columns: 1fr;
            }
            
            .header-info h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Include Header -->
    <?php include '../includes/header.php'; ?>

    <!-- Wishlist Header Component -->
    <section class="wishlist-header" data-component="WishlistHeader">
        <div class="container">
            <div class="wishlist-header-content">
                <div class="header-info">
                    <h1>
                        <i class="fas fa-heart"></i>
                        My Wishlist
                    </h1>
                    <p>Aircraft you've saved for future consideration</p>
                </div>
                
                <div class="wishlist-actions">
                    <a href="/helicopters" class="btn btn-outline">
                        <i class="fas fa-plus"></i> Add More Aircraft
                    </a>
                    
                    <?php if (!empty($wishlistItems)): ?>
                    <button class="btn btn-primary" onclick="shareWishlist()">
                        <i class="fas fa-share"></i> Share Wishlist
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Wishlist Container -->
    <section class="wishlist-container">
        <div class="container">
            <?php if (!empty($wishlistItems)): ?>
                <!-- Wishlist Stats Component -->
                <div class="wishlist-stats" data-component="WishlistStats">
                    <div class="stat-card">
                        <i class="fas fa-heart stat-icon"></i>
                        <div class="stat-value"><?= $wishlistStats['total_items'] ?></div>
                        <div class="stat-label">Saved Aircraft</div>
                    </div>
                    
                    <div class="stat-card">
                        <i class="fas fa-dollar-sign stat-icon"></i>
                        <div class="stat-value"><?= formatPrice($wishlistStats['total_value']) ?></div>
                        <div class="stat-label">Total Value</div>
                    </div>
                    
                    <div class="stat-card">
                        <i class="fas fa-tags stat-icon"></i>
                        <div class="stat-value"><?= count($wishlistStats['categories']) ?></div>
                        <div class="stat-label">Categories</div>
                    </div>
                    
                    <div class="stat-card">
                        <i class="fas fa-chart-line stat-icon"></i>
                        <div class="stat-value"><?= formatPrice($wishlistStats['avg_price']) ?></div>
                        <div class="stat-label">Avg Price</div>
                    </div>
                </div>

                <!-- Wishlist Grid Component -->
                <div class="wishlist-grid" data-component="WishlistGrid">
                    <?php foreach ($wishlistItems as $item): ?>
                        <div class="wishlist-card" data-helicopter-id="<?= $item['helicopter_id'] ?>">
                            <div class="wishlist-image">
                                <?php 
                                $images = json_decode($item['images'], true);
                                $image = $images[0] ?? '/assets/images/helicopter-placeholder.jpg';
                                ?>
                                <img src="<?= $image ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                
                                <button class="remove-btn" onclick="removeFromWishlist(<?= $item['helicopter_id'] ?>)" title="Remove from wishlist">
                                    <i class="fas fa-times"></i>
                                </button>
                                
                                <div class="price-tag"><?= formatPrice($item['price']) ?></div>
                            </div>
                            
                            <div class="wishlist-info">
                                <h3 class="wishlist-title"><?= htmlspecialchars($item['name']) ?></h3>
                                <div class="wishlist-subtitle">
                                    <?= htmlspecialchars($item['manufacturer']) ?> • 
                                    <?= htmlspecialchars($item['model']) ?> • 
                                    <?= $item['year'] ?> • 
                                    <?= ucfirst($item['category']) ?>
                                </div>
                                
                                <p class="wishlist-description">
                                    <?= htmlspecialchars(substr($item['description'], 0, 120)) ?>...
                                </p>
                                
                                <div class="wishlist-specs">
                                    <div class="spec-item">
                                        <span class="spec-value"><?= $item['max_speed'] ?? 'N/A' ?></span>
                                        <div class="spec-label">Max Speed</div>
                                    </div>
                                    <div class="spec-item">
                                        <span class="spec-value"><?= $item['range'] ?? 'N/A' ?></span>
                                        <div class="spec-label">Range</div>
                                    </div>
                                    <div class="spec-item">
                                        <span class="spec-value"><?= $item['passenger_capacity'] ?? 'N/A' ?></span>
                                        <div class="spec-label">Passengers</div>
                                    </div>
                                </div>
                                
                                <div class="wishlist-actions">
                                    <a href="/helicopter/<?= $item['helicopter_id'] ?>" class="btn btn-primary btn-full">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                </div>
                                
                                <div style="margin-top: 10px;">
                                    <small style="color: #aaaaaa;">
                                        <i class="fas fa-calendar"></i> Added <?= date('M d, Y', strtotime($item['added_date'])) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- Empty Wishlist Component -->
                <div class="empty-wishlist" data-component="EmptyWishlist">
                    <i class="fas fa-heart"></i>
                    <h2>Your wishlist is empty</h2>
                    <p>Start exploring our helicopter collection and save aircraft you're interested in.</p>
                    <a href="/helicopters" class="btn btn-primary btn-large">
                        <i class="fas fa-helicopter"></i> Browse Aircraft
                    </a>
                </div>
            <?php endif; ?>

            <!-- Back to Account -->
            <div style="margin-top: 40px; text-align: center;">
                <a href="/account" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </section>

    <!-- Include Footer -->
    <?php include '../includes/footer.php'; ?>

    <script>
        /**
         * WISHLIST PAGE FUNCTIONALITY
         * Following your component-ready pattern from dashboard.js
         */
        
        // Wishlist data (ready for React state)
        const wishlistData = {
            items: <?= json_encode($wishlistItems) ?>,
            stats: <?= json_encode($wishlistStats) ?>,
            user: <?= json_encode($userData) ?>
        };

        document.addEventListener('DOMContentLoaded', function() {
            initializeWishlistPage();
        });

        function initializeWishlistPage() {
            // Initialize tooltips and interactions
            initializeCardInteractions();
            
            console.log('Wishlist page initialized with', wishlistData.items.length, 'items');
        }

        function initializeCardInteractions() {
            // Add hover effects and interactions
            document.querySelectorAll('.wishlist-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px) scale(1.02)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        }

        // Remove from wishlist function
        function removeFromWishlist(helicopterId) {
            if (!confirm('Remove this aircraft from your wishlist?')) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'remove');
            formData.append('helicopter_id', helicopterId);

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove card from DOM with animation
                    const card = document.querySelector(`[data-helicopter-id="${helicopterId}"]`);
                    if (card) {
                        card.style.transform = 'scale(0)';
                        card.style.opacity = '0';
                        setTimeout(() => {
                            card.remove();
                            
                            // Check if wishlist is now empty
                            const remainingCards = document.querySelectorAll('.wishlist-card');
                            if (remainingCards.length === 0) {
                                location.reload(); // Show empty state
                            } else {
                                updateWishlistStats();
                            }
                        }, 300);
                    }
                    
                    showNotification('Removed from wishlist', 'success');
                } else {
                    showNotification(data.message || 'Error removing item', 'error');
                }
            })
            .catch(error => {
                showNotification('Error removing item', 'error');
            });
        }

        function updateWishlistStats() {
            // Recalculate stats after removal
            const remainingCards = document.querySelectorAll('.wishlist-card');
            const totalItems = remainingCards.length;
            
            // Update stats display
            const statsCards = document.querySelectorAll('.stat-card .stat-value');
            if (statsCards[0]) {
                statsCards[0].textContent = totalItems;
            }
        }

        // Share wishlist function
        function shareWishlist() {
            if (navigator.share) {
                navigator.share({
                    title: 'My Helicopter Wishlist',
                    text: `Check out my wishlist of ${wishlistData.items.length} aircraft`,
                    url: window.location.href
                });
            } else {
                // Fallback: copy to clipboard
                navigator.clipboard.writeText(window.location.href).then(() => {
                    showNotification('Wishlist link copied to clipboard', 'success');
                });
            }
        }

        // Notification helper (same as your dashboard pattern)
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