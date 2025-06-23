<?php
// Central configuration for the Helicopter Marketplace


// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting settings
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Site configuration
define('SITE_NAME', 'Helicopter Marketplace');
define('SITE_URL', $_ENV['SITE_URL'] ?? 'http://localhost');
define('SITE_EMAIL', $_ENV['SITE_EMAIL'] ?? 'info@helicoptermarketplace.com');
define('ADMIN_EMAIL', $_ENV['ADMIN_EMAIL'] ?? 'admin@helicoptermarketplace.com');

// Paths
define('ROOT_PATH', dirname(dirname(__FILE__)));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('ASSETS_PATH', '/assets');

// Database configuration (loaded from environment or defaults)
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'helicopter_marketplace');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');

// Security settings
define('PASSWORD_MIN_LENGTH', 8);
define('SESSION_TIMEOUT', 3600); // 1 hour
define('CSRF_TOKEN_NAME', 'csrf_token');
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// File upload settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
define('MAX_IMAGES_PER_HELICOPTER', 10);

// Pagination settings
define('HELICOPTERS_PER_PAGE', 12);
define('ADMIN_ITEMS_PER_PAGE', 20);

// Email settings
define('SMTP_HOST', $_ENV['SMTP_HOST'] ?? 'localhost');
define('SMTP_PORT', $_ENV['SMTP_PORT'] ?? 587);
define('SMTP_USERNAME', $_ENV['SMTP_USERNAME'] ?? '');
define('SMTP_PASSWORD', $_ENV['SMTP_PASSWORD'] ?? '');
define('SMTP_ENCRYPTION', $_ENV['SMTP_ENCRYPTION'] ?? 'tls');

// Payment settings
define('STRIPE_PUBLIC_KEY', $_ENV['STRIPE_PUBLIC_KEY'] ?? '');
define('STRIPE_SECRET_KEY', $_ENV['STRIPE_SECRET_KEY'] ?? '');
define('PAYPAL_CLIENT_ID', $_ENV['PAYPAL_CLIENT_ID'] ?? '');
define('PAYPAL_CLIENT_SECRET', $_ENV['PAYPAL_CLIENT_SECRET'] ?? '');
define('PAYPAL_ENVIRONMENT', $_ENV['PAYPAL_ENVIRONMENT'] ?? 'sandbox');

// Application settings
define('ENABLE_REGISTRATION', true);
define('REQUIRE_EMAIL_VERIFICATION', true);
define('ENABLE_MAINTENANCE_MODE', false);
define('DEBUG_MODE', $_ENV['DEBUG_MODE'] ?? false);

// Helicopter categories
define('HELICOPTER_CATEGORIES', [
    'personal' => 'Personal Use',
    'business' => 'Business',
    'emergency' => 'Emergency Services'
]);

// Helicopter conditions
define('HELICOPTER_CONDITIONS', [
    'new' => 'New',
    'used' => 'Used',
    'refurbished' => 'Refurbished'
]);

// User types
define('USER_TYPES', [
    'customer' => 'Customer',
    'dealer' => 'Dealer',
    'admin' => 'Administrator'
]);

// Currency settings
define('CURRENCY_SYMBOL', '$');
define('CURRENCY_CODE', 'USD');
define('CURRENCY_POSITION', 'before'); // before or after

// Date/Time settings
define('DEFAULT_TIMEZONE', 'America/New_York');
date_default_timezone_set(DEFAULT_TIMEZONE);

// Theme colors (Orange theme)
define('THEME_COLORS', [
    'primary' => '#FF6B35',      // Vibrant Orange
    'secondary' => '#FF8C42',    // Light Orange
    'accent' => '#FFB627',       // Yellow Orange
    'dark' => '#2C2C2C',         // Dark Gray
    'light' => '#F8F9FA',        // Light Gray
    'success' => '#28A745',      // Green
    'warning' => '#FFC107',      // Amber
    'danger' => '#DC3545',       // Red
    'info' => '#17A2B8'          // Blue
]);

// Cache settings
define('CACHE_ENABLED', true);
define('CACHE_DURATION', 3600); // 1 hour

// Social media links
define('SOCIAL_LINKS', [
    'facebook' => $_ENV['FACEBOOK_URL'] ?? '',
    'twitter' => $_ENV['TWITTER_URL'] ?? '',
    'instagram' => $_ENV['INSTAGRAM_URL'] ?? '',
    'linkedin' => $_ENV['LINKEDIN_URL'] ?? '',
    'youtube' => $_ENV['YOUTUBE_URL'] ?? ''
]);

// API settings
define('API_VERSION', 'v1');
define('API_RATE_LIMIT', 100); // requests per hour
define('API_REQUIRE_AUTH', true);

// Search settings
define('SEARCH_MIN_LENGTH', 3);
define('SEARCH_MAX_RESULTS', 50);

// Image settings
define('IMAGE_QUALITY', 85);
define('THUMBNAIL_WIDTH', 300);
define('THUMBNAIL_HEIGHT', 200);
define('LARGE_IMAGE_WIDTH', 1200);
define('LARGE_IMAGE_HEIGHT', 800);

/**
 * Auto-load environment variables from .env file
 */
function loadEnvironmentVariables() {
    $envFile = ROOT_PATH . '/.env';
    
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            if (strpos($line, '#') === 0) {
                continue; // Skip comments
            }
            
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes if present
                if (preg_match('/^(["\'])(.*)\\1$/', $value, $matches)) {
                    $value = $matches[2];
                }
                
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
}

/**
 * Security helper functions
 */
function generateCSRFToken() {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function validateCSRFToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && 
           hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function isLoggedIn() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
}

function getCurrentUser() {
    return $_SESSION['user'] ?? null;
}

function isAdmin() {
    $user = getCurrentUser();
    return $user && $user['user_type'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('HTTP/1.1 403 Forbidden');
        die('Access denied');
    }
}

/**
 * Utility functions
 */
function formatPrice($price, $includeCurrency = true) {
    $formatted = number_format($price, 0);
    
    if ($includeCurrency) {
        return CURRENCY_POSITION === 'before' 
            ? CURRENCY_SYMBOL . $formatted 
            : $formatted . ' ' . CURRENCY_SYMBOL;
    }
    
    return $formatted;
}

function formatDate($date, $format = 'Y-m-d H:i:s') {
    return date($format, strtotime($date));
}

function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

function getUploadPath($type = 'images') {
    $path = UPLOAD_PATH . '/' . $type;
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
    return $path;
}

function logError($message, $file = 'error.log') {
    $logPath = ROOT_PATH . '/logs';
    if (!is_dir($logPath)) {
        mkdir($logPath, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    file_put_contents("$logPath/$file", $logMessage, FILE_APPEND | LOCK_EX);
}

/**
 * Template helper functions
 */
function getThemeColor($color) {
    return THEME_COLORS[$color] ?? '#000000';
}

function includeView($view, $data = []) {
    extract($data);
    $viewFile = ROOT_PATH . "/views/$view.php";
    
    if (file_exists($viewFile)) {
        include $viewFile;
    } else {
        throw new Exception("View file not found: $view");
    }
}

function asset($path) {
    return ASSETS_PATH . '/' . ltrim($path, '/');
}

// Load environment variables
loadEnvironmentVariables();

// Set timezone
if (isset($_ENV['TIMEZONE'])) {
    date_default_timezone_set($_ENV['TIMEZONE']);
}

// Check maintenance mode
if (ENABLE_MAINTENANCE_MODE && !isAdmin()) {
    http_response_code(503);
    include ROOT_PATH . '/views/maintenance.php';
    exit;
}
?>