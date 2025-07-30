<?php
// models/Helicopter.php

class Helicopter {
    private $conn;
    private $table = 'helicopters';

    // Helicopter properties
    public $id;
    public $name;
    public $manufacturer;
    public $model;
    public $year;
    public $price;
    public $category;
    public $condition;
    public $description;
    public $max_speed;
    public $cruise_speed;
    public $range;
    public $passenger_capacity;
    public $total_time;
    public $location;
    public $images;
    public $featured;
    public $status;
    public $created_at;
    public $updated_at;

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all helicopters with filtering and pagination
    public function getAllHelicopters($limit = 12, $offset = 0, $filters = []) {
        $query = "SELECT h.*, u.company_name as seller_name 
                  FROM " . $this->table . " h
                  LEFT JOIN users u ON h.seller_id = u.id
                  WHERE h.status = 'active'";

        // Apply filters
        if (!empty($filters['category'])) {
            $query .= " AND h.category = :category";
        }
        
        if (!empty($filters['manufacturer'])) {
            $query .= " AND h.manufacturer = :manufacturer";
        }
        
        if (!empty($filters['min_price'])) {
            $query .= " AND h.price >= :min_price";
        }
        
        if (!empty($filters['max_price'])) {
            $query .= " AND h.price <= :max_price";
        }
        
        if (!empty($filters['condition'])) {
            $query .= " AND h.condition = :condition";
        }
        
        if (!empty($filters['search'])) {
            $query .= " AND (h.name LIKE :search OR h.manufacturer LIKE :search2 
                        OR h.model LIKE :search3 OR h.description LIKE :search4)";
        }

        // Add sorting
        $query .= " ORDER BY h.created_at DESC";
        
        // Add pagination
        $query .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        if (!empty($filters['category'])) {
            $stmt->bindParam(':category', $filters['category']);
        }
        
        if (!empty($filters['manufacturer'])) {
            $stmt->bindParam(':manufacturer', $filters['manufacturer']);
        }
        
        if (!empty($filters['min_price'])) {
            $stmt->bindParam(':min_price', $filters['min_price']);
        }
        
        if (!empty($filters['max_price'])) {
            $stmt->bindParam(':max_price', $filters['max_price']);
        }
        
        if (!empty($filters['condition'])) {
            $stmt->bindParam(':condition', $filters['condition']);
        }
        
        if (!empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $stmt->bindParam(':search', $searchTerm);
            $stmt->bindParam(':search2', $searchTerm);
            $stmt->bindParam(':search3', $searchTerm);
            $stmt->bindParam(':search4', $searchTerm);
        }
        
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get total count for pagination
    public function getTotalCount($filters = []) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE status = 'active'";

        // Apply same filters as getAllHelicopters
        if (!empty($filters['category'])) {
            $query .= " AND category = :category";
        }
        
        if (!empty($filters['manufacturer'])) {
            $query .= " AND manufacturer = :manufacturer";
        }
        
        if (!empty($filters['min_price'])) {
            $query .= " AND price >= :min_price";
        }
        
        if (!empty($filters['max_price'])) {
            $query .= " AND price <= :max_price";
        }
        
        if (!empty($filters['condition'])) {
            $query .= " AND condition = :condition";
        }
        
