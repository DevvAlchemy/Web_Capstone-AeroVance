<?php
/*
 * User Model
 * Handles user authentication, registration, and profile management
 */

require_once __DIR__ . '/../config/config.php';

class User {
    private $conn;
    private $table = 'users';
    
    // User properties
    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $password;
    public $phone;
    public $address;
    public $city;
    public $state;
    public $zip_code;
    public $country;
    public $user_type; // customer, admin, dealer
    public $pilot_license;
    public $company_name;
    public $tax_id;
    public $status; // active, inactive, suspended
    public $email_verified;
    public $created_at;
    public $updated_at;
    public $last_login;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Register new user
     
    public function register($userData) {
        // Check if email already exists
        if ($this->emailExists($userData['email'])) {
            return ['success' => false, 'message' => 'Email already exists'];
        }
        
        $query = "INSERT INTO " . $this->table . " 
                 (first_name, last_name, email, password, phone, address, city, 
                  state, zip_code, country, user_type, pilot_license, company_name, 
                  tax_id, status, created_at) 
                 VALUES 
                 (:first_name, :last_name, :email, :password, :phone, :address, :city,
                  :state, :zip_code, :country, :user_type, :pilot_license, :company_name,
                  :tax_id, :status, NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        // Hash password
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        // Bind parameters
        $stmt->bindParam(':first_name', $userData['first_name']);
        $stmt->bindParam(':last_name', $userData['last_name']);
        $stmt->bindParam(':email', $userData['email']);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':phone', $userData['phone']);
        $stmt->bindParam(':address', $userData['address']);
        $stmt->bindParam(':city', $userData['city']);
        $stmt->bindParam(':state', $userData['state']);
        $stmt->bindParam(':zip_code', $userData['zip_code']);
        $stmt->bindParam(':country', $userData['country']);
        $stmt->bindParam(':user_type', $userData['user_type']);
        $stmt->bindParam(':pilot_license', $userData['pilot_license']);
        $stmt->bindParam(':company_name', $userData['company_name']);
        $stmt->bindParam(':tax_id', $userData['tax_id']);
        
        $status = 'active';
        $stmt->bindParam(':status', $status);
        
        if ($stmt->execute()) {
            $userId = $this->conn->lastInsertId();
            return ['success' => true, 'user_id' => $userId, 'message' => 'Registration successful'];
        }
        
        return ['success' => false, 'message' => 'Registration failed'];
    }
    
    // Login user
     
