<?php
// api_login.php
header('Content-Type: application/json');
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
// ตรวจสอบ reCAPTCHA
$recaptcha = $data['recaptcha'] ?? '';
if (!$recaptcha) {
    echo json_encode(['success' => false, 'error' => 'กรุณายืนยัน reCAPTCHA']);
    exit;
}
$secret = '6Lc7pnIrAAAAAPbAXGgo3RhLkq7UK8Gfcojw7QUJ';
$verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=" . urlencode($recaptcha));
$captcha_success = json_decode($verify, true);
if (empty($captcha_success['success'])) {
    echo json_encode(['success' => false, 'error' => 'reCAPTCHA ไม่ถูกต้อง']);
    exit;
}
// ตรวจสอบข้อมูลพื้นฐาน
$user = trim($data['user'] ?? '');
$password = $data['password'] ?? '';
if (empty($user) || empty($password)) {
    echo json_encode(['success' => false, 'error' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM users WHERE username=? OR email=?');
$stmt->execute([$user, $user]);
$userRow = $stmt->fetch();

if ($userRow && md5($password) === $userRow['password']) {
    $_SESSION['user_id'] = $userRow['id'];
    $_SESSION['username'] = $userRow['username'];
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Username, Email หรือ Password ไม่ถูกต้อง']);
}
