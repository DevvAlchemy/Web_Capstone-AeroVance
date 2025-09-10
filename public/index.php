<?php
/**
 * CORRECTED ROUTER BASED ON YOUR ACTUAL FILE STRUCTURE
 * 
 * Fixed issues:
 * 1. Handles both standalone HTML files and PHP include files
 * 2. Proper error handling and debugging
 * 3. Correct include paths for all view types
 * 4. Session management
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session BEFORE any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle static file requests FIRST (before any routing)
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Check if request is for a static asset
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$/i', $requestUri)) {
    // Build the file path relative to the project root
    $filePath = dirname(__DIR__) . $requestUri;
    
    if (file_exists($filePath)) {
        // Set appropriate content type
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $contentTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'ico' => 'image/x-icon',
            'svg' => 'image/svg+xml',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject'
        ];
        
        if (isset($contentTypes[$extension])) {
            header('Content-Type: ' . $contentTypes[$extension]);
        }
        
        // Set cache headers for static files
        header('Cache-Control: public, max-age=31536000');
        header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));
        
        // Output the file
        readfile($filePath);
        exit;
    } else {
        // File not found
        header('HTTP/1.0 404 Not Found');
        echo "File not found: " . htmlspecialchars($requestUri);
        exit;
    }
}

// Include configuration
require_once '../config/config.php';
require_once '../config/database.php';

// Enhanced router class
class Router {
    private $routes = [];
    private $basePath = '';
    
    public function __construct($basePath = '') {
        $this->basePath = $basePath;
    }
    
    public function get($route, $callback) {
        $this->addRoute('GET', $route, $callback);
    }
    
    public function post($route, $callback) {
        $this->addRoute('POST', $route, $callback);
    }
    
    private function addRoute($method, $route, $callback) {
        $this->routes[] = [
            'method' => $method,
            'route' => $route,
            'callback' => $callback
        ];
    }
    
    public function run() {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        
        // Remove base path
        if ($this->basePath) {
            $requestUri = substr($requestUri, strlen($this->basePath));
        }
        
        // Remove trailing slash except for root
        if ($requestUri !== '/' && substr($requestUri, -1) === '/') {
            $requestUri = substr($requestUri, 0, -1);
        }
        
        // Debug logging
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            error_log("Router: Trying to match $requestMethod $requestUri");
        }
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod) {
                $pattern = $this->convertRouteToRegex($route['route']);
                
                if (preg_match($pattern, $requestUri, $matches)) {
                    // Remove the full match
                    array_shift($matches);
                    
                    if (defined('DEBUG_MODE') && DEBUG_MODE) {
                        error_log("Router: Matched route " . $route['route']);
                    }
                    
                    try {
                        // Call the callback with matches as parameters
                        call_user_func_array($route['callback'], $matches);
                        return;
                    } catch (Exception $e) {
                        error_log("Route handler error: " . $e->getMessage());
                        http_response_code(500);
                        echo "Internal server error: " . $e->getMessage();
                        return;
                    }
                }
            }
        }
        
        // No route found - 404
        $this->handleNotFound($requestUri);
    }
    
    private function convertRouteToRegex($route) {
        // Convert route parameters like {id} to regex groups
        $route = preg_replace('/\{([^}]+)\}/', '([^/]+)', $route);
        return '#^' . $route . '$#';
    }
    
    private function handleNotFound($requestUri) {
        http_response_code(404);
        
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            echo "<h1>404 - Route Not Found</h1>";
            echo "<p><strong>Requested URI:</strong> " . htmlspecialchars($requestUri) . "</p>";
            echo "<h3>Available Routes:</h3><ul>";
            foreach ($this->routes as $route) {
                echo "<li>{$route['method']} {$route['route']}</li>";
            }
            echo "</ul>";
            echo "<p><strong>Working directory:</strong> " . getcwd() . "</p>";
            echo "<p><strong>Session data:</strong> " . (isset($_SESSION['user']) ? 'User logged in' : 'No user session') . "</p>";
        } else {
            // Try to include a 404 page if it exists
            if (file_exists('../views/errors/404.php')) {
                include '../views/errors/404.php';
            } else {
                echo "Page not found";
            }
        }
    }
}

// Initialize router
$router = new Router();

// Load controllers
if (file_exists('../controllers/HelicopterController.php')) {
    require_once '../controllers/HelicopterController.php';
}

// ============================================================================
// MAIN ROUTES
// ============================================================================

// HOME PAGE
$router->get('/', function() {
    if (file_exists('../views/home.php')) {
        include '../views/home.php';
    } else {
        echo "Home page not found";
    }
});

// HELICOPTER ROUTES
$router->get('/helicopters', function() {
    if (class_exists('HelicopterController')) {
        $controller = new HelicopterController();
        $controller->index();
    } else {
        // Fallback to direct include
        if (file_exists('../views/catalog.php')) {
            include '../views/catalog.php';
        } else {
            echo "Catalog page not found";
        }
    }
});

$router->get('/helicopter/{id}', function($id) {
    // Validate ID
    if (!is_numeric($id) || $id <= 0) {
        http_response_code(404);
        echo "Invalid helicopter ID";
        return;
    }
    
    $_GET['id'] = $id; // Pass ID to view
    
    if (class_exists('HelicopterController')) {
        $controller = new HelicopterController();
        $controller->show($id);
    } else {
        // Fallback to direct include
        if (file_exists('../views/helicopter-detail.php')) {
            include '../views/helicopter-detail.php';
        } else {
            echo "Helicopter detail page not found";
        }
    }
});

$router->get('/category/{category}', function($category) {
    $validCategories = ['personal', 'business', 'emergency'];
    
    if (!in_array($category, $validCategories)) {
        http_response_code(404);
        echo "Invalid category";
        return;
    }
    
    $_GET['category'] = $category; // Pass category to view
    
    if (class_exists('HelicopterController')) {
        $controller = new HelicopterController();
        $controller->category($category);
    } else {
        // Fallback to catalog page
        if (file_exists('../views/catalog.php')) {
            include '../views/catalog.php';
        } else {
            echo "Category page not found";
        }
    }
});

// STATIC PAGES
$router->get('/about', function() {
    if (file_exists('../views/about.php')) {
        include '../views/about.php';
    } else {
        echo "About page not found at ../views/about.php";
    }
});

$router->get('/contact', function() {
    if (file_exists('../views/contact.php')) {
        include '../views/contact.php';
    } else {
        echo "Contact page not found at ../views/contact.php";
    }
});

// AUTHENTICATION ROUTES
$router->get('/login', function() {
    if (isLoggedIn()) {
        header('Location: /account');
        exit;
    }
    
    if (file_exists('../views/auth/login.php')) {
        include '../views/auth/login.php';
    } else {
        echo "Login page not found at ../views/auth/login.php";
    }
});

$router->post('/login', function() {
    if (file_exists('../models/User.php')) {
        require_once '../models/User.php';
        
        $email = sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email and password are required';
            header('Location: /login');
            exit;
        }
        
        try {
            $database = new Database();
            $db = $database->connect();
            $user = new User($db);
            
            $result = $user->login($email, $password);
            
            if ($result['success']) {
                $_SESSION['user'] = $result['user'];
                $redirect = $_GET['redirect'] ?? '/account';
                header('Location: ' . $redirect);
            } else {
                $_SESSION['error'] = $result['message'];
                header('Location: /login');
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Login failed. Please try again.';
            error_log('Login error: ' . $e->getMessage());
            header('Location: /login');
        }
    } else {
        $_SESSION['error'] = 'User model not found';
        header('Location: /login');
    }
    exit;
});

$router->get('/register', function() {
    if (isLoggedIn()) {
        header('Location: /account');
        exit;
    }
    
    if (file_exists('../views/auth/register.php')) {
        include '../views/auth/register.php';
    } else {
        echo "Register page not found at ../views/auth/register.php";
    }
});

$router->get('/logout', function() {
    session_unset();
    session_destroy();
    header('Location: /');
    exit;
});

// ============================================================================
// ACCOUNT/DASHBOARD ROUTES (CRITICAL FIXES)
// ============================================================================

// Main account/dashboard page
$router->get('/account', function() {
    if (!isLoggedIn()) {
        header('Location: /login?redirect=/account');
        exit;
    }
    
    // Check if dashboard file exists
    if (file_exists('../views/account/dashboard.php')) {
        include '../views/account/dashboard.php';
    } else {
        echo "Dashboard not found at ../views/account/dashboard.php<br>";
        echo "Current directory: " . getcwd() . "<br>";
        echo "Looking for: " . realpath('../views/account/dashboard.php') . "<br>";
        echo "File exists check: " . (file_exists('../views/account/dashboard.php') ? 'YES' : 'NO');
    }
});

$router->get('/dashboard', function() {
    if (!isLoggedIn()) {
        header('Location: /login?redirect=/dashboard');
        exit;
    }
    // Redirect to /account for consistency
    header('Location: /account');
    exit;
});

// Profile page
$router->get('/account/profile', function() {
    if (!isLoggedIn()) {
        header('Location: /login?redirect=/account/profile');
        exit;
    }
    
    if (file_exists('../views/account/profile.php')) {
        include '../views/account/profile.php';
    } else {
        echo "Profile page not found at ../views/account/profile.php<br>";
        echo "Current directory: " . getcwd() . "<br>";
        echo "Looking for: " . realpath('../views/account/profile.php') . "<br>";
        echo "File exists check: " . (file_exists('../views/account/profile.php') ? 'YES' : 'NO');
    }
});

// Order detail page
$router->get('/account/order/{id}', function($id) {
    if (!isLoggedIn()) {
        header('Location: /login?redirect=/account/order/' . $id);
        exit;
    }
    
    // Validate ID
    if (!is_numeric($id) || $id <= 0) {
        http_response_code(404);
        echo "Invalid order ID";
        return;
    }
    
    $_GET['id'] = $id; // Pass ID to view
    
    if (file_exists('../views/account/order-details.php')) {
        include '../views/account/order-details.php';
    } else {
        echo "Order details page not found at ../views/account/order-details.php<br>";
        echo "Order ID: $id<br>";
        echo "Current directory: " . getcwd() . "<br>";
        echo "Looking for: " . realpath('../views/account/order-details.php') . "<br>";
        echo "File exists check: " . (file_exists('../views/account/order-details.php') ? 'YES' : 'NO');
    }
});

// Other account routes
$router->get('/account/orders', function() {
    if (!isLoggedIn()) {
        header('Location: /login');
        exit;
    }
    
    if (file_exists('../views/account/orders.php')) {
        include '../views/account/orders.php';
    } else {
        echo "Orders page not found at ../views/account/orders.php";
    }
});

$router->get('/account/wishlist', function() {
    if (!isLoggedIn()) {
        header('Location: /login');
        exit;
    }
    
    if (file_exists('../views/account/wishlist.php')) {
        include '../views/account/wishlist.php';
    } else {
        echo "Wishlist page not found at ../views/account/wishlist.php";
    }
});

$router->get('/account/settings', function() {
    if (!isLoggedIn()) {
        header('Location: /login');
        exit;
    }
    
    if (file_exists('../views/account/settings.php')) {
        include '../views/account/settings.php';
    } else {
        echo "Settings page not found at ../views/account/settings.php";
    }
});

// SHOPPING CART
$router->get('/cart', function() {
    if (!isLoggedIn()) {
        header('Location: /login?redirect=/cart');
        exit;
    }
    
    if (file_exists('../views/cart.php')) {
        include '../views/cart.php';
    } else {
        echo "Cart page not found at ../views/cart.php";
    }
});

// CHECKOUT
$router->get('/checkout', function() {
    if (!isLoggedIn()) {
        header('Location: /login?redirect=/checkout');
        exit;
    }
    
    if (file_exists('../views/checkout.php')) {
        include '../views/checkout.php';
    } else {
        echo "Checkout page not found at ../views/checkout.php";
    }
});

// TEST ROUTE (for debugging)
$router->get('/test-paths', function() {
    include '../views/test-paths.php';
});

// SEARCH
$router->post('/search', function() {
    $query = http_build_query($_POST);
    header('Location: /helicopters' . ($query ? ('?' . $query) : ''));
    exit;
});

// ============================================================================
// API ROUTES (Basic structure)
// ============================================================================

// API routes would go here...
$router->get('/api/helicopters', function() {
    header('Content-Type: application/json');
    echo json_encode(['message' => 'API endpoint working']);
});

// ============================================================================
// RUN THE ROUTER
// ============================================================================

try {
    $router->run();
} catch (Exception $e) {
    error_log('Router error: ' . $e->getMessage());
    http_response_code(500);
    
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        echo "<h1>Router Error</h1>";
        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        echo "Internal Server Error";
    }
}
?>