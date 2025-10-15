<?php
session_start();

// Redirect jika keranjang kosong atau user mencoba akses langsung
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

// Data statis untuk mendapatkan nama kantin (akan dihubungkan ke database nanti)
$all_data = [
    'kantin' => [
        1 => [ 'id' => 1, 'nama_kantin' => 'Kantin Sejahtera' ],
        2 => [ 'id' => 2, 'nama_kantin' => 'Warung Ibu Siti' ],
        3 => [ 'id' => 3, 'nama_kantin' => 'Pojok Kopi' ]
    ]
];

$cart_items = $_SESSION['cart'];
$kantin_id = $_SESSION['cart_kantin_id'];
$kantin_name = ($kantin_id > 0 && isset($all_data['kantin'][$kantin_id])) ? $all_data['kantin'][$kantin_id]['nama_kantin'] : '';

$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['harga'] * $item['quantity'];
}
$biaya_layanan = 1000; 
$total_pembayaran = $subtotal + $biaya_layanan;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - KantinGo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50">

    <header class="fixed top-0 left-0 right-0 z-20 bg-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex items-center">
            <a href="keranjang.php" class="mr-4 p-2 rounded-full hover:bg-gray-100">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <h1 class="text-xl font-bold text-gray-800">Konfirmasi Pesanan</h1>
        </div>
    </header>

    <main class="pt-20 pb-48 px-4 container mx-auto">
        <!-- Detail Pesanan -->
        <div class="bg-white p-4 rounded-xl shadow-sm mb-4">
            <h2 class="font-bold text-lg mb-2">Pesanan dari: <?php echo htmlspecialchars($kantin_name); ?></h2>
            <div class="space-y-3 border-t pt-3">
                <?php foreach($cart_items as $item): ?>
                <div class="flex justify-between items-start">
                    <div class="flex space-x-3">
                        <span class="font-semibold text-orange-600 bg-orange-100 rounded-md px-2"><?php echo $item['quantity']; ?>x</span>
                        <div>
                            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($item['nama_menu']); ?></p>
                            <p class="text-sm text-gray-500">Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></p>
                        </div>
                    </div>
                    <p class="font-semibold text-gray-800">Rp <?php echo number_format($item['harga'] * $item['quantity'], 0, ',', '.'); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Checkout -->
        <form action="order_process.php" method="POST">
            <input type="hidden" name="kantin_id" value="<?php echo $kantin_id; ?>">
            <input type="hidden" name="total_amount" value="<?php echo $total_pembayaran; ?>">

            <!-- Detail Pembayaran -->
            <div class="bg-white p-4 rounded-xl shadow-sm mb-4">
                <h2 class="font-bold text-lg mb-2">Rincian Pembayaran</h2>
                <div class="space-y-2 border-t pt-3">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Biaya Layanan</span>
                        <span>Rp <?php echo number_format($biaya_layanan, 0, ',', '.'); ?></span>
                    </div>
                </div>
                
                <!-- Metode Pembayaran -->
                <div class="mt-4 border-t pt-3">
                     <h3 class="font-bold text-md mb-3 text-gray-800">Metode Pembayaran</h3>
                     <div class="space-y-3">
                        <label for="tunai" class="flex items-center p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="radio" id="tunai" name="payment_method" value="tunai" class="h-4 w-4 text-orange-600 border-gray-300 focus:ring-orange-500" checked>
                            <span class="ml-3 font-medium text-gray-700">Bayar di Tempat (Tunai)</span>
                        </label>
                        <label for="qris" class="flex items-center p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="radio" id="qris" name="payment_method" value="qris" class="h-4 w-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                            <span class="ml-3 font-medium text-gray-700">QRIS</span>
                        </label>
                     </div>
                </div>

                <div class="flex justify-between font-bold text-lg text-gray-900 mt-4 border-t pt-3">
                    <span>Total Pembayaran</span>
                    <span>Rp <?php echo number_format($total_pembayaran, 0, ',', '.'); ?></span>
                </div>
            </div>
            
            <!-- Checkout Bar -->
            <div class="fixed bottom-16 left-0 right-0 z-10 border-t bg-white p-4 shadow-[0_-2px_10px_rgba(0,0,0,0.05)]">
                <div class="container mx-auto">
                    <button type="submit" class="w-full block bg-orange-500 text-white text-center rounded-lg shadow-lg py-3 text-lg font-bold hover:bg-orange-600 transition">
                        Pesan Sekarang
                    </button>
                </div>
            </div>
        </form>
        
    </main>

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