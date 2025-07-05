<?php
header('Content-Type: application/json');

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
    
    // DirectAdmin returns different error formats
    if (isset($json['error']) && $json['error'] == '1') {
        $errorMsg = $json['text'] ?? $json['details'] ?? 'Unknown DirectAdmin error';
        return ['success'=>false, 'error'=>$errorMsg, 'raw'=>$json];
    }
    
    if (isset($json['success']) && $json['success'] === false) {
        return ['success'=>false, 'error'=>$json['error'] ?? 'DirectAdmin operation failed', 'raw'=>$json];
    }
    
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
$id = trim($data['id'] ?? ''); // UUID string
$db_pass = $data['db_pass'] ?? '';

// Validation
if (empty($id) || empty($db_pass)) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

// Get the existing database record to get the username
$stmt = $pdo->prepare('SELECT db_username, db_name FROM db_requests WHERE id=? AND user_id=?');
$stmt->execute([$id, $user_id]);
$dbRecord = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$dbRecord) {
    echo json_encode(['success' => false, 'error' => 'Database record not found']);
    exit;
}

$db_user = $dbRecord['db_username'];

// ไม่ต้องตัด prefix docdagco_ ออก ส่งชื่อเต็มไปเลย
$da_db_user = $db_user;

$daRes = da_api('/CMD_API_DB_USER', [
    'action' => 'modify',
    'user'   => $da_db_user,
    'passwd' => $db_pass,
    'passwd2'=> $db_pass
]);

if (!$daRes['success']) {
    // Log error for debugging
    error_log('DirectAdmin API Edit Error: ' . json_encode($daRes));
    echo json_encode([
        'success' => false, 
        'error' => 'DirectAdmin: ' . ($daRes['error'] ?? 'Unknown error'),
        'debug' => $daRes // Remove this in production
    ]);
    exit;
}

// แต่บันทึก local DB ด้วยรหัสผ่านใหม่
$stmt = $pdo->prepare('UPDATE db_requests SET db_password=? WHERE id=? AND user_id=?');
$stmt->execute([$db_pass, $id, $user_id]);
echo json_encode(['success' => true]);
