<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Route Testing - AeroVance</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 40px; 
            background: #1a1a1a; 
            color: white; 
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .test-link { 
            display: block; 
            margin: 10px 0; 
            padding: 15px; 
            background: #333; 
            border-radius: 8px; 
            text-decoration: none; 
            color: #FF6B35;
            transition: all 0.3s ease;
        }
        .test-link:hover { 
            background: #444; 
            transform: translateY(-2px);
        }
        .working { 
            background: #0a5d0a !important; 
            color: white !important;
        }
        .broken { 
            background: #5d0a0a !important; 
            color: white !important;
        }
        .section {
            margin: 30px 0;
            padding: 20px;
            background: #2a2a2a;
            border-radius: 10px;
        }
        .debug-info {
            background: #333;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 14px;
            overflow-x: auto;
        }
        .status {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            margin-left: 10px;
        }
        .status.ok {
            background: #28a745;
            color: white;
        }
        .status.missing {
            background: #dc3545;
            color: white;
        }
        .status.unknown {
            background: #ffc107;
            color: black;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚁 AeroVance Route Testing Dashboard</h1>
        <p>Click each link to test if the routes work correctly. This helps debug navigation issues.</p>
        
        <div class="section">
            <h2>🏠 Main Pages</h2>
            <a href="/" class="test-link">
                Home Page
                <span class="status ok">SHOULD WORK</span>
            </a>
            <a href="/helicopters" class="test-link">
                All Helicopters (Catalog)
                <span class="status ok">SHOULD WORK</span>
            </a>
            <a href="/helicopters/learn" class="test-link">
                Learning Center (General)
                <span class="status <?= file_exists('../views/general-helicopters.php') ? 'ok' : 'missing' ?>">
                    <?= file_exists('../views/general-helicopters.php') ? 'FILE EXISTS' : 'FILE MISSING' ?>
                </span>
            </a>
        </div>
        
        <div class="section">
            <h2>📁 Category Pages</h2>
            <a href="/category/personal" class="test-link">
                👤 Personal Helicopters
                <span class="status <?= file_exists('../views/personal-helicopters.php') ? 'ok' : 'missing' ?>">
                    <?= file_exists('../views/personal-helicopters.php') ? 'FILE EXISTS' : 'FILE MISSING' ?>
                </span>
            </a>
            <a href="/category/business" class="test-link">
                🏢 Business Helicopters
                <span class="status <?= file_exists('../views/business-helicopters.php') ? 'ok' : 'missing' ?>">
                    <?= file_exists('../views/business-helicopters.php') ? 'FILE EXISTS' : 'FILE MISSING' ?>
                </span>
            </a>
            <a href="/category/emergency" class="test-link">
                🚨 Emergency Services
                <span class="status <?= file_exists('../views/emergency-helicopters.php') ? 'ok' : 'missing' ?>">
                    <?= file_exists('../views/emergency-helicopters.php') ? 'FILE EXISTS' : 'FILE MISSING' ?>
                </span>
            </a>
        </div>
        
        <div class="section">
            <h2>📄 Static Pages</h2>
            <a href="/about" class="test-link">
                ℹ️ About Us
                <span class="status <?= file_exists('../views/about.php') ? 'ok' : 'unknown' ?>">
                    <?= file_exists('../views/about.php') ? 'FILE EXISTS' : 'FALLBACK CONTENT' ?>
                </span>
            </a>
            <a href="/contact" class="test-link">
                📞 Contact
                <span class="status <?= file_exists('../views/contact.php') ? 'ok' : 'unknown' ?>">
                    <?= file_exists('../views/contact.php') ? 'FILE EXISTS' : 'FALLBACK CONTENT' ?>
                </span>
            </a>
        </div>
        
        <div class="section">
            <h2>🔐 Authentication</h2>
            <a href="/login" class="test-link">
                🔐 Login
                <span class="status <?= file_exists('../views/auth/login.php') ? 'ok' : 'unknown' ?>">
                    <?= file_exists('../views/auth/login.php') ? 'FILE EXISTS' : 'FALLBACK CONTENT' ?>
                </span>
            </a>
            <a href="/register" class="test-link">
                ✍️ Register
                <span class="status <?= file_exists('../views/auth/register.php') ? 'ok' : 'unknown' ?>">
                    <?= file_exists('../views/auth/register.php') ? 'FILE EXISTS' : 'FALLBACK CONTENT' ?>
                </span>
            </a>
        </div>
        
        <div class="section">
            <h2>🔍 Debug Information</h2>
            <div class="debug-info">
                <strong>Current URL:</strong> <?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'N/A') ?><br>
                <strong>Document Root:</strong> <?= htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') ?><br>
                <strong>Script Name:</strong> <?= htmlspecialchars($_SERVER['SCRIPT_NAME'] ?? 'N/A') ?><br>
                <strong>Working Directory:</strong> <?= getcwd() ?><br>
                <strong>Views Directory:</strong> <?= is_dir('../views') ? '✅ EXISTS' : '❌ MISSING' ?><br>
                
                <?php if (is_dir('../views')): ?>
                <strong>Files in views/:</strong><br>
                <?php
                $viewFiles = scandir('../views');
                foreach ($viewFiles as $file) {
                    if ($file !== '.' && $file !== '..') {
                        echo "- $file<br>";
                    }
                }
                ?>
                <?php endif; ?>
                
                <strong>Session Status:</strong> 
                <?php if (isset($_SESSION['user'])): ?>
                    ✅ User logged in (<?= htmlspecialchars($_SESSION['user']['name'] ?? 'Unknown') ?>)
                <?php else: ?>
                    ❌ No user session
                <?php endif; ?>
            </div>
        </div>
        
        <div class="section">
            <h2>🛠️ Quick Fixes</h2>
            <div style="background: #444; padding: 15px; border-radius: 8px; margin: 10px 0;">
                <h4>If links are broken:</h4>
                <ol>
                    <li>Check that your <code>public/index.php</code> has the correct routes</li>
                    <li>Make sure your <code>.htaccess</code> file exists in project root</li>
                    <li>Verify file paths in the debug info above</li>
                    <li>Check file permissions (files should be readable)</li>
                </ol>
            </div>
            
            <div style="background: #444; padding: 15px; border-radius: 8px; margin: 10px 0;">
                <h4>Common Issues:</h4>
                <ul>
                    <li><strong>404 errors:</strong> Route not defined in router</li>
                    <li><strong>File not found:</strong> View file doesn't exist at expected path</li>
                    <li><strong>Blank page:</strong> PHP syntax error (check error logs)</li>
                    <li><strong>.htaccess issues:</strong> Apache mod_rewrite not enabled</li>
                </ul>
            </div>
        </div>
        
        <div class="section">
            <h2>🎯 Next Steps</h2>
            <ol>
                <li>Test each link above - working links should load properly</li>
                <li>If you see 404 errors, check your <code>public/index.php</code> routes</li>
                <li>If pages are blank, check PHP error logs</li>
                <li>Once navigation works, you can safely delete this test page</li>
            </ol>
        </div>
        
        <div style="text-align: center; margin-top: 40px;">
            <a href="/" class="btn" style="background: #FF6B35; color: white; padding: 15px 30px; border-radius: 8px; text-decoration: none;">
                ← Back to Home
            </a>
        </div>
    </div>

    <script>
        // Test all links and mark them as working or broken
        document.querySelectorAll('.test-link').forEach(link => {
            link.addEventListener('click', function(e) {
                this.style.opacity = '0.7';
                const status = this.querySelector('.status');
                if (status) {
                    status.textContent = 'TESTING...';
                    status.className = 'status unknown';
                }
            });
        });
        
        // Add visual feedback for successful page loads
        window.addEventListener('load', () => {
            console.log('🚁 Route testing page loaded successfully');
            console.log('📍 Current location:', window.location.href);
        });
    </script>
</body>
</html>