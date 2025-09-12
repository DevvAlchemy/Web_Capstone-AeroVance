//views/home.php 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AeroVance Helicopter Marketplace - Where Innovation Meets the Sky</title>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- AOS Animation Library -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="/helicopter-marketplace/assets/css/app.css">
</head>
<body>
    <!-- Header -->
    <header class="header" id="header">
        <div class="nav-container">
            <a href="/helicopter-marketplace/views/home.php" class="logo">
                <i class="fas fa-helicopter"></i>
                <span>AeroVance</span>
            </a>
            
     <nav class="nav-menu">

    <a href="/helicopter-marketplace/" class="active">Home</a>
    
    <a href="/helicopter-marketplace/views/catalog.php">Helicopters</a>
    <a href="/helicopter-marketplace/views/personal-helicopters.php">Personal</a>
    <a href="/helicopter-marketplace/views/business-helicopters.php">Business</a>
    <a href="/helicopter-marketplace/views/emergency-helicopters.php">Emergency</a>
    <a href="/helicopter-marketplace/views/about.php">About</a>
    <a href="/helicopter-marketplace/views/contact.php">Contact</a>
</nav>

<div class="auth-buttons">
    <a href="/helicopter-marketplace/views/auth/login.php" class="btn btn-outline">Login</a>
    <a href="/helicopter-marketplace/views/auth/register.php" class="btn btn-primary">Sign Up</a>
