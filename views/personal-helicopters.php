<?php
/**
 * Personal Helicopters Category Page
 * Educational content + E-commerce focused on personal use helicopters
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
    
    // Get personal helicopters with pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 12;
    $offset = ($page - 1) * $limit;
    
    // Filter for personal category
    $filters = ['category' => 'personal'];
    
    // Add any additional filters from URL
    if (!empty($_GET['manufacturer'])) $filters['manufacturer'] = $_GET['manufacturer'];
    if (!empty($_GET['min_price'])) $filters['min_price'] = $_GET['min_price'];
    if (!empty($_GET['max_price'])) $filters['max_price'] = $_GET['max_price'];
    if (!empty($_GET['search'])) $filters['search'] = $_GET['search'];
    
    $personalHelicopters = $helicopter->getAllHelicopters($limit, $offset, $filters);
    $totalPersonalCount = $helicopter->getTotalCount($filters);
    $totalPages = ceil($totalPersonalCount / $limit);
    $manufacturers = $helicopter->getManufacturers();
    
} catch (Exception $e) {
    error_log('Personal helicopters page error: ' . $e->getMessage());
    $personalHelicopters = [];
    $totalPersonalCount = 0;
    $totalPages = 1;
    $manufacturers = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Helicopters - Private Aviation Freedom | Toronto, Ontario</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Discover personal helicopters for private use in Toronto. From training aircraft to luxury personal transport, find your perfect rotorcraft for recreational flying.">
    <meta name="keywords" content="personal helicopters, private helicopters, recreational flying, pilot training, Toronto helicopter sales">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="/helicopter-marketplace/assets/css/app.css">
    
    <style>
        /* Personal Aviation Specific Styles */
        .personal-hero {
            background: linear-gradient(135deg, 
                rgba(255, 107, 53, 0.85), 
                rgba(255, 140, 66, 0.75)), 
                url('/assets/images/personal-flying.jpg') center/cover;
            padding: 120px 0 80px;
            text-align: center;
            color: white;
            position: relative;
        }

        .personal-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 30% 70%, rgba(255, 107, 53, 0.3), transparent 50%);
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 20px;
            border-radius: 25px;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Education Cards */
        .education-section {
            padding: 80px 0;
            background: #1a1a1a;
        }

        .education-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .education-card {
            background: linear-gradient(135deg, #333333, #2a2a2a);
            border-radius: 15px;
            padding: 30px;
            border: 1px solid rgba(255, 107, 53, 0.2);
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .education-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #FF6B35, #FF8C42);
        }

        .education-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(255, 107, 53, 0.2);
            border-color: #FF6B35;
        }

        .card-icon {
            font-size: 3rem;
            color: #FF6B35;
            margin-bottom: 20px;
        }

        /* Benefits Grid */
        .benefits-section {
            padding: 80px 0;
            background: #2a2a2a;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 50px;
        }

        .benefit-item {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            padding: 25px;
            background: rgba(255, 107, 53, 0.05);
            border-radius: 10px;
            border: 1px solid rgba(255, 107, 53, 0.1);
            transition: all 0.3s ease;
        }

        .benefit-item:hover {
            background: rgba(255, 107, 53, 0.1);
            border-color: rgba(255, 107, 53, 0.3);
            transform: translateY(-2px);
        }

        .benefit-icon {
            font-size: 2rem;
            color: #FF6B35;
            min-width: 40px;
        }

        .benefit-content h4 {
            color: #ffffff;
            margin-bottom: 10px;
        }

        .benefit-content p {
            color: #cccccc;
            margin: 0;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* Popular Models Showcase */
        .popular-models {
            padding: 80px 0;
            background: #1a1a1a;
        }

        .models-showcase {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .model-card {
            background: #333333;
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid rgba(255, 107, 53, 0.2);
            transition: all 0.3s ease;
            position: relative;
        }

        .model-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            border-color: #FF6B35;
        }

        .model-image {
            height: 250px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .model-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: linear-gradient(135deg, #FF6B35, #FF8C42);
            color: white;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .model-info {
            padding: 25px;
        }

        .model-specs {
            display: flex;
            justify-content: space-between;
            margin: 15px 0;
            padding: 15px;
            background: rgba(255, 107, 53, 0.1);
            border-radius: 8px;
        }

        .spec-item {
            text-align: center;
        }

        .spec-value {
            font-weight: bold;
            color: #FF6B35;
            display: block;
            font-size: 1.1rem;
        }

        .spec-label {
            font-size: 0.8rem;
            color: #aaaaaa;
            text-transform: uppercase;
        }

        /* Financing Section */
        .financing-section {
            padding: 60px 0;
            background: linear-gradient(135deg, #FF6B35, #FF8C42);
            color: white;
            text-align: center;
        }

        .financing-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .financing-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .financing-option {
            background: rgba(255, 255, 255, 0.1);
            padding: 25px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }

        /* Filter Section */
        .filter-section {
            background: #333333;
            padding: 30px 0;
            margin-top: 80px;
            border-radius: 15px;
            margin-bottom: 40px;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            color: #FF6B35;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .filter-group input,
        .filter-group select {
            padding: 10px 12px;
            border: 1px solid #555555;
            border-radius: 6px;
            background: #2a2a2a;
            color: #ffffff;
            transition: all 0.3s ease;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: #FF6B35;
            box-shadow: 0 0 10px rgba(255, 107, 53, 0.3);
        }

        /* Results Section */
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .results-info {
            color: #cccccc;
        }

        .results-info strong {
            color: #FF6B35;
        }

        .view-options {
            display: flex;
            gap: 10px;
        }

        .view-btn {
            padding: 8px 12px;
            border: 1px solid #555555;
            background: #2a2a2a;
            color: #cccccc;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .view-btn.active,
        .view-btn:hover {
            background: #FF6B35;
            color: white;
            border-color: #FF6B35;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .personal-hero h1 {
                font-size: 2.5rem;
            }

            .filter-grid {
                grid-template-columns: 1fr;
            }

            .results-header {
                flex-direction: column;
                align-items: stretch;
            }

            .models-showcase,
            .education-cards {
                grid-template-columns: 1fr;
            }

            .financing-options {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <!-- Include Header -->
    <?php include '../includes/header.php'; ?>

    <!-- Personal Aviation Hero Section -->
    <section class="personal-hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-badge">
                    <i class="fas fa-user"></i> Personal Aviation
                </div>
                <h1><i class="fas fa-helicopter"></i> Your Gateway to Private Flight</h1>
                <p style="font-size: 1.3rem; max-width: 700px; margin: 0 auto 30px;">
                    Discover the freedom of personal helicopter ownership. Whether you're seeking adventure, 
                    convenience, or pursuing your pilot's license, explore our collection of aircraft designed 
                    for individual pilots and private use in the Greater Toronto Area.
                </p>
                <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                    <a href="#aircraft-inventory" class="btn btn-outline btn-large" style="border-color: white; color: white;">
                        <i class="fas fa-search"></i> Browse Aircraft
                    </a>
                    <a href="#learning-center" class="btn btn-primary btn-large" style="background: white; color: #FF6B35;">
                        <i class="fas fa-graduation-cap"></i> Learn to Fly
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Education Section -->
    <section class="education-section" id="learning-center">
        <div class="container">
            <div class="section-title">
                <h2><i class="fas fa-book-open"></i> Personal Aviation Guide</h2>
                <p>Everything you need to know about owning and flying personal helicopters</p>
            </div>

            <div class="education-cards">
                <!-- Getting Started Card -->
                <div class="education-card" data-aos="fade-up">
                    <div class="card-icon">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <h3>Getting Started</h3>
                    <p>New to helicopters? Learn the basics of rotorcraft flight, licensing requirements, 
                       and what to expect from your first helicopter experience. Our Toronto-based instructors 
                       guide you through every step.</p>
                    <div class="card-features">
                        <span class="feature-tag">✓ Private Pilot License</span>
                        <span class="feature-tag">✓ Ground School</span>
                        <span class="feature-tag">✓ Flight Training</span>
                    </div>
                </div>

                <!-- Choosing Your Aircraft -->
                <div class="education-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-icon">
                        <i class="fas fa-helicopter"></i>
                    </div>
                    <h3>Choosing Your Aircraft</h3>
                    <p>From training helicopters like the Robinson R22 to luxury personal transport aircraft, 
                       understand the factors that determine which helicopter best fits your mission, budget, and experience level.</p>
                    <div class="card-features">
                        <span class="feature-tag">✓ Mission Analysis</span>
                        <span class="feature-tag">✓ Budget Planning</span>
                        <span class="feature-tag">✓ Performance Comparison</span>
                    </div>
                </div>

                <!-- Ownership Costs -->
                <div class="education-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <h3>Ownership Economics</h3>
                    <p>Understand the true cost of helicopter ownership including purchase price, insurance, 
                       maintenance, hangar fees, and operating costs. Make informed financial decisions with our cost calculator.</p>
                    <div class="card-features">
                        <span class="feature-tag">✓ Cost Calculator</span>
                        <span class="feature-tag">✓ Insurance Guide</span>
                        <span class="feature-tag">✓ Financing Options</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits of Personal Helicopters -->
    <section class="benefits-section">
        <div class="container">
            <div class="section-title">
                <h2><i class="fas fa-star"></i> Why Choose Personal Aviation?</h2>
                <p>Discover the advantages that make helicopter ownership worthwhile</p>
            </div>

            <div class="benefits-grid">
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="benefit-content">
                        <h4>Time Freedom</h4>
                        <p>Travel on your schedule. Bypass traffic, reach remote locations, and cut travel time dramatically. Toronto to Muskoka in 45 minutes instead of 2+ hours driving.</p>
                    </div>
                </div>

                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <div class="benefit-content">
                        <h4>Access Anywhere</h4>
                        <p>Land virtually anywhere with appropriate permissions. Access remote cottages, private properties, and locations unreachable by conventional transport.</p>
                    </div>
                </div>

                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="benefit-content">
                        <h4>Safety & Reliability</h4>
                        <p>Modern helicopters feature advanced avionics, redundant systems, and rigorous maintenance standards. Statistically safer than driving for experienced pilots.</p>
                    </div>
                </div>

                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="benefit-content">
                        <h4>Pure Adventure</h4>
                        <p>Experience the thrill of three-dimensional flight. Perfect for sightseeing, photography, and creating unforgettable memories with family and friends.</p>
                    </div>
                </div>

                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="benefit-content">
                        <h4>Community & Network</h4>
                        <p>Join an exclusive community of pilots and aviation enthusiasts. Access to private airfields, fly-ins, and networking opportunities across Ontario.</p>
                    </div>
                </div>

                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="benefit-content">
                        <h4>Investment Value</h4>
                        <p>Well-maintained helicopters hold their value well. Quality aircraft from reputable manufacturers often appreciate over time with proper care.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Personal Models -->
    <section class="popular-models">
        <div class="container">
            <div class="section-title">
                <h2><i class="fas fa-trophy"></i> Most Popular Personal Aircraft</h2>
                <p>Discover the helicopters that personal pilots trust most</p>
            </div>

            <div class="models-showcase">
                <!-- Robinson R44 -->
                <div class="model-card">
                    <div class="model-image" style="background-image: url('/assets/images/helicopters/r44-personal.jpg');">
                        <div class="model-badge">Best Seller</div>
                    </div>
                    <div class="model-info">
                        <h3>Robinson R44 Raven II</h3>
                        <p>The world's most popular personal helicopter. Perfect balance of performance, reliability, and operating costs for private pilots.</p>
                        <div class="model-specs">
                            <div class="spec-item">
                                <span class="spec-value">4</span>
                                <span class="spec-label">Seats</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-value">130</span>
                                <span class="spec-label">Max Speed</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-value">348</span>
                                <span class="spec-label">Range (mi)</span>
                            </div>
                        </div>
                        <div class="model-price" style="font-size: 1.5rem; color: #FF6B35; font-weight: bold; margin-bottom: 15px;">
                            Starting at $505,000
                        </div>
                        <a href="/helicopter/1" class="btn btn-primary btn-full">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                    </div>
                </div>

                <!-- Robinson R22 -->
                <div class="model-card">
                    <div class="model-image" style="background-image: url('/assets/images/helicopters/r22-training.jpg');">
                        <div class="model-badge">Training Favorite</div>
                    </div>
                    <div class="model-info">
                        <h3>Robinson R22 Beta II</h3>
                        <p>The industry standard training helicopter. Ideal for flight schools and new pilots building experience.</p>
                        <div class="model-specs">
                            <div class="spec-item">
                                <span class="spec-value">2</span>
                                <span class="spec-label">Seats</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-value">110</span>
                                <span class="spec-label">Max Speed</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-value">240</span>
                                <span class="spec-label">Range (mi)</span>
                            </div>
                        </div>
                        <div class="model-price" style="font-size: 1.5rem; color: #FF6B35; font-weight: bold; margin-bottom: 15px;">
                            Starting at $285,000
                        </div>
                        <a href="/helicopter/2" class="btn btn-primary btn-full">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                    </div>
                </div>

                <!-- Schweizer 300 -->
                <div class="model-card">
                    <div class="model-image" style="background-image: url('/assets/images/helicopters/schweizer-300.jpg');">
                        <div class="model-badge">Value Choice</div>
                    </div>
                    <div class="model-info">
                        <h3>Schweizer 300CBi</h3>
                        <p>Rugged and reliable, perfect for training and personal flying with excellent safety record and lower operating costs.</p>
                        <div class="model-specs">
                            <div class="spec-item">
                                <span class="spec-value">3</span>
                                <span class="spec-label">Seats</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-value">105</span>
                                <span class="spec-label">Max Speed</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-value">180</span>
                                <span class="spec-label">Range (mi)</span>
                            </div>
                        </div>
                        <div class="model-price" style="font-size: 1.5rem; color: #FF6B35; font-weight: bold; margin-bottom: 15px;">
                            Starting at $395,000
                        </div>
                        <a href="/helicopter/3" class="btn btn-primary btn-full">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Aircraft Inventory Section -->
    <section id="aircraft-inventory" style="padding: 80px 0;">
        <div class="container">
            <div class="section-title">
                <h2><i class="fas fa-search"></i> Personal Aircraft Inventory</h2>
                <p>Browse our complete selection of personal helicopters available in Toronto</p>
            </div>

            <!-- Filters -->
            <div class="filter-section">
                <form method="GET" action="/category/personal">
                    <div class="filter-grid">
                        <div class="filter-group">
                            <label for="manufacturer">Manufacturer</label>
                            <select id="manufacturer" name="manufacturer">
                                <option value="">All Manufacturers</option>
                                <?php foreach ($manufacturers as $mfr): ?>
                                    <option value="<?= htmlspecialchars($mfr) ?>" 
                                            <?= (isset($_GET['manufacturer']) && $_GET['manufacturer'] === $mfr) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($mfr) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label>Price Range</label>
                            <div style="display: grid; grid-template-columns: 1fr auto 1fr; gap: 10px; align-items: end;">
                                <input type="number" name="min_price" placeholder="Min $" 
                                       value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>">
                                <span style="color: #cccccc;">to</span>
                                <input type="number" name="max_price" placeholder="Max $" 
                                       value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="filter-group">
                            <label for="search">Search</label>
                            <input type="text" id="search" name="search" placeholder="Search aircraft..." 
                                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        </div>

                        <div class="filter-group" style="align-self: end;">
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
                    Showing <strong><?= count($personalHelicopters) ?></strong> of <strong><?= $totalPersonalCount ?></strong> personal helicopters
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
            <?php if (!empty($personalHelicopters)): ?>
                <div class="helicopters-grid" id="aircraft-grid">
                    <?php foreach ($personalHelicopters as $heli): ?>
                        <div class="helicopter-card">
                            <div class="helicopter-image" style="background-image: url('<?= !empty($heli['images']) ? json_decode($heli['images'])[0] : '/assets/images/helicopter-placeholder.jpg' ?>');">
                                <div class="price-tag"><?= formatPrice($heli['price']) ?></div>
                                <div class="category-badge">Personal</div>
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
                    <div class="pagination" style="margin-top: 40px;">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?><?= http_build_query($_GET) ? '&' . http_build_query(array_diff_key($_GET, ['page' => ''])) : '' ?>">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <?php if ($i == $page): ?>
                                <span class="current"><?= $i ?></span>
                            <?php else: ?>
                                <a href="?page=<?= $i ?><?= http_build_query($_GET) ? '&' . http_build_query(array_diff_key($_GET, ['page' => ''])) : '' ?>"><?= $i ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?= $page + 1 ?><?= http_build_query($_GET) ? '&' . http_build_query(array_diff_key($_GET, ['page' => ''])) : '' ?>">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <!-- No Results -->
                <div class="no-results" style="text-align: center; padding: 60px 20px;">
                    <i class="fas fa-search" style="font-size: 4rem; color: #FF6B35; margin-bottom: 20px;"></i>
                    <h3>No Personal Helicopters Found</h3>
                    <p>Try adjusting your search criteria or browse all available aircraft.</p>
                    <a href="/category/personal" class="btn btn-primary">Clear Filters</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Financing Section -->
    <section class="financing-section">
        <div class="container">
            <div class="financing-content">
                <h2 style="font-size: 2.5rem; margin-bottom: 20px;">Flexible Financing Solutions</h2>
                <p style="font-size: 1.2rem; margin-bottom: 40px;">
                    Make your dream of helicopter ownership a reality with our comprehensive financing options. 
                    Our Toronto-based finance specialists work with leading aviation lenders across Canada.
                </p>
                
                <div class="financing-options">
                    <div class="financing-option">
                        <h4 style="margin-bottom: 15px;"><i class="fas fa-percentage"></i> Competitive Rates</h4>
                        <p>Starting from 5.9% APR for qualified buyers with terms up to 20 years.</p>
                    </div>
                    <div class="financing-option">
                        <h4 style="margin-bottom: 15px;"><i class="fas fa-handshake"></i> Quick Approval</h4>
                        <p>Pre-qualification in 24 hours, full approval within 72 hours.</p>
                    </div>
                    <div class="financing-option">
                        <h4 style="margin-bottom: 15px;"><i class="fas fa-calculator"></i> Payment Calculator</h4>
                        <p>Use our online tools to estimate payments and explore options.</p>
                    </div>
                    <div class="financing-option">
                        <h4 style="margin-bottom: 15px;"><i class="fas fa-shield-alt"></i> Insurance Included</h4>
                        <p>Comprehensive insurance packages available through our partners.</p>
                    </div>
                </div>
                
                <div style="margin-top: 40px;">
                    <a href="/financing" class="btn btn-outline btn-large" style="border-color: white; color: white; margin-right: 15px;">
                        <i class="fas fa-calculator"></i> Calculate Payment
                    </a>
                    <a href="/contact" class="btn btn-primary btn-large" style="background: white; color: #FF6B35;">
                        <i class="fas fa-phone"></i> Speak with Specialist
                    </a>
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
                localStorage.setItem('personalViewPreference', 'grid');
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
            const savedView = localStorage.getItem('personalViewPreference') || 'grid';
            toggleView(savedView);
            
            console.log('✅ Personal Helicopters page loaded with <?= count($personalHelicopters) ?> aircraft');
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