<?php
require 'db_connection.php';

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

session_start();

$data = json_decode(file_get_contents("php://input"), true);
$title = $data['title'] ?? null;
$content = $data['content'] ?? null;
$category = $data['category'] ?? null;

if (!isset($_SESSION['username'])) {
    echo json_encode(['error' => 'You must be logged in to add news.']);
    exit;
}

if (!$title || !$content || !$category) {
    echo json_encode(['error' => 'Title, content, and category are required.']);
    exit;
}

try {
    $author = $_SESSION['username'];
    $date = date('Y-m-d H:i:s');
    $sql = "INSERT INTO news (title, content, author, date, category) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$title, $content, $author, $date, $category]);

    echo json_encode(['message' => 'News added successfully!']);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Failed to add news: ' . $e->getMessage()]);
}
?>
