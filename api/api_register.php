<?php
// api_register.php
header('Content-Type: application/json');
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$username = trim($data['username'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$confirm = $data['confirm_password'] ?? '';

if ($password !== $confirm) {
    echo json_encode(['success' => false, 'error' => 'รหัสผ่านไม่ตรงกัน']);
    exit;
}

// ตรวจสอบ username/email ซ้ำ
$stmt = $pdo->prepare('SELECT id FROM users WHERE username=? OR email=?');
$stmt->execute([$username, $email]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'error' => 'Username หรือ Email นี้ถูกใช้แล้ว']);
    exit;
}

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
if (empty($username) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'error' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
    exit;
}
if (strlen($username) < 3) {
    echo json_encode(['success' => false, 'error' => 'ชื่อผู้ใช้ต้องมีอย่างน้อย 3 ตัวอักษร']);
    exit;
}
if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'error' => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'รูปแบบอีเมลไม่ถูกต้อง']);
    exit;
}

$hash = md5($password); // MD5 ตามที่ขอ
$stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
if ($stmt->execute([$username, $email, $hash])) {
    // สร้าง session หลังสมัครสมาชิกสำเร็จ
    session_start();
    $user_id = $pdo->lastInsertId();
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'เกิดข้อผิดพลาดในการสมัครสมาชิก']);
}
