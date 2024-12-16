<?php
session_start();
include 'koneksi.php';

date_default_timezone_set('Asia/Jakarta'); // Set zona waktu untuk Indonesia

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Anda harus login untuk mengakses halaman ini!'); window.location.href='login.php';</script>";
    exit;
}

// Ambil username dari session
$username = $_SESSION['username'];

// Ambil data masjid dari database
$masjid_query = $conn->query("SELECT id_masjid, nama_masjid FROM masjid");
$masjids = [];
if ($masjid_query) {
    while ($row = $masjid_query->fetch_assoc()) {
        $masjids[] = $row; // Simpan data masjid dalam array
    }
}

// Proses submit data (buat kegiatan baru)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_masjid = $_POST['id_masjid'];
    $kode_kegiatan = $_POST['kode_kegiatan'];
    $nama_kegiatan = $_POST['nama_kegiatan'];
    $deskripsi_kegiatan = $_POST['deskripsi_kegiatan'];
    $tempat_kegiatan = $_POST['tempat_kegiatan'];
    $waktu_kegiatan = $_POST['waktu_kegiatan'];
    $tanggal_kegiatan = $_POST['tanggal_kegiatan'];

    // Periksa apakah kombinasi kode_kegiatan, waktu_kegiatan, dan tanggal_kegiatan sudah ada
    $checkStmt = $conn->prepare("SELECT COUNT(*) AS count 
        FROM kegiatan 
        WHERE id_masjid = ? AND kode_kegiatan = ? AND waktu_kegiatan = ? AND tanggal_kegiatan = ?");
    $checkStmt->bind_param("isss", $id_masjid, $kode_kegiatan, $waktu_kegiatan, $tanggal_kegiatan);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        // Jika data sudah ada, tampilkan alert
        echo "<script>alert('Kegiatan sudah ada. Silahkan buat kegiatan lain!'); window.location.href='tambah_kegiatan.php';</script>";
    } else {
        // Jika data belum ada, lakukan proses INSERT
        $query = $conn->prepare("INSERT INTO kegiatan (id_masjid, kode_kegiatan, nama_kegiatan, deskripsi_kegiatan, tempat_kegiatan, waktu_kegiatan, tanggal_kegiatan, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $query->bind_param("issssss", $id_masjid, $kode_kegiatan, $nama_kegiatan, $deskripsi_kegiatan, $tempat_kegiatan, $waktu_kegiatan, $tanggal_kegiatan);

        if ($query->execute()) {
            echo "<script>alert('Kegiatan berhasil dibuat!'); window.location.href='manage_kegiatan.php';</script>";
        } else {
            echo "<script>alert('Kegiatan gagal dibuat!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Buat Kegiatan Baru</title>
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
    <h2>Buat Kegiatan Baru</h2>
    <form action="" method="post">
        <div class="form-group">
            <label for="id_masjid">Masjid :</label>
            <select name="id_masjid" id="id_masjid" required>
                <option value="">Pilih Masjid</option>
                <?php foreach ($masjids as $masjid): ?>
                    <option value="<?= htmlspecialchars($masjid['id_masjid']); ?>"><?= htmlspecialchars($masjid['nama_masjid']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="nama_kegiatan">Nama Kegiatan :</label>
            <select name="nama_kegiatan" id="nama_kegiatan" required>
                <option value="">Pilih Kegiatan</option>
                <option value="Khutbah Jum'at">Khutbah Jum'at</option>
                <option value="Pengajian">Pengajian</option>
                <option value="Kajian">Kajian</option>
                <option value="Tabligh Akbar">Tabligh Akbar</option>
                <option value="Tahun Baru Hijriah">Tahun Baru Hijriah</option>
                <option value="Isra Mi'raj">Isra Mi'raj</option>
                <option value="Maulid Nabi Muhammad">Maulid Nabi Muhammad</option>
                <option value="Idul Fitri">Idul Fitri</option>
                <option value="Idul Adha">Idul Adha</option>
            </select>
        </div>
        <div class="form-group">
            <label for="kode_kegiatan">Kode Kegiatan :</label>
            <select name="kode_kegiatan" id="kode_kegiatan" required>
                <option value="">Pilih Kode Kegiatan</option>
            </select>
        </div>
        <div class="form-group">
            <label for="tempat_kegiatan">Tempat :</label>
            <select name="tempat_kegiatan" id="tempat_kegiatan" required>
                <option value="">Pilih Tempat Kegiatan</option>
                <?php foreach ($masjids as $masjid): ?>
                    <option value="<?= htmlspecialchars($masjid['nama_masjid']); ?>"><?= htmlspecialchars($masjid['nama_masjid']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="deskripsi_kegiatan">Deskripsi :</label>
            <textarea name="deskripsi_kegiatan" id="deskripsi_kegiatan" required></textarea>
        </div>
        <div class="form-group">
            <label for="waktu_kegiatan">Waktu :</label>
            <input type="time" name="waktu_kegiatan" id="waktu_kegiatan" required>
        </div>
        <div class="form-group">
            <label for="tanggal_kegiatan">Tanggal :</label>
            <input type="date" name="tanggal_kegiatan" id="tanggal_kegiatan" required>
        </div>

        <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Create</button>
    </form>
        <!-- Tombol Cancel -->
        <button type="button" onclick="location.href='manage_kegiatan.php'" class="w-full bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 mt-4">Cancel</button>
    </div>

    <script>
    document.getElementById('nama_kegiatan').addEventListener('change', function() {
        const kodeKegiatanDropdown = document.getElementById('kode_kegiatan');
        const selectedKegiatan = this.value;

        // Clear existing options in kode_kegiatan
        kodeKegiatanDropdown.innerHTML = '<option value="">Pilih Kode Kegiatan</option>';

        // Define logic for populating kode_kegiatan
        const kodeOptions = {
            "Khutbah Jum'at": ["KMG"],
            "Pengajian": ["KMG", "KBL", "KTH"],
            "Kajian": ["KMG", "KBL", "KTH"],
            "Tabligh Akbar": ["KBL", "KTH"],
            "Tahun Baru Hijriah": ["KTH"],
            "Isra Mi'raj": ["KTH"],
            "Maulid Nabi Muhammad": ["KTH"],
            "Idul Fitri": ["KTH"],
            "Idul Adha": ["KTH"]
        };

        // Add logic to provide descriptive labels for kode kegiatan
        const kodeDescriptions = {
            "KMG": "KMG (Mingguan)",
            "KBL": "KBL (Bulanan)",
            "KTH": "KTH (Tahunan)"
        };

        // Populate kode_kegiatan options based on the selected nama_kegiatan
        if (kodeOptions[selectedKegiatan]) {
            kodeOptions[selectedKegiatan].forEach(kode => {
                const option = document.createElement('option');
                option.value = kode;
                option.textContent = kodeDescriptions[kode] || kode;
                kodeKegiatanDropdown.appendChild(option);
            });
        }
    });
    </script>
</body>
</html> 