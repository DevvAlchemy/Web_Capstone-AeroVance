<?php
// models/Order.php

class Order {
    private $conn;
    private $table = 'orders';
    private $itemsTable = 'order_items';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new order
    public function createOrder($userId, $orderData) {
        // Begin transaction
        $this->conn->beginTransaction();
        
        try {
            // Generate order number
            $orderNumber = $this->generateOrderNumber();
            
            // Insert order
            $query = "INSERT INTO " . $this->table . " 
                      (user_id, order_number, subtotal, documentation_fee, inspection_fee, 
                       discount_amount, tax_amount, total_amount, status, payment_method, 
                       payment_status, shipping_address, billing_address, notes, created_at)
                      VALUES 
                      (:user_id, :order_number, :subtotal, :documentation_fee, :inspection_fee,
                       :discount_amount, :tax_amount, :total_amount, 'pending', :payment_method,
                       'pending', :shipping_address, :billing_address, :notes, NOW())";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':order_number', $orderNumber);
            $stmt->bindParam(':subtotal', $orderData['subtotal']);
            $stmt->bindParam(':documentation_fee', $orderData['documentation_fee']);
            $stmt->bindParam(':inspection_fee', $orderData['inspection_fee']);
            $stmt->bindParam(':discount_amount', $orderData['discount_amount']);
            $stmt->bindParam(':tax_amount', $orderData['tax_amount']);
            $stmt->bindParam(':total_amount', $orderData['total_amount']);
            $stmt->bindParam(':payment_method', $orderData['payment_method']);
            $stmt->bindParam(':shipping_address', json_encode($orderData['shipping_address']));
            $stmt->bindParam(':billing_address', json_encode($orderData['billing_address']));
            $stmt->bindParam(':notes', $orderData['notes']);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to create order');
            }
            
            $orderId = $this->conn->lastInsertId();
            
            // Commit transaction
            $this->conn->commit();
            
            return [
                'success' => true,
                'order_id' => $orderId,
                'order_number' => $orderNumber
            ];
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    // Get user's orders
    public function getUserOrders($userId, $limit = 20, $offset = 0) {
        $query = "SELECT o.*, 
                         (SELECT COUNT(*) FROM " . $this->itemsTable . " WHERE order_id = o.id) as item_count
                  FROM " . $this->table . " o
                  WHERE o.user_id = :user_id
                  ORDER BY o.created_at DESC
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get recent orders for dashboard
    public function getUserRecentOrders($userId, $limit = 5) {
        return $this->getUserOrders($userId, $limit, 0);
    }

    // Get order by ID
    public function getOrderById($orderId, $userId = null) {
        $query = "SELECT o.*, 
                         (SELECT COUNT(*) FROM " . $this->itemsTable . " WHERE order_id = o.id) as item_count
                  FROM " . $this->table . " o
                  WHERE o.id = :order_id";
        
        if ($userId) {
            $query .= " AND o.user_id = :user_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId);
        
        if ($userId) {
            $stmt->bindParam(':user_id', $userId);
        }
        
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get order items
    public function getOrderItems($orderId) {
        $query = "SELECT oi.*, h.name, h.manufacturer, h.model, h.year, h.images
                  FROM " . $this->itemsTable . " oi
                  JOIN helicopters h ON oi.helicopter_id = h.id
                  WHERE oi.order_id = :order_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get user's total order count
    public function getUserOrderCount($userId) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                  WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Get user's pending order count
    public function getUserPendingOrderCount($userId) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                  WHERE user_id = :user_id AND status = 'pending'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Get user's total spent
    public function getUserTotalSpent($userId) {
        $query = "SELECT SUM(total_amount) as total FROM " . $this->table . " 
                  WHERE user_id = :user_id AND payment_status = 'paid'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    // Update order status
    public function updateOrderStatus($orderId, $status) {
        $validStatuses = ['pending', 'processing', 'completed', 'cancelled'];
        
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        $query = "UPDATE " . $this->table . " 
                  SET status = :status, updated_at = NOW() 
                  WHERE id = :order_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':order_id', $orderId);
        
        return $stmt->execute();
    }

    // Update payment status
    public function updatePaymentStatus($orderId, $paymentStatus, $transactionId = null) {
        $query = "UPDATE " . $this->table . " 
                  SET payment_status = :payment_status, 
                      transaction_id = :transaction_id,
                      updated_at = NOW() 
                  WHERE id = :order_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':payment_status', $paymentStatus);
        $stmt->bindParam(':transaction_id', $transactionId);
        $stmt->bindParam(':order_id', $orderId);
        
        return $stmt->execute();
    }

    // Cancel order
    public function cancelOrder($orderId, $userId, $reason = null) {
        $query = "UPDATE " . $this->table . " 
                  SET status = 'cancelled', 
                      cancellation_reason = :reason,
                      cancelled_at = NOW(),
                      updated_at = NOW() 
                  WHERE id = :order_id AND user_id = :user_id 
                  AND status IN ('pending', 'processing')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':reason', $reason);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->bindParam(':user_id', $userId);
        
        return $stmt->execute();
    }

    // Generate unique order number
    private function generateOrderNumber() {
        $prefix = 'HM-' . date('Y') . '-';
        
        // Get the last order number for this year
        $query = "SELECT order_number FROM " . $this->table . " 
                  WHERE order_number LIKE :prefix 
                  ORDER BY id DESC LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $searchPrefix = $prefix . '%';
        $stmt->bindParam(':prefix', $searchPrefix);
        $stmt->execute();
        
        $lastOrder = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($lastOrder) {
            // Extract the number and increment
            $lastNumber = intval(substr($lastOrder['order_number'], -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $newNumber;
    }

    // Get order statistics for admin
    public function getOrderStatistics($period = 'month') {
        $dateCondition = "";
        
        switch ($period) {
            case 'day':
                $dateCondition = "DATE(created_at) = CURDATE()";
                break;
            case 'week':
                $dateCondition = "YEARWEEK(created_at) = YEARWEEK(NOW())";
                break;
            case 'month':
                $dateCondition = "MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())";
                break;
            case 'year':
                $dateCondition = "YEAR(created_at) = YEAR(NOW())";
                break;
        }
        
        $query = "SELECT 
                    COUNT(*) as total_orders,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
                    SUM(total_amount) as total_revenue,
                    AVG(total_amount) as average_order_value
                  FROM " . $this->table;
        
        if ($dateCondition) {
            $query .= " WHERE " . $dateCondition;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}