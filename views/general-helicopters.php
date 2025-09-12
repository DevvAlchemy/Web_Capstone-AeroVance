<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helicopters - Learn, Explore & Discover | AeroVance</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Discover the world of helicopters - from basics to advanced models. Learn about helicopter types, technology, and find your perfect aircraft in Toronto, Ontario.">
    <meta name="keywords" content="helicopters, rotorcraft, aviation, pilot training, helicopter sales, Toronto">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="/helicopter-marketplace/assets/css/app.css">
    
    <style>
        /* 🎨 FIXED: Complete styles for General Helicopter Page */
        
        /* Base body styling (in case main CSS doesn't load) */
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #1a1a1a;
            color: #ffffff;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        /* Container utility */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Section title styling */
        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title h2 {
            font-size: 3rem;
            color: #FF6B35;
            margin-bottom: 15px;
        }

        .section-title p {
            font-size: 1.2rem;
            color: #cccccc;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Hero buttons styling */
        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Button base styles (backup) */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #FF6B35, #FF8C42);
            color: white;
        }

        .btn-outline {
            border: 2px solid #FF6B35;
            color: #FF6B35;
            background: transparent;
        }

        .btn-large {
            padding: 15px 30px;
            font-size: 1.1rem;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        /* Educational Section Styles */
        .education-hero {
            background: linear-gradient(135deg, 
                rgba(255, 107, 53, 0.9), 
                rgba(255, 140, 66, 0.8)), 
                url('/assets/images/helicopter-cockpit.jpg') center/cover;
            padding: 120px 0 80px;
            text-align: center;
            color: white;
            margin-top: 80px; /* Account for fixed header */
        }

        .education-hero h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .education-hero p {
            font-size: 1.3rem;
            max-width: 800px;
            margin: 0 auto 40px;
            line-height: 1.6;
        }

        /* Learning Modules */
        .learning-modules {
            padding: 80px 0;
            background: #1a1a1a;
        }

        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .learning-module {
            background: linear-gradient(135deg, #333333, #2a2a2a);
            border-radius: 15px;
            padding: 30px;
            border: 1px solid rgba(255, 107, 53, 0.2);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .learning-module::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #FF6B35, #FF8C42);
        }

        .learning-module:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(255, 107, 53, 0.3);
            border-color: #FF6B35;
        }

        .module-icon {
            font-size: 2.5rem;
            color: #FF6B35;
            margin-bottom: 20px;
        }

        .module-title {
            color: #ffffff;
            font-size: 1.4rem;
            margin-bottom: 15px;
        }

        .module-description {
            color: #cccccc;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        /* 🔧 FIXED: Missing Progress Bar Styles */
        .module-progress {
            margin-bottom: 20px;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #FF6B35, #FF8C42);
            transition: width 0.5s ease;
            border-radius: 4px;
        }

        .progress-text {
            color: #aaaaaa;
            font-size: 0.9rem;
            text-align: center;
            display: block;
        }

        /* Interactive Timeline */
        .helicopter-timeline {
            padding: 80px 0;
            background: #2a2a2a;
        }

        .timeline-container {
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
        }

        .timeline-line {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #FF6B35, #FF8C42);
        }

        .timeline-item {
            display: flex;
            margin-bottom: 50px;
            align-items: center;
            position: relative;
        }

        .timeline-item:nth-child(even) {
            flex-direction: row-reverse;
        }

        .timeline-content {
            flex: 1;
            background: #333333;
            border-radius: 15px;
            padding: 25px;
            margin: 0 30px;
            border: 1px solid rgba(255, 107, 53, 0.2);
            position: relative;
        }

        .timeline-content h3 {
            color: #FF6B35;
            margin-bottom: 10px;
        }

        .timeline-content p {
            color: #cccccc;
            margin: 0;
        }

        .timeline-year {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #FF6B35, #FF8C42);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: bold;
            z-index: 2;
            min-width: 80px;
            text-align: center;
        }

        /* Featured Collections */
        .featured-collections {
            padding: 80px 0;
            background: #2a2a2a;
        }

        .collections-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .collection-card {
            position: relative;
            height: 350px;
            border-radius: 15px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .collection-card:hover {
            transform: scale(1.02);
        }

        .collection-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            background-color: #444444; /* Fallback color */
            transition: transform 0.5s ease;
        }

        .collection-card:hover .collection-background {
            transform: scale(1.1);
        }

        .collection-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, 
                rgba(255, 107, 53, 0.8), 
                rgba(255, 140, 66, 0.6));
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 30px;
        }

        .collection-stats {
            display: flex;
            gap: 30px;
            margin-top: 20px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* CTA Section */
        .cta-section {
            padding: 60px 0;
            background: linear-gradient(135deg, #FF6B35, #FF8C42);
            text-align: center;
            color: white;
        }

        .cta-content {
            max-width: 600px;
            margin: 0 auto;
        }

        /* 🔧 FIXED: Mobile Responsive Improvements */
        @media (max-width: 768px) {
            .education-hero h1 {
                font-size: 2.5rem;
            }

            .modules-grid {
                grid-template-columns: 1fr;
            }

            .timeline-content {
                margin: 0 15px;
            }

            .timeline-year {
                position: relative;
                left: auto;
                transform: none;
                margin-bottom: 15px;
                display: inline-block;
            }

            .timeline-item {
                flex-direction: column;
                text-align: center;
            }

            .timeline-item:nth-child(even) {
                flex-direction: column;
            }

            .collection-stats {
                flex-direction: column;
                gap: 15px;
            }

            .collections-grid {
                grid-template-columns: 1fr;
            }
        }

        /* 🔧 FIXED: Debugging styles - remove after fixing */
        .debug-section {
            background: #ff0000 !important;
            min-height: 200px;
            border: 2px solid #00ff00;
        }
    </style>
</head>

<body>
    <!-- 🔧 DEBUGGING: Simplified header for testing -->
    <header style="position: fixed; top: 0; width: 100%; background: #2a2a2a; padding: 15px 0; z-index: 1000;">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="color: #FF6B35; font-size: 1.5rem; font-weight: bold;">
                    <i class="fas fa-helicopter"></i> AeroVance
                </div>
                <nav style="display: flex; gap: 20px;">
                    <a href="/" style="color: #cccccc; text-decoration: none;">Home</a>
                    <a href="/helicopters" style="color: #FF6B35; text-decoration: none;">Helicopters</a>
                    <a href="/category/personal" style="color: #cccccc; text-decoration: none;">Personal</a>
                    <a href="/category/business" style="color: #cccccc; text-decoration: none;">Business</a>
                    <a href="/category/emergency" style="color: #cccccc; text-decoration: none;">Emergency</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Education Hero Section -->
    <section class="education-hero">
        <div class="container">
            <h1><i class="fas fa-graduation-cap"></i> Master the Sky</h1>
            <p>From the fundamentals of rotorcraft flight to advanced helicopter operations, 
               explore comprehensive resources that will elevate your aviation knowledge and help you 
               make informed decisions about helicopter ownership and operation.</p>
            
            <div class="hero-buttons">
                <a href="#learning-modules" class="btn btn-outline btn-large">
                    <i class="fas fa-book-open"></i> Start Learning
                </a>
                <a href="#featured-aircraft" class="btn btn-primary btn-large">
                    <i class="fas fa-shopping-cart"></i> Shop Aircraft
                </a>
            </div>
        </div>
    </section>

    <!-- Learning Modules Section -->
    <section class="learning-modules" id="learning-modules">
        <div class="container">
            <div class="section-title">
                <h2><i class="fas fa-brain"></i> Learning Center</h2>
                <p>Comprehensive guides to help you understand helicopters inside and out</p>
            </div>

            <div class="modules-grid">
                <!-- Module 1: Basics -->
                <div class="learning-module">
                    <div class="module-icon">
                        <i class="fas fa-helicopter"></i>
                    </div>
                    <h3 class="module-title">Helicopter Fundamentals</h3>
                    <p class="module-description">
                        Learn how helicopters achieve flight through rotor dynamics, understand the four forces of flight, 
                        and discover what makes rotorcraft unique from fixed-wing aircraft.
                    </p>
                    <div class="module-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 0%"></div>
                        </div>
                        <span class="progress-text">0% Complete</span>
                    </div>
                    <button class="btn btn-primary" onclick="startModule('fundamentals')">
                        <i class="fas fa-play"></i> Start Module
                    </button>
                </div>

                <!-- Module 2: Types -->
                <div class="learning-module">
                    <div class="module-icon">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <h3 class="module-title">Helicopter Categories</h3>
                    <p class="module-description">
                        Explore different helicopter classifications from light single-engine to heavy-lift twin turbines. 
                        Understand which type suits specific missions and operational requirements.
                    </p>
                    <div class="module-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 0%"></div>
                        </div>
                        <span class="progress-text">0% Complete</span>
                    </div>
                    <button class="btn btn-primary" onclick="startModule('categories')">
                        <i class="fas fa-play"></i> Start Module
                    </button>
                </div>

                <!-- Module 3: Operations -->
                <div class="learning-module">
                    <div class="module-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <h3 class="module-title">Flight Operations</h3>
                    <p class="module-description">
                        Master helicopter operations including pre-flight procedures, weather considerations, 
                        emergency protocols, and advanced flight techniques for various scenarios.
                    </p>
                    <div class="module-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 0%"></div>
                        </div>
                        <span class="progress-text">0% Complete</span>
                    </div>
                    <button class="btn btn-primary" onclick="startModule('operations')">
                        <i class="fas fa-play"></i> Start Module
                    </button>
                </div>

                <!-- Module 4: Maintenance -->
                <div class="learning-module">
                    <div class="module-icon">
                        <i class="fas fa-wrench"></i>
                    </div>
                    <h3 class="module-title">Maintenance & Safety</h3>
                    <p class="module-description">
                        Understand helicopter maintenance requirements, safety protocols, regulatory compliance, 
                        and cost factors involved in helicopter ownership and operation.
                    </p>
                    <div class="module-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 0%"></div>
                        </div>
                        <span class="progress-text">0% Complete</span>
                    </div>
                    <button class="btn btn-primary" onclick="startModule('maintenance')">
                        <i class="fas fa-play"></i> Start Module
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Helicopter Timeline -->
    <section class="helicopter-timeline">
        <div class="container">
            <div class="section-title">
                <h2><i class="fas fa-history"></i> Aviation Evolution</h2>
                <p>Journey through the fascinating history of rotorcraft development</p>
            </div>

            <div class="timeline-container">
                <div class="timeline-line"></div>
                
                <div class="timeline-item">
                    <div class="timeline-year">1907</div>
                    <div class="timeline-content">
                        <h3>First Helicopter Flight</h3>
                        <p>Paul Cornu achieves the first free helicopter flight in France, reaching 1 meter altitude for 20 seconds.</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-year">1939</div>
                    <div class="timeline-content">
                        <h3>Modern Era Begins</h3>
                        <p>Igor Sikorsky's VS-300 establishes the single main rotor configuration still used today.</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-year">1975</div>
                    <div class="timeline-content">
                        <h3>Robinson Revolution</h3>
                        <p>Frank Robinson introduces the R22, making helicopter ownership accessible to private pilots.</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-year">2025</div>
                    <div class="timeline-content">
                        <h3>Electric Future</h3>
                        <p>Advanced eVTOL aircraft and sustainable aviation technologies reshape urban mobility.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Collections -->
    <section class="featured-collections" id="featured-aircraft">
        <div class="container">
            <div class="section-title">
                <h2><i class="fas fa-star"></i> Featured Aircraft Collections</h2>
                <p>Explore our curated selections of premium helicopters by category</p>
            </div>

            <div class="collections-grid">
                <!-- Personal Collection -->
                <div class="collection-card" onclick="window.location.href='/category/personal'">
                    <div class="collection-background" style="background-color: #4CAF50;"></div>
                    <div class="collection-overlay">
                        <h3 style="font-size: 2rem; margin-bottom: 15px;">Personal Aviation</h3>
                        <p style="font-size: 1.1rem; margin-bottom: 20px;">Experience the freedom of private flight</p>
                        <div class="collection-stats">
                            <div class="stat-item">
                                <span class="stat-number">25+</span>
                                <span class="stat-label">Aircraft Available</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">$500K+</span>
                                <span class="stat-label">Starting Price</span>
                            </div>
                        </div>
                        <div class="btn btn-outline btn-large" style="margin-top: 20px; border-color: white; color: white;">
                            <i class="fas fa-arrow-right"></i> Explore Personal
                        </div>
                    </div>
                </div>

                <!-- Business Collection -->
                <div class="collection-card" onclick="window.location.href='/category/business'">
                    <div class="collection-background" style="background-color: #2196F3;"></div>
                    <div class="collection-overlay">
                        <h3 style="font-size: 2rem; margin-bottom: 15px;">Business Solutions</h3>
                        <p style="font-size: 1.1rem; margin-bottom: 20px;">Elevate your corporate operations</p>
                        <div class="collection-stats">
                            <div class="stat-item">
                                <span class="stat-number">35+</span>
                                <span class="stat-label">Aircraft Available</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">$2M+</span>
                                <span class="stat-label">Starting Price</span>
                            </div>
                        </div>
                        <div class="btn btn-outline btn-large" style="margin-top: 20px; border-color: white; color: white;">
                            <i class="fas fa-arrow-right"></i> Explore Business
                        </div>
                    </div>
                </div>

                <!-- Emergency Collection -->
                <div class="collection-card" onclick="window.location.href='/category/emergency'">
                    <div class="collection-background" style="background-color: #f44336;"></div>
                    <div class="collection-overlay">
                        <h3 style="font-size: 2rem; margin-bottom: 15px;">Emergency Services</h3>
                        <p style="font-size: 1.1rem; margin-bottom: 20px;">Mission-critical aircraft solutions</p>
                        <div class="collection-stats">
                            <div class="stat-item">
                                <span class="stat-number">20+</span>
                                <span class="stat-label">Aircraft Available</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">$8M+</span>
                                <span class="stat-label">Starting Price</span>
                            </div>
                        </div>
                        <div class="btn btn-outline btn-large" style="margin-top: 20px; border-color: white; color: white;">
                            <i class="fas fa-arrow-right"></i> Explore Emergency
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 style="margin-bottom: 20px; font-size: 2.5rem;">Ready to Take Flight?</h2>
                <p style="font-size: 1.2rem; margin-bottom: 30px;">
                    Whether you're looking to learn, buy, or just explore, our Toronto-based team 
                    is here to guide your aviation journey.
                </p>
                <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                    <a href="/contact" class="btn btn-outline btn-large" style="border-color: white; color: white;">
                        <i class="fas fa-phone"></i> Contact Our Experts
                    </a>
                    <a href="/helicopters" class="btn btn-primary btn-large" style="background: white; color: #FF6B35;">
                        <i class="fas fa-shopping-cart"></i> Browse All Aircraft
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Simple Footer -->
    <footer style="background: #0d0d0d; padding: 40px 0; text-align: center; color: #cccccc;">
        <div class="container">
            <p>&copy; 2025 AeroVance. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // 🔧 FIXED: Improved notification system
        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#28a745' : '#17a2b8'};
                color: white;
                padding: 15px 20px;
                border-radius: 5px;
                z-index: 10000;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                transform: translateX(100%);
                transition: transform 0.3s ease;
            `;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 10);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }

        // Learning module functionality
        function startModule(moduleType) {
            console.log(`Starting module: ${moduleType}`);
            
            // Store user progress
            localStorage.setItem(`module_${moduleType}_started`, 'true');
            
            // Show notification
            showNotification(`Starting ${moduleType} module...`, 'success');
            
            // Simulate progress update
            setTimeout(() => {
                updateModuleProgress(moduleType, 25);
            }, 1000);
        }

        // Update learning progress
        function updateModuleProgress(moduleType, percentage) {
            const moduleElements = document.querySelectorAll('.learning-module');
            const moduleNames = ['fundamentals', 'categories', 'operations', 'maintenance'];
            const moduleIndex = moduleNames.indexOf(moduleType);
            
            if (moduleIndex !== -1 && moduleElements[moduleIndex]) {
                const module = moduleElements[moduleIndex];
                const progressFill = module.querySelector('.progress-fill');
                const progressText = module.querySelector('.progress-text');
                
                if (progressFill && progressText) {
                    progressFill.style.width = percentage + '%';
                    progressText.textContent = percentage + '% Complete';
                }
            }
        }

        // Load saved progress on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚁 General Helicopters page loading...');
            
            // Check for saved progress
            const moduleNames = ['fundamentals', 'categories', 'operations', 'maintenance'];
            moduleNames.forEach(moduleName => {
                if (localStorage.getItem(`module_${moduleName}_started`)) {
                    const progress = Math.floor(Math.random() * 100);
                    updateModuleProgress(moduleName, progress);
                }
            });

            console.log('✅ General Helicopters page loaded successfully');
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>