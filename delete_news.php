<?php
require 'db_connection.php';

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
        echo json_encode(['error' => 'You must be logged in as an admin to delete news.']);
        exit;
    }

    $newsId = $_GET['id'] ?? null;

    if (!$newsId) {
        echo json_encode(['error' => 'News ID is required.']);
        exit;
    }

    try {
        $sql = "DELETE FROM news WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$newsId]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['message' => 'News deleted successfully.']);
        } else {
            echo json_encode(['error' => 'Failed to delete news or news does not exist.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}
?>
