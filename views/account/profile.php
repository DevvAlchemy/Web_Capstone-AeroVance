//profile.php file

<?php


// Debug paths
// echo "Current file: " . __FILE__ . "<br>";
// echo "Current dir: " . __DIR__ . "<br>";
// echo "Parent dir: " . dirname(__DIR__) . "<br>";
// echo "Root dir: " . dirname(dirname(__DIR__)) . "<br>";
// echo "Config path: " . dirname(dirname(__DIR__)) . '/config/config.php' . "<br>";

// // Check if files exist
// $config_path = dirname(dirname(__DIR__)) . '/config/config.php';
// echo "Config exists: " . (file_exists($config_path) ? 'YES' : 'NO') . "<br>";

// $db_path = dirname(dirname(__DIR__)) . '/config/database.php';
// echo "Database exists: " . (file_exists($db_path) ? 'YES' : 'NO') . "<br>";

// die(); // Stop here to see the output


// require_once $_SERVER['DOCUMENT_ROOT'] . '/helicopter-marketplace/config/config.php';
// require_once $_SERVER['DOCUMENT_ROOT'] . '/helicopter-marketplace/config/database.php';
// require_once $_SERVER['DOCUMENT_ROOT'] . '/helicopter-marketplace/models/User.php';


require_once dirname(dirname(__DIR__)) . '/config/config.php';
require_once dirname(dirname(__DIR__)) . '/config/database.php';
require_once dirname(dirname(__DIR__)) . '/models/User.php';

// require_once PROJECT_ROOT . '/config/config.php';
// require_once PROJECT_ROOT . '/config/database.php';
// require_once PROJECT_ROOT . '/models/User.php';



// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: /login');
    exit;
}

$userId = $_SESSION['user']['id'];
$successMessage = '';
$errorMessage = '';