</div>

        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="floating-elements">
            <i class="fas fa-helicopter floating-icon" style="top: 20%; left: 10%; animation-delay: 0s;"></i>
            <i class="fas fa-plane floating-icon" style="top: 60%; right: 15%; animation-delay: 2s;"></i>
            <i class="fas fa-rocket floating-icon" style="top: 30%; right: 20%; animation-delay: 4s;"></i>
        </div>
        
        <div class="hero-content">
            <h1>Where Innovation Meets the Sky</h1>
            <p>Pioneering advanced aerial solutions, from state-of-the-art helicopters to cutting-edge drones. We bring the future of flight to every mission.</p>
            
            <div class="hero-buttons">
                <!-- FIXED: Button links match router routes -->
                <a href="/helicopters" class="btn btn-primary btn-large">Browse Helicopters</a>
                <a href="#features" class="btn btn-outline btn-large">Learn More</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-title fade-in">
                <h2>Our Mission</h2>
                <p>Innovation in Aviation</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card fade-in" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3>Personal Aviation</h3>
                    <p>Learn to maintain your flying bird to become a confident soaring enthusiast. From private helicopters to recreational aircraft, experience the freedom of personal flight.</p>
                    <!-- FIXED: Link matches router route -->
                    <a href="/category/personal" class="btn btn-primary" style="margin-top: 20px;">Learn more</a>
                </div>
                
                <div class="feature-card fade-in" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3>Business Solutions</h3>
                    <p>Learn from the best Pilots while Piloting the best aeronautics. Complete machinery training for corporate and commercial aviation needs.</p>
                    <!-- FIXED: Link matches router route -->
                    <a href="/category/business" class="btn btn-primary" style="margin-top: 20px;">Learn more</a>
                </div>
                
                <div class="feature-card fade-in" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-icon">
                        <i class="fas fa-first-aid"></i>
                    </div>
                    <h3>Emergency Services</h3>
                    <p>Test out any of our machinery to determine your perfect Aircraft. Virtual test flight experience for emergency and rescue operations.</p>
                    <!-- FIXED: Link matches router route -->
                    <a href="/category/emergency" class="btn btn-primary" style="margin-top: 20px;">Learn more</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories">
        <div class="container">
            <div class="section-title fade-in">
                <h2>Missions</h2>
                <p>Discover our specialized helicopter categories</p>
            </div>
            
            <div class="categories-grid">
                <!-- FIXED: All category links match router routes -->
                <a href="/category/personal" class="category-card fade-in" data-aos="fade-right">
                    <div class="category-image" style="background-image: url('/assets/images/personal-helicopter.jpg');">
                        <div class="category-overlay">
                            <h3>Personal</h3>
                            <p>Private helicopters for recreational flying and personal transportation</p>
                        </div>
                    </div>
                </a>
                
                <a href="/category/business" class="category-card fade-in" data-aos="fade-up">
                    <div class="category-image" style="background-image: url('/assets/images/business-helicopter.jpg');">
                        <div class="category-overlay">
                            <h3>Business</h3>
                            <p>Corporate and commercial helicopters for professional operations</p>
                        </div>
                    </div>
                </a>
                
                <a href="/category/emergency" class="category-card fade-in" data-aos="fade-left">
                    <div class="category-image" style="background-image: url('/assets/images/emergency-helicopter.jpg');">
                        <div class="category-overlay">
                            <h3>Emergency</h3>
                            <p>Medical, rescue, and law enforcement specialized aircraft</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Helicopters -->
    <section class="featured-helicopters">
        <div class="container">
            <div class="section-title fade-in">
                <h2>Featured Aircraft</h2>
                <p>Discover our premium helicopter collection</p>
            </div>
            
            <div class="helicopters-grid">
                <!-- Demo Helicopter 1 -->
                <div class="helicopter-card fade-in" data-aos="fade-up">
                    <div class="helicopter-image" style="background-image: url('/assets/images/helicopter-robinson-r44.jpg');">
                        <img src="/assets/images/helicopter-robinson-r44.jpg
                        " alt="">
                        <div class="price-tag">$505,000</div>
                    </div>
                    <div class="helicopter-info">
                        <h3>Robinson R44 Raven II</h3>
                        <p>The Robinson R44 Raven II is a four-seat light helicopter produced by Robinson Helicopter Company. Perfect for personal use and flight training.</p>
                        
                        <div class="helicopter-specs">
                            <div class="spec">
                                <span class="spec-value">130</span>
                                <span class="spec-label">Max Speed (mph)</span>
                            </div>
                            <div class="spec">
                                <span class="spec-value">348</span>
                                <span class="spec-label">Range (mi)</span>
                            </div>
                            <div class="spec">
                                <span class="spec-value">4</span>
                                <span class="spec-label">Passengers</span>
                            </div>
                        </div>
                        
                        <div class="helicopter-actions">
                            <!-- FIXED: Helicopter detail link matches router route pattern -->
                            <a href="/helicopter/1" class="btn btn-primary btn-full">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Demo Helicopter 2 -->
                <div class="helicopter-card fade-in" data-aos="fade-up">
                    <div class="helicopter-image" style="background-image: url('/assets/images/helicopters/bell-407gxi-1.jpg');">
                        <div class="price-tag">$2,900,000</div>
                    </div>
                    <div class="helicopter-info">
                        <h3>Bell 407GXi</h3>
                        <p>The Bell 407GXi is a multi-purpose helicopter ideal for corporate transport, emergency medical services, and law enforcement operations.</p>
                        
                        <div class="helicopter-specs">
                            <div class="spec">
                                <span class="spec-value">140</span>
                                <span class="spec-label">Max Speed (mph)</span>
                            </div>
                            <div class="spec">
                                <span class="spec-value">374</span>
                                <span class="spec-label">Range (mi)</span>
                            </div>
                            <div class="spec">
                                <span class="spec-value">7</span>
                                <span class="spec-label">Passengers</span>
                            </div>
                        </div>
                        
                        <div class="helicopter-actions">
                            <a href="/helicopter/2" class="btn btn-primary btn-full">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Demo Helicopter 3 -->
                <div class="helicopter-card fade-in" data-aos="fade-up">
                    <div class="helicopter-image" style="background-image: url('/assets/images/helicopters/h145-1.jpg');">
                        <div class="price-tag">$9,500,000</div>
                    </div>
                    <div class="helicopter-info">
                        <h3>Airbus H145</h3>
                        <p>The Airbus H145 is a twin-engine helicopter designed for emergency medical services, search and rescue, and law enforcement missions.</p>
                        
                        <div class="helicopter-specs">
                            <div class="spec">
                                <span class="spec-value">150</span>
                                <span class="spec-label">Max Speed (mph)</span>
                            </div>
                            <div class="spec">
                                <span class="spec-value">431</span>
                                <span class="spec-label">Range (mi)</span>
                            </div>
                            <div class="spec">
                                <span class="spec-value">9</span>
                                <span class="spec-label">Passengers</span>
                            </div>
                        </div>
                        
                        <div class="helicopter-actions">
                            <a href="/helicopter/3" class="btn btn-primary btn-full">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- News Section -->
    <section class="news">
        <div class="container">
            <div class="section-title fade-in">
                <h2>Latest News</h2>
                <p>Stay updated with aviation industry developments</p>
            </div>
            
            <div class="news-grid">
                <article class="news-card fade-in" data-aos="fade-up">
                    <div class="news-image" style="background-image: url('/assets/images/news-1.jpg');"></div>
                    <div class="news-content">
                        <div class="news-date">June 15, 2025</div>
                        <h3>New Helicopter Fleet Arrives</h3>
                        <p>The first flight of our newest helicopter model proved beyond expectations. With increased fuel efficiency and cutting-edge performance...</p>
                    </div>
                </article>
                
                <article class="news-card fade-in" data-aos="fade-up" data-aos-delay="100">
                    <div class="news-image" style="background-image: url('/assets/images/news-2.jpg');"></div>
                    <div class="news-content">
                        <div class="news-date">June 10, 2025</div>
                        <h3>Advanced Technology Integration</h3>
                        <p>Our helicopters are transforming the healthcare industry by providing fast transport and cutting-edge medical emergencies...</p>
                    </div>
                </article>
                
                <article class="news-card fade-in" data-aos="fade-up" data-aos-delay="200">
                    <div class="news-image" style="background-image: url('/assets/images/news-3.jpg');"></div>
                    <div class="news-content">
                        <div class="news-date">June 5, 2025</div>
                        <h3>Customer Success Stories</h3>
                        <p>Hear from satisfied customers who have transformed their operations with our reliable, advanced technology solutions...</p>
                    </div>
                </article>
            </div>
            
            <div style="text-align: center; margin-top: 40px;">
                <a href="/news" class="btn btn-primary btn-large">Read More</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="/helicopter-marketplace/views/catalog.php">Browse Helicopters</a></li>
                        <li><a href="/helicopter-marketplace/views/personal-helicopters.php">Personal Aircraft</a></li>
                        <li><a href="/helicopter-marketplace/views/business-helicopters.php">Business Solutions</a></li>
                        <li><a href="/helicopter-marketplace/views/emergency-helicopters.php">Emergency Services</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="/contact">Contact Us</a></li>
                        <li><a href="/support">Customer Support</a></li>
                        <li><a href="/financing">Financing Options</a></li>
                        <li><a href="/maintenance">Maintenance Services</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="/about">About Us</a></li>
                        <li><a href="/careers">Careers</a></li>
                        <li><a href="/news">News & Updates</a></li>
                        <li><a href="/investors">Investors</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Connect With Us</h4>
                    <p>Follow us for the latest updates and aviation news</p>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2025 AeroVance. All rights reserved. | <a href="/privacy">Privacy Policy</a> | <a href="/terms">Terms of Service</a></p>
            </div>
        </div>
    </footer>

    <!-- Scroll progress indicator -->
    <div class="scroll-indicator" id="scroll-indicator"></div>

    <!-- JavaScript Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    
    <script>
        // Initialize AOS animations
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });

        // Header scroll effect - adds visual feedback when scrolling
        window.addEventListener('scroll', () => {
            const header = document.getElementById('header');
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Scroll progress indicator - shows page scroll progress
        window.addEventListener('scroll', () => {
            const scrollTop = document.documentElement.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrollProgress = (scrollTop / scrollHeight) * 100;
            
            const indicator = document.getElementById('scroll-indicator');
            if (indicator) {
                indicator.style.width = scrollProgress + '%';
            }
        });

        // Smooth scrolling for anchor links - better UX for internal navigation
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

        // Intersection Observer for fade-in animations
        // This creates a more modern approach to scroll animations
        const observerOptions = {
            threshold: 0.1, // Trigger when 10% of element is visible
            rootMargin: '0px 0px -50px 0px' // Start animation 50px before element enters viewport
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        // Apply observer to all fade-in elements
        document.querySelectorAll('.fade-in').forEach(el => {
            observer.observe(el);
        });

        // Success message for debugging
        console.log('✅ AeroVance loaded successfully');
    </script>
</body>
</html>