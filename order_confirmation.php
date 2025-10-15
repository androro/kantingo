<?php
session_start();
include 'db.php';

// Redirect jika tidak ada order_id di URL
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = (int)$_GET['order_id'];
$order = null;
$order_items = [];

if (isset($mysqli)) {
    // Ambil detail pesanan
    $stmt_order = $mysqli->prepare("SELECT o.*, k.nama_kantin FROM orders o JOIN kantin k ON o.kantin_id = k.id WHERE o.id = ?");
    $stmt_order->bind_param("i", $order_id);
    $stmt_order->execute();
    $result_order = $stmt_order->get_result();
    if ($result_order->num_rows > 0) {
        $order = $result_order->fetch_assoc();
    }
    $stmt_order->close();

    if ($order) {
        // Ambil item-item pesanan
        $stmt_items = $mysqli->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt_items->bind_param("i", $order_id);
        $stmt_items->execute();
        $result_items = $stmt_items->get_result();
        while ($row = $result_items->fetch_assoc()) {
            $order_items[] = $row;
        }
        $stmt_items->close();
    }
    $mysqli->close();
}

if (!$order) {
    die("Pesanan tidak ditemukan.");
}

// Hitung subtotal dan biaya layanan untuk ditampilkan
$subtotal = 0;
foreach ($order_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$biaya_layanan = $order['total_amount'] - $subtotal;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan - KantinGo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50">

    <header class="fixed top-0 left-0 right-0 z-20 bg-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex items-center">
            <a href="riwayat.php" class="mr-4 p-2 rounded-full hover:bg-gray-100">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <h1 class="text-xl font-bold text-gray-800">Detail Pesanan</h1>
        </div>
    </header>

    <main class="pt-20 pb-48 px-4 container mx-auto">
        <?php if ($order['order_status'] == 'Pending'): ?>
        <div class="text-center bg-white p-6 rounded-xl shadow-sm mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-green-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <h2 class="text-2xl font-bold text-gray-800">Pesanan Berhasil Dibuat!</h2>
            <p class="mt-2 text-gray-600">Silakan lakukan pembayaran dan pengambilan di kantin.</p>
        </div>
        <?php elseif ($order['order_status'] == 'Dibatalkan'): ?>
        <div class="text-center bg-white p-6 rounded-xl shadow-sm mb-6">
             <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-red-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <h2 class="text-2xl font-bold text-gray-800">Pesanan Dibatalkan</h2>
            <p class="mt-2 text-gray-600">Pesanan ini telah Anda batalkan.</p>
        </div>
        <?php endif; ?>

        <?php if ($order['order_status'] == 'Pending'): ?>
        <div class="bg-white p-4 rounded-xl shadow-sm mb-4">
            <h2 class="font-bold text-lg mb-2">Kode Pengambilan Pesanan</h2>
            <p class="text-center text-4xl font-extrabold text-orange-600 mb-4"><?php echo htmlspecialchars($order['order_code']); ?></p>
            
            <?php if ($order['payment_method'] == 'tunai'): ?>
            <p class="text-center text-gray-600 mb-4">Tunjukkan kode ini saat mengambil pesanan Anda di Kantin <?php echo htmlspecialchars($order['nama_kantin']); ?>.</p>
            <div class="flex justify-center mb-4">
                <div id="qrcode" class="p-2 bg-white border border-gray-200 rounded-lg inline-block"></div>
            </div>
            <p class="text-center text-sm text-gray-500">Scan QR Code ini untuk konfirmasi pesanan Anda.</p>
            <?php else: ?>
            <p class="text-center text-gray-600">Pesanan Anda akan diproses setelah pembayaran QRIS terverifikasi.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Ringkasan Detail Pesanan -->
        <div class="bg-white p-4 rounded-xl shadow-sm mb-4">
            <h2 class="font-bold text-lg mb-2">Detail Pesanan</h2>
            <p class="text-gray-700 mb-2">Kantin: <span class="font-semibold"><?php echo htmlspecialchars($order['nama_kantin']); ?></span></p>
            <div class="space-y-2 border-t pt-3">
                <?php foreach($order_items as $item): ?>
                <div class="flex justify-between text-gray-700">
                    <div class="flex space-x-2">
                        <span><?php echo $item['quantity']; ?>x</span>
                        <span><?php echo htmlspecialchars($item['menu_item_name']); ?></span>
                    </div>
                    <span>Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-4 border-t pt-3 space-y-2">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span>
                    <span>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></span>
                </div>
                <?php if ($biaya_layanan > 0): ?>
                <div class="flex justify-between text-gray-600">
                    <span>Biaya Layanan</span>
                    <span>Rp <?php echo number_format($biaya_layanan, 0, ',', '.'); ?></span>
                </div>
                <?php endif; ?>
                <div class="flex justify-between font-bold text-lg text-gray-900 mt-2 border-t pt-2">
                    <span>Total Pembayaran</span>
                    <span>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></span>
                </div>
            </div>
             <p class="text-gray-700 mt-3">Metode Pembayaran: <span class="font-semibold capitalize"><?php echo htmlspecialchars($order['payment_method']); ?></span></p>
        </div>

        <div class="text-center mt-6 space-y-3">
            <a href="riwayat.php" class="inline-block bg-blue-500 text-white py-3 px-6 rounded-lg shadow-lg font-bold hover:bg-blue-600 transition">Lihat Riwayat Pesanan</a>
            
            <?php if ($order['order_status'] == 'Pending'): ?>
            <form action="cancel_order_process.php" method="POST" onsubmit="return confirm('Anda yakin ingin membatalkan pesanan ini?');">
                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                <button type="submit" class="w-full bg-red-500 text-white py-3 px-6 rounded-lg shadow-lg font-bold hover:bg-red-600 transition">Batalkan Pesanan</button>
            </form>
            <?php endif; ?>
        </div>
    </main>
    
    <!-- Navigasi Bawah -->
    <div class="fixed bottom-0 left-0 right-0 z-10 border-t bg-white">
        <nav class="container mx-auto px-4 h-16 flex justify-around items-center">
            <a href="index.php" class="flex flex-col items-center justify-center text-gray-500 hover:text-orange-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h7.5" /></svg><span class="text-xs font-medium">Beranda</span></a>
            <a href="keranjang.php" class="flex flex-col items-center justify-center text-gray-500 hover:text-orange-500"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path d="M2.25 2.25a.75.75 0 000 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 00-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 000-1.5H5.378A2.25 2.25 0 017.5 15h11.218a.75.75 0 00.744-.647l2.85-8.55a.75.75 0 00-.744-.853H4.386a.75.75 0 00-.744.647L3.19 5.278A.75.75 0 002.25 5.25H2.25a.75.75 0 000-1.5H.75a.75.75 0 000 1.5H2.25z" /></svg><span class="text-xs font-medium">Keranjang</span></a>
            <a href="riwayat.php" class="flex flex-col items-center justify-center text-gray-500 hover:text-orange-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span class="text-xs font-medium">Riwayat</span></a>
            <a href="akun.php" class="flex flex-col items-center justify-center text-gray-500 hover:text-orange-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg><span class="text-xs font-medium">Akun</span></a>
        </nav>
    </div>

    <script>
        const qrCodeContainer = document.getElementById('qrcode');
        if (qrCodeContainer) {
            const orderCode = "<?php echo addslashes($order['order_code']); ?>";
            
            new QRCode(qrCodeContainer, {
                text: orderCode,
                width: 180,
                height: 180,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
        }
    </script>

</body>
</html>

