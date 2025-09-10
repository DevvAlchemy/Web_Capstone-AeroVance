<?php
/**
 * Emergency Services Helicopters Category Page
 * Educational content + E-commerce focused on emergency/public safety helicopters
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
    
    // Get emergency helicopters with pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 12;
    $offset = ($page - 1) * $limit;
    
    // Filter for emergency category
    $filters = ['category' => 'emergency'];
    
    // Add any additional filters from URL
    if (!empty($_GET['manufacturer'])) $filters['manufacturer'] = $_GET['manufacturer'];
    if (!empty($_GET['min_price'])) $filters['min_price'] = $_GET['min_price'];
    if (!empty($_GET['max_price'])) $filters['max_price'] = $_GET['max_price'];
    if (!empty($_GET['search'])) $filters['search'] = $_GET['search'];
    
    $emergencyHelicopters = $helicopter->getAllHelicopters($limit, $offset, $filters);
    $totalEmergencyCount = $helicopter->getTotalCount($filters);
    $totalPages = ceil($totalEmergencyCount / $limit);
    $manufacturers = $helicopter->getManufacturers();
    
} catch (Exception $e) {
    error_log('Emergency helicopters page error: ' . $e->getMessage());
    $emergencyHelicopters = [];
    $totalEmergencyCount = 0;
    $totalPages = 1;
    $manufacturers = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Services Helicopters - Mission Critical Aircraft | Toronto, Ontario</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Specialized emergency services helicopters for EMS, law enforcement, firefighting, and search & rescue operations in Toronto. Mission-critical aviation solutions.">
    <meta name="keywords" content="emergency helicopters, EMS helicopters, police helicopters, firefighting aircraft, search rescue helicopters, Toronto emergency services">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="/helicopter-marketplace/assets/css/app.css">
    
    <style>
        /* Emergency Services Specific Styles */
        .emergency-hero {
            background: linear-gradient(135deg, 
                rgba(220, 53, 69, 0.9), 
                rgba(255, 107, 53, 0.85)), 
                url('/assets/images/emergency-response.jpg') center/cover;
            padding: 120px 0 80px;
            text-align: center;
            color: white;
            position: relative;
        }

        .emergency-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 50% 30%, rgba(220, 53, 69, 0.3), transparent 60%);
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            display: inline-block;
            background: linear-gradient(135deg, #dc3545, #FF6B35);
            padding: 10px 25px;
            border-radius: 25px;
            margin-bottom: 25px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }

        /* Mission Critical Section */
        .mission-critical {
            padding: 80px 0;
            background: #1a1a1a;
        }

        .critical-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .critical-card {
            background: linear-gradient(135deg, #333333, #2a2a2a);
            border-radius: 15px;
            padding: 35px;
            border: 2px solid rgba(220, 53, 69, 0.3);
            text-align: center;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .critical-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #dc3545, #FF6B35);
        }

        .critical-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 25px 50px rgba(220, 53, 69, 0.3);
            border-color: #dc3545;
        }

        .critical-icon {
            font-size: 3.5rem;
            color: #dc3545;
            margin-bottom: 25px;
        }

        .critical-title {
            color: #ffffff;
            font-size: 1.5rem;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .critical-description {
            color: #cccccc;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .critical-features {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .critical-tag {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            padding: 6px 14px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        /* Service Types Section */
        .service-types {
            padding: 80px 0;
            background: #2a2a2a;
        }

        .types-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 35px;
            margin-top: 50px;
        }

        .service-type-card {
            position: relative;
            height: 450px;
            border-radius: 15px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.4s ease;
            border: 2px solid rgba(220, 53, 69, 0.2);
        }

        .service-type-card:hover {
            transform: scale(1.02);
            border-color: #dc3545;
        }

        .service-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            transition: transform 0.5s ease;
        }

        .service-type-card:hover .service-background {
            transform: scale(1.1);
        }

        .service-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, 
                rgba(220, 53, 69, 0.85), 
                rgba(255, 107, 53, 0.7));
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 30px;
        }

        .service-icon {
            font-size: 3.5rem;
            color: white;
            margin-bottom: 20px;
        }

        .service-title {
            font-size: 2rem;
            margin-bottom: 15px;
            color: white;
            font-weight: 700;
        }

        .service-description {
            color: rgba(255, 255, 255, 0.95);
            line-height: 1.6;
            margin-bottom: 25px;
            font-size: 1.1rem;
        }

        .service-capabilities {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
        }

        .service-capabilities span {
            background: rgba(255, 255, 255, 0.25);
            padding: 6px 14px;
            border-radius: 15px;
            font-size: 0.85rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Specifications Requirements */
        .spec-requirements {
            padding: 80px 0;
            background: #1a1a1a;
        }

        .requirements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 50px;
        }

        .requirement-item {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            padding: 25px;
            background: rgba(220, 53, 69, 0.08);
            border-radius: 12px;
            border: 1px solid rgba(220, 53, 69, 0.15);
            transition: all 0.3s ease;
        }

        .requirement-item:hover {
            background: rgba(220, 53, 69, 0.12);
            border-color: rgba(220, 53, 69, 0.3);
            transform: translateY(-3px);
        }

        .requirement-icon {
            font-size: 2.2rem;
            color: #dc3545;
            min-width: 45px;
        }

        .requirement-content h4 {
            color: #ffffff;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .requirement-content p {
            color: #cccccc;
            margin: 0;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* Emergency Aircraft Showcase */
        .emergency-showcase {
            padding: 80px 0;
            background: #2a2a2a;
        }

        .emergency-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 35px;
            margin-top: 50px;
        }

        .emergency-card {
            background: #333333;
            border-radius: 15px;
            overflow: hidden;
            border: 2px solid rgba(220, 53, 69, 0.2);
            transition: all 0.4s ease;
            position: relative;
        }

        .emergency-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(220, 53, 69, 0.2);
            border-color: #dc3545;
        }

        .emergency-image {
            height: 280px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .emergency-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: linear-gradient(135deg, #dc3545, #FF6B35);
            color: white;
            padding: 8px 16px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            box-shadow: 0 3px 10px rgba(220, 53, 69, 0.4);
        }

        .emergency-info {
            padding: 30px;
        }

        .emergency-specs {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 20px 0;
            padding: 20px;
            background: rgba(220, 53, 69, 0.1);
            border-radius: 10px;
            border: 1px solid rgba(220, 53, 69, 0.2);
        }

        .emergency-spec {
            text-align: center;
        }

        .emergency-spec-value {
            font-weight: bold;
            color: #dc3545;
            display: block;
            font-size: 1.2rem;
        }

        .emergency-spec-label {
            font-size: 0.8rem;
            color: #aaaaaa;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .emergency-price {
            font-size: 1.6rem;
            color: #dc3545;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Contact Emergency Section */
        .emergency-contact {
            padding: 80px 0;
            background: linear-gradient(135deg, #dc3545, #FF6B35);
            color: white;
            text-align: center;
        }

        .contact-content {
            max-width: 900px;
            margin: 0 auto;
        }

        .contact-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }

        .contact-stat {
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
            .emergency-hero h1 {
                font-size: 2.5rem;
            }

            .types-grid,
            .emergency-grid {
                grid-template-columns: 1fr;
            }

            .contact-stats {
                grid-template-columns: 1fr 1fr;
            }

            .stat-number {
                font-size: 2.5rem;
            }

            .service-type-card {
                height: 400px;
            }
        }
    </style>
</head>

<body>
    <!-- Include Header -->
    <?php include '../includes/header.php'; ?>

    <!-- Emergency Services Hero Section -->
    <section class="emergency-hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-badge">
                    <i class="fas fa-first-aid"></i> Emergency Services
                </div>
                <h1><i class="fas fa-helicopter"></i> When Every Second Counts</h1>
                <p style="font-size: 1.3rem; max-width: 800px; margin: 0 auto 30px;">
                    Specialized helicopters engineered for life-saving missions. From emergency medical services 
                    to search and rescue operations, our aircraft deliver the performance, reliability, and 
                    advanced capabilities that emergency professionals depend on across Ontario.
                </p>
                <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                    <a href="#mission-aircraft" class="btn btn-outline btn-large" style="border-color: white; color: white;">
                        <i class="fas fa-search"></i> View Aircraft
                    </a>
                    <a href="#contact-specialists" class="btn btn-primary btn-large" style="background: white; color: #dc3545;">
                        <i class="fas fa-phone"></i> Contact Specialists
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Critical Capabilities -->
    <section class="mission-critical">
        <div class="container">
            <div class="section-title">
                <h2><i class="fas fa-shield-alt"></i> Mission-Critical Capabilities</h2>
                <p>Advanced helicopter solutions designed for life-saving operations</p>
            </div>

            <div class="critical-grid">
                <!-- Rapid Response -->
                <div class="critical-card" data-aos="fade-up">
                    <div class="critical-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h3 class="critical-title">Rapid Response</h3>
                    <p class="critical-description">
                        Lightning-fast deployment capabilities with advanced avionics for all-weather operations. 
                        Reach critical scenes faster than any ground-based transport, saving precious minutes in emergencies.
                    </p>
                    <div class="critical-features">
                        <span class="critical-tag">0-60 in 90 Seconds</span>
                        <span class="critical-tag">All-Weather IFR</span>
                        <span class="critical-tag">24/7 Readiness</span>
                    </div>
                </div>

                <!-- Advanced Medical -->
                <div class="critical-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="critical-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <h3 class="critical-title">Advanced Medical Systems</h3>
                    <p class="critical-description">
                        State-of-the-art medical equipment integration including ventilators, cardiac monitors, 
                        and specialized stretcher systems. Mobile ICU capabilities for critical patient transport.
                    </p>
                    <div class="critical-features">
                        <span class="critical-tag">ICU-Level Care</span>
                        <span class="critical-tag">Specialized Equipment</span>
                        <span class="critical-tag">Medical Oxygen</span>
                    </div>
                </div>

                <!-- Search & Rescue -->
                <div class="critical-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="critical-icon">
                        <i class="fas fa-search-location"></i>
                    </div>
                    <h3 class="critical-title">Search & Rescue Ready</h3>
                    <p class="critical-description">
                        Equipped with FLIR thermal imaging, searchlights, rescue hoists, and GPS tracking systems. 
                        Configured for water rescue, mountain rescue, and urban emergency response operations.
                    </p>
                    <div class="critical-features">
                        <span class="critical-tag">FLIR Thermal</span>
                        <span class="critical-tag">Rescue Hoist</span>
                        <span class="critical-tag">Long Range</span>
                    </div>
                </div>

                <!-- Law Enforcement -->
                <div class="critical-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="critical-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="critical-title">Law Enforcement Configuration</h3>
                    <p class="critical-description">
                        Tactical configurations with surveillance equipment, communication systems, and 
                        specialized lighting. Support for police operations, border patrol, and public safety missions.
                    </p>
                    <div class="critical-features">
                        <span class="critical-tag">Surveillance Systems</span>
                        <span class="critical-tag">Tactical Equipment</span>
                        <span class="critical-tag">Secure Comms</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Emergency Service Types -->
    <section class="service-types">
        <div class="container">
            <div class="section-title">
                <h2><i class="fas fa-ambulance"></i> Emergency Service Applications</h2>
                <p>Specialized helicopter solutions for every emergency service need</p>
            </div>

            <div class="types-grid">
                <!-- Emergency Medical Services -->
                <div class="service-type-card">
                    <div class="service-background" style="background-image: url('/assets/images/ems-helicopter.jpg');"></div>
                    <div class="service-overlay">
                        <div class="service-icon">
                            <i class="fas fa-ambulance"></i>
                        </div>
                        <h3 class="service-title">Emergency Medical Services</h3>
                        <p class="service-description">
                            Critical care transport with advanced life support capabilities. 
                            Rapid patient evacuation from accident scenes to trauma centers.
                        </p>
                        <div class="service-capabilities">
                            <span>Advanced Life Support</span>
                            <span>Critical Care Transport</span>
                            <span>Trauma Response</span>
                            <span>Inter-Hospital Transfer</span>
                        </div>
                    </div>
                </div>

                <!-- Search & Rescue -->
                <div class="service-type-card">
                    <div class="service-background" style="background-image: url('/assets/images/search-rescue.jpg');"></div>
                    <div class="service-overlay">
                        <div class="service-icon">
                            <i class="fas fa-life-ring"></i>
                        </div>
                        <h3 class="service-title">Search & Rescue</h3>
                        <p class="service-description">
                            Locate and extract people in distress from remote or dangerous locations. 
                            Water rescue, mountain rescue, and urban search operations.
                        </p>
                        <div class="service-capabilities">
                            <span>Water Rescue</span>
                            <span>Mountain Operations</span>
                            <span>Urban SAR</span>
                            <span>Wilderness Recovery</span>
                        </div>
                    </div>
                </div>

                <!-- Fire Fighting -->
                <div class="service-type-card">
                    <div class="service-background" style="background-image: url('/assets/images/firefighting-helicopter.jpg');"></div>
                    <div class="service-overlay">
                        <div class="service-icon">
                            <i class="fas fa-fire-extinguisher"></i>
                        </div>
                        <h3 class="service-title">Fire Fighting & Suppression</h3>
                        <p class="service-description">
                            Aerial firefighting with water/retardant drops, personnel transport, 
                            and fire reconnaissance in challenging terrain and conditions.
                        </p>
                        <div class="service-capabilities">
                            <span>Water Drops</span>
                            <span>Retardant Application</span>
                            <span>Fire Reconnaissance</span>
                            <span>Crew Transport</span>
                        </div>
                    </div>
                </div>

                <!-- Law Enforcement -->
                <div class="service-type-card">
                    <div class="service-background" style="background-image: url('/assets/images/police-helicopter.jpg');"></div>
                    <div class="service-overlay">
                        <div class="service-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="service-title">Law Enforcement</h3>
                        <p class="service-description">
                            Police operations including surveillance, pursuit support, 
                            border patrol, and tactical team deployment.
                        </p>
                        <div class="service-capabilities">
                            <span>Aerial Surveillance</span>
                            <span>Pursuit Support</span>
                            <span>Tactical Operations</span>
                            <span>Border Patrol</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Emergency Specifications -->
    <section class="spec-requirements">
        <div class="container">
            <div class="section-title">
                <h2><i class="fas fa-cogs"></i> Emergency Service Requirements</h2>
                <p>Critical specifications and capabilities for emergency operations</p>
            </div>

            <div class="requirements-grid">
                <div class="requirement-item">
                    <div class="requirement-icon">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <div class="requirement-content">
                        <h4>Performance Standards</h4>
                        <p>High-speed cruise capabilities, rapid climb rates, and exceptional maneuverability for time-critical missions and confined area operations.</p>
                    </div>
                </div>

                <div class="requirement-item">
                    <div class="requirement-icon">
                        <i class="fas fa-weight-hanging"></i>
                    </div>
                    <div class="requirement-content">
                        <h4>Payload Capacity</h4>
                        <p>Sufficient weight capacity for medical equipment, rescue gear, personnel, and patients while maintaining operational performance limits.</p>
                    </div>
                </div>

                <div class="requirement-item">
                    <div class="requirement-icon">
                        <i class="fas fa-cloud-rain"></i>
                    </div>
                    <div class="requirement-content">
                        <h4>All-Weather Capability</h4>
                        <p>IFR-certified instruments, de-icing systems, and weather radar for operations in challenging meteorological conditions.</p>
                    </div>
                </div>

                <div class="requirement-item">
                    <div class="requirement-icon">
                        <i class="fas fa-satellite-dish"></i>
                    </div>
                    <div class="requirement-content">
                        <h4>Communication Systems</h4>
                        <p>Multi-frequency radios, satellite communication, GPS navigation, and emergency location transmitters for coordination and safety.</p>
                    </div>
                </div>

                <div class="requirement-item">
                    <div class="requirement-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div class="requirement-content">
                        <h4>Mission Equipment</h4>
                        <p>Modular equipment mounting systems, external cargo hooks, winch/hoist capabilities, and specialized mission-specific configurations.</p>
                    </div>
                </div>

                <div class="requirement-item">
                    <div class="requirement-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <div class="requirement-content">
                        <h4>Certification Standards</h4>
                        <p>Transport Canada certification, maintenance programs, and compliance with emergency services operational requirements and safety standards.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Emergency Aircraft Showcase -->
    <section class="emergency-showcase" id="mission-aircraft">
        <div class="container">
            <div class="section-title">
                <h2><i class="fas fa-star"></i> Mission-Ready Aircraft</h2>
                <p>Proven helicopters trusted by emergency services worldwide</p>
            </div>

            <div class="emergency-grid">
                <!-- Airbus H145 -->
                <div class="emergency-card">
                    <div class="emergency-image" style="background-image: url('/assets/images/helicopters/h145-emergency.jpg');">
                        <div class="emergency-badge">EMS Leader</div>
                    </div>
                    <div class="emergency-info">
                        <h3>Airbus H145</h3>
                        <p>The world's most advanced emergency services helicopter. Twin-engine safety, advanced avionics, and unmatched mission flexibility for critical operations.</p>
                        <div class="emergency-specs">
                            <div class="emergency-spec">
                                <span class="emergency-spec-value">9</span>
                                <span class="emergency-spec-label">Passengers</span>
                            </div>
                            <div class="emergency-spec">
                                <span class="emergency-spec-value">150</span>
                                <span class="emergency-spec-label">Max Speed</span>
                            </div>
                            <div class="emergency-spec">
                                <span class="emergency-spec-value">431</span>
                                <span class="emergency-spec-label">Range (mi)</span>
                            </div>
                        </div>
                        <div class="emergency-price">Starting at $9,500,000</div>
                        <a href="/helicopter/3" class="btn btn-primary btn-full">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                    </div>
                </div>

                <!-- Bell 429 -->
                <div class="emergency-card">
                    <div class="emergency-image" style="background-image: url('/assets/images/helicopters/bell-429-ems.jpg');">
                        <div class="emergency-badge">Multi-Mission</div>
                    </div>
                    <div class="emergency-info">
                        <h3>Bell 429 GlobalRanger</h3>
                        <p>Versatile twin-engine helicopter with exceptional hot and high performance. Ideal for EMS, law enforcement, and search & rescue operations.</p>
                        <div class="emergency-specs">
                            <div class="emergency-spec">
                                <span class="emergency-spec-value">8</span>
                                <span class="emergency-spec-label">Passengers</span>
                            </div>
                            <div class="emergency-spec">
                                <span class="emergency-spec-value">165</span>
                                <span class="emergency-spec-label">Max Speed</span>
                            </div>
                            <div class="emergency-spec">
                                <span class="emergency-spec-value">426</span>
                                <span class="emergency-spec-label">Range (mi)</span>
                            </div>
                        </div>
                        <div class="emergency-price">Starting at $7,200,000</div>
                        <a href="/helicopter/6" class="btn btn-primary btn-full">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                    </div>
                </div>

                <!-- Leonardo AW139 -->
                <div class="emergency-card">
                    <div class="emergency-image" style="background-image: url('/assets/images/helicopters/aw139-rescue.jpg');">
                        <div class="emergency-badge">Heavy Lift</div>
                    </div>
                    <div class="emergency-info">
                        <h3>Leonardo AW139</h3>
                        <p>Medium twin-engine helicopter with superior payload capacity and range. Perfect for offshore operations, SAR missions, and VIP transport.</p>
                        <div class="emergency-specs">
                            <div class="emergency-spec">
                                <span class="emergency-spec-value">15</span>
                                <span class="emergency-spec-label">Passengers</span>
                            </div>
                            <div class="emergency-spec">
                                <span class="emergency-spec-value">193</span>
                                <span class="emergency-spec-label">Max Speed</span>
                            </div>
                            <div class="emergency-spec">
                                <span class="emergency-spec-value">573</span>
                                <span class="emergency-spec-label">Range (mi)</span>
                            </div>
                        </div>
                        <div class="emergency-price">Starting at $12,500,000</div>
                        <a href="/helicopter/7" class="btn btn-primary btn-full">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Full Emergency Inventory -->
    <section style="padding: 80px 0; background: #1a1a1a;">
        <div class="container">
            <div class="section-title">
                <h2><i class="fas fa-search"></i> Complete Emergency Fleet</h2>
                <p>Browse our specialized inventory of emergency services helicopters</p>
            </div>

            <!-- Filters -->
            <div style="background: #333333; padding: 30px 0; border-radius: 15px; margin-bottom: 40px;">
                <form method="GET" action="/category/emergency">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                        <div style="display: flex; flex-direction: column;">
                            <label style="color: #dc3545; margin-bottom: 8px; font-weight: 500;">Manufacturer</label>
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
                            <label style="color: #dc3545; margin-bottom: 8px;">Price Range</label>
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
                            <label style="color: #dc3545; margin-bottom: 8px;">Search</label>
                            <input type="text" name="search" placeholder="Search aircraft..." 
                                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                                   style="padding: 10px 12px; border: 1px solid #555555; border-radius: 6px; background: #2a2a2a; color: #ffffff;">
                        </div>

                        <div style="align-self: end;">
                            <button type="submit" class="btn btn-primary btn-full" style="background: linear-gradient(135deg, #dc3545, #FF6B35);">
                                <i class="fas fa-search"></i> Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Results Header -->
            <div class="results-header">
                <div class="results-info">
                    Showing <strong><?= count($emergencyHelicopters) ?></strong> of <strong><?= $totalEmergencyCount ?></strong> emergency helicopters
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
            <?php if (!empty($emergencyHelicopters)): ?>
                <div class="helicopters-grid" id="aircraft-grid">
                    <?php foreach ($emergencyHelicopters as $heli): ?>
                        <div class="helicopter-card">
                            <div class="helicopter-image" style="background-image: url('<?= !empty($heli['images']) ? json_decode($heli['images'])[0] : '/assets/images/helicopter-placeholder.jpg' ?>');">
                                <div class="price-tag" style="background: linear-gradient(135deg, #dc3545, #FF6B35);"><?= formatPrice($heli['price']) ?></div>
                                <div class="category-badge" style="background: rgba(220, 53, 69, 0.9); color: white;">Emergency</div>
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
                                        <span class="spec-label">Capacity</span>
                                    </div>
                                </div>
                                
                                <div class="helicopter-actions">
                                    <a href="/helicopter/<?= $heli['id'] ?>" class="btn btn-primary btn-full" 
                                       style="background: linear-gradient(135deg, #dc3545, #FF6B35);">
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
                                <span style="padding: 10px 15px; background: linear-gradient(135deg, #dc3545, #FF6B35); color: white; border-radius: 5px;"><?= $i ?></span>
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
                    <i class="fas fa-search" style="font-size: 4rem; color: #dc3545; margin-bottom: 20px;"></i>
                    <h3 style="color: #ffffff; margin-bottom: 15px;">No Emergency Helicopters Found</h3>
                    <p style="color: #cccccc; margin-bottom: 20px;">Try adjusting your search criteria or contact our emergency services specialists for custom configurations.</p>
                    <a href="/category/emergency" class="btn btn-primary" style="background: linear-gradient(135deg, #dc3545, #FF6B35);">Clear Filters</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Emergency Contact Section -->
    <section class="emergency-contact" id="contact-specialists">
        <div class="container">
            <div class="contact-content">
                <h2 style="font-size: 2.5rem; margin-bottom: 20px;">Emergency Services Support</h2>
                <p style="font-size: 1.2rem; margin-bottom: 40px;">
                    Our specialized team understands the critical nature of emergency services operations. 
                    We provide comprehensive support from initial consultation through mission deployment 
                    across Ontario and beyond.
                </p>
                
                <div class="contact-stats">
                    <div class="contact-stat">
                        <span class="stat-number">24/7</span>
                        <span class="stat-label">Emergency Support</span>
                    </div>
                    <div class="contact-stat">
                        <span class="stat-number">200+</span>
                        <span class="stat-label">Emergency Services Clients</span>
                    </div>
                    <div class="contact-stat">
                        <span class="stat-number">99.9%</span>
                        <span class="stat-label">Mission Readiness</span>
                    </div>
                    <div class="contact-stat">
                        <span class="stat-number">15</span>
                        <span class="stat-label">Minutes Response Time</span>
                    </div>
                </div>
                
                <div style="margin-top: 40px;">
                    <a href="/contact?type=emergency&location=toronto" class="btn btn-outline btn-large" 
                       style="border-color: white; color: white; margin-right: 15px;">
                        <i class="fas fa-phone-alt"></i> Emergency Hotline
                    </a>
                    <a href="/emergency-services-brochure.pdf" class="btn btn-primary btn-large" 
                       style="background: white; color: #dc3545;" target="_blank">
                        <i class="fas fa-download"></i> Service Brochure
                    </a>
                </div>
                
                <div style="margin-top: 30px; font-size: 1.1rem;">
                    <i class="fas fa-map-marker-alt"></i> Serving Toronto, Ontario & All of Canada<br>
                    <i class="fas fa-clock"></i> 24/7/365 Emergency Response Available
                </div>
            </div>
        </div>
    </section>

    <!-- Include Footer -->
    <?php include '../includes/footer.php'; ?>

    <script>
        // View toggle functionality
        function toggleView(view) {
            const gridView = document.getElementById('aircraft-grid');
            const gridBtn = document.getElementById('grid-btn');
            const listBtn = document.getElementById('list-btn');

            if (view === 'grid') {
                gridView.style.display = 'grid';
                gridBtn.classList.add('active');
                listBtn.classList.remove('active');
                localStorage.setItem('emergencyViewPreference', 'grid');
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
                        showNotification('Added to emergency services wishlist!', 'success');
                        // Update heart icon
                        event.target.classList.remove('far');
                        event.target.classList.add('fas');
                        event.target.style.color = '#dc3545';
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

        // Emergency response simulation (educational feature)
        function simulateEmergencyResponse() {
            const responses = [
                "🚁 Helicopter dispatched to scene in 3 minutes",
                "📡 GPS coordinates locked, ETA 8 minutes", 
                "🏥 Medical team prepped, trauma bay ready",
                "✅ Patient secured, returning to hospital",
                "🏁 Mission complete - another life saved"
            ];
            
            let currentResponse = 0;
            const interval = setInterval(() => {
                showNotification(responses[currentResponse], 'success');
                currentResponse++;
                if (currentResponse >= responses.length) {
                    clearInterval(interval);
                    setTimeout(() => {
                        showNotification('Emergency response simulation complete', 'info');
                    }, 2000);
                }
            }, 2000);
        }

        // Load saved view preference
        document.addEventListener('DOMContentLoaded', function() {
            const savedView = localStorage.getItem('emergencyViewPreference') || 'grid';
            toggleView(savedView);
            
            // Initialize emergency response demo button if exists
            const demoBtn = document.getElementById('emergency-demo');
            if (demoBtn) {
                demoBtn.addEventListener('click', simulateEmergencyResponse);
            }
            
            console.log('✅ Emergency Services page loaded with <?= count($emergencyHelicopters) ?> aircraft');
            console.log('🚨 Emergency systems ready - saving lives is our mission');
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

        // Emergency contact form validation
        function validateEmergencyContact(form) {
            const requiredFields = ['organization', 'contact_name', 'email', 'phone', 'service_type'];
            let isValid = true;
            
            requiredFields.forEach(fieldName => {
                const field = form.querySelector(`[name="${fieldName}"]`);
                if (!field || !field.value.trim()) {
                    showNotification(`${fieldName.replace('_', ' ')} is required for emergency services`, 'error');
                    isValid = false;
                }
            });
            
            return isValid;
        }

        // Mission readiness check (educational feature)
        function checkMissionReadiness() {
            const checks = [
                { name: 'Aircraft Status', status: true },
                { name: 'Fuel Level', status: true },
                { name: 'Medical Equipment', status: true },
                { name: 'Communication Systems', status: true },
                { name: 'Weather Conditions', status: true },
                { name: 'Crew Certification', status: true }
            ];
            
            let readyCount = checks.filter(check => check.status).length;
            let readiness = (readyCount / checks.length * 100).toFixed(1);
            
            showNotification(`Mission Readiness: ${readiness}% - ${readyCount}/${checks.length} systems green`, 'success');
            
            return readiness > 95;
        }
    </script>
</body>
</html>