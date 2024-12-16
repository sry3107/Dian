<?php 
session_start();
include 'koneksi.php'; // File koneksi ke database

// Ambil parameter pencarian
$query = isset($_GET['query']) ? $conn->real_escape_string($_GET['query']) : '';

// Query untuk mencari data sesuai input
$sql = "SELECT DISTINCT judul_artikel FROM artikel WHERE judul_artikel LIKE '%$query%'";

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
