<?php
require 'db_connection.php';

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
        echo json_encode(['error' => 'You do not have permission to update news.']);
        exit;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    $newsId = $_GET['id'] ?? null;
    $title = $data['title'] ?? null;
    $content = $data['content'] ?? null;
    $category = $data['category'] ?? null;

    if (!$newsId || !$title || !$content || !$category) {
        echo json_encode(['error' => 'All fields are required.']);
        exit;
    }

    try {
        $sql = "UPDATE news SET title = ?, content = ?, category = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([$title, $content, $category, $newsId]);

        if ($result) {
            echo json_encode(['message' => 'News updated successfully!']);
        } else {
            echo json_encode(['error' => 'Failed to update news.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}
?>
