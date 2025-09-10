<?php
/**
 * PATH TESTING FILE
 * Save this as views/test-paths.php
 * Then add a route to test it: /test-paths
 */

echo "<h1>Path Testing</h1>";
echo "<p><strong>Current file:</strong> " . __FILE__ . "</p>";
echo "<p><strong>Current directory:</strong> " . __DIR__ . "</p>";
echo "<p><strong>Parent directory:</strong> " . dirname(__DIR__) . "</p>";

// Test header include path
$headerPath = '../includes/header.php';
echo "<p><strong>Testing header path:</strong> $headerPath</p>";
echo "<p><strong>Full header path:</strong> " . realpath($headerPath) . "</p>";
echo "<p><strong>Header exists?</strong> " . (file_exists($headerPath) ? 'YES' : 'NO') . "</p>";

// Test wrong path (what VSCode suggests)
$wrongHeaderPath = '../../includes/header.php';
echo "<p><strong>Wrong path (VSCode suggestion):</strong> $wrongHeaderPath</p>";
echo "<p><strong>Wrong path exists?</strong> " . (file_exists($wrongHeaderPath) ? 'YES' : 'NO') . "</p>";

// Show working directory when this file runs
echo "<p><strong>Working directory when this runs:</strong> " . getcwd() . "</p>";

// Test if we can include the header
echo "<hr><h2>Attempting to include header:</h2>";
try {
    if (file_exists($headerPath)) {
        echo "<p style='color: green;'>✓ Header file found, including it:</p>";
        include $headerPath;
    } else {
        echo "<p style='color: red;'>✗ Header file not found at: $headerPath</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error including header: " . $e->getMessage() . "</p>";
}
?>