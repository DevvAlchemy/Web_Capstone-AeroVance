<?php
/**
 * Main Entry Point for Helicopter Marketplace
 * Handles routing and static file serving
 */

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

// Initialize router
$router = new Router();

// Load controllers
require_once '../controllers/HelicopterController.php';

// Define routes
$router->get('/', function() {
    include '../views/home.php';
});

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

$router->post('/search', function() {
    $controller = new HelicopterController();
    $controller->search();
});

$router->get('/about', function() {
    include '../views/about.php';
});

$router->get('/contact', function() {
    include '../views/contact.php';
});

$router->post('/contact', function() {
    // Handle contact form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    include '../views/auth/login.php';
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
    include '../views/auth/register.php';
});

$router->get('/logout', function() {
    session_destroy();
    header('Location: /');
    exit;
});

$router->get('/dashboard', function() {
    requireLogin();
    include '../views/dashboard.php';
});

// API routes
$router->get('/api/helicopters', function() {
    header('Content-Type: application/json');
    
    try {
        $controller = new HelicopterController();
        $helicopters = $controller->getFeatured();
        echo json_encode(['data' => $helicopters]);
    } catch (Exception $e) {
        echo json_encode(['data' => [], 'error' => 'Unable to load helicopters']);
    }
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