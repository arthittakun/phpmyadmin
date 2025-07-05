<?php
header('Content-Type: application/json');

// --- CONFIG ---
define('DA_URL', 'https://thsv3.hostatom.com:2222');
define('DA_USER', 'docdagco');
define('DA_PASS', 'Arthit0987944735');

// Debug logging function
function debug_log($message, $data = null) {
    $logFile = '../debug_delete.log';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $message";
    if ($data !== null) {
        $logEntry .= " | Data: " . json_encode($data);
    }
    $logEntry .= "\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

function da_api($endpoint, $params) {
    debug_log("DA API Call", ['endpoint' => $endpoint, 'params' => $params]);
    
    $ch = curl_init(DA_URL . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, DA_USER . ':' . DA_PASS);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    
    // Add verbose output to a temp stream for debugging
    $verbose = fopen('php://temp', 'w+');
    curl_setopt($ch, CURLOPT_STDERR, $verbose);
    
    $params['json'] = 'yes';
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlInfo = curl_getinfo($ch);
    $err = curl_error($ch);
    
    // Get verbose output
    rewind($verbose);
    $verboseLog = stream_get_contents($verbose);
    fclose($verbose);
    
    curl_close($ch);
    
    debug_log("cURL Response", [
        'http_code' => $httpCode,
        'response' => $response,
        'curl_info' => $curlInfo,
        'verbose_log' => $verboseLog,
        'curl_error' => $err
    ]);
    
    if ($err) {
        debug_log("cURL Error", $err);
        return ['success'=>false, 'error'=>'cURL Error: ' . $err];
    }
    
    if ($httpCode !== 200) {
        debug_log("HTTP Error", ['code' => $httpCode, 'response' => $response]);
        return ['success'=>false, 'error'=>'HTTP Error: ' . $httpCode, 'raw'=>$response, 'debug'=>$verboseLog];
    }
    
    $json = json_decode($response, true);
    if (!$json) {
        debug_log("JSON Decode Error", ['response' => $response]);
        return ['success'=>false, 'error'=>'Invalid JSON response', 'raw'=>$response];
    }
    
    // DirectAdmin returns different error formats
    if (isset($json['error']) && $json['error'] == '1') {
        $errorMsg = $json['text'] ?? $json['details'] ?? 'Unknown DirectAdmin error';
        debug_log("DirectAdmin Error", $json);
        return ['success'=>false, 'error'=>$errorMsg, 'raw'=>$json];
    }
    
    if (isset($json['success']) && $json['success'] === false) {
        debug_log("DirectAdmin Success False", $json);
        return ['success'=>false, 'error'=>$json['error'] ?? 'DirectAdmin operation failed', 'raw'=>$json];
    }
    
    debug_log("DA API Success", $json);
    return ['success'=>true, 'data'=>$json];
}

session_start();
require 'db.php';

debug_log("Delete Database Request Started", ['user_id' => $_SESSION['user_id'] ?? 'none']);

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$id = trim($data['id'] ?? ''); // UUID string
$db_name = $data['db_name'] ?? '';

debug_log("Input Data", ['id' => $id, 'db_name' => $db_name, 'user_id' => $user_id]);

// Validation
if (empty($id) || empty($db_name)) {
    debug_log("Validation Error - Missing Data");
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

// Verify ownership first
try {
    $stmt = $pdo->prepare('SELECT db_name FROM db_requests WHERE id=? AND user_id=?');
    $stmt->execute([$id, $user_id]);
    $record = $stmt->fetch();
    
    if (!$record) {
        debug_log("Database not found or not owned by user");
        echo json_encode(['success' => false, 'error' => 'Database not found or access denied']);
        exit;
    }
    
    // Use the database name from our records (this is the authoritative source)
    $db_name_from_record = $record['db_name'];
    debug_log("Database verified", ['record_db_name' => $db_name_from_record, 'input_db_name' => $db_name]);
} catch (Exception $e) {
    debug_log("Database query error", ['error' => $e->getMessage()]);
    echo json_encode(['success' => false, 'error' => 'Database error']);
    exit;
}

// Use the exact database name from our records for DirectAdmin
$da_db_name = $db_name_from_record;
// ไม่ต้องตัด prefix ออก ส่งชื่อเต็มไปเลย

debug_log("Database names", [
    'original_input' => $db_name, 
    'from_record' => $db_name_from_record,
    'for_da' => $da_db_name
]);

// Prepare DirectAdmin request parameters
$da_params = [
    'action' => 'delete',
    'select0' => $da_db_name
];

debug_log("Preparing DirectAdmin delete request", [
    'endpoint' => '/CMD_API_DATABASES',
    'params' => $da_params,
    'da_user' => DA_USER,
    'da_url' => DA_URL
]);

// Try to delete from DirectAdmin
$daRes = da_api('/CMD_API_DATABASES', $da_params);

debug_log("DirectAdmin delete result", [
    'success' => $daRes['success'],
    'error' => $daRes['error'] ?? null,
    'has_raw' => isset($daRes['raw']),
    'raw_length' => isset($daRes['raw']) ? strlen($daRes['raw']) : 0,
    'raw_preview' => isset($daRes['raw']) ? substr($daRes['raw'], 0, 500) : null
]);

if (!$daRes['success']) {
    $errorMsg = 'DirectAdmin: ' . ($daRes['error'] ?? 'Unknown error');
    debug_log("DirectAdmin delete failed", ['error' => $errorMsg, 'full_response' => $daRes]);
    
    // Parse the specific error from DirectAdmin
    $specificError = '';
    if (isset($daRes['raw'])) {
        $rawData = json_decode($daRes['raw'], true);
        if ($rawData && isset($rawData['error'])) {
            $specificError = $rawData['error'];
            if (isset($rawData['result'])) {
                $specificError .= ': ' . $rawData['result'];
            }
        }
    }
    
    // Return more detailed error for debugging
    echo json_encode([
        'success' => false, 
        'error' => $errorMsg . ($specificError ? ' - ' . $specificError : ''),
        'debug_info' => [
            'da_db_name' => $da_db_name,
            'record_db_name' => $db_name_from_record,
            'input_db_name' => $db_name,
            'http_code' => $daRes['http_code'] ?? null,
            'raw_response' => $daRes['raw'] ?? null,
            'verbose_log' => $daRes['debug'] ?? null,
            'specific_error' => $specificError
        ]
    ]);
    exit;
}

// Delete from local database
try {
    $stmt = $pdo->prepare('DELETE FROM db_requests WHERE id=? AND user_id=?');
    $stmt->execute([$id, $user_id]);
    
    debug_log("Local database record deleted successfully");
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    debug_log("Local database delete error", ['error' => $e->getMessage()]);
    echo json_encode(['success' => false, 'error' => 'Failed to delete local record']);
}
