<?php
header('Content-Type: application/json');
echo json_encode([
    ['id' => 1, 'title' => 'Test'],
    ['id' => 2, 'title' => 'Test2']
]);