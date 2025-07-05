<?php
header('Content-Type: application/json');

// Generate UUID v4
function generateUuid() {
    $data = random_bytes(16);
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // set version to 0100
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // set bits 6-7 to 10
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

// --- CONFIG ---
define('DA_URL', 'https://thsv3.hostatom.com:2222');
define('DA_USER', 'docdagco');
define('DA_PASS', 'Arthit0987944735');

function da_api($endpoint, $params) {
    $ch = curl_init(DA_URL . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, DA_USER . ':' . DA_PASS);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $params['json'] = 'yes';
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    
    if ($err) {
        return ['success'=>false, 'error'=>'cURL Error: ' . $err];
    }
    
    if ($httpCode !== 200) {
        return ['success'=>false, 'error'=>'HTTP Error: ' . $httpCode, 'raw'=>$response];
    }
    
    $json = json_decode($response, true);
    if (!$json) {
        return ['success'=>false, 'error'=>'Invalid JSON response', 'raw'=>$response];
    }
    // --- รองรับ response ทั้งสองแบบ ---
    if (isset($json['error'])) {
        // กรณี error จาก DirectAdmin
        $msg = $json['error'];
        if (isset($json['result'])) {
            $msg .= ' : ' . $json['result'];
        }
        return ['success'=>false, 'error'=>$msg, 'raw'=>$json];
    }
    if (isset($json['success']) && $json['success'] === 'Database Created') {
        // สำเร็จแบบ string
        return ['success'=>true, 'data'=>$json];
    }
    // fallback: ถ้า success เป็น true (boolean)
    if (isset($json['success']) && $json['success'] === true) {
        return ['success'=>true, 'data'=>$json];
    }
    // fallback: ถ้าไม่มี error/success ให้ถือว่าสำเร็จถ้าไม่มี error
    return ['success'=>true, 'data'=>$json];
}
session_start();
require 'db.php';
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}
$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$id = generateUuid(); // Generate new UUID
// ตรวจสอบข้อมูลพื้นฐาน
$db_name = trim($data['db_name'] ?? '');
$db_user = trim($data['db_user'] ?? '');
$db_pass = $data['db_pass'] ?? '';
if (empty($db_name) || empty($db_user) || empty($db_pass)) {
    echo json_encode(['success' => false, 'error' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
    exit;
}
if (strlen($db_name) < 3 || strlen($db_user) < 3) {
    echo json_encode(['success' => false, 'error' => 'ชื่อฐานข้อมูลและชื่อผู้ใช้ต้องมีอย่างน้อย 3 ตัวอักษร']);
    exit;
}
if (strlen($db_pass) < 6) {
    echo json_encode(['success' => false, 'error' => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร']);
    exit;
}
if (!preg_match('/^[a-zA-Z0-9_]+$/', $db_name) || !preg_match('/^[a-zA-Z0-9_]+$/', $db_user)) {
    echo json_encode(['success' => false, 'error' => 'ชื่อฐานข้อมูลและชื่อผู้ใช้ใช้ได้เฉพาะตัวอักษร ตัวเลข และ _']);
    exit;
}
// ตรวจสอบ reCAPTCHA
$recaptcha = $data['recaptcha'] ?? '';
if (!$recaptcha) {
    echo json_encode(['success' => false, 'error' => 'กรุณายืนยัน reCAPTCHA']);
    exit;
}
$secret = '6Lc7pnIrAAAAAPbAXGgo3RhLkq7UK8Gfcojw7QUJ';
$verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=" . $recaptcha);
$captcha_success = json_decode($verify, true);
if (empty($captcha_success['success'])) {
    echo json_encode(['success' => false, 'error' => 'reCAPTCHA ไม่ถูกต้อง']);
    exit;
}
// ตัด prefix docdagco_ ออกก่อนส่งไป DirectAdmin
$da_db_user = $db_user;
$da_db_name = $db_name;
if (strpos($db_user, DA_USER . '_') === 0) {
    $da_db_user = substr($db_user, strlen(DA_USER . '_'));
}
if (strpos($db_name, DA_USER . '_') === 0) {
    $da_db_name = substr($db_name, strlen(DA_USER . '_'));
}
// เตรียมชื่อสำหรับบันทึก local DB ด้วย prefix
if (strpos($db_user, DA_USER . '_') !== 0) {
    $db_user_save = DA_USER . '_' . $db_user;
} else {
    $db_user_save = $db_user;
}
if (strpos($db_name, DA_USER . '_') !== 0) {
    $db_name_save = DA_USER . '_' . $db_name;
} else {
    $db_name_save = $db_name;
}
// ตรวจสอบจำนวนฐานข้อมูลของ user
$stmt = $pdo->prepare('SELECT COUNT(*) FROM db_requests WHERE user_id = ?');
$stmt->execute([$user_id]);
$count = $stmt->fetchColumn();
if($_SESSION["user_id"] !== 5){
    if ($count >= 5) {
        echo json_encode(['success' => false, 'error' => 'คุณมีฐานข้อมูลครบ 5 รายการแล้ว']);
        exit;
    }
}

// ตรวจสอบชื่อซ้ำใน user เดียวกัน
$stmt = $pdo->prepare('SELECT COUNT(*) FROM db_requests WHERE user_id = ? AND db_name = ?');
$stmt->execute([$user_id, $db_name_save]);
if ($stmt->fetchColumn() > 0) {
    echo json_encode(['success' => false, 'error' => 'คุณมีฐานข้อมูลนี้อยู่แล้ว']);
    exit;
}
// --- เช็คครบแล้วจึงค่อยสร้างใน DirectAdmin ---
$daRes = da_api('/CMD_API_DATABASES', [
    'action' => 'create',
    'name'   => $db_name, // ใช้ชื่อที่มี prefix docdagco_
    'user'   => $db_user, // ใช้ชื่อที่มี prefix docdagco_
    'passwd' => $db_pass,
    'passwd2'=> $db_pass,
    'json'   => 'yes'
]);

if (!$daRes['success']) {
    // Log error for debugging
    error_log('DirectAdmin API Error: ' . json_encode($daRes));
    $daErrorMsg = $daRes['error'] ?? 'Unknown error';
    // ถ้ามีข้อความ result จาก DirectAdmin ให้แสดงด้วย
    if (isset($daRes['raw']['result'])) {
        $daErrorMsg .= ' : ' . $daRes['raw']['result'];
    } elseif (isset($daRes['raw'])) {
        // ถ้า raw เป็น string JSON ให้ลอง decode
        $raw = $daRes['raw'];
        if (is_string($raw)) {
            $rawArr = json_decode($raw, true);
            if (isset($rawArr['result'])) {
                $daErrorMsg .= ' : ' . $rawArr['result'];
            }
        }
    }
    echo json_encode([
        'success' => false, 
        'error' => $daErrorMsg,
        'debug' => $db_pass . "\n" . $db_name_save . "\n" .  $db_user_save . "\n" . json_encode($daRes, JSON_PRETTY_PRINT)
    ]);
    exit;
}

// --- Set allowed host to % for the new DB user ---
$setHostSuccess = true;
$setHostError = '';
$apiUser = $db_user_save; // ชื่อ user เต็ม (มี prefix)

$apiUrl = DA_URL . "/api/db-manage/users/{$apiUser}/change-hosts";
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, DA_USER . ':' . DA_PASS);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'accept: application/json'
]);
$hostsBody = json_encode(["%" , "localhost"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $hostsBody);
$setHostResponse = curl_exec($ch);
$setHostHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$setHostErr = curl_error($ch);
curl_close($ch);
if ($setHostErr || $setHostHttpCode >= 400) {
    $setHostSuccess = false;
    $setHostError = $setHostErr ?: $setHostResponse;
}

// แต่บันทึก local DB ด้วยชื่อที่เติม prefix
$stmt = $pdo->prepare('INSERT INTO db_requests (id, user_id, db_name, db_username, db_password) VALUES (?, ?, ?, ?, ?)');
$stmt->execute([$id, $user_id, $db_name_save, $db_user_save, $db_pass]);

echo json_encode([
    'success' => true,
    'set_host_success' => $setHostSuccess,
    'set_host_error' => $db_pass
]);
?>
