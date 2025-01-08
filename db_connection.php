<?php
$host = '127.0.0.1';
$db_name = 'news_portal';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;port=3307;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);;
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
