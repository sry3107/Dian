<?php
session_start();
include 'koneksi.php'; // File koneksi ke database

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$data = [];

if (!empty($query)) {
    // Menggunakan DISTINCT untuk menghindari nilai duplikat
    $stmt = $conn->prepare("SELECT DISTINCT nama_kegiatan FROM kegiatan WHERE nama_kegiatan LIKE ? AND kode_kegiatan='KMG'");
    $search = "%$query%";
    $stmt->bind_param("s", $search);
} else {
    $stmt = $conn->prepare("SELECT DISTINCT nama_kegiatan FROM kegiatan");
}

if ($stmt->execute()) {
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row; // Hanya memasukkan nama_kegiatan
    }
}
$stmt->close();

header('Content-Type: application/json');
echo json_encode($data);
$conn->close();
?>
