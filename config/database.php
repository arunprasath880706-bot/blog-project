<?php
// ============================================
// DATABASE CONNECTION
// Task 2 & 3: Database Setup
// ============================================

// Database credentials
$host = 'localhost';
$dbname = 'blog';
$username = 'root';
$password = '';  // Leave empty if no password set

try {
    // Create PDO instance with MySQL
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Set PDO error mode to Exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to Associative Array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Optional: Uncomment to test connection
    // echo "✅ Connected successfully to database: $dbname";
    
} catch(PDOException $e) {
    // Display error if connection fails
    die("❌ Connection failed: " . $e->getMessage());
}
?>