//debug 



<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/helicopter-marketplace/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/helicopter-marketplace/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/helicopter-marketplace/models/User.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/helicopter-marketplace/models/Order.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/helicopter-marketplace/models/Helicopter.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/helicopter-marketplace/models/Wishlist.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: /login');
    exit;
}

$userId = $_SESSION['user']['id'];

try {
    $database = new Database();
    $db = $database->connect();
    
    // Initialize models
    $userModel = new User($db);
    $orderModel = new Order($db);
    $helicopterModel = new Helicopter($db);
    // $wishlistModel = new Wishlist($db);
    
    // Get user data using your existing method
    $userData = $userModel->getUserById($userId);
    
    // Get dashboard statistics using your User model methods
    $stats = [
        'total_orders' => $userModel->getUserOrderCount($userId),
        'pending_orders' => $userModel->getUserPendingOrderCount($userId),
        'wishlist_count' => $userModel->getUserWishlistCount($userId),
        'total_spent' => $userModel->getUserTotalSpent($userId)
    ];
    
    // Get recent orders
    $recentOrders = $orderModel->getUserRecentOrders($userId, 5);
    
    // Get wishlist items (top 4)
    $wishlistItems = $wishlistModel->getUserWishlist($userId, 4);
    
} catch (Exception $e) {
    error_log('Dashboard error: ' . $e->getMessage());
    // Set default empty values
    $userData = $_SESSION['user'];
    $stats = ['total_orders' => 0, 'pending_orders' => 0, 'wishlist_count' => 0, 'total_spent' => 0];
    $recentOrders = [];
    $wishlistItems = [];
}

