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

// Mendapatkan ID kegiatan dari URL
$id_kegiatan = $_GET['id'] ?? null;

// Validasi ID
if (!$id_kegiatan || !filter_var($id_kegiatan, FILTER_VALIDATE_INT)) {
    echo "<script>alert('ID Kegiatan tidak valid!'); window.location.href='manage_kegiatan.php';</script>";
    exit;
}

// Ambil data kegiatan untuk form edit
$query = $conn->prepare("SELECT * FROM kegiatan WHERE id_kegiatan = ?");
$query->bind_param("i", $id_kegiatan);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
} else {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='manage_kegiatan.php';</script>";
    exit;
}

// Proses update data kegiatan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kode_kegiatan = $_POST['kode_kegiatan'];
    $nama_kegiatan = $_POST['nama_kegiatan'];
    $deskripsi_kegiatan = $_POST['deskripsi_kegiatan'];
    $tempat_kegiatan = $_POST['tempat_kegiatan'];  // Ini akan berisi nama_masjid
    $waktu_kegiatan = $_POST['waktu_kegiatan'];
    $tanggal_kegiatan = $_POST['tanggal_kegiatan'];
    $updated_at = date("Y-m-d H:i:s"); // Timestamp saat data diperbarui

    $updateQuery = $conn->prepare("UPDATE kegiatan SET kode_kegiatan = ?, nama_kegiatan = ?, deskripsi_kegiatan = ?, tempat_kegiatan = ?, waktu_kegiatan = ?, tanggal_kegiatan = ?, updated_at = ? WHERE id_kegiatan = ?");
    $updateQuery->bind_param("sssssssi", $kode_kegiatan, $nama_kegiatan, $deskripsi_kegiatan, $tempat_kegiatan, $waktu_kegiatan, $tanggal_kegiatan, $updated_at, $id_kegiatan);

    if ($updateQuery->execute()) {
        echo "<script>alert('Data kegiatan berhasil diperbarui!'); window.location.href='manage_kegiatan.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data kegiatan!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Kegiatan</title>
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
<body>
    <div class="container">
        <h2>Edit Kegiatan</h2>
        <form action="" method="post">
            <div class="form-group">
                <label for="nama_kegiatan">Nama Kegiatan :</label>
                <select name="nama_kegiatan" id="nama_kegiatan" required>
                    <option value="">Pilih Nama Kegiatan</option>
                    <?php
                    // Fetch kegiatan data for the select dropdown
                    $kegiatanQuery = $conn->query("SELECT DISTINCT nama_kegiatan FROM kegiatan");
                    while ($kegiatan = $kegiatanQuery->fetch_assoc()):
                    ?>
                        <option value="<?= htmlspecialchars($kegiatan['nama_kegiatan']); ?>" <?= ($data['nama_kegiatan'] == $kegiatan['nama_kegiatan']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($kegiatan['nama_kegiatan']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <!-- Kode Kegiatan -->
            <div class="form-group">
                <label for="kode_kegiatan">Kode Kegiatan :</label>
                <select name="kode_kegiatan" id="kode_kegiatan" required>
                    <option value="">Pilih Kode Kegiatan</option>
                    <?php
                    // Fetch kode_kegiatan based on nama_kegiatan
                    $kodeQuery = $conn->prepare("SELECT DISTINCT kode_kegiatan FROM kegiatan WHERE nama_kegiatan = ?");
                    $kodeQuery->bind_param("s", $data['nama_kegiatan']);
                    $kodeQuery->execute();
                    $kodeResult = $kodeQuery->get_result();

                    while ($kode = $kodeResult->fetch_assoc()):
                    ?>
                        <option value="<?= htmlspecialchars($kode['kode_kegiatan']); ?>" <?= ($data['kode_kegiatan'] == $kode['kode_kegiatan']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($kode['kode_kegiatan']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Tempat Kegiatan -->
            <div class="form-group">
                <label for="tempat_kegiatan">Tempat :</label>
                <select name="tempat_kegiatan" id="tempat_kegiatan" required>
                    <option value="">Pilih Tempat Kegiatan</option>
                    <?php
                    // Query untuk mengambil nama_masjid dari kegiatan berdasarkan id_kegiatan
                    $queryMasjid = $conn->prepare("SELECT m.nama_masjid FROM kegiatan k JOIN masjid m ON k.tempat_kegiatan = m.id_masjid WHERE k.id_kegiatan = ?");
                    $queryMasjid->bind_param("i", $id_kegiatan); // Binding ID Kegiatan
                    $queryMasjid->execute();
                    $resultMasjid = $queryMasjid->get_result();
                    
                    // Menyimpan nama_masjid yang terkait dengan kegiatan
                    $masjidData = $resultMasjid->fetch_assoc();
                    $selectedMasjidName = $masjidData['nama_masjid']; // Nama Masjid yang dipilih
                    ?>
                    
                    <?php 
                    // Fetch semua masjid untuk dropdown, menampilkan nama_masjid
                    $masjidQuery = $conn->query("SELECT nama_masjid FROM masjid");
                    while ($masjid = $masjidQuery->fetch_assoc()):
                    ?>
                        <option value="<?= htmlspecialchars($masjid['nama_masjid']); ?>" 
                            <?= ($masjid['nama_masjid'] == $selectedMasjidName) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($masjid['nama_masjid']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="deskripsi_kegiatan">Deskripsi :</label>
                <textarea name="deskripsi_kegiatan" id="deskripsi_kegiatan" required><?= htmlspecialchars($data['deskripsi_kegiatan']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="waktu_kegiatan">Waktu :</label>
                <input type="time" name="waktu_kegiatan" id="waktu_kegiatan" value="<?= htmlspecialchars($data['waktu_kegiatan']); ?>" required>
            </div>
            <div class="form-group">
                <label for="tanggal_kegiatan">Tanggal :</label>
                <input type="date" name="tanggal_kegiatan" id="tanggal_kegiatan" value="<?= htmlspecialchars($data['tanggal_kegiatan']); ?>" required>
            </div>

            <button type="submit">Update</button>
        </form>

        <button type="button" onclick="location.href='manage_kegiatan.php'">Cancel</button>
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