    public function login($email, $password) {
        $query = "SELECT id, first_name, last_name, email, password, user_type, status 
                 FROM " . $this->table . " WHERE email = :email AND status = 'active'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($password, $user['password'])) {
                // Update last login
                $this->updateLastLogin($user['id']);
                
                // Remove password from return data
                unset($user['password']);
                
                return ['success' => true, 'user' => $user];
            }
        }
        
        return ['success' => false, 'message' => 'Invalid credentials'];
    }
    
    // Get user by ID
     
    public function getUserById($id) {
        $query = "SELECT id, first_name, last_name, email, phone, address, city, 
                         state, zip_code, country, user_type, pilot_license, 
                         company_name, tax_id, status, email_verified, created_at, 
                         updated_at, last_login 
                 FROM " . $this->table . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Get user by email
     
    public function getUserByEmail($email) {
        $query = "SELECT id, first_name, last_name, email, phone, address, city, 
                         state, zip_code, country, user_type, pilot_license, 
                         company_name, tax_id, status, email_verified, created_at, 
                         updated_at, last_login 
                 FROM " . $this->table . " WHERE email = :email";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Check if email exists
     
    public function emailExists($email) {
        $query = "SELECT id FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    // Update user profile
     
    public function updateProfile($id, $userData) {
        $query = "UPDATE " . $this->table . " SET 
                 first_name = :first_name, last_name = :last_name, 
                 phone = :phone, address = :address, city = :city,
                 state = :state, zip_code = :zip_code, country = :country,
                 pilot_license = :pilot_license, company_name = :company_name,
                 tax_id = :tax_id, updated_at = NOW()
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':first_name', $userData['first_name']);
        $stmt->bindParam(':last_name', $userData['last_name']);
        $stmt->bindParam(':phone', $userData['phone']);
        $stmt->bindParam(':address', $userData['address']);
        $stmt->bindParam(':city', $userData['city']);
        $stmt->bindParam(':state', $userData['state']);
        $stmt->bindParam(':zip_code', $userData['zip_code']);
        $stmt->bindParam(':country', $userData['country']);
        $stmt->bindParam(':pilot_license', $userData['pilot_license']);
        $stmt->bindParam(':company_name', $userData['company_name']);
        $stmt->bindParam(':tax_id', $userData['tax_id']);
        
        return $stmt->execute();
    }
    
    // Change password
    
    public function changePassword($id, $currentPassword, $newPassword) {
        // First verify current password
        $query = "SELECT password FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!password_verify($currentPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        
        // Update password
        $query = "UPDATE " . $this->table . " SET password = :password, updated_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Password updated successfully'];
        }
        
        return ['success' => false, 'message' => 'Password update failed'];
    }
    
    // Update last login timestamp
    
    private function updateLastLogin($id) {
        $query = "UPDATE " . $this->table . " SET last_login = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
    
    // Verify email
     
    public function verifyEmail($id) {
        $query = "UPDATE " . $this->table . " SET email_verified = 1, updated_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    // Get all users (admin function)
    
    public function getAllUsers($limit = 50, $offset = 0) {
        $query = "SELECT id, first_name, last_name, email, phone, user_type, 
                         status, email_verified, created_at, last_login 
                 FROM " . $this->table . " 
                 ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Update user status
     
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table . " SET status = :status, updated_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    // Delete user
     
    public function deleteUser($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    // Generate password reset token
    
    public function generateResetToken($email) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $query = "UPDATE " . $this->table . " 
                 SET reset_token = :token, reset_token_expires = :expires 
                 WHERE email = :email";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expires', $expires);
        $stmt->bindParam(':email', $email);
        
        if ($stmt->execute()) {
            return $token;
        }
        
        return false;
    }
    
    // Reset password with token
    public function resetPassword($token, $newPassword) {
        $query = "SELECT id FROM " . $this->table . " 
                 WHERE reset_token = :token AND reset_token_expires > NOW()";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Update password and clear reset token
            $query = "UPDATE " . $this->table . " 
                     SET password = :password, reset_token = NULL, 
                         reset_token_expires = NULL, updated_at = NOW() 
                     WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':id', $user['id']);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Password reset successful'];
            }
        }
        
        return ['success' => false, 'message' => 'Invalid or expired reset token'];
    }

// Get user's total order count (for dashboard stats)
public function getUserOrderCount($userId) {
    $query = "SELECT COUNT(*) as total FROM orders WHERE user_id = :user_id";
    
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

// Get user's pending order count (for dashboard stats)
public function getUserPendingOrderCount($userId) {
    $query = "SELECT COUNT(*) as total FROM orders 
              WHERE user_id = :user_id AND status = 'pending'";
    
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

// Get user's total spent (for dashboard stats)
public function getUserTotalSpent($userId) {
    $query = "SELECT SUM(total_amount) as total FROM orders 
              WHERE user_id = :user_id AND payment_status = 'paid'";
    
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ?? 0;
}

// Get user's wishlist count (for dashboard stats)
public function getUserWishlistCount($userId) {
    $query = "SELECT COUNT(*) as total FROM wishlist WHERE user_id = :user_id";
    
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

// Get user's inquiry count (for dashboard stats)
public function getUserInquiryCount($userId) {
    $query = "SELECT COUNT(*) as total FROM inquiries WHERE user_id = :user_id";
    
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

// Check if user is a dealer
public function isDealer($userId) {
    $query = "SELECT user_type FROM " . $this->table . " WHERE id = :user_id";
    
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result && $result['user_type'] === 'dealer';
}

// Check if user is admin
public function isAdmin($userId) {
    $query = "SELECT user_type FROM " . $this->table . " WHERE id = :user_id";
    
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result && $result['user_type'] === 'admin';
}

// Get dealers list (for admin or public dealer directory)
public function getDealers($limit = 50, $offset = 0) {
    $query = "SELECT id, first_name, last_name, email, phone, company_name, 
                     city, state, country, created_at 
              FROM " . $this->table . " 
              WHERE user_type = 'dealer' AND status = 'active'
              ORDER BY company_name ASC 
              LIMIT :limit OFFSET :offset";
    
    $stmt = $this->conn->prepare($query);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get user statistics for admin dashboard
public function getUserStatistics() {
    $query = "SELECT 
                COUNT(*) as total_users,
                SUM(CASE WHEN user_type = 'customer' THEN 1 ELSE 0 END) as customers,
                SUM(CASE WHEN user_type = 'dealer' THEN 1 ELSE 0 END) as dealers,
                SUM(CASE WHEN user_type = 'admin' THEN 1 ELSE 0 END) as admins,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_users,
                SUM(CASE WHEN email_verified = 1 THEN 1 ELSE 0 END) as verified_users,
                SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as new_today,
                SUM(CASE WHEN DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as new_this_week
              FROM " . $this->table;
    
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
}
?>