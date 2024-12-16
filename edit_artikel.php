<?php  
session_start();
include 'koneksi.php'; // File koneksi ke database

date_default_timezone_set('Asia/Jakarta'); // Set timezone untuk Indonesia

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Anda harus login untuk mengakses halaman ini!'); window.location.href='login.php';</script>";
    exit;
}

// Ambil username dari session
$username = $_SESSION['username'];

// Mendapatkan ID artikel dari URL
$id_artikel = $_GET['id'] ?? null;

// Validasi ID
if (!$id_artikel || !filter_var($id_artikel, FILTER_VALIDATE_INT)) {
    echo "<script>alert('ID Artikel tidak valid!'); window.location.href='home.php';</script>";
    exit;
}

// Ambil data artikel untuk form edit
$query = $conn->prepare("SELECT * FROM artikel WHERE id_artikel = ?");
$query->bind_param("i", $id_artikel);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
} else {
    echo "<script>alert('Data artikel tidak ditemukan!'); window.location.href='home.php';</script>";
    exit;
}

// Proses update artikel
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul_artikel = $_POST['judul_artikel'];
    $deskripsi_artikel = $_POST['deskripsi_artikel'];
    $tempat_artikel = $_POST['tempat_artikel'];
    $tanggal_artikel = $_POST['tanggal_artikel'];
    $link_artikel = $_POST['link_artikel'];
    $updated_at = date("Y-m-d H:i:s"); // Timestamp saat data diperbarui

    // Query untuk update artikel
    $updateQuery = $conn->prepare("UPDATE artikel SET judul_artikel = ?, deskripsi_artikel = ?, tempat_artikel = ?, tanggal_artikel = ?, link_artikel = ?, updated_at = ? WHERE id_artikel = ?");
    $updateQuery->bind_param("ssssssi", $judul_artikel, $deskripsi_artikel, $tempat_artikel, $tanggal_artikel, $link_artikel, $updated_at, $id_artikel);

    if ($updateQuery->execute()) {
        echo "<script>alert('Artikel berhasil diperbarui!'); window.location.href='home.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui artikel!');</script>";
    }
}

// Proses Hapus Data Artikel
if (isset($_GET['action']) && $_GET['action'] === 'delete' && $id_artikel) {
    $deleteQuery = $conn->prepare("DELETE FROM artikel WHERE id_artikel = ?");
    $deleteQuery->bind_param("i", $id_artikel);

    if ($deleteQuery->execute()) {
        echo "<script>alert('Data artikel berhasil dihapus!'); window.location.href='home.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data artikel! Error: " . $conn->error . "');</script>";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Artikel</title>
</head>
<style>
/* Style for input fields and container */
body {
    font-family: Arial, sans-serif;
    background-color: #fff3ee;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.container {
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 300px;
}

h2 {
    text-align: center;
    color: #333;
}

.form-group {
    display: flex;
    flex-direction: column;
    margin-bottom: 15px;
}

label {
    display: block;
    margin-bottom: 5px;
    color: #555;
}

/* Style untuk input teks dan textarea */
input[type="text"],
input[type="number"],
input[type="time"],
input[type="date"],
input[type="url"],
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
input[type="url"]:focus,
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
</style>
<body>
    <div class="container">
        <h2>Edit Artikel</h2>
        <form action="" method="post">
            <div class="form-group">
                <label for="judul">Judul Artikel:</label>
                <input type="text" name="judul_artikel" id="judul" value="<?= htmlspecialchars($data['judul_artikel']); ?>" required>
            </div>
            <div class="form-group">
                <label for="deskripsi">Deskripsi:</label>
                <textarea name="deskripsi_artikel" id="deskripsi" rows="4" required><?= htmlspecialchars($data['deskripsi_artikel']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="tempat">Tempat:</label>
                <input type="text" name="tempat_artikel" id="tempat" value="<?= htmlspecialchars($data['tempat_artikel']); ?>" required>
            </div>
            <div class="form-group">
                <label for="tanggal">Tanggal:</label>
                <input type="date" name="tanggal_artikel" id="tanggal" value="<?= htmlspecialchars($data['tanggal_artikel']); ?>" required>
            </div>
            <div class="form-group">
                <label for="link">Link Artikel:</label>
                <input type="url" name="link_artikel" id="link" value="<?= htmlspecialchars($data['link_artikel']); ?>" required>
            </div>
            <button type="submit">Update</button>
        </form>
        <?php if ($id_artikel): ?>
            <button type="button" onclick="confirmDelete(<?= $id_artikel; ?>)">Delete</button>
            <script>
                function confirmDelete(id) {
                    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                        window.location.href = 'edit_artikel.php?id=' + id + '&action=delete';
                    }
                }
            </script>
        <?php endif; ?> 
        <button type="button" onclick="location.href='home.php'">Cancel</button>
    </div>
</body>
</html>