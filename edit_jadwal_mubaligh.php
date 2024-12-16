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

// Mendapatkan ID kegiatan dari URL
$id_jadwal = $_GET['id'] ?? null;

// Validasi ID
if (!$id_jadwal || !filter_var($id_jadwal, FILTER_VALIDATE_INT)) {
    echo "<script>alert('ID Kegiatan tidak valid!'); window.location.href='jadwal_mubaligh.php';</script>";
    exit;
}

// Query untuk mendapatkan id_mubaligh berdasarkan username, dengan menghubungkan tabel akun dan mubaligh
$queryMubaligh = $conn->prepare("SELECT m.id_mubaligh FROM akun a
    INNER JOIN mubaligh m ON a.id_akun = m.id_akun
    WHERE a.username = ?");
$queryMubaligh->bind_param("s", $username);
$queryMubaligh->execute();
$resultMubaligh = $queryMubaligh->get_result();
if ($resultMubaligh->num_rows > 0) {
    $dataMubaligh = $resultMubaligh->fetch_assoc();
    $id_mubaligh = $dataMubaligh['id_mubaligh']; // id_mubaligh untuk sesi login
} else {
    echo "<script>alert('Mubaligh tidak ditemukan!'); window.location.href='login.php';</script>";
    exit;
}

// Query untuk mengambil data jadwal berdasarkan id_jadwal dan id_mubaligh
$query = $conn->prepare("SELECT j.id_jadwal, j.kode_jadwal, j.updated_at, m.id_masjid, m.nama_masjid, p.id_pengurus, p.nama_pengurus, mu.id_mubaligh, mu.nama_mubaligh, k.id_kegiatan, k.nama_kegiatan
FROM jadwal j
INNER JOIN masjid m ON j.id_masjid = m.id_masjid
INNER JOIN pengurus p ON j.id_pengurus = p.id_pengurus
INNER JOIN mubaligh mu ON j.id_mubaligh = mu.id_mubaligh
INNER JOIN kegiatan k ON j.id_kegiatan = k.id_kegiatan
WHERE j.id_jadwal = ? AND j.id_mubaligh = ?");
$query->bind_param("ii", $id_jadwal, $id_mubaligh);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
} else {
    echo "<script>alert('Data jadwal tidak ditemukan atau tidak sesuai!'); window.location.href='jadwal_mubaligh.php';</script>";
    exit;
}

// Proses update jadwal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_masjid = $_POST['id_masjid'];
    $id_pengurus = $_POST['id_pengurus'];
    $id_mubaligh = $_POST['id_mubaligh'];
    $id_kegiatan = $_POST['id_kegiatan'];
    $kode_jadwal = $_POST['kode_jadwal'];

    // Periksa apakah data jadwal_id ada
    $checkQuery = $conn->prepare("SELECT * FROM jadwal WHERE id_jadwal = ? AND id_mubaligh = ?");
    $checkQuery->bind_param("ii", $id_jadwal, $id_mubaligh);
    $checkQuery->execute();
    $checkResult = $checkQuery->get_result();

    if ($checkResult->num_rows > 0) {
        // Jika data ada, lakukan update
        $stmt = $conn->prepare("UPDATE jadwal SET id_masjid = ?, id_pengurus = ?, id_mubaligh = ?, id_kegiatan = ?, kode_jadwal = ?, updated_at = NOW() WHERE id_jadwal = ?");
        $stmt->bind_param("iiiisi", $id_masjid, $id_pengurus, $id_mubaligh, $id_kegiatan, $kode_jadwal, $id_jadwal);

        if ($stmt->execute()) {
            echo "<script>alert('Jadwal berhasil diperbarui!'); window.location.href='jadwal_mubaligh.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui jadwal!');</script>";
        }
    } else {
        echo "<script>alert('Jadwal tidak ditemukan atau tidak sesuai dengan data mubaligh Anda!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Buat Jadwal Baru</title>
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

input[type="text"],
input[type="number"],
input[type="time"],
input[type="date"],
textarea,
select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
    font-size: 14px;
    color: #333;
}

button {
    width: 30%;
    padding: 7px;
    background-color: #065f46;
    color: #fff3ee;
    border-radius: 5px;
    cursor: pointer;
    margin: 10px 0;
    display: block;
    margin-left: auto;
    margin-right: auto;
}

button:hover {
    background-color: #16a34a;
}
</style>
</style>
</head>
<body>
    <div class="container">
        <h2>Edit Jadwal</h2>
        <!-- Form Edit Jadwal -->
        <form method="POST" class="space-y-4">
            <div class="form-group">
                <label for="id_masjid">Masjid :</label>
                <select name="id_masjid" id="id_masjid" required>
                    <option value="">Pilih Masjid</option>
                    <?php
                    // Loop through the masjid options, marking the current one as selected
                    $masjidQuery = $conn->query("SELECT id_masjid, nama_masjid FROM masjid");
                    while ($row = $masjidQuery->fetch_assoc()) {
                        $selected = $row['id_masjid'] == $data['id_masjid'] ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($row['id_masjid']) . "' $selected>" . htmlspecialchars($row['nama_masjid']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_pengurus">Pengurus :</label>
                <select name="id_pengurus" id="id_pengurus" required>
                    <option value="">Pilih Pengurus</option>
                    <?php
                    // Loop through pengurus options, marking the current one as selected
                    $pengurusQuery = $conn->query("SELECT id_pengurus, nama_pengurus, jabatan_pengurus FROM pengurus WHERE jabatan_pengurus IN ('Ketua', 'Sekretaris')");
                    while ($row = $pengurusQuery->fetch_assoc()) {
                        $selected = $row['id_pengurus'] == $data['id_pengurus'] ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($row['id_pengurus']) . "' $selected>" . htmlspecialchars($row['nama_pengurus']) . " (" . htmlspecialchars($row['jabatan_pengurus']) . ")</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_mubaligh">Mubaligh :</label>
                <select name="id_mubaligh" id="id_mubaligh" required>
                    <option value="">Pilih Mubaligh</option>
                    <?php
                    // Fetch only the logged-in mubaligh options from the database
                    $mubalighQuery = $conn->prepare("SELECT id_mubaligh, nama_mubaligh FROM mubaligh WHERE id_mubaligh = ?");
                    $mubalighQuery->bind_param("i", $id_mubaligh);  // Use the logged-in mubaligh ID
                    $mubalighQuery->execute();
                    $mubalighResult = $mubalighQuery->get_result();
                    while ($row = $mubalighResult->fetch_assoc()) {
                        // Mark the current mubaligh as selected
                        $selected = $row['id_mubaligh'] == $data['id_mubaligh'] ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($row['id_mubaligh']) . "' $selected>" . htmlspecialchars($row['nama_mubaligh']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_kegiatan">Kegiatan :</label>
                <select name="id_kegiatan" id="id_kegiatan" required>
                    <option value="">Pilih Kegiatan</option>
                    <?php
                    // Mendapatkan id_kegiatan yang sedang digunakan (saat pengeditan)
                    $currentKegiatanId = $data['id_kegiatan'];  // Nilai id_kegiatan yang sedang diedit
                    
                    // Query untuk menampilkan kegiatan yang belum ada di tabel jadwal atau kegiatan yang sedang digunakan
                    $stmt = $conn->prepare("SELECT id_kegiatan, nama_kegiatan, tanggal_kegiatan 
                                            FROM kegiatan  
                                            WHERE (id_kegiatan = ? AND nama_kegiatan = 'Khutbah Jum\'at') 
                                            OR (nama_kegiatan = 'Khutbah Jum\'at' AND id_kegiatan NOT IN (SELECT id_kegiatan FROM jadwal)) 
                                            ORDER BY tanggal_kegiatan");

                    $stmt->bind_param("i", $currentKegiatanId);  // Bind parameter dengan id_kegiatan saat ini
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    // Loop melalui hasil query untuk menampilkan dropdown
                    while ($row = $result->fetch_assoc()) {
                        // Menandai kegiatan yang sedang dipilih sebagai selected
                        $selected = ($row['id_kegiatan'] == $currentKegiatanId) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($row['id_kegiatan']) . "' $selected>" . 
                            htmlspecialchars($row['nama_kegiatan']) . " (" . htmlspecialchars($row['tanggal_kegiatan']) . ")</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="kode_jadwal">Kode Kegiatan :</label>
                <select name="kode_jadwal" id="kode_jadwal" required>
                    <option value="JKJ" <?php echo $data['kode_jadwal'] == 'JKJ' ? 'selected' : ''; ?>>Jadwal Khutbah Jum'at (JKJ)</option>
                </select>
            </div>
            <button type="submit">Update</button>
        </form>

        <!-- Tombol Cancel -->
        <button type="button" onclick="location.href='jadwal_mubaligh.php'">Cancel</button>
    </div>
</body>
</html>
