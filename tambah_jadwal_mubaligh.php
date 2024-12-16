<?php
session_start();
include 'koneksi.php'; // File koneksi ke database

date_default_timezone_set('Asia/Jakarta'); // Set zona waktu untuk Indonesia

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Anda harus login untuk mengakses halaman ini!'); window.location.href='login.php';</script>";
    exit;
}

// Ambil username dari session
$username = $_SESSION['username'];

// Ambil id_akun berdasarkan username
$queryAkun = $conn->prepare("SELECT id_akun FROM akun WHERE username = ?");
$queryAkun->bind_param("s", $username);
$queryAkun->execute();
$resultAkun = $queryAkun->get_result();

if ($resultAkun->num_rows > 0) {
    $akunData = $resultAkun->fetch_assoc();
    $id_akun = $akunData['id_akun']; // Mendapatkan id_akun

    // Ambil data mubaligh dari tabel berdasarkan id_akun
    $queryMubaligh = $conn->prepare("SELECT id_mubaligh, nama_mubaligh FROM mubaligh WHERE id_akun = ?");
    $queryMubaligh->bind_param("i", $id_akun); // Mengikat parameter id_akun
    $queryMubaligh->execute();
    $resultMubaligh = $queryMubaligh->get_result();

    // Jika ada data, ambil data mubaligh
    if ($resultMubaligh->num_rows > 0) {
        $data = $resultMubaligh->fetch_assoc();
    } 
} else {
    echo "Akun tidak ditemukan.";
    exit;
}

// Ambil data jadwal dari database
$query = $conn->prepare("SELECT j.*, m.nama_masjid, p.nama_pengurus, mu.nama_mubaligh, k.nama_kegiatan FROM jadwal j
                        INNER JOIN masjid m ON j.id_masjid = m.id_masjid
                        INNER JOIN pengurus p ON j.id_pengurus = p.id_pengurus
                        INNER JOIN mubaligh mu ON j.id_mubaligh = mu.id_mubaligh
                        INNER JOIN kegiatan k ON j.id_kegiatan = k.id_kegiatan");
$query->execute();
$result = $query->get_result();

$data_jadwal = $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Proses penambahan jadwal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_masjid = $_POST['id_masjid'];
    $id_pengurus = $_POST['id_pengurus'];
    $id_mubaligh = $_POST['id_mubaligh'];
    $id_kegiatan = $_POST['id_kegiatan'];
    $kode_jadwal = $_POST['kode_jadwal']; 

    // Perbaiki query SQL dengan menutup tanda kurung pada NOW()
    $stmt = $conn->prepare("INSERT INTO jadwal (id_masjid, id_pengurus, id_mubaligh, id_kegiatan, kode_jadwal, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiiis", $id_masjid, $id_pengurus, $id_mubaligh, $id_kegiatan, $kode_jadwal);

    if ($stmt->execute()) {
        echo "<script>alert('Jadwal berhasil ditambahkan!'); window.location.href='jadwal_mubaligh.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan jadwal!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Buat Jadwal Baru</title>
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
input[type="number"],
input[type="time"],
input[type="date"],
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
input[type="number"]:focus,
input[type="time"]:focus,
input[type="date"]:focus,
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
        <h2>Buat Jadwal Baru</h2>
        <!-- Form Tambah Jadwal -->
        <form method="POST" class="space-y-4">
            <div class="form-group">
                <label for="id_masjid">Masjid :</label>
                <select name="id_masjid" id="id_masjid" required>
                    <option value="">Pilih Masjid</option>
                    <?php
                    $masjidQuery = $conn->query("SELECT id_masjid, nama_masjid FROM masjid");
                    while ($row = $masjidQuery->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row['id_masjid']) . "'>" . htmlspecialchars($row['nama_masjid']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_pengurus">Pengurus :</label>
                <select name="id_pengurus" id="id_pengurus" required>
                    <option value="">Pilih Pengurus</option>
                    <?php
                    $pengurusQuery = $conn->query("SELECT id_pengurus, nama_pengurus, jabatan_pengurus FROM pengurus WHERE jabatan_pengurus IN ('Ketua', 'Sekretaris')");
                    while ($row = $pengurusQuery->fetch_assoc()) {      
                        echo "<option value='" . htmlspecialchars($row['id_pengurus']) . "'>" . htmlspecialchars($row['nama_pengurus']) . " (" . htmlspecialchars($row['jabatan_pengurus']) . ")</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_mubaligh">Mubaligh :</label>
                <select name="id_mubaligh" id="id_mubaligh" required>
                    <option value="">Pilih Mubaligh</option>
                    <?php
                    // Menampilkan hanya Mubaligh yang sesuai dengan id_akun yang terhubung dengan pengguna
                    if ($resultMubaligh->num_rows > 0) {
                        echo "<option value='" . htmlspecialchars($data['id_mubaligh']) . "'>" . htmlspecialchars($data['nama_mubaligh']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_kegiatan">Kegiatan :</label>
                <select name="id_kegiatan" id="id_kegiatan" required>
                    <option value="">Pilih Kegiatan</option>
                    <?php
                        $kegiatanQuery = $conn->query("SELECT id_kegiatan, nama_kegiatan, tanggal_kegiatan 
                                                    FROM kegiatan 
                                                    WHERE nama_kegiatan='Khutbah Jum\'at' 
                                                    AND id_kegiatan NOT IN (SELECT id_kegiatan FROM jadwal) 
                                                    ORDER BY tanggal_kegiatan");
                        while ($row = $kegiatanQuery->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($row['id_kegiatan']) . "'>" . 
                                htmlspecialchars($row['nama_kegiatan']) . " (" . 
                                htmlspecialchars($row['tanggal_kegiatan']) . ") " . "</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="kode_jadwal">Kode Kegiatan :</label>
                <select name="kode_jadwal" id="kode_jadwal" required>
                    <option value="">Pilih Kode Jadwal</option>
                    <option value="JKJ">Jadwal Khutbah Jum'at (JKJ)</option>
                </select>
            </div>
            <button type="submit">Create</button>
        </form>

        <!-- Tombol Cancel -->
        <button type="button" onclick="location.href='jadwal_mubaligh.php'">Cancel</button>
    </div>
</body>
</html>
