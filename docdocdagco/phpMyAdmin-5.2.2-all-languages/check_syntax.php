<?php
// บังคับเปิดการแสดง Error ทุกรูปแบบในไฟล์นี้
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "กำลังตรวจสอบไวยากรณ์ไฟล์ vendor_config.php...<br><hr>";

// พยายามเรียกใช้ไฟล์ที่น่าสงสัย
include 'vendor_config.php';

// ถ้าโค้ดเดินทางมาถึงบรรทัดนี้ได้ แสดงว่าไม่มี Fatal Error หรือ Parse Error
echo "<hr>การตรวจสอบเสร็จสิ้น ถ้าคุณเห็นข้อความนี้ แสดงว่าไฟล์ vendor_config.php ไม่มีข้อผิดพลาดทางไวยากรณ์ที่ร้ายแรง";
?>