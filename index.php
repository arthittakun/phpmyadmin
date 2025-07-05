<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard");
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no" />

  <title>เข้าสู่ระบบ - DBSQL Dashboard</title>

  <!-- SEO Tags -->
  <meta name="description" content="เข้าสู่ระบบ DBSQL Dashboard - ระบบจัดการฐานข้อมูล MySQL ออนไลน์ ใช้งานง่าย ปลอดภัย เหมาะสำหรับนักพัฒนาและผู้ดูแลเว็บไซต์ทุกระดับ" />
  <meta name="keywords" content="database, mysql, dashboard, ฟรี, phpmyadmin, dbsql, hosting, webapp" />
  <meta name="author" content="DBSQL Team" />
  <link rel="canonical" href="https://freesql.docdag.com/" />

  <!-- Open Graph (Facebook / Discord / LINE) -->
  <meta property="og:type" content="website" />
  <meta property="og:site_name" content="DBSQL" />
  <meta property="og:url" content="https://freesql.docdag.com/" />
  <meta property="og:title" content="เข้าสู่ระบบ - DBSQL Dashboard" />
  <meta property="og:description" content="จัดการฐานข้อมูล MySQL ออนไลน์ได้ฟรี ใช้งานง่าย ปลอดภัย พร้อมแดชบอร์ดทันสมัย" />
  <meta property="og:image" content="https://freesql.docdag.com/assets/seo-card.png" />
  <meta property="og:image:type" content="image/png" />
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="630" />
  <meta property="og:locale" content="th_TH" />

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="เข้าสู่ระบบ - DBSQL Dashboard" />
  <meta name="twitter:description" content="จัดการฐานข้อมูล MySQL ออนไลน์ได้ฟรี ใช้งานง่าย ปลอดภัย พร้อมแดชบอร์ดทันสมัย" />
  <meta name="twitter:image" content="https://freesql.docdag.com/assets/seo-card.png" />
  <meta name="twitter:site" content="@dbsql" />
  <meta name="twitter:creator" content="@dbsql" />
  <meta name="twitter:url" content="https://freesql.docdag.com/" />
  <meta property="twitter:url" content="https://freesql.docdag.com/" />
  <meta property="twitter:title" content="เข้าสู่ระบบ - DBSQL Dashboard" />
  <meta property="twitter:description" content="จัดการฐานข้อมูล MySQL ออนไลน์ได้ฟรี ใช้งานง่าย ปลอดภัย พร้อมแดชบอร์ดทันสมัย" />
  <meta property="twitter:image" content="https://freesql.docdag.com/assets/seo-card.png" />
  <meta property="twitter:card" content="summary_large_image" />

  <!-- Favicon -->
  <link rel="icon" href="/assets/dbsql-cover.png" type="image/png" />
  <link rel="apple-touch-icon" sizes="180x180" href="/assets/dbsql-cover.png" />

  <!-- Preload Cover -->
  <link rel="preload" href="https://freesql.docdag.com/assets/dbsql-cover.png" as="image" />

  <!-- CSS & JS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    // Enhanced login function with validation
    async function login(event) {
        event.preventDefault();
        const form = event.target;
        const recaptcha = grecaptcha.getResponse();
        
        // Validation
        const user = form.user.value.trim();
        const password = form.password.value;
        
        if (!user) {
            Swal.fire({
                title: 'ผิดพลาด!',
                text: 'กรุณาใส่ชื่อผู้ใช้หรืออีเมล',
                icon: 'warning',
                confirmButtonColor: '#3B82F6'
            });
            return;
        }
        
        if (!password) {
            Swal.fire({
                title: 'ผิดพลาด!',
                text: 'กรุณาใส่รหัสผ่าน',
                icon: 'warning',
                confirmButtonColor: '#3B82F6'
            });
            return;
        }
        
        if (!recaptcha) {
            Swal.fire({
                title: 'ผิดพลาด!',
                text: 'กรุณายืนยัน reCAPTCHA',
                icon: 'warning',
                confirmButtonColor: '#3B82F6'
            });
            return;
        }
        
        // Show loading
        Swal.fire({
            title: 'กำลังเข้าสู่ระบบ...',
            text: 'กรุณารอสักครู่',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        const data = {
            user: user,
            password: password,
            recaptcha
        };
        
        try {
            const res = await fetch('api/api_login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await res.json();
            
            if (result.success) {
                Swal.fire({
                    title: 'สำเร็จ!',
                    text: 'เข้าสู่ระบบสำเร็จ กำลังเปลี่ยนหน้า...',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    confirmButtonColor: '#10B981'
                }).then(() => {
                    window.location = 'dashboard';
                });
            } else {
                Swal.fire({
                    title: 'ผิดพลาด!',
                    text: result.error || 'เกิดข้อผิดพลาด',
                    icon: 'error',
                    confirmButtonColor: '#EF4444'
                });
                grecaptcha.reset();
            }
        } catch (error) {
            Swal.fire({
                title: 'ผิดพลาด!',
                text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ',
                icon: 'error',
                confirmButtonColor: '#EF4444'
            });
            grecaptcha.reset();
        }
    }
    </script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all duration-300 hover:shadow-3xl">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="bg-gradient-to-br from-blue-100 to-blue-200 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                <i class="fas fa-database text-blue-600 text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">เข้าสู่ระบบ</h1>
            <p class="text-gray-600">DBSQL Dashboard</p>
        </div>

        <!-- Login Form -->
        <form onsubmit="login(event)" class="space-y-6">
            <div class="group">
                <label class="block text-sm font-medium text-gray-700 mb-2 transition-colors duration-200 group-focus-within:text-blue-600">
                    <i class="fas fa-user mr-2"></i>ชื่อผู้ใช้หรืออีเมล
                </label>
                <input type="text" name="user" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 hover:border-gray-400"
                       placeholder="ใส่ชื่อผู้ใช้หรืออีเมล">
            </div>
            
            <div class="group">
                <label class="block text-sm font-medium text-gray-700 mb-2 transition-colors duration-200 group-focus-within:text-blue-600">
                    <i class="fas fa-lock mr-2"></i>รหัสผ่าน
                </label>
                <input type="password" name="password" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 hover:border-gray-400"
                       placeholder="ใส่รหัสผ่าน">
            </div>
            
            <div class="flex justify-center py-2">
                <div class="g-recaptcha" data-sitekey="6Lc7pnIrAAAAAE4NB4_iMW8ZwwCBN6itP7nXJ7Kf"></div>
            </div>
            
            <button type="submit" 
                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white py-3 rounded-lg transition duration-200 font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <i class="fas fa-sign-in-alt mr-2"></i>เข้าสู่ระบบ
            </button>
        </form>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-gray-600">
                ยังไม่มีบัญชี? 
                <a href="register" class="text-blue-600 hover:text-blue-700 font-medium transition-colors duration-200 hover:underline">สมัครสมาชิก</a>
            </p>
            <a href="https://freesql.docdag.com/docdocdagco/phpMyAdmin-5.2.2-all-languages/" target="_blank">
  เข้าสู่ระบบจัดการฐานข้อมูล
</a>
        </div>
    </div>

    <!-- Background Animation -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-40 -right-32 w-80 h-80 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
        <div class="absolute -bottom-40 -left-32 w-80 h-80 bg-indigo-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
        <div class="absolute top-40 left-40 w-80 h-80 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
    </div>

    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <!-- Cookie Consent Banner -->
    <div id="cookieConsent" class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-2xl z-50 transform translate-y-full transition-transform duration-500 ease-in-out">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-4 sm:space-y-0 sm:space-x-4">
                <div class="flex items-start space-x-3 flex-1">
                    <div class="bg-blue-100 p-2 rounded-full flex-shrink-0">
                        <i class="fas fa-cookie-bite text-blue-600 text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-gray-900 mb-1">เราใช้คุกกี้เพื่อปรับปรุงประสบการณ์ของคุณ</h3>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            เว็บไซต์นี้ใช้คุกกี้เพื่อให้แน่ใจว่าคุณได้รับประสบการณ์ที่ดีที่สุดในเว็บไซต์ของเรา และเพื่อวิเคราะห์การใช้งาน 
                            <a href="#" class="text-blue-600 hover:text-blue-700 underline">อ่านเพิ่มเติม</a>
                        </p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 w-full sm:w-auto">
                    <button onclick="manageCookies()" 
                            class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition duration-200 border border-gray-300">
                        <i class="fas fa-cog mr-1"></i>จัดการคุกกี้
                    </button>
                    <button onclick="acceptAllCookies()" 
                            class="w-full sm:w-auto px-6 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg transition duration-200 shadow-md hover:shadow-lg">
                        <i class="fas fa-check mr-1"></i>ยอมรับทั้งหมด
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cookie Preferences Modal -->
    <div id="cookieModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-shield-alt text-blue-600 mr-2"></i>
                        การตั้งค่าความเป็นส่วนตัว
                    </h2>
                    <button onclick="closeCookieModal()" class="text-gray-400 hover:text-gray-600 p-1">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                <p class="text-gray-600 text-sm leading-relaxed">
                    เราใช้คุกกี้เพื่อปรับปรุงประสบการณ์การใช้งาน วิเคราะห์การเข้าชม และให้บริการที่ดีขึ้น คุณสามารถเลือกประเภทคุกกี้ที่ต้องการได้
                </p>
                
                <!-- Essential Cookies -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            <h3 class="font-semibold text-gray-900">คุกกี้ที่จำเป็น</h3>
                            <span class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">จำเป็น</span>
                        </div>
                        <div class="w-12 h-6 bg-green-500 rounded-full relative cursor-not-allowed opacity-60">
                            <div class="w-5 h-5 bg-white rounded-full absolute top-0.5 right-0.5 transition-transform"></div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600">
                        คุกกี้เหล่านี้จำเป็นสำหรับการทำงานของเว็บไซต์ เช่น การเข้าสู่ระบบ การรักษาความปลอดภัย ไม่สามารถปิดได้
                    </p>
                </div>

                <!-- Analytics Cookies -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-gray-900">คุกกี้การวิเคราะห์</h3>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="analyticsCookies" class="sr-only peer" checked>
                            <div class="w-12 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-6 peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    <p class="text-sm text-gray-600">
                        ช่วยให้เราเข้าใจวิธีการใช้งานเว็บไซต์ เพื่อปรับปรุงประสบการณ์และประสิทธิภาพ
                    </p>
                </div>

                <!-- Marketing Cookies -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-gray-900">คุกกี้การตลาด</h3>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="marketingCookies" class="sr-only peer">
                            <div class="w-12 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-6 peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    <p class="text-sm text-gray-600">
                        ใช้เพื่อแสดงโฆษณาที่เกี่ยวข้องกับความสนใจของคุณ และวัดประสิทธิภาพแคมเปญ
                    </p>
                </div>
            </div>
            
            <div class="p-6 border-t border-gray-200 flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                <button onclick="saveAndAcceptSelected()" 
                        class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-lg font-medium transition duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-save mr-2"></i>บันทึกการตั้งค่า
                </button>
                <button onclick="acceptAllCookiesModal()" 
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200">
                    <i class="fas fa-check mr-2"></i>ยอมรับทั้งหมด
                </button>
            </div>
        </div>
    </div>

    <script>
    // Cookie Consent Management
    function checkCookieConsent() {
        const consent = localStorage.getItem('cookieConsent');
        if (!consent) {
            setTimeout(() => {
                document.getElementById('cookieConsent').style.transform = 'translateY(0)';
            }, 2000); // Show after 2 seconds
        }
    }

    function acceptAllCookies() {
        const consent = {
            essential: true,
            analytics: true,
            marketing: true,
            timestamp: Date.now()
        };
        localStorage.setItem('cookieConsent', JSON.stringify(consent));
        hideCookieBanner();
        
        // Initialize tracking codes here
        initializeAnalytics();
        initializeMarketing();
        
        Swal.fire({
            title: 'บันทึกแล้ว!',
            text: 'การตั้งค่าคุกกี้ของคุณได้รับการบันทึกแล้ว',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
    }

    function manageCookies() {
        document.getElementById('cookieModal').classList.remove('hidden');
        
        // Load current preferences
        const consent = JSON.parse(localStorage.getItem('cookieConsent') || '{}');
        document.getElementById('analyticsCookies').checked = consent.analytics !== false;
        document.getElementById('marketingCookies').checked = consent.marketing === true;
    }

    function closeCookieModal() {
        document.getElementById('cookieModal').classList.add('hidden');
    }

    function saveAndAcceptSelected() {
        const consent = {
            essential: true,
            analytics: document.getElementById('analyticsCookies').checked,
            marketing: document.getElementById('marketingCookies').checked,
            timestamp: Date.now()
        };
        
        localStorage.setItem('cookieConsent', JSON.stringify(consent));
        
        if (consent.analytics) initializeAnalytics();
        if (consent.marketing) initializeMarketing();
        
        hideCookieBanner();
        closeCookieModal();
        
        Swal.fire({
            title: 'บันทึกแล้ว!',
            text: 'การตั้งค่าคุกกี้ของคุณได้รับการบันทึกแล้ว',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
    }

    function acceptAllCookiesModal() {
        document.getElementById('analyticsCookies').checked = true;
        document.getElementById('marketingCookies').checked = true;
        saveAndAcceptSelected();
    }

    function hideCookieBanner() {
        const banner = document.getElementById('cookieConsent');
        banner.style.transform = 'translateY(100%)';
        setTimeout(() => {
            banner.style.display = 'none';
        }, 500);
    }

    function initializeAnalytics() {
        // Initialize Google Analytics or other analytics here
        console.log('Analytics initialized');
        
        // Example: Google Analytics 4
        // gtag('config', 'GA_MEASUREMENT_ID');
    }

    function initializeMarketing() {
        // Initialize marketing cookies here
        console.log('Marketing cookies initialized');
        
        // Example: Facebook Pixel, Google Ads, etc.
    }

    // Check consent on page load
    document.addEventListener('DOMContentLoaded', checkCookieConsent);
    </script>
</body>
</html>

<!--
หมายเหตุ: ตรวจสอบให้แน่ใจว่าไฟล์ https://freesql.docdag.com/assets/dbsql-cover.png และ https://freesql.docdag.com/assets/dbsql-favicon.png มีอยู่จริงและ public
หากแก้ไขแล้วยังไม่แสดงบน Facebook/LINE ให้ใช้ Sharing Debugger (https://developers.facebook.com/tools/debug/) แล้วกด Scrape Again
-->

