<?php
require 'db_connection.php';

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'User not logged in.']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$user_id = $_SESSION['user']['id'];

$name = $data['name'] ?? null;
$surname = $data['surname'] ?? null;
$email = $data['email'] ?? null;

try {
    $sql = "UPDATE users SET name = ?, surname = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$name, $surname, $email, $user_id]);

    echo json_encode(['success' => true, 'message' => 'Profile updated successfully.']);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Failed to update profile: ' . $e->getMessage()]);
}
?>
