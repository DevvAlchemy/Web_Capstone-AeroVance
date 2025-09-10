
// Load saved view<?php
// Get helicopters with filtering
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Helicopter.php';

try {
    $database = new Database();
    $db = $database->connect();
    $helicopter = new Helicopter($db);
    
    // Get filter parameters
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 12;
    $offset = ($page - 1) * $limit;
    
    $filters = [
        'category' => $_GET['category'] ?? '',
        'manufacturer' => $_GET['manufacturer'] ?? '',
        'min_price' => $_GET['min_price'] ?? '',
        'max_price' => $_GET['max_price'] ?? '',
        'condition' => $_GET['condition'] ?? '',
        'search' => $_GET['search'] ?? ''
    ];
    
    // Get helicopters and supporting data
    $helicopters = $helicopter->getAllHelicopters($limit, $offset, $filters);
    $manufacturers = $helicopter->getManufacturers();
    $totalCount = 24; // Simplified for now
    $totalPages = ceil($totalCount / $limit);
    
} catch (Exception $e) {
    $helicopters = [];
    $manufacturers = [];
    $totalPages = 1;
    error_log('Catalog error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/helicopter-marketplace/assets/css/app.css">
    <!-- External Libraries -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <title>Helicopter Catalog - Professional Aircraft Marketplace</title>
    
</head>
<body>
    <!-- Header Component -->
    <header class="header" id="header">
        <div class="nav-container">
            <a href="/" class="logo">
                <i class="fas fa-helicopter"></i>
                <span>AEROVANCE</span>
            </a>
            
            <nav class="nav-menu">
                <a href="/">Home</a>
                <a href="/helicopters" class="active">Helicopters</a>
                <a href="/category/personal">Personal</a>
                <a href="/category/business">Business</a>
                <a href="/category/emergency">Emergency</a>
                <a href="/about">About</a>
                <a href="/contact">Contact</a>
            </nav>
            
            <div class="auth-buttons">
                <a href="/login" class="btn btn-outline">Login</a>
                <a href="/register" class="btn btn-primary">Sign Up</a>
            </div>
        </div>
    </header>

    <!-- Catalog Header Component -->
    <section class="catalog-header">
        <div class="container">
            <div class="catalog-title">
                <h1><i class="fas fa-helicopter"></i> Professional Aircraft Catalog</h1>
                <p>Discover premium helicopters for personal, business, and emergency use</p>
            </div>
        </div>
    </section>

    <!-- Search Filters Component -->
    <section class="container">
        <form class="search-filters" method="GET" action="/helicopters">
            <div class="filters-grid">
                <div class="filter-group">
                    <label for="search">Search</label>
                    <input type="text" id="search" name="search" placeholder="Search helicopters..." 
                           value="<?= htmlspecialchars($filters['search']) ?>">
                </div>

                <div class="filter-group">
                    <label for="category">Category</label>
                    <select id="category" name="category">
                        <option value="">All Categories</option>
                        <option value="personal" <?= $filters['category'] === 'personal' ? 'selected' : '' ?>>Personal Use</option>
                        <option value="business" <?= $filters['category'] === 'business' ? 'selected' : '' ?>>Business</option>
                        <option value="emergency" <?= $filters['category'] === 'emergency' ? 'selected' : '' ?>>Emergency Services</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="manufacturer">Manufacturer</label>
                    <select id="manufacturer" name="manufacturer">
                        <option value="">All Manufacturers</option>
                        <?php foreach ($manufacturers as $mfr): ?>
                            <option value="<?= htmlspecialchars($mfr) ?>" 
                                    <?= $filters['manufacturer'] === $mfr ? 'selected' : '' ?>>
                                <?= htmlspecialchars($mfr) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="condition">Condition</label>
                    <select id="condition" name="condition">
                        <option value="">All Conditions</option>
                        <option value="new" <?= $filters['condition'] === 'new' ? 'selected' : '' ?>>New</option>
                        <option value="used" <?= $filters['condition'] === 'used' ? 'selected' : '' ?>>Used</option>
                        <option value="refurbished" <?= $filters['condition'] === 'refurbished' ? 'selected' : '' ?>>Refurbished</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Price Range</label>
                    <div class="price-range">
                        <input type="number" name="min_price" placeholder="Min $" 
                               value="<?= htmlspecialchars($filters['min_price']) ?>">
                        <span style="color: #cccccc; font-size: 0.9rem;">to</span>
                        <input type="number" name="max_price" placeholder="Max $" 
                               value="<?= htmlspecialchars($filters['max_price']) ?>">
                    </div>
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search Aircraft
                </button>
                <a href="/helicopters" class="btn btn-outline">
                    <i class="fas fa-times"></i> Clear Filters
                </a>
            </div>
        </form>
    </section>

    <!-- Results Section -->
    <section class="container">
        <!-- Results Header Component -->
        <div class="results-header">
            <div class="results-info">
                Showing <strong><?= count($helicopters) ?></strong> helicopters
                <?php if (!empty($filters['category'])): ?>
                    in <strong><?= ucfirst($filters['category']) ?></strong> category
                <?php endif; ?>
            </div>
            
            <div class="view-controls">
                <div class="view-toggle">
                    <button class="view-btn active" onclick="toggleView('grid')" id="grid-btn">
                        <i class="fas fa-th-large"></i>
                    </button>
                    <button class="view-btn" onclick="toggleView('list')" id="list-btn">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
                
                <select class="sort-select" id="sort-select">
                    <option value="newest">Newest First</option>
                    <option value="price-low">Price: Low to High</option>
                    <option value="price-high">Price: High to Low</option>
                    <option value="name">Name A-Z</option>
                </select>
            </div>
        </div>

        <?php if (empty($helicopters)): ?>
            <!-- No Results Component -->
            <div class="no-results">
                <i class="fas fa-search"></i>
                <h3>No helicopters found</h3>
                <p>Try adjusting your search criteria or browse all available aircraft.</p>
                <a href="/helicopters" class="btn btn-primary" style="margin-top: 20px;">Browse All</a>
            </div>
        <?php else: ?>
            <!-- Grid View Component -->
            <div class="helicopters-grid" id="grid-view">
                <?php foreach ($helicopters as $heli): ?>
                    <div class="helicopter-card">
                        <div class="helicopter-image">
                            <div class="image-overlay"></div>
                            <div class="price-badge"><?= formatPrice($heli['price']) ?></div>
                            <div class="category-badge"><?= ucfirst($heli['category']) ?></div>
                        </div>
                        
                        <div class="helicopter-info">
                            <h3 class="helicopter-title"><?= htmlspecialchars($heli['name']) ?></h3>
                            <div class="helicopter-subtitle">
                                <?= htmlspecialchars($heli['manufacturer']) ?> • 
                                <?= htmlspecialchars($heli['model']) ?> • 
                                <?= $heli['year'] ?>
                            </div>
                            
                            <p class="helicopter-description">
                                <?= htmlspecialchars(substr($heli['description'], 0, 120)) ?>...
                            </p>
                            
                            <div class="helicopter-specs">
                                <div class="spec-item">
                                    <span class="spec-value"><?= $heli['max_speed'] ?? 'N/A' ?></span>
                                    <div class="spec-label">Max Speed</div>
                                </div>
                                <div class="spec-item">
                                    <span class="spec-value"><?= $heli['range'] ?? 'N/A' ?></span>
                                    <div class="spec-label">Range</div>
                                </div>
                                <div class="spec-item">
                                    <span class="spec-value"><?= $heli['passenger_capacity'] ?? 'N/A' ?></span>
                                    <div class="spec-label">Passengers</div>
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

            <!-- List View Component -->
            <div class="helicopters-list" id="list-view">
                <?php foreach ($helicopters as $heli): ?>
                    <div class="helicopter-list-item">
                        <div class="list-content">
                            <div class="list-image"></div>
                            
                            <div class="list-details">
                                <h3 class="helicopter-title"><?= htmlspecialchars($heli['name']) ?></h3>
                                <div class="helicopter-subtitle">
                                    <?= htmlspecialchars($heli['manufacturer']) ?> • 
                                    <?= htmlspecialchars($heli['model']) ?> • 
                                    <?= $heli['year'] ?> • 
                                    <?= ucfirst($heli['category']) ?> • 
                                    <?= ucfirst($heli['condition']) ?>
                                </div>
                                
                                <div class="list-specs">
                                    <div class="spec-item">
                                        <span class="spec-value"><?= $heli['max_speed'] ?? 'N/A' ?></span>
                                        <div class="spec-label">Max Speed</div>
                                    </div>
                                    <div class="spec-item">
                                        <span class="spec-value"><?= $heli['range'] ?? 'N/A' ?></span>
                                        <div class="spec-label">Range</div>
                                    </div>
                                    <div class="spec-item">
                                        <span class="spec-value"><?= $heli['passenger_capacity'] ?? 'N/A' ?></span>
                                        <div class="spec-label">Passengers</div>
                                    </div>
                                </div>
                                
                                <p class="helicopter-description">
                                    <?= htmlspecialchars(substr($heli['description'], 0, 200)) ?>...
                                </p>
                            </div>
                            
                            <div class="list-actions">
                                <div class="list-price"><?= formatPrice($heli['price']) ?></div>
                                <a href="/helicopter/<?= $heli['id'] ?>" class="btn btn-primary">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                                <button class="btn btn-outline" onclick="contactSeller(<?= $heli['id'] ?>)">
                                    <i class="fas fa-envelope"></i> Contact
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination Component -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?><?= http_build_query($filters) ? '&' . http_build_query($filters) : '' ?>">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="current"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $i ?><?= http_build_query($filters) ? '&' . http_build_query($filters) : '' ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?><?= http_build_query($filters) ? '&' . http_build_query($filters) : '' ?>">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </section>

    <!-- Footer Component -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>Aircraft Categories</h4>
                    <ul>
                        <li><a href="/category/personal">Personal Use</a></li>
                        <li><a href="/category/business">Business Aviation</a></li>
                        <li><a href="/category/emergency">Emergency Services</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="/contact">Contact Sales</a></li>
                        <li><a href="/financing">Financing Options</a></li>
                        <li><a href="/maintenance">Maintenance Services</a></li>
                        <li><a href="/support">Customer Support</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="/about">About Us</a></li>
                        <li><a href="/careers">Careers</a></li>
                        <li><a href="/news">Latest News</a></li>
                        <li><a href="/investors">Investor Relations</a></li>
                    </ul>
                </div>
                
             <div class="footer-section">
                <h4>Connect</h4>
                <p>Follow us for aviation updates</p>
                <div class="social-links">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2025 AeroVance. All rights reserved. | 
               <a href="/privacy">Privacy Policy</a> | 
               <a href="/terms">Terms of Service</a>
            </p>
        </div>
    </div>
</footer>

<style>