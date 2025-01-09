<?php
require 'db_connection.php';

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data || !isset($data['name'], $data['surname'], $data['username'], $data['password'], $data['email'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'All fields are required.']);
        exit;
    }

    $name = trim($data['name']);
    $surname = trim($data['surname']);
    $username = trim($data['username']);
    $password = trim($data['password']);
    $email = trim($data['email']);

    if (empty($name) || empty($surname) || empty($username) || empty($password) || empty($email)) {
        http_response_code(400); 
        echo json_encode(['success' => false, 'error' => 'All fields are required and cannot be empty.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400); 
        echo json_encode(['success' => false, 'error' => 'Invalid email format.']);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    try {
        
        $sql = "INSERT INTO users (name, surname, username, password, email, role) VALUES (?, ?, ?, ?, ?, 'user')";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$name, $surname, $username, $hashedPassword, $email]);

        http_response_code(201); 
        echo json_encode(['success' => true, 'message' => 'Registration successful!']);
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            http_response_code(409);
            echo json_encode(['success' => false, 'error' => 'Username or email already exists.']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to register user: ' . $e->getMessage()]);
        }
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>
