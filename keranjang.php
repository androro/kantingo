<?php
session_start();

// Data statis untuk mendapatkan nama kantin
$all_data = [
    'kantin' => [
        1 => [ 'id' => 1, 'nama_kantin' => 'Kantin Sejahtera' ],
        2 => [ 'id' => 2, 'nama_kantin' => 'Warung Ibu Siti' ],
        3 => [ 'id' => 3, 'nama_kantin' => 'Pojok Kopi' ]
    ]
];

// Logika untuk update keranjang (tambah, kurang, hapus item)
if (isset($_POST['action'])) {
    $menu_id = (int)$_POST['menu_id'];
    if (isset($_SESSION['cart'][$menu_id])) {
        switch ($_POST['action']) {
            case 'increase':
                $_SESSION['cart'][$menu_id]['quantity']++;
                break;
            case 'decrease':
                $_SESSION['cart'][$menu_id]['quantity']--;
                if ($_SESSION['cart'][$menu_id]['quantity'] <= 0) {
                    unset($_SESSION['cart'][$menu_id]);
                }
                break;
            case 'remove':
                unset($_SESSION['cart'][$menu_id]);
                break;
        }
    }
    // Jika keranjang kosong, reset ID kantin
    if (empty($_SESSION['cart'])) {
        $_SESSION['cart_kantin_id'] = 0;
    }
    header('Location: keranjang.php');
    exit();
}

$cart_items = $_SESSION['cart'] ?? [];
$kantin_id = $_SESSION['cart_kantin_id'] ?? 0;
$kantin_name = ($kantin_id > 0 && isset($all_data['kantin'][$kantin_id])) ? $all_data['kantin'][$kantin_id]['nama_kantin'] : '';

$cart_total_price = 0;
foreach ($cart_items as $item) {
    $cart_total_price += $item['harga'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - KantinGo</title>
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
            <h1 class="text-xl font-bold text-gray-800">Keranjang Saya</h1>
        </div>
    </header>

    <main class="pt-20 pb-48 px-4 container mx-auto">
        <?php if (empty($cart_items)): ?>
            <div class="text-center py-20">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">Keranjang Anda kosong</h3>
                <p class="mt-1 text-sm text-gray-500">Ayo mulai belanja di kantin favoritmu!</p>
            </div>
        <?php else: ?>
            <div class="bg-white p-4 rounded-xl shadow-sm mb-4">
                <h2 class="font-bold text-lg">Pesanan dari: <?php echo htmlspecialchars($kantin_name); ?></h2>
            </div>
            <div class="space-y-4">
                <?php foreach ($cart_items as $item): ?>
                    <div class="bg-white p-4 rounded-xl shadow-sm flex items-center space-x-4">
                        <img src="<?php echo htmlspecialchars($item['gambar_url']); ?>" alt="<?php echo htmlspecialchars($item['nama_menu']); ?>" class="w-20 h-20 rounded-lg object-cover flex-shrink-0">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-800"><?php echo htmlspecialchars($item['nama_menu']); ?></h3>
                            <p class="text-sm text-orange-500 font-bold">Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></p>
                            <div class="flex items-center space-x-3 mt-2">
                                <form action="keranjang.php" method="POST" class="inline-block">
                                    <input type="hidden" name="menu_id" value="<?php echo $item['id']; ?>">
                                    <input type="hidden" name="action" value="decrease">
                                    <button type="submit" class="bg-gray-200 rounded-full w-6 h-6 flex items-center justify-center font-bold">-</button>
                                </form>
                                <span class="font-bold w-4 text-center"><?php echo $item['quantity']; ?></span>
                                <form action="keranjang.php" method="POST" class="inline-block">
                                    <input type="hidden" name="menu_id" value="<?php echo $item['id']; ?>">
                                    <input type="hidden" name="action" value="increase">
                                    <button type="submit" class="bg-gray-200 rounded-full w-6 h-6 flex items-center justify-center font-bold">+</button>
                                </form>
                            </div>
                        </div>
                        <form action="keranjang.php" method="POST">
                            <input type="hidden" name="menu_id" value="<?php echo $item['id']; ?>">
                            <input type="hidden" name="action" value="remove">
                            <button type="submit" class="text-red-500 hover:text-red-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
    
    <!-- Checkout Bar (jika keranjang ada isinya) -->
    <?php if (!empty($cart_items)): ?>
    <div class="fixed bottom-16 left-0 right-0 z-10 border-t bg-white p-4 shadow-[0_-2px_10px_rgba(0,0,0,0.05)]">
        <div class="container mx-auto">
            <div class="flex justify-between items-center mb-3">
                <span class="text-lg font-medium text-gray-700">Total</span>
                <span class="font-bold text-xl text-gray-900">Rp <?php echo number_format($cart_total_price, 0, ',', '.'); ?></span>
            </div>
            <a href="checkout.php" class="w-full block bg-orange-500 text-white text-center rounded-lg shadow-lg py-3 text-lg font-bold hover:bg-orange-600 transition">
                Lanjut ke Pembayaran
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Navigasi Bawah -->
    <div class="fixed bottom-0 left-0 right-0 z-10 border-t bg-white">
        <nav class="container mx-auto px-4 h-16 flex justify-around items-center">
            <a href="index.php" class="flex flex-col items-center justify-center text-gray-500 hover:text-orange-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h7.5" /></svg><span class="text-xs font-medium">Beranda</span></a>
            <a href="keranjang.php" class="flex flex-col items-center justify-center text-orange-500"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path d="M2.25 2.25a.75.75 0 000 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 00-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 000-1.5H5.378A2.25 2.25 0 017.5 15h11.218a.75.75 0 00.744-.647l2.85-8.55a.75.75 0 00-.744-.853H4.386a.75.75 0 00-.744.647L3.19 5.278A.75.75 0 002.25 5.25H2.25a.75.75 0 000-1.5H.75a.75.75 0 000 1.5H2.25z" /></svg><span class="text-xs font-medium">Keranjang</span></a>
            <a href="riwayat.php" class="flex flex-col items-center justify-center text-gray-500 hover:text-orange-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span class="text-xs font-medium">Riwayat</span></a>
            <a href="akun.php" class="flex flex-col items-center justify-center text-gray-500 hover:text-orange-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg><span class="text-xs font-medium">Akun</span></a>
        </nav>
    </div>
</body>
</html>

