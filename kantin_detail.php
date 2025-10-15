<?php
session_start();

// Data statis lengkap (termasuk menu)
$all_data = [
    'kantin' => [
        1 => [ 'id' => 1, 'nama_kantin' => 'Kantin Sejahtera', 'logo_url' => 'https://placehold.co/100x100/facc15/333?text=A', 'status' => 'Buka' ],
        2 => [ 'id' => 2, 'nama_kantin' => 'Warung Ibu Siti', 'logo_url' => 'https://placehold.co/100x100/4ade80/333?text=B', 'status' => 'Buka' ],
        3 => [ 'id' => 3, 'nama_kantin' => 'Pojok Kopi', 'logo_url' => 'https://placehold.co/100x100/f87171/333?text=C', 'status' => 'Tutup' ]
    ],
    'menu' => [
        1 => [ // Menu untuk Kantin Sejahtera (id=1)
            [ 'id' => 101, 'nama_menu' => 'Nasi Goreng Spesial', 'deskripsi_menu' => 'Dengan telur, ayam suwir, dan kerupuk.', 'harga' => 15000, 'gambar_url' => 'https://placehold.co/600x400/f97316/white?text=Nasi+Goreng' ],
            [ 'id' => 102, 'nama_menu' => 'Ayam Bakar Madu', 'deskripsi_menu' => 'Ayam bakar empuk bumbu madu manis.', 'harga' => 18000, 'gambar_url' => 'https://placehold.co/600x400/ef4444/white?text=Ayam+Bakar' ],
            [ 'id' => 103, 'nama_menu' => 'Soto Ayam Lamongan', 'deskripsi_menu' => 'Soto bening dengan koya dan sambal.', 'harga' => 12000, 'gambar_url' => 'https://placehold.co/600x400/eab308/white?text=Soto' ]
        ],
        2 => [ // Menu untuk Warung Ibu Siti (id=2)
            [ 'id' => 201, 'nama_menu' => 'Gado-gado Komplit', 'deskripsi_menu' => 'Sayuran segar dengan bumbu kacang.', 'harga' => 13000, 'gambar_url' => 'https://placehold.co/600x400/22c55e/white?text=Gado-gado' ],
            [ 'id' => 202, 'nama_menu' => 'Jus Alpukat', 'deskripsi_menu' => 'Jus alpukat segar dengan susu coklat.', 'harga' => 8000, 'gambar_url' => 'https://placehold.co/600x400/16a34a/white?text=Jus+Alpukat' ]
        ],
        3 => [ // Menu untuk Pojok Kopi (id=3)
            [ 'id' => 301, 'nama_menu' => 'Kopi Susu Gula Aren', 'deskripsi_menu' => 'Espresso, susu, dan gula aren.', 'harga' => 15000, 'gambar_url' => 'https://placehold.co/600x400/a16207/white?text=Kopi' ]
        ]
    ]
];

// Ambil ID dari URL dan validasi
$kantin_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!array_key_exists($kantin_id, $all_data['kantin'])) {
    die("Kantin tidak ditemukan.");
}

$kantin = $all_data['kantin'][$kantin_id];
$menu_items = $all_data['menu'][$kantin_id] ?? [];

// --- LOGIKA KERANJANG ---
if (isset($_GET['add_to_cart'])) {
    $menu_id_to_add = (int)$_GET['add_to_cart'];
    $item_to_add = null;
    foreach ($menu_items as $item) {
        if ($item['id'] == $menu_id_to_add) {
            $item_to_add = $item;
            break;
        }
    }

    if ($item_to_add) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
            $_SESSION['cart_kantin_id'] = 0;
        }
        // Jika menambah dari kantin berbeda, kosongkan keranjang lama
        if ($_SESSION['cart_kantin_id'] != 0 && $_SESSION['cart_kantin_id'] != $kantin_id) {
            $_SESSION['cart'] = [];
        }
        $_SESSION['cart_kantin_id'] = $kantin_id;

        if (isset($_SESSION['cart'][$menu_id_to_add])) {
            $_SESSION['cart'][$menu_id_to_add]['quantity']++;
        } else {
            $_SESSION['cart'][$menu_id_to_add] = $item_to_add;
            $_SESSION['cart'][$menu_id_to_add]['quantity'] = 1;
        }
    }
    // Redirect untuk menghapus parameter GET dari URL
    header("Location: kantin_detail.php?id=" . $kantin_id);
    exit();
}

