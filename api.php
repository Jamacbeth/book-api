<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->query("SELECT * FROM books ORDER BY created_at DESC");
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($books);
    exit;
} elseif ($method === 'POST') {
    $inputRaw = file_get_contents('php://input');
    $input = json_decode($inputRaw, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON', 'details' => json_last_error_msg()]);
        exit;
    }

    if (empty($input['title']) || empty($input['author'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing title or author']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO books (title, author, description, published_year) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $input['title'],
        $input['author'],
        $input['description'] ?? '',
        $input['published_year'] ?? date('Y')
    ]);

    echo json_encode([
        'message' => 'Book added successfully',
        'id' => $pdo->lastInsertId()
    ]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
exit;