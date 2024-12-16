<?php
session_start();
include 'koneksi.php'; // File koneksi ke database

date_default_timezone_set('Asia/Jakarta'); // Set timezone untuk Indonesia

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Anda harus login untuk mengakses halaman ini!'); window.location.href='login.php';</script>";
    exit;
}

$username = $_SESSION['username'];

// Ambil data akun dari database dengan role='Mubaligh' dan created_at IS NULL
$query = $conn->prepare("SELECT a.id_akun, a.username 
    FROM akun a 
    LEFT JOIN mubaligh m ON a.id_akun = m.id_akun 
    WHERE a.role = 'Mubaligh' AND m.created_at IS NULL AND a.status = 'active'");
$query->execute();
$result = $query->get_result();
$akuns = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $akuns[] = $row;
    }
}

// Ambil data masjid dari database
$masjid_query = $conn->query("SELECT id_masjid, nama_masjid FROM masjid");
$masjids = [];
if ($masjid_query) {
    while ($row = $masjid_query->fetch_assoc()) {
        $masjids[] = $row;
    }
}

// Ambil data mubaligh jika ID tersedia
$id_mubaligh = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$data = [];
if ($id_mubaligh) {
    $queryMubaligh = $conn->prepare("SELECT * FROM mubaligh WHERE id_mubaligh = ?");
    $queryMubaligh->bind_param("i", $id_mubaligh);
    $queryMubaligh->execute();
    $resultMubaligh = $queryMubaligh->get_result();

    if ($resultMubaligh->num_rows > 0) {
        $data = $resultMubaligh->fetch_assoc();
    } else {
        echo "<script>alert('Data tidak ditemukan!'); window.location.href='manage_mubaligh.php';</script>";
        exit;
    }
}

// Proses Tambah Data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$id_mubaligh) {
    // Ambil data dari form
    $id_akun = filter_input(INPUT_POST, 'id_akun', FILTER_VALIDATE_INT);
    $id_masjid = filter_input(INPUT_POST, 'id_masjid', FILTER_VALIDATE_INT);
    $nama_mubaligh = filter_input(INPUT_POST, 'nama_mubaligh', FILTER_SANITIZE_STRING);
    $alamat_mubaligh = filter_input(INPUT_POST, 'alamat_mubaligh', FILTER_SANITIZE_STRING);
    $no_telepon_mubaligh = filter_input(INPUT_POST, 'no_telepon_mubaligh', FILTER_SANITIZE_STRING);

    // Validasi input
    if (!$id_akun || !$id_masjid || !$nama_mubaligh || !$alamat_mubaligh || !$no_telepon_mubaligh) {
        echo "<script>alert('Semua field harus diisi dengan benar!');</script>";
    } else {
        // Insert query
        $insertQuery = $conn->prepare("INSERT INTO mubaligh (id_akun, id_masjid, nama_mubaligh, alamat_mubaligh, no_telepon_mubaligh, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $insertQuery->bind_param("iisss", $id_akun, $id_masjid, $nama_mubaligh, $alamat_mubaligh, $no_telepon_mubaligh);

        if ($insertQuery->execute()) {
            echo "<script>alert('Profil berhasil dibuat!'); window.location.href='manage_mubaligh.php?id=" . $conn->insert_id . "';</script>";
        } else {
            echo "<script>alert('Profil gagal dibuat! Error: " . $conn->error . "');</script>";
        }
    }
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
    display: flex; /* Menggunakan flexbox untuk grup form */
    flex-direction: column; /* Mengatur elemen dalam kolom */
    margin-bottom: 15px; /* Jarak antar grup */
}

/* Style untuk label */
label {
    display: block; /* Memastikan label tampil sebagai blok */
    margin-bottom: 5px; /* Jarak antara label dan input */
    color: #555; /* Warna label */
}

/* Style untuk tabel profil */
.profile-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.profile-table th,
.profile-table td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