// Hitung total item dan harga di keranjang
$cart_item_count = 0;
$cart_total_price = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_item_count += $item['quantity'];
        $cart_total_price += $item['harga'] * $item['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($kantin['nama_kantin']); ?> - KantinGo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50">
    
    <header class="fixed top-0 left-0 right-0 z-20 bg-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex items-center">
            <a href="index.php" class="mr-4 p-2 rounded-full hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h1 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($kantin['nama_kantin']); ?></h1>
        </div>
    </header>

    <main class="pt-20 pb-40 px-4 container mx-auto">
        <!-- Info Kantin -->
        <div class="flex items-center space-x-4 mb-8">
            <img src="<?php echo htmlspecialchars($kantin['logo_url']); ?>" alt="Logo" class="w-16 h-16 rounded-lg">
            <div>
                <h2 class="text-2xl font-bold"><?php echo htmlspecialchars($kantin['nama_kantin']); ?></h2>
                 <?php if ($kantin['status'] == 'Buka'): ?>
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Buka</span>
                <?php else: ?>
                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Tutup</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Daftar Menu -->
        <h3 class="text-lg font-bold text-gray-800 mb-4">Menu</h3>
        <div class="space-y-4">
            <?php if (!empty($menu_items)): ?>
                <?php foreach ($menu_items as $item): ?>
                    <div class="bg-white p-4 rounded-xl shadow-sm flex space-x-4 items-center">
                        <img src="<?php echo htmlspecialchars($item['gambar_url']); ?>" alt="<?php echo htmlspecialchars($item['nama_menu']); ?>" class="w-24 h-24 rounded-lg object-cover flex-shrink-0">
                        <div class="flex-1">
                            <h4 class="font-bold text-md text-gray-900"><?php echo htmlspecialchars($item['nama_menu']); ?></h4>
                            <p class="text-sm text-gray-500 mt-1"><?php echo htmlspecialchars($item['deskripsi_menu']); ?></p>
                            <p class="font-semibold text-gray-800 mt-2">Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></p>
                        </div>
                        <a href="kantin_detail.php?id=<?php echo $kantin_id; ?>&add_to_cart=<?php echo $item['id']; ?>" class="bg-orange-500 text-white rounded-full w-8 h-8 flex items-center justify-center shadow hover:bg-orange-600 transition active:scale-90">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-10"><p class="text-gray-500">Belum ada menu di kantin ini.</p></div>
            <?php endif; ?>
        </div>
    </main>
    
    <!-- Floating Cart Button -->
    <?php if ($cart_item_count > 0): ?>
    <div class="fixed bottom-20 left-0 right-0 z-10 px-4">
        <div class="container mx-auto">
             <a href="keranjang.php" class="w-full bg-orange-500 text-white rounded-lg shadow-lg p-4 flex justify-between items-center hover:bg-orange-600 transition">
                <div class="flex items-center space-x-2">
                    <div class="bg-orange-600 rounded-md px-2 py-0.5 text-sm font-bold"><?php echo $cart_item_count; ?></div>
                    <span class="font-semibold">Lihat Keranjang</span>
                </div>
                <span class="font-bold">Rp <?php echo number_format($cart_total_price, 0, ',', '.'); ?></span>
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Navigasi Bawah -->
    <div class="fixed bottom-0 left-0 right-0 z-10 border-t bg-white">
        <nav class="container mx-auto px-4 h-16 flex justify-around items-center">
            <a href="index.php" class="flex flex-col items-center justify-center text-gray-500 hover:text-orange-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h7.5" /></svg><span class="text-xs font-medium">Beranda</span></a>
            <a href="keranjang.php" class="flex flex-col items-center justify-center text-gray-500 hover:text-orange-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c.51 0 .962-.343 1.087-.835l1.838-5.512A1.125 1.125 0 0018.618 6H4.23a1.125 1.125 0 00-1.087.835L2.25 3z" /></svg><span class="text-xs font-medium">Keranjang</span></a>
            <a href="riwayat.php" class="flex flex-col items-center justify-center text-gray-500 hover:text-orange-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span class="text-xs font-medium">Riwayat</span></a>
            <a href="akun.php" class="flex flex-col items-center justify-center text-gray-500 hover:text-orange-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg><span class="text-xs font-medium">Akun</span></a>
        </nav>
    </div>

</body>
</html>