// This structure makes it easy to pass as props in React
$dashboardData = [
    'user' => $userData,
    'stats' => $stats,
    'recentOrders' => $recentOrders,
    'wishlistItems' => $wishlistItems
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Helicopter Marketplace</title>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="/helicopter-marketplace/assets/css/app.css">
    
    <style>
        /* Component-based styling for easy React migration */
        .dashboard-container {
            margin-top: 100px;
            padding: 40px 0;
            min-height: 80vh;
            background: #1a1a1a;
        }
        
        /* UserHeader Component Styles */
        .user-header {
            background: linear-gradient(135deg, #2a2a2a, #1a1a1a);
            padding: 40px 0;
            margin-bottom: 40px;
            border-bottom: 1px solid rgba(255, 107, 53, 0.3);
        }
        
        .user-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 30px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 25px;
        }
        
        .user-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #FF6B35, #FF8C42);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            font-weight: bold;
        }
        
        .user-details h1 {
            color: #ffffff;
            font-size: 2.2rem;
            margin-bottom: 8px;
        }
        
        .user-details p {
            color: #cccccc;
            margin: 0;
            font-size: 1.1rem;
        }
        
        /* StatsGrid Component Styles */
        .stats-grid {
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
        
        /* Dashboard Layout */
        .dashboard-layout {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 30px;
            align-items: start;
        }
        
        /* Sidebar Component Styles */
        .dashboard-sidebar {
            background: #333333;
            border-radius: 15px;
            padding: 0;
            border: 1px solid rgba(255, 107, 53, 0.2);
            position: sticky;
            top: 100px;
            overflow: hidden;
        }
        
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .nav-item {
            border-bottom: 1px solid #444444;
        }
        
        .nav-item:last-child {
            border-bottom: none;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 18px 25px;
            color: #cccccc;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            background: rgba(255, 107, 53, 0.1);
            color: #FF6B35;
            padding-left: 30px;
        }
        
        .nav-link.active {
            background: rgba(255, 107, 53, 0.2);
            color: #FF6B35;
            border-left: 4px solid #FF6B35;
        }
        
        .nav-link i {
            width: 20px;
            text-align: center;
        }
        
        /* Main Content Area */
        .dashboard-content {
            background: #333333;
            border-radius: 15px;
            padding: 40px;
            border: 1px solid rgba(255, 107, 53, 0.2);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #444444;
        }
        
        .section-title {
            color: #FF6B35;
            font-size: 1.8rem;
            margin: 0;
        }
        
        .section-action {
            color: #FF6B35;
            text-decoration: none;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        /* Quick Actions Component */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .action-card {
            background: #2a2a2a;
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            border: 1px solid #444444;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .action-card:hover {
            border-color: #FF6B35;
            transform: translateY(-3px);
        }
        
        .action-card i {
            font-size: 2rem;
            color: #FF6B35;
            margin-bottom: 15px;
        }
        
        .action-card h4 {
            color: #ffffff;
            margin-bottom: 8px;
        }
        
        .action-card p {
            color: #aaaaaa;
            font-size: 0.9rem;
            margin: 0;
        }
        
        /* Recent Orders Table Component */
        .orders-table {
            width: 100%;
            background: #2a2a2a;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .orders-table thead {
            background: #1a1a1a;
        }
        
        .orders-table th {
            color: #FF6B35;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
        }
        
        .orders-table td {
            padding: 15px;
            color: #cccccc;
            border-top: 1px solid #444444;
        }
        
        .orders-table tr:hover {
            background: rgba(255, 107, 53, 0.05);
        }
        
        .order-number {
            color: #FF6B35;
            font-weight: 600;
        }
        
        .order-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-block;
        }
        
        .status-pending {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }
        
        .status-processing {
            background: rgba(23, 162, 184, 0.2);
            color: #17a2b8;
        }
        
        .status-completed {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }
        
        /* Wishlist Grid Component */
        .wishlist-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .wishlist-card {
            background: #2a2a2a;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #444444;
            transition: all 0.3s ease;
        }
        
        .wishlist-card:hover {
            border-color: #FF6B35;
            transform: translateY(-5px);
        }
        
        .wishlist-image {
            height: 180px;
            background: #444444;
            position: relative;
            overflow: hidden;
        }
        
        .wishlist-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .wishlist-content {
            padding: 20px;
        }
        
        .wishlist-title {
            color: #ffffff;
            font-size: 1.1rem;
            margin-bottom: 8px;
        }
        
        .wishlist-price {
            color: #FF6B35;
            font-size: 1.2rem;
            font-weight: bold;
        }
        
        /* Empty State Component */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #cccccc;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #FF6B35;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .empty-state h3 {
            color: #ffffff;
            margin-bottom: 10px;
        }
        
        /* Responsive */
        @media (max-width: 991px) {
            .dashboard-layout {
                grid-template-columns: 1fr;
            }
            
            .dashboard-sidebar {
                position: static;
            }
            
            .user-header-content {
                flex-direction: column;
                text-align: center;
            }
            
            .user-info {
                flex-direction: column;
            }
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .dashboard-content {
                padding: 25px;
            }
        }
    </style>
