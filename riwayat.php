<?php
session_start();
include 'db.php';

// Redirect jika user belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$orders = [];

if (isset($mysqli)) {
    // Ambil semua pesanan user ini dari database
    $stmt = $mysqli->prepare("SELECT o.*, k.nama_kantin FROM orders o JOIN kantin k ON o.kantin_id = k.id WHERE o.user_id = ? ORDER BY o.created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
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
    <title>Riwayat Pesanan - KantinGo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50">

    <header class="fixed top-0 left-0 right-0 z-20 bg-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex items-center">
            <a href="index.php" class="mr-4 p-2 rounded-full hover:bg-gray-100">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <h1 class="text-xl font-bold text-gray-800">Riwayat Pesanan</h1>
        </div>
    </header>

    <main class="pt-20 pb-24 px-4 container mx-auto">
        <?php if (empty($orders)): ?>
            <div class="text-center py-20">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">Belum ada riwayat pesanan</h3>
                <p class="mt-1 text-sm text-gray-500">Mulai pesan makanan favorit Anda sekarang!</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($orders as $order): ?>
                    <a href="order_confirmation.php?order_id=<?php echo $order['id']; ?>" class="block bg-white p-4 rounded-xl shadow-sm transition hover:shadow-md">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-semibold text-gray-700">#<?php echo htmlspecialchars($order['order_code']); ?></span>
                            <span class="text-xs text-gray-500"><?php echo date('d M Y, H:i', strtotime($order['created_at'])); ?></span>
                        </div>
                        <p class="text-lg font-bold text-gray-900 mb-1"><?php echo htmlspecialchars($order['nama_kantin']); ?></p>
                        <div class="flex justify-between items-center">
                            <span class="text-md font-semibold text-orange-600">Total: Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></span>
                            <span class="text-sm font-medium px-2.5 py-0.5 rounded-full 
                                <?php 
                                    if ($order['order_status'] == 'Pending') echo 'bg-yellow-100 text-yellow-800';
                                    else if ($order['order_status'] == 'Dikonfirmasi') echo 'bg-blue-100 text-blue-800';
                                    else if ($order['order_status'] == 'Selesai') echo 'bg-green-100 text-green-800';
                                    else if ($order['order_status'] == 'Dibatalkan') echo 'bg-red-100 text-red-800';
                                ?>
                            ">
                                <?php echo htmlspecialchars($order['order_status']); ?>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
    
    <!-- Navigasi Bawah -->
    <div class="fixed bottom-0 left-0 right-0 z-10 border-t bg-white">
        <nav class="container mx-auto px-4 h-16 flex justify-around items-center">
            <a href="index.php" class="flex flex-col items-center justify-center text-gray-500 hover:text-orange-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h7.5" /></svg><span class="text-xs font-medium">Beranda</span></a>
            <a href="keranjang.php" class="flex flex-col items-center justify-center text-gray-500 hover:text-orange-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c.51 0 .962-.343 1.087-.835l1.838-5.512A1.125 1.125 0 0018.618 6H4.23a1.125 1.125 0 00-1.087.835L2.25 3z" /></svg><span class="text-xs font-medium">Keranjang</span></a>
            <a href="riwayat.php" class="flex flex-col items-center justify-center text-orange-500"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm.53 5.47a.75.75 0 00-1.06 0l-3 3a.75.75 0 101.06 1.06L12 9.06l2.47 2.47a.75.75 0 101.06-1.06l-3-3z" clip-rule="evenodd" /><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm.53 5.47a.75.75 0 00-1.06 0l-3 3a.75.75 0 101.06 1.06L12 9.06l2.47 2.47a.75.75 0 101.06-1.06l-3-3z" clip-rule="evenodd" /></svg><path d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm.53 5.47a.75.75 0 00-1.06 0l-3 3a.75.75 0 101.06 1.06L12 9.06l2.47 2.47a.75.75 0 101.06-1.06l-3-3z" /></svg><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm.53 5.47a.75.75 0 00-1.06 0l-3 3a.75.75 0 101.06 1.06L12 9.06l2.47 2.47a.75.75 0 101.06-1.06l-3-3z" clip-rule="evenodd" /></svg><span class="text-xs font-medium">Riwayat</span></a>
            <a href="akun.php" class="flex flex-col items-center justify-center text-gray-500 hover:text-orange-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg><span class="text-xs font-medium">Akun</span></a>
        </nav>
    </div>
</body>
</html>
