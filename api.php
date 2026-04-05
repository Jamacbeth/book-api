<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
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
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;

} elseif ($method === 'POST') {
    // Add new book (existing code)
    $input = json_decode(file_get_contents('php://input'), true);
    
    $stmt = $pdo->prepare("INSERT INTO books (title, author, description, published_year) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $input['title'],
        $input['author'],
        $input['description'] ?? '',
        $input['published_year'] ?? date('Y')
    ]);

    echo json_encode(['message' => 'Book added successfully', 'id' => $pdo->lastInsertId()]);
    exit;

} elseif ($method === 'PUT') {
    // UPDATE book
    $input = json_decode(file_get_contents('php://input'), true);
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid book ID']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE books SET title=?, author=?, description=?, published_year=? WHERE id=?");
    $success = $stmt->execute([
        $input['title'],
        $input['author'],
        $input['description'] ?? '',
        $input['published_year'] ?? date('Y'),
        $id
    ]);

    if ($success) {
        echo json_encode(['message' => 'Book updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update book']);
    }
    exit;

} elseif ($method === 'DELETE') {
    // Existing delete code...
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['message' => 'Book deleted successfully']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
exit;
?>