</head>
<body>
    <!-- Include Header -->
    <?php include '../includes/header.php'; ?>

    <!-- User Header Component -->
    <section class="user-header">
        <div class="container">
            <div class="user-header-content">
                <div class="user-info">
                    <div class="user-avatar" data-component="UserAvatar">
                        <?= strtoupper(substr($userData['first_name'] ?? 'U', 0, 1) . substr($userData['last_name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="user-details">
                        <h1>Welcome back, <?= htmlspecialchars($userData['first_name'] ?? 'User') ?>!</h1>
                        <p><?= htmlspecialchars($userData['email']) ?> • Member since <?= date('F Y', strtotime($userData['created_at'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Dashboard Container -->
    <section class="dashboard-container">
        <div class="container">
            <!-- Stats Grid Component -->
            <div class="stats-grid" data-component="StatsGrid">
                <div class="stat-card" data-stat="orders">
                    <i class="fas fa-shopping-bag stat-icon"></i>
                    <div class="stat-value"><?= $stats['total_orders'] ?></div>
                    <div class="stat-label">Total Orders</div>
                </div>
                
                <div class="stat-card" data-stat="pending">
                    <i class="fas fa-clock stat-icon"></i>
                    <div class="stat-value"><?= $stats['pending_orders'] ?></div>
                    <div class="stat-label">Pending Orders</div>
                </div>
                
                <div class="stat-card" data-stat="wishlist">
                    <i class="fas fa-heart stat-icon"></i>
                    <div class="stat-value"><?= $stats['wishlist_count'] ?></div>
                    <div class="stat-label">Wishlist Items</div>
                </div>
                
                <div class="stat-card" data-stat="spent">
                    <i class="fas fa-dollar-sign stat-icon"></i>
                    <div class="stat-value"><?= formatPrice($stats['total_spent']) ?></div>
                    <div class="stat-label">Total Spent</div>
                </div>
            </div>

            <!-- Dashboard Layout -->
            <div class="dashboard-layout">
                <!-- Sidebar Navigation Component -->
                <aside class="dashboard-sidebar" data-component="DashboardSidebar">
                    <nav>
                        <ul class="sidebar-nav">
                            <li class="nav-item">
                                <a href="/account" class="nav-link active" data-page="dashboard">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/account/profile" class="nav-link" data-page="profile">
                                    <i class="fas fa-user"></i>
                                    <span>Profile</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/account/orders" class="nav-link" data-page="orders">
                                    <i class="fas fa-shopping-bag"></i>
                                    <span>Orders</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/account/wishlist" class="nav-link" data-page="wishlist">
                                    <i class="fas fa-heart"></i>
                                    <span>Wishlist</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/account/inquiries" class="nav-link" data-page="inquiries">
                                    <i class="fas fa-envelope"></i>
                                    <span>Inquiries</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/account/settings" class="nav-link" data-page="settings">
                                    <i class="fas fa-cog"></i>
                                    <span>Settings</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/logout" class="nav-link" data-page="logout">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Logout</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </aside>

                <!-- Main Content Area -->
                <main class="dashboard-content" data-component="DashboardContent">
                    <!-- Quick Actions Component -->
                    <section data-component="QuickActions">
                        <div class="section-header">
                            <h2 class="section-title">Quick Actions</h2>
                        </div>
                        
                        <div class="quick-actions">
                            <a href="/helicopters" class="action-card">
                                <i class="fas fa-helicopter"></i>
                                <h4>Browse Aircraft</h4>
                                <p>Explore our inventory</p>
                            </a>
                            
                            <a href="/account/orders" class="action-card">
                                <i class="fas fa-file-invoice"></i>
                                <h4>View Orders</h4>
                                <p>Track your purchases</p>
                            </a>
                            
                            <a href="/account/wishlist" class="action-card">
                                <i class="fas fa-heart"></i>
                                <h4>My Wishlist</h4>
                                <p><?= $stats['wishlist_count'] ?> saved items</p>
                            </a>
                            
                            <a href="/contact" class="action-card">
                                <i class="fas fa-headset"></i>
                                <h4>Get Support</h4>
                                <p>We're here to help</p>
                            </a>
                        </div>
                    </section>

                    <!-- Recent Orders Component -->
                    <section data-component="RecentOrders">
                        <div class="section-header">
                            <h2 class="section-title">Recent Orders</h2>
                            <a href="/account/orders" class="section-action">
                                View all orders <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        
                        <?php if (!empty($recentOrders)): ?>
                            <table class="orders-table">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentOrders as $order): ?>
                                    <tr data-order-id="<?= $order['id'] ?>">
                                        <td class="order-number"><?= htmlspecialchars($order['order_number']) ?></td>
                                        <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                                        <td><?= $order['item_count'] ?> item<?= $order['item_count'] > 1 ? 's' : '' ?></td>
                                        <td><?= formatPrice($order['total_amount']) ?></td>
                                        <td>
                                            <span class="order-status status-<?= $order['status'] ?>">
                                                <?= ucfirst($order['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="/account/order/<?= $order['id'] ?>" class="text-primary">
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-shopping-bag"></i>
                                <h3>No orders yet</h3>
                                <p>Start exploring our helicopter inventory!</p>
                                <a href="/helicopters" class="btn btn-primary">Browse Aircraft</a>
                            </div>
                        <?php endif; ?>
                    </section>

                    <!-- Wishlist Preview Component -->
                    <section data-component="WishlistPreview" style="margin-top: 40px;">
                        <div class="section-header">
                            <h2 class="section-title">Your Wishlist</h2>
                            <a href="/account/wishlist" class="section-action">
                                View all <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        
                        <?php if (!empty($wishlistItems)): ?>
                            <div class="wishlist-grid">
                                <?php foreach ($wishlistItems as $item): ?>
                                <div class="wishlist-card" data-helicopter-id="<?= $item['helicopter_id'] ?>">
                                    <div class="wishlist-image">
                                        <?php 
                                        $images = json_decode($item['images'], true);
                                        $image = $images[0] ?? '/assets/images/helicopter-placeholder.jpg';
                                        ?>
                                        <img src="<?= $image ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                    </div>
                                    <div class="wishlist-content">
                                        <h4 class="wishlist-title"><?= htmlspecialchars($item['name']) ?></h4>
                                        <p class="wishlist-price"><?= formatPrice($item['price']) ?></p>
                                        <a href="/helicopter/<?= $item['helicopter_id'] ?>" class="btn btn-primary btn-small btn-full" style="margin-top: 10px;">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-heart"></i>
                                <h3>Your wishlist is empty</h3>
                                <p>Save helicopters you're interested in for later</p>
                            </div>
                        <?php endif; ?>
                    </section>
                </main>
            </div>
        </div>
    </section>

    <!-- Include Footer -->
    <?php include '../inludes/footer'; ?>

    <script>
        
        // Dashboard State (in React, this would be useState)
        const dashboardState = {
            user: <?= json_encode($userData) ?>,
            stats: <?= json_encode($stats) ?>,
            recentOrders: <?= json_encode($recentOrders) ?>,
            wishlistItems: <?= json_encode($wishlistItems) ?>
        };

        // Component initialization (in React, this would be useEffect)
        document.addEventListener('DOMContentLoaded', function() {
            initializeDashboard();
        });

        function initializeDashboard() {
            // Initialize tooltips
            initTooltips();
            
            // Initialize click handlers
            initClickHandlers();
            
            // Initialize real-time updates (if needed)
            // startRealtimeUpdates();
        }

        function initTooltips() {
            // Tooltip initialization for better UX
            document.querySelectorAll('[data-tooltip]').forEach(element => {
                // Tooltip logic here
            });
        }

        function initClickHandlers() {
            // Quick action cards
            document.querySelectorAll('.action-card').forEach(card => {
                card.addEventListener('click', function(e) {
                    // Track analytics event
                    trackEvent('quick_action_clicked', {
                        action: this.querySelector('h4').textContent
                    });
                });
            });

            // Order row clicks
            document.querySelectorAll('.orders-table tbody tr').forEach(row => {
                row.addEventListener('click', function(e) {
                    if (!e.target.matches('a')) {
                        const orderId = this.dataset.orderId;
                        window.location.href = `/account/order/${orderId}`;
                    }
                });
            });
        }

        // API calls (in React, these would be in separate service files)
        async function refreshStats() {
            try {
                const response = await fetch('/api/user/stats');
                const data = await response.json();
                
                if (data.success) {
                    updateStatsDisplay(data.stats);
                }
            } catch (error) {
                console.error('Error refreshing stats:', error);
            }
        }

        function updateStatsDisplay(stats) {
            // Update stat cards
            Object.keys(stats).forEach(key => {
                const element = document.querySelector(`[data-stat="${key}"] .stat-value`);
                if (element) {
                    element.textContent = stats[key];
                }
            });
        }

        // Track events for analytics
        function trackEvent(eventName, eventData) {
            // Google Analytics or other tracking
            if (typeof gtag !== 'undefined') {
                gtag('event', eventName, eventData);
            }
        }

        // Utility functions
        function formatPrice(price) {
            return '$' + parseInt(price).toLocaleString();
        }
    </script>
</body>
</html>