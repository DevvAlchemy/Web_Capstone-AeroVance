<?php
/**
 * Business Helicopters Category Page
 * Educational content + E-commerce focused on business/commercial helicopters
 * Location: Toronto, Ontario, Canada
 */

// Include configuration and database
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Helicopter.php';

try {
    $database = new Database();
    $db = $database->connect();
    $helicopter = new Helicopter($db);
    
    // Get business helicopters with pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 12;
    $offset = ($page - 1) * $limit;
    
    // Filter for business category
    $filters = ['category' => 'business'];
    
    // Add any additional filters from URL
    if (!empty($_GET['manufacturer'])) $filters['manufacturer'] = $_GET['manufacturer'];
    if (!empty($_GET['min_price'])) $filters['min_price'] = $_GET['min_price'];
    if (!empty($_GET['max_price'])) $filters['max_price'] = $_GET['max_price'];
    if (!empty($_GET['search'])) $filters['search'] = $_GET['search'];
    
    $businessHelicopters = $helicopter->getAllHelicopters($limit, $offset, $filters);
    $totalBusinessCount = $helicopter->getTotalCount($filters);
    $totalPages = ceil($totalBusinessCount / $limit);
    $manufacturers = $helicopter->getManufacturers();
    
} catch (Exception $e) {
    error_log('Business helicopters page error: ' . $e->getMessage());
    $businessHelicopters = [];
    $totalBusinessCount = 0;
    $totalPages = 1;
    $manufacturers = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Helicopters - Corporate Aviation Solutions | Toronto, Ontario</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Premium business helicopters for corporate transport, VIP services, and commercial operations in Toronto. Elevate your business with professional aviation solutions.">
    <meta name="keywords" content="business helicopters, corporate aviation, VIP transport, commercial helicopters, Toronto helicopter charter">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="/helicopter-marketplace/assets/css/app.css">
    
    <style>
        /* Business Aviation Specific Styles */
        .business-hero {
            background: linear-gradient(135deg, 
                rgba(26, 26, 26, 0.9), 
                rgba(255, 107, 53, 0.85)), 
                url('/assets/images/business-skyline.jpg') center/cover;
            padding: 120px 0 80px;
            text-align: center;
            color: white;
            position: relative;
        }

        .business-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 70% 30%, rgba(255, 140, 66, 0.2), transparent 50%);
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            display: inline-block;
            background: linear-gradient(135deg, #FF6B35, #FF8C42);
            padding: 10px 25px;
            border-radius: 25px;
            margin-bottom: 25px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        /* Value Proposition Section */
        .value-props {
            padding: 80px 0;
            background: #1a1a1a;
        }

        .props-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .prop-card {
            background: linear-gradient(135deg, #333333, #2a2a2a);
            border-radius: 15px;
            padding: 35px;
            border: 1px solid rgba(255, 107, 53, 0.2);
            text-align: center;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .prop-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #FF6B35, #FF8C42);
        }

        .prop-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 25px 50px rgba(255, 107, 53, 0.25);
            border-color: #FF6B35;
        }

        .prop-icon {
            font-size: 3.5rem;
            color: #FF6B35;
            margin-bottom: 25px;
        }

        .prop-title {
            color: #ffffff;
            font-size: 1.5rem;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .prop-description {
            color: #cccccc;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .prop-features {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .feature-tag {
            background: rgba(255, 107, 53, 0.1);
            color: #FF6B35;
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        /* ROI Calculator Section */
        .roi-calculator {
            padding: 80px 0;
            background: #2a2a2a;
        }

        .calculator-container {
            max-width: 800px;
            margin: 0 auto;
            background: #333333;
            border-radius: 20px;
            padding: 40px;
            border: 1px solid rgba(255, 107, 53, 0.3);
        }

        .calculator-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .calc-input-group {
            display: flex;
            flex-direction: column;
        }

        .calc-input-group label {
            color: #FF6B35;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .calc-input-group input {
            padding: 12px 15px;
            border: 1px solid #555555;
            border-radius: 8px;
            background: #2a2a2a;
            color: #ffffff;
            transition: all 0.3s ease;
        }

        .calc-input-group input:focus {
            outline: none;
            border-color: #FF6B35;
            box-shadow: 0 0 10px rgba(255, 107, 53, 0.3);
        }

        .roi-results {
            background: rgba(255, 107, 53, 0.1);
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            margin-top: 20px;
        }

        .roi-value {
            font-size: 2.5rem;
            color: #FF6B35;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .roi-description {
            color: #cccccc;
            font-size: 0.9rem;
        }

        /* Mission Types Section */
        .mission-types {
            padding: 80px 0;
            background: #1a1a1a;
        }

        .missions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .mission-card {
            position: relative;
            height: 400px;
            border-radius: 15px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.4s ease;
        }

        .mission-card:hover {
            transform: scale(1.03);
        }

        .mission-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            transition: transform 0.5s ease;
        }

        .mission-card:hover .mission-background {
            transform: scale(1.1);
        }

        .mission-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, 
                rgba(26, 26, 26, 0.8), 
                rgba(255, 107, 53, 0.7));
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 30px;
        }

        .mission-icon {
            font-size: 3rem;
            color: #FF6B35;
            margin-bottom: 20px;
        }

        .mission-title {
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: white;
            font-weight: 600;
        }

        .mission-description {
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .mission-features {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .mission-features span {
            background: rgba(255, 255, 255, 0.2);
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            backdrop-filter: blur(10px);
        }

        /* Fleet Management Section */
        .fleet-management {
            padding: 80px 0;
            background: #2a2a2a;
        }

        .fleet-services {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 50px;
        }

        .service-item {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            padding: 25px;
            background: rgba(255, 107, 53, 0.05);
            border-radius: 12px;
            border: 1px solid rgba(255, 107, 53, 0.1);
            transition: all 0.3s ease;
        }

        .service-item:hover {
            background: rgba(255, 107, 53, 0.1);
            border-color: rgba(255, 107, 53, 0.3);
            transform: translateY(-3px);
        }

        .service-icon {
            font-size: 2.2rem;
            color: #FF6B35;
            min-width: 45px;
        }

        .service-content h4 {
            color: #ffffff;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .service-content p {
            color: #cccccc;
            margin: 0;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* Aircraft Showcase */
        .aircraft-showcase {
            padding: 80px 0;
            background: #1a1a1a;
        }

        .showcase-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 35px;
            margin-top: 50px;
        }

        .showcase-card {
            background: #333333;
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid rgba(255, 107, 53, 0.2);
            transition: all 0.4s ease;
            position: relative;
        }

        .showcase-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            border-color: #FF6B35;
        }

        .showcase-image {
            height: 280px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .showcase-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: linear-gradient(135deg, #FF6B35, #FF8C42);
            color: white;
            padding: 6px 15px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .showcase-info {
            padding: 30px;
        }

        .showcase-specs {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 20px 0;
            padding: 20px;
            background: rgba(255, 107, 53, 0.1);
            border-radius: 10px;
        }

        .showcase-spec {
            text-align: center;
        }

        .spec-value {
            font-weight: bold;
            color: #FF6B35;
            display: block;
            font-size: 1.2rem;
        }

        .spec-label {
            font-size: 0.8rem;
            color: #aaaaaa;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .showcase-price {
            font-size: 1.6rem;
            color: #FF6B35;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }

        /* CTA Section */
        .business-cta {
            padding: 80px 0;
            background: linear-gradient(135deg, #FF6B35, #FF8C42);
            color: white;
            text-align: center;
        }

        .cta-content {
            max-width: 900px;
            margin: 0 auto;
        }

        .cta-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            display: block;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .business-hero h1 {
                font-size: 2.5rem;
            }

            .calculator-grid {
                grid-template-columns: 1fr;
            }

            .roi-value {
                font-size: 2rem;
            }

            .missions-grid,
            .showcase-grid {
                grid-template-columns: 1fr;
            }

            .cta-stats {
                grid-template-columns: 1fr 1fr;
            }

            .stat-number {
                font-size: 2.5rem;
            }
        }
    </style>
</head>

<body>
    <!-- Include Header -->
    <?php include '../includes/header.php'; ?>

    <!-- Business Aviation Hero Section -->
    <section class="business-hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-badge">
                    <i class="fas fa-building"></i> Corporate Aviation
                </div>
                <h1><i class="fas fa-helicopter"></i> Elevate Your Business</h1>
                <p style="font-size: 1.3rem; max-width: 800px; margin: 0 auto 30px;">
                    Transform your corporate operations with premium business helicopters. From executive transport 
                    to commercial operations, discover aircraft solutions that enhance productivity, save time, 
                    and project professional excellence across the Greater Toronto Area.
                </p>
                <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                    <a href="#aircraft-solutions" class="btn btn-outline btn-large" style="border-color: white; color: white;">
                        <i class="fas fa-search"></i> Explore Solutions
                    </a>
                    <a href="#roi-calculator" class="btn btn-primary btn-large" style="background: white; color: #FF6B35;">
                        <i class="fas fa-calculator"></i> Calculate ROI
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Value Propositions Section -->
    <section class="value-props">
        <div class="container">
            <div class="section-title">
                <h2><i class="fas fa-chart-line"></i> Business Advantages</h2>
                <p>Why leading companies choose helicopter solutions for their operations</p>
            </div>

            <div class="props-grid">
                <!-- Time Efficiency -->
                <div class="prop-card" data-aos="fade-up">
                    <div class="prop-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="prop-title">Time Multiplication</h3>
                    <p class="prop-description">
                        Transform travel time into productive time. Reach multiple locations in a single day, 
                        avoid traffic congestion, and maximize executive productivity with door-to-door transport.
                    </p>
                    <div class="prop-features">
                        <span class="feature-tag">5x Faster Travel</span>
                        <span class="feature-tag">Multi-Stop Capability</span>
                        <span class="feature-tag">No Airport Delays</span>
                    </div>
                </div>

                <!-- Business Image -->
                <div class="prop-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="prop-icon">
                        <i class="fas fa-crown"></i>
                    </div>
                    <h3 class="prop-title">Executive Presence</h3>
                    <p class="prop-description">
                        Arrive with distinction and make a lasting impression. Helicopter transport demonstrates 
                        success, innovation, and commitment to excellence that resonates with clients and partners.
                    </p>
                    <div class="prop-features">
                        <span class="feature-tag">VIP Experience</span>
                        <span class="feature-tag">Professional Image</span>
                        <span class="feature-tag">Memorable Arrivals</span>
                    </div>
                </div>

                <!-- Operational Flexibility -->
                <div class="prop-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="prop-icon">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <h3 class="prop-title">Unlimited Access</h3>
                    <p class="prop-description">
                        Reach locations impossible for conventional transport. Access remote sites, private facilities, 
                        and urban helipads. Your schedule, your destinations, your timeline.
                    </p>
                    <div class="prop-features">
                        <span class="feature-tag">Remote Access</span>
                        <span class="feature-tag">Flexible Scheduling</span>
                        <span class="feature-tag">Weather Adaptability</span>
                    </div>
                </div>

                <!-- Cost Effectiveness -->
                <div class="prop-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="prop-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h3 class="prop-title">Strategic Investment</h3>
                    <p class="prop-description">
                        When you factor in executive time value, increased productivity, and business opportunities 
                        captured, helicopter transport often provides superior ROI compared to conventional methods.
                    </p>
                    <div class="prop-features">
                        <span class="feature-tag">Time = Money</span>
                        <span class="feature-tag">Productivity Gains</span>
                        <span class="feature-tag">Opportunity Capture</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ROI Calculator Section -->
    <section class="roi-calculator" id="roi-calculator">
        <div class="container">
            <div class="section-title">
                <h2><i class="fas fa-calculator"></i> Return on Investment Calculator</h2>
                <p>Discover the financial impact of helicopter transport on your business operations</p>
            </div>

            <div class="calculator-container">
                <form id="roiCalculator">
                    <div class="calculator-grid">
                        <div class="calc-input-group">
                            <label for="executiveRate">Executive Hourly Rate ($)</label>
                            <input type="number" id="executiveRate" placeholder="250" value="250">
                        </div>
                        <div class="calc-input-group">
                            <label for="travelHours">Monthly Travel Hours</label>
                            <input type="number" id="travelHours" placeholder="20" value="20">
                        </div>
                        <div class="calc-input-group">
                            <label for="timeSavings">Time Savings per Trip (%)</label>
                            <input type="number" id="timeSavings" placeholder="60" value="60">
                        </div>
                        <div class="calc-input-group">
                            <label for="monthlyFlights">Flights per Month</label>
                            <input type="number" id="monthlyFlights" placeholder="8" value="8">
                        </div>
                    </div>
                    
                    <div class="roi-results">
                        <div class="roi-value" id="roiValue">$0</div>
                        <div class="roi-description" id="roiDescription">
                            Enter your values above to calculate potential monthly time savings value
                        </div>
                    </div>
                    
                    <div style="text-align: center; margin-top: 25px;">
                        <button type="button" onclick="calculateROI()" class="btn btn-primary btn-large">
                            <i class="fas fa-chart-bar"></i> Calculate Savings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Mission Types Section -->
    <section class="mission-types">
        <div class="container">
            <div class="section-title">
                <h2><i class="fas fa-briefcase"></i> Business Applications</h2>
                <p>Versatile helicopter solutions for diverse corporate needs</p>
            </div>

            <div class="missions-grid">
                <!-- Executive Transport -->
                <div class="mission-card">
                    <div class="mission-background" style="background-image: url('/assets/images/executive-transport.jpg');"></div>
                    <div class="mission-overlay">
                        <div class="mission-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h3 class="mission-title">Executive Transport</h3>
                        <p class="mission-description">
                            VIP transport for executives, board members, and key personnel. 
                            Luxury interiors, professional pilots, and seamless logistics.
                        </p>
                        <div class="mission-features">
                            <span>VIP Interiors</span>
                            <span>Professional Service</span>
                            <span>Privacy & Security</span>
                            <span>Flexible Scheduling</span>
                        </div>
                    </div>
                </div>

                <!-- Corporate Shuttle -->
                <div class="mission-card">
                    <div class="mission-background" style="background-image: url('/assets/images/corporate-shuttle.jpg');"></div>
                    <div class="mission-overlay">
                        <div class="mission-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="mission-title">Corporate Shuttle</h3>
                        <p class="mission-description">
                            Regular transport between facilities, airports, and business locations. 
                            Scheduled services for staff and client transport.
                        </p>
                        <div class="mission-features">
                            <span>Scheduled Service</span>
                            <span>Multi-Passenger</span>
                            <span>Cost Efficient</span>
                            <span>Regular Routes</span>
                        </div>
                    </div>
                </div>

                <!-- Site Inspection -->
                <div class="mission-card">
                    <div class="mission-background" style="background-image: url('/assets/images/site-inspection.jpg');"></div>
                    <div class="mission-overlay">
                        <div class="mission-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3 class="mission-title">Site Inspection</h3>
                        <p class="mission-description">
                            Aerial surveys, construction monitoring, and remote site access. 
                            Perfect for real estate, construction, and infrastructure projects.
                        </p>
                        <div class="mission-features">
                            <span>Aerial Surveys</span>
                            <span>Remote Access</span>
                            <span>Quick Deployment</span>
                            <span>Multi-Site Tours</span>
                        </div>
                    </div>
                </div>

                <!-- Charter Services -->
                <div class="mission-card">
                    <div class="mission-background" style="background-image: url('/assets/images/charter-service.jpg');"></div>
                    <div class="mission-overlay">
                        <div class="mission-icon">
                            <i class="fas fa-plane-departure"></i>
                        </div>
                        <h3 class="mission-title">Charter Services</h3>
                        <p class="mission-description">
                            On-demand helicopter services for special events, client entertainment, 
                            and unique business requirements.
                        </p>
                        <div class="mission-features">
                            <span>On-Demand</span>
                            <span>Event Transport</span>
                            <span>Client Services</span>
                            <span>Custom Solutions</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Fleet Management Section -->
    <section class="fleet-management">
        <div class="container">
            <div class="section-title">
                <h2><i class="fas fa-cogs"></i> Comprehensive Fleet Solutions</h2>
                <p>Complete support services for business helicopter operations</p>
            </div>

            <div class="fleet-services">
                <div class="service-item">
                    <div class="service-icon">
                        <i class="fas fa-wrench"></i>
                    </div>
                    <div class="service-content">
                        <h4>Maintenance Management</h4>
                        <p>Certified technicians, scheduled maintenance, and 24/7 support. Keep your fleet operational with our comprehensive maintenance programs.</p>
                    </div>
                </div>

                <div class="service-item">
                    <div class="service-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="service-content">
                        <h4>Pilot Training</h4>
                        <p>Professional pilot training and certification programs. Type ratings, recurrent training, and safety management systems.</p>
                    </div>
                </div>

                <div class="service-item">
                    <div class="service-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="service-content">
                        <h4>Insurance & Risk Management</h4>
                        <p>Comprehensive insurance solutions tailored to business aviation. Risk assessment, policy management, and claims support.</p>
                    </div>
                </div>

                <div class="service-item">
                    <div class="service-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="service-content">
                        <h4>Flight Operations</h4>
                        <p>Professional flight planning, scheduling, and dispatch services. Weather monitoring, route optimization, and passenger coordination.</p>
                    </div>
                </div>

                <div class="service-item">
                    <div class="service-icon">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <div class="service-content">
                        <h4>Hangar Services</h4>
                        <p>Secure hangar facilities across Toronto. Climate-controlled storage, ground handling, and concierge services for your aircraft.</p>
                    </div>
                </div>

                <div class="service-item">
                    <div class="service-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div class="service-content">
                        <h4>24/7 Support</h4>
                        <p>Round-the-clock operational support. Emergency response, technical assistance, and customer service whenever you need it.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Aircraft Showcase -->
    <section class="aircraft-showcase" id="aircraft-solutions">
        <div class="container">
            <div class="section-title">
                <h2><i class="fas fa-star"></i> Premium Business Aircraft</h2>
                <p>Discover our flagship helicopters designed for corporate excellence</p>
            </div>

            <div class="showcase-grid">
                <!-- Bell 407GXi -->
                <div class="showcase-card">
                    <div class="showcase-image" style="background-image: url('/assets/images/helicopters/bell-407gxi-business.jpg');">
                        <div class="showcase-badge">Executive Choice</div>
                    </div>
                    <div class="showcase-info">
                        <h3>Bell 407GXi</h3>
                        <p>The ultimate executive transport helicopter. Advanced avionics, luxurious cabin, and exceptional performance for discerning corporate clients.</p>
                        <div class="showcase-specs">
                            <div class="showcase-spec">
                                <span class="spec-value">7</span>
                                <span class="spec-label">Passengers</span>
                            </div>
                            <div class="showcase-spec">
                                <span class="spec-value">140</span>
                                <span class="spec-label">Max Speed</span>
                            </div>
                            <div class="showcase-spec">
                                <span class="spec-value">374</span>
                                <span class="spec-label">Range (mi)</span>
                            </div>
                        </div>
                        <div class="showcase-price">Starting at $2,900,000</div>
                        <a href="/helicopter/2" class="btn btn-primary btn-full">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                    </div>
                </div>

                <!-- Airbus H130 -->
                <div class="showcase-card">
                    <div class="showcase-image" style="background-image: url('/assets/images/helicopters/airbus-h130.jpg');">
                        <div class="showcase-badge">VIP Transport</div>
                    </div>
                    <div class="showcase-info">
                        <h3>Airbus H130</h3>
                        <p>Quiet, spacious, and sophisticated. Perfect for VIP transport with panoramic windows and whisper-quiet operation.</p>
                        <div class="showcase-specs">
                            <div class="showcase-spec">
                                <span class="spec-value">8</span>
                                <span class="spec-label">Passengers</span>
                            </div>
                            <div class="showcase-spec">
                                <span class="spec-value">140</span>
                                <span class="spec-label">Max Speed</span>
                            </div>
                            <div class="showcase-spec">
                                <span class="spec-value">395</span>
                                <span class="spec-label">Range (mi)</span>
                            </div>
                        </div>
                        <div class="showcase-price">Starting at $3,200,000</div>
                        <a href="/helicopter/4" class="btn btn-primary btn-full">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                    </div>
                </div>

                <!-- Leonardo AW109 -->
                <div class="showcase-card">
                    <div class="showcase-image" style="background-image: url('/assets/images/helicopters/leonardo-aw109.jpg');">
                        <div class="showcase-badge">Twin Engine</div>
                    </div>
                    <div class="showcase-info">
                        <h3>Leonardo AW109 Grand New</h3>
                        <p>Twin-engine safety with single-pilot capability. Exceptional performance and Italian craftsmanship for executive operations.</p>
                        <div class="showcase-specs">
                            <div class="showcase-spec">
                                <span class="spec-value">7</span>
                                <span class="spec-label">Passengers</span>
                            </div>
                            <div class="showcase-spec">
                                <span class="spec-value">177</span>
                                <span class="spec-label">Max Speed</span>
                            </div>
                            <div class="showcase-spec">
                                <span class="spec-value">640</span>
                                <span class="spec-label">Range (mi)</span>
                            </div>
                        </div>
                        <div class="showcase-price">Starting at $4,500,000</div>
                        <a href="/helicopter/5" class="btn btn-primary btn-full">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Full Aircraft Inventory -->
    <section style="padding: 80px 0; background: #2a2a2a;">
        <div class="container">
            <div class="section-title">
                <h2><i class="fas fa-search"></i> Complete Business Fleet</h2>
                <p>Browse our full inventory of business and commercial helicopters</p>
            </div>

            <!-- Filters -->
            <div class="filter-section" style="background: #333333; padding: 30px 0; border-radius: 15px; margin-bottom: 40px;">
                <form method="GET" action="/category/business">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                        <div style="display: flex; flex-direction: column;">
                            <label style="color: #FF6B35; margin-bottom: 8px; font-weight: 500;">Manufacturer</label>
                            <select name="manufacturer" style="padding: 10px 12px; border: 1px solid #555555; border-radius: 6px; background: #2a2a2a; color: #ffffff;">
                                <option value="">All Manufacturers</option>
                                <?php foreach ($manufacturers as $mfr): ?>
                                    <option value="<?= htmlspecialchars($mfr) ?>" 
                                            <?= (isset($_GET['manufacturer']) && $_GET['manufacturer'] === $mfr) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($mfr) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div style="display: flex; flex-direction: column;">
                            <label style="color: #FF6B35; margin-bottom: 8px;">Price Range</label>
                            <div style="display: grid; grid-template-columns: 1fr auto 1fr; gap: 10px; align-items: end;">
                                <input type="number" name="min_price" placeholder="Min $" 
                                       value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>"
                                       style="padding: 10px 12px; border: 1px solid #555555; border-radius: 6px; background: #2a2a2a; color: #ffffff;">
                                <span style="color: #cccccc;">to</span>
                                <input type="number" name="max_price" placeholder="Max $" 
                                       value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>"
                                       style="padding: 10px 12px; border: 1px solid #555555; border-radius: 6px; background: #2a2a2a; color: #ffffff;">
                            </div>
                        </div>

                        <div style="display: flex; flex-direction: column;">
                            <label style="color: #FF6B35; margin-bottom: 8px;">Search</label>
                            <input type="text" name="search" placeholder="Search aircraft..." 
                                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                                   style="padding: 10px 12px; border: 1px solid #555555; border-radius: 6px; background: #2a2a2a; color: #ffffff;">
                        </div>

                        <div style="align-self: end;">
                            <button type="submit" class="btn btn-primary btn-full">
                                <i class="fas fa-search"></i> Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Results Header -->
            <div class="results-header">
                <div class="results-info">
                    Showing <strong><?= count($businessHelicopters) ?></strong> of <strong><?= $totalBusinessCount ?></strong> business helicopters
                </div>
                <div class="view-options">
                    <button class="view-btn active" onclick="toggleView('grid')" id="grid-btn">
                        <i class="fas fa-th-large"></i>
                    </button>
                    <button class="view-btn" onclick="toggleView('list')" id="list-btn">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>

            <!-- Aircraft Grid -->
            <?php if (!empty($businessHelicopters)): ?>
                <div class="helicopters-grid" id="aircraft-grid">
                    <?php foreach ($businessHelicopters as $heli): ?>
                        <div class="helicopter-card">
                            <div class="helicopter-image" style="background-image: url('<?= !empty($heli['images']) ? json_decode($heli['images'])[0] : '/assets/images/helicopter-placeholder.jpg' ?>');">
                                <div class="price-tag"><?= formatPrice($heli['price']) ?></div>
                                <div class="category-badge">Business</div>
                                <?php if (isLoggedIn()): ?>
                                    <button class="wishlist-btn" onclick="addToWishlist(<?= $heli['id'] ?>)">
                                        <i class="far fa-heart"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                            
                            <div class="helicopter-info">
                                <h3><?= htmlspecialchars($heli['name']) ?></h3>
                                <div class="manufacturer-model">
                                    <?= htmlspecialchars($heli['manufacturer']) ?> • 
                                    <?= htmlspecialchars($heli['model']) ?> • 
                                    <?= $heli['year'] ?>
                                </div>
                                
                                <p class="helicopter-description">
                                    <?= htmlspecialchars(substr($heli['description'], 0, 120)) ?>...
                                </p>
                                
                                <div class="helicopter-specs">
                                    <div class="spec">
                                        <span class="spec-value"><?= $heli['max_speed'] ?? 'N/A' ?></span>
                                        <span class="spec-label">Max Speed</span>
                                    </div>
                                    <div class="spec">
                                        <span class="spec-value"><?= $heli['range'] ?? 'N/A' ?></span>
                                        <span class="spec-label">Range</span>
                                    </div>
                                    <div class="spec">
                                        <span class="spec-value"><?= $heli['passenger_capacity'] ?? 'N/A' ?></span>
                                        <span class="spec-label">Seats</span>
                                    </div>
                                </div>
                                
                                <div class="helicopter-actions">
                                    <a href="/helicopter/<?= $heli['id'] ?>" class="btn btn-primary btn-full">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination" style="display: flex; justify-content: center; align-items: center; gap: 10px; margin-top: 40px;">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?><?= http_build_query($_GET) ? '&' . http_build_query(array_diff_key($_GET, ['page' => ''])) : '' ?>" 
                               style="padding: 10px 15px; border: 1px solid #555555; border-radius: 5px; color: #cccccc; text-decoration: none;">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <?php if ($i == $page): ?>
                                <span style="padding: 10px 15px; background: #FF6B35; color: white; border-radius: 5px;"><?= $i ?></span>
                            <?php else: ?>
                                <a href="?page=<?= $i ?><?= http_build_query($_GET) ? '&' . http_build_query(array_diff_key($_GET, ['page' => ''])) : '' ?>"
                                   style="padding: 10px 15px; border: 1px solid #555555; border-radius: 5px; color: #cccccc; text-decoration: none;"><?= $i ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?= $page + 1 ?><?= http_build_query($_GET) ? '&' . http_build_query(array_diff_key($_GET, ['page' => ''])) : '' ?>"
                               style="padding: 10px 15px; border: 1px solid #555555; border-radius: 5px; color: #cccccc; text-decoration: none;">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <!-- No Results -->
                <div style="text-align: center; padding: 60px 20px;">
                    <i class="fas fa-search" style="font-size: 4rem; color: #FF6B35; margin-bottom: 20px;"></i>
                    <h3 style="color: #ffffff; margin-bottom: 15px;">No Business Helicopters Found</h3>
                    <p style="color: #cccccc; margin-bottom: 20px;">Try adjusting your search criteria or contact our sales team for custom solutions.</p>
                    <a href="/category/business" class="btn btn-primary">Clear Filters</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Business CTA Section -->
    <section class="business-cta">
        <div class="container">
            <div class="cta-content">
                <h2 style="font-size: 2.5rem; margin-bottom: 20px;">Ready to Transform Your Business?</h2>
                <p style="font-size: 1.2rem; margin-bottom: 40px;">
                    Join leading Toronto companies who have revolutionized their operations with helicopter solutions. 
                    Our aviation specialists are ready to design a custom solution for your business needs.
                </p>
                
                <div class="cta-stats">
                    <div class="stat-item">
                        <span class="stat-number">500+</span>
                        <span class="stat-label">Corporate Clients</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">15+</span>
                        <span class="stat-label">Years Experience</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">24/7</span>
                        <span class="stat-label">Support Available</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">99.8%</span>
                        <span class="stat-label">Reliability Rate</span>
                    </div>
                </div>
                
                <div style="margin-top: 40px;">
                    <a href="/contact?type=business" class="btn btn-outline btn-large" 
                       style="border-color: white; color: white; margin-right: 15px;">
                        <i class="fas fa-phone"></i> Schedule Consultation
                    </a>
                    <a href="/financing" class="btn btn-primary btn-large" 
                       style="background: white; color: #FF6B35;">
                        <i class="fas fa-calculator"></i> Explore Financing
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Include Footer -->
    <?php include '../includes/footer.php'; ?>

    <script>
        // ROI Calculator functionality
        function calculateROI() {
            const executiveRate = parseFloat(document.getElementById('executiveRate').value) || 250;
            const travelHours = parseFloat(document.getElementById('travelHours').value) || 20;
            const timeSavings = parseFloat(document.getElementById('timeSavings').value) || 60;
            const monthlyFlights = parseFloat(document.getElementById('monthlyFlights').value) || 8;

            // Calculate time saved per month
            const timeSavedPercentage = timeSavings / 100;
            const timeSavedHours = travelHours * timeSavedPercentage;
            
            // Calculate monetary value of time saved
            const monthlySavings = timeSavedHours * executiveRate;
            const annualSavings = monthlySavings * 12;

            // Update display
            document.getElementById('roiValue').textContent = ''
                 + monthlySavings.toLocaleString();
            document.getElementById('roiDescription').innerHTML = `
                Monthly time savings value based on ${timeSavedHours.toFixed(1)} hours saved<br>
                <strong>Annual Value: ${annualSavings.toLocaleString()}</strong>
            `;

            // Show success message
            showNotification('ROI calculated successfully!', 'success');
        }

        // Auto-calculate on input change
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = ['executiveRate', 'travelHours', 'timeSavings', 'monthlyFlights'];
            inputs.forEach(inputId => {
                document.getElementById(inputId).addEventListener('input', calculateROI);
            });
            
            // Initial calculation
            calculateROI();
            
            console.log('✅ Business Helicopters page loaded with <?= count($businessHelicopters) ?> aircraft');
        });

        // View toggle functionality
        function toggleView(view) {
            const gridView = document.getElementById('aircraft-grid');
            const gridBtn = document.getElementById('grid-btn');
            const listBtn = document.getElementById('list-btn');

            if (view === 'grid') {
                gridView.style.display = 'grid';
                gridBtn.classList.add('active');
                listBtn.classList.remove('active');
                localStorage.setItem('businessViewPreference', 'grid');
            } else {
                // List view implementation would go here
                console.log('List view not implemented yet');
            }
        }

        // Wishlist functionality
        function addToWishlist(helicopterId) {
            <?php if (isLoggedIn()): ?>
                fetch('/api/wishlist/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ helicopter_id: helicopterId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Added to wishlist!', 'success');
                        // Update heart icon
                        event.target.classList.remove('far');
                        event.target.classList.add('fas');
                        event.target.style.color = '#FF6B35';
                    } else {
                        showNotification(data.message || 'Already in wishlist', 'info');
                    }
                })
                .catch(error => {
                    showNotification('Error adding to wishlist', 'error');
                });
            <?php else: ?>
                window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname);
            <?php endif; ?>
        }

        // Load saved view preference
        document.addEventListener('DOMContentLoaded', function() {
            const savedView = localStorage.getItem('businessViewPreference') || 'grid';
            toggleView(savedView);
        });

        // Smooth scroll for internal links
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