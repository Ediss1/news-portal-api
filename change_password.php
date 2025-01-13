<?php
require 'db_connection.php';

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $currentPassword = $data['currentPassword'] ?? '';
    $newPassword = $data['newPassword'] ?? '';
    $repeatPassword = $data['repeatPassword'] ?? '';

    if (empty($currentPassword) || empty($newPassword) || empty($repeatPassword)) {
        echo json_encode(['success' => false, 'error' => 'All fields are required.']);
        exit();
    }

    if ($newPassword !== $repeatPassword) {
        echo json_encode(['success' => false, 'error' => 'New passwords do not match.']);
        exit();
    }

    try {
        $userId = $_SESSION['user']['id'];

        // Fetch the user’s current password
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify the current password
        if (!$user || !password_verify($currentPassword, $user['password'])) {
            echo json_encode(['success' => false, 'error' => 'Current password is incorrect.']);
            exit();
        }

        // Update the password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $updateSql = "UPDATE users SET password = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->execute([$hashedPassword, $userId]);

        echo json_encode(['success' => true, 'message' => 'Password changed successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Failed to change password.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>
