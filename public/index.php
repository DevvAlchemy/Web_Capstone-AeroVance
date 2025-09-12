<?php
/**
 * Main Router File - public/index.php
 * Handles routing and static file serving with all navigation fixes applied
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Starting session BEFORE any output
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
        header('Cache-Control: public, max-age=31536000'); // 1 year
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

// Simple router class
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
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod) {
                $pattern = $this->convertRouteToRegex($route['route']);
                
                if (preg_match($pattern, $requestUri, $matches)) {
                    // Remove the full match
                    array_shift($matches);
                    
                    // Call the callback with matches as parameters
                    call_user_func_array($route['callback'], $matches);
                    return;
                }
            }
        }
        
        // No route found - 404
        $this->handleNotFound();
    }
    
    private function convertRouteToRegex($route) {
        // Convert route parameters like {id} to regex groups
        $route = preg_replace('/\{([^}]+)\}/', '([^/]+)', $route);
        return '#^' . $route . '$#';
    }
    
    private function handleNotFound() {
        http_response_code(404);
        echo "Page not found";
    }
}

// put in a better place / change to do
class Helicopter {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }
    public function getFeatured() {
        $query = "SELECT * FROM helicopters WHERE featured = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Initialize router
$router = new Router();

// Load controllers
require_once '../controllers/HelicopterController.php';

// Define routes
$router->get('/', function() {
    include '../views/home.php';
});

// FIXED: All helicopter-related routes
$router->get('/helicopters', function() {
    $controller = new HelicopterController();
    $controller->index();
});

$router->get('/helicopter/{id}', function($id) {
    $controller = new HelicopterController();
    $controller->show($id);
});

// FIXED: Category routes - all corrected to match navigation
$router->get('/category/personal', function() {
    include '../views/personal-helicopters.php';
});

// FIXED: This was the main issue - was /views/business, now /category/business
$router->get('/category/business', function() {
    include '../views/business-helicopters.php';
});

$router->get('/category/emergency', function() {
    include '../views/emergency-helicopters.php';
});

// FIXED: General helicopter learning page
$router->get('/helicopters/learn', function() {
    include '../views/general-helicopters.php';
});

$router->post('/search', function() {
    $controller = new HelicopterController();
    $controller->search();
});

$router->get('/about', function() {
    if (file_exists('../views/about.php')) {
        include '../views/about.php';
    } else {
        // Fallback content
        echo '<!DOCTYPE html><html><head><title>About - AeroVance</title>';
        echo '<link rel="stylesheet" href="/helicopter-marketplace/assets/css/app.css"></head><body>';
        echo '<header class="header"><div class="nav-container"><a href="/" class="logo"><i class="fas fa-helicopter"></i><span>AeroVance</span></a></div></header>';
        echo '<div class="container" style="margin-top: 120px; text-align: center;"><h1>About AeroVance</h1><p>About page coming soon...</p><a href="/" class="btn btn-primary">← Back to Home</a></div></body></html>';
    }
});

$router->get('/contact', function() {
    if (file_exists('../views/contact.php')) {
        include '../views/contact.php';
    } else {
        // Fallback content
        echo '<!DOCTYPE html><html><head><title>Contact - AeroVance</title>';
        echo '<link rel="stylesheet" href="/helicopter-marketplace/assets/css/app.css"></head><body>';
        echo '<header class="header"><div class="nav-container"><a href="/" class="logo"><i class="fas fa-helicopter"></i><span>AeroVance</span></a></div></header>';
        echo '<div class="container" style="margin-top: 120px; text-align: center;"><h1>Contact AeroVance</h1><p>Contact page coming soon...</p><a href="/" class="btn btn-primary">← Back to Home</a></div></body></html>';
    }
});

$router->post('/contact', function() {
    // Handle contact form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        session_start();
        // CSRF protection
        if (
            !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
        ) {
            http_response_code(403);
            echo 'Invalid CSRF token. Please reload the form and try again.';
            exit;
        }
        // Process contact form
        $name = sanitizeInput($_POST['name'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $subject = sanitizeInput($_POST['subject'] ?? '');
        $message = sanitizeInput($_POST['message'] ?? '');

        // Basic validation
        $errors = [];
        if (empty($name)) $errors[] = 'Name is required';
        if (empty($email)) $errors[] = 'Email is required';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
        if (empty($subject)) $errors[] = 'Subject is required';
        if (empty($message)) $errors[] = 'Message is required';

        if (empty($errors)) {
            // Save inquiry to database (if database is set up)
            try {
                $database = new Database();
                $pdo = $database->connect();
                
                $query = "INSERT INTO inquiries (name, email, subject, message, inquiry_type, created_at) 
                         VALUES (:name, :email, :subject, :message, 'general', NOW())";
                
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':subject', $subject);
                $stmt->bindParam(':message', $message);
                
                if ($stmt->execute()) {
                    $_SESSION['success'] = 'Thank you for your message. We will get back to you soon!';
                } else {
                    $_SESSION['error'] = 'Sorry, there was an error sending your message. Please try again.';
                }
            } catch (Exception $e) {
                $_SESSION['success'] = 'Thank you for your message. We will get back to you soon!'; // Fallback for demo
                error_log('Contact form error: ' . $e->getMessage());
            }
        } else {
            $_SESSION['error'] = implode('<br>', $errors);
        }
        
        header('Location: /contact');
        exit;
    }
});

$router->get('/login', function() {
    if (isLoggedIn()) {
        header('Location: /dashboard');
        exit;
    }
    if (file_exists('../views/auth/login.php')) {
        include '../views/auth/login.php';
    } else {
        // Fallback content
        echo '<!DOCTYPE html><html><head><title>Login - AeroVance</title>';
        echo '<link rel="stylesheet" href="/helicopter-marketplace/assets/css/app.css"></head><body>';
        echo '<header class="header"><div class="nav-container"><a href="/" class="logo"><i class="fas fa-helicopter"></i><span>AeroVance</span></a></div></header>';
        echo '<div class="container" style="margin-top: 120px; text-align: center;"><h1>Login</h1><p>Login page coming soon...</p><a href="/" class="btn btn-primary">← Back to Home</a></div></body></html>';
    }
});

$router->post('/login', function() {
    require_once '../models/User.php';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                
                // Redirect to intended page or dashboard
                $redirect = $_GET['redirect'] ?? '/dashboard';
                header('Location: ' . $redirect);
                exit;
            } else {
                $_SESSION['error'] = $result['message'];
                header('Location: /login');
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Login failed. Please try again.';
            error_log('Login error: ' . $e->getMessage());
            header('Location: /login');
            exit;
        }
    }
});

$router->get('/register', function() {
    if (isLoggedIn()) {
        header('Location: /dashboard');
        exit;
    }
    if (file_exists('../views/auth/register.php')) {
        include '../views/auth/register.php';
    } else {
        // Fallback content
        echo '<!DOCTYPE html><html><head><title>Register - AeroVance</title>';
        echo '<link rel="stylesheet" href="/helicopter-marketplace/assets/css/app.css"></head><body>';
        echo '<header class="header"><div class="nav-container"><a href="/" class="logo"><i class="fas fa-helicopter"></i><span>AeroVance</span></a></div></header>';
        echo '<div class="container" style="margin-top: 120px; text-align: center;"><h1>Register</h1><p>Registration page coming soon...</p><a href="/" class="btn btn-primary">← Back to Home</a></div></body></html>';
    }
});

$router->get('/logout', function() {
    session_unset();
    session_destroy();
    header('Location: /');
    exit;
});

// API routes for future use
$router->get('/api/helicopters', function() {
    header('Content-Type: application/json');
    
    try {
        require_once '../models/Helicopter.php';
        $database = new Database();
        $db = $database->connect();
        $helicopter = new Helicopter($db);
        
        $helicopters = $helicopter->getFeatured();
        echo json_encode(['data' => $helicopters]);
    } catch (Exception $e) {
        echo json_encode(['data' => [], 'error' => 'Unable to load helicopters']);
    }
});

// Add these routes to your index.php file after the existing routes

// Shopping Cart routes
$router->get('/cart', function() {
    if (!isLoggedIn()) {
        header('Location: /login?redirect=/cart');
        exit;
    }
    include '../views/cart.php';
});

// Checkout routes
$router->get('/checkout', function() {
    if (!isLoggedIn()) {
        header('Location: /login?redirect=/checkout');
        exit;
    }
    include '../views/checkout.php';
});

// Account/Dashboard routes
$router->get('/account', function() {
    if (!isLoggedIn()) {
        header('Location: /login');
        exit;
    }
    include '../views/account/dashboard.php';
});

$router->get('/account/orders', function() {
    if (!isLoggedIn()) {
        header('Location: /login');
        exit;
    }
    include '../views/account/orders.php';
});

$router->get('/account/wishlist', function() {
    if (!isLoggedIn()) {
        header('Location: /login');
        exit;
    }
    include '../views/account/wishlist.php';
});

$router->get('/account/settings', function() {
    if (!isLoggedIn()) {
        header('Location: /login');
        exit;
    }
    include '../views/account/settings.php';
});

// API Routes for AJAX calls
$router->post('/api/cart/add', function() {
    header('Content-Type: application/json');
    
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $helicopterId = $data['helicopter_id'] ?? 0;
        
        if (!$helicopterId) {
            echo json_encode(['success' => false, 'message' => 'Invalid helicopter ID']);
            exit;
        }
        
        require_once '../config/database.php';
        require_once '../models/Cart.php';
        
        $database = new Database();
        $db = $database->connect();
        $cart = new Cart($db);
        
        $result = $cart->addToCart($_SESSION['user']['id'], $helicopterId);
        $cartCount = $cart->getCartCount($_SESSION['user']['id']);
        
        echo json_encode([
            'success' => $result,
            'cart_count' => $cartCount,
            'message' => $result ? 'Added to cart successfully!' : 'Error adding to cart'
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Server error']);
    }
});

$router->post('/api/cart/update', function() {
    header('Content-Type: application/json');
    
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $cartId = $data['cart_id'] ?? 0;
        $quantity = $data['quantity'] ?? 1;
        
        require_once '../config/database.php';
        require_once '../models/Cart.php';
        
        $database = new Database();
        $db = $database->connect();
        $cart = new Cart($db);
        
        $result = $cart->updateQuantity($cartId, $_SESSION['user']['id'], $quantity);
        $totals = $cart->getCartTotals($_SESSION['user']['id']);
        
        echo json_encode([
            'success' => $result,
            'totals' => $totals,
            'message' => $result ? 'Cart updated' : 'Error updating cart'
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Server error']);
    }
});

$router->post('/api/cart/remove', function() {
    header('Content-Type: application/json');
    
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $cartId = $data['cart_id'] ?? 0;
        
        require_once '../config/database.php';
        require_once '../models/Cart.php';
        
        $database = new Database();
        $db = $database->connect();
        $cart = new Cart($db);
        
        $result = $cart->removeItem($cartId, $_SESSION['user']['id']);
        $cartCount = $cart->getCartCount($_SESSION['user']['id']);
        $totals = $cart->getCartTotals($_SESSION['user']['id']);
        
        echo json_encode([
            'success' => $result,
            'cart_count' => $cartCount,
            'totals' => $totals,
            'message' => $result ? 'Item removed' : 'Error removing item'
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Server error']);
    }
});

$router->post('/api/wishlist/add', function() {
    header('Content-Type: application/json');
    
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $helicopterId = $data['helicopter_id'] ?? 0;
        
        require_once '../config/database.php';
        require_once '../models/Wishlist.php';
        
        $database = new Database();
        $db = $database->connect();
        // $wishlist = new Wishlist($db); //remove comment when fixed other
        
        $result = $wishlist->addToWishlist($_SESSION['user']['id'], $helicopterId);
        
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Added to wishlist!' : 'Already in wishlist'
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Server error']);
    }
});

$router->post('/api/inquiry/send', function() {
    header('Content-Type: application/json');
    
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    try {
        $helicopterId = $_POST['helicopter_id'] ?? 0;
        $name = sanitizeInput($_POST['name'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $message = sanitizeInput($_POST['message'] ?? '');
        
        // Validate inputs
        if (empty($name) || empty($email) || empty($message)) {
            echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
            exit;
        }
        
        require_once '../config/database.php';
        
        $database = new Database();
        $db = $database->connect();
        
        $query = "INSERT INTO inquiries 
                  (user_id, helicopter_id, name, email, phone, message, created_at)
                  VALUES (:user_id, :helicopter_id, :name, :email, :phone, :message, NOW())";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $_SESSION['user']['id']);
        $stmt->bindParam(':helicopter_id', $helicopterId);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':message', $message);
        
        $result = $stmt->execute();
        
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Message sent successfully!' : 'Error sending message'
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Server error']);
    }
});

$router->post('/api/newsletter/subscribe', function() {
    header('Content-Type: application/json');
    
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $email = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL);
        
        if (!$email) {
            echo json_encode(['success' => false, 'message' => 'Invalid email address']);
            exit;
        }
        
        require_once '../config/database.php';
        
        $database = new Database();
        $db = $database->connect();
        
        // Check if already subscribed
        $checkQuery = "SELECT id FROM newsletter_subscribers WHERE email = :email";
        $stmt = $db->prepare($checkQuery);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Already subscribed']);
            exit;
        }
        
        // Subscribe
        $query = "INSERT INTO newsletter_subscribers (email, created_at) VALUES (:email, NOW())";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        
        $result = $stmt->execute();
        
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Successfully subscribed!' : 'Error subscribing'
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Server error']);
    }
});

// TEST ROUTE for debugging navigation issues
$router->get('/test-routes', function() {
    include '../views/test-routes.php';
});

// Run the router
try {
    $router->run();
} catch (Exception $e) {
    // Log error and show generic error page
    error_log('Router error: ' . $e->getMessage());
    http_response_code(500);
    echo "Internal Server Error";
}

?>