.profile-table th {
    width: 40%;
    text-align: left;
    color: #065f46;
}

.profile-table td {
    width: 60%;
    color: #333;
}

/* Gaya untuk ikon */
.icon {
    margin-right: 10px;
    color: #065f46;
}

/* Style untuk input teks dan textarea */
input[type="text"],
textarea {
    width: 100%; /* Memastikan lebar penuh */
    padding: 12px; /* Padding dalam input */
    border: 1px solid #ccc; /* Border abu-abu */
    border-radius: 5px; /* Sudut melengkung */
    box-sizing: border-box; /* Memastikan padding tidak menambah lebar total */
    font-size: 14px; /* Ukuran font yang konsisten */
    color: #333; /* Warna teks dalam input */
}

/* Menambahkan efek fokus untuk input */
input[type="text"]:focus,
textarea:focus {
    border-color: #16a34a; /* Warna border saat fokus */
    outline: none; /* Menghilangkan outline default */
    box-shadow: 0 0 5px #065f46; /* Bayangan saat fokus */
}

select {
    width: 100%; /* Memastikan lebar penuh */
    padding: 12px; /* Padding dalam input */
    border: 1px solid #ccc; /* Border abu-abu */
    border-radius: 5px; /* Sudut melengkung */
    box-sizing: border-box; /* Memastikan padding tidak menambah lebar total */
    font-size: 14px; /* Ukuran font yang konsisten */
    color: #333; /* Warna teks dalam input */
}

select:hover {
    border-color: #16a34a; /* Warna border saat fokus */
    outline: none; /* Menghilangkan outline default */
    box-shadow: 0 0 5px #065f46; /* Bayangan saat fokus */
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

/* Style untuk teks terakhir diperbarui */
.last-updated {
    text-align: center; 
    font-size: 14px; 
    color: #777; 
    margin-top: 10px; 
}

.detail {
    margin-bottom: 20px;
}
</style>
</head>
<body>
    <div class="container">
        <h2>Profil Mubaligh</h2>
        <form action="" method="post">
        <div class="form-group">
            <label for="id_akun">Akun :</label>
                <select name="id_akun" id="id_akun" required>
                <option value="">Pilih Id Akun</option>
                    <?php foreach ($akuns as $akun): ?>
                        <option value="<?= htmlspecialchars($akun['id_akun']); ?>" <?= (isset($data['id_akun']) && $data['id_akun'] == htmlspecialchars($akun['id_akun'])) ? 'selected' : ''; ?>><?= htmlspecialchars($akun['username']); ?></option>
                    <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="id_masjid">Masjid :</label>
            <select name="id_masjid" id="id_masjid" required>
                <option value="">Pilih Masjid</option>
                <?php foreach ($masjids as $masjid): ?>
                    <option value="<?= htmlspecialchars($masjid['id_masjid']); ?>" <?= (isset($data['id_masjid']) && $data['id_masjid'] == htmlspecialchars($masjid['id_masjid'])) ? 'selected' : ''; ?>><?= htmlspecialchars($masjid['nama_masjid']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="nama_mubaligh">Nama :</label>
            <input type="text" name="nama_mubaligh" id="nama_mubaligh" value="<?= htmlspecialchars($data['nama_mubaligh'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="alamat_mubaligh">Alamat :</label>
            <textarea name="alamat_mubaligh" id="alamat_mubaligh" required><?= htmlspecialchars($data['alamat_mubaligh'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="no_telepon_mubaligh">Nomor Telepon :</label>
            <input type="text" name="no_telepon_mubaligh" id="no_telepon_mubaligh" value="<?= htmlspecialchars($data['no_telepon_mubaligh'] ?? ''); ?>" required>
        </div>
        <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Create</button>
    </form>
    <button type="button" onclick="location.href='manage_mubaligh.php'" class="mt-4">Cancel</button>
</div>
</body>
</html>