<?php   
session_start(); // Mulai sesi
include 'koneksi.php'; // Koneksi database
require 'vendor/autoload.php'; // Memuat autoloader PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Asia/Jakarta'); // Set zona waktu untuk Indonesia

$message = ""; // Variabel untuk menyimpan pesan

// Proses login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Role untuk validasi

    // Periksa apakah username ada
    $stmt = $conn->prepare("SELECT * FROM akun WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Cek password terlebih dahulu
        if (password_verify($password, $row['password'])) {
            // Periksa apakah role sesuai
            if ($row['role'] === $role) {
                // Periksa apakah akun aktif
                if ($row['status'] == 'active') {
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = $row['role']; // Simpan role dalam sesi
                    header("Location: home.php"); // Redirect ke halaman home
                    exit();
                } else {
                    $_SESSION['error'] = "Akun Anda belum diaktifkan. Silakan cek email Anda!";
                }
            } else {
                $_SESSION['error'] = "Role tidak sesuai dengan username yang dimasukkan. Silakan pilih role yang benar!";
            }
        } else {
            $_SESSION['error'] = "Password yang dimasukkan salah. Silakan coba password lain!";
        }
    } else {
        $_SESSION['error'] = "Username tidak ditemukan. Silakan periksa kembali username Anda!";
    }
}

// Proses pendaftaran
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = $_POST['reg_username'];
    $password = $_POST['reg_password'];
    $email = $_POST['reg_email'];
    $role = $_POST['reg_role']; // Role yang dipilih saat registrasi

    // Hash password untuk keamanan
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Periksa apakah username atau email sudah digunakan
    $stmt = $conn->prepare("SELECT * FROM akun WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $existing = $result->fetch_assoc();
        if ($existing['username'] === $username) {
            $message = "Username sudah digunakan. Silahkan buat username lain!";
        } elseif ($existing['email'] === $email) {
            $message = "Email sudah digunakan. Silakan gunakan email lain!";
        }
    } else {
        // Jika username dan email unik, masukkan data ke database
        $stmt = $conn->prepare("INSERT INTO akun (username, password, email, role, status) VALUES (?, ?, ?, ?, 'inactive')");
        $stmt->bind_param("ssss", $username, $hashed_password, $email, $role);

        if ($stmt->execute()) {
            // Kirim email aktivasi
            $activation_code = md5($username . time()); // Kode unik untuk aktivasi
            $stmt = $conn->prepare("UPDATE akun SET activation_code = ? WHERE username = ?");
            $stmt->bind_param("ss", $activation_code, $username);
            $stmt->execute();

            // Kirim email menggunakan PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Pengaturan server
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'sinarmuinfo@gmail.com'; // Email untuk pengiriman
                $mail->Password   = 'mqyj xchd dyik ucfg'; // Ganti dengan sandi aplikasi Google
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;

                // Email pengirim
                $mail->setFrom('sinarmuinfo@gmail.com', 'Verifikasi Akun');
                $mail->addAddress($email, $username); // Email penerima

                // Konten
                $mail->isHTML(true);
                $mail->Subject = 'Verifikasi Akun';
                $mail->Body    = 'Selamat ' . $username . '! <br>Pendaftaran akun pada website SINARMU berhasil dilakukan. Silahkan verifikasi untuk aktivasi akun!</br>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="http://localhost/sisfo-masjid/login.php?code=' . $activation_code . '" 
                        style="display: inline-block; padding: 8px 18px; color: #ffffff; background-color: #16a34a; 
                        border-radius: 5px; text-decoration: none; font-weight: bold; font-size: 14px;">Verifikasi Akun</a>
                </div>';
                $mail->send();
                $message = "Akun berhasil dibuat. Silakan cek email Anda untuk aktivasi!";
            } catch (Exception $e) {
                $message = "Email tidak bisa dikirim. Error: {$mail->ErrorInfo}";
            }
        } else {
            $message = "Error: " . $stmt->error;
        }
    }

    // Tutup statement
    $stmt->close();
}

