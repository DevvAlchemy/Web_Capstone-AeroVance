<?php
/**
* HANDLES ALL ROUTING
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Enhanced router class with better debugging
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
                        echo "Internal server error";
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
        } else {
            echo "Page not found";
        }
    }
}

// Initialize router
$router = new Router();

// Load controllers (only once!)
require_once '../controllers/HelicopterController.php';

// ============================================================================
// MAIN ROUTES (no duplicates!)
// ============================================================================

// HOME PAGE
$router->get('/', function() {
    include '../views/home.php';
});

// HELICOPTER ROUTES
$router->get('/helicopters', function() {
    $controller = new HelicopterController();
    $controller->index();
});

$router->get('/helicopter/{id}', function($id) {
    $controller = new HelicopterController();
    $controller->show($id);
});

$router->get('/category/{category}', function($category) {
    $controller = new HelicopterController();
    $controller->category($category);
});

// SEARCH
$router->post('/search', function() {
    // Handle search - redirect to catalog with all POST params as query string
    $query = http_build_query($_POST);
    header('Location: /helicopters' . ($query ? ('?' . $query) : ''));
    exit;
});

// STATIC PAGES
$router->get('/about', function() {
    include '../views/about.php';
});

$router->get('/contact', function() {
    include '../views/contact.php';
});

$router->post('/contact', function() {
    // Handle contact form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        session_start();
        
        // Basic CSRF protection (you can enhance this)
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
            // Save inquiry to database
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
                $_SESSION['success'] = 'Thank you for your message. We will get back to you soon!'; // Fallback
                error_log('Contact form error: ' . $e->getMessage());
            }
        } else {
            $_SESSION['error'] = implode('<br>', $errors);
        }
        
        header('Location: /contact');
        exit;
    }
});

// AUTHENTICATION ROUTES
$router->get('/login', function() {
    if (isLoggedIn()) {
        header('Location: /dashboard');
        exit;
    }
    include '../views/auth/login.php';
});

$router->post('/login', function() {
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
            
            // Redirect to intended page or dashboard
            $redirect = $_GET['redirect'] ?? '/account';
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
});

$router->get('/register', function() {
    if (isLoggedIn()) {
        header('Location: /account');
        exit;
    }
    include '../views/auth/register.php';
});

$router->get('/logout', function() {
    session_unset();
    session_destroy();
    header('Location: /');
    exit;
});

// ============================================================================
// ACCOUNT/DASHBOARD ROUTES (FIXED!)
// ============================================================================

// Main dashboard/account page
$router->get('/account', function() {
    if (!isLoggedIn()) {
        header('Location: /login?redirect=/account');
        exit;
    }
    // Since your dashboard.php file expects to be in views/account/dashboard.php
    // but it's written as if it's in the account folder, we'll include it directly
    include '../views/account/dashboard.php';
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

// FIXED: Profile page route (this was missing!)
$router->get('/account/profile', function() {
    if (!isLoggedIn()) {
        header('Location: /login?redirect=/account/profile');
        exit;
    }
    // Include the profile page
    include '../views/account/profile.php';
});

// FIXED: Order detail page route (this was missing the proper include!)
$router->get('/account/order/{id}', function($id) {
    if (!isLoggedIn()) {
        header('Location: /login?redirect=/account/order/' . $id);
        exit;
    }
    // Pass $id to the view - your order-details.php expects this
    $_GET['id'] = $id;
    include '../views/account/order-details.php';
});

// Other account routes
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

// ============================================================================
// SHOPPING CART ROUTES
// ============================================================================

$router->get('/cart', function() {
    if (!isLoggedIn()) {
        header('Location: /login?redirect=/cart');
        exit;
    }
    include '../views/cart.php';
});

$router->get('/checkout', function() {
    if (!isLoggedIn()) {
        header('Location: /login?redirect=/checkout');
        exit;
    }
    include '../views/checkout.php';
});

// ============================================================================
// API ROUTES FOR AJAX CALLS
// ============================================================================

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
        
        require_once '../models/Wishlist.php';
        $database = new Database();
        $db = $database->connect();
        $wishlist = new Wishlist($db);
        
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
        
        if (empty($name) || empty($email) || empty($message)) {
            echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
            exit;
        }
        
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

// ============================================================================
// RUN THE ROUTER
// ============================================================================

try {
    $router->run();
} catch (Exception $e) {
    // Log error and show generic error page
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