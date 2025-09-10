<?php
/**
 * LOGIN PAGE
 * - Clean, well-structured code
 * - Modern design with good UX
 * - Reusable for other projects
 * - Follows security best practices
 */

// Debug information for learning
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files
require_once dirname(dirname(__DIR__)) . '/config/config.php';

// Initialize variables
$errors = [];
$email = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Basic validation
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required';
    }
    
    // If validation passes, attempt login
    if (empty($errors)) {
        try {
            require_once dirname(dirname(__DIR__)) . '/config/database.php';
            require_once dirname(dirname(__DIR__)) . '/models/User.php';
            
            $database = new Database();
            $db = $database->connect();
            $user = new User($db);
            
            $result = $user->login($email, $password);
            
            if ($result['success']) {
                // Set session data
                $_SESSION['user'] = $result['user'];
                
                // Handle "Remember Me" functionality
                if ($remember) {
                    // Set a secure cookie that lasts 30 days
                    $token = bin2hex(random_bytes(32));
                    setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', true, true);
                    // In production, you'd store this token in the database
                }
                
                // Redirect to intended page
                $redirect = $_GET['redirect'] ?? '/account';
                header('Location: ' . $redirect);
                exit;
            } else {
                $errors[] = $result['message'] ?? 'Invalid email or password';
            }
        } catch (Exception $e) {
            error_log('Login error: ' . $e->getMessage());
            $errors[] = 'Login failed. Please try again.';
        }
    }
}

// Get any session messages
if (isset($_SESSION['error'])) {
    $errors[] = $_SESSION['error'];
    unset($_SESSION['error']);
}