try {
    $database = new Database();
    $db = $database->connect();
    $userModel = new User($db);
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['update_profile'])) {
            // Update profile
            $profileData = [
                'first_name' => sanitizeInput($_POST['first_name']),
                'last_name' => sanitizeInput($_POST['last_name']),
                'phone' => sanitizeInput($_POST['phone']),
                'address' => sanitizeInput($_POST['address']),
                'city' => sanitizeInput($_POST['city']),
                'state' => sanitizeInput($_POST['state']),
                'zip_code' => sanitizeInput($_POST['zip_code']),
                'country' => sanitizeInput($_POST['country']),
                'pilot_license' => sanitizeInput($_POST['pilot_license']),
                'company_name' => sanitizeInput($_POST['company_name']),
                'tax_id' => sanitizeInput($_POST['tax_id'])
            ];
            
            if ($userModel->updateProfile($userId, $profileData)) {
                $successMessage = 'Profile updated successfully!';
                // Update session data
                $_SESSION['user']['first_name'] = $profileData['first_name'];
                $_SESSION['user']['last_name'] = $profileData['last_name'];
            } else {
                $errorMessage = 'Failed to update profile. Please try again.';
            }
        } elseif (isset($_POST['change_password'])) {
            // Change password
            $currentPassword = $_POST['current_password'];
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];
            
            if ($newPassword !== $confirmPassword) {
                $errorMessage = 'New passwords do not match.';
            } else {
                $result = $userModel->changePassword($userId, $currentPassword, $newPassword);
                if ($result['success']) {
                    $successMessage = $result['message'];
                } else {
                    $errorMessage = $result['message'];
                }
            }
        }
    }
    
    // Get current user data
    $userData = $userModel->getUserById($userId);
    
} catch (Exception $e) {
    error_log('Profile error: ' . $e->getMessage());
    $errorMessage = 'An error occurred. Please try again.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Helicopter Marketplace</title>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Main Stylesheet -->
   <!-- <link rel="stylesheet" href="/helicopter-marketplace/assets/css/app.css"> -->
    
    <style>
        /* Profile Edit Page Styles */
        .profile-container {
            margin-top: 100px;
            padding: 40px 0;
            background: #1a1a1a;
            min-height: 80vh;
        }
        
        .profile-layout {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 30px;
            align-items: start;
        }
        
        /* Profile Content */
        .profile-content {
            background: #333333;
            border-radius: 15px;
            padding: 40px;
            border: 1px solid rgba(255, 107, 53, 0.2);
        }
        
        .profile-header {
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 1px solid #444444;
        }
        
        .profile-header h1 {
            color: #FF6B35;
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .profile-header p {
            color: #cccccc;
            margin: 0;
        }
        
        /* Alert Messages */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
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
        
        /* Form Sections */
        .form-section {
            margin-bottom: 40px;
        }
        
        .form-section:last-child {
            margin-bottom: 0;
        }
        
        .section-title {
            color: #FF6B35;
            font-size: 1.4rem;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-group label {
            display: block;
            color: #cccccc;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        
        .form-group label .optional {
            color: #888888;
            font-size: 0.85rem;
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
        
        .form-control:disabled {
            background: #1a1a1a;
            color: #888888;
            cursor: not-allowed;
        }
        
        /* Password Requirements */
        .password-requirements {
            background: rgba(255, 107, 53, 0.05);
            border: 1px solid rgba(255, 107, 53, 0.2);
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
        }
        
        .password-requirements h5 {
            color: #FF6B35;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        
        .password-requirements ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .password-requirements li {
            color: #cccccc;
            font-size: 0.85rem;
            padding: 3px 0;
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
        
        /* Form Actions */
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        /* Account Settings Card */
        .settings-card {
            background: #2a2a2a;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            border: 1px solid #444444;
        }
        
        .settings-card h4 {
            color: #ffffff;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        
        .settings-card p {
            color: #cccccc;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        
        /* Two-Factor Authentication */
        .two-factor-status {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-enabled {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }
        
        .status-disabled {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
        }
        
        /* Responsive */
        @media (max-width: 991px) {
            .profile-layout {
                grid-template-columns: 1fr;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .profile-content {
                padding: 25px;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .form-actions .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!--  Header -->
   <?php include dirname(__DIR__) . '/includes/header.php'; ?>

    <div class="profile-container">
        <div class="container">
            <div class="profile-layout">
                <!-- Sidebar Navigation -->
                <aside class="dashboard-sidebar">
                    <nav>
                        <ul class="sidebar-nav">
                            <li class="nav-item">
                                <a href="/account" class="nav-link">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/account/profile" class="nav-link active">
                                    <i class="fas fa-user"></i>
                                    <span>Profile</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/account/orders" class="nav-link">
                                    <i class="fas fa-shopping-bag"></i>
                                    <span>Orders</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/account/wishlist" class="nav-link">
                                    <i class="fas fa-heart"></i>
                                    <span>Wishlist</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/account/inquiries" class="nav-link">
                                    <i class="fas fa-envelope"></i>
                                    <span>Inquiries</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/account/settings" class="nav-link">
                                    <i class="fas fa-cog"></i>
                                    <span>Settings</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/logout" class="nav-link">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Logout</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </aside>

                <!-- Profile Content -->
                <main class="profile-content">
                    <div class="profile-header">
                        <h1>My Profile</h1>
                        <p>Manage your personal information and account settings</p>
                    </div>

                    <?php if ($successMessage): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <?= htmlspecialchars($successMessage) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($errorMessage): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <?= htmlspecialchars($errorMessage) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Personal Information Form -->
                    <form method="POST" action="" id="profileForm">
                        <div class="form-section">
                            <h2 class="section-title">
                                <i class="fas fa-user"></i> Personal Information
                            </h2>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" name="first_name" class="form-control" 
                                           value="<?= htmlspecialchars($userData['first_name'] ?? '') ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="last_name" class="form-control" 
                                           value="<?= htmlspecialchars($userData['last_name'] ?? '') ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label>Email Address</label>
                                    <input type="email" class="form-control" 
                                           value="<?= htmlspecialchars($userData['email'] ?? '') ?>" disabled>
                                </div>
                                
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="tel" name="phone" class="form-control" 
                                           value="<?= htmlspecialchars($userData['phone'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="form-section">
                            <h2 class="section-title">
                                <i class="fas fa-map-marker-alt"></i> Address Information
                            </h2>
                            
                            <div class="form-grid">
                                <div class="form-group full-width">
                                    <label>Street Address</label>
                                    <input type="text" name="address" class="form-control" 
                                           value="<?= htmlspecialchars($userData['address'] ?? '') ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" name="city" class="form-control" 
                                           value="<?= htmlspecialchars($userData['city'] ?? '') ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label>State/Province</label>
                                    <input type="text" name="state" class="form-control" 
                                           value="<?= htmlspecialchars($userData['state'] ?? '') ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label>ZIP/Postal Code</label>
                                    <input type="text" name="zip_code" class="form-control" 
                                           value="<?= htmlspecialchars($userData['zip_code'] ?? '') ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label>Country</label>
                                    <select name="country" class="form-control">
                                        <option value="">Select Country</option>
                                        <option value="US" <?= ($userData['country'] ?? '') === 'US' ? 'selected' : '' ?>>United States</option>
                                        <option value="CA" <?= ($userData['country'] ?? '') === 'CA' ? 'selected' : '' ?>>Canada</option>
                                        <option value="MX" <?= ($userData['country'] ?? '') === 'MX' ? 'selected' : '' ?>>Mexico</option>
                                        <option value="UK" <?= ($userData['country'] ?? '') === 'UK' ? 'selected' : '' ?>>United Kingdom</option>
                                        <option value="AU" <?= ($userData['country'] ?? '') === 'AU' ? 'selected' : '' ?>>Australia</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Professional Information -->
                        <div class="form-section">
                            <h2 class="section-title">
                                <i class="fas fa-building"></i> Professional Information
                            </h2>
                            
                            <div class="form-grid">
                                <div class="form-group full-width">
                                    <label>Company Name <span class="optional">(Optional)</span></label>
                                    <input type="text" name="company_name" class="form-control" 
                                           value="<?= htmlspecialchars($userData['company_name'] ?? '') ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label>Tax ID / EIN <span class="optional">(Optional)</span></label>
                                    <input type="text" name="tax_id" class="form-control" 
                                           value="<?= htmlspecialchars($userData['tax_id'] ?? '') ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label>Pilot License # <span class="optional">(Optional)</span></label>
                                    <input type="text" name="pilot_license" class="form-control" 
                                           value="<?= htmlspecialchars($userData['pilot_license'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" name="update_profile" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <a href="/account" class="btn btn-outline">Cancel</a>
                        </div>
                    </form>

                    <!-- Change Password Section -->
                    <div class="form-section" style="margin-top: 50px;">
                        <h2 class="section-title">
                            <i class="fas fa-lock"></i> Change Password
                        </h2>
                        
                        <form method="POST" action="" id="passwordForm">
                            <div class="form-grid">
                                <div class="form-group full-width">
                                    <label>Current Password</label>
                                    <input type="password" name="current_password" class="form-control" required>
                                </div>
                                
                                <div class="form-group">
                                    <label>New Password</label>
                                    <input type="password" name="new_password" id="new_password" class="form-control" required>
                                </div>
                                
                                <div class="form-group">
                                    <label>Confirm New Password</label>
                                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="password-requirements">
                                <h5>Password Requirements:</h5>
                                <ul id="password-requirements">
                                    <li id="length-req"><i class="fas fa-circle"></i> At least 8 characters long</li>
                                    <li id="uppercase-req"><i class="fas fa-circle"></i> Contains uppercase letter</li>
                                    <li id="lowercase-req"><i class="fas fa-circle"></i> Contains lowercase letter</li>
                                    <li id="number-req"><i class="fas fa-circle"></i> Contains a number</li>
                                    <li id="match-req"><i class="fas fa-circle"></i> Passwords match</li>
                                </ul>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" name="change_password" class="btn btn-primary">
                                    <i class="fas fa-key"></i> Change Password
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Account Settings -->
                    <div class="form-section" style="margin-top: 50px;">
                        <h2 class="section-title">
                            <i class="fas fa-cog"></i> Account Settings
                        </h2>
                        
                        <div class="settings-card">
                            <h4>Two-Factor Authentication</h4>
                            <div class="two-factor-status">
                                <span>Status:</span>
                                <span class="status-badge status-disabled">Disabled</span>
                            </div>
                            <p>Add an extra layer of security to your account by enabling two-factor authentication.</p>
                            <button class="btn btn-outline" onclick="enable2FA()">
                                <i class="fas fa-shield-alt"></i> Enable 2FA
                            </button>
                        </div>
                        
                        <div class="settings-card">
                            <h4>Email Notifications</h4>
                            <p>Manage your email preferences and notification settings.</p>
                            <a href="/account/settings" class="btn btn-outline">
                                <i class="fas fa-envelope"></i> Manage Notifications
                            </a>
                        </div>
                        
                        <div class="settings-card">
                            <h4>Account Type</h4>
                            <p>Your account type: <strong><?= ucfirst($userData['user_type'] ?? 'customer') ?></strong></p>
                            <?php if ($userData['user_type'] === 'customer'): ?>
                            <p>Upgrade to a dealer account to list helicopters for sale.</p>
                            <a href="/become-dealer" class="btn btn-outline">
                                <i class="fas fa-store"></i> Become a Dealer
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <!--  Footer -->
    <?php include dirname(__DIR__) . '/includes/footer.php'; ?>


    <script>
        // Password validation
        const newPasswordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        
        function validatePassword() {
            const password = newPasswordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            // Length check
            document.getElementById('length-req').className = password.length >= 8 ? 'valid' : '';
            
            // Uppercase check
            document.getElementById('uppercase-req').className = /[A-Z]/.test(password) ? 'valid' : '';
            
            // Lowercase check
            document.getElementById('lowercase-req').className = /[a-z]/.test(password) ? 'valid' : '';
            
            // Number check
            document.getElementById('number-req').className = /\d/.test(password) ? 'valid' : '';
            
            // Match check
            document.getElementById('match-req').className = 
                (password === confirmPassword && password !== '') ? 'valid' : '';
        }
        
        newPasswordInput.addEventListener('keyup', validatePassword);
        confirmPasswordInput.addEventListener('keyup', validatePassword);
        
        // Enable 2FA placeholder
        function enable2FA() {
            alert('Two-factor authentication setup would open here');
        }
        
        // Form validation
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            const password = newPasswordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match');
                return false;
            }
            
            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long');
                return false;
            }
        });
    </script>
</body>
</html>