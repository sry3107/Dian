<?php
require __DIR__ . '/fpdf/fpdf.php'; // Sesuaikan path ke lokasi FPDF
session_start();
include 'koneksi.php'; // File koneksi ke database
date_default_timezone_set('Asia/Jakarta'); // Set timezone untuk Indonesia

// Query untuk mengambil nama masjid, alamat masjid, dan data pengurus
$query = $conn->prepare("SELECT username, email, role, status FROM akun ORDER BY created_at ASC");
$query->execute();
$result = $query->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);

// Query untuk mengambil nama masjid, alamat masjid, dan data pengurus
$querymasjid = $conn->prepare("SELECT nama_masjid, alamat_masjid FROM masjid");
$querymasjid->execute();
$resultmasjid = $querymasjid->get_result();
$datamasjid = $resultmasjid->fetch_all(MYSQLI_ASSOC);

// Cek jika data ada
if (!empty($data)) {
    // Ambil informasi masjid dari data pertama
    $nama_masjid = $datamasjid[0]['nama_masjid'];
    $alamat_masjid = $datamasjid[0]['alamat_masjid'];
} else {
    $nama_masjid = 'Nama Masjid Tidak Ditemukan';
    $alamat_masjid = 'Alamat Masjid Tidak Ditemukan';
}

// Extend FPDF untuk menambahkan header dan footer
class PDF extends FPDF
{
    public $nama_masjid;
    public $alamat_masjid;

    function Header()
    {
        // Logo masjid
        $this->Image('D:/laragon/www/sisfo-masjid/fpdf/logo_masjid.jpg', 10, 5, 30);
        // Judul Header
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'DAFTAR AKUN PENGGUNA APLIKASI SINARMU', 0, 1, 'C');
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 5, $this->nama_masjid, 0, 1, 'C');
        $this->SetFont('Arial', 'IU', 11);
        $this->Cell(0, 5, $this->alamat_masjid, 0, 1, 'C');
        $this->Ln(10);

        // Garis pemisah
        $this->SetLineWidth(0.3);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(8);

        // Header tabel
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(14, 10, 'No.', 1, 0, 'C');
        $this->Cell(64, 10, 'Username', 1, 0, 'C');
        $this->Cell(70, 10, 'Email', 1, 0, 'C');
        $this->Cell(21, 10, 'Role', 1, 0, 'C');
        $this->Cell(21, 10, 'Status', 1, 1, 'C');
    }

    function Footer()
    {
        // Posisi di bawah
        $this->SetY(-20);
        $this->SetFont('Arial', 'I', 10);
        $this->Line(10, $this->GetY() + 10, 200, $this->GetY() + 10);
        $this->Cell(0, 10, 'Tanggal & Waktu: ' . date('Y-m-d H:i:s'), 0, 0, 'L');
        $this->Cell(0, 10, 'Halaman: ' . $this->PageNo() . '/{nb}', 0, 1, 'R');
    }
}

// Buat objek PDF
$pdf = new PDF();
$pdf->AliasNbPages(); // Tambahkan ini untuk menggantikan {nb}
$pdf->nama_masjid = $nama_masjid;
$pdf->alamat_masjid = $alamat_masjid;

$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Tabel Data Pengurus
if (!empty($data)) {
    $no = 1; // Nomor urut
    foreach ($data as $akun) {
        $pdf->Cell(14, 10, $no++.'.', 1, 0, 'C');
        $pdf->Cell(64, 10, $akun['username'], 1, 0, 'L');
        $pdf->Cell(70, 10, $akun['email'], 1, 0, 'L');
        $pdf->Cell(21, 10, $akun['role'], 1, 0, 'C');
        $pdf->Cell(21, 10, $akun['status'], 1, 1, 'C');
    }
} else {
    $pdf->Cell(0, 10, 'Tidak ada data akun', 1, 1, 'C');
}

// Output PDF
$pdf->Output();
?>
