<?php
header('Content-Type: application/json');
session_start();
require 'db.php';
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT * FROM db_requests WHERE user_id=? ORDER BY created_at DESC');
$stmt->execute([$user_id]);
echo json_encode(['success' => true, 'rows' => $stmt->fetchAll()]);
