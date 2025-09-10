<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - AeroVance</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1a1a1a, #2a2a2a);
            color: #ffffff;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            text-align: center;
            max-width: 600px;
            padding: 40px;
            background: rgba(51, 51, 51, 0.8);
            border-radius: 15px;
            border: 1px solid rgba(255, 107, 53, 0.3);
        }
        .error-code {
            font-size: 6rem;
            font-weight: bold;
            color: #FF6B35;
            margin-bottom: 20px;
            text-shadow: 0 0 20px rgba(255, 107, 53, 0.5);
        }
        .error-title {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #ffffff;
        }
        .error-message {
            font-size: 1.1rem;
            color: #cccccc;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .error-links {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .btn-primary {
            background: linear-gradient(135deg, #FF6B35, #FF8C42);
            color: white;
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
        .debug-info {
            margin-top: 30px;
            padding: 20px;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            font-family: monospace;
            font-size: 0.9rem;
            color: #aaaaaa;
            text-align: left;
        }
        .helicopter-icon {
            font-size: 3rem;
            color: #FF6B35;
            margin-bottom: 20px;
            opacity: 0.7;
        }
        @media (max-width: 768px) {
            .error-container {
                margin: 20px;
                padding: 30px 20px;
            }
            .error-code {
                font-size: 4rem;
            }
            .error-title {
                font-size: 1.5rem;
            }
        }
    </style>
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="error-container">
        <div class="helicopter-icon">
            <i class="fas fa-helicopter"></i>
        </div>
        
        <div class="error-code">404</div>
        
        <h1 class="error-title">Flight Path Not Found</h1>
        
        <p class="error-message">
            Looks like this helicopter took a wrong turn! The page you're looking for doesn't exist 
            or may have been moved to a different hangar.
        </p>
        
        <div class="error-links">
            <a href="/" class="btn btn-primary">
                <i class="fas fa-home"></i> Return to Base
            </a>
            <a href="/helicopters" class="btn btn-outline">
                <i class="fas fa-helicopter"></i> Browse Aircraft
            </a>
            <a href="/contact" class="btn btn-outline">
                <i class="fas fa-envelope"></i> Contact Support
            </a>
        </div>
        
        <?php if (defined('DEBUG_MODE') && DEBUG_MODE): ?>
        <div class="debug-info">
            <strong>Debug Information:</strong><br>
            Requested URL: <?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'Unknown') ?><br>
            Request Method: <?= htmlspecialchars($_SERVER['REQUEST_METHOD'] ?? 'Unknown') ?><br>
            Server Time: <?= date('Y-m-d H:i:s') ?><br>
            
            <strong>Common Solutions:</strong><br>
            • Check if the route is defined in public/index.php<br>
            • Verify .htaccess file is properly configured<br>
            • Ensure mod_rewrite is enabled in Apache<br>
            • Check file permissions on views directory<br>
            
            <strong>Available Routes:</strong><br>
            • / (Homepage)<br>
            • /helicopters (Catalog)<br>
            • /helicopter/{id} (Helicopter details)<br>
            • /category/{category} (Category pages)<br>
            • /login, /register, /logout<br>
            • /cart, /contact, /about<br>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            const helicopter = document.querySelector('.helicopter-icon i');
            
            // Rotate helicopter on hover
            helicopter.addEventListener('mouseenter', function() {
                this.style.transform = 'rotate(360deg)';
                this.style.transition = 'transform 1s ease';
            });
            
            helicopter.addEventListener('mouseleave', function() {
                this.style.transform = 'rotate(0deg)';
            });
            
            // Log helpful information for developers
            console.log('🚁 AEROVANCE - 404 Error');
            console.log('Current URL:', window.location.href);
            console.log('Referrer:', document.referrer || 'Direct access');
        });
    </script>
</body>
</html>