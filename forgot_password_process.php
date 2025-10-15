<?php
session_start();

require 'vendor/phpmailer/src/Exception.php';
require 'vendor/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php';
    
    if (!isset($mysqli) || $mysqli->connect_error) {
        die("Koneksi ke database gagal. Periksa konfigurasi dalam file db.php.");
    }

    $email = $_POST['email'];

    // Cek apakah email ada di database
    $stmt = $mysqli->prepare("SELECT id, nama FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Buat token reset
        $token = bin2hex(random_bytes(50));
        
        $stmt = $mysqli->prepare("UPDATE users SET reset_token = ?, reset_token_expires_at = NOW() + INTERVAL 1 HOUR WHERE id = ?");
        $stmt->bind_param("si", $token, $user['id']);
        $stmt->execute();

        // Kirim email dengan PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Opsi untuk mengatasi error SSL di localhost
            $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));
            
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'drovsynn@gmail.com';
            $mail->Password   = 'wuxg hjfe epod sjky';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('no-reply@kantingo.com', 'KantinGo');
            $mail->addAddress($email, $user['nama']);

            $mail->isHTML(true);
            $mail->Subject = 'Reset Password Akun KantinGo Anda';
            $reset_link = "http://localhost/kantingo/reset_password.php?token=" . $token;
            $mail->Body    = "Hai " . htmlspecialchars($user['nama']) . ",<br><br>Silakan klik tautan berikut untuk mereset password Anda: <a href='$reset_link'>Reset Password</a><br><br>Tautan ini hanya berlaku selama 1 jam.";
            
            $mail->send();
        } catch (Exception $e) {
            // Tidak melakukan apa-apa agar pengguna tidak tahu apakah email itu ada atau tidak
        }
    }
    // Selalu redirect ke halaman sukses untuk mencegah orang menebak-nebak email yang terdaftar
    header("Location: forgot_password.php?success=1");
    exit();
}

