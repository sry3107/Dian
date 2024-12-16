<?php
session_start();
include 'koneksi.php'; // File koneksi ke database

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$data = [];

if (!empty($query)) {
    // Query dengan parameter pencarian
    $stmt = $conn->prepare("SELECT DISTINCT id_pengurus, nama_pengurus FROM pengurus WHERE nama_pengurus LIKE ?");
    $search = "%$query%";
    $stmt->bind_param("s", $search);
} else {
    // Query tanpa parameter pencarian
    $stmt = $conn->prepare("SELECT DISTINCT id_pengurus, nama_pengurus FROM pengurus");
}

if ($stmt->execute()) {
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}
$stmt->close();

header('Content-Type: application/json');
echo json_encode($data);
$conn->close();
?>
