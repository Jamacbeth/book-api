<?php
// Suppress any potential warnings/notices so they don't leak into JSON output
error_reporting(0);
ini_set('display_errors', 0);

// Database connection settings
$host = 'localhost';
$db   = 'books_db';
$user = 'root';
$pass = '';           // change this if you have set a MySQL password in XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Output clean JSON error and stop execution
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed', 'message' => $e->getMessage()]);
    exit;
}