<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Helicopter Marketplace</title>
    
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
                <span>Helicopter Marketplace</span>
            </a>
            
            <nav class="nav-menu">
                <a href="/helicopter-marketplace/views/home.php">Home</a>
                <a href="/helicopters">Helicopters</a>
                <a href="/category/personal">Personal</a>
                <a href="/category/business">Business</a>
                <a href="/category/emergency">Emergency</a>
                <a href="/helicopter-marketplace/views/about.php" class="active">About</a>
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
            <i class="fas fa-building floating-icon" style="top: 15%; left: 8%; animation-delay: 0s;"></i>
            <i class="fas fa-users floating-icon" style="top: 70%; right: 12%; animation-delay: 2s;"></i>
            <i class="fas fa-award floating-icon" style="top: 25%; right: 25%; animation-delay: 4s;"></i>
        </div>
        
        <div class="hero-content">
            <h1>Leading the Future of Aviation</h1>
            <p>For over two decades, we've been connecting pilots, businesses, and aviation enthusiasts with the world's finest helicopters. Our commitment to excellence drives everything we do.</p>
            
            <div class="hero-buttons">
                <a href="#story" class="btn btn-primary btn-large">Our Story</a>
                <a href="#team" class="btn btn-outline btn-large">Meet the Team</a>
            </div>
        </div>
    </section>

    <!-- Company Story Section -->
    <section class="features" id="story">
        <div class="container">
            <div class="section-title fade-in">
                <h2>Our Story</h2>
                <p>Two Decades of Aviation Excellence</p>
            </div>
            
            <div class="story-content fade-in" style="max-width: 800px; margin: 0 auto; text-align: center; color: #cccccc; font-size: 1.1rem; line-height: 1.8;">
                <p style="margin-bottom: 30px;">
                    Founded in 2003 by a team of aviation enthusiasts and industry experts, AeroVance began with a simple mission: to democratize access to premium aircraft while maintaining the highest standards of safety and quality.
                </p>
                <p style="margin-bottom: 30px;">
                    What started as a small operation has grown into the industry's most trusted platform, facilitating over $2 billion in helicopter transactions and serving customers across six continents.
                </p>
                <p>
                    Today, we continue to innovate, leveraging cutting-edge technology to make helicopter ownership more accessible while never compromising on the personal service that has made us an industry leader.
                </p>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="categories">
        <div class="container">
            <div class="section-title fade-in">
                <h2>Our Values</h2>
                <p>The principles that guide everything we do</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card fade-in" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Safety First</h3>
                    <p>Every aircraft in our marketplace undergoes rigorous inspection and certification processes. We never compromise on safety standards, ensuring peace of mind for every customer.</p>
                </div>
                
                <div class="feature-card fade-in" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>Trust & Transparency</h3>
                    <p>We believe in honest, transparent dealings. Every helicopter listing includes complete maintenance records, detailed specifications, and verified seller credentials.</p>
                </div>
                
                <div class="feature-card fade-in" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h3>Innovation</h3>
                    <p>We continuously evolve our platform using the latest technology to provide better search capabilities, virtual tours, and seamless transaction experiences.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="featured-helicopters">
        <div class="container">
            <div class="section-title fade-in">
                <h2>By the Numbers</h2>
                <p>Our impact on the aviation industry</p>
            </div>
            
            <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
                <div class="stat-card fade-in" data-aos="fade-up" style="background: #333333; border-radius: 15px; padding: 40px; text-align: center; border: 1px solid rgba(255, 107, 53, 0.2);">
                    <div class="stat-number" style="font-size: 3rem; font-weight: bold; color: #FF6B35; margin-bottom: 10px;">2,500+</div>
                    <h4 style="color: #ffffff; margin-bottom: 10px;">Aircraft Sold</h4>
                    <p style="color: #cccccc;">Successfully connected buyers with their perfect helicopter</p>
                </div>
                
                <div class="stat-card fade-in" data-aos="fade-up" data-aos-delay="100" style="background: #333333; border-radius: 15px; padding: 40px; text-align: center; border: 1px solid rgba(255, 107, 53, 0.2);">
                    <div class="stat-number" style="font-size: 3rem; font-weight: bold; color: #FF6B35; margin-bottom: 10px;">$2.1B</div>
                    <h4 style="color: #ffffff; margin-bottom: 10px;">Total Sales</h4>
                    <p style="color: #cccccc;">In verified helicopter transactions worldwide</p>
                </div>
                
                <div class="stat-card fade-in" data-aos="fade-up" data-aos-delay="200" style="background: #333333; border-radius: 15px; padding: 40px; text-align: center; border: 1px solid rgba(255, 107, 53, 0.2);">
                    <div class="stat-number" style="font-size: 3rem; font-weight: bold; color: #FF6B35; margin-bottom: 10px;">45</div>
                    <h4 style="color: #ffffff; margin-bottom: 10px;">Countries Served</h4>
                    <p style="color: #cccccc;">Global reach with local expertise</p>
                </div>
                
                <div class="stat-card fade-in" data-aos="fade-up" data-aos-delay="300" style="background: #333333; border-radius: 15px; padding: 40px; text-align: center; border: 1px solid rgba(255, 107, 53, 0.2);">
                    <div class="stat-number" style="font-size: 3rem; font-weight: bold; color: #FF6B35; margin-bottom: 10px;">22</div>
                    <h4 style="color: #ffffff; margin-bottom: 10px;">Years Experience</h4>
                    <p style="color: #cccccc;">Trusted industry expertise since 2003</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="news" id="team">
        <div class="container">
            <div class="section-title fade-in">
                <h2>Leadership Team</h2>
                <p>Meet the experts driving our mission forward</p>
            </div>
            
            <div class="news-grid">
                <div class="news-card fade-in" data-aos="fade-up">
                    <div class="news-image" style="background-image: url('/assets/images/team-ceo.jpg'); background-color: #444444;"></div>
                    <div class="news-content">
                        <div class="news-date">Chief Executive Officer</div>
                        <h3>Sarah Mitchell</h3>
                        <p>Former military pilot with 25 years in aviation. Sarah leads our strategic vision and oversees global operations with an unwavering commitment to safety and innovation.</p>
                    </div>
                </div>
                
                <div class="news-card fade-in" data-aos="fade-up" data-aos-delay="100">
                    <div class="news-image" style="background-image: url('/assets/images/team-cto.jpg'); background-color: #444444;"></div>
                    <div class="news-content">
                        <div class="news-date">Chief Technology Officer</div>
                        <h3>Marcus Chen</h3>
                        <p>Technology visionary who pioneered our advanced marketplace platform. Marcus ensures our technical infrastructure meets the demanding needs of aviation professionals.</p>
                    </div>
                </div>
                
                <div class="news-card fade-in" data-aos="fade-up" data-aos-delay="200">
                    <div class="news-image" style="background-image: url('/assets/images/team-sales.jpg'); background-color: #444444;"></div>
                    <div class="news-content">
                        <div class="news-date">VP of Sales</div>
                        <h3>David Rodriguez</h3>
                        <p>Industry veteran with deep relationships across the helicopter ecosystem. David leads our sales team in delivering exceptional customer experiences worldwide.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Statement -->
    <section class="features" style="background: #2a2a2a;">
        <div class="container">
            <div class="section-title fade-in">
                <h2>Our Mission</h2>
                <p>Connecting the aviation community worldwide</p>
            </div>
            
            <div class="mission-content fade-in" style="max-width: 900px; margin: 0 auto; text-align: center;">
                <div style="background: linear-gradient(135deg, rgba(255, 107, 53, 0.1), rgba(255, 140, 66, 0.05)); border: 1px solid rgba(255, 107, 53, 0.3); border-radius: 15px; padding: 50px;">
                    <h3 style="color: #FF6B35; font-size: 1.8rem; margin-bottom: 25px;">Empowering Aviation Dreams</h3>
                    <p style="color: #cccccc; font-size: 1.2rem; line-height: 1.8; margin-bottom: 30px;">
                        We believe that exceptional aircraft should be accessible to those who need them most. Whether you're a emergency services organization saving lives, a business executive optimizing operations, or an individual pursuing your passion for flight, we're here to make it happen.
                    </p>
                    <div style="display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
                        <a href="/helicopter-marketplace/views/contact.php" class="btn btn-primary">Get in Touch</a>
                        <a href="/helicopters" class="btn btn-outline">View Aircraft</a>
                    </div>
                </div>
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
                        <li><a href="/helicopters">Browse Helicopters</a></li>
                        <li><a href="/category/personal">Personal Aircraft</a></li>
                        <li><a href="/category/business">Business Solutions</a></li>
                        <li><a href="/category/emergency">Emergency Services</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="/helicopter-marketplace/views/contact.php">Contact Us</a></li>
                        <li><a href="/support">Customer Support</a></li>
                        <li><a href="/financing">Financing Options</a></li>
                        <li><a href="/maintenance">Maintenance Services</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="/helicopter-marketplace/views/about.php">About Us</a></li>
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
                <p>&copy; 2025 Helicopter Marketplace. All rights reserved. | <a href="/privacy">Privacy Policy</a> | <a href="/terms">Terms of Service</a></p>
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

        // Header scroll effect
        window.addEventListener('scroll', () => {
            const header = document.getElementById('header');
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Scroll progress indicator
        window.addEventListener('scroll', () => {
            const scrollTop = document.documentElement.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrollProgress = (scrollTop / scrollHeight) * 100;
            
            const indicator = document.getElementById('scroll-indicator');
            if (indicator) {
                indicator.style.width = scrollProgress + '%';
            }
        });

        // Smooth scrolling for anchor links
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

        // Fade in animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in').forEach(el => {
            observer.observe(el);
        });

        console.log('✅ About page loaded successfully');
    </script>
</body>
</html>