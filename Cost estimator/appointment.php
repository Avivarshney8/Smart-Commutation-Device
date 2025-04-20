<?php
header('Content-Type: application/json');

require_once 'db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Validate input
if (empty($data['name']) || empty($data['email']) || empty($data['phone']) || 
    empty($data['carModel']) || empty($data['date']) || empty($data['time'])) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Sanitize data
$name = htmlspecialchars($data['name']);
$email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
$phone = htmlspecialchars($data['phone']);
$carModel = htmlspecialchars($data['carModel']);
$date = htmlspecialchars($data['date']);
$time = htmlspecialchars($data['time']);

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

try {
    // Check if the time slot is already booked
    $stmt = $pdo->prepare("SELECT * FROM appointments WHERE date = ? AND time_slot = ?");
    $stmt->execute([$date, $time]);
    $existingAppointment = $stmt->fetch();

    if ($existingAppointment) {
        echo json_encode(['success' => false, 'message' => 'This time slot is already booked. Please choose another time.']);
        exit;
    }

    // Insert new appointment
    $stmt = $pdo->prepare("INSERT INTO appointments (name, email, phone, car_model, date, time_slot, created_at) 
                          VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$name, $email, $phone, $carModel, $date, $time]);

    echo json_encode(['success' => true, 'message' => 'Appointment booked successfully!']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>