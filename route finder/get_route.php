<?php
require __DIR__ . '/config2.php';

try {
    $stmt = $pdo->query("SELECT id, start_point as startPoint, end_point as endPoint, 
                         distance, duration, timestamp 
                         FROM saved_routes 
                         ORDER BY created_at DESC");
    
    $routes = $stmt->fetchAll();
    
    echo json_encode($routes);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error',
        'error' => $e->getMessage()
    ]);
}
?>