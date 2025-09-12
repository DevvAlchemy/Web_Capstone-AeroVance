<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - AeroVance </title>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- AOS Animation Library -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="/helicopter-marketplace/assets/css/app.css">
    
    <style>
        /* Contact Form Specific Styles */
        .contact-form-section {
            background: #333333;
            border-radius: 15px;
            padding: 40px;
            border: 1px solid rgba(255, 107, 53, 0.2);
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            color: #FF6B35;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 1rem;
        }
        
        .form-control {
            width: 100%;
            padding: 15px;
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
        
        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .contact-info-card {
            background: #333333;
            border-radius: 15px;
            padding: 30px;
            border: 1px solid rgba(255, 107, 53, 0.2);
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .contact-info-card:hover {
            transform: translateY(-5px);
            border-color: #FF6B35;
            box-shadow: 0 10px 30px rgba(255, 107, 53, 0.2);
        }
        
        .contact-icon {
            font-size: 2.5rem;
            color: #FF6B35;
            margin-bottom: 20px;
        }
        
        .contact-info-card h4 {
            color: #ffffff;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }
        
        .contact-info-card p {
            color: #cccccc;
            line-height: 1.6;
        }
        
        .contact-info-card a {
            color: #FF6B35;
            text-decoration: none;
        }
        
        .contact-info-card a:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
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
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header" id="header">
        <div class="nav-container">
            <a href="/helicopter-marketplace/views/home.php" class="logo">
                <i class="fas fa-helicopter"></i>
                <span>AEROVANCE</span>
            </a>
            
            <nav class="nav-menu">
                <a href="/helicopter-marketplace/views/home.php" class="active">Home</a>
                <!-- Helicopter catalog - matches router->get('/helicopters') -->
                <a href="/helicopter-marketplace/views/general-helicopters.php">Helicopters</a>
                <a href="/helicopter-marketplace/views/personal-helicopters.php">Personal</a>
                <a href="/helicopter-marketplace/views/business-helicopters.php">Business</a>
                <a href="/helicopter-marketplace/views/emergency-helicopters.php">Emergency</a>
                <!-- Static pages - these have fallback content if files don't exist -->
                <a href="views/about">About</a>
                <a href="/views/contact">Contact</a><a href="/helicopter-marketplace/views/about.php">About</a>
                <a href="/helicopter-marketplace/views/contact.php" class="active">Contact</a>
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
            <i class="fas fa-envelope floating-icon" style="top: 20%; left: 10%; animation-delay: 0s;"></i>
            <i class="fas fa-phone floating-icon" style="top: 60%; right: 15%; animation-delay: 2s;"></i>
            <i class="fas fa-map-marker-alt floating-icon" style="top: 30%; right: 20%; animation-delay: 4s;"></i>
        </div>
        
        <div class="hero-content">
            <h1>Get in Touch</h1>
            <p>Ready to find your perfect aircraft? Have questions about our services? Our aviation experts are here to help you every step of the way.</p>
            
            <div class="hero-buttons">
                <a href="#contact-form" class="btn btn-primary btn-large">Send Message</a>
                <a href="#contact-info" class="btn btn-outline btn-large">Contact Info</a>
            </div>
        </div>
    </section>

    <!-- Contact Information Section -->
    <section class="features" id="contact-info">
        <div class="container">
            <div class="section-title fade-in">
                <h2>Contact Information</h2>
                <p>Multiple ways to reach our team</p>
            </div>
            
            <div class="features-grid">
                <div class="contact-info-card fade-in" data-aos="fade-up" data-aos-delay="100">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h4>Phone</h4>
                    <p>
                        <strong>Sales:</strong> <a href="tel:+1-555-HELI-001">+1 (555) HELI-001</a><br>
                        <strong>Support:</strong> <a href="tel:+1-555-HELI-002">+1 (555) HELI-002</a><br>
                        <strong>Emergency:</strong> <a href="tel:+1-555-HELI-911">+1 (555) HELI-911</a>
                    </p>
                    <p style="font-size: 0.9rem; margin-top: 15px;">
                        Available 24/7 for emergencies<br>
                        Business hours: 8 AM - 8 PM EST
                    </p>
                </div>
                
                <div class="contact-info-card fade-in" data-aos="fade-up" data-aos-delay="200">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h4>Email</h4>
                    <p>
                        <strong>General:</strong> <a href="mailto:info@helimarket.com">info@helimarket.com</a><br>
                        <strong>Sales:</strong> <a href="mailto:sales@helimarket.com">sales@helimarket.com</a><br>
                        <strong>Support:</strong> <a href="mailto:support@helimarket.com">support@helimarket.com</a>
                    </p>
                    <p style="font-size: 0.9rem; margin-top: 15px;">
                        We respond to all inquiries<br>
                        within 24 hours
                    </p>
                </div>
                
                <div class="contact-info-card fade-in" data-aos="fade-up" data-aos-delay="300">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h4>Headquarters</h4>
                    <p>
                        2500 Aviation Blvd<br>
                        Suite 400<br>
                        Toronto, ON K3L 1H2<br>
                        Canada
                    </p>
                    <p style="font-size: 0.9rem; margin-top: 15px;">
                        Visit by appointment<br>
                        <a href="#">Get Directions</a>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="categories" id="contact-form">
        <div class="container">
            <div class="section-title fade-in">
                <h2>Send Us a Message</h2>
                <p>Tell us about your aviation needs</p>
            </div>
            
            <div style="max-width: 800px; margin: 0 auto;">
                <div class="contact-form-section fade-in" data-aos="fade-up">
                    <!-- Alert Messages -->
                    <div id="success-alert" class="alert alert-success">
                        <i class="fas fa-check-circle" style="margin-right: 10px;"></i>
                        <span>Thank you for your message! We'll get back to you within 24 hours.</span>
                    </div>
                    
                    <div id="error-alert" class="alert alert-error">
                        <i class="fas fa-exclamation-circle" style="margin-right: 10px;"></i>
                        <span>Please fill in all required fields and try again.</span>
                    </div>
                    
                    <form id="contact-form" method="POST" action="/helicopter-marketplace/views/contact.php">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First Name *</label>
                                <input type="text" id="first_name" name="first_name" class="form-control" placeholder="Your first name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="last_name">Last Name *</label>
                                <input type="text" id="last_name" name="last_name" class="form-control" placeholder="Your last name" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="your.email@example.com" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" class="form-control" placeholder="+1 (555) 123-4567">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Subject *</label>
                            <select id="subject" name="subject" class="form-control" required>
                                <option value="sales">Sales Inquiry</option>
                                <option value="support">Technical Support</option>
                                <option value="financing">Financing Questions</option>
                                <option value="maintenance">Maintenance Services</option>
                                <option value="partnership">Partnership Opportunities</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="company">Company/Organization</label>
                            <input type="text" id="company" name="company" class="form-control" placeholder="Your company name (optional)">
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message *</label>
                            <textarea id="message" name="message" class="form-control" rows="6" placeholder="Tell us about your aviation needs, questions, or how we can help you..." required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label style="display: flex; align-items: center; gap: 10px; color: #cccccc; font-size: 0.9rem;">
                                <input type="checkbox" name="newsletter" value="1" style="accent-color: #FF6B35;">
                                <span>I'd like to receive updates about new aircraft and industry news</span>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-large" style="width: 100%;">
                            <i class="fas fa-paper-plane"></i>
                            Send Message
                        </button>
                        
                        <p style="color: #aaaaaa; font-size: 0.85rem; text-align: center; margin-top: 20px;">
                            * Required fields. Your information is secure and will never be shared with third parties.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="featured-helicopters">
        <div class="container">
            <div class="section-title fade-in">
                <h2>Frequently Asked Questions</h2>
                <p>Quick answers to common questions</p>
            </div>
            
            <div class="news-grid">
                <div class="news-card fade-in" data-aos="fade-up">
                    <div class="news-content">
                        <h3 style="color: #FF6B35; margin-bottom: 15px;">How do I schedule a helicopter inspection?</h3>
                        <p>Contact our sales team to arrange a comprehensive pre-purchase inspection. We work with certified mechanics and can coordinate inspections at your preferred location.</p>
                    </div>
                </div>
                
                <div class="news-card fade-in" data-aos="fade-up" data-aos-delay="100">
                    <div class="news-content">
                        <h3 style="color: #FF6B35; margin-bottom: 15px;">What financing options are available?</h3>
                        <p>We partner with leading aviation lenders to offer competitive financing packages. Our finance team can help structure loans, leases, and other arrangements to meet your needs.</p>
                    </div>
                </div>
                
                <div class="news-card fade-in" data-aos="fade-up" data-aos-delay="200">
                    <div class="news-content">
                        <h3 style="color: #FF6B35; margin-bottom: 15px;">Do you offer international delivery?</h3>
                        <p>Yes! We handle global deliveries and can manage all export documentation, shipping logistics, and customs clearance to deliver your aircraft anywhere in the world.</p>
                    </div>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 40px;">
                <a href="/faq" class="btn btn-outline btn-large">View All FAQs</a>
            </div>
        </div>
    </section>

    <!-- Office Hours Section -->
    <section class="news">
        <div class="container">
            <div class="section-title fade-in">
                <h2>Office Hours & Availability</h2>
                <p>When our team is available to assist you</p>
            </div>
            
            <div style="max-width: 600px; margin: 0 auto;">
                <div class="contact-form-section fade-in" data-aos="fade-up">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; text-align: center;">
                        <div>
                            <h4 style="color: #FF6B35; margin-bottom: 15px;">
                                <i class="fas fa-clock" style="margin-right: 10px;"></i>
                                Business Hours
                            </h4>
                            <p style="color: #cccccc; line-height: 1.8;">
                                <strong>Monday - Friday:</strong> 8:00 AM - 8:00 PM EST<br>
                                <strong>Saturday:</strong> 9:00 AM - 5:00 PM EST<br>
                                <strong>Sunday:</strong> 10:00 AM - 4:00 PM EST
                            </p>
                        </div>
                        
                        <div>
                            <h4 style="color: #FF6B35; margin-bottom: 15px;">
                                <i class="fas fa-exclamation-triangle" style="margin-right: 10px;"></i>
                                Emergency Support
                            </h4>
                            <p style="color: #cccccc; line-height: 1.8;">
                                <strong>24/7 Emergency Line:</strong><br>
                                <a href="tel:+1-555-HELI-911" style="color: #FF6B35;">+1 (555) HELI-911</a><br>
                                For urgent technical support<br>
                                and emergency assistance
                            </p>
                        </div>
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
                       <li><a href="/helicopter-marketplace/views/catalog.php">Browse Helicopters</a></li>
                        <li><a href="/helicopter-marketplace/views/personal-helicopters.php">Personal Aircraft</a></li>
                        <li><a href="/helicopter-marketplace/views/business-helicopters.php">Business Solutions</a></li>
                        <li><a href="/helicopter-marketplace/views/emergency-helicopters.php">Emergency Services</a></li>
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

        // Contact form handling
        document.getElementById('contact-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Basic form validation
            const requiredFields = ['first_name', 'last_name', 'email', 'subject', 'message'];
            let isValid = true;
            
            requiredFields.forEach(field => {
                const input = document.getElementById(field);
                if (!input.value.trim()) {
                    isValid = false;
                    input.style.borderColor = '#dc3545';
                } else {
                    input.style.borderColor = '#555555';
                }
            });
            
            // Email validation
            const email = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email.value)) {
                isValid = false;
                email.style.borderColor = '#dc3545';
            }
            
            if (isValid) {
                // Show success message
                document.getElementById('success-alert').style.display = 'block';
                document.getElementById('error-alert').style.display = 'none';
                
                // Reset form
                this.reset();
                
                // Scroll to success message
                document.getElementById('success-alert').scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
                
                // In a real application, you would submit the form data here
                console.log('Form would be submitted to server');
                
            } else {
                // Show error message
                document.getElementById('error-alert').style.display = 'block';
                document.getElementById('success-alert').style.display = 'none';
                
                // Scroll to error message
                document.getElementById('error-alert').scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            }
        });

        // Phone number formatting
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 6) {
                value = value.slice(0, 3) + '-' + value.slice(3, 6) + '-' + value.slice(6, 10);
            } else if (value.length >= 3) {
                value = value.slice(0, 3) + '-' + value.slice(3);
            }
            e.target.value = value;
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

        console.log('✅ Contact page loaded successfully');
    </script>
 