<?php
$host = 'sql.docdag.com';     
$db   = 'docdagco_historyDB';  
$user = 'docdagco_historyDB';    
$pass = 'Arthit0987944735';      
$charset = 'utf8mb4';   

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // ให้ throw exception เมื่อ error
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // คืนค่าผลลัพธ์เป็น associative array
    PDO::ATTR_EMULATE_PREPARES   => false,                   // ใช้ prepared statement จริง
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo "การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $e->getMessage();
}
?>
