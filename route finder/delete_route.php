<?php
require __DIR__ . '/config2.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid route ID']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM saved_routes WHERE id = ?");
    $success = $stmt->execute([$id]);
    
    echo json_encode(['success' => $success]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error',
        'error' => $e->getMessage()
    ]);
}
?>