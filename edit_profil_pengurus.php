<?php
session_start();
include 'koneksi.php'; // File koneksi ke database

date_default_timezone_set('Asia/Jakarta'); // Set timezone untuk Indonesia

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Anda harus login untuk mengakses halaman ini!'); window.location.href='login.php';</script>";
    exit;
}

// Ambil data akun sesuai sesi login
$username = $_SESSION['username'];
$akun_query = $conn->prepare("SELECT id_akun, username FROM akun WHERE username = ?");
$akun_query->bind_param("s", $username);
$akun_query->execute();
$akun_result = $akun_query->get_result();
$akuns = $akun_result->fetch_all(MYSQLI_ASSOC);

// Ambil data masjid
$masjid_query = $conn->query("SELECT id_masjid, nama_masjid FROM masjid");
$masjids = $masjid_query->fetch_all(MYSQLI_ASSOC);

// Ambil data pengurus jika ID tersedia
$id_pengurus = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$data = [];
if ($id_pengurus) {
    $query = $conn->prepare("SELECT * FROM pengurus WHERE id_pengurus = ?");
    $query->bind_param("i", $id_pengurus);
    $query->execute();
    $result = $query->get_result();
    $data = $result->fetch_assoc();
}

// Ambil jabatan yang belum digunakan, kecuali jabatan yang saat ini dipilih oleh pengurus
$current_jabatan = $data['jabatan_pengurus'] ?? null;

