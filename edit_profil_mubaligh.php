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

// Ambil data mubaligh jika ID tersedia
$id_mubaligh = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$data = [];
if ($id_mubaligh) {
    $query = $conn->prepare("SELECT * FROM mubaligh WHERE id_mubaligh = ?");
    $query->bind_param("i", $id_mubaligh);
    $query->execute();
    $result = $query->get_result();
    $data = $result->fetch_assoc();
}

// Proses Insert/Update data mubaligh
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_akun = $_POST['id_akun'];
    $id_masjid = $_POST['id_masjid'];
    $nama_mubaligh = $_POST['nama_mubaligh'];
    $alamat_mubaligh = $_POST['alamat_mubaligh'];
    $no_telepon_mubaligh = $_POST['no_telepon_mubaligh'];

    if (empty($id_akun) || empty($id_masjid) || empty($nama_mubaligh) || empty($alamat_mubaligh) || empty($no_telepon_mubaligh)) {
        echo "<script>alert('Semua field harus diisi!');</script>";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $no_telepon_mubaligh)) {
        echo "<script>alert('Nomor telepon harus berupa angka dan memiliki panjang 10-15 digit!');</script>";
    } else {
        if ($id_mubaligh) {
            $updateQuery = $conn->prepare("UPDATE mubaligh 
                SET id_akun = ?, id_masjid = ?, nama_mubaligh = ?, alamat_mubaligh = ?, no_telepon_mubaligh = ?, updated_at = NOW() 
                WHERE id_mubaligh = ?");
            $updateQuery->bind_param(
                "iisssi", 
                $id_akun, $id_masjid, $nama_mubaligh, $alamat_mubaligh, $no_telepon_mubaligh, $id_mubaligh
            );

            if ($updateQuery->execute()) {
                echo "<script>alert('Profil berhasil diperbarui!'); window.location.href='detail_profil_mubaligh.php';</script>";
            } else {
                echo "<script>alert('Profil gagal diperbarui!');</script>";
            }
        } else {
            $insertQuery = $conn->prepare("INSERT INTO mubaligh (id_akun, id_masjid, nama_mubaligh, alamat_mubaligh, no_telepon_mubaligh, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
            $insertQuery->bind_param("iisss", $id_akun, $id_masjid, $nama_mubaligh, $alamat_mubaligh, $no_telepon_mubaligh);

            if ($insertQuery->execute()) {
                echo "<script>alert('Data mubaligh berhasil ditambahkan!'); window.location.href='detail_profil_mubaligh.php';</script>";
            } else {
                echo "<script>alert('Data mubaligh gagal ditambahkan!');</script>";
            }
        }
    }
}

// Proses Hapus Data Mubaligh
if (isset($_GET['action']) && $_GET['action'] === 'delete' && $id_mubaligh) {
    $deleteQuery = $conn->prepare("DELETE FROM mubaligh WHERE id_mubaligh = ?");
    $deleteQuery->bind_param("i", $id_mubaligh);

    if ($deleteQuery->execute()) {
        echo "<script>alert('Data mubaligh berhasil dihapus!'); window.location.href='detail_profil_mubaligh.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data mubaligh! Error: " . $conn->error . "');</script>";
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
    <h2><?php echo ($id_mubaligh) ? 'Edit' : 'Tambah'; ?> Profil Mubaligh</h2>
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
                        <option value="<?= htmlspecialchars($masjid['id_masjid']); ?>" <?= (isset($data['id_masjid']) && $data['id_masjid'] == htmlspecialchars($masjid['id_masjid'])) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($masjid['nama_masjid']); ?>
                        </option>
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

        <button type="submit"><?= ($id_mubaligh) ? 'Update' : 'Create'; ?></button>
    </form>
        <?php if ($id_mubaligh): ?>
            <button type="button" onclick="confirmDelete(<?= $id_mubaligh; ?>)">Delete</button>
            <script>
                function confirmDelete(id) {
                    if (confirm('Apakah Anda yakin ingin menghapus data ini? Penghapusan akan menghilangkan data terkait mubaligh!')) {
                        window.location.href = 'edit_profil_mubaligh.php?id=' + id + '&action=delete';
                    }
                }
            </script>
        <?php endif; ?> 
        <button type="button" onclick="location.href='detail_profil_mubaligh.php'">Cancel</button>
</div>
</body>
</html>