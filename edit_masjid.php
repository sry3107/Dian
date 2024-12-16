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

// Ambil data akun dari database sesuai username
$akun_query = $conn->prepare("SELECT id_akun, username FROM akun WHERE username = ?");
$akun_query->bind_param("s", $username);
$akun_query->execute();
$akun_result = $akun_query->get_result();

// Cek apakah data akun ditemukan
if ($akun_result->num_rows === 0) {
    echo "<script>alert('Akun tidak ditemukan!'); window.location.href='login.php';</script>";
    exit;
}

// Ambil data masjid jika ID tersedia
$id_masjid = $_GET['id'] ?? null;
$data = [];
if ($id_masjid) {
    $query = $conn->prepare("SELECT * FROM masjid WHERE id_masjid = ?");
    $query->bind_param("i", $id_masjid);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
    } else {
        echo "<script>alert('Data tidak ditemukan!'); window.location.href='manage_masjid.php';</script>";
        exit;
    }
}

// Proses input atau edit data masjid
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_masjid = $_POST['nama_masjid'] ?? '';
    $alamat_masjid = $_POST['alamat_masjid'] ?? '';
    $tahun_berdiri = $_POST['tahun_berdiri'] ?? '';

    // Validasi input
    if (empty($nama_masjid) || empty($alamat_masjid) || empty($tahun_berdiri)) {
        echo "<script>alert('Semua field harus diisi!');</script>";
    } else {
        if ($id_masjid) {
            // Update data masjid
            $updateQuery = $conn->prepare("UPDATE masjid SET nama_masjid=?, alamat_masjid=?, tahun_berdiri=?, updated_at=NOW() WHERE id_masjid=?");
            $updateQuery->bind_param("sssi", $nama_masjid, $alamat_masjid, $tahun_berdiri, $id_masjid);

            if ($updateQuery->execute()) {
                echo "<script>alert('Data masjid berhasil diperbarui!'); window.location.href='manage_masjid.php';</script>";
            } else {
                echo "<script>alert('Data masjid gagal diperbarui!');</script>";
            }
        } else {
            // Input data masjid
            $insertQuery = $conn->prepare("INSERT INTO masjid (nama_masjid, alamat_masjid, tahun_berdiri, created_at) VALUES (?, ?, ?, NOW())");
            $insertQuery->bind_param("ssi", $nama_masjid, $alamat_masjid, $tahun_berdiri);

            if ($insertQuery->execute()) {
                echo "<script>alert('Data masjid berhasil dibuat!'); window.location.href='manage_masjid.php';</script>";
            } else {
                echo "<script>alert('Data masjid gagal dibuat!');</script>";
            }
        }
    }
}

// Hapus data masjid
if (isset($_GET['action']) && $_GET['action'] === 'delete' && $id_masjid) {
    $deleteQuery = $conn->prepare("DELETE FROM masjid WHERE id_masjid = ?");
    $deleteQuery->bind_param("i", $id_masjid);

    if ($deleteQuery->execute()) {
        echo "<script>alert('Data masjid berhasil dihapus!'); window.location.href='manage_masjid.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data masjid!');</script>";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?php echo ($id_masjid) ? 'Edit' : 'Input'; ?> Masjid</title>
    <link rel="stylesheet" href="style_menu.css">
</head>
<body>
<body>
    <div class="container">
        <h2><?php echo ($id_masjid) ? 'Edit' : 'Input'; ?> Data Masjid</h2>
        <form action="" method="post">
            <div class="form-group">
                <label for="nama_masjid">Nama Masjid:</label>
                <input type="text" name="nama_masjid" id="nama_masjid" value="<?= htmlspecialchars($data['nama_masjid'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="alamat_masjid">Alamat Masjid:</label>
                <textarea name="alamat_masjid" id="alamat_masjid" required><?= htmlspecialchars($data['alamat_masjid'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="tahun_berdiri">Tahun Berdiri:</label>
                <input type="number" name="tahun_berdiri" id="tahun_berdiri" value="<?= htmlspecialchars($data['tahun_berdiri'] ?? ''); ?>" required min="1900" max="<?= date('Y'); ?>">
            </div>

            <button type="submit"><?= ($id_masjid) ? 'Update' : 'Create'; ?></button>
        </form>
        <?php if ($id_masjid): ?>
            <button type="button" onclick="confirmDelete(<?= $id_masjid; ?>)">Delete</button>
            <script>
                function confirmDelete(id) {
                    if (confirm('Apakah Anda yakin ingin menghapus data ini? Penghapusan akan menghilangkan data terkait masjid!')) {
                        window.location.href = 'edit_masjid.php?id=' + id + '&action=delete';
                    }
                }
            </script>
        <?php endif; ?>
        <button type="button" onclick="location.href='manage_masjid.php'">Cancel</button>
    </div>
</body>
</html>