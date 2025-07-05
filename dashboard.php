<?php
session_start();

if (empty($_SESSION['user_id'])) {
    echo '<script>window.location = "/";</script>';
    exit;
}
include 'header.php';
?>

    <script>
    // --- Local DB CRUD (fetchList, etc) ---
    async function fetchList() {
        const res = await fetch('api/api_db_list.php');
        const data = await res.json();
        if (data.success) renderCards(data.rows);
    }
    
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
        grecaptcha.reset();
    }
    
    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
        document.getElementById('createForm').reset();
    }
    
    function openEditModal(id, db_name, db_user, db_password) {
        document.getElementById('editModal').classList.remove('hidden');
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_db_name').value = db_name;
        document.getElementById('edit_db_user').value = db_user;
        document.getElementById('edit_db_pass').value = db_password;
        document.getElementById('edit_db_pass_confirm').value = '';
    }
    
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.getElementById('editForm').reset();
    }
    
    function showConnectionInfo(db_name, db_user, db_password) {
        const connectionCode = `&lt;?php\n$host = 'sql.docdag.com';\n$db   = '${db_name}';\n$user = '${db_user}';\n$pass = '${db_password}';\n$charset = 'utf8mb4';\n\n$dsn = "mysql:host=$host;dbname=$db;charset=$charset";\n$options = [\n    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,\n    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,\n    PDO::ATTR_EMULATE_PREPARES   => false,\n];\n\ntry {\n    $pdo = new PDO($dsn, $user, $pass, $options);\n    // Success!\n} catch (PDOException $e) {\n    // Handle error\n    die('Connection failed: ' . $e->getMessage());\n}\n?&gt;`;

        Swal.fire({
            title: '🔗 ข้อมูลการเชื่อมต่อฐานข้อมูล',
            html: `
                <div class="text-left">
                    <div class="mb-6">
                        <h4 class="font-bold mb-3 text-lg text-gray-800 flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>ข้อมูลการเชื่อมต่อ:
                        </h4>
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg border border-blue-200 text-sm">
                            <div class="grid grid-cols-1 gap-3">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-gray-700 flex items-center">
                                        <i class="fas fa-server text-blue-500 mr-2 w-4"></i>Host:
                                    </span> 
                                    <span class="text-blue-600 font-mono bg-white px-2 py-1 rounded">sql.docdag.com</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-gray-700 flex items-center">
                                        <i class="fas fa-database text-green-500 mr-2 w-4"></i>Database:
                                    </span> 
                                    <span class="text-green-600 font-mono bg-white px-2 py-1 rounded">${db_name}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-gray-700 flex items-center">
                                        <i class="fas fa-user text-purple-500 mr-2 w-4"></i>Username:
                                    </span> 
                                    <span class="text-purple-600 font-mono bg-white px-2 py-1 rounded">${db_user}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-gray-700 flex items-center">
                                        <i class="fas fa-key text-red-500 mr-2 w-4"></i>Password:
                                    </span> 
                                    <span class="text-red-600 font-mono bg-white px-2 py-1 rounded">${db_password}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-gray-700 flex items-center">
                                        <i class="fas fa-plug text-orange-500 mr-2 w-4"></i>Port:
                                    </span> 
                                    <span class="text-orange-600 font-mono bg-white px-2 py-1 rounded">3306</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h4 class="font-bold mb-3 text-lg text-gray-800 flex items-center">
                            <i class="fas fa-code text-green-500 mr-2"></i>โค้ด PHP สำหรับเชื่อมต่อ (พร้อมใช้งาน):
                        </h4>
                        <div class="relative">
                            <textarea readonly class="w-full h-64 p-4 bg-gray-900 text-green-400 text-xs font-mono rounded-lg border border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="connectionCode">${connectionCode}</textarea>
                            <div class="absolute top-2 right-2">
                                <span class="bg-gray-800 text-gray-300 px-2 py-1 rounded text-xs">PHP</span>
                            </div>
                        </div>
                        <div class="mt-3 p-3 bg-yellow-50 border-l-4 border-yellow-400 rounded-r">
                            <p class="text-sm text-yellow-800 flex items-center">
                                <i class="fas fa-lightbulb mr-2"></i>
                                <strong>คำแนะนำ:</strong> คลิกปุ่ม "คัดลอกโค้ด" ด้านล่างเพื่อคัดลอกโค้ดไปใช้งานได้ทันที
                            </p>
                        </div>
                    </div>
                </div>
            `,
            width: '90%',
            maxWidth: '900px',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-copy mr-2"></i>คัดลอกโค้ด',
            cancelButtonText: '<i class="fas fa-times mr-2"></i>ปิด',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg mr-3 transition duration-200',
                cancelButton: 'bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200'
            },
            didOpen: () => {
                document.getElementById('connectionCode').select();
            }
        }).then((result) => {
            if (result.isConfirmed) {
                navigator.clipboard.writeText(connectionCode).then(() => {
                    Swal.fire('สำเร็จ!', 'คัดลอกโค้ดเรียบร้อย', 'success');
                });
            }
        });
    }
    
    async function createDB(event) {
        event.preventDefault();
        const form = event.target;
        const db_name = form.db_name.value;
        const db_user = form.db_user.value;
        const db_pass = form.db_pass.value;
        const db_pass_confirm = form.db_pass_confirm.value;
        const recaptcha = grecaptcha.getResponse();
        
        if (!recaptcha) {
            Swal.fire({
                title: '🤖 ข้อผิดพลาด!',
                text: 'กรุณายืนยัน reCAPTCHA ก่อนดำเนินการ',
                icon: 'warning',
                confirmButtonColor: '#F59E0B'
            });
            return;
        }
        
        if (db_pass !== db_pass_confirm) {
            Swal.fire({
                title: '🔐 ข้อผิดพลาด!',
                text: 'รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน',
                icon: 'error',
                confirmButtonColor: '#EF4444'
            });
            return;
        }
        
        const payload = { db_name, db_user, db_pass, recaptcha };
        
        try {
            const res = await fetch('api/api_db_create.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            
            if (data.success) {
                Swal.fire({
                    title: '🎉 สำเร็จ!', 
                    text: 'สร้างฐานข้อมูลสำเร็จแล้ว',
                    icon: 'success',
                    confirmButtonColor: '#10B981'
                });
                closeCreateModal();
                fetchList();
            } else {
                Swal.fire({
                    title: '❌ ผิดพลาด!', 
                    text: data.error || 'ไม่สามารถบันทึกในระบบได้',
                    icon: 'error',
                    confirmButtonColor: '#EF4444'
                });
            }
        } catch (error) {
            Swal.fire('ผิดพลาด!', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
        }
    }
    
    async function editDB(event) {
        event.preventDefault();
        const form = event.target;
        const id = form.edit_id.value;
        const db_pass = form.edit_db_pass.value;
        const db_pass_confirm = form.edit_db_pass_confirm.value;
        
        if (db_pass !== db_pass_confirm) {
            Swal.fire('ผิดพลาด!', 'รหัสผ่านไม่ตรงกัน', 'error');
            return;
        }
        
        const payload = { id, db_pass };
        
        try {
            const res = await fetch('api/api_db_edit.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            
            if (data.success) {
                Swal.fire('สำเร็จ!', 'เปลี่ยนรหัสผ่านสำเร็จ', 'success');
                closeEditModal();
                fetchList();
            } else {
                Swal.fire('ผิดพลาด!', data.error || 'ไม่สามารถบันทึกในระบบได้', 'error');
            }
        } catch (error) {
            Swal.fire('ผิดพลาด!', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
        }
    }
    
    async function deleteDB(id, db_name) {
        const result = await Swal.fire({
            title: 'ยืนยันการลบ?',
            text: `คุณต้องการลบฐานข้อมูล "${db_name}" หรือไม่?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
        });
        
        if (!result.isConfirmed) return;
        
        const payload = { id, db_name };
        
        try {
            const res = await fetch('api/api_db_delete.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            
            if (data.success) {
                Swal.fire('สำเร็จ!', 'ลบฐานข้อมูลสำเร็จ', 'success');
                fetchList();
            } else {
                // Show detailed error information for debugging
                const errorMsg = data.error || 'ไม่สามารถลบในระบบได้';
                const debugInfo = data.debug_info || {};
                
                let detailsHtml = '';
                if (debugInfo.http_code) {
                    detailsHtml += `<p><strong>HTTP Code:</strong> ${debugInfo.http_code}</p>`;
                }
                if (debugInfo.da_db_name) {
                    detailsHtml += `<p><strong>Database Name (DA):</strong> ${debugInfo.da_db_name}</p>`;
                }
                if (debugInfo.raw_response) {
                    detailsHtml += `<p><strong>Raw Response:</strong></p><pre style="text-align: left; font-size: 12px; max-height: 200px; overflow-y: auto;">${escapeHtml(JSON.stringify(debugInfo.raw_response, null, 2))}</pre>`;
                }
                
                Swal.fire({
                    title: 'ผิดพลาด!',
                    html: `<p>${errorMsg}</p>${detailsHtml}`,
                    icon: 'error',
                    width: '600px',
                    customClass: {
                        content: 'text-left'
                    }
                });
            }
        } catch (error) {
            Swal.fire({
                title: 'ผิดพลาด!',
                html: `<p>เกิดข้อผิดพลาดในการเชื่อมต่อ</p><p><strong>Error:</strong> ${error.message}</p>`,
                icon: 'error'
            });
        }
    }
    
    function renderCards(rows) {
        const container = document.getElementById('db-cards');
        container.innerHTML = '';
        
        if (rows.length === 0) {
            container.innerHTML = `
                <div class="col-span-full text-center py-16">
                    <div class="bg-white rounded-2xl shadow-lg p-12 max-w-md mx-auto">
                        <div class="bg-gradient-to-br from-blue-100 to-indigo-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-database text-4xl text-blue-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-3">ยังไม่มีฐานข้อมูล</h3>
                        <p class="text-gray-500 mb-6">เริ่มต้นสร้างฐานข้อมูลแรกของคุณเพื่อจัดเก็บข้อมูล</p>
                        <button onclick="openCreateModal()" class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-8 py-3 rounded-lg transition duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-plus mr-2"></i>สร้างฐานข้อมูลแรก
                        </button>
                    </div>
                </div>
            `;
            return;
        }
        
        for (const r of rows) {
            const card = document.createElement('div');
            card.className = 'bg-white rounded-xl shadow-lg p-4 sm:p-6 hover:shadow-xl transition-all duration-300 border border-gray-100 transform hover:-translate-y-1';
            card.innerHTML = `
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center">
                        <div class="bg-gradient-to-br from-blue-100 to-blue-200 p-2 sm:p-3 rounded-full mr-3 sm:mr-4 shadow-sm">
                            <i class="fas fa-database text-blue-600 text-lg sm:text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-semibold text-gray-800 break-all">${escapeHtml(r.db_name)}</h3>
                            <p class="text-xs sm:text-sm text-gray-500">ID: ${r.id}</p>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-2 sm:space-y-3 mb-4 sm:mb-6">
                    <div class="flex items-center text-xs sm:text-sm">
                        <i class="fas fa-user w-4 sm:w-5 text-gray-400 flex-shrink-0"></i>
                        <span class="text-gray-600 ml-2 flex-shrink-0">ผู้ใช้:</span>
                        <span class="ml-2 font-medium break-all">${escapeHtml(r.db_username)}</span>
                    </div>
                    <div class="flex items-center text-xs sm:text-sm">
                        <i class="fas fa-key w-4 sm:w-5 text-gray-400 flex-shrink-0"></i>
                        <span class="text-gray-600 ml-2 flex-shrink-0">รหัสผ่าน:</span>
                        <span class="ml-2 font-mono bg-gray-100 px-2 py-1 rounded text-xs break-all">${escapeHtml(r.db_password)}</span>
                    </div>
                    <div class="flex items-start text-xs sm:text-sm">
                        <i class="fas fa-clock w-4 sm:w-5 text-gray-400 flex-shrink-0 mt-0.5"></i>
                        <span class="text-gray-600 ml-2 flex-shrink-0">สร้างเมื่อ:</span>
                        <span class="ml-2 break-all">${new Date(r.created_at).toLocaleString('th-TH')}</span>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                    <button onclick="showConnectionInfo('${escapeHtml(r.db_name)}', '${escapeHtml(r.db_username)}', '${escapeHtml(r.db_password)}')" 
                            class="flex-1 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-3 sm:px-4 py-2 rounded-lg transition duration-200 text-xs sm:text-sm shadow-md hover:shadow-lg transform hover:scale-105">
                        <i class="fas fa-plug mr-1"></i>เชื่อมต่อ
                    </button>
                    <button onclick="openEditModal('${r.id}', '${escapeHtml(r.db_name)}', '${escapeHtml(r.db_username)}', '${escapeHtml(r.db_password)}')" 
                            class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-3 sm:px-4 py-2 rounded-lg transition duration-200 text-xs sm:text-sm shadow-md hover:shadow-lg transform hover:scale-105">
                        <i class="fas fa-edit mr-1"></i>แก้ไข
                    </button>
                    <button onclick="deleteDB('${r.id}', '${escapeHtml(r.db_name)}')" 
                            class="flex-1 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-3 sm:px-4 py-2 rounded-lg transition duration-200 text-xs sm:text-sm shadow-md hover:shadow-lg transform hover:scale-105">
                        <i class="fas fa-trash mr-1"></i>ลบ
                    </button>
                </div>
            `;
            container.appendChild(card);
        }
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    window.onload = fetchList;
    </script>

    
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 sm:mb-8 space-y-4 sm:space-y-0">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">จัดการฐานข้อมูล</h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">สร้างและจัดการฐานข้อมูลของคุณ</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 items-center">
                <a href="https://freesql.docdag.com/phpMyAdmin/" target="_blank" rel="noopener noreferrer"
                   class="bg-gradient-to-r from-gray-700 to-gray-900 hover:from-gray-800 hover:to-black text-white px-4 sm:px-6 py-3 rounded-lg transition duration-200 shadow-lg hover:shadow-xl flex items-center text-sm sm:text-base">
                    <i class="fas fa-database mr-2"></i>phpMyAdmin
                </a>
                <button onclick="openCreateModal()" 
                        class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 sm:px-6 py-3 rounded-lg transition duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 text-sm sm:text-base">
                    <i class="fas fa-plus mr-2"></i>สร้างฐานข้อมูลใหม่
                </button>
            </div>
        </div>

        <!-- Database Cards Grid -->
        <div id="db-cards" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
            <!-- Cards will be rendered here -->
        </div>
    </div>

    <!-- Create Modal -->
    <div id="createModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl p-6 sm:p-8 w-full max-w-md mx-4 shadow-2xl transform transition-all duration-300">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900">สร้างฐานข้อมูลใหม่</h2>
                <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 p-1">
                    <i class="fas fa-times text-lg sm:text-xl"></i>
                </button>
            </div>
            
            <form id="createForm" onsubmit="createDB(event)" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-database mr-2"></i>ชื่อฐานข้อมูล
                    </label>
                    <input name="db_name" type="text" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 text-sm sm:text-base"
                           placeholder="ใส่ชื่อฐานข้อมูล">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2"></i>ชื่อผู้ใช้ฐานข้อมูล
                    </label>
                    <input name="db_user" type="text" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 text-sm sm:text-base"
                           placeholder="ใส่ชื่อผู้ใช้">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-key mr-2"></i>รหัสผ่าน
                    </label>
                    <input name="db_pass" type="password" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 text-sm sm:text-base"
                           placeholder="ใส่รหัสผ่าน">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-key mr-2"></i>ยืนยันรหัสผ่าน
                    </label>
                    <input name="db_pass_confirm" type="password" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 text-sm sm:text-base"
                           placeholder="ยืนยันรหัสผ่าน">
                </div>
                
                <div class="flex justify-center py-2">
                    <div class="g-recaptcha" data-sitekey="6Lc7pnIrAAAAAE4NB4_iMW8ZwwCBN6itP7nXJ7Kf"></div>
                </div>
                
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4 pt-4">
                    <button type="button" onclick="closeCreateModal()" 
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-3 rounded-lg transition duration-200 text-sm sm:text-base">
                        ยกเลิก
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-3 rounded-lg transition duration-200 shadow-lg hover:shadow-xl text-sm sm:text-base">
                        <i class="fas fa-plus mr-2"></i>สร้าง
                    </button>
                </div>
            </form>
        </div>
    </div>
                   

    <!-- Edit Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl p-6 sm:p-8 w-full max-w-md mx-4 shadow-2xl transform transition-all duration-300">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900">เปลี่ยนรหัสผ่านฐานข้อมูล</h2>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 p-1">
                    <i class="fas fa-times text-lg sm:text-xl"></i>
                </button>
            </div>
            
            <form id="editForm" onsubmit="editDB(event)" class="space-y-4">
                <input type="hidden" id="edit_id" name="edit_id">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-database mr-2"></i>ชื่อฐานข้อมูล
                    </label>
                    <input id="edit_db_name" type="text" disabled 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 text-sm sm:text-base">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2"></i>ชื่อผู้ใช้ฐานข้อมูล
                    </label>
                    <input id="edit_db_user" name="edit_db_user" type="text" disabled 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 text-sm sm:text-base">
                    <p class="text-xs text-gray-500 mt-1">ไม่สามารถแก้ไขชื่อผู้ใช้ได้</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-key mr-2"></i>รหัสผ่านใหม่
                    </label>
                    <input id="edit_db_pass" name="edit_db_pass" type="password" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200 text-sm sm:text-base">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-key mr-2"></i>ยืนยันรหัสผ่านใหม่
                    </label>
                    <input id="edit_db_pass_confirm" name="edit_db_pass_confirm" type="password" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200 text-sm sm:text-base">
                </div>
                
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4 pt-4">
                    <button type="button" onclick="closeEditModal()" 
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-3 rounded-lg transition duration-200 text-sm sm:text-base">
                        ยกเลิก
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-4 py-3 rounded-lg transition duration-200 shadow-lg hover:shadow-xl text-sm sm:text-base">
                        <i class="fas fa-save mr-2"></i>บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
           

 <?php include 'footer.php'; ?>