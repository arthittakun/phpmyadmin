<?php
declare(strict_types=1);

$i = 1;

/**
 * Authentication type
 * เปลี่ยนเป็น 'signon' เพื่อเปิดใช้ SSO
 */
$cfg['Servers'][$i]['auth_type'] = 'signon';

/**
 * Server parameters
 */
$cfg['Servers'][$i]['host'] = 'localhost';
$cfg['Servers'][$i]['compress'] = false;
$cfg['Servers'][$i]['AllowNoPassword'] = false;

/**
 * Signon settings
 */
// URL หน้าล็อกอินของเว็บคุณ (หากผู้ใช้ยังไม่ล็อกอิน)
$cfg['Servers'][$i]['SignonURL'] = 'https://freesql.docdag.com/login.php';

// (สำคัญมาก) ชื่อ Session ของเว็บแอปพลิเคชันหลักของคุณ
$cfg['Servers'][$i]['SignonSession'] = 'PHPSESSID';

// Path เต็มไปยังสคริปต์ signon ที่เราจะสร้างกันในขั้นตอนถัดไป
$cfg['Servers'][$i]['SignonScript'] = '/home/docdagco/domains/freesql.docdag.com/public_html/docdocdagco/phpMyAdmin-5.2.2-all-languages/vendor_config.php';

?>