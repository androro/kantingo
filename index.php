<?php
session_start();

// Cek apakah pengguna sudah login atau belum
$is_logged_in = isset($_SESSION['user_id']);

// Data kantin statis (tanpa database) untuk sementara
$kantin_list = [
    [
        'id' => 1,
        'nama_kantin' => 'Kantin Sejahtera',
        'deskripsi_singkat' => 'Nasi Goreng, Ayam Bakar, Soto',
        'logo_url' => 'https://placehold.co/100x100/facc15/333?text=A',
        'status' => 'Buka'
    ],
    [
        'id' => 2,
        'nama_kantin' => 'Warung Ibu Siti',
        'deskripsi_singkat' => 'Gado-gado, Karedok, Aneka Jus',
        'logo_url' => 'https://placehold.co/100x100/4ade80/333?text=B',
        'status' => 'Buka'
    ],
    [
        'id' => 3,
        'nama_kantin' => 'Pojok Kopi',
        'deskripsi_singkat' => 'Kopi, Teh, Roti Bakar',
        'logo_url' => 'https://placehold.co/100x100/f87171/333?text=C',
        'status' => 'Tutup'
    ]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KantinGo - Pesan Kapan Saja</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .swiper-pagination-bullet-active { background-color: #f97316 !important; }
    </style>
</head>
<body class="bg-gray-50">
    
    <header class="fixed top-0 left-0 right-0 z-10 bg-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-orange-500">KantinGo</h1>
        </div>
    </header>

    <main class="pt-20 pb-24 px-4 container mx-auto">
        <!-- Banner Promosi -->
        <div class="swiper mySwiper rounded-xl overflow-hidden mb-6">
            <div class="swiper-wrapper">
                <div class="swiper-slide"><img src="https://placehold.co/600x300/f97316/white?text=Diskon+50%25" alt="Promo 1" class="w-full h-full object-cover"></div>
                <div class="swiper-slide"><img src="https://placehold.co/600x300/16a34a/white?text=Menu+Baru" alt="Promo 2" class="w-full h-full object-cover"></div>
                <div class="swiper-slide"><img src="https://placehold.co/600x300/3b82f6/white?text=Gratis+Ongkir" alt="Promo 3" class="w-full h-full object-cover"></div>
            </div>
            <div class="swiper-pagination"></div>
        </div>

        <!-- Daftar Kantin -->
        <div>
            <h2 class="text-xl font-bold text-gray-800 mb-4">Pilih Kantin</h2>
            <div class="space-y-4">
                <?php foreach ($kantin_list as $kantin): 
                    // Tentukan tujuan link berdasarkan status login
                    $link_tujuan = $is_logged_in ? "kantin_detail.php?id=" . $kantin['id'] : "login.php";
                ?>
                    <a href="<?php echo $link_tujuan; ?>" class="bg-white p-4 rounded-xl shadow-sm flex items-center space-x-4 transition hover:shadow-md active:scale-95">
                        <img src="<?php echo htmlspecialchars($kantin['logo_url']); ?>" alt="Logo Kantin" class="w-20 h-20 rounded-lg object-cover">
                        <div class="flex-1">
                            <h3 class="font-bold text-lg text-gray-900"><?php echo htmlspecialchars($kantin['nama_kantin']); ?></h3>
                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($kantin['deskripsi_singkat']); ?></p>
                            <div class="mt-2">
                                <?php if ($kantin['status'] == 'Buka'): ?>
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Buka</span>
                                <?php else: ?>
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Tutup</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <!-- Navigasi Bawah -->
    <div class="fixed bottom-0 left-0 right-0 z-10 border-t bg-white">
        <?php if ($is_logged_in): ?>
            <!-- Tampilan untuk pengguna yang sudah login -->
            <nav class="container mx-auto px-4 h-16 flex justify-around items-center">
                <a href="index.php" class="flex flex-col items-center justify-center text-orange-500"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z" /><path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z" /></svg><span class="text-xs font-medium">Beranda</span></a>
                <a href="keranjang.php" class="flex flex-col items-center justify-center text-gray-500 hover:text-orange-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c.51 0 .962-.343 1.087-.835l1.838-5.512A1.125 1.125 0 0018.618 6H4.23a1.125 1.125 0 00-1.087.835L2.25 3z" /></svg><span class="text-xs font-medium">Keranjang</span></a>
                <a href="riwayat.php" class="flex flex-col items-center justify-center text-gray-500 hover:text-orange-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span class="text-xs font-medium">Riwayat</span></a>
                <a href="akun.php" class="flex flex-col items-center justify-center text-gray-500 hover:text-orange-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg><span class="text-xs font-medium">Akun</span></a>
            </nav>
        <?php else: ?>
            <!-- Tampilan untuk pengguna yang belum login -->
            <div class="p-4">
                <a href="login.php" class="w-full block bg-orange-500 text-white text-center rounded-lg shadow-lg py-3 text-lg font-bold hover:bg-orange-600 transition">
                    Masuk untuk Memesan
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
      const swiper = new Swiper(".mySwiper", {
        loop: true,
        autoplay: { delay: 2500, disableOnInteraction: false },
        pagination: { el: ".swiper-pagination", clickable: true },
      });
    </script>
</body>
</html>