// Proses verifikasi email
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $stmt = $conn->prepare("SELECT * FROM akun WHERE activation_code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $created_at = date("Y-m-d H:i:s"); // Timestamp sesuai waktu Indonesia
        $stmt = $conn->prepare("UPDATE akun SET status = 'active', activation_code = NULL, created_at = ? WHERE activation_code = ?");
        $stmt->bind_param("ss", $created_at, $code);

        try {
            $stmt->execute();
            $message = "Verifikasi berhasil. Akun Anda telah aktif!";
        } catch (Exception $e) {
            $message = "Gagal melakukan verifikasi. Error: " . $e->getMessage();
        }
    } else {
        $message = "Kode verifikasi tidak valid!";
    }
    $stmt->close();
}
// Tutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome!</title>
    <link rel="stylesheet" href="style_login.css"> <!-- Link ke CSS -->
</head>
<style>
.register-link {
    text-align: center;
    color: #065f46;
    padding: 7px;
    font-size: 14px;
}

.register-link:hover {
    color: #16a34a; /* Warna saat hover */
}

.register-container {
        text-align: center;
    }
</style>
<body>
    <div class="container">
        <h2>Welcome!</h2>
        <form id="loginForm" method="POST" action="" style="display: block;">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="role">Role:</label>
                <select name="role" required>
                    <option value="">Pilih Role</option>
                    <option value="Pengurus">Pengurus</option>
                    <option value="Mubaligh">Mubaligh</option>
                </select>
            </div>
            <button type="submit" name="login">Log In</button>
            <button type="button" onclick="location.href='home_public.php'">Cancel</button> <!-- Tombol Cancel ke home_public -->
            <div class="register-container">
                <a href="#" onclick="showRegisterForm()" class="register-link">Register</a>
            </div>
        </form>

        <h4 id="registerHeader" style="display: none;">Create your account:</h4> <!-- Header Registrasi -->
        <form id="registerForm" method="POST" action="" style="display: none;">
            <div class="form-group">
                <label for="reg_username">Username:</label>
                <input type="text" name="reg_username" required>
            </div>
            <div class="form-group">
                <label for="reg_password">Password:</label>
                <input type="password" name="reg_password" required>
            </div>
            <div class="form-group">
                <label for="reg_email">Email:</label>
                <input type="email" name="reg_email" required>
            </div>
            <div class="form-group">
                <label for="reg_role">Role:</label>
                <select name="reg_role" required>
                    <option value="">Pilih Role</option>
                    <option value="Pengurus">Pengurus</option>
                    <option value="Mubaligh">Mubaligh</option>
                </select>
            </div>
            <button type="submit" name="register">Create</button> <!-- Tombol Daftar -->
            <button type="button" onclick="hideRegisterForm()">Cancel</button> <!-- Tombol Cancel -->
        </form>
    </div>

    <?php
    if (isset($_SESSION['error'])) {
        echo '<div class="popup" id="popup" style="display:block;">
                <p>' . $_SESSION['error'] . '</p>
                <button onclick="closePopup()">Close</button>
            </div>';
        unset($_SESSION['error']);
    }

    if ($message) {
        echo '<div class="popup" id="popup" style="display:block;">
                <p>' . $message . '</p>
                <button onclick="closePopup()">Close</button>
            </div>';
    }
    ?>

    <script>
        function showRegisterForm() {
            document.getElementById("loginForm").style.display = "none";
            document.getElementById("registerForm").style.display = "block";
            document.getElementById("registerHeader").style.display = "block";
        }

        function hideRegisterForm() {
            document.getElementById("loginForm").style.display = "block";
            document.getElementById("registerForm").style.display = "none";
            document.getElementById("registerHeader").style.display = "none";
        }

        function closePopup() {
            document.getElementById("popup").style.display = "none";
        }
    </script>
</body>
</html>
