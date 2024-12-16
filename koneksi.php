<?php
$host = 'localhost'; 
$user = 'root'; 
$password = ''; 
$database = 'sisfo-masjid'; 

// Membuat koneksi
$conn = new mysqli($host, $user, $password, $database);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8");

?>
