<?php
/**
 * Database Setup Script
 * This script creates the database and imports all tables with UTF8MB4 charset
 */

// Include database configuration
require_once __DIR__ . '/includes/db.php';

echo "<h2>Setting up database...</h2>\n";

try {
    // Connect without selecting database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error . "\n");
    }
    
    echo "✓ Connected to MySQL server<br>\n";
    
    // Create database if not exists
    $sql = "CREATE DATABASE IF NOT EXISTS `pro` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if ($conn->query($sql)) {
        echo "✓ Database 'pro' created successfully<br>\n";
    } else {
        echo "✗ Error creating database: " . $conn->error . "<br>\n";
        exit(1);
    }
    
    // Select the database
    $conn->select_db("pro");
    
    // Read and execute migration file
    $migrationFile = __DIR__ . '/database/migrations/001_create_tables.sql';
    if (file_exists($migrationFile)) {
        echo "✓ Reading migration file...<br>\n";
        
        $sqlContent = file_get_contents($migrationFile);
        
        // Remove comments and split by semicolon
        $sqlContent = preg_replace('/--.*$/m', '', $sqlContent);
        
        // Execute each statement
        $statements = explode(';', $sqlContent);
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (empty($statement)) {
                continue;
            }
            
            if ($conn->query($statement)) {
                $successCount++;
            } else {
                $errorCount++;
                // Only show error if it's not a "table already exists" error
                if (strpos($conn->error, 'already exists') === false) {
                    echo "⚠ Warning: " . $conn->error . "<br>\n";
                }
            }
        }
        
        echo "✓ Executed $successCount SQL statements successfully<br>\n";
        if ($errorCount > 0 && $successCount > 0) {
            echo "ℹ Some warnings were encountered (tables may already exist)<br>\n";
        }
    } else {
        echo "✗ Migration file not found: $migrationFile<br>\n";
        exit(1);
    }
    
    // Set charset to utf8mb4
    $conn->set_charset("utf8mb4");
    echo "✓ Charset set to UTF8MB4<br>\n";
    
    // Verify tables
    $result = $conn->query("SHOW TABLES");
    $tableCount = 0;
    echo "<br><strong>Created tables:</strong><br>\n";
    while ($row = $result->fetch_row()) {
        echo "  ✓ " . $row[0] . "<br>\n";
        $tableCount++;
    }
    
    echo "<br><strong>✓ Database setup completed successfully!</strong><br>\n";
    echo "<strong>Total tables: $tableCount</strong><br>\n";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<br><strong>✗ Error: " . $e->getMessage() . "</strong><br>\n";
    exit(1);
}

echo "<br><p><a href='index.php'>← Back to application</a></p>\n";
?>