        if (!empty($filters['search'])) {
            $query .= " AND (name LIKE :search OR manufacturer LIKE :search2 
                        OR model LIKE :search3 OR description LIKE :search4)";
        }

        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        if (!empty($filters['category'])) {
            $stmt->bindParam(':category', $filters['category']);
        }
        
        if (!empty($filters['manufacturer'])) {
            $stmt->bindParam(':manufacturer', $filters['manufacturer']);
        }
        
        if (!empty($filters['min_price'])) {
            $stmt->bindParam(':min_price', $filters['min_price']);
        }
        
        if (!empty($filters['max_price'])) {
            $stmt->bindParam(':max_price', $filters['max_price']);
        }
        
        if (!empty($filters['condition'])) {
            $stmt->bindParam(':condition', $filters['condition']);
        }
        
        if (!empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $stmt->bindParam(':search', $searchTerm);
            $stmt->bindParam(':search2', $searchTerm);
            $stmt->bindParam(':search3', $searchTerm);
            $stmt->bindParam(':search4', $searchTerm);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Get single helicopter by ID
    public function getHelicopterById($id) {
        $query = "SELECT h.*, u.company_name as seller_name, u.created_at as seller_joined
                  FROM " . $this->table . " h
                  LEFT JOIN users u ON h.seller_id = u.id
                  WHERE h.id = :id AND h.status = 'active'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get featured helicopters
    public function getFeatured($limit = 3) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE featured = 1 AND status = 'active' 
                  ORDER BY created_at DESC 
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get related helicopters
    public function getRelatedHelicopters($category, $excludeId, $limit = 3) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE category = :category 
                  AND id != :exclude_id 
                  AND status = 'active' 
                  ORDER BY RAND() 
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':exclude_id', $excludeId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get helicopters by category
    public function getByCategory($category, $limit = 12, $offset = 0) {
        $query = "SELECT h.*, u.company_name as seller_name 
                  FROM " . $this->table . " h
                  LEFT JOIN users u ON h.seller_id = u.id
                  WHERE h.category = :category AND h.status = 'active'
                  ORDER BY h.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all manufacturers for filter dropdown
    public function getManufacturers() {
        $query = "SELECT DISTINCT manufacturer FROM " . $this->table . " 
                  WHERE status = 'active' 
                  ORDER BY manufacturer ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_column($results, 'manufacturer');
    }

    // Search helicopters
    public function searchHelicopters($searchTerm, $limit = 10) {
        $query = "SELECT id, name, manufacturer, model, price, images 
                  FROM " . $this->table . " 
                  WHERE status = 'active' 
                  AND (name LIKE :search 
                       OR manufacturer LIKE :search2 
                       OR model LIKE :search3 
                       OR description LIKE :search4)
                  ORDER BY 
                    CASE 
                      WHEN name LIKE :exact THEN 1
                      WHEN manufacturer LIKE :exact2 THEN 2
                      WHEN model LIKE :exact3 THEN 3
                      ELSE 4
                    END,
                    created_at DESC
                  LIMIT :limit";

        $searchWildcard = '%' . $searchTerm . '%';
        $exactMatch = $searchTerm . '%';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':search', $searchWildcard);
        $stmt->bindParam(':search2', $searchWildcard);
        $stmt->bindParam(':search3', $searchWildcard);
        $stmt->bindParam(':search4', $searchWildcard);
        $stmt->bindParam(':exact', $exactMatch);
        $stmt->bindParam(':exact2', $exactMatch);
        $stmt->bindParam(':exact3', $exactMatch);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Create helicopter listing (for sellers)
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (name, manufacturer, model, year, price, category, `condition`, 
                   description, max_speed, cruise_speed, `range`, passenger_capacity, 
                   total_time, location, images, seller_id, status)
                  VALUES 
                  (:name, :manufacturer, :model, :year, :price, :category, :condition,
                   :description, :max_speed, :cruise_speed, :range, :passenger_capacity,
                   :total_time, :location, :images, :seller_id, 'pending')";

        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':manufacturer', $data['manufacturer']);
        $stmt->bindParam(':model', $data['model']);
        $stmt->bindParam(':year', $data['year']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':category', $data['category']);
        $stmt->bindParam(':condition', $data['condition']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':max_speed', $data['max_speed']);
        $stmt->bindParam(':cruise_speed', $data['cruise_speed']);
        $stmt->bindParam(':range', $data['range']);
        $stmt->bindParam(':passenger_capacity', $data['passenger_capacity']);
        $stmt->bindParam(':total_time', $data['total_time']);
        $stmt->bindParam(':location', $data['location']);
        $stmt->bindParam(':images', json_encode($data['images']));
        $stmt->bindParam(':seller_id', $data['seller_id']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }

    // Update helicopter listing
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET name = :name,
                      manufacturer = :manufacturer,
                      model = :model,
                      year = :year,
                      price = :price,
                      category = :category,
                      `condition` = :condition,
                      description = :description,
                      max_speed = :max_speed,
                      cruise_speed = :cruise_speed,
                      `range` = :range,
                      passenger_capacity = :passenger_capacity,
                      total_time = :total_time,
                      location = :location,
                      updated_at = NOW()
                  WHERE id = :id AND seller_id = :seller_id";

        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':manufacturer', $data['manufacturer']);
        $stmt->bindParam(':model', $data['model']);
        $stmt->bindParam(':year', $data['year']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':category', $data['category']);
        $stmt->bindParam(':condition', $data['condition']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':max_speed', $data['max_speed']);
        $stmt->bindParam(':cruise_speed', $data['cruise_speed']);
        $stmt->bindParam(':range', $data['range']);
        $stmt->bindParam(':passenger_capacity', $data['passenger_capacity']);
        $stmt->bindParam(':total_time', $data['total_time']);
        $stmt->bindParam(':location', $data['location']);
        $stmt->bindParam(':seller_id', $data['seller_id']);
        
        return $stmt->execute();
    }

    // Delete helicopter listing (soft delete)
    public function delete($id, $sellerId) {
        $query = "UPDATE " . $this->table . " 
                  SET status = 'deleted', updated_at = NOW() 
                  WHERE id = :id AND seller_id = :seller_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':seller_id', $sellerId);
        
        return $stmt->execute();
    }

    // Get helicopters by seller
    public function getBySeller($sellerId, $limit = 20, $offset = 0) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE seller_id = :seller_id 
                  AND status != 'deleted'
                  ORDER BY created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':seller_id', $sellerId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update view count
    public function incrementViews($id) {
        $query = "UPDATE " . $this->table . " 
                  SET views = views + 1 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    // Get popular helicopters
    public function getPopular($limit = 6) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE status = 'active' 
                  ORDER BY views DESC, created_at DESC 
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get recent helicopters
    public function getRecent($limit = 6) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE status = 'active' 
                  ORDER BY created_at DESC 
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get price range for filters
    public function getPriceRange() {
        $query = "SELECT MIN(price) as min_price, MAX(price) as max_price 
                  FROM " . $this->table . " 
                  WHERE status = 'active'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Additional methods for HelicopterController compatibility
    
    // Get count by category
    public function getCountByCategory($category) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                  WHERE category = :category AND status = 'active'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category', $category);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    // Alias method for controller compatibility
    public function getHelicoptersByCategory($category, $limit = 12, $offset = 0) {
        return $this->getByCategory($category, $limit, $offset);
    }
    
    // Alias method for getFeatured
    public function getFeaturedHelicopters($limit = 3) {
        return $this->getFeatured($limit);
    }
    
    // Alias method for delete
    public function deleteHelicopter($id, $sellerId = null) {
        if ($sellerId) {
            return $this->delete($id, $sellerId);
        }
        
        // If no seller ID provided, just soft delete
        $query = "UPDATE " . $this->table . " 
                  SET status = 'deleted', updated_at = NOW() 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    // Alias method for create
    public function createHelicopter($data) {
        return $this->create($data);
    }
    
    // Alias method for update
    public function updateHelicopter($id, $data, $sellerId = null) {
        if ($sellerId) {
            $data['seller_id'] = $sellerId;
        }
        return $this->update($id, $data);
    }
}