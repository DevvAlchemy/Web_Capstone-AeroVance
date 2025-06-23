<?php
/**
 * Helicopter Model
 * Handles all helicopter-related database operations
 */

require_once '../config/database.php';

class Helicopter {
    private $conn;
    private $table = 'helicopters';
    
    // Helicopter properties
    public $id;
    public $name;
    public $manufacturer;
    public $model;
    public $category; // personal, business, emergency
    public $price;
    public $year;
    public $condition; // new, used, refurbished
    public $max_speed;
    public $range;
    public $passenger_capacity;
    public $engine_type;
    public $fuel_capacity;
    public $description;
    public $specifications;
    public $images;
    public $stock_quantity;
    public $status; // available, sold, maintenance, 
    public $created_at;
    public $updated_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
     // Get all helicopters with pagination and filters
    
    public function getAllHelicopters($limit = 20, $offset = 0, $filters = []) {
        $query = "SELECT * FROM " . $this->table . " WHERE status = 'available'";
        $params = [];
        
        // Apply filters
        if (!empty($filters['category'])) {
            $query .= " AND category = :category";
            $params[':category'] = $filters['category'];
        }
        
        if (!empty($filters['manufacturer'])) {
            $query .= " AND manufacturer = :manufacturer";
            $params[':manufacturer'] = $filters['manufacturer'];
        }
        
        if (!empty($filters['min_price'])) {
            $query .= " AND price >= :min_price";
            $params[':min_price'] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $query .= " AND price <= :max_price";
            $params[':max_price'] = $filters['max_price'];
        }
        
        if (!empty($filters['condition'])) {
            $query .= " AND condition = :condition";
            $params[':condition'] = $filters['condition'];
        }
        
        if (!empty($filters['search'])) {
            $query .= " AND (name LIKE :search OR manufacturer LIKE :search OR model LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        $query .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    // Get helicopter by ID
    
    public function getHelicopterById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    //Get helicopters by category
    public function getHelicoptersByCategory($category, $limit = 10) {
        $query = "SELECT * FROM " . $this->table . " 
                 WHERE category = :category AND status = 'available' 
                 ORDER BY created_at DESC LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category', $category);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
   // Create new helicopter listing
    public function createHelicopter($data) {
        $query = "INSERT INTO " . $this->table . " 
                 (name, manufacturer, model, category, price, year, condition, 
                  max_speed, `range`, passenger_capacity, engine_type, fuel_capacity, 
                  description, specifications, images, stock_quantity, status, created_at) 
                 VALUES 
                 (:name, :manufacturer, :model, :category, :price, :year, :condition,
                  :max_speed, :range, :passenger_capacity, :engine_type, :fuel_capacity,
                  :description, :specifications, :images, :stock_quantity, :status, NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':manufacturer', $data['manufacturer']);
        $stmt->bindParam(':model', $data['model']);
        $stmt->bindParam(':category', $data['category']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':year', $data['year']);
        $stmt->bindParam(':condition', $data['condition']);
        $stmt->bindParam(':max_speed', $data['max_speed']);
        $stmt->bindParam(':range', $data['range']);
        $stmt->bindParam(':passenger_capacity', $data['passenger_capacity']);
        $stmt->bindParam(':engine_type', $data['engine_type']);
        $stmt->bindParam(':fuel_capacity', $data['fuel_capacity']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':specifications', $data['specifications']);
        $stmt->bindParam(':images', $data['images']);
        $stmt->bindParam(':stock_quantity', $data['stock_quantity']);
        $stmt->bindParam(':status', $data['status']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    //Update helicopter information
    public function updateHelicopter($id, $data) {
        $query = "UPDATE " . $this->table . " SET 
                 name = :name, manufacturer = :manufacturer, model = :model,
                 category = :category, price = :price, year = :year,
                 condition = :condition, max_speed = :max_speed, `range` = :range,
                 passenger_capacity = :passenger_capacity, engine_type = :engine_type,
                 fuel_capacity = :fuel_capacity, description = :description,
                 specifications = :specifications, images = :images,
                 stock_quantity = :stock_quantity, status = :status,
                 updated_at = NOW()
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':manufacturer', $data['manufacturer']);
        $stmt->bindParam(':model', $data['model']);
        $stmt->bindParam(':category', $data['category']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':year', $data['year']);
        $stmt->bindParam(':condition', $data['condition']);
        $stmt->bindParam(':max_speed', $data['max_speed']);
        $stmt->bindParam(':range', $data['range']);
        $stmt->bindParam(':passenger_capacity', $data['passenger_capacity']);
        $stmt->bindParam(':engine_type', $data['engine_type']);
        $stmt->bindParam(':fuel_capacity', $data['fuel_capacity']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':specifications', $data['specifications']);
        $stmt->bindParam(':images', $data['images']);
        $stmt->bindParam(':stock_quantity', $data['stock_quantity']);
        $stmt->bindParam(':status', $data['status']);
        
        return $stmt->execute();
    }
    
    // delete 
    public function deleteHelicopter($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    // featured
    public function getFeaturedHelicopters($limit = 6) {
        $query = "SELECT * FROM " . $this->table . " 
                 WHERE status = 'available' AND stock_quantity > 0
                 ORDER BY created_at DESC LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Update stock quantity
    public function updateStock($id, $quantity) {
        $query = "UPDATE " . $this->table . " SET stock_quantity = :quantity WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':quantity', $quantity);
        
        return $stmt->execute();
    }
    
    //Get manufacturers list
    public function getManufacturers() {
        $query = "SELECT DISTINCT manufacturer FROM " . $this->table . " ORDER BY manufacturer";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    // Get helicopter count by category
    public function getCountByCategory() {
        $query = "SELECT category, COUNT(*) as count FROM " . $this->table . " 
                 WHERE status = 'available' GROUP BY category";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>