
<?php

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Order.php';
require_once '../models/User.php';

// Checking if user is logged in
if (!isLoggedIn()) {
    header('Location: /login');
    exit;
}

$userId = $_SESSION['user']['id'];
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$orderId) {
    header('Location: /account/orders');
    exit;
}

try {
    $database = new Database();
    $db = $database->connect();
    
    $orderModel = new Order($db);
    $userModel = new User($db);
    
    // Get order details (verify it belongs to user)
    $order = $orderModel->getOrderById($orderId, $userId);
    
    if (!$order) {
        header('Location: /account/orders');
        exit;
    }
    
    // Get order items
    $orderItems = $orderModel->getOrderItems($orderId);
    
    // Parse addresses
    $shippingAddress = json_decode($order['shipping_address'], true);
    $billingAddress = json_decode($order['billing_address'], true);
    
} catch (Exception $e) {
    error_log('Order details error: ' . $e->getMessage());
    header('Location: /account/orders');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?= htmlspecialchars($order['order_number']) ?> - AeroVance</title>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="/helicopter-marketplace/assets/css/app.css">
    
    <style>
        /* Order Details Page Styles */
        .order-details-container {
            margin-top: 100px;
            padding: 40px 0;
            background: #1a1a1a;
            min-height: 80vh;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #FF6B35;
            text-decoration: none;
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            transform: translateX(-5px);
        }
        
        /* Order Header */
        .order-header {
            background: #333333;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 107, 53, 0.2);
        }
        
        .order-header-top {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .order-title h1 {
            color: #FF6B35;
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .order-meta {
            color: #cccccc;
            font-size: 0.95rem;
        }
        
        .order-status-badge {
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
        }
        
        .status-pending {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid #ffc107;
        }
        
        .status-processing {
            background: rgba(23, 162, 184, 0.2);
            color: #17a2b8;
            border: 1px solid #17a2b8;
        }
        
        .status-completed {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid #28a745;
        }
        
        .status-cancelled {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid #dc3545;
        }
        
        /* Order Actions */
        .order-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        /* Content Grid */
        .order-content {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
            align-items: start;
        }
        
        /* Order Items Section */
        .order-items-section {
            background: #333333;
            border-radius: 15px;
            padding: 30px;
            border: 1px solid rgba(255, 107, 53, 0.2);
        }
        
        .section-title {
            color: #FF6B35;
            font-size: 1.5rem;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #444444;
        }
        
        .order-item {
            display: grid;
            grid-template-columns: 120px 1fr auto;
            gap: 20px;
            padding: 20px 0;
            border-bottom: 1px solid #444444;
            align-items: center;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .item-image {
            height: 100px;
            background: #444444;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .item-details h4 {
            color: #ffffff;
            font-size: 1.2rem;
            margin-bottom: 8px;
        }
        
        .item-details p {
            color: #aaaaaa;
            font-size: 0.9rem;
            margin: 0;
        }
        
        .item-pricing {
            text-align: right;
        }
        
        .item-quantity {
            color: #aaaaaa;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .item-price {
            color: #FF6B35;
            font-size: 1.3rem;
            font-weight: 600;
        }
        
        /* Order Summary Sidebar */
        .order-sidebar {
            position: sticky;
            top: 100px;
        }
        
        .summary-card {
            background: #333333;
            border-radius: 15px;
            padding: 30px;
            border: 1px solid rgba(255, 107, 53, 0.2);
            margin-bottom: 20px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            color: #cccccc;
            font-size: 0.95rem;
        }
        
        .summary-row.total {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #444444;
            font-size: 1.2rem;
            font-weight: 600;
            color: #ffffff;
        }
        
        .summary-row.total .amount {
            color: #FF6B35;
        }
        
        /* Address Cards */
        .address-card {
            background: #333333;
            border-radius: 15px;
            padding: 25px;
            border: 1px solid rgba(255, 107, 53, 0.2);
            margin-bottom: 20px;
        }
        
        .address-card h3 {
            color: #FF6B35;
            font-size: 1.2rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .address-details {
            color: #cccccc;
            line-height: 1.8;
        }
        
        /* Payment Info */
        .payment-info {
            background: rgba(255, 107, 53, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .payment-info h4 {
            color: #FF6B35;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }
        
        .payment-details {
            color: #cccccc;
            font-size: 0.95rem;
        }
        
        /* Order Timeline */
        .order-timeline {
            background: #333333;
            border-radius: 15px;
            padding: 30px;
            border: 1px solid rgba(255, 107, 53, 0.2);
            margin-top: 30px;
        }
        
        .timeline-item {
            display: flex;
            gap: 20px;
            padding: 20px 0;
            border-bottom: 1px solid #444444;
        }
        
        .timeline-item:last-child {
            border-bottom: none;
        }
        
        .timeline-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 107, 53, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FF6B35;
            flex-shrink: 0;
        }
        
        .timeline-content h4 {
            color: #ffffff;
            margin-bottom: 5px;
        }
        
        .timeline-content p {
            color: #aaaaaa;
            font-size: 0.9rem;
            margin: 0;
        }
        
        /* Print Styles */
        @media print {
            .back-link,
            .order-actions,
            header,
            footer {
                display: none !important;
            }
            
            body {
                background: white !important;
                color: black !important;
            }
            
            .order-content {
                grid-template-columns: 1fr !important;
            }
        }
        
        /* Responsive */
        @media (max-width: 991px) {
            .order-content {
                grid-template-columns: 1fr;
            }
            
            .order-sidebar {
                position: static;
            }
        }
        
        @media (max-width: 768px) {
            .order-header-top {
                flex-direction: column;
            }
            
            .order-item {
                grid-template-columns: 80px 1fr;
                gap: 15px;
            }
            
            .item-pricing {
                grid-column: 2;
                text-align: left;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Include Header -->
    <?php include '../includes/header.php'; ?>

    <div class="order-details-container">
        <div class="container">
            <!-- Back Link -->
            <a href="/account/orders" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Back to Orders
            </a>

            <!-- Order Header -->
            <div class="order-header">
                <div class="order-header-top">
                    <div class="order-title">
                        <h1>Order #<?= htmlspecialchars($order['order_number']) ?></h1>
                        <div class="order-meta">
                            Placed on <?= date('F d, Y \a\t g:i A', strtotime($order['created_at'])) ?>
                        </div>
                    </div>
                    
                    <div>
                        <span class="order-status-badge status-<?= $order['status'] ?>">
                            <?= ucfirst($order['status']) ?>
                        </span>
                    </div>
                </div>
                
                <div class="order-actions">
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> Print Order
                    </button>
                    
                    <?php if ($order['status'] === 'pending'): ?>
                    <button class="btn btn-outline" onclick="cancelOrder(<?= $orderId ?>)">
                        <i class="fas fa-times"></i> Cancel Order
                    </button>
                    <?php endif; ?>
                    
                    <a href="/contact?order=<?= $order['order_number'] ?>" class="btn btn-outline">
                        <i class="fas fa-headset"></i> Get Help
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="order-content">
                <!-- Left Column -->
                <div>
                    <!-- Order Items -->
                    <div class="order-items-section">
                        <h2 class="section-title">Order Items</h2>
                        
                        <?php foreach ($orderItems as $item): ?>
                        <div class="order-item">
                            <div class="item-image">
                                <?php 
                                $images = json_decode($item['images'], true);
                                $image = $images[0] ?? '/assets/images/helicopter-placeholder.jpg';
                                ?>
                                <img src="<?= $image ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                            </div>
                            
                            <div class="item-details">
                                <h4><?= htmlspecialchars($item['name']) ?></h4>
                                <p><?= htmlspecialchars($item['manufacturer']) ?> • <?= htmlspecialchars($item['model']) ?> • <?= $item['year'] ?></p>
                            </div>
                            
                            <div class="item-pricing">
                                <div class="item-quantity">Qty: <?= $item['quantity'] ?></div>
                                <div class="item-price"><?= formatPrice($item['price']) ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Order Timeline -->
                    <div class="order-timeline">
                        <h2 class="section-title">Order Timeline</h2>
                        
                        <div class="timeline-item">
                            <div class="timeline-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="timeline-content">
                                <h4>Order Placed</h4>
                                <p><?= date('F d, Y \a\t g:i A', strtotime($order['created_at'])) ?></p>
                            </div>
                        </div>
                        
                        <?php if ($order['status'] === 'processing' || $order['status'] === 'completed'): ?>
                        <div class="timeline-item">
                            <div class="timeline-icon">
                                <i class="fas fa-cog"></i>
                            </div>
                            <div class="timeline-content">
                                <h4>Processing Started</h4>
                                <p>Your order is being processed</p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($order['status'] === 'completed'): ?>
                        <div class="timeline-item">
                            <div class="timeline-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="timeline-content">
                                <h4>Order Completed</h4>
                                <p>Your order has been completed</p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($order['status'] === 'cancelled'): ?>
                        <div class="timeline-item">
                            <div class="timeline-icon">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div class="timeline-content">
                                <h4>Order Cancelled</h4>
                                <p><?= $order['cancelled_at'] ? date('F d, Y', strtotime($order['cancelled_at'])) : 'Cancelled' ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="order-sidebar">
                    <!-- Order Summary -->
                    <div class="summary-card">
                        <h3 class="section-title">Order Summary</h3>
                        
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span><?= formatPrice($order['subtotal']) ?></span>
                        </div>
                        
                        <?php if ($order['documentation_fee'] > 0): ?>
                        <div class="summary-row">
                            <span>Documentation Fee</span>
                            <span><?= formatPrice($order['documentation_fee']) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($order['inspection_fee'] > 0): ?>
                        <div class="summary-row">
                            <span>Inspection Fee</span>
                            <span><?= formatPrice($order['inspection_fee']) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($order['discount_amount'] > 0): ?>
                        <div class="summary-row">
                            <span>Discount</span>
                            <span>-<?= formatPrice($order['discount_amount']) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($order['tax_amount'] > 0): ?>
                        <div class="summary-row">
                            <span>Tax</span>
                            <span><?= formatPrice($order['tax_amount']) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="summary-row total">
                            <span>Total</span>
                            <span class="amount"><?= formatPrice($order['total_amount']) ?></span>
                        </div>
                        
                        <!-- Payment Info -->
                        <div class="payment-info">
                            <h4>Payment Information</h4>
                            <div class="payment-details">
                                <p>Method: <?= ucfirst($order['payment_method']) ?></p>
                                <p>Status: <span class="text-<?= $order['payment_status'] === 'paid' ? 'success' : 'warning' ?>">
                                    <?= ucfirst($order['payment_status']) ?>
                                </span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <?php if ($shippingAddress): ?>
                    <div class="address-card">
                        <h3><i class="fas fa-truck"></i> Delivery Address</h3>
                        <div class="address-details">
                            <?= htmlspecialchars($shippingAddress['name'] ?? '') ?><br>
                            <?= htmlspecialchars($shippingAddress['address'] ?? '') ?><br>
                            <?= htmlspecialchars($shippingAddress['city'] ?? '') ?>, 
                            <?= htmlspecialchars($shippingAddress['state'] ?? '') ?> 
                            <?= htmlspecialchars($shippingAddress['zip'] ?? '') ?><br>
                            <?= htmlspecialchars($shippingAddress['country'] ?? '') ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Billing Address -->
                    <?php if ($billingAddress): ?>
                    <div class="address-card">
                        <h3><i class="fas fa-file-invoice"></i> Billing Address</h3>
                        <div class="address-details">
                            <?= htmlspecialchars($billingAddress['name'] ?? '') ?><br>
                            <?= htmlspecialchars($billingAddress['address'] ?? '') ?><br>
                            <?= htmlspecialchars($billingAddress['city'] ?? '') ?>, 
                            <?= htmlspecialchars($billingAddress['state'] ?? '') ?> 
                            <?= htmlspecialchars($billingAddress['zip'] ?? '') ?><br>
                            <?= htmlspecialchars($billingAddress['country'] ?? '') ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Footer -->
    <?php include '../includes/footer.php'; ?>

    <script>
        // Cancel order function
        function cancelOrder(orderId) {
            if (confirm('Are you sure you want to cancel this order?')) {
                fetch('/api/order/cancel', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        order_id: orderId,
                        reason: 'Customer requested cancellation'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Order cancelled successfully', 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showNotification(data.message || 'Error cancelling order', 'error');
                    }
                })
                .catch(error => {
                    showNotification('Error cancelling order', 'error');
                });
            }
        }
    </script>
</body>
</html>