$success = '';
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Helicopter Marketplace</title>
    
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /**
         * LOGIN PAGE STYLES
         * Matches register page design for consistency
         * Clean, modern, reusable design
         */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .auth-container {
            background: #333333;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 107, 53, 0.3);
            width: 100%;
            max-width: 450px;
            padding: 40px;
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .auth-header h1 {
            color: #FF6B35;
            font-size: 2.2rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .auth-header p {
            color: #cccccc;
            font-size: 1rem;
        }
        
        .auth-header a {
            color: #FF6B35;
            text-decoration: none;
        }
        
        .auth-header a:hover {
            text-decoration: underline;
        }
        
        /* Alert Messages */
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            border: 1px solid #28a745;
            color: #28a745;
        }
        
        .alert-error {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid #dc3545;
            color: #dc3545;
        }
        
        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            color: #cccccc;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-group label .required {
            color: #FF6B35;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #555555;
            border-radius: 8px;
            background: #2a2a2a;
            color: #ffffff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #FF6B35;
            box-shadow: 0 0 10px rgba(255, 107, 53, 0.3);
        }
        
        .form-control::placeholder {
            color: #aaaaaa;
        }
        
        /* Checkbox styling */
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .checkbox-group input[type="checkbox"] {
            accent-color: #FF6B35;
        }
        
        .checkbox-group label {
            margin: 0;
            font-size: 0.9rem;
            cursor: pointer;
        }
        
        /* Button Styles */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            width: 100%;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #FF6B35, #FF8C42);
            color: white;
            border: 2px solid transparent;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4);
        }
        
        .btn-outline {
            border: 2px solid #FF6B35;
            color: #FF6B35;
            background: transparent;
        }
        
        .btn-outline:hover {
            background: #FF6B35;
            color: white;
        }
        
        /* Forgot Password Link */
        .forgot-password {
            text-align: center;
            margin: 20px 0;
        }
        
        .forgot-password a {
            color: #FF6B35;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .forgot-password a:hover {
            text-decoration: underline;
        }
        
        /* Action Links */
        .auth-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #444444;
        }
        
        .auth-footer p {
            color: #cccccc;
            margin-bottom: 15px;
        }
        
        .auth-footer a {
            color: #FF6B35;
            text-decoration: none;
            font-weight: 500;
        }
        
        .auth-footer a:hover {
            text-decoration: underline;
        }
        
        /* Demo Credentials Box */
        .demo-credentials {
            background: rgba(23, 162, 184, 0.1);
            border: 1px solid #17a2b8;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .demo-credentials h4 {
            color: #17a2b8;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }
        
        .demo-credentials p {
            color: #cccccc;
            font-size: 0.85rem;
            margin: 5px 0;
        }
        
        .demo-credentials code {
            background: #2a2a2a;
            padding: 2px 6px;
            border-radius: 3px;
            color: #FF6B35;
        }
        
        /* Loading State */
        .btn.loading {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        .btn.loading::after {
            content: '';
            width: 16px;
            height: 16px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 8px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .auth-container {
                padding: 30px 20px;
            }
            
            .auth-header h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <!-- Header -->
        <div class="auth-header">
            <h1>
                <i class="fas fa-helicopter"></i>
                Welcome Back
            </h1>
            <p>Sign in to your helicopter marketplace account</p>
        </div>

        <!-- Success Message -->
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <!-- Error Messages -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <?php foreach ($errors as $error): ?>
                        <div><?= htmlspecialchars($error) ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Demo Credentials (Remove in production) -->
        <div class="demo-credentials">
            <h4><i class="fas fa-info-circle"></i> Demo Credentials</h4>
            <p>Email: <code>demo@helicopter.com</code></p>
            <p>Password: <code>demo123</code></p>
            <p><small>Use these credentials to test the login system</small></p>
        </div>

        <!-- Login Form -->
        <form method="POST" action="" id="loginForm" novalidate>
            <!-- Email Field -->
            <div class="form-group">
                <label for="email">Email Address <span class="required">*</span></label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       class="form-control" 
                       placeholder="Enter your email address"
                       value="<?= htmlspecialchars($email) ?>"
                       required
                       autofocus>
            </div>

            <!-- Password Field -->
            <div class="form-group">
                <label for="password">Password <span class="required">*</span></label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       class="form-control" 
                       placeholder="Enter your password"
                       required>
            </div>

            <!-- Remember Me Checkbox -->
            <div class="checkbox-group">
                <input type="checkbox" 
                       id="remember" 
                       name="remember" 
                       value="1">
                <label for="remember">Remember me for 30 days</label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="fas fa-sign-in-alt"></i>
                Sign In
            </button>
        </form>

        <!-- Forgot Password Link -->
        <div class="forgot-password">
            <a href="/forgot-password">
                <i class="fas fa-key"></i>
                Forgot your password?
            </a>
        </div>

        <!-- Footer -->
        <div class="auth-footer">
            <p>Don't have an account?</p>
            <a href="/register" class="btn btn-outline">
                <i class="fas fa-user-plus"></i>
                Create Account
            </a>
            
            <div style="margin-top: 20px;">
                <a href="/">
                    <i class="fas fa-home"></i>
                    Back to Homepage
                </a>
            </div>
        </div>
    </div>

    <script>
        /**
         * LOGIN FORM ENHANCEMENTS
         * Modern JavaScript for better user experience
         */

        document.addEventListener('DOMContentLoaded', function() {
            initializeLoginForm();
        });

        function initializeLoginForm() {
            const form = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');

            // Form submission handling
            form.addEventListener('submit', handleFormSubmission);

            // Enter key handling for better UX
            emailInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    passwordInput.focus();
                }
            });

            passwordInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    form.submit();
                }
            });

            // Demo credential auto-fill (remove in production)
            addDemoCredentialFiller();

            console.log('Login form initialized successfully');
        }

        function handleFormSubmission(event) {
            const submitBtn = document.getElementById('submitBtn');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');

            // Basic client-side validation
            if (!emailInput.value.trim()) {
                event.preventDefault();
                emailInput.focus();
                showValidationError(emailInput, 'Email is required');
                return;
            }

            if (!passwordInput.value.trim()) {
                event.preventDefault();
                passwordInput.focus();
                showValidationError(passwordInput, 'Password is required');
                return;
            }

            // Add loading state
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;

            // Form will submit normally
        }

        function showValidationError(input, message) {
            input.style.borderColor = '#dc3545';
            
            // Remove existing error message
            const existingError = input.parentNode.querySelector('.error-message');
            if (existingError) {
                existingError.remove();
            }

            // Add error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.style.color = '#dc3545';
            errorDiv.style.fontSize = '0.85rem';
            errorDiv.style.marginTop = '5px';
            errorDiv.textContent = message;
            
            input.parentNode.appendChild(errorDiv);

            // Clear error on input
            input.addEventListener('input', function() {
                input.style.borderColor = '#555555';
                const errorMsg = input.parentNode.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.remove();
                }
            }, { once: true });
        }

        function addDemoCredentialFiller() {
            // Add click handler to demo credentials for easy testing
            const demoBox = document.querySelector('.demo-credentials');
            if (demoBox) {
                demoBox.style.cursor = 'pointer';
                demoBox.title = 'Click to auto-fill demo credentials';
                
                demoBox.addEventListener('click', function() {
                    document.getElementById('email').value = 'demo@helicopter.com';
                    document.getElementById('password').value = 'demo123';
                    
                    // Visual feedback
                    this.style.background = 'rgba(40, 167, 69, 0.1)';
                    this.style.borderColor = '#28a745';
                    
                    setTimeout(() => {
                        this.style.background = 'rgba(23, 162, 184, 0.1)';
                        this.style.borderColor = '#17a2b8';
                    }, 1000);
                });
            }
        }

        // Handle browser back/forward buttons
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                // Reset form state if page was cached
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            }
        });
    </script>
</body>
</html>