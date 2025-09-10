<?php
/**
 * REGISTER PAGE 
 * - Reusable for other projects
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
require_once dirname(dirname(__DIR__)) . '/config/database.php';

// Initialize variables for form handling
$errors = [];
$success = '';
$formData = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input data
    $formData = [
        'first_name' => sanitizeInput($_POST['first_name'] ?? ''),
        'last_name' => sanitizeInput($_POST['last_name'] ?? ''),
        'email' => sanitizeInput($_POST['email'] ?? ''),
        'phone' => sanitizeInput($_POST['phone'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? '',
        'user_type' => $_POST['user_type'] ?? 'customer',
        'terms' => isset($_POST['terms'])
    ];
    
    // Validation rules
    if (empty($formData['first_name'])) {
        $errors[] = 'First name is required';
    }
    
    if (empty($formData['last_name'])) {
        $errors[] = 'Last name is required';
    }
    
    if (empty($formData['email'])) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }
    
    if (empty($formData['password'])) {
        $errors[] = 'Password is required';
    } elseif (strlen($formData['password']) < 8) {
        $errors[] = 'Password must be at least 8 characters long';
    }
    
    if ($formData['password'] !== $formData['confirm_password']) {
        $errors[] = 'Passwords do not match';
    }
    
    if (!$formData['terms']) {
        $errors[] = 'You must accept the terms and conditions';
    }
    
    // If no errors, attempt to register user
    if (empty($errors)) {
        try {
            require_once dirname(dirname(__DIR__)) . '/models/User.php';
            
            $database = new Database();
            $db = $database->connect();
            $user = new User($db);
            
            // Check if email already exists
            if ($user->emailExists($formData['email'])) {
                $errors[] = 'An account with this email already exists';
            } else {
                // Create user account
                $userData = [
                    'first_name' => $formData['first_name'],
                    'last_name' => $formData['last_name'],
                    'email' => $formData['email'],
                    'phone' => $formData['phone'],
                    'password' => $formData['password'],
                    'user_type' => $formData['user_type'],
                    'address' => '',
                    'city' => '',
                    'state' => '',
                    'zip_code' => '',
                    'country' => '',
                    'pilot_license' => '',
                    'company_name' => '',
                    'tax_id' => ''
                ];
                
                $result = $user->register($userData);
                
                if ($result['success']) {
                    $success = 'Account created successfully! You can now log in.';
                    $formData = []; // Clear form data
                } else {
                    $errors[] = $result['message'];
                }
            }
        } catch (Exception $e) {
            error_log('Registration error: ' . $e->getMessage());
            $errors[] = 'Registration failed. Please try again.';
        }
    }
}

// Get any session messages
if (isset($_SESSION['error'])) {
    $errors[] = $_SESSION['error'];
    unset($_SESSION['error']);
}

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
    <title>Create Account - AeroVance</title>
    
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /**
         * AUTHENTICATION PAGE STYLES
         * Orange theme for AeroVance 
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
            max-width: 500px;
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
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
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
        
        /* Select dropdown styling */
        select.form-control {
            cursor: pointer;
        }
        
        /* Checkbox styling */
        .checkbox-group {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-top: 10px;
        }
        
        .checkbox-group input[type="checkbox"] {
            accent-color: #FF6B35;
            margin-top: 3px;
        }
        
        .checkbox-group label {
            margin: 0;
            font-size: 0.9rem;
            line-height: 1.4;
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
        
        /* Password Requirements */
        .password-requirements {
            background: rgba(255, 107, 53, 0.05);
            border: 1px solid rgba(255, 107, 53, 0.2);
            border-radius: 6px;
            padding: 12px;
            margin-top: 8px;
            font-size: 0.85rem;
        }
        
        .password-requirements h5 {
            color: #FF6B35;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        
        .password-requirements ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .password-requirements li {
            color: #cccccc;
            margin-bottom: 3px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .password-requirements li i {
            color: #888888;
            font-size: 0.7rem;
        }
        
        .password-requirements li.valid i {
            color: #28a745;
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
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .auth-container {
                padding: 30px 20px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .auth-header h1 {
                font-size: 1.8rem;
            }
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
    </style>
</head>
<body>
    <div class="auth-container">
        <!-- Header -->
        <div class="auth-header">
            <h1>
                <i class="fas fa-helicopter"></i>
                Create Account
            </h1>
            <p>Join the premier AeroVance</p>
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

        <!-- Registration Form -->
        <form method="POST" action="" id="registerForm" novalidate>
            <!-- Name Fields -->
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name <span class="required">*</span></label>
                    <input type="text" 
                           id="first_name" 
                           name="first_name" 
                           class="form-control" 
                           placeholder="Enter your first name"
                           value="<?= htmlspecialchars($formData['first_name'] ?? '') ?>"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="last_name">Last Name <span class="required">*</span></label>
                    <input type="text" 
                           id="last_name" 
                           name="last_name" 
                           class="form-control" 
                           placeholder="Enter your last name"
                           value="<?= htmlspecialchars($formData['last_name'] ?? '') ?>"
                           required>
                </div>
            </div>

            <!-- Email Field -->
            <div class="form-group">
                <label for="email">Email Address <span class="required">*</span></label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       class="form-control" 
                       placeholder="Enter your email address"
                       value="<?= htmlspecialchars($formData['email'] ?? '') ?>"
                       required>
            </div>

            <!-- Phone Field -->
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" 
                       id="phone" 
                       name="phone" 
                       class="form-control" 
                       placeholder="Enter your phone number"
                       value="<?= htmlspecialchars($formData['phone'] ?? '') ?>">
            </div>

            <!-- Account Type -->
            <div class="form-group">
                <label for="user_type">Account Type <span class="required">*</span></label>
                <select id="user_type" name="user_type" class="form-control" required>
                    <option value="customer" <?= ($formData['user_type'] ?? 'customer') === 'customer' ? 'selected' : '' ?>>
                        Customer - Buy helicopters
                    </option>
                    <option value="dealer" <?= ($formData['user_type'] ?? '') === 'dealer' ? 'selected' : '' ?>>
                        Dealer - Sell helicopters
                    </option>
                </select>
            </div>

            <!-- Password Fields -->
            <div class="form-group">
                <label for="password">Password <span class="required">*</span></label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       class="form-control" 
                       placeholder="Create a strong password"
                       required>
                
                <!-- Password Requirements -->
                <div class="password-requirements">
                    <h5>Password Requirements:</h5>
                    <ul id="password-requirements">
                        <li id="length-req">
                            <i class="fas fa-circle"></i>
                            At least 8 characters long
                        </li>
                        <li id="uppercase-req">
                            <i class="fas fa-circle"></i>
                            Contains uppercase letter
                        </li>
                        <li id="lowercase-req">
                            <i class="fas fa-circle"></i>
                            Contains lowercase letter
                        </li>
                        <li id="number-req">
                            <i class="fas fa-circle"></i>
                            Contains a number
                        </li>
                    </ul>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password <span class="required">*</span></label>
                <input type="password" 
                       id="confirm_password" 
                       name="confirm_password" 
                       class="form-control" 
                       placeholder="Confirm your password"
                       required>
            </div>

            <!-- Terms and Conditions -->
            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" 
                           id="terms" 
                           name="terms" 
                           value="1"
                           <?= ($formData['terms'] ?? false) ? 'checked' : '' ?>
                           required>
                    <label for="terms">
                        I agree to the <a href="/terms" target="_blank">Terms of Service</a> 
                        and <a href="/privacy" target="_blank">Privacy Policy</a> 
                        <span class="required">*</span>
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="fas fa-user-plus"></i>
                Create Account
            </button>
        </form>

        <!-- Footer -->
        <div class="auth-footer">
            <p>Already have an account?</p>
            <a href="/login" class="btn btn-outline">
                <i class="fas fa-sign-in-alt"></i>
                Sign In Instead
            </a>
        </div>
    </div>

    <script>
        /**
         * CLIENT-SIDE VALIDATION AND UX ENHANCEMENTS
         * Modern JavaScript following 2025 best practices
         */

        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', function() {
            initializeRegistrationForm();
        });

        function initializeRegistrationForm() {
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const form = document.getElementById('registerForm');
            const submitBtn = document.getElementById('submitBtn');

            // Real-time password validation
            passwordInput.addEventListener('input', validatePassword);
            confirmPasswordInput.addEventListener('input', validatePasswordMatch);

            // Form submission handling
            form.addEventListener('submit', handleFormSubmission);

            console.log('Registration form initialized successfully');
        }

        function validatePassword() {
            const password = document.getElementById('password').value;
            
            // Length check
            updateRequirement('length-req', password.length >= 8);
            
            // Uppercase check
            updateRequirement('uppercase-req', /[A-Z]/.test(password));
            
            // Lowercase check
            updateRequirement('lowercase-req', /[a-z]/.test(password));
            
            // Number check
            updateRequirement('number-req', /\d/.test(password));
        }

        function updateRequirement(elementId, isValid) {
            const element = document.getElementById(elementId);
            if (isValid) {
                element.classList.add('valid');
                element.querySelector('i').className = 'fas fa-check-circle';
            } else {
                element.classList.remove('valid');
                element.querySelector('i').className = 'fas fa-circle';
            }
        }

        function validatePasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const confirmInput = document.getElementById('confirm_password');
            
            if (confirmPassword === '') {
                confirmInput.style.borderColor = '#555555';
                return;
            }
            
            if (password === confirmPassword) {
                confirmInput.style.borderColor = '#28a745';
            } else {
                confirmInput.style.borderColor = '#dc3545';
            }
        }

        function handleFormSubmission(event) {
            const submitBtn = document.getElementById('submitBtn');
            
            // Add loading state
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            
            // Note: Form will submit normally, loading state is just for UX
            // In a more advanced setup, this could be AJAX
        }

        // Auto-format phone number (basic example)
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 6) {
                value = value.slice(0, 3) + '-' + value.slice(3, 6) + '-' + value.slice(6, 10);
            } else if (value.length >= 3) {
                value = value.slice(0, 3) + '-' + value.slice(3);
            }
            e.target.value = value;
        });
    </script>
</body>
</html>