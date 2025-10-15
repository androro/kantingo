<?php
session_start();

// Jika sudah login, redirect ke index.php
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KantinGo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Inter', sans-serif; } 
        .form-input {
            appearance: none;
            border-radius: 0.5rem;
            border-width: 1px;
            --tw-border-opacity: 1;
            border-color: rgb(209 213 219 / var(--tw-border-opacity));
            width: 100%;
            padding: 0.75rem 1rem;
            line-height: 1.25rem;
            font-size: 0.875rem;
            --tw-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --tw-shadow-colored: 0 1px 2px 0 var(--tw-shadow-color);
            box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
        }
        .form-input:focus {
            outline: 2px solid transparent;
            outline-offset: 2px;
            --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
            --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color);
            box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
            --tw-ring-opacity: 1;
            --tw-ring-color: rgb(249 115 22 / var(--tw-ring-opacity));
            --tw-border-opacity: 1;
            border-color: rgb(249 115 22 / var(--tw-border-opacity));
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-sm w-full bg-white shadow-xl rounded-2xl p-8 space-y-6">
            <!-- Logo dan Judul -->
            <div class="text-center">
                <h1 class="text-4xl font-extrabold text-orange-500">KantinGo</h1>
                <p class="mt-2 text-md text-gray-600">Login ke akun Anda</p>
            </div>

            <!-- Pesan Peringatan (Error/Success) -->
            <div>
                <?php
                if (isset($_SESSION['message'])) {
                    echo '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert"><p>' . htmlspecialchars($_SESSION['message']) . '</p></div>';
                    unset($_SESSION['message']);
                }
                if (isset($_GET['error'])) {
                    $errorMessage = '';
                    switch ($_GET['error']) {
                        case 'wrongpassword':
                            $errorMessage = 'Password yang Anda masukkan salah.';
                            break;
                        case 'nouser':
                            $errorMessage = 'Username tidak ditemukan.';
                            break;
                        case 'notverified':
                            $errorMessage = 'Akun Anda belum diverifikasi. Silakan cek email Anda.';
                            break;
                        default:
                            $errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                            break;
                    }
                    echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert"><p>' . htmlspecialchars($errorMessage) . '</p></div>';
                }
                ?>
            </div>

            <!-- Form Login -->
            <form class="space-y-6" action="login_process.php" method="POST">
                <div class="space-y-2">
                    <label for="username" class="text-sm font-medium text-gray-700">Username</label>
                    <input id="username" name="username" type="text" required class="form-input">
                </div>

                <div class="space-y-2">
                    <label for="password" class="text-sm font-medium text-gray-700">Password</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" required class="form-input pr-10">
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <svg id="eyeSlashIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                        </button>
                    </div>
                    <div class="text-right">
                        <a href="forgot_password.php" class="text-sm font-medium text-orange-600 hover:text-orange-500">Lupa Password?</a>
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        Login
                    </button>
                </div>
            </form>

            <p class="text-center text-sm text-gray-600">
                Belum punya akun?
                <a href="register.php" class="font-medium text-orange-600 hover:text-orange-500">
                    Daftar di sini
                </a>
            </p>
        </div>
    </div>
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeSlashIcon = document.getElementById('eyeSlashIcon');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            eyeIcon.classList.toggle('hidden');
            eyeSlashIcon.classList.toggle('hidden');
        });
    </script>
</body>
</html>

