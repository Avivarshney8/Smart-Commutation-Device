<?php
header('Content-Type: application/json');

$host = '127.0.0.1'; // More reliable than 'localhost'
$db   = 'smart_commutation';
$user = 'root';      // Default XAMPP username
$pass = '';          // Default XAMPP password (empty)
$charset = 'utf8mb4';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, $options);
    
    // Test connection
    $pdo->query("SELECT 1");
} catch (PDOException $e) {
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed',
        'error' => $e->getMessage(),
        'credentials' => [
            'host' => $host,
            'db' => $db,
            'user' => $user
        ]
    ]));
}
?>