$unused_jabatan_query = $conn->prepare("SELECT jabatan 
    FROM (
        SELECT 'Ketua' AS jabatan 
        UNION SELECT 'Wakil' 
        UNION SELECT 'Sekretaris' 
        UNION SELECT 'Bendahara'
    ) AS all_jabatan 
    WHERE jabatan NOT IN (
        SELECT jabatan_pengurus 
        FROM pengurus 
        WHERE jabatan_pengurus IS NOT NULL AND jabatan_pengurus != COALESCE(?, ''))
");
$unused_jabatan_query->bind_param("s", $current_jabatan);
$unused_jabatan_query->execute();
$result = $unused_jabatan_query->get_result();
$unused_jabatan = $result->fetch_all(MYSQLI_ASSOC);


// Proses Insert/Update data pengurus
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_akun = $_POST['id_akun'];
    $id_masjid = $_POST['id_masjid'];
    $nama_pengurus = $_POST['nama_pengurus'];
    $jabatan_pengurus = $_POST['jabatan_pengurus'];
    $alamat_pengurus = $_POST['alamat_pengurus'];
    $no_telepon_pengurus = $_POST['no_telepon_pengurus'];

    if (empty($id_akun) || empty($id_masjid) || empty($nama_pengurus) || empty($jabatan_pengurus) || empty($alamat_pengurus) || empty($no_telepon_pengurus)) {
        echo "<script>alert('Semua field harus diisi!');</script>";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $no_telepon_pengurus)) {
        echo "<script>alert('Nomor telepon harus berupa angka dan memiliki panjang 10-15 digit!');</script>";
    } else {
        if ($id_pengurus) {
            $updateQuery = $conn->prepare("UPDATE pengurus 
                SET id_akun = ?, id_masjid = ?, nama_pengurus = ?, jabatan_pengurus = ?, alamat_pengurus = ?, no_telepon_pengurus = ?, updated_at = NOW() 
                WHERE id_pengurus = ?");
            $updateQuery->bind_param(
                "iissssi", 
                $id_akun, $id_masjid, $nama_pengurus, 
                $jabatan_pengurus, $alamat_pengurus, 
                $no_telepon_pengurus, $id_pengurus
            );

            if ($updateQuery->execute()) {
                echo "<script>alert('Profil berhasil diperbarui!'); window.location.href='detail_profil_pengurus.php';</script>";
            } else {
                echo "<script>alert('Profil gagal diperbarui!');</script>";
            }
        } else {
            $insertQuery = $conn->prepare("INSERT INTO pengurus (id_akun, id_masjid, nama_pengurus, jabatan_pengurus, alamat_pengurus, no_telepon_pengurus, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $insertQuery->bind_param("iissss", $id_akun, $id_masjid, $nama_pengurus, $jabatan_pengurus, $alamat_pengurus, $no_telepon_pengurus);

            if ($insertQuery->execute()) {
                echo "<script>alert('Data pengurus berhasil ditambahkan!'); window.location.href='detail_profil_pengurus.php';</script>";
            } else {
                echo "<script>alert('Data pengurus gagal ditambahkan!');</script>";
            }
        }
    }
}

// Proses Hapus Data Pengurus
if (isset($_GET['action']) && $_GET['action'] === 'delete' && $id_pengurus) {
    $deleteQuery = $conn->prepare("DELETE FROM pengurus WHERE id_pengurus = ?");
    $deleteQuery->bind_param("i", $id_pengurus);

    if ($deleteQuery->execute()) {
        echo "<script>alert('Data pengurus berhasil dihapus!'); window.location.href='detail_profil_pengurus.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data pengurus! Error: " . $conn->error . "');</script>";
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
    <h2><?php echo ($id_pengurus) ? 'Edit' : 'Input'; ?> Profil Pengurus</h2>
    <form action="" method="post">
        <!-- Dropdown ID Akun -->
        <div class="form-group">
            <label for="id_akun">Akun :</label>
                <select name="id_akun" id="id_akun" required>
                <option value="">Pilih Id Akun</option>
                    <?php foreach ($akuns as $akun): ?>
                        <option value="<?= htmlspecialchars($akun['id_akun']); ?>" <?= (isset($data['id_akun']) && $data['id_akun'] == $akun['id_akun']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($akun['username']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Dropdown ID Masjid -->
            <div class="form-group">
                <label for="id_masjid">Masjid :</label>
                <select name="id_masjid" id="id_masjid" required>
                    <option value="">Pilih Masjid</option>
                    <?php foreach ($masjids as $masjid): ?>
                        <option value="<?= htmlspecialchars($masjid['id_masjid']); ?>" <?= (isset($data['id_masjid']) && $data['id_masjid'] == htmlspecialchars($masjid['id_masjid'])) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($masjid['nama_masjid']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Input Nama -->
            <div class="form-group">
                <label for="nama_pengurus">Nama :</label>
                <input type="text" name="nama_pengurus" id="nama_pengurus" value="<?= htmlspecialchars($data['nama_pengurus'] ?? ''); ?>" required>
            </div>

            <!-- Dropdown Jabatan -->
            <div class="form-group">
                <label for="jabatan_pengurus">Jabatan :</label>
                <select name="jabatan_pengurus" id="jabatan_pengurus" required>
                    <option value="">Pilih Jabatan</option>
                    <?php foreach ($unused_jabatan as $jabatan): ?>
                        <option value="<?= htmlspecialchars($jabatan['jabatan']); ?>" <?= (isset($current_jabatan) && $current_jabatan == htmlspecialchars($jabatan['jabatan'])) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($jabatan['jabatan']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Input Alamat -->
            <div class="form-group">
                <label for="alamat_pengurus">Alamat :</label>
                <textarea name="alamat_pengurus" id="alamat_pengurus" required><?= htmlspecialchars($data['alamat_pengurus'] ?? ''); ?></textarea>
            </div>

            <!-- Input Nomor Telepon -->
            <div class="form-group">
                <label for="no_telepon_pengurus">Nomor Telepon :</label>
                <input type="text" name="no_telepon_pengurus" id="no_telepon_pengurus" value="<?= htmlspecialchars($data['no_telepon_pengurus'] ?? ''); ?>" required>
            </div>
        <button type="submit"><?= ($id_pengurus) ? 'Update' : 'Create'; ?></button>
    </form>
        <?php if ($id_pengurus): ?>
            <button type="button" onclick="confirmDelete(<?= $id_pengurus; ?>)">Delete</button>
            <script>
                function confirmDelete(id) {
                    if (confirm('Apakah Anda yakin ingin menghapus data ini? Penghapusan akan menghilangkan data terkait pengurus!')) {
                        window.location.href = 'edit_profil_pengurus.php?id=' + id + '&action=delete';
                    }
                }
            </script>
        <?php endif; ?> 
        <button type="button" onclick="location.href='detail_profil_pengurus.php'">Cancel</button>
</div>
</body>
</html> 