<?php
// Simple test to see your helicopter data
// Put this in your public folder as test_db.php

$host = 'localhost';
$dbname = 'helicopter_marketplace';
$username = 'root'; // Your MySQL username
$password = '';     // Your MySQL password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>🚁 Your Helicopter Database</h1>";
    
    // Get all helicopters
    $stmt = $pdo->query("
        SELECT 
            id, name, manufacturer, model, category, price, year, condition,
            max_speed, `range`, passenger_capacity, featured, status
        FROM helicopters 
        ORDER BY featured DESC, created_at DESC
    ");
    
    $helicopters = $stmt->fetchAll();
    
    echo "<h2>Found " . count($helicopters) . " helicopters:</h2>";
    
    if ($helicopters) {
        echo "<style>
            table { border-collapse: collapse; width: 100%; margin: 20px 0; }
            th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
            th { background-color: #FF6B35; color: white; }
            tr:nth-child(even) { background-color: #f2f2f2; }
            .featured { background-color: #fff3cd; }
            .price { font-weight: bold; color: #FF6B35; }
        </style>";
        
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th><th>Manufacturer</th><th>Category</th><th>Price</th><th>Year</th><th>Specs</th><th>Featured</th><th>Status</th></tr>";
        
        foreach ($helicopters as $h) {
            $rowClass = $h['featured'] ? 'featured' : '';
            echo "<tr class='$rowClass'>";
            echo "<td>{$h['id']}</td>";
            echo "<td>{$h['name']}</td>";
            echo "<td>{$h['manufacturer']} {$h['model']}</td>";
            echo "<td>" . ucfirst($h['category']) . "</td>";
            echo "<td class='price'>$" . number_format($h['price']) . "</td>";
            echo "<td>{$h['year']} ({$h['condition']})</td>";
            echo "<td>{$h['max_speed']}mph | {$h['range']}mi | {$h['passenger_capacity']}pax</td>";
            echo "<td>" . ($h['featured'] ? '⭐ Featured' : 'Standard') . "</td>";
            echo "<td>" . ucfirst($h['status']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Count by category
        $stmt = $pdo->query("SELECT category, COUNT(*) as count FROM helicopters GROUP BY category");
        $categories = $stmt->fetchAll();
        
        echo "<h3>By Category:</h3>";
        foreach ($categories as $cat) {
            echo "<p><strong>" . ucfirst($cat['category']) . ":</strong> {$cat['count']} helicopters</p>";
        }
        
        echo "<hr>";
        echo "<h3>✅ Database is ready for catalog development!</h3>";
        echo "<p>You can now build the catalog page that will display this data.</p>";
        
    } else {
        echo "<p>No helicopters found. Please run the cleanup script first.</p>";
    }
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>❌ Database Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>Make sure your database credentials are correct in this file.</p>";
}
?>