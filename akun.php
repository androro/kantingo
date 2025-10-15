<?php
session_start();
include 'db.php';

// 1. Cek jika user belum login, redirect ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Ambil data user dari database untuk ditampilkan
$user_id = $_SESSION['user_id'];
$nama = '';
$username = '';

if (isset($mysqli)) {
    $stmt = $mysqli->prepare("SELECT nama, username FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        $nama = $user['nama'];
        $username = $user['username'];
    }
    $stmt->close();
    $mysqli->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Saya - KantinGo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50">
    
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-10 bg-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex justify-center items-center relative">
            <h1 class="text-xl font-bold text-gray-800">Akun Saya</h1>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pt-20 pb-24 px-4 container mx-auto">
        <!-- Profile Info -->
        <div class="flex flex-col items-center mb-8">
            <div class="w-24 h-24 bg-orange-500 rounded-full flex items-center justify-center text-white text-4xl font-bold mb-4">
                <?php echo htmlspecialchars(strtoupper(substr($nama, 0, 1))); ?>
            </div>
            <h2 class="text-2xl font-bold text-gray-900"><?php echo htmlspecialchars($nama); ?></h2>
            <p class="text-md text-gray-500">@<?php echo htmlspecialchars($username); ?></p>
        </div>

        <!-- Menu List -->
        <div class="space-y-4">
            <!-- Group Menu Informasi -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <a href="#" class="p-4 flex items-center justify-between w-full text-left border-b">
                    <div class="flex items-center space-x-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-semibold text-gray-700">Bantuan</span>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <a href="#" class="p-4 flex items-center justify-between w-full text-left border-b">
                    <div class="flex items-center space-x-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span class="font-semibold text-gray-700">Kebijakan Privasi</span>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <a href="#" class="p-4 flex items-center justify-between w-full text-left">
                    <div class="flex items-center space-x-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="font-semibold text-gray-700">Ketentuan Layanan</span>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <!-- Tombol Logout -->
            <div class="bg-white rounded-xl shadow-sm">
                <a href="logout.php" class="p-4 flex items-center justify-center w-full text-left space-x-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="font-semibold text-red-600">Logout</span>
                </a>
            </div>
        </div>
    </main>

    <!-- Navigasi Bawah -->
    <div class="fixed bottom-0 left-0 right-0 z-10 border-t bg-white">
        <nav class="container mx-auto px-4 h-16 flex justify-around items-center">
            <a href="index.php" class="flex flex-col items-center justify-center text-gray-500 hover:text-orange-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h7.5" /></svg><span class="text-xs font-medium">Beranda</span></a>
            <a href="keranjang.php" class="flex flex-col items-center justify-center text-gray-500 hover:text-orange-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c.51 0 .962-.343 1.087-.835l1.838-5.512A1.125 1.125 0 0018.618 6H4.23a1.125 1.125 0 00-1.087.835L2.25 3z" /></svg><span class="text-xs font-medium">Keranjang</span></a>
            <a href="riwayat.php" class="flex flex-col items-center justify-center text-gray-500 hover:text-orange-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span class="text-xs font-medium">Riwayat</span></a>
            <a href="akun.php" class="flex flex-col items-center justify-center text-orange-500"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path fill-rule="evenodd" d="M18.685 19.097A9.723 9.723 0 0021.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 003.065 7.097A9.716 9.716 0 0012 21.75a9.716 9.716 0 006.685-2.653zm-12.54-1.285A7.486 7.486 0 0112 15a7.486 7.486 0 015.855 2.812A8.224 8.224 0 0112 20.25a8.224 8.224 0 01-5.855-2.438zM15.75 9a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" clip-rule="evenodd" /></svg><span class="text-xs font-medium">Akun</span></a>
        </nav>
    </div>
</body>
</html>

