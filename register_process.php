<?php
session_start();

// Memasukkan file PHPMailer
require 'vendor/phpmailer/src/Exception.php';
require 'vendor/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Memasukkan koneksi database
    include 'db.php';
    
    // Periksa koneksi
    if (!isset($mysqli) || $mysqli->connect_error) {
        die("Koneksi ke database gagal: " . $mysqli->connect_error);
    }

    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi dasar
    if ($password !== $confirm_password) {
        header("Location: register.php?error=passwordmismatch");
        exit();
    }

    // Cek apakah username atau email sudah ada menggunakan prepared statements
    $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: register.php?error=userexists");
        exit();
    }
    $stmt->close();

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $verification_token = bin2hex(random_bytes(50));
    $is_verified = 0; // 0 = belum diverifikasi

    // Simpan user baru ke database
    $stmt = $mysqli->prepare("INSERT INTO users (nama, username, email, password, verification_token, is_verified) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $nama, $username, $email, $hashed_password, $verification_token, $is_verified);

    if ($stmt->execute()) {
        // Pendaftaran berhasil, kirim email verifikasi
        $mail = new PHPMailer(true);
        try {
            // Opsi untuk mengatasi error SSL di localhost
            $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'drovsynn@gmail.com'; // GANTI DENGAN EMAIL ANDA
            $mail->Password   = 'wuxg hjfe epod sjky';   // GANTI DENGAN APP PASSWORD ANDA
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('no-reply@kantingo.com', 'KantinGo');
            $mail->addAddress($email, $nama);

            $mail->isHTML(true);
            $mail->Subject = 'Verifikasi Akun KantinGo Anda';
            $verification_link = "http://localhost/kantingo/verify.php?token=" . $verification_token;
            $mail->Body    = "Hai " . htmlspecialchars($nama) . ",<br><br>Silakan klik tautan ini untuk memverifikasi akun Anda: <a href='$verification_link'>Verifikasi Akun</a>";
            
            $mail->send();
            header("Location: verify-notice.php");
            exit();

        } catch (Exception $e) {
            // Jika email gagal dikirim, bisa ditambahkan log error
            header("Location: register.php?error=mailfail");
            exit();
        }
    } else {
        header("Location: register.php?error=dberror");
        exit();
    }
    $stmt->close();
    $mysqli->close();
}
?>

