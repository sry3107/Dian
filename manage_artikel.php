<?php 
session_start();
include 'koneksi.php'; // File koneksi ke database

// Periksa apakah pengguna memiliki peran "Pengurus"
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Pengurus') {
    header('Location: home.php');
    exit();
}

// Proses penambahan artikel baru
if (isset($_POST['submit'])) {
    $judul_artikel = $_POST['judul_artikel'];
    $deskripsi_artikel = $_POST['deskripsi_artikel'];
    $tempat_artikel = $_POST['tempat_artikel'];
    $tanggal_artikel = $_POST['tanggal_artikel'];
    $link_artikel = $_POST['link_artikel'];

    // Query untuk menambahkan artikel baru
    $query = "INSERT INTO artikel (judul_artikel, deskripsi_artikel, tempat_artikel, tanggal_artikel, link_artikel, created_at) VALUES (?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param('sssss', $judul_artikel, $deskripsi_artikel, $tempat_artikel, $tanggal_artikel, $link_artikel);

        // Eksekusi statement
        if ($stmt->execute()) {
            echo "<script>alert('Data artikel berhasil ditambahkan!'); window.location.href='home.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan data artikel!');</script>";
        }

        $stmt->close(); // Tutup statement setelah digunakan
    } else {
        echo "<script>alert('Terjadi kesalahan pada query!');</script>";
    }
}

$conn->close(); // Tutup koneksi setelah semua proses selesai
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Artikel</title>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
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
</head>
<body>
    <div class="container mx-auto p-6">
        <h2>Kelola Artikel</h2>

        <!-- Form Input Artikel -->
        <form action="manage_artikel.php" method="POST" class="mb-6">
            <div class="form-group">
                <label for="judul_artikel">Judul Artikel:</label>
                <input type="text" name="judul_artikel" id="judul_artikel" required>
            </div>
            <div class="form-group">
                <label for="deskripsi_artikel">Deskripsi:</label>
                <textarea name="deskripsi_artikel" id="deskripsi_artikel" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="tempat_artikel">Tempat:</label>
                <input type="text" name="tempat_artikel" id="tempat_artikel" required>
            </div>
            <div class="form-group">
                <label for="tanggal_artikel">Tanggal:</label>
                <input type="date" name="tanggal_artikel" id="tanggal_artikel" required>
            </div>
            <div class="form-group">
                <label for="link_artikel">Link Artikel:</label>
                <input type="url" name="link_artikel" id="link_artikel" required>
            </div>
            <button type="submit" name="submit">
                <i class="fas fa-save"></i>Create
            </button>
            <button type="button" onclick="location.href='home.php'">Cancel</button>
        </form>
    </div>
</body>
</html>