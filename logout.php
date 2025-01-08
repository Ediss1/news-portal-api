<?php
header("Access-Control-Allow-Origin: http://localhost:4200"); // Replace with your frontend origin
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

session_start();
session_unset(); // Clear all session variables
session_destroy(); // Destroy the session

echo json_encode(['message' => 'Logged out successfully!']);
?>
