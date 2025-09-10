<?php
/**
 * MINIMAL ROUTER - AUTHENTICATION TESTING ONLY
 * 
 * This is a simplified router to test ONLY the login and register pages
 * If these work, we know the pattern for fixing other pages
 * 
 * Student Learning:
 * - Start simple, then build complexity
 * - Isolate problems to find root causes
 * - Test one thing at a time
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle static files first
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$/i', $requestUri)) {
    $filePath = dirname(__DIR__) . $requestUri;
    if (file_exists($filePath)) {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $contentTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg'
        ];
        
        if (isset($contentTypes[$extension])) {
            header('Content-Type: ' . $contentTypes[$extension]);
        }
        
        readfile($filePath);
        exit;
    }
}

// Include config files
require_once '../config/config.php';
require_once '../config/database.php';

// Simple routing function
function route($method, $path, $callback) {
    $currentMethod = $_SERVER['REQUEST_METHOD'];
    $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    // Remove trailing slash
    if ($currentPath !== '/' && substr($currentPath, -1) === '/') {
        $currentPath = rtrim($currentPath, '/');
    }
    
    if ($currentMethod === $method && $currentPath === $path) {
        call_user_func($callback);
        exit;
    }
}

// Debug function
function debug($message) {
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        error_log("Router Debug: " . $message);
    }
}

debug("Request: " . $_SERVER['REQUEST_METHOD'] . " " . $requestUri);

// ============================================================================
// MINIMAL ROUTES - ONLY AUTHENTICATION PAGES
// ============================================================================

// HOME PAGE (simple redirect for now)
route('GET', '/', function() {
    echo "<h1>AEROVANCE</h1>";
    echo "<p><a href='/login'>Login</a> | <a href='/register'>Register</a></p>";
    echo "<p>Testing authentication pages only</p>";
});

// REGISTER PAGE
route('GET', '/register', function() {
    debug("Loading register page");
    
    $filePath = '../views/auth/register.php';
    if (file_exists($filePath)) {
        include $filePath;
    } else {
        echo "Register page not found at: " . realpath($filePath);
        echo "<br>Current directory: " . getcwd();
        echo "<br>Looking for file: " . $filePath;
        echo "<br>File exists: " . (file_exists($filePath) ? 'YES' : 'NO');
    }
});

// REGISTER FORM SUBMISSION
route('POST', '/register', function() {
    debug("Processing register form");
    
    // The register.php file handles its own POST processing
    $filePath = '../views/auth/register.php';
    if (file_exists($filePath)) {
        include $filePath;
    } else {
        echo "Register page not found for form processing";
    }
});

// LOGIN PAGE
route('GET', '/login', function() {
    debug("Loading login page");
    
    $filePath = '../views/auth/login.php';
    if (file_exists($filePath)) {
        include $filePath;
    } else {
        echo "Login page not found at: " . realpath($filePath);
        echo "<br>Current directory: " . getcwd();
        echo "<br>Looking for file: " . $filePath;
        echo "<br>File exists: " . (file_exists($filePath) ? 'YES' : 'NO');
    }
});

// LOGIN FORM SUBMISSION
route('POST', '/login', function() {
    debug("Processing login form");
    
    // The login.php file handles its own POST processing
    $filePath = '../views/auth/login.php';
    if (file_exists($filePath)) {
        include $filePath;
    } else {
        echo "Login page not found for form processing";
    }
});

// LOGOUT
route('GET', '/logout', function() {
    session_unset();
    session_destroy();
    header('Location: /');
    exit;
});

// SIMPLE ACCOUNT PAGE (for testing after login)
route('GET', '/account', function() {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    echo "<h1>Account Dashboard</h1>";
    echo "<p>Welcome, " . htmlspecialchars($_SESSION['user']['first_name'] ?? 'User') . "!</p>";
    echo "<p>Email: " . htmlspecialchars($_SESSION['user']['email'] ?? '') . "</p>";
    echo "<p><a href='/logout'>Logout</a></p>";
    echo "<hr>";
    echo "<h3>Debug Info:</h3>";
    echo "<pre>" . print_r($_SESSION['user'], true) . "</pre>";
});

// TEST ROUTE
route('GET', '/test', function() {
    echo "<h1>Router Test</h1>";
    echo "<p>Current directory: " . getcwd() . "</p>";
    echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";
    echo "<p>Request method: " . $_SERVER['REQUEST_METHOD'] . "</p>";
    echo "<p>Session status: " . (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive') . "</p>";
    
    echo "<h3>File checks:</h3>";
    $files = [
        '../views/auth/login.php',
        '../views/auth/register.php', 
        '../config/config.php',
        '../config/database.php',
        '../models/User.php'
    ];
    
    foreach ($files as $file) {
        echo "<p>$file: " . (file_exists($file) ? '✓ EXISTS' : '✗ MISSING') . "</p>";
    }
});

// ============================================================================
// 404 HANDLER
// ============================================================================

// If we reach here, no route matched
http_response_code(404);
echo "<h1>404 - Route Not Found</h1>";
echo "<p>Requested: " . htmlspecialchars($requestUri) . "</p>";
echo "<h3>Available routes for testing:</h3>";
echo "<ul>";
echo "<li>GET / - Homepage</li>";
echo "<li>GET /register - Registration page</li>";
echo "<li>POST /register - Registration form</li>";
echo "<li>GET /login - Login page</li>";
echo "<li>POST /login - Login form</li>";
echo "<li>GET /logout - Logout</li>";
echo "<li>GET /account - Account page (requires login)</li>";
echo "<li>GET /test - Test route</li>";
echo "</ul>";
echo "<p>Current directory: " . getcwd() . "</p>";
echo "<p>If you're seeing this, the router is working but the specific route wasn't found.</p>";
?>