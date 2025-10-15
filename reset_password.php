<?php
include 'db.php';
session_start();

$token = $_GET['token'] ?? '';
$error = '';
$token_valid = false;

// Periksa koneksi
if (!isset($mysqli) || $mysqli->connect_error) {
    $error = "Tidak dapat terhubung ke database.";
} else {
    if (empty($token)) {
        $error = "Token tidak ditemukan atau tautan tidak valid.";
    } else {
        // Cek apakah token ada dan belum kedaluwarsa
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expires_at > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $token_valid = true;
        } else {
            $error = "Token tidak valid atau sudah kedaluwarsa. Silakan minta tautan baru.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - KantinGo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <h1 class="text-4xl font-extrabold text-orange-600">KantinGo</h1>
            <h2 class="mt-6 text-center text-2xl font-bold text-gray-900">
                Atur Ulang Password Anda
            </h2>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <?php if (!$token_valid): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                        <p class="font-bold">Gagal Memuat Halaman</p>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    </div>
                    <div class="text-center mt-4">
                        <a href="forgot_password.php" class="font-medium text-orange-600 hover:text-orange-500">Minta tautan baru &rarr;</a>
                    </div>
                <?php else: ?>
                    <?php if(isset($_GET['error'])): ?>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                            <?php
                                $errorMessage = '';
                                switch ($_GET['error']) {
                                    case 'passwordmismatch':
                                        $errorMessage = 'Password dan konfirmasi password tidak cocok. Silakan coba lagi.';
                                        break;
                                    case 'samepassword':
                                        $errorMessage = 'Password baru tidak boleh sama dengan password lama Anda.';
                                        break;
                                    case 'invalidtoken':
                                        $errorMessage = 'Token yang Anda gunakan tidak valid. Silakan minta tautan baru.';
                                        break;
                                    default:
                                        $errorMessage = 'Terjadi kesalahan yang tidak diketahui.';
                                        break;
                                }
                                echo '<p>' . htmlspecialchars($errorMessage) . '</p>';
                            ?>
                        </div>
                    <?php endif; ?>
                    <form class="space-y-6" action="reset_password_process.php" method="POST">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                        
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                            <div class="mt-1 relative">
                                <input id="password" name="password" type="password" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm pr-10">
                                <button type="button" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500" onclick="togglePasswordVisibility('password')">
                                    <svg id="eyeIcon-password" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    <svg id="eyeSlashIcon-password" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                            <div class="mt-1 relative">
                                <input id="confirm_password" name="confirm_password" type="password" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm pr-10">
                                <button type="button" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500" onclick="togglePasswordVisibility('confirm_password')">
                                    <svg id="eyeIcon-confirm_password" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    <svg id="eyeSlashIcon-confirm_password" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                Simpan Password Baru
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
        function togglePasswordVisibility(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const eyeIcon = document.getElementById('eyeIcon-' + fieldId);
            const eyeSlashIcon = document.getElementById('eyeSlashIcon-' + fieldId);

            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            eyeIcon.classList.toggle('hidden');
            eyeSlashIcon.classList.toggle('hidden');
        }
    </script>
</body>
</html>

