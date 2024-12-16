<?php 
session_start();
include 'koneksi.php'; // File koneksi ke database

// Ambil parameter pencarian
$query = isset($_GET['query']) ? $conn->real_escape_string($_GET['query']) : '';

// Query untuk mencari data sesuai input
$sql = "SELECT DISTINCT j.kode_jadwal, mu.nama_mubaligh 
        FROM jadwal j 
        INNER JOIN mubaligh mu ON j.id_mubaligh = mu.id_mubaligh
        WHERE mu.nama_mubaligh LIKE '%$query%'";

$result = $conn->query($sql);

$data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Kembalikan hasil dalam format JSON
header('Content-Type: application/json');
echo json_encode($data);

$conn->close();
?>
