<?php
require __DIR__ . '/config2.php';

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (empty($data['startPoint']) || empty($data['endPoint'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Start and end points are required']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO saved_routes 
                         (start_point, end_point, distance, duration, timestamp) 
                         VALUES (?, ?, ?, ?, ?)");
    
    $success = $stmt->execute([
        $data['startPoint'],
        $data['endPoint'],
        $data['distance'],
        $data['duration'],
        $data['timestamp']
    ]);
    
    echo json_encode([
        'success' => $success,
        'message' => $success ? 'Route saved successfully' : 'Failed to save route',
        'id' => $pdo->lastInsertId()
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error',
        'error' => $e->getMessage()
    ]);
}
?>