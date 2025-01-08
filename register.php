<?php
require 'db_connection.php';

header("Access-Control-Allow-Origin: http://localhost:4200"); // Zamijenite s odgovarajućim originom
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pročitajte ulazne podatke
    $data = json_decode(file_get_contents("php://input"), true);

    // Provjera unosa
    if (!$data || !isset($data['name'], $data['surname'], $data['username'], $data['password'], $data['email'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'error' => 'All fields are required.']);
        exit;
    }

    $name = trim($data['name']);
    $surname = trim($data['surname']);
    $username = trim($data['username']);
    $password = trim($data['password']);
    $email = trim($data['email']);

    // Provjera praznih polja
    if (empty($name) || empty($surname) || empty($username) || empty($password) || empty($email)) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'error' => 'All fields are required and cannot be empty.']);
        exit;
    }

    // Validacija email adrese
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'error' => 'Invalid email format.']);
        exit;
    }

    // Hashiranje lozinke
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    try {
        // Unos korisnika u bazu podataka
        $sql = "INSERT INTO users (name, surname, username, password, email, role) VALUES (?, ?, ?, ?, ?, 'user')";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$name, $surname, $username, $hashedPassword, $email]);

        http_response_code(201); // Created
        echo json_encode(['success' => true, 'message' => 'Registration successful!']);
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') { // Greška za duplikat unosa
            http_response_code(409); // Conflict
            echo json_encode(['success' => false, 'error' => 'Username or email already exists.']);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'error' => 'Failed to register user: ' . $e->getMessage()]);
        }
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>
