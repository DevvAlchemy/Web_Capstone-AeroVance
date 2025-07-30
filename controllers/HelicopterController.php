<?php
/**
 * Helicopter Controller
 * Handles HTTP requests for helicopter operations
 */

require_once '../models/Helicopter.php';
require_once '../config/database.php';

class HelicopterController {
    private $helicopter;
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->helicopter = new Helicopter($this->db);
    }
    
    // Display helicopter catalog page
     
    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        // Build filters from GET parameters
        $filters = [];
        if (isset($_GET['category']) && !empty($_GET['category'])) {
            $filters['category'] = $_GET['category'];
        }
        if (isset($_GET['manufacturer']) && !empty($_GET['manufacturer'])) {
            $filters['manufacturer'] = $_GET['manufacturer'];
        }
        if (isset($_GET['min_price']) && !empty($_GET['min_price'])) {
            $filters['min_price'] = (float)$_GET['min_price'];
        }
        if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
            $filters['max_price'] = (float)$_GET['max_price'];
        }
        if (isset($_GET['condition']) && !empty($_GET['condition'])) {
            $filters['condition'] = $_GET['condition'];
        }
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $filters['search'] = $_GET['search'];
        }
        
        try {
            $helicopters = $this->helicopter->getAllHelicopters($limit, $offset, $filters);
            $manufacturers = $this->helicopter->getManufacturers();
            $categoryStats = $this->helicopter->getCountByCategory($filters);
            
            $data = [
                'helicopters' => $helicopters,
                'manufacturers' => $manufacturers,
                'categoryStats' => $categoryStats,
                'currentPage' => $page,
                'filters' => $filters,
                'totalPages' => $this->calculateTotalPages($filters, $limit)
            ];
            
            $this->loadView('catalog', $data);
            
        } catch (Exception $e) {
            $this->handleError('Error loading helicopters: ' . $e->getMessage());
        }
    }
    
    // Display single helicopter details
     
    public function show($id) {
        try {
            $helicopter = $this->helicopter->getHelicopterById($id);
            
            if (!$helicopter) {
                $this->handleError('Helicopter not found', 404);
                return;
            }
            
            // Get related helicopters (same category)
            $relatedHelicopters = $this->helicopter->getHelicoptersByCategory(
                $helicopter['category'], 4
            );
            
            $data = [
                'helicopter' => $helicopter,
                'relatedHelicopters' => $relatedHelicopters,
                'images' => $this->parseImages($helicopter['images']),
                'specifications' => $this->parseSpecifications($helicopter['specifications'])
            ];
            
            $this->loadView('helicopter-detail', $data);
            
        } catch (Exception $e) {
            $this->handleError('Error loading helicopter details: ' . $e->getMessage());
        }
    }
    
    // Display helicopters by category
     
    public function category($category) {
        $validCategories = ['personal', 'business', 'emergency'];
        
        if (!in_array($category, $validCategories)) {
            $this->handleError('Invalid category', 404);
            return;
        }
        
        try {
            $helicopters = $this->helicopter->getHelicoptersByCategory($category, 20);
            $categoryStats = $this->helicopter->getCountByCategory(['category' => $category]);
            
            $data = [
                'helicopters' => $helicopters,
                'category' => $category,
                'categoryStats' => $categoryStats,
                'categoryTitle' => ucfirst($category) . ' Helicopters'
            ];
            
            $this->loadView('category', $data);
            
        } catch (Exception $e) {
            $this->handleError('Error loading category: ' . $e->getMessage());
        }
    }
    
    // Handle helicopter search via AJAX
     
    public function search() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }
        
        $searchTerm = isset($_POST['search']) ? trim($_POST['search']) : '';
        
        if (empty($searchTerm)) {
            echo json_encode(['helicopters' => []]);
            return;
        }
        
        try {
            $filters = ['search' => $searchTerm];
            $helicopters = $this->helicopter->getAllHelicopters(10, 0, $filters);
            
            // Format response for JSON
            $response = array_map(function($helicopter) {
                return [
                    'id' => $helicopter['id'],
                    'name' => $helicopter['name'],
                    'manufacturer' => $helicopter['manufacturer'],
                    'model' => $helicopter['model'],
                    'price' => number_format($helicopter['price']),
                    'category' => $helicopter['category'],
                    'year' => $helicopter['year'],
                    'image' => $this->getFirstImage($helicopter['images'])
                ];
            }, $helicopters);
            
            header('Content-Type: application/json');
            echo json_encode(['helicopters' => $response]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Search failed']);
        }
    }
    
    // Get featured helicopters for homepage
    public function getFeatured() {
        try {
            $helicopters = $this->helicopter->getFeaturedHelicopters(6);
            return $helicopters;
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Admin: Create new helicopter listing
    
    public function create() {
        if (!$this->isAdmin()) {
            $this->redirectUnauthorized();
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processCreateHelicopter();
        } else {
            $this->loadView('admin/helicopter-create');
        }
    }
    
    // Admin: Edit helicopter listing
     
    public function edit($id) {
        if (!$this->isAdmin()) {
            $this->redirectUnauthorized();
            return;
        }
        
        try {
            $helicopter = $this->helicopter->getHelicopterById($id);
            
            if (!$helicopter) {
                $this->handleError('Helicopter not found', 404);
                return;
            }
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->processUpdateHelicopter($id);
            } else {
                $data = ['helicopter' => $helicopter];
                $this->loadView('admin/helicopter-edit', $data);
            }
            
        } catch (Exception $e) {
            $this->handleError('Error loading helicopter for editing: ' . $e->getMessage());
        }
    }
    
    // Admin: Delete helicopter listing
     
    public function delete($id) {
        if (!$this->isAdmin()) {
            $this->redirectUnauthorized();
            return;
        }
        
        try {
            if ($this->helicopter->deleteHelicopter($id)) {
                $_SESSION['success'] = 'Helicopter deleted successfully';
            } else {
                $_SESSION['error'] = 'Failed to delete helicopter';
            }
            
            header('Location: /admin/helicopters');
            exit;
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error deleting helicopter: ' . $e->getMessage();
            header('Location: /admin/helicopters');
            exit;
        }
    }
    
    // Process helicopter creation
     
    private function processCreateHelicopter() {
        $data = $this->validateHelicopterData($_POST);
        
        if (!$data['valid']) {
            $_SESSION['error'] = implode('<br>', $data['errors']);
            $this->loadView('admin/helicopter-create', ['formData' => $_POST]);
            return;
        }
        
        try {
            $helicopterId = $this->helicopter->createHelicopter($data['data']);
            
            if ($helicopterId) {
                $_SESSION['success'] = 'Helicopter created successfully';
                header('Location: /helicopter/' . $helicopterId);
                exit;
            } else {
                $_SESSION['error'] = 'Failed to create helicopter';
                $this->loadView('admin/helicopter-create', ['formData' => $_POST]);
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error creating helicopter: ' . $e->getMessage();
            $this->loadView('admin/helicopter-create', ['formData' => $_POST]);
        }
    }
    
    // Process helicopter update
    
    private function processUpdateHelicopter($id) {
        $data = $this->validateHelicopterData($_POST);
        
        if (!$data['valid']) {
            $_SESSION['error'] = implode('<br>', $data['errors']);
            header('Location: /admin/helicopter/edit/' . $id);
            exit;
        }
        
        try {
            if ($this->helicopter->updateHelicopter($id, $data['data'])) {
                $_SESSION['success'] = 'Helicopter updated successfully';
                header('Location: /helicopter/' . $id);
                exit;
            } else {
                $_SESSION['error'] = 'Failed to update helicopter';
                header('Location: /admin/helicopter/edit/' . $id);
                exit;
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error updating helicopter: ' . $e->getMessage();
            header('Location: /admin/helicopter/edit/' . $id);
            exit;
        }
    }
    
    //  Validate helicopter form data
    private function validateHelicopterData($postData) {
        $errors = [];
        $data = [];
        
        // Required fields validation
        $requiredFields = [
            'name', 'manufacturer', 'model', 'category', 'price', 
            'year', 'condition', 'description'
        ];
        
        foreach ($requiredFields as $field) {
            if (empty($postData[$field])) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            } else {
                $data[$field] = trim($postData[$field]);
            }
        }
        
        // Validate category
        if (!empty($postData['category']) && 
            !in_array($postData['category'], ['personal', 'business', 'emergency'])) {
            $errors[] = 'Invalid category selected';
        }
        
        // Validate price
        if (!empty($postData['price']) && !is_numeric($postData['price'])) {
            $errors[] = 'Price must be a valid number';
        }
        
        // Validate year
        if (!empty($postData['year']) && 
            (!is_numeric($postData['year']) || $postData['year'] < 1900 || $postData['year'] > date('Y') + 2)) {
            $errors[] = 'Year must be a valid year';
        }
        
        // Optional fields
        $optionalFields = [
            'max_speed', 'range', 'passenger_capacity', 'engine_type', 
            'fuel_capacity', 'specifications', 'images', 'stock_quantity'
        ];
        
        foreach ($optionalFields as $field) {
            $data[$field] = isset($postData[$field]) ? trim($postData[$field]) : '';
        }
        
        $data['status'] = isset($postData['status']) ? $postData['status'] : 'available';
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $data
        ];
    }
    
    /**
     * Helper method to parse image URLs
     */
    private function parseImages($imagesString) {
        if (empty($imagesString)) {
            return [];
        }
        
        return array_filter(array_map('trim', explode(',', $imagesString)));
    }
    
    /**
     * Helper method to get first image
     */
    private function getFirstImage($imagesString) {
        $images = $this->parseImages($imagesString);
        return !empty($images) ? $images[0] : '/assets/images/helicopter-placeholder.jpg';
    }
    
    /**
     * Helper method to parse specifications JSON
     */
    private function parseSpecifications($specificationsString) {
        if (empty($specificationsString)) {
            return [];
        }
        
        $specs = json_decode($specificationsString, true);
        return is_array($specs) ? $specs : [];
    }
    
    // Calculate total pages for pagination
     
    private function calculateTotalPages($filters, $limit) {
        // This would require a count query - simplified for now
        return 10; // Placeholder
    }
    
    /**
     * Load view with data
     */
    private function loadView($view, $data = []) {
        extract($data);
        include "../views/{$view}.php";
    }
    
    /**
     * Handle errors
     */
    private function handleError($message, $code = 500) {
        http_response_code($code);
        $data = ['error' => $message, 'code' => $code];
        $this->loadView('error', $data);
    }
    
    /**
     * Check if user is admin
     */
    private function isAdmin() {
        return isset($_SESSION['user']) && $_SESSION['user']['user_type'] === 'admin';
    }
    
    /**
     * Redirect unauthorized users
     */
    private function redirectUnauthorized() {
        header('Location: /login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}
?>