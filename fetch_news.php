<?php
require 'db_connection.php';

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

try {
    $category = isset($_GET['category']) ? $_GET['category'] : null;
    $date = isset($_GET['date']) ? $_GET['date'] : null;

    $sql = "SELECT id, title, content, author, date, category FROM news WHERE 1=1";
    $params = [];

    if ($category) {
        $sql .= " AND category = ?";
        $params[] = $category;
    }

    if ($date) {
        $sql .= " AND DATE(date) = ?";
        $params[] = $date;
    }

    $sql .= " ORDER BY date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    $news = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'news' => $news]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Failed to fetch news: ' . $e->getMessage()]);
}
?>
