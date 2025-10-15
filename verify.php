<?php
session_start();
include 'db.php';

// Fungsi untuk menampilkan pesan dengan UI yang konsisten
function show_message($message, $title, $is_error = false) {
    $themeColor = $is_error ? 'red' : 'green';
    $icon = $is_error ? 
        '<svg class="w-12 h-12 mx-auto text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' : 
        '<svg class="w-12 h-12 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';

    return "
        <div class='text-center'>
            $icon
            <h2 class='mt-4 text-2xl font-bold text-gray-800'>$title</h2>
            <p class='mt-2 text-gray-600'>$message</p>
        </div>
    ";
}

$page_output = '';

// Periksa koneksi terlebih dahulu
if (!isset($mysqli) || $mysqli->connect_error) {
    $page_output = show_message('Tidak dapat terhubung ke server. Silakan coba lagi nanti.', 'Koneksi Gagal', true);
} else {
    if (isset($_GET['token'])) {
        $token = $_GET['token'];

        // Cari user dengan token yang sesuai
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE verification_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $user_id = $user['id'];

            // Update status verifikasi dan hapus token
            $stmt_update = $mysqli->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?");
            $stmt_update->bind_param("i", $user_id);
            
            if ($stmt_update->execute()) {
                $_SESSION['message'] = "Akun Anda berhasil diverifikasi! Silakan login.";
                header("Location: login.php");
                exit();
            } else {
                $page_output = show_message('Terjadi kesalahan saat memperbarui status akun Anda.', 'Update Gagal', true);
            }
            $stmt_update->close();

        } else {
            $page_output = show_message('Token tidak valid atau sudah kedaluwarsa. Silakan daftar ulang jika perlu.', 'Token Tidak Valid', true);
        }
        $stmt->close();
    } else {
        $page_output = show_message('Tautan verifikasi tidak lengkap atau tidak ada token.', 'Token Hilang', true);
    }
    $mysqli->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Akun - KantinGo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white shadow-lg rounded-lg p-8">
            <?php echo $page_output; ?>
        </div>
    </div>
</body>
</html>

