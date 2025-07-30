<?php
// views/helicopter-detail.php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Helicopter.php';

// Helper function to get helicopter image
function getHelicopterImage($helicopter) {
    if (!empty($helicopter['images'])) {
        $images = json_decode($helicopter['images'], true);
        return $images[0] ?? '/assets/images/helicopter-placeholder.jpg';
    }
    return '/assets/images/helicopter-placeholder.jpg';
}

// Get helicopter ID from URL
$helicopter_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$helicopter_id) {
    header('Location: /helicopters');
    exit;
}

try {
    $database = new Database();
    $db = $database->connect();
    $helicopter = new Helicopter($db);
    
    // Get helicopter details
    $helicopterData = $helicopter->getHelicopterById($helicopter_id);
    
    if (!$helicopterData) {
        header('Location: /helicopters');
        exit;
    }
    
    // Get related helicopters
    $relatedHelicopters = $helicopter->getRelatedHelicopters($helicopterData['category'], $helicopter_id, 3);
    
} catch (Exception $e) {
    error_log('Helicopter detail error: ' . $e->getMessage());
    header('Location: /helicopters');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($helicopterData['name']) ?> - Helicopter Marketplace</title>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="/helicopter-marketplace/assets/css/app.css">
    
    <style>
        /* Product Detail Page Specific Styles/ removing later after testing */
        .product-detail {
            margin-top: 100px;
            padding: 40px 0;
        }
        
        .product-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            margin-bottom: 60px;
        }
        
        /* Image Gallery */
        .product-gallery {
            position: sticky;
            top: 100px;
            height: fit-content;
        }
        
        .main-image {
            position: relative;
            height: 500px;
            background: #444444;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 107, 53, 0.3);
        }
        
        .main-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .gallery-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .thumbnail-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }
        
        .thumbnail {
            height: 100px;
            background: #444444;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }
        
        .thumbnail:hover,
        .thumbnail.active {
            border-color: #FF6B35;
            transform: scale(1.05);
        }
        
        .thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        /* Product Information */
        .product-info {
            padding-top: 20px;
        }
        
        .product-header {
            margin-bottom: 30px;
        }
        
        .product-title {
            font-size: 2.5rem;
            color: #ffffff;
            margin-bottom: 10px;
            line-height: 1.2;
        }
        
        .product-subtitle {
            color: #FF6B35;
            font-size: 1.2rem;
            margin-bottom: 20px;
        }
        
        .product-price {
            font-size: 2.5rem;
            font-weight: bold;
            color: #FF6B35;
            margin-bottom: 10px;
        }
        
        .price-note {
            color: #aaaaaa;
            font-size: 0.9rem;
            margin-bottom: 30px;
        }
        
        /* Quick Info Cards */
        .quick-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .info-card {
            background: rgba(255, 107, 53, 0.1);
            border: 1px solid rgba(255, 107, 53, 0.3);
            border-radius: 10px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .info-card i {
            color: #FF6B35;
            font-size: 1.5rem;
        }
        
        .info-content h4 {
            color: #aaaaaa;
            font-size: 0.85rem;
            font-weight: 400;
            margin-bottom: 2px;
        }
        
        .info-content p {
            color: #ffffff;
            font-weight: 600;
            margin: 0;
        }
        
        /* Action Buttons */
        .product-actions {
            display: flex;
            gap: 15px;
            margin-bottom: 40px;
        }
        
        .product-actions .btn {
            flex: 1;
        }
        
        /* Mobile Responsive */
        @media (max-width: 991px) {
            .product-container {
                grid-template-columns: 1fr;
            }
            
            .product-gallery {
                position: static;
            }
        }
    </style>
</head>
<body>
    <!-- Include Header -->
    <?php include '../includes/header.php'; ?>

    <!-- Breadcrumb -->
    <div class="container" style="margin-top: 100px; margin-bottom: 20px;">
        <nav aria-label="breadcrumb">
            <div style="display: flex; gap: 10px; color: #aaaaaa; font-size: 0.9rem;">
                <a href="/" style="color: #aaaaaa;">Home</a>
                <span>/</span>
                <a href="/helicopters" style="color: #aaaaaa;">Helicopters</a>
                <span>/</span>
                <a href="/category/<?= $helicopterData['category'] ?>" style="color: #aaaaaa;">
                    <?= ucfirst($helicopterData['category']) ?>
                </a>
                <span>/</span>
                <span style="color: #FF6B35;"><?= htmlspecialchars($helicopterData['name']) ?></span>
            </div>
        </nav>
    </div>

    <!-- Product Detail Section -->
    <section class="product-detail">
        <div class="container">
            <div class="product-container">
                <!-- Product Gallery -->
                <div class="product-gallery">
                    <div class="main-image">
                        <?php if (!empty($helicopterData['images'])): ?>
                            <?php $images = json_decode($helicopterData['images'], true); ?>
                            <img src="<?= $images[0] ?? '/assets/images/helicopter-placeholder.jpg' ?>" 
                                 alt="<?= htmlspecialchars($helicopterData['name']) ?>" id="mainImage">
                            <div class="gallery-badge">
                                <i class="fas fa-images"></i>
                                <span><?= count($images) ?> Photos</span>
                            </div>
                        <?php else: ?>
                            <img src="/assets/images/helicopter-placeholder.jpg" 
                                 alt="<?= htmlspecialchars($helicopterData['name']) ?>">
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($images) && count($images) > 1): ?>
                    <div class="thumbnail-grid">
                        <?php foreach ($images as $index => $image): ?>
                            <div class="thumbnail <?= $index === 0 ? 'active' : '' ?>" 
                                 onclick="changeImage('<?= $image ?>', this)">
                                <img src="<?= $image ?>" alt="View <?= $index + 1 ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Product Information -->
                <div class="product-info">
                    <div class="product-header">
                        <h1 class="product-title"><?= htmlspecialchars($helicopterData['name']) ?></h1>
                        <p class="product-subtitle">
                            <?= htmlspecialchars($helicopterData['manufacturer']) ?> • 
                            <?= htmlspecialchars($helicopterData['model']) ?> • 
                            <?= $helicopterData['year'] ?> • 
                            <?= ucfirst($helicopterData['category']) ?>
                        </p>
                        
                        <div class="product-price"><?= formatPrice($helicopterData['price']) ?></div>
                        <p class="price-note">Plus applicable taxes and fees • Financing available</p>
                    </div>

                    <!-- Quick Info Cards -->
                    <div class="quick-info">
                        <div class="info-card">
                            <i class="fas fa-calendar"></i>
                            <div class="info-content">
                                <h4>Year</h4>
                                <p><?= $helicopterData['year'] ?></p>
                            </div>
                        </div>
                        <div class="info-card">
                            <i class="fas fa-clock"></i>
                            <div class="info-content">
                                <h4>Total Time</h4>
                                <p><?= $helicopterData['total_time'] ?? 'N/A' ?> Hours</p>
                            </div>
                        </div>
                        <div class="info-card">
                            <i class="fas fa-users"></i>
                            <div class="info-content">
                                <h4>Capacity</h4>
                                <p><?= $helicopterData['passenger_capacity'] ?> Passengers</p>
                            </div>
                        </div>
                        <div class="info-card">
                            <i class="fas fa-map-marker-alt"></i>
                            <div class="info-content">
                                <h4>Location</h4>
                                <p><?= htmlspecialchars($helicopterData['location'] ?? 'Contact Seller') ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="product-actions">
                        <?php if (isLoggedIn()): ?>
                            <button class="btn btn-primary btn-large" onclick="addToCart(<?= $helicopter_id ?>)">
                                <i class="fas fa-shopping-cart"></i>
                                Add to Cart
                            </button>
                            <button class="btn btn-outline btn-large" onclick="addToWishlist(<?= $helicopter_id ?>)">
                                <i class="fas fa-heart"></i>
                                Save to Wishlist
                            </button>
                        <?php else: ?>
                            <a href="/login?redirect=/helicopter/<?= $helicopter_id ?>" class="btn btn-primary btn-large">
                                <i class="fas fa-lock"></i>
                                Login to Purchase
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- Seller Info -->
                    <?php if (!empty($helicopterData['seller_id'])): ?>
                    <div class="seller-info">
                        <div class="seller-details">
                            <h4><?= htmlspecialchars($helicopterData['seller_name'] ?? 'Professional Dealer') ?></h4>
                            <p>Member since <?= date('Y', strtotime($helicopterData['seller_joined'] ?? 'now')) ?></p>
                        </div>
                        <button class="contact-seller-btn" onclick="contactSeller(<?= $helicopter_id ?>)">
                            <i class="fas fa-envelope"></i>
                            Contact Seller
                        </button>
                    </div>
                    <?php endif; ?>

                    <!-- Description -->
                    <div class="description-section">
                        <h3>About This Aircraft</h3>
                        <?= nl2br(htmlspecialchars($helicopterData['description'])) ?>
                    </div>

                    <!-- Specifications Tabs -->
                    <div class="specs-tabs">
                        <div class="tab-header">
                            <button class="tab-button active" onclick="showTab('performance')">
                                <i class="fas fa-tachometer-alt"></i> Performance
                            </button>
                            <button class="tab-button" onclick="showTab('dimensions')">
                                <i class="fas fa-ruler"></i> Dimensions
                            </button>
                            <button class="tab-button" onclick="showTab('features')">
                                <i class="fas fa-star"></i> Features
                            </button>
                            <button class="tab-button" onclick="showTab('equipment')">
                                <i class="fas fa-tools"></i> Equipment
                            </button>
                        </div>

                        <!-- Tab content would be populated from database specifications -->
                        <div class="tab-content active" id="performance-tab">
                            <div class="specs-grid">
                                <div class="spec-group">
                                    <h4>Speed & Range</h4>
                                    <div class="spec-row">
                                        <span class="spec-label">Maximum Speed</span>
                                        <span class="spec-value"><?= $helicopterData['max_speed'] ?? 'N/A' ?></span>
                                    </div>
                                    <div class="spec-row">
                                        <span class="spec-label">Cruise Speed</span>
                                        <span class="spec-value"><?= $helicopterData['cruise_speed'] ?? 'N/A' ?></span>
                                    </div>
                                    <div class="spec-row">
                                        <span class="spec-label">Range</span>
                                        <span class="spec-value"><?= $helicopterData['range'] ?? 'N/A' ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Products Section -->
    <?php if (!empty($relatedHelicopters)): ?>
    <section class="related-section">
        <div class="container">
            <div class="related-header">
                <h2>Similar Aircraft</h2>
                <p>You might also be interested in these helicopters</p>
            </div>
            
            <div class="helicopters-grid">
                <?php foreach ($relatedHelicopters as $related): ?>
                <div class="helicopter-card">
                    <div class="helicopter-image" style="background-image: url('<?= getHelicopterImage($related) ?>');">
                        <div class="price-tag"><?= formatPrice($related['price']) ?></div>
                    </div>
                    <div class="helicopter-info">
                        <h3><?= htmlspecialchars($related['name']) ?></h3>
                        <p><?= htmlspecialchars(substr($related['description'], 0, 120)) ?>...</p>
                        
                        <div class="helicopter-specs">
                            <div class="spec">
                                <span class="spec-value"><?= $related['max_speed'] ?? 'N/A' ?></span>
                                <span class="spec-label">Max Speed</span>
                            </div>
                            <div class="spec">
                                <span class="spec-value"><?= $related['range'] ?? 'N/A' ?></span>
                                <span class="spec-label">Range</span>
                            </div>
                            <div class="spec">
                                <span class="spec-value"><?= $related['passenger_capacity'] ?? 'N/A' ?></span>
                                <span class="spec-label">Passengers</span>
                            </div>
                        </div>
                        
                        <div class="helicopter-actions">
                            <a href="/helicopter/<?= $related['id'] ?>" class="btn btn-primary btn-full">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Include Footer -->
    <?php include '../includes/footer.php'; ?>

    <!-- Contact Modal -->
    <div class="modal-overlay" id="contactModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Contact Seller</h3>
                <button class="modal-close" onclick="closeContactModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form class="contact-form" id="contactSellerForm">
                <input type="hidden" name="helicopter_id" value="<?= $helicopter_id ?>">
                
                <div class="form-group">
                    <label>Your Name</label>
                    <input type="text" name="name" class="form-control" required 
                           value="<?= $_SESSION['user']['name'] ?? '' ?>">
                </div>
                
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" required
                           value="<?= $_SESSION['user']['email'] ?? '' ?>">
                </div>
                
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" class="form-control" rows="4" 
                              placeholder="I'm interested in the <?= htmlspecialchars($helicopterData['name']) ?>..."></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>
            </form>
        </div>
    </div>

    <script>
        // Image gallery functionality
        function changeImage(src, thumbnail) {
            document.getElementById('mainImage').src = src;
            
            // Update active thumbnail
            document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
            thumbnail.classList.add('active');
        }

        // Tab functionality
        function showTab(tabName) {
            // Hide all tabs
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            // Remove active from all buttons
            const buttons = document.querySelectorAll('.tab-button');
            buttons.forEach(btn => btn.classList.remove('active'));
            
            // Show selected tab
            document.getElementById(tabName + '-tab').classList.add('active');
            
            // Set button active
            event.target.closest('.tab-button').classList.add('active');
        }

        // Add to cart functionality
        function addToCart(helicopterId) {
            fetch('/api/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ helicopter_id: helicopterId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Added to cart successfully!', 'success');
                    updateCartCount(data.cart_count);
                } else {
                    showNotification(data.message || 'Error adding to cart', 'error');
                }
            })
            .catch(error => {
                showNotification('Error adding to cart', 'error');
            });
        }

        // Add to wishlist functionality
        function addToWishlist(helicopterId) {
            fetch('/api/wishlist/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ helicopter_id: helicopterId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Added to wishlist!', 'success');
                } else {
                    showNotification(data.message || 'Error adding to wishlist', 'error');
                }
            })
            .catch(error => {
                showNotification('Error adding to wishlist', 'error');
            });
        }

        // Contact seller
        function contactSeller(helicopterId) {
            <?php if (isLoggedIn()): ?>
                document.getElementById('contactModal').classList.add('active');
            <?php else: ?>
                window.location.href = '/login?redirect=/helicopter/' + helicopterId;
            <?php endif; ?>
        }

        function closeContactModal() {
            document.getElementById('contactModal').classList.remove('active');
        }

        // Contact form submission
        document.getElementById('contactSellerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('/api/inquiry/send', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Message sent successfully!', 'success');
                    closeContactModal();
                    this.reset();
                } else {
                    showNotification(data.message || 'Error sending message', 'error');
                }
            })
            .catch(error => {
                showNotification('Error sending message', 'error');
            });
        });

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

        // Update cart count
        function updateCartCount(count) {
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                cartCount.textContent = count;
            }
        }
    </script>
</body>
</html>