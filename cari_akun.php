<?php
session_start();
include 'koneksi.php'; // File koneksi ke database

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$data = [];

if (!empty($query)) {
    // Menggunakan DISTINCT untuk menghindari data duplikat
    $stmt = $conn->prepare("SELECT DISTINCT username FROM akun WHERE username LIKE ?");
    $search = "%$query%";
    $stmt->bind_param("s", $search);
} else {
    $stmt = $conn->prepare("SELECT DISTINCT username FROM akun");
}

if ($stmt->execute()) {
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row; // Hanya memasukkan username yang unik
    }
}
$stmt->close();

header('Content-Type: application/json');
echo json_encode($data);
$conn->close();
?>
