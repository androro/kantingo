<?php
session_start();
include 'db.php';

// Redirect jika bukan POST request atau keranjang kosong
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

// Pastikan user_id ada jika pelanggan harus login
$user_id = $_SESSION['user_id'] ?? null;

$kantin_id = (int)$_POST['kantin_id'];
$total_amount = (int)$_POST['total_amount'];
$payment_method = $_POST['payment_method'];
$cart_items = $_SESSION['cart'];

// Mulai transaksi database
$mysqli->begin_transaction();

try {
    // 1. Generate Order Code
    // Format: KTG-DDMMYY-HHMMSS (KantinGo-Tanggal-Waktu)
    $order_code = 'KTG-' . date('dmy-His');
    
    // Pastikan order code unik (walaupun kemungkinan bentrok sangat kecil)
    $stmt_check_code = $mysqli->prepare("SELECT id FROM orders WHERE order_code = ?");
    $stmt_check_code->bind_param("s", $order_code);
    $stmt_check_code->execute();
    while ($stmt_check_code->get_result()->num_rows > 0) {
        $order_code = 'KTG-' . date('dmy-His') . rand(0, 9); // Tambah angka acak jika bentrok
        $stmt_check_code->bind_param("s", $order_code);
        $stmt_check_code->execute();
    }
    $stmt_check_code->close();

    // 2. Insert into orders table
    $stmt_order = $mysqli->prepare("INSERT INTO orders (user_id, kantin_id, order_code, total_amount, payment_method, order_status) VALUES (?, ?, ?, ?, ?, 'Pending')");
    $stmt_order->bind_param("iisds", $user_id, $kantin_id, $order_code, $total_amount, $payment_method);
    $stmt_order->execute();
    $order_id = $mysqli->insert_id; // Ambil ID pesanan yang baru dibuat
    $stmt_order->close();

    // 3. Insert into order_items table
    $stmt_item = $mysqli->prepare("INSERT INTO order_items (order_id, menu_item_name, price, quantity) VALUES (?, ?, ?, ?)");
    foreach ($cart_items as $item) {
        $stmt_item->bind_param("isii", $order_id, $item['nama_menu'], $item['harga'], $item['quantity']);
        $stmt_item->execute();
    }
    $stmt_item->close();

    // Commit transaksi jika semua berhasil
    $mysqli->commit();

    // 4. Kosongkan keranjang setelah pesanan berhasil
    unset($_SESSION['cart']);
    unset($_SESSION['cart_kantin_id']);

    // 5. Redirect ke halaman konfirmasi pesanan
    header("Location: order_confirmation.php?order_id=" . $order_id);
    exit();

} catch (mysqli_sql_exception $exception) {
    // Rollback transaksi jika ada error
    $mysqli->rollback();
    error_log("Order Process Error: " . $exception->getMessage()); // Catat error ke log
    header("Location: keranjang.php?error=orderfailed"); // Kembali ke keranjang dengan pesan error
    exit();
}

$mysqli->close();
?>
