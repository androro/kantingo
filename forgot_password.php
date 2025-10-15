<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - KantinGo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style> 
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f8fafc;
        } 
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full bg-white p-8 rounded-2xl shadow-lg">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-orange-500">KantinGo</h1>
            <p class="mt-2 text-gray-600">Lupa Password Anda?</p>
            <p class="mt-2 text-sm text-gray-500">
                Masukkan email Anda di bawah untuk menerima tautan reset.
            </p>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                <p class="font-bold">Tautan Terkirim!</p>
                <p>Jika email Anda terdaftar, silakan periksa kotak masuk.</p>
            </div>
        <?php endif; ?>

        <form class="space-y-6" action="forgot_password_process.php" method="POST">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <div class="mt-1">
                    <input id="email" name="email" type="email" autocomplete="email" required class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                </div>
            </div>
            
            <div>
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    Kirim Tautan Reset
                </button>
            </div>
        </form>
        <p class="text-sm text-center text-gray-600">
            Ingat passwordnya? 
            <a href="login.php" class="font-medium text-orange-500 hover:text-orange-600">
                Login di sini
            </a>
        </p>
    </div>
</body>
</html>

