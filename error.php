<?php
$error_code = $_GET['code'] ?? '500';
$error_message = $_GET['message'] ?? 'เกิดข้อผิดพลาดในระบบ';

$error_details = [
    '404' => [
        'title' => 'ไม่พบหน้าที่ต้องการ',
        'message' => 'หน้าที่คุณต้องการเข้าถึงไม่มีอยู่ในระบบ',
        'icon' => 'fas fa-search',
        'color' => 'blue'
    ],
    '403' => [
        'title' => 'ไม่มีสิทธิ์เข้าถึง',
        'message' => 'คุณไม่มีสิทธิ์ในการเข้าถึงหน้านี้',
        'icon' => 'fas fa-lock',
        'color' => 'red'
    ],
    '500' => [
        'title' => 'ข้อผิดพลาดของเซิร์ฟเวอร์',
        'message' => 'เกิดข้อผิดพลาดภายในของเซิร์ฟเวอร์',
        'icon' => 'fas fa-exclamation-triangle',
        'color' => 'yellow'
    ]
];

$current_error = $error_details[$error_code] ?? $error_details['500'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อผิดพลาด <?php echo $error_code; ?> - DBSQL Dashboard</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md mx-4 text-center">
        <!-- Error Icon -->
        <div class="bg-<?php echo $current_error['color']; ?>-100 w-32 h-32 rounded-full flex items-center justify-center mx-auto mb-8 shadow-lg">
            <i class="<?php echo $current_error['icon']; ?> text-<?php echo $current_error['color']; ?>-600 text-6xl"></i>
        </div>
        
        <!-- Error Code -->
        <h1 class="text-6xl font-bold text-gray-800 mb-4"><?php echo $error_code; ?></h1>
        
        <!-- Error Title -->
        <h2 class="text-2xl font-semibold text-gray-700 mb-4"><?php echo $current_error['title']; ?></h2>
        
        <!-- Error Message -->
        <p class="text-gray-600 mb-8 leading-relaxed">
            <?php echo htmlspecialchars($error_message ?: $current_error['message']); ?>
        </p>
        
        <!-- Action Buttons -->
        <div class="space-y-4">
            <button onclick="history.back()" 
                    class="w-full bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <i class="fas fa-arrow-left mr-2"></i>กลับหน้าก่อนหน้า
            </button>
            
            <a href="dashboard" 
               class="block w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <i class="fas fa-home mr-2"></i>กลับหน้าหลัก
            </a>
        </div>
        
        <!-- Footer -->
        <div class="mt-8 pt-8 border-t border-gray-200">
            <p class="text-sm text-gray-500">
                หากปัญหายังคงมีอยู่ กรุณาติดต่อผู้ดูแลระบบ
            </p>
        </div>
    </div>

    <!-- Background Animation -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-40 -right-32 w-80 h-80 bg-<?php echo $current_error['color']; ?>-300 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob"></div>
        <div class="absolute -bottom-40 -left-32 w-80 h-80 bg-gray-300 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob animation-delay-2000"></div>
        <div class="absolute top-40 left-40 w-80 h-80 bg-<?php echo $current_error['color']; ?>-400 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-4000"></div>
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
</body>
</html>
