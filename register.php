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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก - DBSQL Dashboard</title>
    <meta name="description" content="สมัครสมาชิก DBSQL Dashboard - ระบบจัดการฐานข้อมูลที่ทันสมัย">
    <meta name="keywords" content="register, database, mysql, management, dashboard">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    // Enhanced register function with validation
    async function register(event) {
        event.preventDefault();
        const form = event.target;
        const recaptcha = grecaptcha.getResponse();
        
        // Get form data
        const username = form.username.value.trim();
        const email = form.email.value.trim();
        const password = form.password.value;
        const confirmPassword = form.confirm_password.value;
        
        // Validation
        if (!username) {
            Swal.fire({
                title: 'ผิดพลาด!',
                text: 'กรุณาใส่ชื่อผู้ใช้',
                icon: 'warning',
                confirmButtonColor: '#10B981'
            });
            return;
        }
        
        if (username.length < 3) {
            Swal.fire({
                title: 'ผิดพลาด!',
                text: 'ชื่อผู้ใช้ต้องมีอย่างน้อย 3 ตัวอักษร',
                icon: 'warning',
                confirmButtonColor: '#10B981'
            });
            return;
        }
        
        if (!email) {
            Swal.fire({
                title: 'ผิดพลาด!',
                text: 'กรุณาใส่อีเมล',
                icon: 'warning',
                confirmButtonColor: '#10B981'
            });
            return;
        }
        
        if (!password) {
            Swal.fire({
                title: 'ผิดพลาด!',
                text: 'กรุณาใส่รหัสผ่าน',
                icon: 'warning',
                confirmButtonColor: '#10B981'
            });
            return;
        }
        
        if (password.length < 6) {
            Swal.fire({
                title: 'ผิดพลาด!',
                text: 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร',
                icon: 'warning',
                confirmButtonColor: '#10B981'
            });
            return;
        }
        
        if (password !== confirmPassword) {
            Swal.fire({
                title: 'ผิดพลาด!',
                text: 'รหัสผ่านไม่ตรงกัน',
                icon: 'warning',
                confirmButtonColor: '#10B981'
            });
            return;
        }
        
        if (!recaptcha) {
            Swal.fire({
                title: 'ผิดพลาด!',
                text: 'กรุณายืนยัน reCAPTCHA',
                icon: 'warning',
                confirmButtonColor: '#10B981'
            });
            return;
        }
        
        // Show loading
        Swal.fire({
            title: 'กำลังสมัครสมาชิก...',
            text: 'กรุณารอสักครู่',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        const data = {
            username,
            email,
            password,
            confirm_password: confirmPassword,
            recaptcha
        };
        
        try {
            const res = await fetch('api/api_register.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await res.json();
            
            if (result.success) {
                Swal.fire({
                    title: 'สำเร็จ!',
                    text: 'สมัครสมาชิกสำเร็จ กำลังเปลี่ยนหน้า...',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    confirmButtonColor: '#10B981'
                }).then(() => {
                    window.location = '/';
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
<body class="bg-gradient-to-br from-green-50 to-emerald-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all duration-300 hover:shadow-3xl">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="bg-gradient-to-br from-green-100 to-green-200 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                <i class="fas fa-user-plus text-green-600 text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">สมัครสมาชิก</h1>
            <p class="text-gray-600">DBSQL Dashboard</p>
        </div>

        <!-- Register Form -->
        <form onsubmit="register(event)" class="space-y-6">
            <div class="group">
                <label class="block text-sm font-medium text-gray-700 mb-2 transition-colors duration-200 group-focus-within:text-green-600">
                    <i class="fas fa-user mr-2"></i>ชื่อผู้ใช้
                </label>
                <input type="text" name="username" required minlength="3"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 hover:border-gray-400"
                       placeholder="ใส่ชื่อผู้ใช้ (อย่างน้อย 3 ตัวอักษร)">
            </div>
            
            <div class="group">
                <label class="block text-sm font-medium text-gray-700 mb-2 transition-colors duration-200 group-focus-within:text-green-600">
                    <i class="fas fa-envelope mr-2"></i>อีเมล
                </label>
                <input type="email" name="email" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 hover:border-gray-400"
                       placeholder="ใส่อีเมล">
            </div>
            
            <div class="group">
                <label class="block text-sm font-medium text-gray-700 mb-2 transition-colors duration-200 group-focus-within:text-green-600">
                    <i class="fas fa-lock mr-2"></i>รหัสผ่าน
                </label>
                <input type="password" name="password" required minlength="6"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 hover:border-gray-400"
                       placeholder="ใส่รหัสผ่าน (อย่างน้อย 6 ตัวอักษร)">
            </div>
            
            <div class="group">
                <label class="block text-sm font-medium text-gray-700 mb-2 transition-colors duration-200 group-focus-within:text-green-600">
                    <i class="fas fa-lock mr-2"></i>ยืนยันรหัสผ่าน
                </label>
                <input type="password" name="confirm_password" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 hover:border-gray-400"
                       placeholder="ยืนยันรหัสผ่าน">
            </div>
            
            <div class="flex justify-center py-2">
                <div class="g-recaptcha" data-sitekey="6Lc7pnIrAAAAAE4NB4_iMW8ZwwCBN6itP7nXJ7Kf"></div>
            </div>
            
            <button type="submit" 
                    class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white py-3 rounded-lg transition duration-200 font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <i class="fas fa-user-plus mr-2"></i>สมัครสมาชิก
            </button>
        </form>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-gray-600">
                มีบัญชีอยู่แล้ว? 
                <a href="/" class="text-green-600 hover:text-green-700 font-medium transition-colors duration-200 hover:underline">เข้าสู่ระบบ</a>
            </p>
        </div>
    </div>

    <!-- Background Animation -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-40 -right-32 w-80 h-80 bg-green-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
        <div class="absolute -bottom-40 -left-32 w-80 h-80 bg-emerald-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
        <div class="absolute top-40 left-40 w-80 h-80 bg-teal-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
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
</body>
</html>
