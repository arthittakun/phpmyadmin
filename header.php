<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 min-h-screen">
<!-- Navbar -->
<nav class="bg-white shadow-lg border-b border-gray-200 sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-16">
      <!-- Logo Section -->
      <div class="flex items-center flex-shrink-0">
        <div class="flex items-center">
          <div class="bg-blue-100 p-2 rounded-lg mr-3">
            <i class="fas fa-database text-blue-600 text-xl"></i>
          </div>
          <div class="hidden sm:block">
            <h1 class="text-xl font-bold text-gray-900">DBSQL Dashboard</h1>
            <p class="text-xs text-gray-500">จัดการฐานข้อมูล</p>
          </div>
          <h1 class="text-lg font-bold text-gray-900 sm:hidden">DBSQL</h1>
        </div>
      </div>
      
      <!-- User Section -->
      <div class="flex items-center space-x-2 sm:space-x-4">
        <!-- User Info -->
        <div class="flex items-center text-sm text-gray-700">
          <div class="bg-gray-100 p-2 rounded-lg mr-2">
            <i class="fas fa-user-circle text-gray-600"></i>
          </div>
          <div class="hidden sm:block">
            <span class="font-medium block"><?=$_SESSION['username']?></span>
            <span class="text-xs text-gray-500">ผู้ใช้งาน</span>
          </div>
        </div>
        
        <!-- Logout Button -->
        <a href="logout" 
           class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 sm:px-4 rounded-lg transition duration-200 text-sm font-medium shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
          <i class="fas fa-sign-out-alt mr-1 sm:mr-2"></i>
          <span class="hidden sm:inline">ออกจากระบบ</span>
          <span class="sm:hidden">ออก</span>
        </a>
      </div>
    </div>
  </div>
</nav>
