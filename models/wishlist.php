<?php
// models/Wishlist.php

class Wishlist {
    private $conn;
    private $table = 'wishlist';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Add to wishlist
    public function addToWishlist($userId, $helicopterId) {
        // Check if already in wishlist
        $checkQuery = "SELECT id FROM " . $this->table . " 
                       WHERE user_id = :user_id AND helicopter_id = :helicopter_id";
        
        $stmt = $this->conn->prepare($checkQuery);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':helicopter_id', $helicopterId);
        $stmt->execute();
        
        if ($stmt->fetch()) {
            return false; // Already in wishlist
        }
        
        // Add to wishlist
        $query = "INSERT INTO " . $this->table . " (user_id, helicopter_id, created_at) 
                  VALUES (:user_id, :helicopter_id, NOW())";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':helicopter_id', $helicopterId);
        
        return $stmt->execute();
    }

    // Remove from wishlist
    public function removeFromWishlist($userId, $helicopterId) {
        $query = "DELETE FROM " . $this->table . " 
                  WHERE user_id = :user_id AND helicopter_id = :helicopter_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':helicopter_id', $helicopterId);
        
        return $stmt->execute();
    }

    // Get user's wishlist
    public function getUserWishlist($userId) {
        $query = "SELECT w.*, h.*, w.created_at as added_date 
                  FROM " . $this->table . " w
                  JOIN helicopters h ON w.helicopter_id = h.id
                  WHERE w.user_id = :user_id
                  AND h.status = 'active'
                  ORDER BY w.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Check if helicopter is in wishlist
    public function isInWishlist($userId, $helicopterId) {
        $query = "SELECT id FROM " . $this->table . " 
                  WHERE user_id = :user_id AND helicopter_id = :helicopter_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':helicopter_id', $helicopterId);
        $stmt->execute();
        
        return $stmt->fetch() ? true : false;
    }

    // Get wishlist count
    public function getWishlistCount($userId) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                  WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
}