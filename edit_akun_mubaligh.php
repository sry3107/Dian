<?php
session_start(); // Mulai sesi
include 'koneksi.php'; // Koneksi database
require 'vendor/autoload.php'; // Memuat autoloader PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Asia/Jakarta'); // Set zona waktu untuk Indonesia

$username = $_SESSION['username'] ?? null;
if (!$username) {
    echo "<script>alert('Anda belum login!'); window.location.href='login.php';</script>";
    exit;
}

// Ambil id_akun berdasarkan username
$queryAkun = $conn->prepare("SELECT id_akun FROM akun WHERE username = ?");
$queryAkun->bind_param("s", $username);
$queryAkun->execute();
$resultAkun = $queryAkun->get_result();
if ($resultAkun->num_rows === 0) {
    die("Akun tidak ditemukan.");
}

$akunData = $resultAkun->fetch_assoc();
$id_akun = $akunData['id_akun'];

// Ambil data akun dari tabel
$query = $conn->prepare("SELECT id_akun, username, email, password FROM akun WHERE id_akun = ?");
$query->bind_param("i", $id_akun);
$query->execute();
$result = $query->get_result();
$data_akun = $result->fetch_assoc();

// Variabel pesan peringatan
$username_message = '';
$email_message = '';

// Proses update data akun
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_account'])) {
    $username = $_POST['username'] ?? $data_akun['username'];
    $email = $_POST['email'] ?? $data_akun['email'];
    $password = $_POST['password'] ?? null;

    $hashed_password = $password ? password_hash($password, PASSWORD_DEFAULT) : $data_akun['password'];

    // Validasi username dan email
    $usernameCheckQuery = $conn->prepare("SELECT id_akun FROM akun WHERE username = ? AND id_akun != ?");
    $usernameCheckQuery->bind_param("si", $username, $id_akun);
    $usernameCheckQuery->execute();
    $usernameExists = $usernameCheckQuery->get_result()->num_rows > 0;

    $emailCheckQuery = $conn->prepare("SELECT id_akun FROM akun WHERE email = ? AND id_akun != ?");
    $emailCheckQuery->bind_param("si", $email, $id_akun);
    $emailCheckQuery->execute();
    $emailExists = $emailCheckQuery->get_result()->num_rows > 0;

    // Pesan error jika username atau email sudah digunakan
    if ($usernameExists) {
        $username_message = "Username sudah digunakan oleh akun lain.";
    }
    if ($emailExists) {
        $email_message = "Email sudah terdaftar dengan akun lain.";
    }

    // Update data jika tidak ada error
    if (!$username_message && !$email_message) {
        if ($email !== $data_akun['email']) {
            // Update dengan pengiriman ulang aktivasi jika email berubah
            $activation_code = md5($username . time());
            $stmt = $conn->prepare("UPDATE akun SET username = ?, password = ?, email = ?, activation_code = ?, status = 'inactive', updated_at = NOW() WHERE id_akun = ?");
            $stmt->bind_param("ssssi", $username, $hashed_password, $email, $activation_code, $id_akun);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                sendActivationEmail($email, $username, $activation_code);
                echo "<script>alert('Data berhasil diperbarui! Silakan cek email untuk aktivasi.'); window.location.href='login.php';</script>";
            }
        } else {
            // Update tanpa pengiriman ulang aktivasi
            $stmt = $conn->prepare("UPDATE akun SET username = ?, password = ?, updated_at = NOW() WHERE id_akun = ?");
            $stmt->bind_param("ssi", $username, $hashed_password, $id_akun);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "<script>alert('Data berhasil diperbarui!'); window.location.href='login.php';</script>";
            }
        }
        $stmt->close();
    }
}

