<?php
/**
 * DEBUGGING HELPER FOR AeroVance
 * 
 * This file will help me understand what's happening with their routes
 * 
 * My LEARNING OBJECTIVES:
 * 1. Understanding how web server routing works
 * 2. Debugging 404 errors systematically
 * 3. Understanding file paths and URL mapping
 * 4. Learning to check server configuration
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get project root directory
$projectRoot = dirname(__DIR__);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Helper - AeroVance</title>
    <style>
        body { 
            font-family: 'Consolas', 'Monaco', monospace; 
            background: #1a1a1a; 
            color: #ffffff; 
            padding: 20px; 
            line-height: 1.6;
            margin: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .section { 
            background: #333; 
            padding: 20px; 
            margin: 20px 0; 
            border-radius: 8px; 
            border-left: 4px solid #FF6B35;
        }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        .info { color: #17a2b8; }
        h1, h2 { color: #FF6B35; }
        h1 { border-bottom: 2px solid #FF6B35; padding-bottom: 10px; }
        pre { 
            background: #222; 
            padding: 15px; 
            border-radius: 4px; 
            overflow-x: auto;
            font-size: 0.9rem;
        }
        .file-check { 
            display: flex; 
            align-items: center; 
            margin: 8px 0; 
            padding: 5px;
        }
        .file-check i { 
            margin-right: 15px; 
            width: 20px; 
            font-size: 1.1rem;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 15px 0; 
            background: #2a2a2a;
        }
        th, td { 
            border: 1px solid #555; 
            padding: 12px 8px; 
            text-align: left; 
            font-size: 0.9rem;
        }
        th { 
            background: #444; 
            color: #FF6B35;
            font-weight: bold;
        }
        .test-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .test-link {
            background: #FF6B35;
            color: white;
            padding: 12px;
            text-decoration: none;
            border-radius: 6px;
            text-align: center;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        .test-link:hover {
            background: #ff8c42;
            color: white;
        }
        .code-block {
            background: #1a1a1a;
            border: 1px solid #444;
            border-radius: 4px;
            padding: 15px;
            margin: 10px 0;
        }
        .highlight {
            background: rgba(255, 107, 53, 0.1);
            padding: 2px 4px;
            border-radius: 3px;
        }
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-bug"></i> AeroVance - Debug Helper</h1>
        <p>This tool helps diagnose routing and configuration issues. Use this to identify what's not working.</p>

        <!-- SERVER INFORMATION -->
        <div class="section">
            <h2><i class="fas fa-server"></i> Server Information</h2>
            <table>
                <tr><td><strong>Server Software</strong></td><td><?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?></td></tr>
                <tr><td><strong>PHP Version</strong></td><td><?= PHP_VERSION ?></td></tr>
                <tr><td><strong>Document Root</strong></td><td><?= $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown' ?></td></tr>
                <tr><td><strong>Current Script</strong></td><td><?= __FILE__ ?></td></tr>
                <tr><td><strong>Project Root</strong></td><td><?= $projectRoot ?></td></tr>
                <tr><td><strong>Request URI</strong></td><td><?= $_SERVER['REQUEST_URI'] ?? 'Unknown' ?></td></tr>
                <tr><td><strong>Request Method</strong></td><td><?= $_SERVER['REQUEST_METHOD'] ?? 'Unknown' ?></td></tr>
                <tr><td><strong>Query String</strong></td><td><?= $_SERVER['QUERY_STRING'] ?? 'None' ?></td></tr>
            </table>
        </div>

        <!-- FILE STRUCTURE CHECK -->
        <div class="section">
            <h2><i class="fas fa-folder-tree"></i> File Structure Check</h2>
            <p>Checking if important files exist in the correct locations:</p>
            
            <?php
            $filesToCheck = [
                'public/index.php' => 'Main router file (YOU ARE HERE)',
                '.htaccess' => 'Apache configuration (PROJECT ROOT)',
                'config/config.php' => 'Main configuration',
                'config/database.php' => 'Database configuration',
                'views/home.php' => 'Homepage view',
                'views/catalog.php' => 'Catalog view',
                'views/helicopter-detail.php' => 'Helicopter detail view',
                'views/cart.php' => 'Shopping cart view',
                'views/account/dashboard.php' => 'Account dashboard',
                'views/account/profile.php' => 'Profile page',
                'views/account/order-details.php' => 'Order details page',
                'includes/header.php' => 'Header include',
                'includes/footer.php' => 'Footer include',
                'assets/css/app.css' => 'Main stylesheet',
                'assets/js/catalog.js' => 'Catalog JavaScript',
                'controllers/HelicopterController.php' => 'Helicopter controller',
                'models/Helicopter.php' => 'Helicopter model',
                'models/User.php' => 'User model',
                'models/Cart.php' => 'Cart model'
            ];
            
            foreach ($filesToCheck as $file => $description) {
                $fullPath = $projectRoot . '/' . $file;
                $exists = file_exists($fullPath);
                $readable = $exists && is_readable($fullPath);
                
                echo '<div class="file-check">';
                if ($exists && $readable) {
                    echo '<i class="fas fa-check-circle success"></i>';
                    echo '<span class="success">✓ ' . $file . '</span> - ' . $description;
                } elseif ($exists) {
                    echo '<i class="fas fa-exclamation-triangle warning"></i>';
                    echo '<span class="warning">⚠ ' . $file . '</span> - Exists but not readable - ' . $description;
                } else {
                    echo '<i class="fas fa-times-circle error"></i>';
                    echo '<span class="error">✗ ' . $file . '</span> - Missing - ' . $description;
                }
                echo '</div>';
            }
            ?>
        </div>

        <!-- APACHE MODULE CHECK -->
        <div class="section">
            <h2><i class="fas fa-puzzle-piece"></i> Apache Module Check</h2>
            <?php if (function_exists('apache_get_modules')): ?>
                <?php 
                $modules = apache_get_modules();
                $requiredModules = ['mod_rewrite', 'mod_headers', 'mod_expires'];
                ?>
                <p>Checking for required Apache modules:</p>
                <?php foreach ($requiredModules as $module): ?>
                    <div class="file-check">
                        <?php if (in_array($module, $modules)): ?>
                            <i class="fas fa-check-circle success"></i>
                            <span class="success">✓ <?= $module ?></span> - Enabled
                        <?php else: ?>
                            <i class="fas fa-times-circle error"></i>
                            <span class="error">✗ <?= $module ?></span> - Not found or disabled
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Cannot check Apache modules (function not available). This is normal on some servers.
                </div>
            <?php endif; ?>
        </div>

        <!-- URL REWRITING TEST -->
        <div class="section">
            <h2><i class="fas fa-route"></i> URL Rewriting Test</h2>
            <p>Testing if .htaccess and URL rewriting work correctly:</p>
            
            <div id="rewrite-test">
                <p><i class="fas fa-spinner fa-spin"></i> Testing URL rewriting...</p>
            </div>

            <script>
            // Test URL rewriting by making a request to a non-existent file
            fetch('/test-rewrite-debug-123')
                .then(response => {
                    const testDiv = document.getElementById('rewrite-test');
                    if (response.status === 404) {
                        return response.text().then(text => {
                            if (text.includes('AEROVANCE') || text.includes('Router') || text.includes('Page not found')) {
                                testDiv.innerHTML = '<div class="success"><i class="fas fa-check-circle"></i> ✓ URL rewriting is working - requests are reaching your router</div>';
                            } else {
                                testDiv.innerHTML = '<div class="error"><i class="fas fa-times-circle"></i> ✗ URL rewriting may not be working - getting default Apache 404</div>';
                            }
                        });
                    } else if (response.status === 500) {
                        testDiv.innerHTML = '<div class="warning"><i class="fas fa-exclamation-triangle"></i> ⚠ Router reached but has errors (500 error)</div>';
                    } else {
                        testDiv.innerHTML = '<div class="warning"><i class="fas fa-exclamation-triangle"></i> ⚠ Unexpected response: ' + response.status + '</div>';
                    }
                })
                .catch(error => {
                    document.getElementById('rewrite-test').innerHTML = '<div class="error"><i class="fas fa-times-circle"></i> ✗ Error testing URL rewriting: ' + error.message + '</div>';
                });
            </script>
        </div>

        <!-- CONFIGURATION CHECK -->
        <div class="section">
            <h2><i class="fas fa-cogs"></i> Configuration Check</h2>
            <?php
            $configPath = $projectRoot . '/config/config.php';
            if (file_exists($configPath)) {
                try {
                    include_once $configPath;
                    echo '<div class="success"><i class="fas fa-check-circle"></i> ✓ Configuration file loaded successfully</div>';
                    
                    // Check important constants
                    $constants = [
                        'DEBUG_MODE' => 'Debug mode',
                        'DB_HOST' => 'Database host',
                        'DB_NAME' => 'Database name',
                        'SITE_URL' => 'Site URL'
                    ];
                    
                    echo '<h3>Configuration Values:</h3>';
                    echo '<table>';
                    foreach ($constants as $const => $desc) {
                        $value = defined($const) ? constant($const) : 'Not defined';
                        $status = defined($const) ? 'success' : 'error';
                        echo "<tr><td><strong>$desc ($const)</strong></td><td class='$status'>$value</td></tr>";
                    }
                    echo '</table>';
                } catch (Exception $e) {
                    echo '<div class="error"><i class="fas fa-times-circle"></i> ✗ Error loading configuration: ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
            } else {
                echo '<div class="error"><i class="fas fa-times-circle"></i> ✗ Configuration file not found at: ' . htmlspecialchars($configPath) . '</div>';
            }
            ?>
        </div>

        <!-- DATABASE CONNECTION TEST -->
        <div class="section">
            <h2><i class="fas fa-database"></i> Database Connection Test</h2>
            <?php
            try {
                $dbPath = $projectRoot . '/config/database.php';
                if (file_exists($dbPath)) {
                    include_once $dbPath;
                    
                    if (class_exists('Database')) {
                        $database = new Database();
                        $connection = $database->connect();
                        
                        if ($connection) {
                            echo '<div class="success"><i class="fas fa-check-circle"></i> ✓ Database connection successful</div>';
                            
                            // Test a simple query
                            try {
                                $stmt = $connection->query("SELECT COUNT(*) as count FROM helicopters");
                                $result = $stmt->fetch();
                                echo '<div class="info"><i class="fas fa-info-circle"></i> Found ' . $result['count'] . ' helicopters in database</div>';
                            } catch (Exception $e) {
                                echo '<div class="warning"><i class="fas fa-exclamation-triangle"></i> Database connected but helicopters table may not exist</div>';
                            }
                        } else {
                            echo '<div class="error"><i class="fas fa-times-circle"></i> ✗ Database connection failed</div>';
                        }
                    } else {
                        echo '<div class="error"><i class="fas fa-times-circle"></i> ✗ Database class not found</div>';
                    }
                } else {
                    echo '<div class="error"><i class="fas fa-times-circle"></i> ✗ Database configuration file not found</div>';
                }
            } catch (Exception $e) {
                echo '<div class="error"><i class="fas fa-times-circle"></i> ✗ Database error: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
            ?>
        </div>

        <!-- ROUTE TESTING -->
        <div class="section">
            <h2><i class="fas fa-map-signs"></i> Route Testing</h2>
            <p>Test your routes by clicking these links. They should NOT give 404 errors:</p>
            
            <div class="test-links">
                <?php
                $testRoutes = [
                    '/' => 'Homepage',
                    '/helicopters' => 'Helicopter Catalog',
                    '/helicopter/1' => 'Helicopter Detail (ID: 1)',
                    '/category/personal' => 'Personal Category',
                    '/category/business' => 'Business Category',
                    '/about' => 'About Page',
                    '/contact' => 'Contact Page',
                    '/login' => 'Login Page',
                    '/account' => 'Account Dashboard',
                    '/account/profile' => 'Profile Page',
                    '/account/order/1' => 'Order Details (ID: 1)'
                ];
                
                foreach ($testRoutes as $route => $description) {
                    echo '<a href="' . $route . '" target="_blank" class="test-link">' . $description . '</a>';
                }
                ?>
            </div>
        </div>

        <!-- COMMON ISSUES AND SOLUTIONS -->
        <div class="section">
            <h2><i class="fas fa-tools"></i> Common Issues & Solutions</h2>
            
            <h3>If you're getting 404 errors:</h3>
            <ol>
                <li><strong>Check .htaccess file:</strong> Make sure it's in your project root (not in public/)</li>
                <li><strong>Enable mod_rewrite:</strong> Your Apache server needs mod_rewrite enabled</li>
                <li><strong>File permissions:</strong> Ensure Apache can read your files (755 for directories, 644 for files)</li>
                <li><strong>Check your virtual host:</strong> Make sure AllowOverride is set to All</li>
            </ol>
            
            <h3>If CSS/JS files aren't loading:</h3>
            <ol>
                <li><strong>Check file paths:</strong> CSS should be at /assets/css/app.css</li>
                <li><strong>Static file handling:</strong> .htaccess should serve static files directly</li>
                <li><strong>MIME types:</strong> Server should recognize .css and .js file types</li>
            </ol>
            
            <h3>If account pages give errors:</h3>
            <ol>
                <li><strong>Missing routes:</strong> Check if /account/profile and /account/order/{id} are defined</li>
                <li><strong>Include paths:</strong> Change ../../includes/ to ../includes/ in account views</li>
                <li><strong>File locations:</strong> Make sure views/account/ files exist</li>
            </ol>
            
            <h3>Virtual Host Configuration Example:</h3>
            <div class="code-block">
&lt;VirtualHost *:80&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;DocumentRoot "/path/to/helicopter-marketplace"<br>
&nbsp;&nbsp;&nbsp;&nbsp;ServerName helicopter-marketplace.local<br>
&nbsp;&nbsp;&nbsp;&nbsp;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;Directory "/path/to/helicopter-marketplace"&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;AllowOverride All<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Require all granted<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;/Directory&gt;<br>
&lt;/VirtualHost&gt;
            </div>
        </div>

        <!-- LIVE DEBUGGING -->
        <div class="section">
            <h2><i class="fas fa-eye"></i> Current Request Information</h2>
            <p>This shows what the server receives for the current request:</p>
            
            <h3>$_SERVER Variables:</h3>
            <pre><?php 
            $serverVars = $_SERVER;
            // Hide sensitive information
            if (isset($serverVars['HTTP_AUTHORIZATION'])) $serverVars['HTTP_AUTHORIZATION'] = '[HIDDEN]';
            if (isset($serverVars['PHP_AUTH_PW'])) $serverVars['PHP_AUTH_PW'] = '[HIDDEN]';
            print_r($serverVars); 
            ?></pre>
            
            <h3>$_GET Variables:</h3>
            <pre><?php print_r($_GET); ?></pre>
            
            <h3>$_POST Variables:</h3>
            <pre><?php print_r($_POST); ?></pre>
        </div>

        <div class="section">
            <h2><i class="fas fa-graduation-cap"></i> Student Learning Notes</h2>
            <ul>
                <li><span class="success">Green checkmarks (✓)</span> mean everything is working correctly</li>
                <li><span class="error">Red X marks (✗)</span> indicate problems that need fixing</li>
                <li><span class="warning">Yellow warnings (⚠)</span> suggest potential issues to investigate</li>
                <li>Always test your routes after making changes to configuration</li>
                <li>Use browser developer tools to see network requests and responses</li>
                <li>Check Apache error logs if you see 500 errors</li>
                <li>URL rewriting allows pretty URLs like /helicopters instead of /index.php?page=helicopters</li>
            </ul>
        </div>
    </div>
</body>
</html>
