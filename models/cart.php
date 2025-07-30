<?php
// models/Cart.php

class Cart {
    private $conn;
    private $table = 'cart';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all cart items for a user
    public function getCartItems($userId) {
        $query = "SELECT c.*, h.name, h.manufacturer, h.model, h.year, 
                         h.price, h.category, h.total_time, h.images,
                         c.id as cart_id, h.id as helicopter_id
                  FROM " . $this->table . " c
                  JOIN helicopters h ON c.helicopter_id = h.id
                  WHERE c.user_id = :user_id
                  AND h.status = 'active'
                  ORDER BY c.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add item to cart
    public function addToCart($userId, $helicopterId, $quantity = 1) {
        // Check if item already exists in cart
        $checkQuery = "SELECT id, quantity FROM " . $this->table . " 
                       WHERE user_id = :user_id AND helicopter_id = :helicopter_id";

        $stmt = $this->conn->prepare($checkQuery);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':helicopter_id', $helicopterId);
        $stmt->execute();
        
        $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingItem) {
            // Update quantity if item exists
            $updateQuery = "UPDATE " . $this->table . " 
                           SET quantity = quantity + :quantity,
                               updated_at = NOW()
                           WHERE id = :id";
            
            $stmt = $this->conn->prepare($updateQuery);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':id', $existingItem['id']);
            
            return $stmt->execute();
        } else {
            // Insert new item
            $insertQuery = "INSERT INTO " . $this->table . " 
                           (user_id, helicopter_id, quantity, created_at)
                           VALUES (:user_id, :helicopter_id, :quantity, NOW())";
            
            $stmt = $this->conn->prepare($insertQuery);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':helicopter_id', $helicopterId);
            $stmt->bindParam(':quantity', $quantity);
            
            return $stmt->execute();
        }
    }

    // Update cart item quantity
    public function updateQuantity($cartId, $userId, $quantity) {
        $query = "UPDATE " . $this->table . " 
                  SET quantity = :quantity,
                      updated_at = NOW()
                  WHERE id = :cart_id AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':cart_id', $cartId);
        $stmt->bindParam(':user_id', $userId);
        
        return $stmt->execute();
    }

    // Remove item from cart
    public function removeItem($cartId, $userId) {
        $query = "DELETE FROM " . $this->table . " 
                  WHERE id = :cart_id AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cart_id', $cartId);
        $stmt->bindParam(':user_id', $userId);
        
        return $stmt->execute();
    }

    // Clear entire cart for user
    public function clearCart($userId) {
        $query = "DELETE FROM " . $this->table . " 
                  WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        
        return $stmt->execute();
    }

    // Get cart count for user
    public function getCartCount($userId) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                  WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    // Get cart totals
    public function getCartTotals($userId) {
        $query = "SELECT SUM(h.price * c.quantity) as subtotal,
                         COUNT(DISTINCT c.id) as item_count,
                         SUM(c.quantity) as total_quantity
                  FROM " . $this->table . " c
                  JOIN helicopters h ON c.helicopter_id = h.id
                  WHERE c.user_id = :user_id
                  AND h.status = 'active'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Calculate additional fees
        $subtotal = $result['subtotal'] ?? 0;
        $documentationFee = 2500;
        $inspectionFee = 5000;
        $total = $subtotal + $documentationFee + $inspectionFee;
        
        return [
            'subtotal' => $subtotal,
            'documentation_fee' => $documentationFee,
            'inspection_fee' => $inspectionFee,
            'total' => $total,
            'item_count' => $result['item_count'] ?? 0,
            'total_quantity' => $result['total_quantity'] ?? 0
        ];
    }

    // Check if helicopter is in cart
    public function isInCart($userId, $helicopterId) {
        $query = "SELECT id FROM " . $this->table . " 
                  WHERE user_id = :user_id AND helicopter_id = :helicopter_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':helicopter_id', $helicopterId);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    // Move cart items to order (used during checkout)
    public function moveToOrder($userId, $orderId) {
        // Get cart items
        $cartItems = $this->getCartItems($userId);
        
        if (empty($cartItems)) {
            return false;
        }
        
        // Insert items into order_items table
        $query = "INSERT INTO order_items 
                  (order_id, helicopter_id, quantity, price, created_at)
                  VALUES (:order_id, :helicopter_id, :quantity, :price, NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($cartItems as $item) {
            $stmt->bindParam(':order_id', $orderId);
            $stmt->bindParam(':helicopter_id', $item['helicopter_id']);
            $stmt->bindParam(':quantity', $item['quantity']);
            $stmt->bindParam(':price', $item['price']);
            
            if (!$stmt->execute()) {
                return false;
            }
        }
        
        // Clear the cart
        return $this->clearCart($userId);
    }

    // Apply promo code
    public function applyPromoCode($userId, $promoCode) {
        // Check if promo code exists and is valid
        $query = "SELECT * FROM promo_codes 
                  WHERE code = :code 
                  AND status = 'active'
                  AND (expires_at IS NULL OR expires_at > NOW())
                  AND (uses_remaining IS NULL OR uses_remaining > 0)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $promoCode);
        $stmt->execute();
        
        $promo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$promo) {
            return ['success' => false, 'message' => 'Invalid or expired promo code'];
        }
        
        // Check if user has already used this promo
        $checkQuery = "SELECT id FROM promo_usage 
                       WHERE user_id = :user_id AND promo_code_id = :promo_id";
        
        $stmt = $this->conn->prepare($checkQuery);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':promo_id', $promo['id']);
        $stmt->execute();
        
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'You have already used this promo code'];
        }
        
        // Get cart totals
        $totals = $this->getCartTotals($userId);
        
        // Apply discount
        $discount = 0;
        if ($promo['discount_type'] === 'percentage') {
            $discount = $totals['subtotal'] * ($promo['discount_value'] / 100);
        } else {
            $discount = $promo['discount_value'];
        }
        
        // Store promo in session
        $_SESSION['cart_promo'] = [
            'code' => $promoCode,
            'discount' => $discount,
            'promo_id' => $promo['id']
        ];
        
        // Recalculate totals with discount
        $totals['discount'] = $discount;
        $totals['total'] = $totals['subtotal'] + $totals['documentation_fee'] + 
                          $totals['inspection_fee'] - $discount;
        
        return [
            'success' => true,
            'message' => 'Promo code applied successfully!',
            'totals' => $totals
        ];
    }

    // Remove promo code
    public function removePromoCode($userId) {
        unset($_SESSION['cart_promo']);
        
        return [
            'success' => true,
            'totals' => $this->getCartTotals($userId)
        ];
    }
}
