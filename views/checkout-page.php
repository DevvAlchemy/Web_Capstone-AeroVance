<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Helicopter Marketplace</title>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="/helicopter-marketplace/assets/css/app.css">
    
    <style>
        /* Checkout Page Specific Styles */
        .checkout-page {
            margin-top: 100px;
            padding: 40px 0;
            background: #1a1a1a;
        }
        
        .checkout-header {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .checkout-header h1 {
            font-size: 2.5rem;
            color: #FF6B35;
            margin-bottom: 10px;
        }
        
        /* Progress Steps */
        .checkout-progress {
            display: flex;
            justify-content: center;
            margin-bottom: 50px;
            padding: 0 20px;
        }
        
        .progress-steps {
            display: flex;
            align-items: center;
            gap: 30px;
            max-width: 600px;
            width: 100%;
        }
        
        .progress-step {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
        }
        
        .step-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #333333;
            border: 2px solid #555555;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #555555;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .progress-step.active .step-icon,
        .progress-step.completed .step-icon {
            background: #FF6B35;
            border-color: #FF6B35;
            color: white;
        }
        
        .progress-step.completed .step-icon {
            background: #28a745;
            border-color: #28a745;
        }
        
        .step-label {
            font-size: 0.9rem;
            color: #555555;
            transition: color 0.3s ease;
        }
        
        .progress-step.active .step-label,
        .progress-step.completed .step-label {
            color: #ffffff;
        }
        
        .progress-line {
            flex: 1;
            height: 2px;
            background: #555555;
            position: relative;
        }
        
        .progress-line.completed {
            background: #28a745;
        }
        
        /* Checkout Layout */
        .checkout-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
            align-items: start;
        }
        
        /* Checkout Form */
        .checkout-form {
            background: #333333;
            border-radius: 15px;
            padding: 40px;
            border: 1px solid rgba(255, 107, 53, 0.2);
        }
        
        .form-section {
            margin-bottom: 40px;
        }
        
        .form-section:last-child {
            margin-bottom: 0;
        }
        
        .section-title {
            color: #FF6B35;
            font-size: 1.5rem;
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
        
        .form-control.error {
            border-color: #dc3545;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 0.8rem;
            margin-top: 5px;
        }
        
        /* Payment Methods */
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .payment-method {
            border: 2px solid #555555;
            border-radius: 10px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .payment-method:hover {
            border-color: #FF6B35;
            background: rgba(255, 107, 53, 0.05);
        }
        
        .payment-method.selected {
            border-color: #FF6B35;
            background: rgba(255, 107, 53, 0.1);
        }
        
        .payment-method i {
            font-size: 2rem;
            color: #FF6B35;
            margin-bottom: 10px;
        }
        
        .payment-method h4 {
            color: #ffffff;
            font-size: 1rem;
            margin: 0;
        }
        
        /* Card Input */
        .card-input-group {
            position: relative;
            margin-bottom: 20px;
        }
        
        .card-input-group .form-control {
            padding-left: 50px;
        }
        
        .card-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #FF6B35;
            font-size: 1.5rem;
        }
        
        /* Security Badge */
        .security-badge {
            background: rgba(40, 167, 69, 0.1);
            border: 1px solid #28a745;
            border-radius: 8px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }
        
        .security-badge i {
            color: #28a745;
            font-size: 1.5rem;
        }
        
        .security-badge p {
            color: #cccccc;
            font-size: 0.9rem;
            margin: 0;
        }
        
        /* Order Summary Sidebar */
        .order-summary {
            position: sticky;
            top: 100px;
        }
        
        .summary-card {
            background: #333333;
            border-radius: 15px;
            padding: 30px;
            border: 1px solid rgba(255, 107, 53, 0.2);
        }
        
        .summary-header {
            color: #FF6B35;
            font-size: 1.5rem;
            margin-bottom: 25px;
        }
        
        .order-items {
            margin-bottom: 20px;
        }
        
        .order-item {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #444444;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .order-item-image {
            width: 80px;
            height: 60px;
            background: #444444;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .order-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .order-item-details {
            flex: 1;
        }
        
        .order-item-details h4 {
            color: #ffffff;
            font-size: 0.95rem;
            margin-bottom: 5px;
        }
        
        .order-item-details p {
            color: #aaaaaa;
            font-size: 0.8rem;
            margin: 0;
        }
        
        .order-item-price {
            color: #FF6B35;
            font-weight: 600;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            color: #cccccc;
            font-size: 0.95rem;
        }
        
        .summary-row.total {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #444444;
            font-size: 1.2rem;
            font-weight: 600;
            color: #ffffff;
        }
        
        .summary-row.total .amount {
            color: #FF6B35;
        }
        
        .place-order-btn {
            width: 100%;
            margin-top: 20px;
            padding: 15px;
            font-size: 1.1rem;
        }
        
        /* Terms Checkbox */
        .terms-checkbox {
            display: flex;
            align-items: start;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .terms-checkbox input[type="checkbox"] {
            margin-top: 2px;
            accent-color: #FF6B35;
        }
        
        .terms-checkbox label {
            color: #cccccc;
            font-size: 0.9rem;
            line-height: 1.4;
        }
        
        .terms-checkbox a {
            color: #FF6B35;
        }
        
        /* Responsive */
        @media (max-width: 991px) {
            .checkout-container {
                grid-template-columns: 1fr;
            }
            
            .order-summary {
                position: static;
                margin-top: 30px;
            }
        }
        
        @media (max-width: 768px) {
            .checkout-form {
                padding: 25px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .payment-methods {
                grid-template-columns: 1fr;
            }
            
            .progress-steps {
                gap: 15px;
            }
            
            .step-label {
                display: none;
            }
            
            .progress-line {
                width: 30px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header" id="header">
        <div class="nav-container">
            <a href="/" class="logo">
                <i class="fas fa-helicopter"></i>
                <span>Helicopter Marketplace</span>
            </a>
            
            <nav class="nav-menu">
                <a href="/">Home</a>
                <a href="/helicopters">Helicopters</a>
                <a href="/category/personal">Personal</a>
                <a href="/category/business">Business</a>
                <a href="/category/emergency">Emergency</a>
                <a href="/about">About</a>
                <a href="/contact">Contact</a>
            </nav>
            
            <div class="auth-buttons">
                <a href="/cart" class="btn btn-outline">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count">2</span>
                </a>
                <a href="/account" class="btn btn-primary">My Account</a>
            </div>
        </div>
    </header>

    <!-- Checkout Page -->
    <section class="checkout-page">
        <div class="container">
            <div class="checkout-header">
                <h1><i class="fas fa-lock"></i> Secure Checkout</h1>
            </div>

            <!-- Progress Steps -->
            <div class="checkout-progress">
                <div class="progress-steps">
                    <div class="progress-step completed">
                        <div class="step-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <span class="step-label">Cart</span>
                    </div>
                    <div class="progress-line completed"></div>
                    <div class="progress-step active">
                        <div class="step-icon">2</div>
                        <span class="step-label">Information</span>
                    </div>
                    <div class="progress-line"></div>
                    <div class="progress-step">
                        <div class="step-icon">3</div>
                        <span class="step-label">Payment</span>
                    </div>
                    <div class="progress-line"></div>
                    <div class="progress-step">
                        <div class="step-icon">4</div>
                        <span class="step-label">Confirmation</span>
                    </div>
                </div>
            </div>

            <div class="checkout-container">
                <!-- Checkout Form -->
                <div class="checkout-form">
                    <form id="checkoutForm">
                        <!-- Contact Information -->
                        <div class="form-section">
                            <h2 class="section-title">
                                <i class="fas fa-user"></i> Contact Information
                            </h2>
                            
                            <div class="form-group full-width">
                                <label>Email Address <span class="required">*</span></label>
                                <input type="email" class="form-control" required placeholder="john@example.com">
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>First Name <span class="required">*</span></label>
                                    <input type="text" class="form-control" required placeholder="John">
                                </div>
                                
                                <div class="form-group">
                                    <label>Last Name <span class="required">*</span></label>
                                    <input type="text" class="form-control" required placeholder="Doe">
                                </div>
                            </div>
                            
                            <div class="form-group full-width">
                                <label>Phone Number <span class="required">*</span></label>
                                <input type="tel" class="form-control" required placeholder="+1 (555) 123-4567">
                            </div>
                        </div>

                        <!-- Billing Address -->
                        <div class="form-section">
                            <h2 class="section-title">
                                <i class="fas fa-map-marker-alt"></i> Billing Address
                            </h2>
                            
                            <div class="form-group full-width">
                                <label>Company Name (Optional)</label>
                                <input type="text" class="form-control" placeholder="Aviation Partners LLC">
                            </div>
                            
                            <div class="form-group full-width">
                                <label>Street Address <span class="required">*</span></label>
                                <input type="text" class="form-control" required placeholder="123 Airport Road">
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>City <span class="required">*</span></label>
                                    <input type="text" class="form-control" required placeholder="Los Angeles">
                                </div>
                                
                                <div class="form-group">
                                    <label>State/Province <span class="required">*</span></label>
                                    <select class="form-control" required>
                                        <option value="">Select State</option>
                                        <option value="CA">California</option>
                                        <option value="TX">Texas</option>
                                        <option value="FL">Florida</option>
                                        <!-- Add more states -->
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>ZIP/Postal Code <span class="required">*</span></label>
                                    <input type="text" class="form-control" required placeholder="90001">
                                </div>
                                
                                <div class="form-group">
                                    <label>Country <span class="required">*</span></label>
                                    <select class="form-control" required>
                                        <option value="US">United States</option>
                                        <option value="CA">Canada</option>
                                        <option value="MX">Mexico</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <div class="form-section">
                            <h2 class="section-title">
                                <i class="fas fa-credit-card"></i> Payment Method
                            </h2>
                            
                            <div class="payment-methods">
                                <div class="payment-method selected" onclick="selectPayment('card')">
                                    <i class="fas fa-credit-card"></i>
                                    <h4>Credit Card</h4>
                                </div>
                                <div class="payment-method" onclick="selectPayment('wire')">
                                    <i class="fas fa-university"></i>
                                    <h4>Wire Transfer</h4>
                                </div>
                                <div class="payment-method" onclick="selectPayment('financing')">
                                    <i class="fas fa-hand-holding-usd"></i>
                                    <h4>Financing</h4>
                                </div>
                                <div class="payment-method" onclick="selectPayment('crypto')">
                                    <i class="fab fa-bitcoin"></i>
                                    <h4>Cryptocurrency</h4>
                                </div>
                            </div>
                            
                            <div id="card-payment">
                                <div class="card-input-group">
                                    <i class="fas fa-credit-card card-icon"></i>
                                    <input type="text" class="form-control" placeholder="Card Number" maxlength="19">
                                </div>
                                
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Expiry Date</label>
                                        <input type="text" class="form-control" placeholder="MM/YY" maxlength="5">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>CVV</label>
                                        <input type="text" class="form-control" placeholder="123" maxlength="4">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="security-badge">
                                <i class="fas fa-shield-alt"></i>
                                <p>Your payment information is encrypted and secure. We use industry-standard SSL encryption.</p>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Order Summary -->
                <div class="order-summary">
                    <div class="summary-card">
                        <h2 class="summary-header">Order Summary</h2>
                        
                        <div class="order-items">
                            <div class="order-item">
                                <div class="order-item-image">
                                    <img src="/assets/images/helicopters/r44-raven-1.jpg" alt="Robinson R44">
                                </div>
                                <div class="order-item-details">
                                    <h4>Robinson R44 Raven II</h4>
                                    <p>2023 Model • Qty: 1</p>
                                    <p class="order-item-price">$505,000</p>
                                </div>
                            </div>
                            
                            <div class="order-item">
                                <div class="order-item-image">
                                    <img src="/assets/images/helicopters/bell-407gxi-1.jpg" alt="Bell 407GXi">
                                </div>
                                <div class="order-item-details">
                                    <h4>Bell 407GXi</h4>
                                    <p>2022 Model • Qty: 1</p>
                                    <p class="order-item-price">$2,900,000</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>$3,405,000</span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Documentation Fee</span>
                            <span>$2,500</span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Inspection Service</span>
                            <span>$5,000</span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Estimated Tax</span>
                            <span>$340,500</span>
                        </div>
                        
                        <div class="summary-row total">
                            <span>Total</span>
                            <span class="amount">$3,753,000</span>
                        </div>
                        
                        <div class="terms-checkbox">
                            <input type="checkbox" id="terms" required>
                            <label for="terms">
                                I agree to the <a href="/terms">Terms of Service</a> and 
                                <a href="/privacy">Privacy Policy</a>. I understand that this is a binding 
                                purchase agreement for the aircraft listed above.
                            </label>
                        </div>
                        
                        <button type="submit" form="checkoutForm" class="btn btn-primary btn-large place-order-btn">
                            <i class="fas fa-lock"></i> Place Order - $3,753,000
                        </button>
                        
                        <div class="security-badge" style="margin-top: 20px;">
                            <i class="fas fa-lock"></i>
                            <p>Secure 256-bit SSL Encryption</p>
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
                        <li><a href="/helicopters">Browse Helicopters</a></li>
                        <li><a href="/category/personal">Personal Aircraft</a></li>
                        <li><a href="/category/business">Business Solutions</a></li>
                        <li><a href="/category/emergency">Emergency Services</a></li>
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
                <p>&copy; 2025 Helicopter Marketplace. All rights reserved. | <a href="/privacy">Privacy Policy</a> | <a href="/terms">Terms of Service</a></p>
            </div>
        </div>
    </footer>

    <script>
        // Payment method selection
        function selectPayment(method) {
            // Remove selected from all
            document.querySelectorAll('.payment-method').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Add selected to clicked
            event.target.closest('.payment-method').classList.add('selected');
            
            // Show/hide payment forms based on selection
            if (method === 'card') {
                document.getElementById('card-payment').style.display = 'block';
            } else {
                document.getElementById('card-payment').style.display = 'none';
                // In real app, would show other payment forms
            }
        }

        // Form validation
        document.getElementById('checkoutForm').addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Check if terms are accepted
            if (!document.getElementById('terms').checked) {
                alert('Please accept the terms and conditions to proceed.');
                return;
            }
            
            // In real app, would process payment
            alert('Order placed successfully! Redirecting to confirmation...');
            window.location.href = '/order-confirmation';
        });

        // Card number formatting
        document.querySelector('input[placeholder="Card Number"]').addEventListener('input', (e) => {
            let value = e.target.value.replace(/\s/g, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });

        // Expiry date formatting
        document.querySelector('input[placeholder="MM/YY"]').addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.slice(0, 2) + '/' + value.slice(2, 4);
            }
            e.target.value = value;
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
    </script>
</body>
</html>