// Fungsi untuk mengirim email aktivasi
function sendActivationEmail($email, $username, $activation_code) {
    global $conn; // Menggunakan koneksi database global

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
        $mail->Body    = 'Selamat ' . $username . '! <br>Email berhasil diperbarui pada website SINARMU, silahkan lakukan verifikasi ulang untuk aktivasi akun!</br>
            <div style="text-align: center; margin-top: 20px;">
                <a href="http://localhost/sisfo-masjid/login.php?code=' . $activation_code . '" 
                    style="display: inline-block; padding: 8px 18px; color: #ffffff; background-color: #16a34a; 
                    border-radius: 5px; text-decoration: none; font-weight: bold; font-size: 14px;">Verifikasi Akun</a>
            </div>';
        // Kirim email
        if (!$mail->send()) {
            throw new Exception('Email tidak bisa dikirim. Error: ' . htmlspecialchars($mail->ErrorInfo));
        }
        
    } catch (Exception $e) {
        echo "<script>alert('Gagal mengirim email aktivasi: " . htmlspecialchars($e->getMessage()) . "');</script>";
    }
}

// Hapus data akun
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_akun = $_GET['id'];
    $deleteQuery = $conn->prepare("DELETE FROM akun WHERE id_akun = ?");
    $deleteQuery->bind_param("i", $id_akun);

    if ($deleteQuery->execute()) {
        echo "<script>alert('Akun Anda telah dihapus. Silahkan log in/registrasi ulang!'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus akun Anda. Error: " . htmlspecialchars($deleteQuery->error) . "');</script>";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail Profil</title>
<style>
/* Style untuk body */
body {
    font-family: Arial, sans-serif;
    background-color: #fff3ee;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.text {
    color: gray;
}

/* Style untuk container */
.container {
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 300px;
}

/* Style untuk judul */
h2 {
    text-align: center;
    color: #333;
}

/* Style untuk grup form */
.form-group {
    margin-bottom: 15px;
}

/* Style untuk label */
label {
    display: block;
    margin-bottom: 5px;
    color: #555;
}

/* Style untuk input teks dan password */
input[type="text"],
input[type="password"],
input[type="email"] {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
    font-size: 14px;
    color: #333;
}

/* Menambahkan efek fokus untuk input */
input[type="text"]:focus,
input[type="password"]:focus,
input[type="email"]:focus {
    border-color: #16a34a;
    outline: none;
    box-shadow: 0 0 5px #065f46;
}

/* Style untuk combobox (select role) */
select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
    font-size: 14px;
    color: #333;
    background-color: #fff;
}

/* Menambahkan efek fokus untuk select */
select:focus {
    border-color: #16a34a;
    outline: none;
    box-shadow: 0 0 5px #065f46;
}

/* Style untuk tombol */
button {
    width: 30%;
    text-align: center;
    padding: 7px;
    background-color: #065f46;
    color: #fff3ee;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
    margin: 10px 0;
    display: block;
    margin-left: auto;
    margin-right: auto;
}

/* Efek hover untuk tombol */
button:hover {
    background-color: #16a34a;
}
</style>
</head>
<body>
<div class="container">
    <h2>Edit Akun</h2>
    <form action="" method="post">
        <input type="hidden" name="id_akun" value="<?= htmlspecialchars($data_akun['id_akun']); ?>">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" value="<?= htmlspecialchars($data_akun['username'] ?? ''); ?>" required>
            <?php if (!empty($username_message)) { echo "<small style='color: red;'>" . htmlspecialchars($username_message) . "</small>"; } ?>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" placeholder="opsional"><small class="text">Lewati jika tidak ingin mengubah password!</small>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($data_akun['email'] ?? ''); ?>" required>
            <?php if (!empty($email_message)) { echo "<small style='color: red;'>" . htmlspecialchars($email_message) . "</small>"; } ?>
        </div>
        <button type="submit" name="update_account">Update</button>
    </form>
    <button type="button" onclick="confirmDelete(<?php echo $data_akun['id_akun']; ?>)" class="mt-4">Delete</button> 
        <script>
            function confirmDelete(id){
                var confirmation = confirm('Apakah Anda yakin ingin menghapus data ini? Penghapusan akan menghilangkan data terkait akun!');
                if (confirmation) {
                    window.location.href='edit_akun_mubaligh.php?id=<?php echo $data_akun['id_akun']; ?>&action=delete'
                    } else{
                        window.location.href='pengaturan_akun_mubaligh.php';
                    }
                }
        </script>
    <button type="button" onclick="location.href='pengaturan_akun_mubaligh.php'" class="mt-4">Cancel</button>
</div>
</body>
</html> 